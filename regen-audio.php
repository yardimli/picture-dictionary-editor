<?php
require_once( "./config/session.php" );

require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\Translate\V2\TranslateClient;

require_once( "./config/class.dictionary.php" );
$dictionary_list = new DICTIONARY();

require_once( "./config/class.user.php" );
$auth_user = new USER();
$user_id   = $_SESSION['user_session'];
# get logged in user details through session id
$stmt = $auth_user->runQuery( "SELECT username, useremail, dateadded FROM users WHERE id=:user_id" );
$stmt->execute( array( ":user_id" => $user_id ) );
$userRow  = $stmt->fetch( PDO::FETCH_ASSOC );
$lu_uname = $userRow['username'];
$lu_email = $userRow['useremail'];
$lu_date  = $userRow['dateadded'];
$date     = new DateTime( $lu_date );

$error_msg = "";

clearstatcache();

function beautify_filename( $filename ) {
	// reduce consecutive characters
	$filename = preg_replace( array(
		// "file   name.zip" becomes "file-name.zip"
		'/ +/',
		// "file___name.zip" becomes "file-name.zip"
		'/_+/',
		// "file---name.zip" becomes "file-name.zip"
		'/-+/'
	), '-', $filename );
	$filename = preg_replace( array(
		// "file--.--.-.--name.zip" becomes "file.name.zip"
		'/-*\.-*/',
		// "file...name..zip" becomes "file.name.zip"
		'/\.{2,}/'
	), '.', $filename );
	// lowercase for windows/unix interoperability http://support.microsoft.com/kb/100625
	$filename = mb_strtolower( $filename, mb_detect_encoding( $filename ) );
	// ".file-name.-" becomes "file-name"
	$filename = trim( $filename, '.-' );

	return $filename;
}

function filter_filename( $filename, $beautify = true ) {
	// sanitize filename
	$filename = preg_replace(
		'~
        [<>:"/\\|?*]|            # file system reserved https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
        [\x00-\x1F]|             # control characters http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247%28v=vs.85%29.aspx
        [\x7F\xA0\xAD]|          # non-printing characters DEL, NO-BREAK SPACE, SOFT HYPHEN
        [#\[\]@!$&\'()+,;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2
        [{}^\~`]                 # URL unsafe characters https://www.ietf.org/rfc/rfc1738.txt
        ~x',
		'-', $filename );
	// avoids ".", ".." or ".hiddenFiles"
	$filename = ltrim( $filename, '.-' );
	// optional beautification
	if ( $beautify ) {
		$filename = beautify_filename( $filename );
	}
	// maximize filename length to 255 bytes http://serverfault.com/a/9548/44086
	$ext      = pathinfo( $filename, PATHINFO_EXTENSION );
	$filename = mb_strcut( pathinfo( $filename, PATHINFO_FILENAME ), 0, 255 - ( $ext ? strlen( $ext ) + 1 : 0 ), mb_detect_encoding( $filename ) ) . ( $ext ? '.' . $ext : '' );

	return $filename;
}

function url_make( $str ) {
	$before = array( 'ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ö', 'Ç' ); // , '\'', '""'
	$after  = array( 'i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 'o', 'c' ); // , '', ''

	$clean = str_replace( $before, $after, $str );
	return $clean;
}


if ( ! $auth_user->is_loggedin() ) {
	# redirect to login page, gtfo
	$auth_user->doLogout();
} else {


	$textToSpeechClient = new TextToSpeechClient( [ 'credentials' => __DIR__ . "/google-key.json" ] );

	$word_en = $_POST["word_EN"];

	if ( $word_en !== "" ) {
		$input = new SynthesisInput();
		$input->setText( $word_en );
		$voice = new VoiceSelectionParams();
		$voice->setLanguageCode( 'en-US' );
		$voice->setName( 'en-US-Wavenet-A' );
		$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
		$audioConfig = new AudioConfig();
		$audioConfig->setSpeakingRate( 0.75 );
		$audioConfig->setAudioEncoding( AudioEncoding::MP3 );

		$audio_en = filter_filename( $word_en ) . ".mp3";

		$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
    if (file_exists( "./audio/en/" . $audio_en )) {
      unlink("./audio/en/" . $audio_en);
    }
		file_put_contents( "./audio/en/" . $audio_en, $resp->getAudioContent() );

		$stmt2 = $dictionary_list->runQuery( 'UPDATE dictionary set audio_EN="' . $audio_en . '" WHERE id=' . $_POST["word_id"] );
		$stmt2->execute();
	}

	$word_tr = $_POST["word_TR"];
	if ( $word_tr !== "" ) {
		$input = new SynthesisInput();
		$input->setText( $word_tr );
		$voice = new VoiceSelectionParams();
		$voice->setLanguageCode( 'tr-TR' );
		$voice->setName( 'tr-TR-Wavenet-D' );
		$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
		$audioConfig = new AudioConfig();
		$audioConfig->setSpeakingRate( 0.75 );
		$audioConfig->setAudioEncoding( AudioEncoding::MP3 );

		$audio_tr = url_make( filter_filename( $word_tr ) ) . ".mp3";

		$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );

    if (file_exists( "./audio/tr/" . $audio_tr )) {
      unlink("./audio/tr/" . $audio_tr);
    }
		file_put_contents( "./audio/tr/" . $audio_tr, $resp->getAudioContent() );

		$stmt2 = $dictionary_list->runQuery( 'UPDATE dictionary set audio_TR="' . $audio_tr . '" WHERE id=' . $_POST["word_id"] );
		$stmt2->execute();
	}

	$word_ch = $_POST["word_CH"];
	if ( $word_ch !== "" ) {
		$input = new SynthesisInput();
		$input->setText( $word_ch );
		$voice = new VoiceSelectionParams();
		$voice->setLanguageCode( 'cmn-TW' );
		$voice->setName( 'cmn-TW-Wavenet-A' );
		$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
		$audioConfig = new AudioConfig();
		$audioConfig->setSpeakingRate( 0.75 );
		$audioConfig->setAudioEncoding( AudioEncoding::MP3 );

		if ( filter_filename( $word_en ) !== "" ) {
			$audio_ch = filter_filename( $word_en ) . ".mp3";
		} else {
			$audio_ch = $_POST["word_id"] . ".mp3";
		}

		$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
		if (file_exists( "./audio/ch/" . $audio_ch )) {
		  if (unlink("./audio/ch/" . $audio_ch)) {
        $error_msg = "Deleted old chinese audio.";
      } else
      {
        $error_msg = "Can't delete old chinese audio.";
      }
    }
		file_put_contents( "./audio/ch/" . $audio_ch, $resp->getAudioContent() );

		$stmt2 = $dictionary_list->runQuery( 'UPDATE dictionary set audio_CH="' . $audio_ch . '" WHERE id=' . $_POST["word_id"] );
		$stmt2->execute();
	}
	echo "<div class=\"alert alert-success\" role=\"alert\">";
	echo "<p>Audio files created successfully.</p>";
	echo $error_msg;
	echo "</div>";
}

?>
<?php
require_once( "./config/session.php" );

require_once __DIR__ . '/vendor/autoload.php';

use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\Translate\V2\TranslateClient;

require_once "text-to-speech-key.php";

require_once( "./config/class.story.php" );
$story_list = new STORY();

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

	if ( $_POST["story_id"] === "0" ) {
		echo "<div class=\"alert alert-success\" role=\"alert\">";
		echo "<p>Please save and open word in edit mode to update audio.</p>";
		echo $error_msg;
		echo "</div>";

	} else {

		$audio_speed = $_POST["audio_speed"] . "";
		if ( $audio_speed === "" || $audio_speed === "2" ) {
			$audio_speed = 1.00;
		}
		if ( $audio_speed === "1" ) {
			$audio_speed = 0.80;
		}
		if ( $audio_speed === "3" ) {
			$audio_speed = 1.20;
		}

		$story = $_POST["story"];
		$story_id = $_POST["story_id"];
		$story_lang = $_POST["target_lang"];
		$audio_file = filter_filename( $story_id  ."_". $story_lang. "_". time()) . ".mp3";

		if ( $story !== "" && $story_lang === "en" ) {
			$input = new SynthesisInput();
			$input->setText( $story );
			$voice = new VoiceSelectionParams();
			$voice->setLanguageCode( 'en-US' );
			$voice->setName( 'en-US-Wavenet-A' );
			$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
			$audioConfig = new AudioConfig();
			$audioConfig->setSpeakingRate( $audio_speed );
			$audioConfig->setAudioEncoding( AudioEncoding::MP3 );


			$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
			if ( file_exists( "./audio/story/" . $audio_file ) ) {
				unlink( "./audio/story/" . $audio_file );
			}
			file_put_contents( "./audio/story/" . $audio_file, $resp->getAudioContent() );

			$stmt2 = $story_list->runQuery( 'UPDATE story set audio="' . $audio_file . '" WHERE id=' . $story_id );
			$stmt2->execute();
			?>
			<script>
				$("#en_audio_player").attr({"src": "../audio/story/<?php echo $audio_file; ?>"});
				$("#en_audio_file").html("<?php echo $audio_file; ?>");
			</script>
			<?php
		}

		if ( $story !== "" && $story_lang === "tr" ) {
			$input = new SynthesisInput();
			$input->setText( $story );
			$voice = new VoiceSelectionParams();
			$voice->setLanguageCode( 'tr-TR' );
			$voice->setName( 'tr-TR-Wavenet-D' );
			$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
			$audioConfig = new AudioConfig();
			$audioConfig->setSpeakingRate( $audio_speed );
			$audioConfig->setAudioEncoding( AudioEncoding::MP3 );

			$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );

			if ( file_exists( "./audio/story/" . $audio_file ) ) {
				unlink( "./audio/story/" . $audio_file );
			}
			file_put_contents( "./audio/story/" . $audio_file, $resp->getAudioContent() );

			$stmt2 = $story_list->runQuery( 'UPDATE story set audio="' . $audio_file . '" WHERE id=' . $story_id );
			$stmt2->execute();
			?>
			<script>
				$("#tr_audio_player").attr({"src": "../audio/story/<?php echo $audio_file; ?>"});
				$("#tr_audio_file").html("<?php echo $audio_file; ?>");
			</script>
			<?php
		}

		if ( $story !== "" && $story_lang === "ch" ) {
			$input = new SynthesisInput();
			$input->setText( $story );
			$voice = new VoiceSelectionParams();
			$voice->setLanguageCode( 'cmn-TW' );
			$voice->setName( 'cmn-TW-Wavenet-A' );
			$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
			$audioConfig = new AudioConfig();
			$audioConfig->setSpeakingRate( $audio_speed );
			$audioConfig->setAudioEncoding( AudioEncoding::MP3 );

			$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
			if ( file_exists( "./audio/story/" . $audio_file ) ) {
				if ( unlink( "./audio/story/" . $audio_file ) ) {
					$error_msg = "Deleted old chinese audio.";
				} else {
					$error_msg = "Can't delete old chinese audio.";
				}
			}
			file_put_contents( "./audio/story/" . $audio_file, $resp->getAudioContent() );

			$stmt2 = $story_list->runQuery( 'UPDATE story set audio="' . $audio_file . '" WHERE id=' . $story_id );
			$stmt2->execute();
			?>
			<script>
			$("#ch_audio_player").attr({"src": "../audio/story/<?php echo $audio_file; ?>"});
			$("#ch_audio_file").html("<?php echo $audio_file; ?>");
			</script>
<?php
		}
		echo "<div class=\"alert alert-success\" role=\"alert\">";
		echo "<p>Audio files created successfully.</p>";
		echo $error_msg;
		echo "</div>";
	}
}

?>
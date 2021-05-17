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

	$filename = str_ireplace( "=", "eq", $filename );

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

require_once "text-to-speech-key.php";


$audio_speed = 1.00;

$LessonRange = 30;

$LoopTop = 30;


$word_en = "3 minus 2 =";
$word_tr = "3 eksi 2 =";
$word_ch = "3減2 =";

echo "creating sum words:<br>";

$word_list = ["plus","minus","equals"];
$file_list = ["plus","minus","equals"];
for ($i=0; $i<count($word_list); $i++) {
	$input   = new SynthesisInput();
	$input->setText( $word_list[$i] );
	$voice = new VoiceSelectionParams();
	$voice->setLanguageCode( 'en-US' );
	$voice->setName( 'en-US-Wavenet-A' );
	$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
	$audioConfig = new AudioConfig();
	$audioConfig->setSpeakingRate( $audio_speed );
	$audioConfig->setAudioEncoding( AudioEncoding::MP3 );

	$audio_en = filter_filename( $file_list[$i] ) . ".mp3";

	if ( ! file_exists( "./audio/math/en/" . $audio_en ) ) {
		echo $word_list[$i] . "<br>";
		$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
		file_put_contents( "./audio/math/en/" . $audio_en, $resp->getAudioContent() );
		usleep( 500000 );
	}
}

$word_list = ["artı","eksi","eşittir"];
$file_list = ["plus","minus","equals"];
for ($i=0; $i<count($word_list); $i++) {
	$input = new SynthesisInput();
	$input->setText( $word_list[$i] );
	$voice = new VoiceSelectionParams();
	$voice->setLanguageCode( 'tr-TR' );
	$voice->setName( 'tr-TR-Wavenet-B' );
	$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
	$audioConfig = new AudioConfig();
	$audioConfig->setSpeakingRate( $audio_speed );
	$audioConfig->setAudioEncoding( AudioEncoding::MP3 );

	$audio_tr = url_make( filter_filename( $file_list[$i] ) ) . ".mp3";

	if ( ! file_exists( "./audio/math/tr/" . $audio_tr ) ) {
		echo $word_list[$i] . "<br>";
		$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
		file_put_contents( "./audio/math/tr/" . $audio_tr, $resp->getAudioContent() );
		usleep(500000);
	}
}

$word_list = ["加","減","="];
$file_list = ["plus","minus","equals"];
for ($i=0; $i<count($word_list); $i++) {
	$input = new SynthesisInput();
	$input->setText( $word_list[$i] );
	$voice = new VoiceSelectionParams();
	$voice->setLanguageCode( 'cmn-TW' );
	$voice->setName( 'cmn-TW-Wavenet-A' );
	$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
	$audioConfig = new AudioConfig();
	$audioConfig->setSpeakingRate( $audio_speed );
	$audioConfig->setAudioEncoding( AudioEncoding::MP3 );

	$audio_en = url_make( filter_filename( $file_list[$i] ) ) . ".mp3";

	if ( ! file_exists( "./audio/math/ch/" . $audio_en ) ) {
		echo $word_list[$i] . "<br>";
		try {
			$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
			file_put_contents( "./audio/math/ch/" . $audio_en, $resp->getAudioContent() );
			usleep(500000);
		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}
	}
}



echo "creating for counting:<br>";

for ( $i = 0; $i <= 100; $i ++ ) {

	$word_en = $i;
	$input = new SynthesisInput();
	$input->setText( $word_en );
	$voice = new VoiceSelectionParams();
	$voice->setLanguageCode( 'en-US' );
	$voice->setName( 'en-US-Wavenet-A' );
	$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
	$audioConfig = new AudioConfig();
	$audioConfig->setSpeakingRate( $audio_speed );
	$audioConfig->setAudioEncoding( AudioEncoding::MP3 );

	$audio_en = filter_filename( $word_en ) . ".mp3";

	if ( ! file_exists( "./audio/math/en/" . $audio_en ) ) {
		echo $word_en . "<br>";
		$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
		file_put_contents( "./audio/math/en/" . $audio_en, $resp->getAudioContent() );
		usleep(500000);
	}


	$word_tr = $i;

	$input = new SynthesisInput();
	$input->setText( $word_tr );
	$voice = new VoiceSelectionParams();
	$voice->setLanguageCode( 'tr-TR' );
	$voice->setName( 'tr-TR-Wavenet-B' );
	$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
	$audioConfig = new AudioConfig();
	$audioConfig->setSpeakingRate( $audio_speed );
	$audioConfig->setAudioEncoding( AudioEncoding::MP3 );

	$audio_tr = url_make( filter_filename( $word_tr ) ) . ".mp3";

	if ( ! file_exists( "./audio/math/tr/" . $audio_tr ) ) {
		echo $word_tr . "<br>";
		$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
		file_put_contents( "./audio/math/tr/" . $audio_tr, $resp->getAudioContent() );
		usleep(500000);
	}

	$word_ch = $i;

	$input = new SynthesisInput();
	$input->setText( $word_ch );
	$voice = new VoiceSelectionParams();
	$voice->setLanguageCode( 'cmn-TW' );
	$voice->setName( 'cmn-TW-Wavenet-A' );
	$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
	$audioConfig = new AudioConfig();
	$audioConfig->setSpeakingRate( $audio_speed );
	$audioConfig->setAudioEncoding( AudioEncoding::MP3 );

	$audio_en = url_make( filter_filename( $word_en ) ) . ".mp3";

	if ( ! file_exists( "./audio/math/ch/" . $audio_en ) ) {
		echo $word_ch . "<br>";
		try {
			$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
			file_put_contents( "./audio/math/ch/" . $audio_en, $resp->getAudioContent() );
			usleep(500000);
		} catch ( Exception $e ) {
			echo 'Caught exception: ', $e->getMessage(), "\n";
		}
	}



}


echo "creating for english:<br>";

for ( $i = 1; $i <= $LoopTop; $i ++ ) {
	for ( $j = 1; $j <= $LoopTop; $j ++ ) {
		if ( $i + $j <= $LessonRange ) {

			$word_en = $i . " plus " . $j . " = " . ( $i + $j );
			$word_en = $i . " plus " . $j . " = ";

			$input = new SynthesisInput();
			$input->setText( $word_en );
			$voice = new VoiceSelectionParams();
			$voice->setLanguageCode( 'en-US' );
			$voice->setName( 'en-US-Wavenet-A' );
			$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
			$audioConfig = new AudioConfig();
			$audioConfig->setSpeakingRate( $audio_speed );
			$audioConfig->setAudioEncoding( AudioEncoding::MP3 );

			$audio_en = filter_filename( $word_en ) . ".mp3";

			if ( ! file_exists( "./audio/math/en/" . $audio_en ) ) {
				echo $word_en . "<br>";
				$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
				file_put_contents( "./audio/math/en/" . $audio_en, $resp->getAudioContent() );
				usleep(500000);
			}

		}
	}
}

for ( $i = 1; $i <= $LoopTop; $i ++ ) {
	for ( $j = 1; $j <= $LoopTop; $j ++ ) {
		if ( $i - $j <= $LessonRange && $i - $j >= 0 ) {

			$word_en = $i . " minus " . $j . " = " . ( $i - $j );
			$word_en = $i . " minus " . $j . " = ";

			$input = new SynthesisInput();
			$input->setText( $word_en );
			$voice = new VoiceSelectionParams();
			$voice->setLanguageCode( 'en-US' );
			$voice->setName( 'en-US-Wavenet-A' );
			$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
			$audioConfig = new AudioConfig();
			$audioConfig->setSpeakingRate( $audio_speed );
			$audioConfig->setAudioEncoding( AudioEncoding::MP3 );

			$audio_en = filter_filename( $word_en ) . ".mp3";

			if ( ! file_exists( "./audio/math/en/" . $audio_en ) ) {
				echo $word_en . "<br>";
				$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
				file_put_contents( "./audio/math/en/" . $audio_en, $resp->getAudioContent() );
				usleep(500000);
			}

		}
	}
}


echo "creating for turkish:<br>";

for ( $i = 1; $i <= $LoopTop; $i ++ ) {
	for ( $j = 1; $j <= $LoopTop; $j ++ ) {
		if ( $i + $j <= $LessonRange ) {

			$word_tr = $i . " artı " . $j . " = " . ( $i + $j );
			$word_tr = $i . " artı " . $j . " = ";

			$input = new SynthesisInput();
			$input->setText( $word_tr );
			$voice = new VoiceSelectionParams();
			$voice->setLanguageCode( 'tr-TR' );
			$voice->setName( 'tr-TR-Wavenet-B' );
			$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
			$audioConfig = new AudioConfig();
			$audioConfig->setSpeakingRate( $audio_speed );
			$audioConfig->setAudioEncoding( AudioEncoding::MP3 );

			$audio_tr = url_make( filter_filename( $word_tr ) ) . ".mp3";

			if ( ! file_exists( "./audio/math/tr/" . $audio_tr ) ) {
				echo $word_tr . "<br>";
				$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
				file_put_contents( "./audio/math/tr/" . $audio_tr, $resp->getAudioContent() );
				usleep(500000);
			}
		}
	}
}

for ( $i = 1; $i <= $LoopTop; $i ++ ) {
	for ( $j = 1; $j <= $LoopTop; $j ++ ) {
		if ( $i - $j <= $LessonRange && $i - $j >= 0 ) {

			$word_tr = $i . " eksi " . $j . " = " . ( $i - $j );
			$word_tr = $i . " eksi " . $j . " = ";

			$input = new SynthesisInput();
			$input->setText( $word_tr );
			$voice = new VoiceSelectionParams();
			$voice->setLanguageCode( 'tr-TR' );
			$voice->setName( 'tr-TR-Wavenet-B' );
			$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
			$audioConfig = new AudioConfig();
			$audioConfig->setSpeakingRate( $audio_speed );
			$audioConfig->setAudioEncoding( AudioEncoding::MP3 );

			$audio_tr = url_make( filter_filename( $word_tr ) ) . ".mp3";


			if ( ! file_exists( "./audio/math/tr/" . $audio_tr ) ) {
				echo $word_tr . "<br>";
				$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
				file_put_contents( "./audio/math/tr/" . $audio_tr, $resp->getAudioContent() );
				usleep(500000);
			}
		}
	}
}

echo "creating for chinese:<br>";


for ( $i = 1; $i <= $LoopTop; $i ++ ) {
	for ( $j = 1; $j <= $LoopTop; $j ++ ) {
		if ( $i + $j <= $LessonRange ) {

			$word_en = $i . " plus " . $j . " = " . ( $i + $j );
			$word_en = $i . " plus " . $j . " = ";
			$word_ch = $i . " 加 " . $j . " = " . ( $i + $j );
			$word_ch = $i . " 加 " . $j . " = ";

			$input = new SynthesisInput();
			$input->setText( $word_ch );
			$voice = new VoiceSelectionParams();
			$voice->setLanguageCode( 'cmn-TW' );
			$voice->setName( 'cmn-TW-Wavenet-A' );
			$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
			$audioConfig = new AudioConfig();
			$audioConfig->setSpeakingRate( $audio_speed );
			$audioConfig->setAudioEncoding( AudioEncoding::MP3 );

			$audio_en = url_make( filter_filename( $word_en ) ) . ".mp3";

			if ( ! file_exists( "./audio/math/ch/" . $audio_en ) ) {
				echo $word_ch . "<br>";
				try {
					$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
					file_put_contents( "./audio/math/ch/" . $audio_en, $resp->getAudioContent() );
					usleep(500000);
				} catch ( Exception $e ) {
					echo 'Caught exception: ', $e->getMessage(), "\n";
				}
			}
		}
	}
}

for ( $i = 1; $i <= $LoopTop; $i ++ ) {
	for ( $j = 1; $j <= $LoopTop; $j ++ ) {
		if ( $i - $j <= $LessonRange && $i - $j >= 0 ) {

			$word_en = $i . " minus " . $j . " = " . ( $i - $j );
			$word_en = $i . " minus " . $j . " = ";
			$word_ch = $i . " 減 " . $j . " = " . ( $i - $j );
			$word_ch = $i . " 減 " . $j . " = ";

			$input = new SynthesisInput();
			$input->setText( $word_ch );
			$voice = new VoiceSelectionParams();
			$voice->setLanguageCode( 'cmn-TW' );
			$voice->setName( 'cmn-TW-Wavenet-A' );
			$voice->setSsmlGender( SsmlVoiceGender::FEMALE );
			$audioConfig = new AudioConfig();
			$audioConfig->setSpeakingRate( $audio_speed );
			$audioConfig->setAudioEncoding( AudioEncoding::MP3 );

			$audio_en = url_make( filter_filename( $word_en ) ) . ".mp3";

			if ( ! file_exists( "./audio/math/ch/" . $audio_en ) ) {
				echo $word_ch . "<br>";
				try {
					$resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
					file_put_contents( "./audio/math/ch/" . $audio_en, $resp->getAudioContent() );
					usleep(500000);
				} catch ( Exception $e ) {
					echo 'Caught exception: ', $e->getMessage(), "\n";
				}
			}
		}
	}
}

?>
<?php

require_once "text-to-speech-key.php";

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
}

$word_tr = $_POST["word_TR"];
if ( $word_tr === "" ) {
	$result  = $translate->translate( $word_en, [
		'target' => 'tr'
	] );
	$word_tr = $result['text'];
//		var_dump($result);
}


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
}

$word_ch = $_POST["word_CH"];
if ( $word_ch === "" ) {
	$result  = $translate->translate( $word_en, [
		'target' => 'zh-TW'
	] );
	$word_ch = $result['text'];
//		var_dump($result);
}


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
}

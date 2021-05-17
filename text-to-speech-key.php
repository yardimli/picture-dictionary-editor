<?php

$translate = new TranslateClient( array(
	'keyFilePath' => __DIR__ . "/key.json",
	'projectId'   => 'key'
) );


// Translate text from english to french.
$textToSpeechClient = new TextToSpeechClient( [ 'credentials' => __DIR__ . "/key.json" ] );

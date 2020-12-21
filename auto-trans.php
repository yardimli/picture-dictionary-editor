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


if ( ! $auth_user->is_loggedin() ) {
	# redirect to login page, gtfo
	$auth_user->doLogout();
} else {


	$translate = new TranslateClient( array(
		'keyFilePath' => __DIR__ . "/google-key.json",
		'projectId'   => 'onyx-cumulus-289504'
	) );


// Translate text from english to french.
	$textToSpeechClient = new TextToSpeechClient( [ 'credentials' => __DIR__ . "/google-key.json" ] );

	$gen_tr = false;
	$gen_en = false;
	$gen_ch = false;

	if ($_POST["trans_source"]==="en") {
		$word_en = $_POST["word_EN"];
		$result  = $translate->translate( $word_en, [
			'target' => 'tr'
		] );
		$word_tr = $result['text'];
		$gen_tr = true;
		$stmt2 = $dictionary_list->runQuery( 'UPDATE dictionary set word_TR="' . $word_tr . '" WHERE id=' . $_POST["word_id"] );
		$stmt2->execute();

		$result  = $translate->translate( $word_en, [
			'target' => 'zh-TW'
		] );
		$word_ch = $result['text'];
		$gen_ch = true;
		$stmt2 = $dictionary_list->runQuery( 'UPDATE dictionary set word_CH="' . $word_ch . '" WHERE id=' . $_POST["word_id"] );
		$stmt2->execute();

		$zh = new \DictPedia\ZhuyinPinyin();

		$stmt = $dictionary_list->runQuery( "SELECT * FROM cedict WHERE traditional=:word_CH" );
		$stmt->bindparam( ":word_CH", $word_ch );
		$stmt->execute();
		if ( $p_word = $stmt->fetch() ) {
			$bopomofo = strtolower( $p_word["pinyin_numbers"]);
//			echo $bopomofo;
			$bopomofo2 = explode(" ",$bopomofo);
			$bopomofo3 = "";
			for ($i=0; $i<count($bopomofo2); $i++) {
				$bopomofo3 .= $zh->encodeZhuyin($bopomofo2[$i])." ";
//				echo $bopomofo2[$i]." - ".$zh->encodeZhuyin($bopomofo2[$i]);
			}

			$stmt2 = $dictionary_list->runQuery( 'UPDATE dictionary set bopomofo="' . $bopomofo3 . '" WHERE id=' . $_POST["word_id"] );
			$stmt2->execute();
		} else {
			echo $word_ch." not found in dictionary";
		}
	}

	if ($_POST["trans_source"]==="tr") {
		$word_tr = $_POST["word_TR"];
		$result  = $translate->translate( $word_tr, [
			'target' => 'en'
		] );
		$word_en = $result['text'];
		$gen_en = true;
		$stmt2 = $dictionary_list->runQuery( 'UPDATE dictionary set word_EN="' . $word_en . '" WHERE id=' . $_POST["word_id"] );
		$stmt2->execute();

		$result  = $translate->translate( $word_en, [
			'target' => 'zh-TW'
		] );
		$word_ch = $result['text'];
		$gen_ch = true;
		$stmt2 = $dictionary_list->runQuery( 'UPDATE dictionary set word_CH="' . $word_ch . '" WHERE id=' . $_POST["word_id"] );
		$stmt2->execute();

		$zh = new \DictPedia\ZhuyinPinyin();

		$stmt = $dictionary_list->runQuery( "SELECT * FROM cedict WHERE traditional=:word_CH" );
		$stmt->bindparam( ":word_CH", $word_ch );
		$stmt->execute();
		if ( $p_word = $stmt->fetch() ) {
			$bopomofo = strtolower( $p_word["pinyin_numbers"]);
			$bopomofo2 = explode(" ",$bopomofo);
			$bopomofo3 = "";
			for ($i=0; $i<count($bopomofo2); $i++) {
				$bopomofo3 .= $zh->encodeZhuyin($bopomofo2[$i])." ";
			}

			$stmt2 = $dictionary_list->runQuery( 'UPDATE dictionary set bopomofo="' . $bopomofo3 . '" WHERE id=' . $_POST["word_id"] );
		}
	}

	if ($_POST["trans_source"]==="ch") {
		$word_ch = $_POST["word_CH"];
		$result  = $translate->translate( $word_ch, [
			'target' => 'en'
		] );
		$word_en = $result['text'];
		$gen_en = true;
		$stmt2 = $dictionary_list->runQuery( 'UPDATE dictionary set word_EN="' . $word_en . '" WHERE id=' . $_POST["word_id"] );
		$stmt2->execute();

		$result  = $translate->translate( $word_en, [
			'target' => 'tr'
		] );
		$word_tr = $result['text'];
		$gen_tr = true;
		$stmt2 = $dictionary_list->runQuery( 'UPDATE dictionary set word_TR="' . $word_tr . '" WHERE id=' . $_POST["word_id"] );
		$stmt2->execute();
	}

	echo "<div class=\"alert alert-success\" role=\"alert\">";
	echo "<p>Auto Translations created successfully.</p>";
	echo "</div>";

}
?>
<?php
require_once( "../config/class.story.php" );
$story_list = new STORY();

require_once( "../config/class.category.php" );
$category_list = new CATEGORY();

header( "Content-type: application/json; charset=utf-8" );
//$stmt = $dictionary_list->runQuery( 'SELECT * FROM dictionary WHERE deleted=:val' );

$stmt = $story_list->runQuery( 'SELECT * FROM story WHERE deleted=:val' );
$stmt->execute( array( ':val' => 0 ) );

$result = [];
while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {

	unset( $row['deleted'] );
	unset( $row['update_time'] );

	$picture_hash = "";
	$audio_hash   = "";
	if ( file_exists( "../pictures/story/" . $row["picture"] ) && $row["picture"] !== null && $row["picture"] !== "" ) {
		$picture_hash = md5_file( "../pictures/story/" . $row["picture"] );
	}

	if ( file_exists( "../audio/story/" . $row["audio"] ) && $row["audio"] !== null && $row["audio"] !== "" ) {
		$audio_hash = md5_file( "../audio/story/" . $row["audio"] );
	}

	$row['picture_hash'] = $picture_hash;
	$row['audio_hash']   = $audio_hash;

	//------- categories
	$stmt2 = $story_list->runQuery( 'SELECT * FROM story_categories WHERE story_id=:story_id' );
	$stmt2->execute( array( ':story_id' => $row["id"] ) );

	$categories = [];
	while ( $row2 = $stmt2->fetch( PDO::FETCH_ASSOC ) ) {
		array_push( $categories, $row2["cat_id"] );
	}

	//------- questions
	$stmt2 = $story_list->runQuery( 'SELECT * FROM story_question WHERE story_id=:story_id AND deleted=:val' );
	$stmt2->execute( array( ':val' => 0, ':story_id' => $row["id"] ) );

	$questions = [];
	while ( $row2 = $stmt2->fetch( PDO::FETCH_ASSOC ) ) {

		unset( $row2['deleted'] );
		unset( $row2['update_time'] );

		$audio_hash = "";
		if ( file_exists( "../audio/story-question/" . $row2["audio"] ) && $row2["audio"] !== null && $row2["audio"] !== "" ) {
			$audio_hash = md5_file( "../audio/story-question/" . $row2["audio"] );
		}

		$row2['audio_hash'] = $audio_hash;

		$picture_hash = "";
		if ( file_exists( "../pictures/story-question/" . $row2["picture"] ) && $row2["picture"] !== null && $row2["picture"] !== "" ) {
			$picture_hash = md5_file( "../pictures/story-question/" . $row2["picture"] );
		}

		$row2['picture_hash'] = $picture_hash;


		//------- answers
		$stmt3 = $story_list->runQuery( 'SELECT * FROM story_answer WHERE story_id=:story_id AND question_id=:question_id AND deleted=:val' );
		$stmt3->execute( array( ':val' => 0, ':story_id' => $row["id"], ':question_id' => $row2["id"] ) );

		$answers = [];
		while ( $row3 = $stmt3->fetch( PDO::FETCH_ASSOC ) ) {
			unset( $row3['deleted'] );
			unset( $row3['update_time'] );

			$picture_hash = "";
			$audio_hash   = "";
			if ( file_exists( "../pictures/story-answer/" . $row3["picture"] ) && $row3["picture"] !== null && $row3["picture"] !== "" ) {
				$picture_hash = md5_file( "../pictures/story-answer/" . $row3["picture"] );
			}

			if ( file_exists( "../audio/story-answer/" . $row3["audio"] ) && $row3["audio"] !== null && $row3["audio"] !== "" ) {
				$audio_hash = md5_file( "../audio/story-answer/" . $row3["audio"] );
			}

			$row3['picture_hash'] = $picture_hash;
			$row3['audio_hash']   = $audio_hash;


			array_push( $answers, $row3 );
		}
		$row2["answers"] = $answers;


		array_push( $questions, $row2 );
	}


	$row['categories'] = $categories;
	$row['questions']  = $questions;


//	unset( $row['categoryID'] );
//	unset( $row['multi_category'] );
//	unset( $row['deleted'] );
//	unset( $row['dateadded'] );
//	unset( $row['lesson_flag1'] );
//
//	unset( $row['QUESTION_EN'] );
//	unset( $row['QUESTION_TR'] );
//	unset( $row['QUESTION_CH'] );
//	unset( $row['audio_Q_EN'] );
//	unset( $row['audio_Q_TR'] );
//	unset( $row['audio_Q_CH'] );
//	unset( $row['video'] );
//	unset( $row['wordlist'] );
//	unset( $row['update_time'] );
//	unset( $row['userid'] );
//
//	$row['categoryID'] = $row['cat_id'];
//	unset( $row['cat_id'] );
//
//	$picture_hash= "";
//	$audio_en_hash = "";
//	$audio_tr_hash = "";
//	$audio_ch_hash = "";
//
//	if ( file_exists( "../pictures/" . $row["picture"] ) ) {
//		$picture_hash = $md5file = md5_file( "../pictures/" . $row["picture"] );
//	}
//
//	if ( file_exists( "../audio/en/" . $row["audio_EN"] ) ) {
//		$audio_en_hash = $md5file = md5_file( "../audio/en/" . $row["audio_EN"] );
//	}
//
//	if ( file_exists( "../audio/tr/" . $row["audio_TR"] ) ) {
//		$audio_tr_hash = $md5file = md5_file( "../audio/tr/" . $row["audio_TR"] );
//	}
//
//	if ( file_exists( "../audio/ch/" . $row["audio_CH"] ) ) {
//		$audio_ch_hash = $md5file = md5_file( "../audio/ch/" . $row["audio_CH"] );
//	}
//	$row['picture_hash'] = $picture_hash;
//	$row['audio_en_hash'] = $audio_en_hash;
//	$row['audio_tr_hash'] = $audio_tr_hash;
//	$row['audio_ch_hash'] = $audio_ch_hash;

	array_push( $result, $row );
}
echo json_encode( $result );
?>
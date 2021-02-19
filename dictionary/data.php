<?php
require_once( "../config/class.dictionary.php" );
$dictionary_list = new DICTIONARY();

require_once( "../config/class.category.php" );
$category_list = new CATEGORY();

header( "Content-type: application/json; charset=utf-8" );
$stmt = $dictionary_list->runQuery( 'SELECT * FROM dictionary WHERE deleted=:val' );
$stmt->execute( array( ':val' => 0 ) );
$result = [];
while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {

	$stmt2 = $dictionary_list->runQuery( 'SELECT * FROM category WHERE deleted=:val AND id=:id' );
	$stmt2->execute( array( ':val' => 0, ':id' => $row["categoryID"] ) );
	if ( $row2 = $stmt2->fetch( PDO::FETCH_ASSOC ) ) {
		$row['categoryParentID'] = $row2["parentID"];
	} else {
		$row['categoryParentID'] = "99999";
	}


	$picture_hash= "";
	$audio_en_hash = "";
	$audio_tr_hash = "";
	$audio_ch_hash = "";

	if ( file_exists( "../pictures/" . $row["picture"] ) ) {
		$picture_hash = $md5file = md5_file( "../pictures/" . $row["picture"] );
	}

	if ( file_exists( "../audio/en/" . $row["audio_EN"] ) ) {
		$audio_en_hash = $md5file = md5_file( "../audio/en/" . $row["audio_EN"] );
	}

	if ( file_exists( "../audio/tr/" . $row["audio_TR"] ) ) {
		$audio_tr_hash = $md5file = md5_file( "../audio/tr/" . $row["audio_TR"] );
	}

	if ( file_exists( "../audio/ch/" . $row["audio_CH"] ) ) {
		$audio_ch_hash = $md5file = md5_file( "../audio/ch/" . $row["audio_CH"] );
	}
	$row['picture_hash'] = $picture_hash;
	$row['audio_en_hash'] = $audio_en_hash;
	$row['audio_tr_hash'] = $audio_tr_hash;
	$row['audio_ch_hash'] = $audio_ch_hash;

	array_push( $result, $row );
//  echo $row['id'];
//  echo $row['word_EN'];
//  echo $row['word_TR'];
//  echo $row['word_CH'];
//  echo $row['categoryID'];
//  echo $row['picture'];
//  echo $row['level'];
//  echo $row['bopomofo'];
//  echo $row['audio_EN'];
//  echo $row['audio_CH'];
//  echo $row['audio_TR'];
//  echo $category_list->category_full_path_string( $row['categoryID'] );
//  echo $dateadded;

//  echo "\n";
}
echo json_encode( $result );
?>
<?php
require_once( "../config/session.php" );
require_once( "../config/class.user.php" );

require '../vendor/autoload.php';

require_once( "../config/class.dictionary.php" );
$dictionary_list = new DICTIONARY();

require_once( "../config/class.category.php" );
$category_list = new CATEGORY();

header("Content-type: application/json; charset=utf-8");
$stmt = $dictionary_list->runQuery( 'SELECT * FROM dictionary WHERE deleted=:val' );
$stmt->execute( array( ':val' => 0 ) );
$result = [];
while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {

  array_push($result,$row);
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
echo json_encode($result);
?>
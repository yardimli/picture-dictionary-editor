<?php
require_once( "../config/class.dictionary.php" );
$dictionary_list = new DICTIONARY();

require_once( "../config/class.category.php" );
$category_list = new CATEGORY();

header( "Content-type: application/json; charset=utf-8" );


$cat_array = $category_list->all_categories( 0 );
$result    = [];

function loopArray( $arr, $parent ) {
	global $result;
	for ( $i = 0; $i < count( $arr ); $i ++ ) {
		if ( count( $arr[ $i ]["children"] ) > 0 ) {
			if ( $parent === "" ) {

				array_push( $result, [
					"id"       => $arr[ $i ]["id"],
					"name"     => $arr[ $i ]["name"],
					"parentID" => $arr[ $i ]["parentID"],
					"parent"   => $parent,
					"fullpath" => $arr[ $i ]["name"]
				] );

				loopArray( $arr[ $i ]["children"], $arr[ $i ]["name"] );
			} else {

				array_push( $result, [
					"id"       => $arr[ $i ]["id"],
					"name"     => $arr[ $i ]["name"],
					"parentID" => $arr[ $i ]["parentID"],
					"parent"   => $parent,
					"fullpath" => $parent . " / " . $arr[ $i ]["name"]
				] );

				loopArray( $arr[ $i ]["children"], $parent . " / " . $arr[ $i ]["name"] );
			}
		} else {

			array_push( $result, [
				"id"       => $arr[ $i ]["id"],
				"name"     => $arr[ $i ]["name"],
				"parentID" => $arr[ $i ]["parentID"],
				"parent"   => $parent,
				"fullpath" => $parent . " / " . $arr[ $i ]["name"]
			] );
		}
	}
}

loopArray( $cat_array, "");

echo json_encode( $result );
?>
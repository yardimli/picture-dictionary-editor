<?php
require_once( "./config/session.php" );
require_once( "./config/class.user.php" );

require_once( "./config/class.category.php" );
$category_list = new CATEGORY();

require_once( "./config/class.dictionary.php" );
$dictionary_list = new DICTIONARY();


$string = file_get_contents( "list1.json" );
$json_a = json_decode( $string, true );

//var_dump($json_a);


// Define function
function print_recursive( $arr, $nameA, $dataPathA ) {
	global $category_list;
	global $dictionary_list;
	$name        = $nameA;
	$picture     = "";
	$wordX       = "";
	$wordPicture = "";
	$dataPath = $dataPathA;

	foreach ( $arr as $key => $val ) {
		if ( is_array( $val ) ) {
			if ( $name !== $nameA ) {
				echo "<b>>Category:" . $name . ", Image:" . $picture . ", Parent:" . $nameA . ", data path:".$dataPath."</b><br>";


//				if ( $category_list->add_category( $name, "", "", $picture, $nameA,-1 ) ) {
//					echo "<p>Category Added</strong></p>";
//				} else {
//					echo "<br>";
//				}
			}
//			echo "Rec: ".$key." (".$name.")<br>";
			print_recursive( $val, $name,$dataPath );

		} else {
			if ( $key === "icon" ) {
				$picture     = $val;
				$wordPicture = $val;
			}

			if ( $key === "word" ) {
				$wordX = $val;
			}

			if ( $key === "name" ) {
				$name = $val;
			}

			if ( $key === "dataPath" ) {
				$dataPath = $val;
			}

			if ( $wordX !== "" && $wordPicture !== "" && !file_exists("../pictures/svg/".$wordPicture) ) {
				echo $dataPathA. "/" . $wordPicture."<br>";
				file_put_contents("../pictures/svg/".$wordPicture,  file_get_contents("https://kids.wordsmyth.net/".str_replace("app","wild",$dataPathA). "/" . $wordPicture) );
//				if ( $dictionary_list->add_word( $wordX, "", "", $wordPicture, $nameA ) ) {
//					echo "<p>Word Added</strong></p>";
//				}
				$wordPicture = "";
				$wordX       = "";
			}

//			echo( "$nameA) $key = $val <br/>" );


		}
	}

	return;
}

// Call function
print_recursive( $json_a, "Root", "" );


$jsonIterator = new RecursiveIteratorIterator(
	new RecursiveArrayIterator( json_decode( $string, true ) ),
	RecursiveIteratorIterator::SELF_FIRST );

foreach ( $jsonIterator as $key => $val ) {
	if ( is_array( $val ) ) {
		echo "$key:<br>";
	} else {
		if ( $key === "name" || $key === "word" ) {
			echo "$key => $val<br>";
		}
	}
}
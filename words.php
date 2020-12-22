<?php
require_once( "./config/session.php" );

require_once( "./config/class.dictionary.php" );
$dictionary_list = new DICTIONARY();

require_once( "./config/class.category.php" );
$category_list = new CATEGORY();

require_once( "./config/class.user.php" );
$auth_user = new USER();

$user_id = $_SESSION['user_session'];
# get logged in user details through session id
$stmt = $auth_user->runQuery( "SELECT username, useremail, dateadded FROM users WHERE id=:user_id" );
$stmt->execute( array( ":user_id" => $user_id ) );
$userRow  = $stmt->fetch( PDO::FETCH_ASSOC );
$lu_uname = $userRow['username'];
$lu_email = $userRow['useremail'];
$lu_date  = $userRow['dateadded'];
$date     = new DateTime( $lu_date );

if ( ! $auth_user->is_loggedin() ) {
	echo json_encode( array( "result" => false ) );
} else {

	$stmt = $dictionary_list->runQuery( "SELECT * FROM dictionary WHERE deleted=:val " );
	$stmt->execute( array( ':val' => 0 ) );
//	$stmt->execute( array( ':val' => 0, ':catid' => $_GET["catid"] ) );
	$stmt->execute();
	$result_array = [];
	while ( $p_word = $stmt->fetch() ) {
		$dateAdded = new DateTime( $p_word['dateadded'] );
		$dateadded = $dateAdded->format( 'M. d, Y h:m.s' );
		array_push( $result_array, array( "id"         => $p_word["id"],
		                                  "dateAdded"  => $dateAdded,
		                                  "word_EN"    => $p_word['word_EN'],
		                                  "word_TR"    => $p_word['word_TR'],
		                                  "word_CH"    => $p_word['word_CH'],
		                                  "categoryID" => $p_word['categoryID'],
		                                  "picture"    => $p_word['picture'],
		                                  "level"      => $p_word['level'],
		                                  "bopomofo"   => $p_word['bopomofo'],
		                                  "audio_EN"   => $p_word['audio_EN'],
		                                  "audio_TR"   => $p_word['audio_TR'],
		                                  "audio_CH"   => $p_word['audio_CH'],
		                                  "category"   => $category_list->category_full_path_string( $p_word['categoryID'] )
		) );

	}
	echo json_encode( array( "result" => true, "words" => $result_array ) );

}
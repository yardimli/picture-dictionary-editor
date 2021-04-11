<?php
require_once( "./config/session.php" );

require __DIR__ . '/vendor/autoload.php';

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


$error_msg = "";

clearstatcache();

if ( ! $auth_user->is_loggedin() ) {
	# redirect to login page, gtfo
	$auth_user->doLogout();
} else {
	echo "<div class=\"alert alert-success\" role=\"alert\">";
	if ( $_POST["language"] === "en" && $_POST["word_id"] === "0" ) {
		if ( $dictionary_list->add_word_en( $_POST["word_EN"], $_POST["category_id"], $_POST["level"] ) ) {
			echo "<p>English Word Text data Added</strong></p>";
		}
	} else {
		if ( $_POST["language"] === "en" ) {
			if ( $dictionary_list->update_word_en( $_POST["word_id"], $_POST["word_EN"], $_POST["category_id"], $_POST["level"] ) ) {
				echo "<p>English Word Text data Updated</strong></p>";
			}
		}

		if ( $_POST["language"] === "tr" ) {
			if ( $dictionary_list->update_word_tr( $_POST["word_id"], $_POST["word_TR"] ) ) {
				echo "<p>Turkish Word Text data Updated</strong></p>";
			}
		}

		if ( $_POST["language"] === "ch" ) {
			if ( $dictionary_list->update_word_ch( $_POST["word_id"], $_POST["word_CH"], $_POST["bopomofo"] ) ) {
				echo "<p>Turkish Word Text data Updated</strong></p>";
			}
		}

	}
	echo "</div>";
}
?>
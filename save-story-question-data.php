<?php
require_once( "./config/session.php" );

require __DIR__ . '/vendor/autoload.php';

require_once( "./config/class.story.php" );
$story_list = new STORY();

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
	if ( $_POST["question_id"] === "0" ) {
		if ( $story_list->add_story_question( $_POST["story_id"], $_POST["question"], $_POST["show_answer_pictures"], $_POST["random_answers_from_other_questions"], $_POST["random_answers_from_same_question"] ) ) {
			echo "<p>Story Question data Added</strong></p>";
		}
	} else if ( $story_list->update_story_question( $_POST["question_id"], $_POST["story_id"], $_POST["question"], $_POST["show_answer_pictures"], $_POST["random_answers_from_other_questions"], $_POST["random_answers_from_same_question"] ) ) {
		echo "<p>Story Question data Updated</strong></p>";
	}

	echo "</div>";
}
?>
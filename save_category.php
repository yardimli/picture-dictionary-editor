<?php
require_once( "./config/session.php" );
require_once( "./config/class.user.php" );

require_once( "./config/class.category.php" );
$category_list = new CATEGORY();

$auth_user = new USER();
if ( ! $auth_user->is_loggedin() ) {
	# redirect to login page, gtfo
	$auth_user->doLogout();
} else {


	$HasError = false;

	if ( $HasError ) {
		echo "<div class=\"alert alert-danger\" role=\"alert\">Post Error.</div>";
	} else if ( $_POST["category_id"] === "0" ) {
		if ( $category_list->add_category( $_POST["category_EN"], "", "", "", $_POST["parent_en"], $_POST["parent_id"] ) ) {
			echo "<p>Category Added</strong></p>";
		}
	} else {
		if ( $category_list->update_category( $_POST["category_EN"], "", "", "", $_POST["parent_en"], $_POST["parent_id"], $_POST["category_id"] ) ) {
			echo "<p>Category Updated</strong></p>";
		}
	}

}
?>
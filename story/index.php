<?php
ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( E_ALL );

require_once( "../config/session.php" );
require_once( "../config/class.user.php" );

require '../vendor/autoload.php';

require_once( "../config/class.story.php" );
$story_list = new STORY();

require_once( "../config/class.category.php" );
$category_list = new CATEGORY();


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

if ( isset( $_GET['clone_story'] ) ) {
# check if user is logged-in
	if ( ! $auth_user->is_loggedin() ) {
		# redirect to login page, gtfo
		$auth_user->doLogout();
	} else {
		$id = intval( $_GET['clone_story'] );

		$stmt1 = $story_list->runQuery( 'SELECT * FROM story WHERE id=:id AND deleted=:val' );
		$stmt1->execute( array( ':val' => 0, ":id" => $id ) );
		if ( $row1 = $stmt1->fetch( PDO::FETCH_ASSOC ) ) {

			//******************* add story
			if ( $story_list->add_story( $row1["title"] . " [clone]", $row1["story"], $row1["language"], [] ) ) {

				$stmt2 = $story_list->runQuery( 'SELECT * FROM story WHERE deleted=:val ORDER BY id DESC LIMIT 1' );
				$stmt2->execute( array( ':val' => 0 ) );
				if ( $row2 = $stmt2->fetch( PDO::FETCH_ASSOC ) ) {
					$new_story_id = $row2["id"];
//					echo "!!!!!" . $new_story_id . "!!!!!!";

					$stmt2 = $story_list->runQuery( 'UPDATE story set picture="' . $row1["picture"] . '", audio="' . $row1["audio"] . '" WHERE id=' . $new_story_id );
					$stmt2->execute();

					$stmt3 = $story_list->runQuery( 'SELECT * FROM story_question WHERE story_id=:id AND deleted=:val' );
					$stmt3->execute( array( ':val' => 0, ":id" => $id ) );
					while ( $row3 = $stmt3->fetch( PDO::FETCH_ASSOC ) ) {

						//******************* add question
						if ( $story_list->add_story_question( $new_story_id, $row3["question"], $row3["show_answer_pictures"], $row3["random_answers_from_other_questions"], $row3["random_answers_from_same_question"] ) ) {

							$stmt4 = $story_list->runQuery( 'SELECT * FROM story_question WHERE deleted=:val ORDER BY id DESC LIMIT 1' );
							$stmt4->execute( array( ':val' => 0 ) );
							if ( $row4 = $stmt4->fetch( PDO::FETCH_ASSOC ) ) {
								$new_question_id = $row4["id"];
//								echo "!!!!!" . $new_question_id . "!!!!!!";

								$stmt5 = $story_list->runQuery( 'UPDATE story_question set picture="' . $row3["picture"] . '", audio="' . $row3["audio"] . '" WHERE id=' . $new_question_id );
								$stmt5->execute();


								$stmt6 = $story_list->runQuery( 'SELECT * FROM story_answer WHERE question_id=:id AND deleted=:val' );
								$stmt6->execute( array( ':val' => 0, ":id" => $row3["id"] ) );
								while ( $row6 = $stmt6->fetch( PDO::FETCH_ASSOC ) ) {

									//******************* add answer
									if ( $story_list->add_story_answer( $new_story_id, $new_question_id, $row6["answer"], $row6["is_correct"] ) ) {

										$stmt7 = $story_list->runQuery( 'SELECT * FROM story_answer WHERE deleted=:val ORDER BY id DESC LIMIT 1' );
										$stmt7->execute( array( ':val' => 0 ) );
										if ( $row7 = $stmt7->fetch( PDO::FETCH_ASSOC ) ) {
											$new_answer_id = $row7["id"];
//											echo "!!!!!" . $new_answer_id . "!!!!!!";

											$stmt5 = $story_list->runQuery( 'UPDATE story_answer set picture="' . $row6["picture"] . '", audio="' . $row6["audio"] . '" WHERE id=' . $new_answer_id );
											$stmt5->execute();
										}
									} else
									{
										echo "add answer failed.";
									}
								}


							}
						} else
						{
							echo "add question failed.";
						}
					}
				}
			} else
			{
				echo "add story failed.";
			}
		}
	}
	header( "Location: /picture-dictionary-editor/story/" );
}

# for deleting
if ( isset( $_GET['delete_story_id'] ) ) {
	# check if user is logged-in
	if ( ! $auth_user->is_loggedin() ) {
		# redirect to login page, gtfo
		$auth_user->doLogout();
	} else {
		$id = intval( $_GET['delete_story_id'] );

		try {
			if ( $story_list->delete_story( $id ) ) {
				//....
			}
		} catch ( PDOException $e ) {
			echo $e->getMessage();
		}
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Ege Learn Surface | Stories</title>
	<!-- Tell the browser to be responsive to screen width -->
	<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
	<!-- Bootstrap 3.3.6 -->
	<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>bootstrap/css/bootstrap.min.css">
	<!-- Font Awesome -->
	<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>dist/font-awesome-4.7.0/css/font-awesome.css">
	<!-- Ionicons -->
	<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>dist/ionicons-2.0.1/css/ionicons.css">
	<!-- DataTables -->
	<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>plugins/datatables/dataTables.bootstrap.css">
	<!-- Theme style -->
	<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>dist/css/AdminLTE.min.css">
	<!-- AdminLTE Skins. Choose a skin from the css/skins
	folder instead of downloading all of them to reduce the load. -->
	<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>dist/css/skins/_all-skins.min.css">
	<!-- Pace style -->
	<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>plugins/pace/pace.css">

	<link rel="stylesheet" href="<?php echo WEB_ROOT; ?>/dist/css/bootstrap-select.min.css">

	<link rel="stylesheet" href="story.css">
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
	<?php include( $_SERVER["DOCUMENT_ROOT"] . WEB_ROOT . 'header.php' ); ?>
	<?php include( $_SERVER["DOCUMENT_ROOT"] . WEB_ROOT . 'sidebar.php' ); ?>
	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				List of Stories
				<small>Stories &nbsp;&nbsp;
					<button type="button" class="btn btn-primary btn-flat btn-sm" id="add_story_btn"><i class="fa fa-plus-circle"></i> Add Story
					</button>
				</small>

				<small>Filter Category:</small>
				<div style=" display: inline-block !important;" class="form-group">
					<?php
					$cat_array = $category_list->all_categories( 0 );
					?>
					<select class="form-control" required id="category_filter_id" name="category_filter_id" style="width:400px; display: inline-block !important;">
						<?php
						function loopArray2( $arr, $parent ) {
							for ( $i = 0; $i < count( $arr ); $i ++ ) {
								if ( count( $arr[ $i ]["children"] ) > 0 ) {
									if ( $parent === "" ) {
										echo "<option value=''";
										if ( array_key_exists( "catid", $_GET ) && "" === $_GET["catid"] ) {
											echo " SELECTED ";
										}
										echo ">" . $arr[ $i ]["name"] . "</option>";
										loopArray2( $arr[ $i ]["children"], $arr[ $i ]["name"] );
									} else {
										echo "<option value='" . $arr[ $i ]["id"] . "'";
										if ( array_key_exists( "catid", $_GET ) && $arr[ $i ]["id"] === $_GET["catid"] ) {
											echo " SELECTED ";
										}
										echo ">" . $parent . " / " . $arr[ $i ]["name"] . "</option>";
										loopArray2( $arr[ $i ]["children"], $parent . " / " . $arr[ $i ]["name"] );
									}
								} else {
									echo "<option value='" . $arr[ $i ]["id"] . "'";
									if ( array_key_exists( "catid", $_GET ) && $arr[ $i ]["id"] === $_GET["catid"] ) {
										echo " SELECTED ";
									}
									echo ">" . $parent . " / " . $arr[ $i ]["name"] . "</option>";
								}
							}
						}

						loopArray2( $cat_array, "" );
						?>
					</select>
				</div>
				<small>Language:</small>
				<div style=" display: inline-block !important;" class="form-group">
					<select class="form-control" required id="language_filter_id" name="language_filter_id" style="width:130px; display: inline-block !important;">
						<option value="">All Languages</option>
						<option value="en" <?php
						if ( array_key_exists( "lang", $_GET ) && $_GET["lang"] === "en" ) {
							echo " SELECTED ";
						}
						?>>English
						</option>
						<option value="tr" <?php
						if ( array_key_exists( "lang", $_GET ) && $_GET["lang"] === "tr" ) {
							echo " SELECTED ";
						}
						?>>Turkish
						</option>
						<option value="ch" <?php
						if ( array_key_exists( "lang", $_GET ) && $_GET["lang"] === "ch" ) {
							echo " SELECTED ";
						}
						?>>Chinese
						</option>
					</select>
				</div>

			</h1>
			<ol class="breadcrumb">
				<li><a href="<?php echo WEB_ROOT; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">List of Stories</li>
			</ol>
		</section>
		<!-- Main content -->
		<section class="content">
			<div class="row">
				<div class="col-xs-12">
					<div class="box">
						<div class="box-body">
							<?php
							if ( isset( $error ) ) {
								?>
								<div class="alert alert-warning alert-dismissible flat">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
									<h4><i class="icon fa fa-warning"></i> Warning</h4>
									<?php echo $error; ?>
								</div>
							<?php } else if ( isset( $_GET['confirm'] ) ) { ?>
								<div class="alert alert-success alert-dismissible flat">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
									<h4><i class="icon fa fa-check"></i> Confirmation</h4>
									New user added.
								</div>
							<?php } else if ( isset( $_GET['update'] ) ) { ?>
								<div class="alert alert-info alert-dismissible flat">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
									<h4><i class="icon fa fa-check"></i> Confirmation</h4>
									User updated.
								</div>
							<?php } else if ( isset( $_GET['deleted'] ) ) { ?>
								<div class="alert alert-info alert-dismissible flat">
									<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
									<h4><i class="icon fa fa-check"></i> Confirmation</h4>
									User deleted.
								</div>
							<?php } ?>
							<table id="example1" class="table table-bordered table-striped">
								<thead>
								<tr>
									<th>id</th>
									<th>Story</th>
									<th>Language</th>
									<th>Category</th>
									<th>Last Update</th>
									<th>Action</th>
								</tr>
								</thead>
								<tbody>
								<?php
								$x = 0;
								if ( array_key_exists( "catid", $_GET ) && is_numeric( $_GET["catid"] ) && $_GET["catid"] !== "0" ) {
									$stmt = $story_list->runQuery( "SELECT * FROM category WHERE parentID=:val " );
									$stmt->execute( array( ':val' => $_GET["catid"] ) );
									$stmt->execute();
									$category_id_array = [ $_GET["catid"] ];
									while ( $p_category = $stmt->fetch() ) {
										array_push( $category_id_array, $p_category["id"] );
									}
									$cates = "(" . implode( ",", $category_id_array ) . ")";
//									echo $cates;

									$language_filter = "%";
									if ( array_key_exists( "lang", $_GET ) && $_GET["lang"] !== "" ) {
										$language_filter = $_GET["lang"];
									}

									$stmt = $story_list->runQuery( 'SELECT DISTINCT story.* FROM story LEFT JOIN story_categories ON story_categories.story_id=story.id  WHERE deleted=:val AND language LIKE :language AND story_categories.cat_id IN ' . $cates );
									$stmt->execute( array( ':val' => 0, ":language" => $language_filter ) );
								} else {

									$language_filter = "%";
									if ( array_key_exists( "lang", $_GET ) && $_GET["lang"] !== "" ) {
										$language_filter = $_GET["lang"];
									}

									$stmt = $story_list->runQuery( 'SELECT * FROM story WHERE deleted=:val AND language LIKE :language ' );
									$stmt->execute( array( ':val' => 0, ":language" => $language_filter ) );
								}
								while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
									$x ++;
									$dateAdded = new DateTime( $row['update_time'] );
									$dateadded = $dateAdded->format( 'M. d, Y h:m.s' );

									$stmt_cat = $story_list->runQuery( 'SELECT category.* FROM story_categories LEFT JOIN category ON category.id = story_categories.cat_id WHERE story_id =:story_id' );
									$stmt_cat->execute( array( ':story_id' => $row['id'] ) );
									$category_ids     = [];
									$category_strings = "";
									while ( $row_cat = $stmt_cat->fetch( PDO::FETCH_ASSOC ) ) {
										array_push( $category_ids, $row_cat["id"] );
										if ( $category_strings !== "" ) {
											$category_strings .= ", ";
										}

										$category_strings .= $category_list->category_full_path_string( $row_cat['id'] );
									}


									?>
									<tr>
										<td><?php echo $row['id']; ?></td>
										<td><?php echo $row['title']; ?></td>

										<td><?php echo $row['language']; ?></td>
										<td><?php
											//echo $category_list->category_full_path_string( $row['categoryID'] );
											echo $category_strings;
											?></td>
										<td><?php echo $dateadded; ?></td>
										<td style="white-space: nowrap;">
											<button type="button" class="btn btn-primary btn-flat edit-story-btn" title="edit record" data-title="<?php echo $row['title']; ?>"
											        data-story="<?php echo $row['story']; ?>"
											        data-language="<?php echo $row['language']; ?>"
											        data-multi_category="<?php echo implode( ",", $category_ids ); ?>"
											        data-story_id="<?php echo $row['id']; ?>"
											        data-hide_after_intro="<?php echo $row['hide_after_intro']; ?>"
											        data-picture="<?php echo $row['picture']; ?>"
											        data-audio="<?php echo $row['audio']; ?>"><i class="fa fa-edit"></i> Edit
											</button>

											<a href="/picture-dictionary-editor/story_question/?story_id=<?php echo $row['id']; ?>" class="btn btn-primary btn-flat"><i
													class="fa fa-question"></i> Questions</a>

											<a href="/picture-dictionary-editor/story/?clone_story=<?php echo $row['id']; ?>" class="btn btn-primary btn-flat"><i class="fa fa-clone"></i> Clone</a>
											&nbsp;
											<button type="button" class="btn btn-danger btn-flat" onClick="window.location.href='javascript:delete_story(<?php echo $row['id']; ?>);'"><i
													class="fa fa-trash"></i> Delete
											</button>

										</td>
									</tr>
								<?php } ?>
								</tbody>
								<tfoot>
								<tr>
									<th>id</th>
									<th>Story</th>
									<th>Language</th>
									<th>Category</th>
									<th>Last Update</th>
									<th>Action</th>
								</tr>
								</tfoot>
							</table>
						</div>
						<!-- /.box-body -->

						<?php include "edit_story_modals.php"; ?>

		</section>
	</div>
	<!-- /.box -->
</div>
<!-- /.col -->
</div>
<!-- /.row -->
</section>
<!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php include_once "../footer.php"; ?>
<!-- ./wrapper -->
<!-- jQuery 2.2.3 -->
<script src="<?php echo WEB_ROOT; ?>plugins/jQuery/jquery-2.2.3.min.js"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?php echo WEB_ROOT; ?>bootstrap/js/bootstrap.min.js"></script>
<!-- PACE -->
<script src="<?php echo WEB_ROOT; ?>plugins/pace/pace.min.js"></script>
<!-- DataTables -->
<script src="<?php echo WEB_ROOT; ?>plugins/datatables/jquery.dataTables.min.js"></script>
<script src="<?php echo WEB_ROOT; ?>plugins/datatables/dataTables.bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="<?php echo WEB_ROOT; ?>plugins/slimScroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo WEB_ROOT; ?>plugins/fastclick/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo WEB_ROOT; ?>dist/js/app.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo WEB_ROOT; ?>dist/js/demo.js"></script>

<script src="<?php echo WEB_ROOT; ?>dist/js/bootstrap-select.min.js"></script>

<script src="story.js"></script>
</body>
</html>
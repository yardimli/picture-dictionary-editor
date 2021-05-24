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

# for deleting
if(isset($_GET['delete_answer_id']))
{
	# check if user is logged-in
	if(!$auth_user->is_loggedin())
	{
		# redirect to login page, gtfo
		$auth_user->doLogout();
	}
	else
	{
		$id = intval($_GET['delete_answer_id']);

		try
		{
			if($story_list->delete_answer($id)) {
				//....
			}
		}
		catch(PDOException $e)
		{
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
	<title>Ege Learn Surface | Story Answers</title>
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

	<link rel="stylesheet" href="story-answer.css">
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
				List of Story Answers
				<small>Answers &nbsp;&nbsp;
					<button type="button" class="btn btn-primary btn-flat btn-sm" id="add_story_answer_btn"><i class="fa fa-plus-circle"></i> Add Story Answer
					</button>
				</small>

				<small>Filter Story/Question:</small>
				<div style=" display: inline-block !important;" class="form-group">
					<select class="form-control" required id="question_filter_id" name="question_filter_id" style="width:400px; display: inline-block !important;">
						echo "
						<option value=''>Select Story/Question</option>
						";
						<?php
						$stmt = $story_list->runQuery( 'SELECT story_question.*, story.title as title FROM story_question LEFT join story ON story.id=story_question.story_id WHERE story_question.deleted=:val  ORDER by title ASC, question ASC' );
						$stmt->execute( array( ':val' => 0 ) );
						while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {

							echo "<option value='" . $row["id"] . "'";
							if ( array_key_exists( "question_id", $_GET ) && $_GET["question_id"] === $row["id"] ) {
								echo " selected ";
							}
							echo ">" . $row["title"] ." / " . $row["question"] . "</option>";
						}
						?>
					</select>
				</div>

			</h1>
			<ol class="breadcrumb">
				<li><a href="<?php echo WEB_ROOT; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">List of Story Answers</li>
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
									<th>Story / Question</th>
									<th>Answer</th>
									<th>Last Update</th>
									<th>Action</th>
								</tr>
								</thead>
								<tbody>
								<?php

								if ( array_key_exists( "question_id", $_GET ) && is_numeric( $_GET["question_id"] ) && $_GET["question_id"] !== "0" ) {
									$stmt = $story_list->runQuery( 'SELECT story_answer.*,story.title AS story_title, story.language AS language, story.story AS story, story_question.question AS story_question FROM story_answer 
									LEFT JOIN story ON story.id=story_answer.story_id 
									LEFT JOIN story_question ON story_question.id=story_answer.question_id 
									WHERE story_answer.question_id=:question_id AND story_answer.deleted=:val' );
									$stmt->execute( array( ':question_id' => $_GET["question_id"], ':val' => 0 ) );
								} else {
									$stmt = $story_list->runQuery( 'SELECT story_answer.*, story.title AS story_title, story.language AS language, story.story AS story, story_question.question AS story_question FROM story_answer 
									LEFT JOIN story ON story.id=story_answer.story_id 
									LEFT JOIN story_question ON story_question.id=story_answer.question_id 
									WHERE story_answer.deleted=:val' );
									$stmt->execute( array( ':val' => 0 ) );
								}

								while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
									$dateAdded = new DateTime( $row['update_time'] );
									$dateadded = $dateAdded->format( 'M. d, Y h:m.s' );

									?>
									<tr>
										<td><?php echo $row['id']; ?></td>
										<td><?php echo $row['story_title'] . " (" . $row['language'] . ").<br>".$row['story_question']; ?></td>
										<td><?php
											if ($row['is_correct']==="1") { echo "<span style='font-weight:bold'>"; }
											echo $row['answer'];
											if ($row['is_correct']==="1") { echo "</span>"; }
											?></td>
										<td><?php echo $dateadded; ?></td>
										<td style="white-space: nowrap;">
											<button type="button" class="btn btn-primary btn-flat edit-story-answer-btn" title="edit record"     data-picture="<?php echo $row['picture']; ?>"
											        data-answer="<?php echo $row['answer']; ?>"
											        data-answer_id="<?php echo $row['id']; ?>"
											        data-is_correct="<?php echo $row['is_correct']; ?>"
											        data-question_id="<?php echo $row['question_id']; ?>"
											        data-story_id="<?php echo $row['story_id']; ?>"
											        data-story="<?php echo $row['story']; ?>"
											        data-language="<?php echo $row['language']; ?>"
											        data-audio="<?php echo $row['audio']; ?>"><i class="fa fa-edit"></i> Edit</button>

											<button type="button" class="btn btn-danger btn-flat" onClick="window.location.href='javascript:delete_answer(<?php echo $row['id']; ?>);'"><i class="fa fa-trash"></i> Delete</button>
										</td>
									</tr>
								<?php } ?>
								</tbody>
								<tfoot>
								<tr>
									<th>id</th>
									<th>Story</th>
									<th>Answer</th>
									<th>Last Update</th>
									<th>Action</th>
								</tr>
								</tfoot>
							</table>
						</div>
						<!-- /.box-body -->

						<?php include "edit_story_answer_modals.php"; ?>

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
<script>
  var new_answer_question_id = <?php
		if ( array_key_exists( "question_id", $_GET ) && is_numeric( $_GET["question_id"] ) && $_GET["question_id"] !== "0" ) {
			echo $_GET["question_id"];
		} else {
			echo "0";
		}
		?>;

  var stories_array = [<?php

		$stmt = $story_list->runQuery( 'SELECT story_question.*, story.title as title, story.story AS story, story.id AS s_id FROM story_question LEFT join story ON story.id=story_question.story_id WHERE story_question.deleted=:val' );
		$stmt->execute( array( ':val' => 0 ) );
		while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {

			echo "{ 'q_id' : '" . $row['id'] . "', 's_id' : '" . $row['s_id'] . "', 'story' : '" . $row['story'] . "', 'title' : '" . $row['title'] . "', 'question' : '" . $row['question'] . "'},";
		}
		?>];
</script>
<script src="story-answer.js"></script>
</body>
</html>
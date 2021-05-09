<?php
ini_set( 'display_errors', 1 );
ini_set( 'display_startup_errors', 1 );
error_reporting( E_ALL );

require_once( "../config/session.php" );
require_once( "../config/class.user.php" );

require '../vendor/autoload.php';

require_once( "../config/class.dictionary.php" );
$dictionary_list = new DICTIONARY();

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
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Ege Learn Surface | Dictionary</title>
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

	<link rel="stylesheet" href="dictionary.css">
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
				List of Words
				<small>Dictionary &nbsp;&nbsp;
					<button type="button" class="btn btn-primary btn-flat btn-sm" id="add_word_btn"><i class="fa fa-plus-circle"></i> Add Word
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
										echo "<option value='" . $arr[ $i ]["id"] . "'>" . $arr[ $i ]["name"] . "</option>";
										loopArray2( $arr[ $i ]["children"], $arr[ $i ]["name"] );
									} else {
										echo "<option value='" . $arr[ $i ]["id"] . "'>" . $parent . " / " . $arr[ $i ]["name"] . "</option>";
										loopArray2( $arr[ $i ]["children"], $parent . " / " . $arr[ $i ]["name"] );
									}
								} else {
									echo "<option value='" . $arr[ $i ]["id"] . "'>" . $parent . " / " . $arr[ $i ]["name"] . "</option>";
								}
							}
						}

						loopArray2( $cat_array, "" );
						?>
					</select>
				</div>

			</h1>
			<ol class="breadcrumb">
				<li><a href="<?php echo WEB_ROOT; ?>"><i class="fa fa-dashboard"></i> Home</a></li>
				<li class="active">List of Dictionary</li>
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
									<th>English</th>
									<th>Turkish</th>
									<th>Chinese</th>
									<th>Category</th>
									<th>Level</th>
									<th>Last Update</th>
								</tr>
								</thead>
								<tbody>
								<?php
								$x = 0;
								if ( array_key_exists( "catid", $_GET ) && is_numeric( $_GET["catid"] ) && $_GET["catid"] !== "0" ) {
									$stmt = $dictionary_list->runQuery( "SELECT * FROM category WHERE parentID=:val " );
									$stmt->execute( array( ':val' => $_GET["catid"] ) );
									$stmt->execute();
									$category_id_array = [ $_GET["catid"] ];
									while ( $p_category = $stmt->fetch() ) {
										array_push( $category_id_array, $p_category["id"] );
									}
									$cates = "(" . implode( ",", $category_id_array ) . ")";
//									echo $cates;
									$stmt = $dictionary_list->runQuery( 'SELECT DISTINCT dictionary.* FROM dictionary LEFT JOIN word_categories ON word_categories.word_id=dictionary.id  WHERE word_categories.cat_id IN ' . $cates );
									$stmt->execute( array( ':val' => 0 ) );
								} else {
									$stmt = $dictionary_list->runQuery( 'SELECT * FROM dictionary WHERE deleted=:val' );
									$stmt->execute( array( ':val' => 0 ) );
								}
								while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
									$x ++;
									$dateAdded = new DateTime( $row['update_time'] );
									$dateadded = $dateAdded->format( 'M. d, Y h:m.s' );

									$stmt_cat = $dictionary_list->runQuery( 'SELECT category.* FROM word_categories LEFT JOIN category ON category.id = word_categories.cat_id WHERE word_id =:word_id' );
									$stmt_cat->execute( array( ':word_id' => $row['id'] ) );
									$category_ids = [];
									$category_strings = "";
									while ( $row_cat = $stmt_cat->fetch( PDO::FETCH_ASSOC ) ) {
										array_push($category_ids, $row_cat["id"]);
										if ($category_strings!=="") {
											$category_strings .= ", ";
										}

										$category_strings .= $category_list->category_full_path_string( $row_cat['id'] );
									}


									?>
									<tr>
										<td><?php echo $row['id']; ?></td>
										<td><span class="edit-word-btn edit-word-en-btn" title="edit english word"
										          data-word_en="<?php echo $row['word_EN']; ?>" data-multi_category="<?php echo implode(",",$category_ids); ?>"
										          data-word_id="<?php echo $row['id']; ?>"
										          data-picture="<?php echo $row['picture']; ?>"
										          data-level="<?php echo $row['level']; ?>" data-audio_en="<?php echo $row['audio_EN']; ?>"><?php echo $row['word_EN'];
												if ( $row['word_EN'] === "" || $row['word_EN'] === null ) {
													echo ' <i class="fa fa-edit"></i> ';
												} ?></span></td>

										<td><span class="edit-word-btn edit-word-tr-btn" title="edit turkish word"
										          data-word_tr="<?php echo $row['word_TR']; ?>" data-word_id="<?php echo $row['id']; ?>"
										          data-audio_tr="<?php echo $row['audio_TR']; ?>"><?php echo $row['word_TR'];
												if ( $row['word_TR'] === "" || $row['word_TR'] === null ) {
													echo ' <i class="fa fa-edit"></i> ';
												} ?></span></td>

										<td><span class="edit-word-btn edit-word-ch-btn" title="edit chinese word"
										          data-word_ch="<?php echo $row['word_CH']; ?>" data-word_id="<?php echo $row['id']; ?>" data-bopomofo="<?php echo $row['bopomofo']; ?>"
										          data-audio_ch="<?php echo $row['audio_CH']; ?>"><?php echo $row['word_CH'];
												if ( $row['word_CH'] === "" || $row['word_CH'] === null ) {
													echo ' <i class="fa fa-edit"></i> ';
												} ?></span></td>

										<td><?php
											//echo $category_list->category_full_path_string( $row['categoryID'] );
											echo $category_strings;
											?></td>
										<td><?php echo $row['level']; ?></td>
										<td><?php echo $dateadded; ?></td>
									</tr>
								<?php } ?>
								</tbody>
								<tfoot>
								<tr>
									<th>Word</th>
									<th>Category</th>
									<th>Level</th>
									<th>Date</th>
								</tr>
								</tfoot>
							</table>
						</div>
						<!-- /.box-body -->

						<?php include "edit_word_modals.php"; ?>

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

<script src="dictionary.js"></script>
</body>
</html>
<?php
require_once( "../config/session.php" );
require_once( "../config/class.user.php" );

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
</head>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">
	<?php include( $_SERVER["DOCUMENT_ROOT"] . WEB_ROOT . '/header.php' ); ?>
	<?php include( $_SERVER["DOCUMENT_ROOT"] .  WEB_ROOT .'/sidebar.php' ); ?>
	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				List of Categories
				<small>Dictionary &nbsp;&nbsp;
					<button type="button" class="btn btn-primary btn-flat btn-sm" id="add_word_btn"><i class="fa fa-plus-circle"></i> Add Category
					</button>
				</small>
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
									<th>Category</th>
									<th>Action</th>
								</tr>
								</thead>
								<tbody>
								<?php

								$cat_array = $category_list->all_categories( 0 );
								function loopArray2( $arr, $parent, $parent_id, $parent_en ) {
									for ( $i = 0; $i < count( $arr ); $i ++ ) {

										?>
										<tr>
											<td><?php echo $arr[ $i ]['id']; ?></td>
											<td><?php
												echo $parent;
												if ( $parent !== "" ) {
													echo " / ";
												}
												echo $arr[ $i ]['name']; ?></td>
											<td>
												<button type="button" class="btn btn-primary btn-flat edit-word-btn" title="edit record" class="edit_row"
												        data-category_id="<?php echo $arr[ $i ]['id']; ?>" data-category_en="<?php echo $arr[ $i ]['name']; ?>"
												        data-parent_id="<?php echo $parent_id; ?>" data-parent_en="<?php echo $parent_en; ?>"/>
												<i class="fa fa-edit"></i> Edit</button>
												&nbsp;
												<button type="button" class="btn btn-danger btn-flat" onClick="window.location.href='javascript:deleteuser(<?php echo $arr[ $i ]['id']; ?>);'"><i
														class="fa fa-trash"></i> Delete
												</button>
											</td>
										</tr>
										<?php

										if ( count( $arr[ $i ]["children"] ) > 0 ) {
											if ( $parent === "" ) {
												loopArray2( $arr[ $i ]["children"], $arr[ $i ]["name"], $arr[ $i ]["id"], $arr[ $i ]["name"] );
											} else {
												loopArray2( $arr[ $i ]["children"], $parent . " / " . $arr[ $i ]["name"], $arr[ $i ]["id"], $arr[ $i ]["name"] );
											}
										} else {
//											echo "<option value='".$arr[$i]["id"]."'>".$parent. " / ".$arr[$i]["name"]."</option>";
										}
									}
								}

								loopArray2( $cat_array, "", 0,"" );
								?>

								</tbody>
								<tfoot>
								<tr>
									<th>Category</th>
									<th>Action</th>
								</tr>
								</tfoot>
							</table>
						</div>
						<!-- /.box-body -->

						<!-- modal -->
						<div class="modal fade" id="edit_word_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
							<div class="modal-dialog modal-md" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title" id="gridSystemModalLabel"><i class="ion ion-compose"></i> <span id="word_modal_title">Edit Word</span></h4>
									</div>
									<div class="modal-body">
										<form role="form" method="POST" id="upload-image-form" enctype="multipart/form-data">
											<input type="hidden" id="category_id" name="category_id" value="">
											<input type="hidden" id="parent_en" name="parent_en" value="">
											<div class="box-body">
												<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
													<label>Name</label>
													<div class="clearfix"></div>
													<input style="width: 350px;" required data-minlength="6" type="text" class="form-control" name="category_EN" id="category_EN"
													       placeholder="English Name"
													       value="">
												</div>

                        <div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
                          <label for="sel1">Category:</label>
                          <select class="form-control" required id="parent_id" name="parent_id" style="width:400px;">
                            <?php
                            $cat_array = $category_list->all_categories( 0 );
                            function loopArray( $arr, $parent ) {
                              for ( $i = 0; $i < count( $arr ); $i ++ ) {
                                if ( count( $arr[ $i ]["children"] ) > 0 ) {
                                  if ( $parent === "" ) {
                                    echo "<option value='" . $arr[ $i ]["id"] . "'>" . $arr[ $i ]["name"] . "</option>";
                                    loopArray( $arr[ $i ]["children"], $arr[ $i ]["name"] );
                                  } else {
                                    echo "<option value='" . $arr[ $i ]["id"] . "'>" . $parent . " / " . $arr[ $i ]["name"] . "</option>";
                                    loopArray( $arr[ $i ]["children"], $parent . " / " . $arr[ $i ]["name"] );
                                  }
                                } else {
                                  echo "<option value='" . $arr[ $i ]["id"] . "'>" . $parent . " / " . $arr[ $i ]["name"] . "</option>";
                                }
                              }
                            }

                            loopArray( $cat_array, "" );
                            ?>
                          </select>
                        </div>

<!--												<div style="margin-top:8px;margin-right:10px;" class="form-group">-->
<!--													<label>Parent ID</label>-->
<!--													<div class="clearfix"></div>-->
<!--													<input style="width: 150px;" required type="text" class="form-control" name="parent_id" id="parent_id" placeholder="parent_id"-->
<!--													       value="">-->
<!--												</div>-->
											</div>

											<div id="message"></div>

											<div class="box-body">
												<button type="submit" name="btn-update" id="upload-button" class="btn btn-primary btn-flat btn-sm">Save Changes</button>
											</div>
											<!-- /.box-body -->
										</form>
									</div>
								</div>
							</div>
						</div>
						<!-- /end modal -->


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
<!-- page script -->
<script>
  function deleteuser(userID) {
    if (confirm('Delete this user?')) {
      window.location.href = 'index.php?userID=' + userID;
    }
  }
</script>
<script>
  $(document).ready(function () {

    $('#example1').DataTable({
      "drawCallback": function (settings) {
        $(".edit-word-btn").off('click').on('click', function () {
          $("#category_EN").val($(this).data("category_en"));
          $("#parent_id").val($(this).data("parent_id"));
          $("#category_id").val($(this).data("category_id"));
          $("#parent_en").val($(this).data("parent_en"));


          $("#word_modal_title").html("Edit Category");
          $("#edit_word_modal").modal("show");
        });
      },
      "paging": true,
      "lengthChange": false,
      "pageLength": 250,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": true
    });

    $("#add_word_btn").on('click', function () {
      $("#category_EN").val("");
      $("#parent_id").val("0");
      $("#parent_en").val("");
      $("#category_id").val("0");

	    $("#word_modal_title").html("Add Category");
      $("#edit_word_modal").modal("show");
    });


    setTimeout(function () {
      $(".alert").fadeOut("slow", function () {
        $(".alert").remove();
      });
    }, 5000);


    /*jslint browser: true, white: true, eqeq: true, plusplus: true, sloppy: true, vars: true*/

    /*global $, console, alert, FormData, FileReader*/


    $('#upload-image-form').on('submit', function (e) {

      e.preventDefault();

      $('#message').empty();
      $('#loading').show();

      $.ajax({
        url: "../save_category.php",
        type: "POST",
        data: new FormData(this),
        contentType: false,
        cache: false,
        processData: false,
        success: function (data) {
          $('#loading').hide();
          $('#message').html(data);
        }
      });

    });
  });
</script>
</body>
</html>
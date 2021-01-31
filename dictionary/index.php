<?php
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
	<style>
		.edit-word-btn, .audio_play_link {
			cursor: pointer;
			color: blue;
		}

		.edit-word-btn:hover, .audio_play_link:hover {
			text-decoration: underline;
		}
	</style>
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
          <select class="form-control" required id="category_filter_id" name="category_filter_id" style="width:400px; display: inline-block !important;">
            <?php
            $cat_array = $category_list->all_categories( 0 );
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
									<th>Word</th>
									<th>Category</th>
									<th>Level</th>
									<th>Date</th>
								</tr>
								</thead>
								<tbody>
								<?php
								$x = 0;
								if ( array_key_exists( "catid", $_GET ) && is_numeric( $_GET["catid"] ) && $_GET["catid"]!=="0" ) {
									$stmt = $dictionary_list->runQuery( "SELECT * FROM category WHERE parentID=:val " );
									$stmt->execute( array( ':val' => $_GET["catid"] ) );
									$stmt->execute();
									$category_id_array = [$_GET["catid"]];
									while ( $p_category = $stmt->fetch() ) {
										array_push($category_id_array,$p_category["id"]);
									}
									$cates = "(" . implode( ",", $category_id_array ) . ")";

									$stmt = $dictionary_list->runQuery( 'SELECT * FROM dictionary WHERE deleted=:val AND categoryID IN '.$cates );
									$stmt->execute( array( ':val' => 0 ) );
								} else {
									$stmt = $dictionary_list->runQuery( 'SELECT * FROM dictionary WHERE deleted=:val' );
									$stmt->execute( array( ':val' => 0 ) );
								}
								while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
									$x ++;
									$dateAdded = new DateTime( $row['dateadded'] );
									$dateadded = $dateAdded->format( 'M. d, Y h:m.s' );

									?>
									<tr>
										<td><?php echo $row['id']; ?></td>
										<td><span class="edit-word-btn" title="edit record"
										          data-word_en="<?php echo $row['word_EN']; ?>" data-word_tr="<?php echo $row['word_TR']; ?>" data-word_ch="<?php echo $row['word_CH']; ?>"
										          data-category_id="<?php echo $row['categoryID']; ?>" data-word_id="<?php echo $row['id']; ?>" data-picture="<?php echo $row['picture']; ?>"
										          data-level="<?php echo $row['level']; ?>" data-bopomofo="<?php echo $row['bopomofo']; ?>" data-audio_en="<?php echo $row['audio_EN']; ?>"
										          data-audio_ch="<?php echo $row['audio_CH']; ?>" data-audio_tr="<?php echo $row['audio_TR']; ?>"><?php echo $row['word_EN']; ?></span></td>
										<td><?php echo $category_list->category_full_path_string( $row['categoryID'] ); ?></td>
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
											<input type="hidden" id="word_id" name="word_id" value="">
											<input type="hidden" id="picture" name="picture" value="">
											<input type="hidden" id="audio_EN" name="audio_EN" value="">
											<input type="hidden" id="audio_TR" name="audio_TR" value="">
											<input type="hidden" id="audio_CH" name="audio_CH" value="">
											<div class="box-body">
												<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
													<label>English</label>
													<div class="clearfix"></div>
													<input style="width: 150px;" required data-minlength="6" type="text" class="form-control" name="word_EN" id="word_EN" placeholder="English"
													       value="">
												</div>
												<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
													<label>Turkish</label>
													<div class="clearfix"></div>
													<input style="width: 150px;" data-minlength="6" type="text" class="form-control" name="word_TR" id="word_TR" placeholder="Turkish"
													       value="">
												</div>
												<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
													<label>Chinese</label>
													<div class="clearfix"></div>
													<input style="width: 150px;" data-minlength="6" type="text" class="form-control" name="word_CH" id="word_CH" placeholder="Chinese"
													       value="">
												</div>

												<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
													<label>Chinese BoPoMoFo</label>
													<div class="clearfix"></div>
													<input style="width: 150px;" data-minlength="6" type="text" class="form-control" name="bopomofo" id="bopomofo" placeholder="bopomofo"
													       value="">
												</div>

												<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
													<label for="sel1">Category:</label>
													<select class="form-control" required id="category_id" name="category_id" style="width:400px;">
														<?php
														$cat_array = $category_list->all_categories( 0 );
														function loopArray( $arr, $parent ) {
															for ( $i = 0; $i < count( $arr ); $i ++ ) {
																if ( count( $arr[ $i ]["children"] ) > 0 ) {
																	if ( $parent === "" ) {
																		loopArray( $arr[ $i ]["children"], $arr[ $i ]["name"] );
																	} else {
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
												<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
													<label>Level</label>
													<div class="clearfix"></div>
													<select class="form-control" required id="level" name="level" style="width:60px;">
														<option value="1">1</option>
														<option value="2">2</option>
														<option value="3">3</option>
														<option value="4">4</option>
														<option value="5">5</option>
														<option value="6">6</option>
														<option value="7">7</option>
														<option value="8">8</option>
														<option value="9">9</option>
														<option value="10">10</option>
													</select>
												</div>


												<div id="image-preview-div" style="display: none">
													<label for="exampleInputFile">Selected image (<span id="image_file_name"></span>):</label>
													<br>
													<img id="preview-img" src="noimage">
												</div>
												<div class="form-group">
													<input type="file" name="file" id="file">
												</div>


												<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
													<label for="exampleInputFile">English Audio (<span id="en_audio_file"></span>):</label> <span id="play_en_audio"
													                                                                                              class="audio_play_link">Play</span>
													<input type="file" name="file_audio_en" id="file_audio_en" class="form-control">
												</div>

												<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
													<label for="exampleInputFile">Turkish Audio (<span id="tr_audio_file"></span>):</label> <span id="play_tr_audio"
													                                                                                              class="audio_play_link">Play</span>
													<input type="file" name="file_audio_tr" id="file_audio_tr" class="form-control">
												</div>

												<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
													<label for="exampleInputFile">Chinese Audi (<span id="ch_audio_file"></span>)o:</label> <span id="play_ch_audio"
													                                                                                              class="audio_play_link">Play</span>
													<input type="file" name="file_audio_ch" id="file_audio_ch" class="form-control">
												</div>

												<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
													<label>Audio Speed</label>
													<div class="clearfix"></div>
													<select class="form-control" required id="audio_speed" name="audio_speed" style="width:90px;">
														<option value="1">80%</option>
														<option value="2" selected>100%</option>
														<option value="3">120%</option>
													</select>
												</div>


												<audio
													id="en_audio_player"
													src=""></audio>

												<audio
													id="tr_audio_player"
													src=""></audio>

												<audio
													id="ch_audio_player"
													src=""></audio>


												<div class="alert alert-info" id="loading" style="display: none;" role="alert">
													<span id="loading_msg">Uploading image...</span>
													<div class="progress">
														<div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"
														     style="width: 100%">
														</div>
													</div>
												</div>
												<div id="message"></div>

											</div>

											<div class="box-body">
												<button type="submit" name="btn-update" id="upload-button" class="btn btn-primary btn-flat btn-sm">Save Changes</button>

												<div id="btn-regen-audio" class="btn btn-primary btn-flat btn-sm">Regenerate Audio</div>

												<div id="btn-trans-en" class="btn btn-primary btn-flat btn-sm">Trans From En</div>
												<div id="btn-trans-tr" class="btn btn-primary btn-flat btn-sm">Trans From Tr</div>
												<div id="btn-trans-ch" class="btn btn-primary btn-flat btn-sm">Trans From Ch</div>
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

  function togglePlay(audiodiv) {
    var myAudio = document.getElementById(audiodiv);
    return myAudio.paused ? myAudio.play() : myAudio.pause();
  }

  $(document).ready(function () {

    $("#category_filter_id").on('change',function () {
      window.location.href = "/picture-dictionary-editor/dictionary/?catid=" + $(this).val();
    });

    $('#example1').DataTable({
      // "ajax": "words.php",
      // "columns": [
      //   { "data": "name" },
      //   { "data": "position" },
      //   { "data": "office" },
      //   { "data": "extn" },
      //   { "data": "start_date" },
      //   { "data": "salary" }
      // ],
      "drawCallback": function (settings) {

        $("#play_en_audio").off('click').on('click', function () {
          togglePlay("en_audio_player");
        });

        $("#play_tr_audio").off('click').on('click', function () {
          togglePlay("tr_audio_player");
        });

        $("#play_ch_audio").off('click').on('click', function () {
          togglePlay("ch_audio_player");
        });

        $(".edit-word-btn").off('click').on('click', function () {
          $("#word_EN").val($(this).data("word_en"));
          $("#word_TR").val($(this).data("word_tr"));
          $("#word_CH").val($(this).data("word_ch"));
          $("#category_id").val($(this).data("category_id"));
          $("#picture").val($(this).data("picture"));
          $("#word_id").val($(this).data("word_id"));
          $("#level").val($(this).data("level"));

          $("#image_file_name").html($(this).data("picture"));

          $("#en_audio_file").html($(this).data("audio_en"))
          $("#tr_audio_file").html($(this).data("audio_tr"))
          $("#ch_audio_file").html($(this).data("audio_ch"))

          $("#en_audio_player").attr({"src": "../audio/en/" + $(this).data("audio_en")+"?cb=" + new Date().getTime()});
          $("#tr_audio_player").attr({"src": "../audio/tr/" + $(this).data("audio_tr")+"?cb=" + new Date().getTime()});
          $("#ch_audio_player").attr({"src": "../audio/ch/" + $(this).data("audio_ch")+"?cb=" + new Date().getTime()});

          $("#bopomofo").val($(this).data("bopomofo"));
          $("#audio_EN").val($(this).data("audio_en"));
          $("#audio_CH").val($(this).data("audio_ch"));
          $("#audio_TR").val($(this).data("audio_tr"));

          $('#file').css("color", "green");
          $('#image-preview-div').css("display", "block");
          $('#preview-img').attr('src', "../pictures/" + $(this).data("picture"));
          $('#preview-img').css('max-width', '150px');


          $("#word_modal_title").html("Edit Word");
          $("#edit_word_modal").modal("show");
        });
      },
      "paging": true,
      "lengthChange": false,
      "pageLength": 50,
      "searching": true,
      "ordering": true,
      "info": true,
      "autoWidth": true
    });

    $("#add_word_btn").on('click', function () {
      $("#word_EN").val("");
      $("#word_TR").val("");
      $("#word_CH").val("");
      $("#category_id").val(0);
      $("#picture").val("");
      $("#word_id").val("0");
      $("#level").val("1");

      $("#bopomofo").val("");
      $("#audio_EN").val("");
      $("#audio_CH").val("");
      $("#audio_TR").val("");

      $("#image_file_name").html("daha-1.jpg");


      $('#file').css("color", "green");
      $('#image-preview-div').css("display", "block");
      $('#preview-img').attr('src', "../pictures/daha-1.jpg");
      $('#preview-img').css('max-width', '150px');


      $("#word_modal_title").html("Add Word");
      $("#edit_word_modal").modal("show");
    });


    // setTimeout(function () {
    //   $(".alert").fadeOut("slow", function () {
    //     $(".alert").remove();
    //   });
    // }, 5000);


    /*jslint browser: true, white: true, eqeq: true, plusplus: true, sloppy: true, vars: true*/

    /*global $, console, alert, FormData, FileReader*/


    function selectImage(e) {
      $('#file').css("color", "green");
      $('#image-preview-div').css("display", "block");
      $('#preview-img').attr('src', e.target.result);
      $('#preview-img').css('max-width', '150px');
    }


    var maxsize = 500 * 1024; // 500 KB

    $('#max-size').html((maxsize / 1024).toFixed(2));

    $("#btn-trans-en").off('click').on('click', function () {
      $('#message').empty();
      $('#loading').show();
      $("#loading_msg").html("auto translate...");

      $.ajax({
        url: "../auto-trans.php",
        type: "POST",
        data: {
          trans_source: "en",
          word_EN: $("#word_EN").val(),
          word_id: $("#word_id").val()
        },
        dataType: "JSON",
        success: function (data) {
          $('#loading').hide();
          if (data["result"]) {
            $("#word_TR").val(data["word_TR"]);
            $("#word_CH").val(data["word_CH"]);
            $("#bopomofo").val(data["bopomofo"]);

            $('#message').html("Translations successful.");
          }
          else {
            $('#message').html("Translations failed.");
          }
        }
      });

    });


    $("#btn-trans-tr").off('click').on('click', function () {
      $('#message').empty();
      $('#loading').show();
      $("#loading_msg").html("auto translate...");

      $.ajax({
        url: "../auto-trans.php",
        type: "POST",
        data: {
          trans_source: "tr",
          word_TR: $("#word_TR").val(),
          word_id: $("#word_id").val()
        },
        dataType: "JSON",
        success: function (data) {
          $('#loading').hide();
          if (data["result"]) {
            $("#word_EN").val(data["word_EN"]);
            $("#word_CH").val(data["word_CH"]);
            $("#bopomofo").val(data["bopomofo"]);

            $('#message').html("Translations successful.");
          }
          else {
            $('#message').html("Translations failed.");
          }
        }
      });

    });

    $("#btn-trans-ch").off('click').on('click', function () {
      $('#message').empty();
      $('#loading').show();
      $("#loading_msg").html("auto translate...");

      $.ajax({
        url: "../auto-trans.php",
        type: "POST",
        data: {
          trans_source: "ch",
          word_CH: $("#word_CH").val(),
          word_id: $("#word_id").val()
        },
        dataType: "JSON",
        success: function (data) {
          $('#loading').hide();
          if (data["result"]) {
            $("#word_TR").val(data["word_TR"]);
            $("#word_EN").val(data["word_EN"]);

            $('#message').html("Translations successful.");
          }
          else {
            $('#message').html("Translations failed.");
          }
        }
      });

    });


    $("#btn-regen-audio").off('click').on('click', function () {

      $('#message').empty();
      $('#loading').show();
      $("#loading_msg").html("generating audio...");

      $.ajax({
        url: "../regen-audio.php",
        type: "POST",
        data: {
	        audio_speed: $("#audio_speed").val(),
          word_EN: $("#word_EN").val(),
          word_TR: $("#word_TR").val(),
          word_CH: $("#word_CH").val(),
          word_id: $("#word_id").val()
        },
        success: function (data) {
          $('#loading').hide();
          $('#message').html(data);
        }
      });
    });

    $('#upload-image-form').on('submit', function (e) {

      e.preventDefault();

      $('#message').empty();
      $('#loading').show();
      $("#loading_msg").html("uploading image...");

      $.ajax({
        url: "../upload-image.php",
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

    $('#file').change(function () {

      $('#message').empty();

      var file = this.files[0];
      var match = ["image/jpeg", "image/png", "image/jpg"];

      if (!((file.type == match[0]) || (file.type == match[1]) || (file.type == match[2]))) {
        $('#message').html('<div class="alert alert-warning" role="alert">Invalid image format. Allowed formats: JPG, JPEG, PNG.</div>');

        return false;
      }

      if (file.size > maxsize) {
        $('#message').html('<div class=\"alert alert-danger\" role=\"alert\">The size of image you are attempting to upload is ' + (file.size / 1024).toFixed(2) + ' KB, maximum size allowed is ' + (maxsize / 1024).toFixed(2) + ' KB</div>');

        return false;
      }

      // $('#upload-button').removeAttr("disabled");

      var reader = new FileReader();
      reader.onload = selectImage;
      reader.readAsDataURL(this.files[0]);


    });
  });
</script>
</body>
</html>
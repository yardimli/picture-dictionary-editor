<!-- modal -->
<div class="modal fade" id="edit_story_answer_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="ion ion-compose"></i> <span id="story_answer_modal_title">Edit Story</span></h4>
			</div>
			<div class="modal-body" style="margin-top: 0px; padding-top: 0px;">
				<form role="form" method="POST" id="upload-image-form" enctype="multipart/form-data">
					<input type="hidden" id="answer_id" name="answer_id" value="">
					<input type="hidden" id="story_id" name="story_id" value="">
					<input type="hidden" id="language" name="language" value="">
					<input type="hidden" id="audio" name="audio" value="">
					<div class="box-body" style="margin-top: 0px; padding-top: 0px;">
						<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
							<label>Story</label>
							<div class="clearfix"></div>

							<select class="form-control" required id="question_id" name="question_id" style="width:400px; display: inline-block !important;">
								echo "<option value=''>Select Story/Question</option>";
								<?php
								$stmt = $story_list->runQuery( 'SELECT story_question.*, story.title as title FROM story_question LEFT join story ON story.id=story_question.story_id WHERE story_question.deleted=:val' );
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

						<br>
						<div id="story_text" style="margin-top:5px;margin-right:10px; margin-bottom: 0px; background-color: #eee; padding:10px;" class="form-group">...</div>
						<div id="story_question_text" style="margin-top:1px;margin-right:10px; margin-bottom: 10px; background-color: #ddd; padding:10px;" class="form-group">...</div>

						<div style="margin-top:0px;margin-right:10px; margin-bottom: 0px;" class="form-group">
							<label>Answer</label>
							<div class="clearfix"></div>
							<input style="" required data-minlength="6" type="text" class="form-control" name="answer" id="answer" placeholder="Answer"
							       value="">
						</div>

						<div style="margin-top:10px;margin-right:10px; margin-bottom: 0px;" class="form-group">
							<label>Is Correct?</label>
							<input required type="checkbox" class="" style="margin-left:10px;  vertical-align: top;" name="is_correct" id="is_correct">
						</div>

						<br>
						<button id="update-text-fields-button" class="btn btn-primary btn-flat btn-sm" style="margin-right:20px;">Save Changes</button>
						<button data-question_id="0" class="refresh_page_btn btn btn-primary btn-flat btn-sm" style="margin-right:20px; display: none;">Close and Refresh List</button>

						<hr>

						<div id="story_answer_media_edit">
							<div id="image-preview-div" style="display: none;">
								<label for="exampleInputFile">Selected image (<span id="image_file_name"></span>):</label>
								<br>
								<img id="preview-img" src="../images/noimage.png">
							</div>

							<div class="form-group" style=" vertical-align: top;">
								<input type="file" name="image-file" id="image-file" class="form-control" style="display: inline-block; width: 250px;">
								<button id="upload-image-button" class="btn btn-primary btn-flat btn-sm" style="margin-top:0px; margin-right:20px; height: 34px; vertical-align: top;">
									Upload Image
								</button>
								<button data-question_id="0" class="refresh_page_btn btn btn-primary btn-flat btn-sm"
								        style="margin-top:0px; display: none;  height: 34px; vertical-align: top;">Close and Refresh List
								</button>
							</div>

							<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
								<label for="exampleInputFile">Audio (<span id="audio_file"></span>):</label> <span id="play_audio"
								                                                                                   class="audio_play_link">Play</span>
								<br>
								<input type="file" name="file_audio" id="file_audio" class="form-control" style="display: inline-block; width: 200px;">
								<button id="upload-audio-button" class="btn btn-primary btn-flat btn-sm"
								        style="margin-top:0px; vertical-align: top; height: 34px; margin-right:20px; ">Upload Audio</button>
								<button data-question_id="0" class="refresh_page_btn btn btn-primary btn-flat btn-sm" style="margin-top:0px; vertical-align: top; height: 34px; display: none;">
									Close and Refresh List
								</button>
							</div>

							<div style="margin-top:0px; vertical-align: top;" class="form-group">
								<label>Generated Audio Speed: </label>
								<select class="form-control" required id="audio_speed" name="audio_speed" style="width:90px; display: inline-block !important;">
									<option value="1">80%</option>
									<option value="2" selected>100%</option>
									<option value="3">120%</option>
								</select>
								<br>
								<button id="regen-audio-button" class="btn btn-danger btn-flat btn-sm" style="margin-right:20px; height: 34px; vertical-align: top;">Generate Audio</button>
								<button data-question_id="0" class="refresh_page_btn btn btn-primary btn-flat btn-sm" style=" display: none; height: 34px; vertical-align: top;">Close and
									Refresh List
								</button>
							</div>

							<audio
								id="audio_player"
								src=""></audio>

							<div class="progress-wrp" id="progress-wrp">
								<div class="progress-bar"></div>
								<div class="status">0%</div>
							</div>

							<div class="alert alert-info" id="loading" style="display: none;" role="alert">
								<span id="loading_msg">Uploading image...</span>
							</div>
						</div>
						<div id="message"></div>

					</div>
					<!-- /.box-body -->
				</form>
			</div>
		</div>
	</div>
</div>
<!-- /end modal -->





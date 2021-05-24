<!-- modal -->
<div class="modal fade" id="edit_story_question_modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="ion ion-compose"></i> <span id="story_question_modal_title">Edit Story</span></h4>
			</div>
			<div class="modal-body" style="margin-top: 0px; padding-top: 0px;">
				<form role="form" method="POST" id="upload-image-form" enctype="multipart/form-data">
					<input type="hidden" id="question_id" name="question_id" value="">
					<input type="hidden" id="language" name="language" value="">
					<input type="hidden" id="audio" name="audio" value="">
					<div class="box-body" style="margin-top: 0px; padding-top: 0px;">
						<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
							<label>Story</label>
							<div class="clearfix"></div>

							<?php
							$cat_array = $story_list->all_stories();
							?>
							<select class="form-control" required id="story_id" name="story_id" style="width:400px; display: inline-block !important;">
								echo "<option value=''>Select Story</option>";
								<?php
								for ( $i = 0; $i < count( $cat_array ); $i ++ ) {
									echo "<option value='" . $cat_array[ $i ]["id"] . "'>" . $cat_array[ $i ]["title"] . "</option>";
								}
								?>
							</select>
						</div>

						<br>
						<div id="story_text" style="margin-top:5px;margin-right:10px; margin-bottom: 10px; background-color: #eee; padding:10px;" class="form-group">...</div>

						<div style="margin-top:0px;margin-right:10px; margin-bottom: 0px;" class="form-group">
							<label>Question</label>
							<div class="clearfix"></div>
							<input style="" required data-minlength="6" type="text" class="form-control" name="question" id="question" placeholder="Question"
							       value="">
						</div>

						<div style="margin-top:10px;margin-right:10px; margin-bottom: 0px;" class="form-group">
							<label>Show pictures in answers:</label>
							<input required type="checkbox" class="" style="margin-left:10px;  vertical-align: top;" name="show_answer_pictures" id="show_answer_pictures">
						</div>

						<div style="margin-top:10px;margin-right:10px; margin-bottom: 0px;" class="form-group">
							<label>Get random answers from other questions:</label>
							<input required type="checkbox" class="" style="margin-left:10px;  vertical-align: top;" name="random_answers_from_other_questions" id="random_answers_from_other_questions">
						</div>

						<br>
						<button id="update-text-fields-button" class="btn btn-primary btn-flat btn-sm" style="margin-right:20px;">Save Changes</button>
						<button data-catid="0" class="refresh_page_btn btn btn-primary btn-flat btn-sm" style="margin-right:20px; display: none;">Close and Refresh List</button>

						<hr>

						<div id="story_question_media_edit">

							<div style="margin-top:8px;margin-right:10px;" class="form-group">
								<label for="exampleInputFile">Audio (<span id="audio_file"></span>):</label> <span id="play_audio"
								                                                                                              class="audio_play_link">Play</span>
								<br>
								<input type="file" name="file_audio" id="file_audio" class="form-control" style="display: inline-block; width: 200px;">
								<button id="upload-audio-button" class="btn btn-primary btn-flat btn-sm"
								        style="margin-top:0px; vertical-align: top; height: 34px; margin-right:20px; ">Upload Audio</button>
								<button data-catid="0" class="refresh_page_btn btn btn-primary btn-flat btn-sm" style="margin-top:0px; vertical-align: top; height: 34px; display: none;">
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
								<button data-catid="0" class="refresh_page_btn btn btn-primary btn-flat btn-sm" style=" display: none; height: 34px; vertical-align: top;">Close and
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





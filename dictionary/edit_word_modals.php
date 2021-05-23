<!-- modal -->
<div class="modal fade" id="edit_word_modal_en" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="ion ion-compose"></i> <span id="word_modal_title_en">Edit Word</span></h4>
			</div>
			<div class="modal-body">
				<form role="form" method="POST" id="upload-image-form-en" enctype="multipart/form-data">
					<input type="hidden" id="language_en" name="language_en" value="en">
					<input type="hidden" id="word_id_en" name="word_id_en" value="">
					<input type="hidden" id="picture" name="picture" value="">
					<input type="hidden" id="audio_EN" name="audio_EN" value="">
					<div class="box-body">
						<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
							<label>English</label>
							<div class="clearfix"></div>
							<input style="width: 150px;" required data-minlength="6" type="text" class="form-control" name="word_EN" id="word_EN" placeholder="English"
							       value="">
						</div>

						<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
							<label for="sel1">Category:</label>
							<div class="clearfix"></div>
							<select class="form-control selectpicker" multiple data-max-options="5" required id="multi_category" name="multi_category"
							        style="width:400px; max-width: 400px;">
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

						<br>
						<button id="update-text-fields-button-en" class="btn btn-primary btn-flat btn-sm" style="margin-right:20px;">Save Changes</button>
						<button data-catid="0" class="refresh_page_btn btn btn-primary btn-flat btn-sm" style="margin-right:20px; display: none;">Close and Refresh List</button>

						<hr>

						<div id="en_word_media_edit">

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
								<button data-catid="0" class="refresh_page_btn btn btn-primary btn-flat btn-sm"
								        style="margin-top:0px; display: none;  height: 34px; vertical-align: top;">Close and Refresh List
								</button>
							</div>


							<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
								<label for="exampleInputFile">English Audio (<span id="en_audio_file"></span>):</label> <span id="play_en_audio"
								                                                                                              class="audio_play_link">Play</span>
								<br>
								<input type="file" name="file_audio_en" id="file_audio_en" class="form-control" style="display: inline-block; width: 250px;">
								<button id="upload-audio-en-button" class="btn btn-primary btn-flat btn-sm"
								        style="margin-top:0px; vertical-align: top; height: 34px; margin-right:20px; ">Upload Audio (en)
								</button>
								<button data-catid="0" class="refresh_page_btn btn btn-primary btn-flat btn-sm" style="margin-top:0px; vertical-align: top; height: 34px; display: none;">
									Close and Refresh List
								</button>
							</div>

							<div style="margin-top:8px; vertical-align: top;" class="form-group">
								<label>Generated Audio Speed: </label>
								<select class="form-control" required id="audio_speed_en" name="audio_speed_en" style="width:90px; display: inline-block !important;">
									<option value="1">80%</option>
									<option value="2" selected>100%</option>
									<option value="3">120%</option>
								</select>
								<button id="regen-audio-en-button" class="btn btn-danger btn-flat btn-sm" style="margin-right:20px; height: 34px; vertical-align: top;">Generate Audio
									(en)
								</button>
								<button data-catid="0" class="refresh_page_btn btn btn-primary btn-flat btn-sm" style=" display: none; height: 34px; vertical-align: top;">Close and
									Refresh List
								</button>
							</div>

							<audio
								id="en_audio_player"
								src=""></audio>

							<div class="progress-wrp" id="progress-wrp-en">
								<div class="progress-bar"></div>
								<div class="status">0%</div>
							</div>

							<div class="alert alert-info" id="loading_en" style="display: none;" role="alert">
								<span id="loading_msg_en">Uploading image...</span>
							</div>
						</div>
						<div id="message_en"></div>

					</div>
					<!-- /.box-body -->
				</form>
			</div>
		</div>
	</div>
</div>
<!-- /end modal -->


<!-- modal -->
<div class="modal fade" id="edit_word_modal_tr" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="ion ion-compose"></i> <span id="word_modal_title_tr">Edit Word</span></h4>
			</div>
			<div class="modal-body">
				<form role="form" method="POST" id="upload-image-form-tr" enctype="multipart/form-data">
					<input type="hidden" id="language_tr" name="language_tr" value="tr">
					<input type="hidden" id="word_id_tr" name="word_id_tr" value="">
					<input type="hidden" id="audio_TR" name="audio_TR" value="">
					<div class="box-body">
						<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
							<label>Turkish</label>
							<div class="clearfix"></div>
							<input style="width: 300px; display: inline-block;" data-minlength="6" type="text" class="form-control" name="word_TR" id="word_TR" placeholder="Turkish"
							       value="">

							<button id="update-text-fields-button-tr" class="btn btn-primary btn-flat btn-sm" style="margin-right:20px; vertical-align: top; height: 34px;">Save
								Changes
							</button>
							<button data-catid="0" class="refresh_page_btn btn btn-primary btn-flat btn-sm"
							        style="margin-right:20px;  vertical-align: top; height: 34px; display: none;">Close and Refresh List
							</button>
						</div>

						<hr>


						<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
							<label for="exampleInputFile">Turkish Audio (<span id="tr_audio_file"></span>):</label> <span id="play_tr_audio"
							                                                                                              class="audio_play_link">Play</span>
							<input type="file" name="file_audio_tr" id="file_audio_tr" class="form-control">
							<button id="upload-audio-tr-button" class="btn btn-primary btn-flat btn-sm" style="margin-top:4px; margin-right:20px;">Upload Audio (tr)</button>
							<button data-catid="0" class="refresh_page_btn btn btn-primary btn-flat btn-sm" style=" margin-top:4px; display: none;">Close and Refresh List</button>
						</div>

						<div style="margin-top:8px; vertical-align: top;" class="form-group">
							<label>Generated Audio Speed: </label>
							<select class="form-control" required id="audio_speed_tr" name="audio_speed_tr" style="width:90px; display: inline-block !important;">
								<option value="1">80%</option>
								<option value="2" selected>100%</option>
								<option value="3">120%</option>
							</select>
							<button id="regen-audio-tr-button" class="btn btn-danger btn-flat btn-sm" style="margin-top:0px; margin-right:20px;  vertical-align: top; height: 34px;">
								Generate Audio (tr)
							</button>
							<button data-catid="0" class="refresh_page_btn btn btn-primary btn-flat btn-sm" style="margin-top:0px; display: none;  vertical-align: top; height: 34px;">
								Close and Refresh List
							</button>
						</div>

						<audio
							id="tr_audio_player"
							src=""></audio>

						<div class="progress-wrp" id="progress-wrp-tr">
							<div class="progress-bar"></div>
							<div class="status">0%</div>
						</div>

						<div class="alert alert-info" id="loading_tr" style="display: none;" role="alert">
							<span id="loading_msg_tr">Uploading image...</span>
						</div>
						<div id="message_tr"></div>

					</div>

					<!-- /.box-body -->
				</form>
			</div>
		</div>
	</div>
</div>
<!-- /end modal -->


<!-- modal -->
<div class="modal fade" id="edit_word_modal_ch" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title"><i class="ion ion-compose"></i> <span id="word_modal_title_ch">Edit Word</span></h4>
			</div>
			<div class="modal-body">
				<form role="form" method="POST" id="upload-image-form-ch" enctype="multipart/form-data">
					<input type="hidden" id="language_ch" name="language_ch" value="ch">
					<input type="hidden" id="word_id_ch" name="word_id_ch" value="">
					<input type="hidden" id="audio_CH" name="audio_CH" value="">
					<div class="box-body">
						<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
							<label>Chinese</label>
							<div class="clearfix"></div>
							<input style="width: 200px;" data-minlength="6" type="text" class="form-control" name="word_CH" id="word_CH" placeholder="Chinese"
							       value="">
						</div>

						<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
							<label>Chinese BoPoMoFo</label>
							<div class="clearfix"></div>
							<input style="width: 200px;" data-minlength="6" type="text" class="form-control" name="bopomofo" id="bopomofo" placeholder="bopomofo"
							       value="">
						</div>

						<br>
						<button id="update-text-fields-button-ch" class="btn btn-primary btn-flat btn-sm" style="margin-right:20px;">Save Changes</button>
						<button data-catid="0" class="refresh_page_btn btn btn-primary btn-flat btn-sm" style="margin-right:20px; display: none;">Close and Refresh List</button>

						<hr>


						<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
							<label for="exampleInputFile">Chinese Audi (<span id="ch_audio_file"></span>)o:</label> <span id="play_ch_audio"
							                                                                                              class="audio_play_link">Play</span>
							<input type="file" name="file_audio_ch" id="file_audio_ch" class="form-control">
							<button id="upload-audio-ch-button" class="btn btn-primary btn-flat btn-sm" style="margin-top:4px; margin-right:20px;">Upload Audio (ch)</button>
							<button data-catid="0" class="refresh_page_btn btn btn-primary btn-flat btn-sm" style=" margin-top:4px; display: none;">Close and Refresh List</button>
						</div>

						<div style="margin-top:8px;" class="form-group">
							<label>Generated Audio Speed: </label>
							<select class="form-control" required id="audio_speed_ch" name="audio_speed_ch" style="width:90px; display: inline-block !important;">
								<option value="1">80%</option>
								<option value="2" selected>100%</option>
								<option value="3">120%</option>
							</select>
							<button id="regen-audio-ch-button" class="btn btn-danger btn-flat btn-sm" style="margin-top:0px; margin-right:20px;  vertical-align: top; height: 34px;">Generate Audio (ch)</button>
							<button data-catid="0" class="refresh_page_btn btn btn-primary btn-flat btn-sm" style="margin-top:0px; display: none; vertical-align: top; height: 34px;">Close and Refresh List</button>
						</div>

						<audio
							id="ch_audio_player"
							src=""></audio>


						<div class="progress-wrp" id="progress-wrp-ch">
							<div class="progress-bar"></div>
							<div class="status">0%</div>
						</div>

						<div class="alert alert-info" id="loading_ch" style="display: none;" role="alert">
							<span id="loading_msg_ch">Uploading image...</span>
						</div>
						<div id="message_ch"></div>

					</div>
					<!-- /.box-body -->
				</form>
			</div>
		</div>
	</div>
</div>
<!-- /end modal -->

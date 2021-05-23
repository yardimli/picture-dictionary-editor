<!-- modal -->
<div class="modal fade" id="edit_word_modal_all" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
	<div class="modal-dialog modal-md" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" ><i class="ion ion-compose"></i> <span id="word_modal_title">Edit Word</span></h4>
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
								function loopArray3( $arr, $parent ) {
									for ( $i = 0; $i < count( $arr ); $i ++ ) {
										if ( count( $arr[ $i ]["children"] ) > 0 ) {
											if ( $parent === "" ) {
												loopArray3( $arr[ $i ]["children"], $arr[ $i ]["name"] );
											} else {
												loopArray3( $arr[ $i ]["children"], $parent . " / " . $arr[ $i ]["name"] );
											}
										} else {
											echo "<option value='" . $arr[ $i ]["id"] . "'>" . $parent . " / " . $arr[ $i ]["name"] . "</option>";
										}
									}
								}

								loopArray3( $cat_array, "" );
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
						<button id="update-text-fields-button" class="btn btn-primary btn-flat btn-sm" style="margin-right:20px;">Save Changes</button>

						<div id="btn-trans-en" class="btn btn-danger btn-flat btn-sm">Trans From En</div>
						<div id="btn-trans-tr" class="btn btn-danger btn-flat btn-sm">Trans From Tr</div>
						<div id="btn-trans-ch" class="btn btn-danger btn-flat btn-sm">Trans From Ch</div>

						<hr>


						<div id="image-preview-div" style="display: none;">
							<label for="exampleInputFile">Selected image (<span id="image_file_name"></span>):</label>
							<br>
							<img id="preview-img" src="../images/noimage.png">
						</div>
						<div class="form-group" style="display: inline-block; vertical-align: top;">
							<input type="file" name="image-file" id="image-file">
							<button id="upload-image-button" class="btn btn-primary btn-flat btn-sm" style="margin-top:4px;">Upload Image</button>
						</div>


						<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
							<label for="exampleInputFile">English Audio (<span id="en_audio_file"></span>):</label> <span id="play_en_audio"
							                                                                                              class="audio_play_link">Play</span>
							<input type="file" name="file_audio_en" id="file_audio_en" class="form-control">
							<button id="upload-audio-en-button" class="btn btn-primary btn-flat btn-sm" style="margin-top:4px;">Upload Audio (en)</button>
							<button id="regen-audio-en-button" class="btn btn-danger btn-flat btn-sm" style="margin-top:4px;">Generate Audio (en)</button>
						</div>

						<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
							<label for="exampleInputFile">Turkish Audio (<span id="tr_audio_file"></span>):</label> <span id="play_tr_audio"
							                                                                                              class="audio_play_link">Play</span>
							<input type="file" name="file_audio_tr" id="file_audio_tr" class="form-control">
							<button id="upload-audio-tr-button" class="btn btn-primary btn-flat btn-sm" style="margin-top:4px;">Upload Audio (tr)</button>
							<button id="regen-audio-tr-button" class="btn btn-danger btn-flat btn-sm" style="margin-top:4px;">Generate Audio (tr)</button>
						</div>

						<div style="margin-top:8px;margin-right:10px; display: inline-block !important;" class="form-group">
							<label for="exampleInputFile">Chinese Audi (<span id="ch_audio_file"></span>)o:</label> <span id="play_ch_audio"
							                                                                                              class="audio_play_link">Play</span>
							<input type="file" name="file_audio_ch" id="file_audio_ch" class="form-control">
							<button id="upload-audio-ch-button" class="btn btn-primary btn-flat btn-sm" style="margin-top:4px;">Upload Audio (ch)</button>
							<button id="regen-audio-ch-button" class="btn btn-danger btn-flat btn-sm" style="margin-top:4px;">Generate Audio (ch)</button>
						</div>

						<div style="margin-top:8px;margin-right:10px; display: inline-block !important; vertical-align: top;" class="form-group">
							<label>Generated Audio Speed: </label>
							<select class="form-control" required id="audio_speed" name="audio_speed" style="width:90px; display: inline-block !important;">
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


						<div id="progress-wrp">
							<div class="progress-bar"></div>
							<div class="status">0%</div>
						</div>

						<div class="alert alert-info" id="loading" style="display: none;" role="alert">
							<span id="loading_msg">Uploading image...</span>
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

$(document).ready(function () {

  $(".progress-wrp").hide();

  $("#question_filter_id").on('change', function () {
    window.location.href = "/picture-dictionary-editor/story_answer/?question_id=" + $(this).val();
  });


  var Upload = function (file, answer_id, upload_type) {
    this.file = file;
    this.answer_id = answer_id;
    this.upload_type = upload_type;
  };

  Upload.prototype.getType = function () {
    return this.file.type;
  };
  Upload.prototype.getSize = function () {
    return this.file.size;
  };
  Upload.prototype.getName = function () {
    return this.file.name;
  };

  var progress_bar_id = "#progress-wrp";
  var progress_text =  '#loading';
  var current_upload_button_id = "#upload-image-button";


  Upload.prototype.doUpload = function () {
    var that = this;
    var formData = new FormData();


    // add assoc key values, this will be posts values
    formData.append("file", this.file, this.getName());
    formData.append("upload_file", true);
    formData.append("answer_id", this.answer_id);
    formData.append("upload_type", this.upload_type);

    $.ajax({
      type: "POST",
      url: "../upload-file-story-answer.php",
      xhr: function () {
        var myXhr = $.ajaxSettings.xhr();
        if (myXhr.upload) {
          myXhr.upload.addEventListener('progress', that.progressHandling, false);
        }
        return myXhr;
      },
      success: function (data) {
        // your callback here
        $('#message').html(data);
        $(progress_bar_id).hide();

        setTimeout(function () {
          $(progress_text).fadeOut();
        },1000);

        $(current_upload_button_id).hide();

        $(".refresh_page_btn").show();

      },
      error: function (error) {
        // handle error
      },
      async: true,
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      timeout: 60000
    });
  };

  Upload.prototype.progressHandling = function (event) {
    var percent = 0;
    var position = event.loaded || event.position;
    var total = event.total;
    if (event.lengthComputable) {
      percent = Math.ceil(position / total * 100);
    }

    console.log(percent+"... progress update");

    // update progressbars classes so it fits your code
    $(progress_bar_id + " .progress-bar").css("width", +percent + "%");
    $(progress_bar_id + " .status").text(percent + "%");
  };

  function togglePlay(audiodiv) {
    var myAudio = document.getElementById(audiodiv);
    return myAudio.paused ? myAudio.play() : myAudio.pause();
  }

  $("#category_filter_id").on('change', function () {
    window.location.href = "/picture-dictionary-editor/story-answer/?catid=" + $(this).val();
  });

  $('#example1').DataTable({
    "drawCallback": function (settings) {

      $("#play_audio").off('click').on('click', function () {
        togglePlay("audio_player");
      });

      $(".edit-story-answer-btn").off('click').on('click', function () {
        $("#story_answer_media_edit").show();

        $("#update-text-fields-button").show();
        $(".refresh_page_btn").hide();

        $("#image_file_name").html($(this).data("picture"));
        $("#picture").val($(this).data("picture"));
        $("#language").val($(this).data("language"));
        $("#story_id").val($(this).data("story_id"));
        $("#question_id").val($(this).data("question_id"));
        $("#answer_id").val($(this).data("answer_id"));
        $("#answer").val($(this).data("answer"));

        $("#is_correct").prop('checked', false);
        if ($(this).data("is_correct")=="1") {
          $("#is_correct").prop('checked', true);
        }

        $("#story_text").html( "..." );
        $("#story_question_text").html( "..." );

        for (var i=0; i< stories_array.length; i++) {
          if (stories_array[i]['q_id'] == $(this).data("question_id")) {
            $("#story_text").html( stories_array[i]['story'] );
            $("#story_question_text").html( stories_array[i]['question'] );
          }
        }


        $("#audio_file").html($(this).data("audio"))

        $("#audio_player").attr({"src": "../audio/story-answer/" + $(this).data("audio") + "?cb=" + new Date().getTime()});

        $("#audio").val($(this).data("audio"));

        $('#image-file').css("color", "green");
        $('#image-preview-div').css("display", "inline-block");
        if ( $(this).data("picture") === "") {
          $('#preview-img').attr('src', "../pictures/daha-1.jpg");
        } else
        {
          $('#preview-img').attr('src', "../pictures/story-answer/" + $(this).data("picture"));
        }
        $('#preview-img').css('max-width', '150px');


        $("#story_answer_modal_title").html("Edit Answer");
        $("#edit_story_answer_modal").modal("show");
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


  $("#add_story_answer_btn").on('click', function () {
    $("#update-text-fields-button").show();
    $(".refresh_page_btn").hide();

    $("#question_id").val("");
    $("#story_id").val("0");

    if (new_answer_question_id!==0) {
      $("#question_id").val(new_answer_question_id);

      $("#story_text").html( "..." );
      $("#story_question_text").html( "..." );

      for (var i=0; i< stories_array.length; i++) {
        if (stories_array[i]['q_id'] == new_answer_question_id) {
          $("#story_text").html( stories_array[i]['story'] );
          $("#story_question_text").html( stories_array[i]['question'] );
          $("#story_id").val(stories_array[i]['s_id']);
        }
      }
    }


    $("#answer").val("");
    $("#is_correct").prop('checked', false);
    $("#answer_id").val("0");
    $("#audio").val("");
    $("#image_file_name").html("daha-1.jpg");

    $('#image-file').css("color", "green");
    $('#image-preview-div').css("display", "inline-block");
    $('#preview-img').attr('src', "../pictures/daha-1.jpg");
    $('#preview-img').css('max-width', '150px');

    $("#story_answer_modal_title").html("Add Story Answer");

    $("#story_answer_media_edit").hide();
    $("#edit_story_answer_modal").modal("show");
  });


  function selectImage(e) {
    $('#image-file').css("color", "green");
    $('#image-preview-div').css("display", "inline-block");
    $('#preview-img').attr('src', e.target.result);
    $('#preview-img').css('max-width', '150px');
  }

  var maxsize = 500 * 1024; // 500 KB

  $('#max-size').html((maxsize / 1024).toFixed(2));

  $("#upload-image-button").off('click').on('click', function (e) {
    e.preventDefault();
    $('#message').empty();
    $('#loading').show();
    $("#loading_msg").html("uploading...");
    $(".progress-wrp").show();

    current_upload_button_id = "#upload-image-button";
    progress_text = "#loading";
    progress_bar_id = "#progress-wrp";
    var file = $("#image-file")[0].files[0];
    var upload = new Upload(file, $("#answer_id").val(), "picture");
    // maby check size or type here with upload.getSize() and upload.getType()
    upload.doUpload();
  });

  $("#upload-audio-button").off('click').on('click', function (e) {
    e.preventDefault();
    $('#message').empty();
    $('#loading').show();
    $("#loading_msg").html("uploading...");
    $(".progress-wrp").show();

    current_upload_button_id = "#upload-audio-button";
    progress_text = "#loading";
    progress_bar_id = "#progress-wrp";
    var file = $("#file_audio")[0].files[0];
    var upload = new Upload(file, $("#answer_id").val(), "audio");
    // maby check size or type here with upload.getSize() and upload.getType()
    upload.doUpload();
  });

  $("#regen-audio-button").off('click').on('click', function (e) {
    e.preventDefault();

    $('#message').empty();
    $('#loading').show();
    $("#loading_msg").html("generating audio...");

    $.ajax({
      url: "../regen-audio-story-answer.php",
      type: "POST",
      data: {
        target_lang: $("#language").val(),
        audio_speed: $("#audio_speed").val(),
        answer: $("#answer").val(),
        answer_id: $("#answer_id").val()
      },
      success: function (data) {
        $('#loading').hide();
        $('#message').html(data);
        $("#regen-audio-button").hide();
        $(".refresh_page_btn").show();

        setTimeout(function () {
          $("#message").fadeOut();
        },1000);

      }
    });
  });

  $('#update-text-fields-button').on('click', function (e) {

    e.preventDefault();

    $('#message').empty();
    $('#loading').show();
    $("#loading_msg").html("saving form data...");

    $(".refresh_page_btn").each(function () {
      $(this).data("catid", $("#multi_category").val());
    });

    console.log ( $(".refresh_page_btn").data("catid") );

    $.ajax({
      url: "../save-story-answer-data.php",
      type: "POST",
      data: {
        story_id: $("#story_id").val(),
        question_id: $("#question_id").val(),
        answer_id: $("#answer_id").val(),
        answer: $("#answer").val(),
        is_correct: $("#is_correct").attr("checked") ? 1 : 0
      },
      // contentType: false,
      cache: false,
      // processData: false,
      success: function (data) {
        $('#message').html(data);
        $("#update-text-fields-button").hide();

        setTimeout(function () {
          $("#message").fadeOut();
          $('#loading').hide();
        },1000);

        $(".refresh_page_btn").show();

      }
    });

  });

  $('#image-file').change(function () {

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


  $(".refresh_page_btn").off('click').on('click',function () {
    var new_url = "https://elosoft.tw/picture-dictionary-editor/story-answer/?catid="+$(this).data("catid");
    window.location.href = new_url;
    return false;
  });


  $("#question_id").on('change', function () {
    $("#story_text").html( "..." );
    $("#story_question_text").html( "..." );

    for (var i=0; i< stories_array.length; i++) {
      if (stories_array[i]['q_id'] === $(this).val()) {
        $("#story_text").html( stories_array[i]['story'] );
        $("#story_question_text").html( stories_array[i]['question'] );
        $("#story_id").val( stories_array[i]['s_id'] );
      }
    }
  });


});

function delete_answer(answer_id) {
  if (confirm('Delete this answer?')) {
    window.location.href = 'index.php?delete_answer_id=' + answer_id;
  }
}


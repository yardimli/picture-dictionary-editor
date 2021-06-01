$(document).ready(function () {

  $(".progress-wrp").hide();

  $("#story_filter_id").on('change', function () {
    window.location.href = "/picture-dictionary-editor/story_question/?story_id=" + $(this).val();
  });

  $(".refresh_page_btn").each(function () {
    $(this).data("story_id", $("#story_filter_id").val());
  });
  console.log ( $(".refresh_page_btn").data("story_id") );


  var Upload = function (file, question_id, upload_type) {
    this.file = file;
    this.question_id = question_id;
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
    formData.append("question_id", this.question_id);
    formData.append("upload_type", this.upload_type);

    $.ajax({
      type: "POST",
      url: "../upload-file-story-question.php",
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
    window.location.href = "/picture-dictionary-editor/story_question/?story_id=" + $(this).val();
  });

  $('#example1').DataTable({
    "drawCallback": function (settings) {

      $("#play_audio").off('click').on('click', function () {
        togglePlay("audio_player");
      });

      $(".edit-story-question-btn").off('click').on('click', function () {
        $("#story_question_media_edit").show();

        $("#update-text-fields-button").show();
        $(".refresh_page_btn").hide();

        $("#language").val($(this).data("language"));
        $("#story_id").val($(this).data("story_id"));
        $("#question_id").val($(this).data("question_id"));
        $("#question").val($(this).data("question"));

        $("#show_answer_pictures").prop('checked', false);
        if ($(this).data("show_answer_pictures")=="1") {
          $("#show_answer_pictures").prop('checked', true);
        }

        $("#random_answers_from_other_questions").prop('checked', false);
        if ($(this).data("random_answers_from_other_questions")=="1") {
          $("#random_answers_from_other_questions").prop('checked', true);
        }

        $("#random_answers_from_same_question").prop('checked', false);
        if ($(this).data("random_answers_from_same_question")=="1") {
          $("#random_answers_from_same_question").prop('checked', true);
        }


        $("#story_text").html( "..." );
        for (var i=0; i< stories_array.length; i++) {
          if (stories_array[i]['id'] == $(this).data("story_id")) {
            $("#story_text").html( stories_array[i]['story'].replace("\n","<br>") );
          }
        }


        $("#audio_file").html($(this).data("audio"))

        $("#audio_player").attr({"src": "../audio/story-question/" + $(this).data("audio") + "?cb=" + new Date().getTime()});

        $("#audio").val($(this).data("audio"));

        $('#image-file').css("color", "green");
        $('#image-preview-div').css("display", "inline-block");
        if ( $(this).data("picture") === "") {
          $('#preview-img').attr('src', "../pictures/daha-1.jpg");
        } else
        {
          $('#preview-img').attr('src', "../pictures/story-question/" + $(this).data("picture"));
        }
        $('#preview-img').css('max-width', '150px');


        $("#story_question_modal_title").html("Edit Question");
        $("#edit_story_question_modal").modal("show");
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


  $("#add_story_question_btn").on('click', function () {
    $("#update-text-fields-button").show();
    $(".refresh_page_btn").hide();

    $("#story_id").val("");
    if (new_question_story_id!==0) {
      $("#story_id").val(new_question_story_id);

      $("#story_text").html( "..." );
      for (var i=0; i< stories_array.length; i++) {
        if (stories_array[i]['id'] == new_question_story_id) {
          $("#story_text").html( stories_array[i]['story'].replace("\n","<br>") );
        }
      }

    }
    $("#question").val("");
    $("#question_id").val("0");
    $("#audio").val("");
    $("#image_file_name").html("daha-1.jpg");

    $('#image-file').css("color", "green");
    $('#image-preview-div').css("display", "inline-block");
    $('#preview-img').attr('src', "../pictures/daha-1.jpg");
    $('#preview-img').css('max-width', '150px');

    $("#show_answer_pictures").prop('checked', false);
    $("#random_answers_from_other_questions").prop('checked', false);
    $("#random_answers_from_same_question").prop('checked', false);

    $("#story_question_modal_title").html("Add Story Question");

    $("#story_question_media_edit").hide();
    $("#edit_story_question_modal").modal("show");
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
    var upload = new Upload(file, $("#question_id").val(), "picture");
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
    var upload = new Upload(file, $("#question_id").val(), "audio");
    // maby check size or type here with upload.getSize() and upload.getType()
    upload.doUpload();
  });

  $("#regen-audio-button").off('click').on('click', function (e) {
    e.preventDefault();

    $('#message').empty();
    $('#loading').show();
    $("#loading_msg").html("generating audio...");

    $.ajax({
      url: "../regen-audio-story-question.php",
      type: "POST",
      data: {
        target_lang: $("#language").val(),
        audio_speed: $("#audio_speed").val(),
        question: $("#question").val(),
        question_id: $("#question_id").val()

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

    $.ajax({
      url: "../save-story-question-data.php",
      type: "POST",
      data: {
        story_id: $("#story_id").val(),
        question_id: $("#question_id").val(),
        question: $("#question").val(),
        show_answer_pictures: $("#show_answer_pictures").prop("checked") ? 1 : 0,
        random_answers_from_other_questions: $("#random_answers_from_other_questions").prop("checked") ? 1 : 0,
        random_answers_from_same_question: $("#random_answers_from_same_question").prop("checked") ? 1 : 0
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
    var new_url = "https://elosoft.tw/picture-dictionary-editor/story_question/?story_id="+$(this).data("story_id");
    window.location.href = new_url;
    return false;
  });


  $("#story_id").on('change', function () {
    $("#story_text").html( "..." );
    for (var i=0; i< stories_array.length; i++) {
      if (stories_array[i]['id'] === $(this).val()) {
        $("#story_text").html( stories_array[i]['story'].replace("\n","<br>") );
      }
    }
  });


});

function delete_question(question_id) {
  if (confirm('Delete this question?')) {
    window.location.href = 'index.php?delete_question_id=' + question_id+"&story_id="+$("#story_filter_id").val();
  }
}


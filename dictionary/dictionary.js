$(document).ready(function () {

  $(".progress-wrp").hide();

  $("#multi_category").html($("#multi_category option").sort(function (a, b) {
    return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
  }));

  $('#multi_category').selectpicker({"width": "400px", "size": 10});

  var Upload = function (file, word_id, upload_type) {
    this.file = file;
    this.word_id = word_id;
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

  var progress_bar_id = "#progress-wrp-en";
  var progress_text = '#loading_en';
  var current_upload_button_id = "#upload-image-button";


  Upload.prototype.doUpload = function () {
    var that = this;
    var formData = new FormData();


    // add assoc key values, this will be posts values
    formData.append("file", this.file, this.getName());
    formData.append("upload_file", true);
    formData.append("word_id", this.word_id);
    formData.append("upload_type", this.upload_type);

    $.ajax({
      type: "POST",
      url: "../upload-file.php",
      xhr: function () {
        var myXhr = $.ajaxSettings.xhr();
        if (myXhr.upload) {
          myXhr.upload.addEventListener('progress', that.progressHandling, false);
        }
        return myXhr;
      },
      success: function (data) {
        // your callback here
        $('#message_en').html(data);
        $(progress_bar_id).hide();

        setTimeout(function () {
          $(progress_text).fadeOut();
        }, 1000);

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

    console.log(percent + "... progress update");

    // update progressbars classes so it fits your code
    $(progress_bar_id + " .progress-bar").css("width", +percent + "%");
    $(progress_bar_id + " .status").text(percent + "%");
  };

  function togglePlay(audiodiv) {
    var myAudio = document.getElementById(audiodiv);
    return myAudio.paused ? myAudio.play() : myAudio.pause();
  }

  $("#category_filter_id").on('change', function () {
    window.location.href = "/picture-dictionary-editor/dictionary/?catid=" + $(this).val();
  });

  $("#category_filter_id").html($("#category_filter_id option").sort(function (a, b) {
    return a.text == b.text ? 0 : a.text < b.text ? -1 : 1
  }));

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

      $(".play-sound-btn").off('click').on('click', function () {
        const synth = new Tone.Synth().toDestination();
        const now = Tone.now();

        var toneX = $(this).data("word_sound");
        var tones = toneX.split(",");

        for (var i = 0; i < tones.length; i++) {
          synth.triggerAttackRelease(tones[i], "4n", now + (i * 0.6));
        }
      });

      $(".edit-word-en-btn").off('click').on('click', function () {
        $("#en_word_media_edit").show();

        $("#update-text-fields-button-en").show();
        $(".refresh_page_btn").hide();

        var xcatid = $(this).data("multi_category");
        $(".refresh_page_btn").each(function () {
          $(this).data("catid", PageCatID);
        });

        $("#word_sound").val($(this).data("word_sound"));
        $("#word_EN").val($(this).data("word_en"));
        console.log($(this).data("multi_category"));
        var temp_cats_str = $(this).data("multi_category") + "";
        $("#multi_category").selectpicker("val", temp_cats_str.split(","));
        $('#multi_category').selectpicker('refresh');
        $("#picture").val($(this).data("picture"));
        $("#word_id_en").val($(this).data("word_id"));
        $("#level").val($(this).data("level"));

        $("#image_file_name").html($(this).data("picture"));

        $("#en_audio_file").html($(this).data("audio_en"))

        $("#en_audio_player").attr({"src": "../audio/en/" + $(this).data("audio_en") + "?cb=" + new Date().getTime()});

        $("#audio_EN").val($(this).data("audio_en"));

        $('#image-file').css("color", "green");
        $('#image-preview-div').css("display", "inline-block");
        if ($(this).data("picture") === "") {
          $('#preview-img').attr('src', "../pictures/daha-1.jpg");
        }
        else {
          $('#preview-img').attr('src', "../pictures/" + $(this).data("picture"));
        }
        $('#preview-img').css('max-width', '150px');

        $("#word_modal_title").html("Edit Word");
        $("#edit_word_modal_en").modal("show");
      });


      $(".edit-word-tr-btn").off('click').on('click', function () {
        $("#update-text-fields-button-tr").show();
        $(".refresh_page_btn").hide();

        var xcatid = $(this).data("multi_category");
        $(".refresh_page_btn").each(function () {
          $(this).data("catid", PageCatID);
        });

        $("#word_TR").val($(this).data("word_tr"));
        $("#word_id_tr").val($(this).data("word_id"));
        $("#tr_audio_file").html($(this).data("audio_tr"))
        $("#tr_audio_player").attr({"src": "../audio/tr/" + $(this).data("audio_tr") + "?cb=" + new Date().getTime()});
        $("#audio_TR").val($(this).data("audio_tr"));

        $("#word_modal_title_tr").html("Edit Word");
        $("#edit_word_modal_tr").modal("show");
      });

      $(".edit-word-ch-btn").off('click').on('click', function () {
        $("#update-text-fields-button-ch").show();
        $(".refresh_page_btn").hide();

        var xcatid = $(this).data("multi_category");
        $(".refresh_page_btn").each(function () {
          $(this).data("catid", PageCatID);
        });

        $("#word_CH").val($(this).data("word_ch"));
        $("#word_id_ch").val($(this).data("word_id"));
        $("#ch_audio_file").html($(this).data("audio_ch"))
        $("#ch_audio_player").attr({"src": "../audio/ch/" + $(this).data("audio_ch") + "?cb=" + new Date().getTime()});
        $("#bopomofo").val($(this).data("bopomofo"));
        $("#audio_CH").val($(this).data("audio_ch"));

        $("#word_modal_title_ch").html("Edit Word");
        $("#edit_word_modal_ch").modal("show");
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
    $("#update-text-fields-button-en").show();
    $(".refresh_page_btn").hide();

    $("#word_sound").val("");
    $("#word_EN").val("");
    $("#multi_category").val(0);
    $('#multi_category').selectpicker('refresh');

    $("#picture").val("");
    $("#word_id_en").val("0");
    $("#level").val("1");

    $("#audio_EN").val("");

    $("#image_file_name").html("daha-1.jpg");


    $('#image-file').css("color", "green");
    $('#image-preview-div').css("display", "inline-block");
    $('#preview-img').attr('src', "../pictures/daha-1.jpg");
    $('#preview-img').css('max-width', '150px');


    $("#word_modal_title_en").html("Add Word");

    $("#en_word_media_edit").hide();
    $("#edit_word_modal_en").modal("show");
  });


  // setTimeout(function () {
  //   $(".alert").fadeOut("slow", function () {
  //     $(".alert").remove();
  //   });
  // }, 5000);


  /*jslint browser: true, white: true, eqeq: true, plusplus: true, sloppy: true, vars: true*/

  /*global $, console, alert, FormData, FileReader*/


  function selectImage(e) {
    $('#image-file').css("color", "green");
    $('#image-preview-div').css("display", "inline-block");
    $('#preview-img').attr('src', e.target.result);
    $('#preview-img').css('max-width', '150px');
  }


  var maxsize = 500 * 1024; // 500 KB

  $('#max-size').html((maxsize / 1024).toFixed(2));

  $("#btn-trans-en").off('click').on('click', function (e) {
    e.preventDefault();
    $('#message').empty();
    $('#loading').show();
    $("#loading_msg").html("auto translate...");

    $.ajax({
      url: "../auto-trans.php",
      type: "POST",
      data: {
        trans_source: "en",
        word_EN: $("#word_EN").val(),
        word_id: $("#word_id_en").val()
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

  $("#upload-image-button").off('click').on('click', function (e) {
    e.preventDefault();
    $('#message_en').empty();
    $('#loading_en').show();
    $("#loading_msg_en").html("uploading...");
    $(".progress-wrp").show();

    current_upload_button_id = "#upload-image-button";
    progress_text = "#loading_en";
    progress_bar_id = "#progress-wrp-en";
    var file = $("#image-file")[0].files[0];
    var upload = new Upload(file, $("#word_id_en").val(), "picture");
    // maby check size or type here with upload.getSize() and upload.getType()
    upload.doUpload();
  });

  $("#upload-audio-en-button").off('click').on('click', function (e) {
    e.preventDefault();
    $('#message_en').empty();
    $('#loading_en').show();
    $("#loading_msg_en").html("uploading...");
    $(".progress-wrp").show();

    current_upload_button_id = "#upload-audio-en-button";
    progress_text = "#loading_en";
    progress_bar_id = "#progress-wrp-en";
    var file = $("#file_audio_en")[0].files[0];
    var upload = new Upload(file, $("#word_id_en").val(), "audio_EN");
    // maby check size or type here with upload.getSize() and upload.getType()
    upload.doUpload();
  });

  $("#upload-audio-tr-button").off('click').on('click', function (e) {
    e.preventDefault();
    $('#message_tr').empty();
    $('#loading_tr').show();
    $("#loading_msg_tr").html("uploading...");
    $(".progress-wrp").show();

    current_upload_button_id = "#upload-audio-tr-button";
    progress_text = "#loading_tr";
    progress_bar_id = "#progress-wrp-tr";
    var file = $("#file_audio_tr")[0].files[0];
    var upload = new Upload(file, $("#word_id_tr").val(), "audio_TR");
    // maby check size or type here with upload.getSize() and upload.getType()
    upload.doUpload();
  });

  $("#upload-audio-ch-button").off('click').on('click', function (e) {
    e.preventDefault();
    $('#message_ch').empty();
    $('#loading_ch').show();
    $("#loading_msg_ch").html("uploading...");
    $(".progress-wrp").show();

    current_upload_button_id = "#upload-audio-ch-button";
    progress_text = "#loading_ch";
    progress_bar_id = "#progress-wrp-ch";
    var file = $("#file_audio_ch")[0].files[0];
    var upload = new Upload(file, $("#word_id_ch").val(), "audio_CH");
    // maby check size or type here with upload.getSize() and upload.getType()
    upload.doUpload();
  });

  $("#regen-audio-en-button").off('click').on('click', function (e) {
    e.preventDefault();

    $('#message_en').empty();
    $('#loading_en').show();
    $("#loading_msg_en").html("generating audio...");

    $.ajax({
      url: "../regen-audio.php",
      type: "POST",
      data: {
        target_lang: "en",
        audio_speed: $("#audio_speed_en").val(),
        word_EN: $("#word_EN").val(),
        word_id: $("#word_id_en").val()
      },
      success: function (data) {
        $('#loading_en').hide();
        $('#message_en').html(data);
        $("#regen-audio-en-button").hide();
        $(".refresh_page_btn").show();

        setTimeout(function () {
          $("#message_en").fadeOut();
        }, 1000);

      }
    });
  });

  $("#regen-audio-tr-button").off('click').on('click', function (e) {
    e.preventDefault();

    $('#message_tr').empty();
    $('#loading_tr').show();
    $("#loading_msg_tr").html("generating audio...");

    $.ajax({
      url: "../regen-audio.php",
      type: "POST",
      data: {
        target_lang: "tr",
        audio_speed: $("#audio_speed_tr").val(),
        word_TR: $("#word_TR").val(),
        word_id: $("#word_id_tr").val()
      },
      success: function (data) {
        $('#loading_tr').hide();
        $('#message_tr').html(data);
        $("#regen-audio-tr-button").hide();
        $(".refresh_page_btn").show();

        setTimeout(function () {
          $("#message_tr").fadeOut();
        }, 1000);

      }
    });
  });

  $("#regen-audio-ch-button").off('click').on('click', function (e) {
    e.preventDefault();

    $('#message_ch').empty();
    $('#loading_ch').show();
    $("#loading_msg_ch").html("generating audio...");

    $.ajax({
      url: "../regen-audio.php",
      type: "POST",
      data: {
        target_lang: "ch",
        audio_speed: $("#audio_speed_ch").val(),
        word_CH: $("#word_CH").val(),
        word_id: $("#word_id_ch").val()
      },
      success: function (data) {
        $('#loading_ch').hide();
        $('#message_ch').html(data);
        $("#regen-audio-ch-button").hide();
        $(".refresh_page_btn").show();

        setTimeout(function () {
          $("#message_ch").fadeOut();
        }, 1000);

      }
    });
  });

  $('#update-text-fields-button-en').on('click', function (e) {

    e.preventDefault();

    $('#message_en').empty();
    $('#loading_en').show();
    $("#loading_msg_en").html("saving form data...");

    $(".refresh_page_btn").each(function () {
      $(this).data("catid", PageCatID);
    });

    console.log($(".refresh_page_btn").data("catid"));

    $.ajax({
      url: "../save-word-data.php",
      type: "POST",
      data: {
        language: $("#language_en").val(),
        word_id: $("#word_id_en").val(),
        word_EN: $("#word_EN").val(),
        word_sound: $("#word_sound").val(),
        multi_category: $("#multi_category").val(),
        level: $("#level").val(),
      },
      // contentType: false,
      cache: false,
      // processData: false,
      success: function (data) {
        $('#message_en').html(data);
        $("#update-text-fields-button-en").hide();

        setTimeout(function () {
          $("#message_en").fadeOut();
          $('#loading_en').hide();
        }, 1000);

        $(".refresh_page_btn").show();

      }
    });

  });

  $('#update-text-fields-button-tr').on('click', function (e) {

    e.preventDefault();

    $('#message_tr').empty();
    $('#loading_tr').show();
    $("#loading_msg_tr").html("saving form data...");

    $.ajax({
      url: "../save-word-data.php",
      type: "POST",
      data: {
        language: $("#language_tr").val(),
        word_id: $("#word_id_tr").val(),
        word_TR: $("#word_TR").val(),
      },
      // contentType: false,
      cache: false,
      // processData: false,
      success: function (data) {
        $('#message_tr').html(data);
        $("#update-text-fields-button-tr").hide();

        setTimeout(function () {
          $('#loading_tr').hide();
          $("#message_tr").fadeOut();
        }, 1000);

        $(".refresh_page_btn").show();

      }
    });

  });

  $('#update-text-fields-button-ch').on('click', function (e) {

    e.preventDefault();

    $('#message_ch').empty();
    $('#loading_ch').show();
    $("#loading_msg_ch").html("saving form data...");

    $.ajax({
      url: "../save-word-data.php",
      type: "POST",
      data: {
        language: $("#language_ch").val(),
        word_id: $("#word_id_ch").val(),
        word_CH: $("#word_CH").val(),
        bopomofo: $("#bopomofo").val(),
      },
      // contentType: false,
      cache: false,
      // processData: false,
      success: function (data) {
        $('#message_ch').html(data);
        $("#update-text-fields-button-ch").hide();

        setTimeout(function () {
          $('#loading_ch').hide();
          $("#message_ch").fadeOut();
        }, 1000);

        $(".refresh_page_btn").show();
      }
    });

  });

  $("#check-word-sound").on('click', function (e) {

    $.ajax({
      url: "../save-word-data.php",
      type: "POST",
      data: {
        operation: "check_word_sound",
        word_id: $("#word_id_en").val(),
        word_sound: $("#word_sound").val()
      },
      cache: false,
      success: function (data) {
        $('#check-word-sound-result').fadeIn();
        $('#check-word-sound-result').html(data);
        setTimeout(function () {
          $("#check-word-sound-result").fadeOut();
        }, 3000);
      }
    });

  });

  $("#play-word-sound").on('click', function (e) {
    e.preventDefault();

    const synth = new Tone.Synth().toDestination();
    const now = Tone.now();

    var toneX = $("#word_sound").val();
    var tones = toneX.split(",");


    for (var i = 0; i < tones.length; i++) {
      synth.triggerAttackRelease(tones[i], "4n", now + (i * 0.6));
    }

    // synth.triggerAttackRelease("C4", "4n", now);
    // synth.triggerAttackRelease("E4", "4n", now + 0.75);
    // synth.triggerAttackRelease("G4", "4n", now + 1.5);

    return false;

  });

  $('#image-file').change(function () {

    $('#message_en').empty();

    var file = this.files[0];
    var match = ["image/jpeg", "image/png", "image/jpg"];

    if (!((file.type == match[0]) || (file.type == match[1]) || (file.type == match[2]))) {
      $('#message_en').html('<div class="alert alert-warning" role="alert">Invalid image format. Allowed formats: JPG, JPEG, PNG.</div>');

      return false;
    }

    if (file.size > maxsize) {
      $('#message_en').html('<div class=\"alert alert-danger\" role=\"alert\">The size of image you are attempting to upload is ' + (file.size / 1024).toFixed(2) + ' KB, maximum size allowed is ' + (maxsize / 1024).toFixed(2) + ' KB</div>');

      return false;
    }

    // $('#upload-button').removeAttr("disabled");

    var reader = new FileReader();
    reader.onload = selectImage;
    reader.readAsDataURL(this.files[0]);
  });

  $(".refresh_page_btn").off('click').on('click', function () {
    var new_url = "https://elosoft.tw/picture-dictionary-editor/dictionary/?catid=" + $(this).data("catid");
    window.location.href = new_url;
    return false;
  });
});

function delete_word(wordID) {
  if (confirm('Delete this word?')) {
    window.location.href = 'index.php?delete_word_id=' + wordID;
  }
}


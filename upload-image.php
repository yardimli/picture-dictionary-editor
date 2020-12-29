<?php
require_once( "./config/session.php" );

require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\TextToSpeech\V1\AudioConfig;
use Google\Cloud\TextToSpeech\V1\AudioEncoding;
use Google\Cloud\TextToSpeech\V1\SsmlVoiceGender;
use Google\Cloud\TextToSpeech\V1\SynthesisInput;
use Google\Cloud\TextToSpeech\V1\TextToSpeechClient;
use Google\Cloud\TextToSpeech\V1\VoiceSelectionParams;
use Google\Cloud\Translate\V2\TranslateClient;

require_once( "./config/class.dictionary.php" );
$dictionary_list = new DICTIONARY();

require_once( "./config/class.user.php" );
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


$error_msg = "";

clearstatcache();

function beautify_filename( $filename ) {
  // reduce consecutive characters
  $filename = preg_replace( array(
    // "file   name.zip" becomes "file-name.zip"
    '/ +/',
    // "file___name.zip" becomes "file-name.zip"
    '/_+/',
    // "file---name.zip" becomes "file-name.zip"
    '/-+/'
  ), '-', $filename );
  $filename = preg_replace( array(
    // "file--.--.-.--name.zip" becomes "file.name.zip"
    '/-*\.-*/',
    // "file...name..zip" becomes "file.name.zip"
    '/\.{2,}/'
  ), '.', $filename );
  // lowercase for windows/unix interoperability http://support.microsoft.com/kb/100625
  $filename = mb_strtolower( $filename, mb_detect_encoding( $filename ) );
  // ".file-name.-" becomes "file-name"
  $filename = trim( $filename, '.-' );

  return $filename;
}

function filter_filename( $filename, $beautify = true ) {
  // sanitize filename
  $filename = preg_replace(
    '~
        [<>:"/\\|?*]|            # file system reserved https://en.wikipedia.org/wiki/Filename#Reserved_characters_and_words
        [\x00-\x1F]|             # control characters http://msdn.microsoft.com/en-us/library/windows/desktop/aa365247%28v=vs.85%29.aspx
        [\x7F\xA0\xAD]|          # non-printing characters DEL, NO-BREAK SPACE, SOFT HYPHEN
        [#\[\]@!$&\'()+,;=]|     # URI reserved https://tools.ietf.org/html/rfc3986#section-2.2
        [{}^\~`]                 # URL unsafe characters https://www.ietf.org/rfc/rfc1738.txt
        ~x',
    '-', $filename );
  // avoids ".", ".." or ".hiddenFiles"
  $filename = ltrim( $filename, '.-' );
  // optional beautification
  if ( $beautify ) {
    $filename = beautify_filename( $filename );
  }
  // maximize filename length to 255 bytes http://serverfault.com/a/9548/44086
  $ext      = pathinfo( $filename, PATHINFO_EXTENSION );
  $filename = mb_strcut( pathinfo( $filename, PATHINFO_FILENAME ), 0, 255 - ( $ext ? strlen( $ext ) + 1 : 0 ), mb_detect_encoding( $filename ) ) . ( $ext ? '.' . $ext : '' );

  return $filename;
}

function url_make( $str ) {
  $before = array( 'ı', 'ğ', 'ü', 'ş', 'ö', 'ç', 'İ', 'Ğ', 'Ü', 'Ö', 'Ç' ); // , '\'', '""'
  $after  = array( 'i', 'g', 'u', 's', 'o', 'c', 'i', 'g', 'u', 'o', 'c' ); // , '', ''

  $clean = str_replace( $before, $after, $str );

  return $clean;
}


if ( ! $auth_user->is_loggedin() ) {
  # redirect to login page, gtfo
  $auth_user->doLogout();
} else {

  $audio_en = $_POST["audio_EN"];
  if ( isset( $_FILES["file_audio_en"] ) && $_FILES ["file_audio_en"] ["error"] == UPLOAD_ERR_OK ) {
    $max_size              = 500 * 1024; // 500 KB
    $destination_directory = "./audio/en/";
    $validextensions       = array( "mp3" );
    $temporary             = explode( ".", $_FILES["file_audio_en"]["name"] );
    $file_extension        = end( $temporary );

    if ( in_array( $file_extension, $validextensions ) ) {
      if ( $_FILES["file_audio_en"]["size"] < ( $max_size ) ) {
        if ( $_FILES["file_audio_en"]["error"] > 0 ) {
          echo "<div class=\"alert alert-danger\" role=\"alert\">Error: <strong>" . $_FILES["file_audio_en"]["error"] . "</strong></div>";
        } else {
          if ( file_exists( $destination_directory . $_FILES["file_audio_en"]["name"] ) ) {
            echo "<div class=\"alert alert-danger\" role=\"alert\">Error: File <strong>" . $_FILES["file_audio_en"]["name"] . "</strong> already exists. Unlinking.</div>";
            $audio_en = $_FILES["file_audio_en"]["name"];
            unlink( $destination_directory . $_FILES["file_audio_en"]["name"] );
          }

          $sourcePath = $_FILES["file_audio_en"]["tmp_name"];
          $targetPath = $destination_directory . $_FILES["file_audio_en"]["name"];
          move_uploaded_file( $sourcePath, $targetPath );
          $audio_en = $_FILES["file_audio_en"]["name"];

          echo "<div class=\"alert alert-success\" role=\"alert\">";
          echo "<p>En Audio uploaded successful</p>";
          echo "<p>File Name: <a href=\"" . $targetPath . "\"><strong>" . $targetPath . "</strong></a></p>";
          echo "<p>Type: <strong>" . $_FILES["file_audio_en"]["type"] . "</strong></p>";
          echo "<p>Size: <strong>" . round( $_FILES["file_audio_en"]["size"] / 1024, 2 ) . " kB</strong></p>";
          echo "<p>Temp file: <strong>" . $_FILES["file_audio_en"]["tmp_name"] . "</strong></p>";

        }
      } else {
        echo "<div class=\"alert alert-danger\" role=\"alert\">The size of audio you are attempting to upload is " . round( $_FILES["file_audio_en"]["size"] / 1024, 2 ) . " KB, maximum size allowed is " . round( $max_size / 1024, 2 ) . " KB</div>";
      }
    } else {
      echo "<div class=\"alert alert-danger\" role=\"alert\">" . $_FILES["file_audio_en"]["type"] . " You can only upload en audio.</div>";
    }
  }

  $audio_tr = $_POST["audio_TR"];
  if ( isset( $_FILES["file_audio_tr"] ) && $_FILES ["file_audio_tr"] ["error"] == UPLOAD_ERR_OK ) {
    $max_size              = 500 * 1024; // 500 KB
    $destination_directory = "./audio/tr/";
    $validextensions       = array( "mp3" );
    $temporary             = explode( ".", $_FILES["file_audio_tr"]["name"] );
    $file_extension        = end( $temporary );

    if ( in_array( $file_extension, $validextensions ) ) {
      if ( $_FILES["file_audio_tr"]["size"] < ( $max_size ) ) {
        if ( $_FILES["file_audio_tr"]["error"] > 0 ) {
          echo "<div class=\"alert alert-danger\" role=\"alert\">Error: <strong>" . $_FILES["file_audio_tr"]["error"] . "</strong></div>";
        } else {
          if ( file_exists( $destination_directory . $_FILES["file_audio_tr"]["name"] ) ) {
            echo "<div class=\"alert alert-danger\" role=\"alert\">Error: File <strong>" . $_FILES["file_audio_tr"]["name"] . "</strong> already exists. Unlinking.</div>";
            $audio_tr = $_FILES["file_audio_tr"]["name"];
            unlik( $destination_directory . $_FILES["file_audio_tr"]["name"] );
          }

          $sourcePath = $_FILES["file_audio_tr"]["tmp_name"];
          $targetPath = $destination_directory . $_FILES["file_audio_tr"]["name"];
          move_uploaded_file( $sourcePath, $targetPath );
          $audio_tr = $_FILES["file_audio_tr"]["name"];

          echo "<div class=\"alert alert-success\" role=\"alert\">";
          echo "<p>En Audio uploaded successful</p>";
          echo "<p>File Name: <a href=\"" . $targetPath . "\"><strong>" . $targetPath . "</strong></a></p>";
          echo "<p>Type: <strong>" . $_FILES["file_audio_tr"]["type"] . "</strong></p>";
          echo "<p>Size: <strong>" . round( $_FILES["file_audio_tr"]["size"] / 1024, 2 ) . " kB</strong></p>";
          echo "<p>Temp file: <strong>" . $_FILES["file_audio_tr"]["tmp_name"] . "</strong></p>";

        }
      } else {
        echo "<div class=\"alert alert-danger\" role=\"alert\">The size of audio you are attempting to upload is " . round( $_FILES["file_audio_tr"]["size"] / 1024, 2 ) . " KB, maximum size allowed is " . round( $max_size / 1024, 2 ) . " KB</div>";
      }
    } else {
      echo "<div class=\"alert alert-danger\" role=\"alert\">" . $_FILES["file_audio_tr"]["type"] . " You can only tr upload audio.</div>";
    }
  }

  $audio_ch = $_POST["audio_CH"];
  if ( isset( $_FILES["file_audio_ch"] ) && $_FILES ["file_audio_ch"] ["error"] == UPLOAD_ERR_OK ) {
    $max_size              = 500 * 1024; // 500 KB
    $destination_directory = "./audio/en/";
    $validextensions       = array( "mp3" );
    $temporary             = explode( ".", $_FILES["file_audio_ch"]["name"] );
    $file_extension        = end( $temporary );

    if ( in_array( $file_extension, $validextensions ) ) {
      if ( $_FILES["file_audio_ch"]["size"] < ( $max_size ) ) {
        if ( $_FILES["file_audio_ch"]["error"] > 0 ) {
          echo "<div class=\"alert alert-danger\" role=\"alert\">Error: <strong>" . $_FILES["file_audio_ch"]["error"] . "</strong></div>";
        } else {
          if ( file_exists( $destination_directory . $_FILES["file_audio_ch"]["name"] ) ) {
            echo "<div class=\"alert alert-danger\" role=\"alert\">Error: File <strong>" . $_FILES["file_audio_ch"]["name"] . "</strong> already exists. Unlinking.</div>";
            $audio_ch = $_FILES["file_audio_ch"]["name"];
            unlink( $destination_directory . $_FILES["file_audio_ch"]["name"] );
          }

          $sourcePath = $_FILES["file_audio_ch"]["tmp_name"];
          $targetPath = $destination_directory . $_FILES["file_audio_ch"]["name"];
          move_uploaded_file( $sourcePath, $targetPath );
          $audio_ch = $_FILES["file_audio_ch"]["name"];

          echo "<div class=\"alert alert-success\" role=\"alert\">";
          echo "<p>En Audio uploaded successful</p>";
          echo "<p>File Name: <a href=\"" . $targetPath . "\"><strong>" . $targetPath . "</strong></a></p>";
          echo "<p>Type: <strong>" . $_FILES["file_audio_ch"]["type"] . "</strong></p>";
          echo "<p>Size: <strong>" . round( $_FILES["file_audio_ch"]["size"] / 1024, 2 ) . " kB</strong></p>";
          echo "<p>Temp file: <strong>" . $_FILES["file_audio_ch"]["tmp_name"] . "</strong></p>";

        }
      } else {
        echo "<div class=\"alert alert-danger\" role=\"alert\">The size of audio you are attempting to upload is " . round( $_FILES["file_audio_ch"]["size"] / 1024, 2 ) . " KB, maximum size allowed is " . round( $max_size / 1024, 2 ) . " KB</div>";
      }
    } else {
      echo "<div class=\"alert alert-danger\" role=\"alert\">" . $_FILES["file_audio_ch"]["type"] . " You can only upload ch audio.</div>";
    }
  }


  $picture = $_POST["picture"];
  if ( isset( $_FILES["file"] ) && $_FILES ["file"] ["error"] == UPLOAD_ERR_OK ) {
    $max_size              = 500 * 1024; // 500 KB
    $destination_directory = "./pictures/";
    $validextensions       = array( "jpeg", "jpg", "png" );

    $temporary      = explode( ".", $_FILES["file"]["name"] );
    $file_extension = end( $temporary );

    if ( ( ( $_FILES["file"]["type"] == "image/png" ) ||
           ( $_FILES["file"]["type"] == "image/jpg" ) ||
           ( $_FILES["file"]["type"] == "image/jpeg" )
         ) && in_array( $file_extension, $validextensions ) ) {
      if ( $_FILES["file"]["size"] < ( $max_size ) ) {
        if ( $_FILES["file"]["error"] > 0 ) {
          echo "<div class=\"alert alert-danger\" role=\"alert\">Error: <strong>" . $_FILES["file"]["error"] . "</strong></div>";
        } else {
          if ( file_exists( $destination_directory . $_FILES["file"]["name"] ) ) {
            echo "<div class=\"alert alert-danger\" role=\"alert\">Error: File <strong>" . $_FILES["file"]["name"] . "</strong> already exists. Unliking.</div>";
            $picture = $_FILES["file"]["name"];
            unlink( $destination_directory . $_FILES["file"]["name"] );
          }

          $sourcePath = $_FILES["file"]["tmp_name"];
          $targetPath = $destination_directory . $_FILES["file"]["name"];
          move_uploaded_file( $sourcePath, $targetPath );
          $picture = $_FILES["file"]["name"];

          echo "<div class=\"alert alert-success\" role=\"alert\">";
          echo "<p>Image uploaded successful</p>";
          echo "<p>File Name: <a href=\"" . $targetPath . "\"><strong>" . $targetPath . "</strong></a></p>";
          echo "<p>Type: <strong>" . $_FILES["file"]["type"] . "</strong></p>";
          echo "<p>Size: <strong>" . round( $_FILES["file"]["size"] / 1024, 2 ) . " kB</strong></p>";
          echo "<p>Temp file: <strong>" . $_FILES["file"]["tmp_name"] . "</strong></p>";

        }
      } else {
        echo "<div class=\"alert alert-danger\" role=\"alert\">The size of image you are attempting to upload is " . round( $_FILES["file"]["size"] / 1024, 2 ) . " KB, maximum size allowed is " . round( $max_size / 1024, 2 ) . " KB</div>";
      }
    } else {
      echo "<div class=\"alert alert-danger\" role=\"alert\">You can only upload image files.</div>";
    }
  }


  $translate = new TranslateClient( array(
    'keyFilePath' => __DIR__ . "/google-key.json",
    'projectId'   => 'onyx-cumulus-289504'
  ) );


// Translate text from english to french.


  $textToSpeechClient = new TextToSpeechClient( [ 'credentials' => __DIR__ . "/google-key.json" ] );

  $word_en = $_POST["word_EN"];

  if ( $word_en !== "" ) {
    $input = new SynthesisInput();
    $input->setText( $word_en );
    $voice = new VoiceSelectionParams();
    $voice->setLanguageCode( 'en-US' );
    $voice->setName( 'en-US-Wavenet-A' );
    $voice->setSsmlGender( SsmlVoiceGender::FEMALE );
    $audioConfig = new AudioConfig();
    $audioConfig->setSpeakingRate( 0.75 );
    $audioConfig->setAudioEncoding( AudioEncoding::MP3 );

    $audio_en = filter_filename( $word_en ) . ".mp3";

    $resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
    if (file_exists( "./audio/en/" . $audio_en )) {
      unlink("./audio/en/" . $audio_en);
    }
    file_put_contents( "./audio/en/" . $audio_en, $resp->getAudioContent() );
  }

  $word_tr = $_POST["word_TR"];
  if ( $word_tr === "" ) {
    $result  = $translate->translate( $word_en, [
      'target' => 'tr'
    ] );
    $word_tr = $result['text'];
//		var_dump($result);
  }


  if ( $word_tr !== "" ) {
    $input = new SynthesisInput();
    $input->setText( $word_tr );
    $voice = new VoiceSelectionParams();
    $voice->setLanguageCode( 'tr-TR' );
    $voice->setName( 'tr-TR-Wavenet-D' );
    $voice->setSsmlGender( SsmlVoiceGender::FEMALE );
    $audioConfig = new AudioConfig();
    $audioConfig->setSpeakingRate( 0.75 );
    $audioConfig->setAudioEncoding( AudioEncoding::MP3 );

    $audio_tr = url_make( filter_filename( $word_tr ) ) . ".mp3";

    $resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );

    if (file_exists( "./audio/tr/" . $audio_tr )) {
      unlink("./audio/tr/" . $audio_tr);
    }
    file_put_contents( "./audio/tr/" . $audio_tr, $resp->getAudioContent() );
  }

  $word_ch = $_POST["word_CH"];
  if ( $word_ch === "" ) {
    $result  = $translate->translate( $word_en, [
      'target' => 'zh-TW'
    ] );
    $word_ch = $result['text'];
//		var_dump($result);
  }


  if ( $word_ch !== "" ) {
    $input = new SynthesisInput();
    $input->setText( $word_ch );
    $voice = new VoiceSelectionParams();
    $voice->setLanguageCode( 'cmn-TW' );
    $voice->setName( 'cmn-TW-Wavenet-A' );
    $voice->setSsmlGender( SsmlVoiceGender::FEMALE );
    $audioConfig = new AudioConfig();
    $audioConfig->setSpeakingRate( 0.75 );
    $audioConfig->setAudioEncoding( AudioEncoding::MP3 );

    if ( filter_filename( $word_en ) !== "" ) {
      $audio_ch = filter_filename( $word_en ) . ".mp3";
    } else {
      $audio_ch = $_POST["word_id"] . ".mp3";
    }

    $resp = $textToSpeechClient->synthesizeSpeech( $input, $voice, $audioConfig );
    if (file_exists( "./audio/ch/" . $audio_ch )) {
      if (unlink("./audio/ch/" . $audio_ch)) {
        $error_msg = "Deleted old chinese audio.";
      } else
      {
        $error_msg = "Can't delete old chinese audio.";
      }
    }
    file_put_contents( "./audio/ch/" . $audio_ch, $resp->getAudioContent() );
  }

  if ( $_POST["word_id"] === "0" ) {
    if ( $dictionary_list->add_word( $word_en, $word_tr, $word_ch, $_POST["bopomofo"], $picture, $_POST["category_id"], $_POST["level"], $audio_en, $audio_tr, $audio_ch ) ) {
      echo "<p>Word Added</strong></p>";
    }
  } else {
    if ( $dictionary_list->update_dictionary( $word_en, $word_tr, $word_ch, $_POST["bopomofo"], $picture, $_POST["category_id"], $_POST["word_id"], $_POST["level"], $audio_en, $audio_tr, $audio_ch ) ) {
      echo "<p>Word Updated</strong></p>";
    }
  }
  echo "</div>";
}
?>
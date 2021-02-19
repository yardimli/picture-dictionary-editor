<?php
require_once( "./config/session.php" );

require __DIR__ . '/vendor/autoload.php';

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
	if ( $_POST["word_id"] === "0" ) {
		echo "<div class=\"alert alert-success\" role=\"alert\">";
		echo "<p>Please save and open word in edit mode to update audio.</p>";
		echo $error_msg;
		echo "</div>";

	} else {

		if ( $_POST["upload_type"] === "audio_EN" ) {
			$max_size              = 500 * 1024; // 500 KB
			$destination_directory = "./audio/en/";
			$validextensions       = array( "mp3" );
		}

		if ( $_POST["upload_type"] === "audio_TR" ) {
			$max_size              = 500 * 1024; // 500 KB
			$destination_directory = "./audio/tr/";
			$validextensions       = array( "mp3" );
		}

		if ( $_POST["upload_type"] === "audio_CH" ) {
			$max_size              = 500 * 1024; // 500 KB
			$destination_directory = "./audio/ch/";
			$validextensions       = array( "mp3" );
		}

		if ( $_POST["upload_type"] === "picture" ) {
			$max_size              = 500 * 1024; // 500 KB
			$destination_directory = "./pictures/";
			$validextensions       = array( "jpeg", "jpg", "png" );
		}

		if ( isset( $_FILES["file"] ) && $_FILES ["file"] ["error"] == UPLOAD_ERR_OK ) {
			$temporary      = explode( ".", $_FILES["file"]["name"] );
			$file_extension = end( $temporary );

//			if ( ( ( $_FILES["file"]["type"] == "image/png" ) ||
//			       ( $_FILES["file"]["type"] == "image/jpg" ) ||
//			       ( $_FILES["file"]["type"] == "image/jpg" ) ||
//			       ( $_FILES["file"]["type"] == "image/jpeg" )

			if ( in_array( $file_extension, $validextensions ) ) {
				if ( $_FILES["file"]["size"] < ( $max_size ) ) {
					if ( $_FILES["file"]["error"] > 0 ) {
						echo "<div class=\"alert alert-danger\" role=\"alert\">Error: <strong>" . $_FILES["file"]["error"] . "</strong></div>";
					} else {

						$new_filename = $_FILES["file"]["name"];
						$new_filename = str_replace( "." . $file_extension, "_" . time() . "." . $file_extension, $new_filename );

						if ( file_exists( $destination_directory . $new_filename ) ) {
							echo "<div class=\"alert alert-danger\" role=\"alert\">Error: File <strong>" . $new_filename . "</strong> already exists. Unliking.</div>";
							unlink( $destination_directory . $new_filename );
						}

						$sourcePath = $_FILES["file"]["tmp_name"];
						$targetPath = $destination_directory . $new_filename;
						move_uploaded_file( $sourcePath, $targetPath );

						echo "<div class=\"alert alert-success\" role=\"alert\">";
						echo "<p>Image uploaded successful</p>";
						echo "<p>File Name: <a href=\"" . $targetPath . "\"><strong>" . $targetPath . "</strong></a></p>";
						echo "<p>Type: <strong>" . $_FILES["file"]["type"] . "</strong></p>";
						echo "<p>Size: <strong>" . round( $_FILES["file"]["size"] / 1024, 2 ) . " kB</strong></p>";
						echo "<p>Temp file: <strong>" . $_FILES["file"]["tmp_name"] . "</strong></p>";

						if ( $_POST["upload_type"] === "picture" ) {
							$stmt2 = $dictionary_list->runQuery( 'UPDATE dictionary set picture="' . $new_filename . '" WHERE id=' . $_POST["word_id"] );
							$stmt2->execute();
						}

						if ( $_POST["upload_type"] === "audio_EN" ) {
							$stmt2 = $dictionary_list->runQuery( 'UPDATE dictionary set audio_EN="' . $new_filename . '" WHERE id=' . $_POST["word_id"] );
							$stmt2->execute();
							?>
							<script>
								$("#en_audio_player").attr({"src": "../audio/en/<?php echo $new_filename; ?>"});
								$("#en_audio_file").html("<?php echo $new_filename; ?>");
							</script>
							<?php
						}

						if ( $_POST["upload_type"] === "audio_TR" ) {
							$stmt2 = $dictionary_list->runQuery( 'UPDATE dictionary set audio_TR="' . $new_filename . '" WHERE id=' . $_POST["word_id"] );
							$stmt2->execute();
							?>
							<script>
								$("#tr_audio_player").attr({"src": "../audio/tr/<?php echo $new_filename; ?>"});
								$("#tr_audio_file").html("<?php echo $new_filename; ?>");
							</script>
							<?php
						}

						if ( $_POST["upload_type"] === "audio_CH" ) {
							$stmt2 = $dictionary_list->runQuery( 'UPDATE dictionary set audio_CH="' . $new_filename . '" WHERE id=' . $_POST["word_id"] );
							$stmt2->execute();
							?>
							<script>
								$("#ch_audio_player").attr({"src": "../audio/ch/<?php echo $new_filename; ?>"});
								$("#ch_audio_file").html("<?php echo $new_filename; ?>");
							</script>
							<?php
						}
					}
				} else {
					echo "<div class=\"alert alert-danger\" role=\"alert\">The size of image you are attempting to upload is " . round( $_FILES["file"]["size"] / 1024, 2 ) . " KB, maximum size allowed is " . round( $max_size / 1024, 2 ) . " KB</div>";
				}
			} else {
				echo "<div class=\"alert alert-danger\" role=\"alert\">You can only upload image and audio files.</div>";
			}
		}
	}
}
?>
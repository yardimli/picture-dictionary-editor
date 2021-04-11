<?php
require_once( "./config/class.dictionary.php" );
$dictionary_list = new DICTIONARY();

header( "Content-type: application/json; charset=utf-8" );

try {
	$stmt = $dictionary_list->runQuery( "INSERT INTO report_log (student_id, student_name, lesson_type, word, language, is_correct, lesson_date) VALUES (:student_id, :student_name, :lesson_type, :word, :language, :is_correct, now())" );
	$stmt->bindparam( ":student_id", $_POST["student_id"] );
	$stmt->bindparam( ":student_name", $_POST["student_name"] );
	$stmt->bindparam( ":lesson_type", $_POST["lesson_type"] );
	$stmt->bindparam( ":word", $_POST["word"] );
	$stmt->bindparam( ":language", $_POST["language"] );
	$stmt->bindparam( ":is_correct", $_POST["is_correct"] );
//	$stmt->bindparam( ":lesson_date", "now()" );

//				$stmt->debugDumpParams();
	$stmt->execute();

	echo json_encode( array( "result" => true, "text" => $stmt ) );


} catch ( PDOException $e ) {
	echo json_encode( array( "result" => false, "text" => $e->getMessage() ) );
}


?>
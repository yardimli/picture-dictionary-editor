<?php
require_once( 'dbconfig.php' );

class STORY {
	private $conn;

	public function __construct() {
		$database   = new Database();
		$db         = $database->dbConnection();
		$this->conn = $db;
	}

	public function runQuery( $sql ) {
		$stmt = $this->conn->prepare( $sql );

		return $stmt;
	}

	public function add_story( $title, $story, $language, $hide_after_intro, $categoryID ) {

		try {
			$stmt = $this->conn->prepare( "INSERT INTO story (title, story, language, userid, hide_after_intro)
    		VALUES(:title, :story, :language, :userid, :hide_after_intro)" );
			$stmt->bindparam( ":title", $title );
			$stmt->bindparam( ":story", $story );
			$stmt->bindparam( ":language", $language );
			$stmt->bindparam( ":userid", $_SESSION['user_session'] );
			$stmt->bindparam( ":hide_after_intro", $hide_after_intro );
//				$stmt->debugDumpParams();
			$stmt->execute();

			$id = $this->conn->lastInsertId();
//			echo $id;

			foreach ( $categoryID as $key => $value ) {
//				echo $value . "<br />";
				try {
					$stmt = $this->conn->prepare( "INSERT INTO story_categories (story_id,cat_id)
    		VALUES(:story_id, :cat_id)" );
					$stmt->bindparam( ":story_id", $id );
					$stmt->bindparam( ":cat_id", $value );

//				$stmt->debugDumpParams();
					$stmt->execute();

//					return $stmt;
				} catch ( PDOException $e ) {
					echo $e->getMessage() . " (1-1)";
				}
			}

			return $stmt;
		} catch ( PDOException $e ) {
			echo $e->getMessage() . " (1)";
		}
	}

	public function update_story( $id, $title, $story, $language, $hide_after_intro, $categoryID ) {

		try {
			$stmt = $this->conn->prepare( "UPDATE story SET
				title=:title,
				story=:story,
				language=:language,
				hide_after_intro=:hide_after_intro
    			WHERE id=:id" );
			$stmt->bindparam( ":title", $title );
			$stmt->bindparam( ":story", $story );
			$stmt->bindparam( ":language", $language );
			$stmt->bindparam( ":hide_after_intro", $hide_after_intro );
			$stmt->bindparam( ":id", $id );

			$stmt->execute();

			try {
				$stmt = $this->conn->prepare( "DELETE FROM story_categories WHERE story_id=:story_id" );
				$stmt->bindparam( ":story_id", $id );
				$stmt->execute();
			} catch ( PDOException $e ) {
				echo $e->getMessage() . " (3-1)";
			}

			foreach ( $categoryID as $key => $value ) {
				try {
					$stmt = $this->conn->prepare( "INSERT INTO story_categories (story_id,cat_id)
    		VALUES(:story_id, :cat_id)" );
					$stmt->bindparam( ":story_id", $id );
					$stmt->bindparam( ":cat_id", $value );
					$stmt->execute();
				} catch ( PDOException $e ) {
					echo $e->getMessage() . " (3-2)";
				}
			}


			return $stmt;
		} catch ( PDOException $e ) {
			echo $e->getMessage() . " (3)";
		}
	}


	public function all_stories() {
		try {
			$stmt    = $this->conn->prepare( "SELECT * FROM story WHERE deleted=:val" );
			$del_val = 0;
			$stmt->bindparam( ":val", $del_val );
			$stmt->execute();
			$stories = [];
			while ( $p_story = $stmt->fetch() ) {
				array_push( $stories, [ "id"               => $p_story["id"],
				                        "title"            => $p_story["title"],
				                        "language"         => $p_story["language"],
				                        "hide_after_intro" => $p_story["hide_after_intro"]
				] );
			}
		} catch
		( PDOException $e ) {
			echo $e->getMessage();
		}

		return $stories;

	}


	//------------------------------------------------------------------------------------------------
	public function add_story_question( $story_id, $question, $show_answer_pictures, $random_answers_from_other_questions,$random_answers_from_same_question ) {

		try {
			$stmt = $this->conn->prepare( "INSERT INTO story_question (story_id, question, show_answer_pictures, random_answers_from_other_questions, random_answers_from_same_question, userid)
    		VALUES(:story_id, :question, :show_answer_pictures, :random_answers_from_other_questions, :random_answers_from_same_question, :userid)" );
			$stmt->bindparam( ":story_id", $story_id );
			$stmt->bindparam( ":question", $question );
			$stmt->bindparam( ":show_answer_pictures", $show_answer_pictures );
			$stmt->bindparam( ":random_answers_from_other_questions", $random_answers_from_other_questions );
			$stmt->bindparam( ":random_answers_from_same_question", $random_answers_from_same_question );
			$stmt->bindparam( ":userid", $_SESSION['user_session'] );
//			echo "<pre>";
//				$stmt->debugDumpParams();
//			echo "</pre>";
			$stmt->execute();

			$id = $this->conn->lastInsertId();
//			echo $id;


			return $stmt;
		} catch ( PDOException $e ) {
			echo $e->getMessage() . " (1)";
		}
	}

	public function update_story_question( $id, $story_id, $question, $show_answer_pictures, $random_answers_from_other_questions, $random_answers_from_same_question ) {

		try {
			$stmt = $this->conn->prepare( "UPDATE story_question SET story_id=:story_id, question=:question, show_answer_pictures=:show_answer_pictures, random_answers_from_other_questions=:random_answers_from_other_questions, random_answers_from_same_question=:random_answers_from_same_question WHERE id=:id" );
			$stmt->bindparam( ":story_id", $story_id );
			$stmt->bindparam( ":question", $question );
			$stmt->bindparam( ":show_answer_pictures", $show_answer_pictures );
			$stmt->bindparam( ":random_answers_from_other_questions", $random_answers_from_other_questions );
			$stmt->bindparam( ":random_answers_from_same_question", $random_answers_from_same_question );
			$stmt->bindparam( ":id", $id );
			$stmt->execute();

			return $stmt;
		} catch ( PDOException $e ) {
			echo $e->getMessage() . " (3)";
		}
	}


	//------------------------------------------------------------------------------------------------
	public function add_story_answer( $story_id, $question_id, $answer, $is_correct ) {

		try {
			$stmt = $this->conn->prepare( "INSERT INTO story_answer (story_id, question_id, answer, is_correct, userid)
    		VALUES(:story_id, :question_id, :answer, :is_correct, :userid)" );
			$stmt->bindparam( ":story_id", $story_id );
			$stmt->bindparam( ":question_id", $question_id );
			$stmt->bindparam( ":answer", $answer );
			$stmt->bindparam( ":is_correct", $is_correct );
			$stmt->bindparam( ":userid", $_SESSION['user_session'] );
//				$stmt->debugDumpParams();
			$stmt->execute();

			$id = $this->conn->lastInsertId();

//			echo $id;

			return $stmt;
		} catch ( PDOException $e ) {
			echo $e->getMessage() . " (1)";
		}
	}

	public function update_story_answer( $id, $story_id, $question_id, $answer, $is_correct ) {

		try {
			$stmt = $this->conn->prepare( "UPDATE story_answer SET story_id=:story_id, question_id=:question_id, answer=:answer, is_correct=:is_correct WHERE id=:id" );

			$stmt->bindparam( ":story_id", $story_id );
			$stmt->bindparam( ":question_id", $question_id );
			$stmt->bindparam( ":answer", $answer );
			$stmt->bindparam( ":is_correct", $is_correct );
			$stmt->bindparam( ":id", $id );
			$stmt->execute();

			return $stmt;
		} catch ( PDOException $e ) {
			echo $e->getMessage() . " (3)";
		}
	}










	//------------------------------------------------------------------------------------------------
	public function delete_story( $id ) {
		try {
			$stmt = $this->conn->prepare( "UPDATE story SET deleted=1 WHERE id=:id AND userid=:userid" );
			$stmt->execute( array( ":id" => $id, ":userid" => $_SESSION['user_session'] ) );

			return $stmt;
		} catch ( PDOException $e ) {
			echo $e->getMessage();
		}
	}


	public function delete_question( $id ) {
		try {
			$stmt = $this->conn->prepare( "UPDATE story_question SET deleted=1 WHERE id=:id AND userid=:userid" );
			$stmt->execute( array( ":id" => $id, ":userid" => $_SESSION['user_session'] ) );

			return $stmt;
		} catch ( PDOException $e ) {
			echo $e->getMessage();
		}
	}

	public function delete_answer( $id ) {
		try {
			$stmt = $this->conn->prepare( "UPDATE story_answer SET deleted=1 WHERE id=:id AND userid=:userid" );
			$stmt->execute( array( ":id" => $id, ":userid" => $_SESSION['user_session'] ) );

			return $stmt;
		} catch ( PDOException $e ) {
			echo $e->getMessage();
		}
	}

	public function redirect( $url ) {
		header( "Location: $url" );
	}
}

?>
<?php
require_once( 'dbconfig.php' );

class DICTIONARY {
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

	public function add_word_en( $Word_EN, $categoryID, $level, $word_sound ) {

		try {
			$stmt = $this->conn->prepare( "INSERT INTO dictionary (word_EN, level, word_sound, userid)
    		VALUES(:word_EN, :level, :word_sound, :userid)" );
			$stmt->bindparam( ":word_EN", $Word_EN );
//			$stmt->bindparam( ":categoryID", $categoryID );
			$stmt->bindparam( ":level", $level );
			$stmt->bindparam( ":word_sound", $word_sound );
			$stmt->bindparam( ":userid", $_SESSION['user_session'] );

//				$stmt->debugDumpParams();
			$stmt->execute();

			$id = $this->conn->lastInsertId();
//			echo $id;

			foreach ( $categoryID as $key => $value ) {
//				echo $value . "<br />";
				try {
					$stmt = $this->conn->prepare( "INSERT INTO word_categories (word_id,cat_id)
    		VALUES(:word_id, :cat_id)" );
					$stmt->bindparam( ":word_id", $id );
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


	public function check_word_sound( $word_id, $word_sound ) {

		$WordFound = false;
		try {
			$stmt = $this->conn->prepare( "SELECT * FROM dictionary WHERE word_sound=:word_sound" );
			$stmt->bindparam( ":word_sound", $word_sound );
			$stmt->execute();
			while ( $row = $stmt->fetch( PDO::FETCH_ASSOC ) ) {
				if ($row["id"] !== $word_id) {
					$WordFound = true;
				}
			}

		} catch ( PDOException $e ) {
			echo $e->getMessage() . " (2)";
		}

		return $WordFound;
	}

	public function add_word_original( $Word_EN, $Word_TR, $Word_CH, $bopomofo, $categoryID, $level, $word_sound ) {

		$WordFound = false;
		try {
			$stmt = $this->conn->prepare( "SELECT * FROM dictionary WHERE word_EN=:word_EN" );
			$stmt->bindparam( ":word_EN", $Word_EN );
			$stmt->execute();
			if ( $p_category = $stmt->fetch() ) {
				$WordFound = true;
			}
		} catch ( PDOException $e ) {
			echo $e->getMessage() . " (2)";
		}


//		if ( $WordFound ) {
//			echo "Word Already Exists.";
//		} else {

		if ( $bopomofo === "" ) {

			$zh = new \DictPedia\ZhuyinPinyin();

			$stmt = $this->conn->prepare( "SELECT * FROM cedict WHERE traditional=:word_CH" );
			$stmt->bindparam( ":word_CH", $Word_CH );
			$stmt->execute();
			if ( $p_word = $stmt->fetch() ) {

				$bopomofo  = strtolower( $p_word["pinyin_numbers"] );
				$bopomofo2 = explode( " ", $bopomofo );
				$bopomofo3 = "";
				for ( $i = 0; $i < count( $bopomofo2 ); $i ++ ) {
					$bopomofo3 .= $zh->encodeZhuyin( $bopomofo2[ $i ] ) . " ";
				}
				$bopomofo3 = trim( $bopomofo3 );
				$bopomofo3 = str_replace( " ", ":", $bopomofo3 );
				$bopomofo  = $bopomofo3;
			}

		}

		try {
			$stmt = $this->conn->prepare( "INSERT INTO dictionary (word_EN, word_TR, word_CH, bopomofo, categoryID, level, word_sound)
    		VALUES(:word_EN, :word_TR, :word_CH, :bopomofo, :categoryID, :level, :word_sound)" );
			$stmt->bindparam( ":word_EN", $Word_EN );
			$stmt->bindparam( ":word_TR", $Word_TR );
			$stmt->bindparam( ":word_CH", $Word_CH );
			$stmt->bindparam( ":bopomofo", $bopomofo );
//				$stmt->bindparam( ":picture", $picture );
			$stmt->bindparam( ":categoryID", $categoryID );
			$stmt->bindparam( ":level", $level );
			$stmt->bindparam( ":word_sound", $word_sound );
//				$stmt->bindparam( ":audio_EN", $audio_en );
//				$stmt->bindparam( ":audio_TR", $audio_tr );
//				$stmt->bindparam( ":audio_CH", $audio_ch );

//				$stmt->debugDumpParams();
			$stmt->execute();


			return $stmt;
		} catch ( PDOException $e ) {
			echo $e->getMessage() . " (1)";
		}
//		}
	}


	public function update_word_en( $id, $Word_EN, $categoryID, $level, $word_sound ) {
		try {
			$stmt = $this->conn->prepare( "UPDATE dictionary SET
				word_EN=:Word_EN,
				level=:level,
				word_sound=:word_sound,
				update_time = now()
    			WHERE id=:id" );
			$stmt->bindparam( ":Word_EN", $Word_EN );
			$stmt->bindparam( ":id", $id );
			$stmt->bindparam( ":level", $level );
			$stmt->bindparam( ":word_sound", $word_sound );

			$stmt->execute();

			try {
				$stmt = $this->conn->prepare( "DELETE FROM word_categories WHERE word_id=:word_id" );
				$stmt->bindparam( ":word_id", $id );
				$stmt->execute();
			} catch ( PDOException $e ) {
				echo $e->getMessage() . " (3-1)";
			}

			foreach ( $categoryID as $key => $value ) {
				try {
					$stmt = $this->conn->prepare( "INSERT INTO word_categories (word_id,cat_id)
    		VALUES(:word_id, :cat_id)" );
					$stmt->bindparam( ":word_id", $id );
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

	public function update_word_tr( $id, $Word_TR ) {

		try {
			$stmt = $this->conn->prepare( "UPDATE dictionary SET
				word_TR=:Word_TR,
				update_time = now()
    			WHERE id=:id" );
			$stmt->bindparam( ":Word_TR", $Word_TR );
			$stmt->bindparam( ":id", $id );

			$stmt->execute();

			return $stmt;
		} catch ( PDOException $e ) {
			echo $e->getMessage() . " (3)";
		}
	}

	public function update_word_ch( $id, $Word_CH, $bopomofo ) {

		if ( $bopomofo === "" ) {

			$zh = new \DictPedia\ZhuyinPinyin();

			$stmt = $this->conn->prepare( "SELECT * FROM cedict WHERE traditional=:word_CH" );
			$stmt->bindparam( ":word_CH", $Word_CH );
			$stmt->execute();
			if ( $p_word = $stmt->fetch() ) {
				$bopomofo  = strtolower( $p_word["pinyin_numbers"] );
				$bopomofo2 = explode( " ", $bopomofo );
				$bopomofo3 = "";
				for ( $i = 0; $i < count( $bopomofo2 ); $i ++ ) {
					$bopomofo3 .= $zh->encodeZhuyin( $bopomofo2[ $i ] ) . " ";
				}
				$bopomofo3 = trim( $bopomofo3 );
				$bopomofo3 = str_replace( " ", ":", $bopomofo3 );
				$bopomofo  = $bopomofo3;
			}
		}

		try {
			$stmt = $this->conn->prepare( "UPDATE dictionary SET
				word_CH=:Word_CH,
				bopomofo=:bopomofo,
				update_time = now()
    			WHERE id=:id" );
			$stmt->bindparam( ":Word_CH", $Word_CH );
			$stmt->bindparam( ":bopomofo", $bopomofo );
			$stmt->bindparam( ":id", $id );

			$stmt->execute();

			return $stmt;
		} catch ( PDOException $e ) {
			echo $e->getMessage() . " (3)";
		}
	}


	public function update_dictionary( $Word_EN, $Word_TR, $Word_CH, $bopomofo, $picture, $categoryID, $id, $level, $audio_en, $audio_tr, $audio_ch ) {

		if ( $bopomofo === "" ) {

			$zh = new \DictPedia\ZhuyinPinyin();

			$stmt = $this->conn->prepare( "SELECT * FROM cedict WHERE traditional=:word_CH" );
			$stmt->bindparam( ":word_CH", $Word_CH );
			$stmt->execute();
			if ( $p_word = $stmt->fetch() ) {
				$bopomofo  = strtolower( $p_word["pinyin_numbers"] );
				$bopomofo2 = explode( " ", $bopomofo );
				$bopomofo3 = "";
				for ( $i = 0; $i < count( $bopomofo2 ); $i ++ ) {
					$bopomofo3 .= $zh->encodeZhuyin( $bopomofo2[ $i ] ) . " ";
				}
				$bopomofo3 = trim( $bopomofo3 );
				$bopomofo3 = str_replace( " ", ":", $bopomofo3 );
				$bopomofo  = $bopomofo3;
			}
		}

		try {
			$stmt = $this->conn->prepare( "UPDATE dictionary SET
				word_EN=:Word_EN,
				word_TR=:Word_TR,
				word_CH=:Word_CH,
				bopomofo=:bopomofo,
				picture=:picture,
				categoryID=:categoryID,
				audio_EN=:audio_EN,
				audio_TR=:audio_TR,
				audio_CH=:audio_CH,
				level=:level
    			WHERE id=:id" );
			$stmt->bindparam( ":Word_EN", $Word_EN );
			$stmt->bindparam( ":Word_TR", $Word_TR );
			$stmt->bindparam( ":Word_CH", $Word_CH );
			$stmt->bindparam( ":bopomofo", $bopomofo );
			$stmt->bindparam( ":picture", $picture );
			$stmt->bindparam( ":categoryID", $categoryID );
			$stmt->bindparam( ":id", $id );
			$stmt->bindparam( ":level", $level );
			$stmt->bindparam( ":audio_EN", $audio_en );
			$stmt->bindparam( ":audio_TR", $audio_tr );
			$stmt->bindparam( ":audio_CH", $audio_ch );

			$stmt->execute();

			return $stmt;
		} catch ( PDOException $e ) {
			echo $e->getMessage() . " (3)";
		}
	}

	public function delete_word( $id ) {
		try {
			$stmt = $this->conn->prepare( "UPDATE dictionary SET deleted=:val WHERE id=:id AND userid=:userid" );
			$stmt->execute( array( ":val" => 1, ":id" => $id, ":userid" => $_SESSION['user_session'] ) );

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
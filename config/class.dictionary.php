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

	public function add_word( $Word_EN, $Word_TR, $Word_CH, $bopomofo, $picture, $categoryID, $level, $audio_en, $audio_tr, $audio_ch ) {

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


		if ( $WordFound ) {
			echo "Word Already Exists.";
		} else {

			if ($bopomofo==="") {

				$zh = new \DictPedia\ZhuyinPinyin();

				$stmt = $this->conn->prepare( "SELECT * FROM cedict WHERE traditional=:word_CH" );
				$stmt->bindparam( ":word_CH", $Word_CH );
				$stmt->execute();
				if ( $p_word = $stmt->fetch() ) {

					$bopomofo = strtolower( $p_word["pinyin_numbers"]);
					$bopomofo2 = explode(" ",$bopomofo);
					$bopomofo3 = "";
					for ($i=0; $i<count($bopomofo2); $i++) {
						$bopomofo3 .= $zh->encodeZhuyin($bopomofo2[$i])." ";
					}
					$bopomofo = $bopomofo3;
				}

			}

			try {
				$stmt = $this->conn->prepare( "INSERT INTO dictionary (word_EN, word_TR, word_CH, bopomofo, picture, categoryID, level, audio_EN, audio_TR, audio_CH)
    		VALUES(:word_EN, :word_TR, :word_CH, :bopomofo, :picture, :categoryID, :level, :audio_EN, :audio_TR, :audio_CH)" );
				$stmt->bindparam( ":word_EN", $Word_EN );
				$stmt->bindparam( ":word_TR", $Word_TR );
				$stmt->bindparam( ":word_CH", $Word_CH );
				$stmt->bindparam( ":bopomofo", $bopomofo );
				$stmt->bindparam( ":picture", $picture );
				$stmt->bindparam( ":categoryID", $categoryID );
				$stmt->bindparam( ":level", $level );
				$stmt->bindparam( ":audio_EN", $audio_en );
				$stmt->bindparam( ":audio_TR", $audio_tr );
				$stmt->bindparam( ":audio_CH", $audio_ch );

//				$stmt->debugDumpParams();
				$stmt->execute();


				return $stmt;
			} catch ( PDOException $e ) {
				echo $e->getMessage() . " (1)";
			}
		}
	}

	public function update_dictionary( $Word_EN, $Word_TR, $Word_CH, $bopomofo, $picture, $categoryID, $id, $level, $audio_en, $audio_tr, $audio_ch ) {

		if ($bopomofo==="") {

			$zh = new \DictPedia\ZhuyinPinyin();

			$stmt = $this->conn->prepare( "SELECT * FROM cedict WHERE traditional=:word_CH" );
			$stmt->bindparam( ":word_CH", $Word_CH );
			$stmt->execute();
			if ( $p_word = $stmt->fetch() ) {
				$bopomofo = strtolower( $p_word["pinyin_numbers"]);
				$bopomofo2 = explode(" ",$bopomofo);
				$bopomofo3 = "";
				for ($i=0; $i<count($bopomofo2); $i++) {
					$bopomofo3 .= $zh->encodeZhuyin($bopomofo2[$i])." ";
				}
				$bopomofo = $bopomofo3;
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
			$stmt = $this->conn->prepare( "UPDATE dictionary SET deleted=:val WHERE id=:id" );
			$stmt->execute( array( ":val" => 1, ":id" => $id ) );

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
<?php
require_once( 'dbconfig.php' );

class CATEGORY {
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

	public function category_full_path_string( $id ) {

		$parents = [];
		$this->get_parent_path( $id, $parents );
		$result = "";
		for ( $i = 0; $i < count( $parents ); $i ++ ) {
			if ( $i === 0 ) {
				$result = "<a href='?catid=".$parents[ $i ]["id"]."'>" . $parents[ $i ]["name"] . "</a>";
			} else {

				if ($parents[ $i ]["parentID"]!=="0") {
					$result = "<a href='?catid=".$parents[ $i ]["id"]."'>" . $parents[ $i ]["name"] . "</a>" . " - ".$result;
				} else
				{
					$result = "<a href='?catid=0'>" . $parents[ $i ]["name"] . "</a>" . " - ".$result;
				}
			}
		}
//		print_r( $parents );

		return $result;
	}

	public function get_parent_path( $parent_id, &$parents ) {
		try {
			$stmt = $this->conn->prepare( "SELECT * FROM category WHERE ID=:ID" );
			$stmt->bindparam( ":ID", $parent_id );
			$stmt->execute();
			$p_category = $stmt->fetch();
			if ( $p_category ) {
				array_push( $parents, [ "id" => $p_category["id"], "parentID" => $p_category["parentID"], "name" => $p_category["category_EN"] ] );
				if ( $p_category["parentID"] !== "0" ) {
					$this->get_parent_path( $p_category["parentID"], $parents );
				}
			}
		} catch ( PDOException $e ) {
			echo $e->getMessage();
		}
	}


	public function all_categories( $parent_id ) {
		try {
			$stmt = $this->conn->prepare( "SELECT * FROM category WHERE parentID=:parentID" );
			$stmt->bindparam( ":parentID", $parent_id );
			$stmt->execute();
			$children = [];
			while ( $p_category = $stmt->fetch() ) {
				array_push( $children, [ "id" => $p_category["id"], "name" => $p_category["category_EN"], "parentID" => $p_category["parentID"], "children" => $this->all_categories( $p_category["id"] ) ] );
			}
		} catch
		( PDOException $e ) {
			echo $e->getMessage();
		}

		return $children;

	}

	public function add_category( $category_EN, $category_TR, $category_CH, $picture, $parent_EN, $parent_id ) {

		$CategoryFound = false;

		try {
      $stmt = $this->conn->prepare( "SELECT * FROM category WHERE category_EN=:category_EN AND parentID=:parent_id" );
      $stmt->bindparam( ":category_EN", $category_EN );
      $stmt->bindparam( ":parent_id", $parent_id );
      $stmt->execute();
			if ( $p_category = $stmt->fetch() ) {
				$CategoryFound = true;
			}
		} catch ( PDOException $e ) {
			echo $e->getMessage();
		}

		if ( $CategoryFound ) {
			echo "Category already exists.";
		} else {

			if ( $parent_id === - 1 ) {
				try {
					$stmt = $this->conn->prepare( "SELECT * FROM category WHERE category_EN=:parent_EN" );
					$stmt->bindparam( ":parent_EN", $parent_EN );
					$stmt->execute();
					if ( $p_category = $stmt->fetch() ) {
						$parent_id = $p_category["id"];
					}
				} catch ( PDOException $e ) {
					echo $e->getMessage();
				}
			}


			try {
				$stmt = $this->conn->prepare( "INSERT INTO category (category_EN, category_TR, category_CH,picture,parent_EN,parentID)
    		VALUES(:category_EN, :category_TR, :category_CH, :picture, :parent_EN, :parentID)" );
				$stmt->bindparam( ":category_EN", $category_EN );
				$stmt->bindparam( ":category_TR", $category_TR );
				$stmt->bindparam( ":category_CH", $category_CH );
				$stmt->bindparam( ":picture", $picture );
				$stmt->bindparam( ":parent_EN", $parent_EN );
				$stmt->bindparam( ":parentID", $parent_id );
				$stmt->execute();

				return $stmt;
			} catch
			( PDOException $e ) {
				echo $e->getMessage();
			}
		}
	}

	public function update_category( $category_EN, $category_TR, $category_CH, $picture, $parent_EN, $parent_id, $id ) {
		$CategoryFound = false;

		try {
			$stmt = $this->conn->prepare( "SELECT * FROM category WHERE category_EN=:category_EN AND parentID=:parent_id" );
			$stmt->bindparam( ":category_EN", $category_EN );
      $stmt->bindparam( ":parent_id", $parent_id );
			$stmt->execute();
			if ( $p_category = $stmt->fetch() ) {
				$CategoryFound = true;
			}
		} catch ( PDOException $e ) {
			echo $e->getMessage();
		}

		if ( $CategoryFound ) {
			echo "Category already exists.";
		} else {
			try {
				$stmt = $this->conn->prepare( "UPDATE category SET
				category_EN=:category_EN,
				category_TR=:category_TR,
				category_CH=:category_CH,
				parent_EN=:parent_EN,
				picture=:picture,
				parentID=:parent_id
    			WHERE id=:id" );
				$stmt->bindparam( ":category_EN", $category_EN );
				$stmt->bindparam( ":category_TR", $category_TR );
				$stmt->bindparam( ":category_CH", $category_CH );
				$stmt->bindparam( ":picture", $picture );
				$stmt->bindparam( ":parent_EN", $parent_EN );
				$stmt->bindparam( ":parent_id", $parent_id );
				$stmt->bindparam( ":id", $id );
				$stmt->execute();

				return $stmt;
			} catch ( PDOException $e ) {
				echo $e->getMessage();
			}
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
<?php

class Database {
	private $host = "phpworkspace-ssl-mysql";
	private $db_name = "dictionary_2";
	private $username = "root";
	private $password = "A123456b";

//	private $host = "localhost";
//	private $db_name = "dictionary_2";
//	private $username = "root";
//	private $password = "BKZ!A123456b!";

	public $conn;

	public function dbConnection() {
		$this->conn = null;
		try {
			$this->conn = new PDO( "mysql:host=" . $this->host . ";dbname=" . $this->db_name .";charset=utf8", $this->username, $this->password );
			$this->conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
		} catch ( PDOException $exception ) {
			echo "Connection error: " . $exception->getMessage();
		}

		return $this->conn;
	}
}

$thisFile = str_replace( '\\', '/', __FILE__ );
$srvRoot  = str_replace( 'config/dbconfig.php', '', $thisFile );
$webRoot  = '/picture-dictionary-editor/';
$srvpath  = '/picture-dictionary-editor/';

define( 'WEB_ROOT', $webRoot );
define( 'SRV_ROOT', $srvRoot );
define( 'SRV_PATH', $srvpath );
?>

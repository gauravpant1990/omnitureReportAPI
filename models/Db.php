<?php
class Db
{
	protected $servername = "localhost";
	protected $username = "root";
	protected $password = "";
	protected $database = "keyword_metrics";
	public $conn;

	public function __construct()
	{
		$this->conn = new mysqli($this->servername, $this->username, $this->password, $this->database);
	}

	public function checkConnection()
	{
		if ($this->conn->connect_error) {
			die("Connection failed: " . $this->conn->connect_error);
		}
		echo "<br>Connected successfully!!<br>";
	}
}
/*$db = new Db;
$db->checkConnection();*/
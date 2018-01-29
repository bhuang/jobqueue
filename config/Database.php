<?php
class Database{
 
    // specify your own database credentials
    private $host = "127.0.0.1";
    private $db_name = "massdrop";
    private $username = "root";
    private $password = "password";
    public $conn;
 
    // get the database connection
    public function getConnection(){
 
        $this->conn = null;
 
        try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->exec("set names utf8");
			//error_log("got connect");
        }catch(PDOException $exception){
            error_log("Connection error: " . $exception->getMessage());
        }
 
        return $this->conn;
    }
}
?>
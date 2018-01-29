<?php
	
class Job {
	
	const QUEUED_STATE = "queued";
	const FINISHED_STATE = "finished";
	const FAILED_STATE = "failed";
	const ERROR_STATE = "error";
	
	public static $table_name ="Jobs";
	
	public $id;
	public $url;
	public $state;
	public $result;
	public $createdAt;
	
	public function create() {
		//creating the job as well ast he queuedjob here
		if(isset($this->id)) {
			return false;
		}
		
		// sanitize
		$this->url = htmlspecialchars($this->url, ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8');
		$this->url = strpos($this->url, 'http') !== 0 ? "http://$this->url" : $this->url;
		//make sure it's a valid url
		if(!filter_var($this->url, FILTER_VALIDATE_URL)) {
			return false;
		}
		
		//prepare
		try {
			$query = "INSERT INTO " . Job::$table_name . " SET url=:url";
			$db = new Database();
			$db->getConnection();
		
			//starting transaction
			$db->conn->beginTransaction();
		
			$stmt1 = $db->conn->prepare($query);
		
			// bind values
			$stmt1->bindParam(":url", $this->url);
		
			$stmt1->execute();
			$lastId = $db->conn->lastInsertId();
			
			$query = "INSERT INTO " . QueuedJob::$table_name . " SET jobId=:jobId";
			$stmt2 = $db->conn->prepare($query);
			// bind values
			$stmt2->bindParam(":jobId", $lastId);
			$stmt2->execute();
		
			$db->conn->commit();
			$this->id = $lastId;
			return $lastId;
		} catch (Exception $e){
    		$cxn->rollback();
    		error_log("an error has occurred");
			return false;
  	  	}
	}
	
	public function load() {
		$query = "SELECT * FROM " . Job::$table_name  . " WHERE id=:id LIMIT 1;";
		
		$db = new Database();
		$db->getConnection();
		$stmt = $db->conn->prepare( $query );
		//bind
		$stmt->bindParam(':id', $this->id);
	    // execute query
	    if($stmt->execute() && $stmt->rowCount()) {

		    // get retrieved row
		    $row = $stmt->fetch(PDO::FETCH_ASSOC);
		
		    // set values to object properties
		    $this->url = $row['url'];
		    $this->state = $row['state'];
		    $this->result = $row['result'];
			$this->createdAt = $row['createdAt'];
			return true;
		} else {
			return false;
		}
		
	}
	
	public function update() {
		$query = "UPDATE
		                " . Job::$table_name  . "
		            SET
		                url = :url,
		                state = :state,
		                result = :result
		            WHERE
		                id = :id";
 
		    // prepare query statement
			$db = new Database();
			$db->getConnection();
			$stmt = $db->conn->prepare( $query );
 
		    // bind new values
		    $stmt->bindParam(':url', $this->url);
		    $stmt->bindParam(':state', $this->state);
		    $stmt->bindParam(':result', $this->result);
		    $stmt->bindParam(':id', $this->id);
 
		    // execute the query
		    if($stmt->execute()){
		        return true;
		    }
 
		    return false;
	}
	
	public function process() {
		$result = $this->getUrlResult();
		$result = htmlspecialchars($result, ENT_QUOTES | ENT_SUBSTITUTE, 'utf-8');
		//if there is no results, than we put it in an error state
		if($result) {
			$this->result = $result;
			$this->state = Job::FINISHED_STATE;
		} else {
			$this->state = Job::ERROR_STATE;
		}
		return $this->update();
	}
	
	public function getUrlResult() {
		// create curl resource 
	    $ch = curl_init(); 

        // set url 
        curl_setopt($ch, CURLOPT_URL, $this->url); 

        //return the transfer as a string 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 

        // $output contains the output string 
        $output = curl_exec($ch); 

        // close curl resource to free up system resources 
        curl_close($ch);  
		return $output;
	}
}
	
?>
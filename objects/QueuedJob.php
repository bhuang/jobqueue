<?php
	
class QueuedJob {
	
	const RETRY_TIME_LIMIT = 120;//2 mins
	const RETRY_LIMIT= 3;
	
	public static $table_name ="QueuedJobs";
	
	public $jobId;
	public $tries;
	public $lastAttempt;
	public $createdAt;
	
	public function load() {
		$query = "SELECT * FROM " . QueuedJob::$table_name  . " WHERE jobId=:jobId LIMIT 1;";
		
		$db = new Database();
		$db->getConnection();
		$stmt = $db->conn->prepare( $query );
		//bind
		$stmt->bindParam(':jobId', $this->jobId);
	    // execute query
	    if($stmt->execute() && $stmt->rowCount()) {

		    // get retrieved row
		    $row = $stmt->fetch(PDO::FETCH_ASSOC);
		
		    // set values to object properties
		    $this->tries = $row['tries'];
			$this->lastAttempt = $row['lastAttempt'];
			$this->createdAt = $row['createdAt'];
			return true;
		} else {
			return false;
		}
		
	}
	
	public static function getNextJob() {
		//query gets jobs that either have not been attempted yet or has been attempted but not finished within time limit
		$query = "SELECT * FROM " . QueuedJob::$table_name . " WHERE lastAttempt IS NULL OR (NOW()-lastAttempt)> ".QueuedJob::RETRY_TIME_LIMIT." order by createdAt ASC LIMIT 1";
		
		$db = new Database();
		$db->getConnection();
		$stmt = $db->conn->prepare( $query );
	    // execute query
	    if($stmt->execute() && $stmt->rowCount()) {

		    // get retrieved row
		    $row = $stmt->fetch(PDO::FETCH_ASSOC);

		    // set values to object properties
			$nextQueuedJob = new QueuedJob();
		    $nextQueuedJob->jobId = $row['jobId'];
		    $nextQueuedJob->tries = $row['tries'];
		    $nextQueuedJob->lastAttempt = $row['lastAttempt'];
		    $nextQueuedJob->createdAt = $row['createdAt'];
		
			//if a job has been attempted multiple ties without success and has crossed the retry limit, we flag it as failed so we stop trying to process it 
			if($nextQueuedJob->tries>=QueuedJob::RETRY_LIMIT) {
				error_log("removing job " . $nextQueuedJob->jobId  . " because there were too many failures");
				$failedJob = new Job();
				$failedJob->id = $nextQueuedJob->jobId;
				if($failedJob->load()) {
					$failedJob->state = Job::FAILED_STATE;
					$failedJob->update();
				}
				//removing failed job from queue
				$nextQueuedJob->delete();
				return QueuedJob::getNextJob();
			} else {
				return $nextQueuedJob;
			}
		} else {
			return false;
		}
	}
	
	public function delete() {
		$query = "DELETE FROM ".QueuedJob::$table_name." WHERE jobId=:jobId";
		$db = new Database();
		$db->getConnection();
		$stmt = $db->conn->prepare( $query );
		//bind
		$stmt->bindParam(':jobId', $this->jobId);
	    // execute query
	    if($stmt->execute()) {
	    	return true;
	    } else {
	    	return false;
	    }
	}
	
	public function update() {
		$query = "UPDATE
		                " . QueuedJob::$table_name  . "
		            SET
		                tries = :tries,
		                lastAttempt = :lastAttempt
		            WHERE
		                jobId = :jobId";
 
		    // prepare query statement
			$db = new Database();
			$db->getConnection();
			$stmt = $db->conn->prepare( $query );
 
		    // bind new values
		    $stmt->bindParam(':tries', $this->tries);
		    $stmt->bindParam(':lastAttempt', $this->lastAttempt);
		    $stmt->bindParam(':jobId', $this->jobId);
 
		    // execute the query
		    if($stmt->execute()){
		        return true;
		    }
 
		    return false;
	}
}
	
?>
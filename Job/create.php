<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
 
// include database and object files
include_once '../config/Database.php';
include_once '../objects/Job.php';
include_once '../objects/QueuedJob.php';
if(isset($_POST["url"])) {
	$job = new Job();
	$job->url= $_POST["url"];
	$jobId = $job->create();
	if($jobId) {
		$result = array();
		$result["jobId"] = $jobId;
		echo(json_encode($result));
	} else {
		echo "{}";
	}
} else {
	echo "{}";
}

?>
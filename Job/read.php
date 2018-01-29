<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');
 
include_once '../config/Database.php';
include_once '../objects/Job.php';
include_once '../objects/QueuedJob.php';

$job = new Job();
$job->id = $_GET["id"];

if($job->load()) {
	$data = array();
	$data["id"] = $job->id;
	$data["url"] = $job->url;
	$data["state"] = $job->state;
	$data["createdAt"] = $job->createdAt;
	if($job->state==Job::FINISHED_STATE) {
		$data["result"] = $job->result;
	}

	echo(json_encode($data));
} else {
	echo("{}");
}
?>
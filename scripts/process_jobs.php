<?php
	
include_once '../config/Database.php';
include_once '../objects/Job.php';
include_once '../objects/QueuedJob.php';
date_default_timezone_set('America/Los_Angeles');

//prevent script from running multiple times
$running = exec("ps aux|grep ". basename(__FILE__) ."|grep -v grep|wc -l");
if($running > 1) {
	echo("Already processing job queue! " . $running);
	echo(shell_exec("ps aux|grep ". basename(__FILE__) ."|grep -v grep"));
	exit();
}

$nextQueuedJob = QueuedJob::getNextJob();
while($nextQueuedJob) {
	$nextQueuedJob->tries+=1;
	$nextQueuedJob->lastAttempt= date('Y-m-d H:i:s');
	$nextQueuedJob->update();
	$nextJob = new Job();
	$nextJob->id = $nextQueuedJob->jobId;
	if($nextJob->load()) {
		echo("processing url " . $nextJob->url . "\n");
		if($nextJob->process()) {
			echo($nextJob->url . " competed!\n");
			$nextQueuedJob->delete();
		}
	}
	$nextQueuedJob = QueuedJob::getNextJob();
}

echo("Done processing jobs!\n");
?>
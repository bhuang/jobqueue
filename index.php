<!doctype html>
<html lang="en">
	<head>
    	<meta charset="utf-8">
    	<title>Jobs</title>
    	<script src="https://code.jquery.com/jquery-1.10.2.js"></script>
  </head>
  <style>
	  table {
	      border-collapse: collapse;
	  }

	  table, th, td {
	      border: 1px solid black;
	  }
	  
	  #submit_url {
	  	height:50px;
	  }
  </style>
<body>
 
	<div id="submit_url">
		<div>
			<form id="submit_url_form" action="./Job/create.php">
			  Submit Url:
			  <input id="submit_url_form_url" type="text" name="url">
			  <input id="submit_url_form_submit" type="submit" value="Submit">
			</form>
		</div>
		<div id="submit_url_result">
	
		</div>
	</div>

	<div id="job_status">
		<div>
			<form id="job_status_form" action="./Job/read.php">
			  Job Id:
			  <input id="job_status_form_job_id" type="text" name="id">
			  <input type="submit" value="Submit">
			</form>
		</div>
		<div id="job_status_result">
	
		</div>
	</div>
<script src="./js/index.js"></script>
</body>
</html>
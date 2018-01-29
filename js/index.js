
  $("#submit_url_form").submit(function( event ) {
	  var data = { url: $('#submit_url_form_url').val() };
	  var posting = $.post( './Job/create.php', data );
      posting.done(function( data ) {
		  if(data.jobId) {
			  $("#submit_url_result").text("Job Id: " + data.jobId);
	  	  } else {
	  	  		$("#submit_url_result").text("Invalid Url");
	  	  }
		  $('#submit_url_form_url').val("");
      });
	
  	event.preventDefault();
});

$("#job_status_form").submit(function( event ) {
	var data = { id: $('#job_status_form_job_id').val() };
	var getting = $.get( './Job/read.php', data );

	      /* Alerts the results */
	      getting.done(function( data ) {
			  if(data.id) {
				  $('#job_status_form_job_id').val("");
				  var result = "<table>";
				  result += "<tr><td>ID: </td><td>"+data.id+"</td></tr>";
				  result += "<tr><td>State: </td><td>"+data.state+"</td></tr>";
				  if(data.result) {
				  	result += "<tr><td>Result: </td><td>"+data.result+"</td></tr>";
			  	  }
				  result += "<table>";
				  $("#job_status_result").html(result);
		  	} else {
		  		$("#job_status_result").text("No such job!");
		  	}
	      });
	
  	event.preventDefault();
});
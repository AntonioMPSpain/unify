<!DOCTYPE html> 
<html lang="en"> 
<head> 
<meta charset="utf-8"> 
<title>jQuery AJAX form submit using twitter bootstrap modal</title> 
<script src="modal/jquery.min.js"></script>
<link href="modal/bootstrap-combined.min.css" rel="stylesheet"> 
<script src="modal/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
<h4>Crop</h4>
	<!-- twitter content -->
	<div id="form-content" class="modal hide fade in" style="display: none; ">
		<div class="modal-header">
			  <a class="close" data-dismiss="modal">×</a>
			  <h3>Contact us</h3>
		</div>
		<div><div class="clearfix"></div>
			<? include("index.php");?>
		</div>
	     <div class="modal-footer">
	         <button class="btn btn-success" id="submit">submit</button>
	         <a href="#" class="btn" data-dismiss="modal">Close</a>
  		</div>
	</div>

<div id="thanks"><p><a data-toggle="modal" href="#form-content" class="btn btn-primary">Imagen</a></p></div>
 </div>
<script>
 $(function() {
//twitter bootstrap script
	$("button#submit").click(function(){
		   	$.ajax({
    		   	type: "POST",
						resizable: true,
						
			url: "modal/process.php",
			data: $('form.contact').serialize(),
						success: function(msg){
						  $("#thanks").html(msg)
						$("#form-content").modal('hide');	
 		        	},
			error: function(){
				alert("failure");
				}
      			});
	});
});
</script>
</body>
</html>

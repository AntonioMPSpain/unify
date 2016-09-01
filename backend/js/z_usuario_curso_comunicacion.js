jQuery(function($) {

    $('#textoSMS').on('keyup', function() {
        var max = 140;
    	  var len = $(this).val().length;

        var ch = max - len;

	  if (ch < 0){
		$('#enviarSMS').hide();
	  }
	  else{
		$('#enviarSMS').show();
	  }
		
        $('#characterLeft').text(ch + ' caracteres restantes');
        
    });
});


function selectAll(id){
	
	if ($("#chck_"+id).is(':checked')){
		
		$("#table_"+id).find(':checkbox').each(function(){

			jQuery(this).attr('checked',true);
		});
		
	}
	else{
		 $("#table_"+id).find(':checkbox').each(function(){
	
			jQuery(this).attr('checked',false);

		});
		 
	}
    
}

jQuery(function($) {

    $('#select_modalidad').on('change', function() {
       if (this.value==0){
			$(".modo0").show(500);	
			$(".modo1").hide();	
			$(".modo3").hide();	  
			$("#fechascurso").show(500);
			document.getElementById("plazopermanente").required = false;
	   }
               
		if (this.value==1){
			$(".modo0").hide();	
			$(".modo1").show(500);	
			$(".modo3").hide();	  
			$("#fechascurso").show(500);
			document.getElementById("plazopermanente").required = false;
	   }       
	   
	   if (this.value==2){
			$(".modo0").show(500);	
			$(".modo1").show(500);	
			$(".modo3").hide();	   
			$("#fechascurso").show(500);
			document.getElementById("plazopermanente").required = false;
	   }       
	   
	   if (this.value==3){
			$(".modo0").hide();	
			$(".modo1").hide();	
			$(".modo3").show(500);	  
			$("#fechascurso").hide(500);
			document.getElementById("plazopermanente").required = true;
	   }
    });
});

jQuery(function($) {

    $('#select_estado').on('change', function() {
	var selectvalue = $('#select_estado').val();
	var estado = $('#estadoAntiguo').val();
	var espera = $('#esperaAntiguo').val();
					
						  
   if ((espera==1)&&(selectvalue==0)){
		$("#iframeEspera").show();		
   }
	else{
		$("#iframeEspera").hide();	
   }	
   
   if ((estado!=1)&&(selectvalue==1)){
		$("#iframeBaja").show();		   
   }
   else{
		$("#iframeBaja").hide();	
   }
     
		
    });
});



jQuery(function($) {

    $('.diploma').on('change', function() {
	var selectvalue = $('#diploma:checked').val();
	var diploma = $('#diplomaAntiguo').val();

		console.error(selectvalue);

					
   if ((diploma!=1)&&(selectvalue==1)){
		$("#iframeDiploma").show();		
   }
	else{ 
		$("#iframeDiploma").hide();	
   }	
   
   if ((diploma!=-1)&&(selectvalue==-1)){
		$("#iframeDiplomaNOApto").show();		   
   }
   else{
		$("#iframeDiplomaNOApto").hide();	
   }
     
		
    });
});




jQuery(function($) {

    $('#privadocolegiados').on('change', function() {
		if($('#privadocolegiados').is(":checked")) {
			$('.precionocolegiado').hide(500);
		}
		else{
			
			$('.precionocolegiado').show(500);
		}
               
		
    });
});


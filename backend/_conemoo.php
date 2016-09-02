<?
/*function iConectarse(){ 
	if (!($link=pg_connect("localhost","userweb14","14pepe$4321","web"))) { 
      echo "Error conectando a la base de datos A. "; 
			echo pg_error($link);
      exit(); 
   } 
	/ * cambiar el conjunto de caracteres a utf8 * /
	if (!mysqli_set_charset($link, "utf8")) {
		//printf("Error cargando el conjunto de caracteres utf8: %s\n", mysqli_error($link));
	} else {
		//printf("Conjunto de caracteres actual: %s\n", mysqli_character_set_name($link));
		//exit();
	}	   
   return $link; 
} */


function conectarmoo(){
	if(!($link = pg_connect("host=localhost dbname=moodle user=moodleuser password=activatie_2014$")) ){
		return false;
		echo "Error conectando a la base de datos A. "; 
	}else{ // ESTE ELSE LO AGREGUE YO
		//echo "paso por aqu&iacute;";
		return $link;
	}
}
function iConectarsemoo(){
	if(!($link = pg_connect("host=localhost dbname=moodle user=moodleuser password=activatie_2014$")) ){
		return false;
		echo "Error conectando a la base de datos A. "; 
	}else{ // ESTE ELSE LO AGREGUE YO
		//echo "paso por aqu&iacute;";
		return $link;
	}
}
?>

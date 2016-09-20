<?

function getUri(){
	return $_SERVER["REQUEST_URI"];
}

function contieneUri($texto){
	
	$uri= getUri();
	
	if(stripos($uri,$texto) !== false){
		return true;
	}
	else return false;
}

function getFechaConMes($fecha){
    
	return (strftime("%e de %B de %Y", strtotime($fecha)));

}

function dias_transcurridos($fecha_i,$fecha_f)
{
	$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
	$dias 	= abs($dias); $dias = floor($dias);		
	return $dias;
}

function diasRestantes($fecha){
	$hoy= date('Y-m-d');
	return dias_transcurridos($hoy, $fecha);
}
?>

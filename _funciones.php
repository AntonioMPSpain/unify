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


?>

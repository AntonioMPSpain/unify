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
    
	return (strftime("%e %h. %Y", strtotime($fecha)));

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

function buscarLinkGenerarHTML($texto){
	// The Regular Expression filter
	$reg_exUrl = "/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";

	// Check if there is a url in the text
	if(preg_match($reg_exUrl, $texto, $url)) {
       // make the urls hyper links
       $texto = preg_replace($reg_exUrl, "<a target='_blank' href='".$url[0]."'>".$url[0]."</a> ", $texto);
	} 
	return $texto;
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'año',
        'm' => 'mes',
        'w' => 'semana',
        'd' => 'día',
        'h' => 'hora',
        'i' => 'minuto',
        's' => 'segundo',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ' : 'justo ahora';
}


?>

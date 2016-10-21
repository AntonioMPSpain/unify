<?
//###############################################################################################################################//
//###############################################################################################################################//
//######################################## ALGUNAS VARIABLES ###########################################################//
//###############################################################################################################################//
	global $c_prepass;
	global $c_pospass;
	global $url;
	global $c_directorio;
	global $c_md1;
	global $c_md2;
	$c_prepass="act2";
	$c_pospass="1p5265h3jkl";
	$url="http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	$c_directorio = dirname($url);
	$c_directorio_img = "/var/www/web";

	$c_md1="asjkh359sdj7";
	$c_md2="gdf07843fjckxdp09q";
	
//###############################################################################################################################//
//###############################################################################################################################//
function resizeFit($im,$width,$height) {
    //Original sizes
    $ow = imagesx($im); $oh = imagesy($im);
    
    //To fit the image in the new box by cropping data from the image, i have to check the biggest prop. in height and width
    if($width/$ow > $height/$oh) {
        $nw = $width;
        $nh = ($oh * $nw) / $ow;
        $px = 0;
        $py = ($height - $nh) / 2;
    } else {
        $nh = $height;
        $nw = ($ow * $nh) / $oh;
        $py = 0;
        $px = ($width - $nw) / 2;
    }
    
    //Create a new image width requested size
    $new = imagecreatetruecolor($width,$height);
    
    //Copy the image loosing the least space
    imagecopyresampled($new, $im, $px, $py, 0, 0, $nw, $nh, $ow, $oh);
    
    return $new;
}
function sql_last_inserted_id($connection, $result, $table_name, $column_name) {
   $oid = pg_last_oid ( $result);
      $query_for_id = "SELECT $column_name FROM $table_name WHERE oid=$oid";
   $result_for_id = pg_query($connection,$query_for_id);
   if(pg_num_rows($result_for_id))
      $id=pg_fetch_array($result_for_id,0,PGSQL_ASSOC);
   return $id[$column_name];
}
function variabletex(){
    foreach($_REQUEST as $key => $data){
        $data = strtolower($data);

        if (strpos($data, "base64_") !== false)
            exit;

        if (strpos($data, "union") !== false && strpos($data, "select") !== false && strpos($data, "update") !== false && strpos($data, "insert") !== false && strpos($data, "delete") !== false) 
            exit;
    }
}
function cleanInput($input) {
	$input=limpiarCadena($input);
	$search = array(
    '@<script[^>]*?>.*?</script>@si',   // Elimina javascript
    '@<[\/\!]*?[^<>]*?>@si',            // Elimina las etiquetas HTML
    '@<style[^>]*?>.*?</style>@siU',    // Elimina las etiquetas de estilo
    '@<![\s\S]*?--[ \t\n\r]*>@'         // Elimina los comentarios multi-línea
  );
 
    $output = preg_replace($search, '', $input);
    return $output;
  }
function limpiarCadena($valor)
{
	$valor = str_ireplace("base64_","",$valor);
	$valor = str_ireplace("UNION","",$valor);
	$valor = str_ireplace("<?php","",$valor);
	$valor = str_ireplace("<?PHP","",$valor);
	$valor = str_ireplace("SELECT","",$valor);
	$valor = str_ireplace("COPY","",$valor);
	$valor = str_ireplace("DELETE","",$valor);
	$valor = str_ireplace("DROP","",$valor);
	$valor = str_ireplace("DUMP","",$valor);
	$valor = str_ireplace(" OR ","",$valor);
	$valor = str_ireplace("%","",$valor);
	$valor = str_ireplace("LIKE","",$valor);
	$valor = str_ireplace("--","",$valor);
	$valor = str_ireplace("^","",$valor);
	$valor = str_ireplace("[","",$valor);
	$valor = str_ireplace("]","",$valor);
	$valor = str_ireplace("\\","",$valor);
	$valor = str_ireplace("!","",$valor);
	$valor = str_ireplace("¡","",$valor);
	$valor = str_ireplace("?","",$valor);
	$valor = str_ireplace("=","",$valor);
	$valor = str_ireplace("&","",$valor);
	$valor = str_ireplace("'","",$valor);
	$valor = str_ireplace("´","",$valor);
	return $valor;
}
function sanitize($input) {
    if (is_array($input)) {
        foreach($input as $var=>$val) {
            $output[$var] = sanitize($val);
        }
    }
    else {
        if (get_magic_quotes_gpc()) {
            $input = stripslashes($input);
        }
        $input  = cleanInput($input);
        $output = mysql_real_escape_string($input);
    }
    return $output;
}
//Funcion inversa a nl2br() de PHP
function br2nl($string, $line_break=PHP_EOL) {
    $patterns = array("/(<br>|<br \/>|<br\/>)\s*/i","/(\r\n|\r|\n)/");
    $replacements = array(PHP_EOL,$line_break);
    $string = preg_replace($patterns, $replacements, $string);
    return $string;
}

function valida_nif_cif_nie($cif) {
//Copyright ©2005-2011 David Vidal Serra. Bajo licencia GNU GPL.
//Este software viene SIN NINGUN TIPO DE GARANTIA; para saber mas detalles
//puede consultar la licencia en http://www.gnu.org/licenses/gpl.txt(1)
//Esto es software libre, y puede ser usado y redistribuirdo de acuerdo
//con la condicion de que el autor jamas sera responsable de su uso.
//Returns: 1 = NIF ok, 2 = CIF ok, 3 = NIE ok, -1 = NIF bad, -2 = CIF bad, -3 = NIE bad, 0 = ??? bad
         $cif = strtoupper($cif);
         for ($i = 0; $i < 9; $i ++)
         {
                  $num[$i] = substr($cif, $i, 1);
         }
//si no tiene un formato valido devuelve error
         if (!preg_match('/((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)/', $cif))
         {
                  return 0;
         }
//comprobacion de NIFs estandar
         if (preg_match('/(^[0-9]{8}[A-Z]{1}$)/', $cif))
         {
                  if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($cif, 0, 8) % 23, 1))
                  {
                           return 1;
                  }
                  else
                  {
                           return -1;
                  }
         }
//algoritmo para comprobacion de codigos tipo CIF
         $suma = $num[2] + $num[4] + $num[6];
         for ($i = 1; $i < 8; $i += 2)
         {
                  $suma += substr((2 * $num[$i]),0,1) + substr((2 * $num[$i]), 1, 1);
         }
         $n = 10 - substr($suma, strlen($suma) - 1, 1);
//comprobacion de NIFs especiales (se calculan como CIFs o como NIFs)
         if (preg_match('/^[KLM]{1}/', $cif))
         {
                  if ($num[8] == chr(64 + $n) || $num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr($cif, 1, 8) % 23, 1))
                  {
                           return 1;
                  }
                  else
                  {
                           return -1;
                  }
         }
//comprobacion de CIFs
         if (preg_match('/^[ABCDEFGHJNPQRSUVW]{1}/', $cif))
         {
                  if ($num[8] == chr(64 + $n) || $num[8] == substr($n, strlen($n) - 1, 1))
                  {
                           return 2;
                  }
                  else
                  {
                           return -2;
                  }
         }
//comprobacion de NIEs
         if (preg_match('/^[XYZ]{1}/', $cif))
         {
                  if ($num[8] == substr('TRWAGMYFPDXBNJZSQVHLCKE', substr(str_replace(array('X','Y','Z'), array('0','1','2'), $cif), 0, 8) % 23, 1))
                  {
                           return 3;
                  }
                  else
                  {
                           return -3;
                  }
         }
//si todavia no se ha verificado devuelve error
         return 0;
}

/**
 * Reemplaza todos los acentos por sus equivalentes sin ellos
 *
 * @param $string
 *  string la cadena a sanear
 *
 * @return $string
 *  string saneada
 */

function sanear_string($string1)
{
 
    $string = trim($string1);
 
    $string = str_replace(
        array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
        array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
        $string
    );
 
    $string = str_replace(
        array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
        array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
        $string
    );
 
    $string = str_replace(
        array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
        array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
        $string
    );
 
    $string = str_replace(
        array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
        array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
        $string
    );
 
    $string = str_replace(
        array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
        array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
        $string
    );
 
    $string = str_replace(
        array('ñ', 'Ñ', 'ç', 'Ç'),
        array('n', 'N', 'c', 'C'),
        $string
    );
	
    $string = str_replace(
        array(' '),
        array('_'),
        $string
    );
 
    //Esta parte se encarga de eliminar cualquier caracter extraño
    $string = str_replace(
        array("\\", "¨", "º", "-", "~",
             "#", "@", "|", "!", "\"",
             "·", "$", "%", "&", "/",
             "(", ")", "?", "'", "¡",
             "¿", "[", "^", "`", "]",
             "+", "}", "{", "¨", "´",
             ">", "<", ";", ",", ":",
             "."),
        '',
        $string
    );
 
    return $string;
}
 function salir() {
	session_unset();
	session_destroy();
	session_start();
	session_regenerate_id(true);
}

function twitter2($msg) {
	session_start();
	require_once('twitteroauth/twitteroauth/twitteroauth.php');
	require_once('twitteroauth/config.php');
	$_SESSION['access_token']['oauth_token'] = '2208651625-tWUVuMAMPstiofHG220M9uJ7ytE8jTPfYA8hiRa'; //inserta tu access token
	$_SESSION['access_token']['oauth_token_secret'] = '4iUTH2OZqttQZjOxwzXxTHWmj2g4ZMqrEIvLhRnaeHSe3'; //inserta tu token secret
	
	
	/* If access tokens are not available redirect to connect page. */
	if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
		header('Location: ./clearsessions.php');
	}
	/* Get user access tokens out of the session. */
	$access_token = $_SESSION['access_token'];
	
	/* Create a TwitterOauth object with consumer/user tokens. */
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
	
	/* If method is set change API call made. Test is called by default. */
	//$content = $connection->get('account/verify_credentials');
	$connection->post('statuses/update', array('status' => $msg));
}

function pasa_acentos($s) {
	$s = str_replace("&Aacute;","Á",$s);
	$s = str_replace("&aacute;","á",$s);
	$s = str_replace("&Eacute;","É",$s);
	$s = str_replace("&eacute;","é", $s);
	$s = str_replace("&iacute;","í",$s);
	$s = str_replace("&Iacute;","Í",$s);
	$s = str_replace("&oacute;","ó",$s);
	$s = str_replace("&Oacute;","Ó",$s);
	$s = str_replace("&uacute;","ú",$s);
	$s = str_replace("&Uacute;","Ú;",$s);
	$s = str_replace("&ntilde;","ñ",$s);
	$s = str_replace("&Ntilde;","Ñ",$s);
	return $s;
}
function cambia_acentos($s) {
	$s = str_replace("á", "&aacute;", $s);
	$s = str_replace("Á","&Aacute;",$s);
	$s = str_replace("é","&eacute;",$s);
	$s = str_replace("É","&Eacute;",$s);
	$s = str_replace("í","&iacute;",$s);
	$s = str_replace("Í","&Iacute;",$s);
	$s = str_replace("ó","&oacute;",$s);
	$s = str_replace("Ó","&Oacute;",$s);
	$s = str_replace("ú","&uacute;",$s);
	$s = str_replace("Ú","&Uacute;",$s);
	$s = str_replace("ñ","&ntilde;",$s);
	$s = str_replace("Ñ","&Ntilde;",$s);
	return $s;
}
function quita_acentos($s) {
	$s = str_replace("á", "a", $s);
	$s = str_replace("Á","A",$s);
	$s = str_replace("é","e",$s);
	$s = str_replace("É","E",$s);
	$s = str_replace("í","i",$s);
	$s = str_replace("Í","I",$s);
	$s = str_replace("ó","o",$s);
	$s = str_replace("Ó","O",$s);
	$s = str_replace("ú","u",$s);
	$s = str_replace("Ú","U",$s);
	return $s;
}
function sql_htm($string)
{
  $xml_str = mb_convert_encoding($string, "UTF-8", "ISO-8859-1");
  return $xml_str;
}
function htm_sql($string)
{
  $xml_str = mb_convert_encoding($string, "ISO-8859-1", "UTF-8");
  return $xml_str;
}  
	
function noCache() {
  header("Expires: Tue, 01 Jul 2001 06:00:00 GMT");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
}

function recoge($var) 
{
    $tmp = (isset($_REQUEST[$var])) ? strip_tags(trim(htmlspecialchars($_REQUEST[$var]))) : '';
    if (get_magic_quotes_gpc()) {
        $tmp = stripslashes($tmp);
    }
    return $tmp;
}

function array_envia($array) {
    $tmp = serialize($array);
    $tmp = urlencode($tmp);
    return $tmp;
} 
function array_recibe($url_array) {
    $tmp = stripslashes($url_array);
    $tmp = urldecode($tmp);
    $tmp = unserialize($tmp);
   return $tmp;
} 
function MergeArrays($Arr1, $Arr2)
{
  foreach($Arr2 as $key => $Value)
  {
    if(array_key_exists($key, $Arr1) && is_array($Value))
      $Arr1[$key] = MergeArrays($Arr1[$key], $Arr2[$key]);

    else
      $Arr1[$key] = $Value;

  }

  return $Arr1;

}


///////////////////////////////
//////////////////////////////


function insertar($campos, $tabla)
{

	foreach ($campos as &$cmpq){
		if ($cmpq != NULL ){
			$cmpq = "'".$cmpq."'";
			//echo "<p>$cmpq</p>";
		} else{
			$cmpq = "NULL";
		}
 	}
	$str_campos = implode (',', $campos);	
	echo "<p>INSERT INTO $tabla VALUES ($str_campos)</p>";

	$query = mysql_query("INSERT INTO $tabla VALUES ($str_campos)") or die ( "ERROR #1: " .mysql_error());	
	
}







///////////////////////////////////
//////////////////////////////////
// ORDENA FECHA /////////////////
////////////////////////////////
function get_date($array){
	// array("001", "TextoA", "01/11/2012");
	$fecha = $array[2];
	$dia = $fecha[0].$fecha[1];
	$mes = $fecha[3].$fecha[4];
	$year = $fecha[6].$fecha[7].$fecha[8].$fecha[9];
	//print "$fecha[6]$fecha[7]$fecha[8]$fecha[9]";
	$fecha = array ($dia, $mes, $year);
	return $fecha;
}
function primera_fecha_menor($fecha1, $fecha2){
	///////////////////////////////////////////////////////
	// array ([0]-> dia, [1]-> mes, [2]-> año)
	// En caso de que sea la misma fecha devuelve fecha1
	// PRIMERO: Comparamos el año
	if ($fecha1[2] < $fecha2[2]){
		return TRUE;
	} elseif ($fecha1[2] > $fecha2[2]) {
		return FALSE;
	}else{
		// Si son iguales miramos el MES
		if ($fecha1[1] < $fecha2[1]){
			return TRUE;	
		} elseif ($fecha1[1] > $fecha2[1]){
			return FALSE;
		} else{
			// Si el mes tambien es el mismo comparamos el DÍA
			if ($fecha1[0]<= $fecha2[0]){
				return TRUE;
			}else{
				return FALSE;
			}
		}
	}
}
function cmp($a = array(), $b = array()){
	// array("001", "TextoA", "01/11/2012");
	// array("001", "TextoA", "01/11/2012");
	
	if ( primera_fecha_menor(get_date($a), get_date($b)) ){
		return TRUE;
	} else {
		return FALSE;
	}
}
/**
 * La función ordena_fecha(); utiliza las siguientes funciones: 
 *  get_date(); primera_fecha_menor(); y cmp();
 * @param $a1:array, $a2:array
 * @return array
 */   
function ordena_fecha($a1, $a2)
{
	//////////////////////////////////
	// Unimos los arrays en uno solo y lo ordenamos.
	$a_result = array();
	foreach ($a1 as &$row){
		$row[3] = 1;
	}
	foreach ($a2 as &$row){
		$row[3] = 2;
	}
		
   	$a_result = array_merge($a1, $a2);
	usort($a_result, "cmp");
	return array_reverse($a_result);
}
///////////////////////////////////
//////////////////////////////////
// FIN ORDENA FECHA /////////////
////////////////////////////////


function valida_cif($cif) {
	//Copyright ©2005-2011 David Vidal Serra. Bajo licencia GNU GPL.
	//Este software viene SIN NINGUN TIPO DE GARANTIA; para saber mas detalles
	//puede consultar la licencia en http://www.gnu.org/licenses/gpl.txt(1)
	//Esto es software libre, y puede ser usado y redistribuirdo de acuerdo
	//con la condicion de que el autor jamas sera responsable de su uso.
	//Returns: 1 = NIF ok, 2 = CIF ok, 3 = NIE ok, -1 = NIF bad, -2 = CIF bad, -3 = NIE bad, 0 = ??? bad
         $cif = strtoupper($cif);
         for ($i = 0; $i < 9; $i ++)
         {
                  $num[$i] = substr($cif, $i, 1);
         }
		//si no tiene un formato valido devuelve error
         if (!preg_match('/((^[A-Z]{1}[0-9]{7}[A-Z0-9]{1}$|^[T]{1}[A-Z0-9]{8}$)|^[0-9]{8}[A-Z]{1}$)/', $cif))
         {
                  return 0;
         }
        
		//algoritmo para comprobacion de codigos tipo CIF
         $suma = $num[2] + $num[4] + $num[6];
         for ($i = 1; $i < 8; $i += 2)
         {
                  $suma += substr((2 * $num[$i]),0,1) + substr((2 * $num[$i]), 1, 1);
         }
         $n = 10 - substr($suma, strlen($suma) - 1, 1);
		//comprobacion de CIFs
         if (preg_match('/^[ABCDEFGHJNPQRSUVW]{1}/', $cif))
         {
                  if ($num[8] == chr(64 + $n) || $num[8] == substr($n, strlen($n) - 1, 1))
                  {
                           return 2;
                  }
                  else
                  {
                           return -2;
                  }
         }
		//si todavia no se ha verificado devuelve error
         return 0;
}



		/**
	 * Función que comprueba si una cadena dada es un DNI/NIE válida
	 * http://www.kiwwito.com/articulo/funcion-php-para-validar-dni-nie-espanol
	 * @param string $cadena
	 * @return boolean
	 */      
	function es_DNI_NIE_valido($cadena)
	{
	    //Comprobamos longitud
	    if (strlen($cadena) != 9) return false;      
	 
	    //Posibles valores para la letra final
	    $valoresLetra = array(
	        0 => 'T', 1 => 'R', 2 => 'W', 3 => 'A', 4 => 'G', 5 => 'M',
	        6 => 'Y', 7 => 'F', 8 => 'P', 9 => 'D', 10 => 'X', 11 => 'B',
	        12 => 'N', 13 => 'J', 14 => 'Z', 15 => 'S', 16 => 'Q', 17 => 'V',
	        18 => 'L', 19 => 'H', 20 => 'C', 21 => 'K',22 => 'E'
	    );
	
	    //Comprobar si es un DNI
	    if (preg_match('/^[0-9]{8}[A-Z]$/i', $cadena))
	    {
	        //Comprobar letra
	        if (strtoupper($cadena[strlen($cadena) - 1]) !=
	            $valoresLetra[((int) substr($cadena, 0, strlen($cadena) - 1)) % 23])
	            return false;
	 
	        //Todo fue bien
	        return true;
	    }
	    //Comprobar si es un NIE
	    else if (preg_match('/^[XYZ][0-9]{7}[A-Z]$/i', $cadena))
	    {
	        //Comprobar letra
	        if (strtoupper($cadena[strlen($cadena) - 1]) !=
	            $valoresLetra[((int) substr($cadena, 1, strlen($cadena) - 2)) % 23])
	            return false;
	
	        //Todo fue bien
	        return true;
	    }
	   
	    //Cadena no válida  
	    return false;
	}

	
	function esCorrecto($ccc)
		{
	
		$banco=substr($ccc, 0, 8);
		$digito1=substr($ccc, 8, 1);
	        $digito2=substr($ccc, 9, 1);
		$cuenta=substr($ccc, 10);
	
		$APesos = Array(1,2,4,8,5,10,9,7,3,6); // Array de "pesos"
		$DC1=0;
		$DC2=0;
		$x=8;
		while($x>0) {
			$digito=$banco[$x-1];
			$DC1=$DC1+($APesos[$x+2-1]*($digito));
			$x = $x - 1;
		}
		$Resto = $DC1%11;
		$DC1=11-$Resto;
		if ($DC1==10) $DC1=1;
		if ($DC1==11) $DC1=0;              // D�gito control Entidad-Oficina
	
		$x=10;
		while($x>0) {
			$digito=$cuenta[$x-1];
			$DC2=$DC2+($APesos[$x-1]*($digito));
			$x = $x - 1;
		}
		$Resto = $DC2%11;
		$DC2=11-$Resto;
		if ($DC2==10) $DC2=1;
		if ($DC2==11) $DC2=0;         // D�gito Control C/C
	
		if (!$ccc) {
			//return "NADA";
		} elseif (($DC1==$digito1) && ($DC2==$digito2) && (strlen($ccc)=="20")) {
			return true;
		} else {
			return false;
		}
	
	}
	


	function crear_RSS( $tabla, $dominio, $titulo_RSS='', $x = '10', $correo='', $logo= '', $descripcion='', $imagen='')
	{
		$ruta_temporal=$_SERVER['HTTP_HOST'];
		//////////////// FECHA
		$data = date("d\m\Y");
		////////////////////////////////////////////////
		////////// Genero el archivo.
		$archivo = "imagen/".$tabla.".rss.xml";
		$fp = fopen($archivo, "w+") or die ( "fallo al abrir el archivo" .mysql_error() );
		///////////////////////////////////////////////
		//////// Establecemos TODOS los parametros
		$query = mysql_query("SELECT * FROM $tabla WHERE permiso<>3 AND permiso<>2 AND tipo='noticia' AND borrado=0  ORDER BY id DESC LIMIT $x ") or die ( "ERROR #1: " .mysql_error());
		//////////////////////////////////////////////
		/////// Si hay algun campo vacío, 
		/////// lo cargamos con el de configuración
		$query_conf = mysql_query("SELECT * FROM configuracion") or die ("ERROR #2 conf: " .mysql_error());
		$datos_conf = mysql_fetch_object($query_conf);
		if ($titulo_RSS == NULL){
			$titulo_RSS = $datos_conf->c_title;
			// Eliinamos ID, IDPADRE .... 
		}
		if ($correo == NULL){
			$correo = $datos_conf->c_email; 
		}
		if ($logo == NULL){
			$logo = $datos_conf->c_logo; 
		}
		if ($descripcion == NULL){
			$descripcion = $datos_conf->c_description;
		}
		if ($imagen == NULL){
			$imagen = $datos_conf->imagen;
		}
		///////////////////////////////////////////////
		//////// Voy a acumularlo con varias cadenas  
		/*$s_cabecera = "<?xml version='1.0'  encoding='iso-8859-1' ?>
					<rss version='2.0' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance'><channel>
					<title>$titulo_RSS</title>
					<link>http://$dominio</link>
					<image>http://$dominio/img/logo-coaatmu.png</image>
					<url>http://$dominio</url>
					<description>$descripcion</description>
					<language>es</language>
					<generator>http://$dominio + newsisco.com</generator>
					";*/
		$fecha=date(DATE_RSS);
		$s_cabecera="<?xml version='1.0' encoding='iso-8859-1' standalone='yes'?>
		<rss version='2.0' >
			<channel>
				<title>coaatiemu - noticias</title>
				<link>http://$ruta_temporal</link>
				<description>coaat, colegio, aparejadores, noticias, archivo</description>
				<language>es-ES</language>
				<image>
					<url>http://$ruta_temporal/img/logo-coaatmu.png</url>
					<title>coaat</title>
					<link>http://$ruta_temporal</link>				
				</image>
				<pubDate>$fecha</pubDate>
				";
		////////////////////////////////////////////////
		////////// Escribo denttro del rss	
		$i=0;
		$s_noticia = array();
		while ($new = mysql_fetch_object($query)) {
			$fecha=date(DATE_RSS);
			$datosss=$new->informacion;
			$s_noticia[$i] = "
					<item>
						<title><![CDATA[".utf8_decode($new->titulo)."]]></title>
						<link>http://$ruta_temporal/noticia.php?id=$new->id</link>
						<description><![CDATA[".$datosss."]]></description>
						<pubDate>$fecha</pubDate>
						<guid>http://$ruta_temporal/noticia.php?id=$new->id</guid> 
					</item>
			";
			$i++;
			 
		}
		$s_noticia[$i] = "
				   </channel>
				   </rss>";
		  
		////////////////////////////////////////////////
		////////// Escribo denttro del rss la cabecera y todas las noticias.
		$write = fputs($fp, $s_cabecera);
		foreach ($s_noticia as $notic){
			$write = fputs($fp, $notic);
		}
		fclose($fp); 
		//echo "DONE";
		////////////////////////////////////////////////		
		/////////////////////////  FIN ////////////////
	 }



		function mostrar_tabla($table_name, $campos = array(), $modo = 'normal', $css = 'none' ){
		
		///////////////////////////////////////////////
		//// INSTRUCCIONES
		////
		/* Nombre de la tabla obligatorio
		 * Nombres de los campos -> Optativo. Si no se especifica los muestra todos
		 * EXCEPTO ID, BORRADO e IDPADRE (Si es ADMIN muestra las opciones MODIFICAR Y ELIMINAR)
		 * Estilo  CSS para la tabla -> Optativo.
		 */	
		 
		 /* Voy a mostrar la información en una tabla a la que le aplicaremos  
		  * el correspondiente estilo CSS mediante la clase. 
		  */
		 ////////////////////////////////////////////////////////////////////
		 //////////////////////////////////////////////////////////////////
		// $campo1 ='', $campo2 ='', $campo3 ='', $campo4 ='', $campo5 ='', $campo6 ='', $campo7 ='', $campo8 ='', $campo9 ='', $campo10 ='', $campo11 ='', $campo12 ='', $campo13 ='', $campo14 ='', $campo15 ='', $campo16 ='', $campo17 ='', $campo18 ='', $campo19 ='', $campo20 =''
		$i = 0;
		//////////// Se activa cuando el modo es admin pero no se incluye el id en los campos 
		$no_id = FALSE;
		//////////////////////////////////////////////
		// Obtengo el nombre de  TODOS los campos de la tabla
		$query = mysql_query("DESCRIBE $table_name") or die ("ERROR:" .mysql_error());

		while ( $fila = mysql_fetch_object($query) ) {
			$name = $fila->Field;
			$cabecera[$i] = $name;
			$i++;
			//echo "<td>$name</td> "; 
		}
		//////////////////////////////////////////////
		//////////////////////////////////////////////
		//////// Si el parametro 'campos' esta vacío y el modo es normal:
		/////// ELIMINO: ID, BORRADO, IDPADRE
		//////
		if ($campos == NULL && $modo == 'admin'){
			// necesito el ID, pero no lo quiero mostrar
			$borrar = array("borrado","idpadre");
			$campos = array_diff($cabecera, $borrar); 
			$no_id = TRUE;
			////////////////////////////////////////
			///// Reestructuro el array para que no queden vacíos
			$aux = $campos;
			$campos = array();
			$i = 0;
			foreach ($aux as &$value){
				if ($value != NULL){
					$campos[$i] = $value;
					$i++;
				}	
			}
		} elseif ($campos == NULL && $modo != 'admin'){
			// no quiero el ID
			$borrar = array("id","borrado","idpadre");
			$campos = array_diff($cabecera, $borrar); 
			////////////////////////////////////////
			///// Reestructuro el array para que no queden vacíos
			$aux = $campos;
			$campos = array();
			$i = 0;
			foreach ($aux as &$value){
				if ($value != NULL){
					$campos[$i] = $value;
					$i++;
				}	
			}
		//} elseif ($campos != NULL && $modo != 'admin'){
		// No hago NADA
		} elseif ($campos != NULL && $modo == 'admin'){
			// Ver si en los campos incluidos está el ID
			if ( !(in_array('id', $campos)) ){
				// Hay que añadirlo, lo vamos a necesitar
				array_unshift($campos, 'id');
				$no_id = TRUE;
			}
		}		
		//////////////////////////////////////////////
		//////////////////////////////////////////////
		///// Preparo los campos para la consulta.
		$string_campos = implode (',', $campos);	
		//////////////////////////////////////////////
		///// Creo la consulta.
		$query = mysql_query("SELECT $string_campos FROM $table_name") or die ("ERROR: " .mysql_error() );	
		//////////////////////////////////////////////
		/////////////////////////////////////////////
		/////// Pinto la TABLA /////////////////////
		echo "<table class = '$css' ><tr>";
		if ($no_id){
			foreach ($campos as &$campo){
				if ($campo != 'id'){
					echo "<td>$campo</td> ";
				} 
			}
		} else {
			foreach ($campos as &$campo){
				echo "<td>$campo</td> "; 
			}
		}
		///////////////////////////////////////////		
		echo "</tr>";
		echo "<tr>";	
		$i = 0;
		$n = count($campos);
		if ($modo == 'admin'){
			//////////////////////////////////////////////
			//////// Con ADMIN option	
			/// No quiero mostar el id ---> lo único que cambio son los índices '$i' de las cabeceras
			if ( $no_id ){
				$i=1;
				while ( $fila = mysql_fetch_object($query) ) {
					while ($i < $n){
						$name = $fila->$campos[$i % $n];
						echo "<td>$name</td> ";
						$id = $fila->id; 
						$i++;
					}
					echo "<td><a class='a_verde' href='modificar_".$table_name.".html.php?id=$id'>Modificar</a></td>";
					echo "<td><a class='a_rojo' href='eliminar_".$table_name.".html.php?id=$id'>Eliminar</a></td>";
					echo" </tr><tr>";
					$i = 1;
				}			
			} else {
				//// Muestro todas las cabeceras
				while ( $fila = mysql_fetch_object($query) ) {
					while ($i < $n){
						$name = $fila->$campos[$i % $n];
						echo "<td>$name</td> ";
						$id = $fila->id; 
						$i++;
					}
					echo "<td><a class='a_verde' href='modificar_".$table_name.".html.php?id=$id'>Modificar</a></td>";
					echo "<td><a class='a_rojo' href='eliminar_".$table_name.".html.php?id=$id'>Eliminar</a></td>";
					echo" </tr><tr>";
					$i = 0;
				}
				
			}
		} else {
			//////////////////////////////////////////////
			//////// Sin admin option	
			while ( $fila = mysql_fetch_object($query) ) {
				while ($i < $n){
					$name = $fila->$campos[$i % $n];
					echo "<td>$name</td> "; 
					$i++;
				}
				echo" </tr><tr>";
				$i = 0;
			}
			
		}
		echo "</tr></table>";
		////////////////////////////////////////////////		
		/////////////////////////  FIN ////////////////
	}


function comprobar_email($email){ 
    $mail_correcto = 0; 
    //compruebo unas cosas primeras 
    if ((strlen($email) >= 6) && (substr_count($email,"@") == 1) && (substr($email,0,1) != "@") && (substr($email,strlen($email)-1,1) != "@")){ 
       if ((!strstr($email,"'")) && (!strstr($email,"\"")) && (!strstr($email,"\\")) && (!strstr($email,"\$")) && (!strstr($email," "))) { 
          //miro si tiene caracter . 
          if (substr_count($email,".")>= 1){ 
             //obtengo la terminacion del dominio 
             $term_dom = substr(strrchr ($email, '.'),1); 
             //compruebo que la terminaci�n del dominio sea correcta 
             if (strlen($term_dom)>1 && strlen($term_dom)<5 && (!strstr($term_dom,"@")) ){ 
                //compruebo que lo de antes del dominio sea correcto 
                $antes_dom = substr($email,0,strlen($email) - strlen($term_dom) - 1); 
                $caracter_ult = substr($antes_dom,strlen($antes_dom)-1,1); 
                if ($caracter_ult != "@" && $caracter_ult != "."){ 
                   $mail_correcto = 1; 
                } 
             } 
          } 
       } 
    } 
    if ($mail_correcto) 
       return 1; 
    else 
       return 0; 
} 

// Funciones de texto
function comprobar_nombre($nombre_usuario){ 
   if (ereg("^[a-zA-Z0-9\-_]{1,20}$", $nombre_usuario)) { 
	  return true; 
   } else { 
	  //echo "<br><br>El texto $nombre_usuario no es valido<br>"; 
	  return false;
	  exit();
   } 
}


function tamano_archivo($peso , $decimales = 2 ) {
	$clase = array(" Bytes", " KB", " MB", " GB", " TB"); 
	return round($peso/pow(1024,($i = floor(log($peso, 1024)))),$decimales ).$clase[$i];
} 

// Copyright (C) 2010  Juan Valencia <jvalenciae@jveweb.net>
// Todos los derechos reservados.
//
// La redistribución de este script, con o sin modificaciones, es permitida
// dado que las siquientes condiciones sean cumplidas:
//
// 1. Redistribuciones de este script deben retener la noticia de copyright,
//    esta lista de condiciones y la siquiente renuncia.
//
// ESTE SOFTWARE ES PROVISTO POR EL AUTOR ''COMO ES'' Y CUALQUIER GARANTÍA
// EXPRESADA O IMPLÍCITA, INCLUYENDO, PERO NO LIMITADAS A, LAS GARANTÍAS
// IMPLICÍTAS DE COMERCIABILIDAD Y SALUBRIDAD PARA UN PROPÓSITO EN PARTICULAR
// ES RENUNCIADA. EN NINGÚNA EVENTUALIDAD SERÁ EL AUTOR RESPONSABLE POR
// NINGÚN DAÑO DIRECTO, INDIRECTO, INCIDENTAL, ESPECIAL, EJEMPLIFICANTE, O
// CONSECUENCIAL (INCLUYENDO, PERO NO LIMITADO A, PROCURAMIENTO O SUBSTITUCIÓN
// DE BIENES O SERVICIOS; PERDIDA DE USO, DATOS O GANANCIAS; O INTERRUPCIÓN DE
// NEGOCIOS) COMO SEA QUE HAYAN SIDO CAUSADOS Y EN CUALQUIER TEORÍA DE
// RESPONSABILIDAD, YA SEA EN CONTRATO, RESPONSABILIDAD ESTRICTA, O DELITO
// (INCLUYENDO NEGLIGENCIA U OTRA COSA) QUE RESULTE DE CUALQUIER MANERA DE EL
// USO DE ESTE SOFTWARE, AÚN SI SE SABE DE LA POSIBILIDAD DE SEMEJANTE DAÑO.

// crear_previo_cortado()
// $direccion_imagen - La dirección relativa a una imágen existente desde el
//    script
// $previo_ancho - El áncho máximo de la vista previa
// $previo_alto - El alto máximo de la vista previa
// $prefijo - Un prefijo a ser añadido al nombre del archivo original a fin de
//    obtener el nombre que le será dado a la vista previa

function crear_previo_cortado($direccion_imagen, $previo_ancho, $previo_alto, $prefijo) {
    if (!(is_integer($previo_ancho) && $previo_ancho > 0)) {
       // echo "El ancho es inválido";
            return false;
    }

    if (!(is_integer($previo_alto) && $previo_alto > 0)) {
        //echo "El alto es inválido";
            return false;
    }
    $extension = pathinfo($direccion_imagen, PATHINFO_EXTENSION);
   switch ($extension) {
        case "jpg":
            $imagen_original = imagecreatefromjpeg($direccion_imagen);
            break;
        case "jpeg":
            $imagen_original = imagecreatefromjpeg($direccion_imagen);
            break;
        case "gif":
            $imagen_original = imagecreatefromgif($direccion_imagen);
            break;
        case "png":
            $imagen_original = imagecreatefrompng($direccion_imagen);
            break;
        default:
            return false;
            //echo "Tipo de archivo inválido";
            break;
    }
    $original_ancho = imageSX($imagen_original);
    $original_alto = imageSY($imagen_original);
    if ((($original_ancho / $original_alto) - ($previo_ancho / $previo_alto)) == 0) {
        $original_x = 0;
        $original_y = 0;
    }
    if (($original_ancho / $original_alto) > ($previo_ancho / $previo_alto)) {
        $original_y = 0;
        $temporal_ancho = ceil($original_alto * $previo_ancho / $previo_alto);
        $original_x = ceil(($original_ancho - $temporal_ancho) / 2);
        $original_ancho = $temporal_ancho;
    }
    if (($original_ancho / $original_alto) < ($previo_ancho / $previo_alto)) {
        $original_x = 0;
        $temporal_alto = ceil($original_ancho * $previo_alto / $previo_ancho);
        $original_y = ceil(($original_alto - $temporal_alto) / 2);
        $original_alto = $temporal_alto;
    }
    $imagen_destino = ImageCreateTrueColor($previo_ancho, $previo_alto);
    imagecopyresampled($imagen_destino, $imagen_original, 0, 0, $original_x, $original_y, $previo_ancho, $previo_alto, $original_ancho, $original_alto);
    $archivo_destino = pathinfo($direccion_imagen, PATHINFO_DIRNAME) . "/";
   // $archivo_destino .= $prefijo . "_" . pathinfo($direccion_imagen, PATHINFO_FILENAME).".".$extension; //he añadido extension
    $archivo_destino .= $prefijo . pathinfo($direccion_imagen, PATHINFO_FILENAME).".".$extension; //he añadido extension
    switch ($extension) {
        case "jpg":
            imagejpeg($imagen_destino, $archivo_destino);
            break;
        case "jpeg":
            imagejpeg($imagen_destino, $archivo_destino);
            break;
        case "gif":
            imagegif($imagen_destino, $archivo_destino);
            break;
        case "png":
            imagepng($imagen_destino, $archivo_destino);
            break;
		
    }
    imagedestroy($imagen_destino);
    imagedestroy($imagen_original);
	return true;
}

function crear_previo_cortado_($direccion_imagen, $previo_ancho, $previo_alto, $prefijo) {
    if (!(is_integer($previo_ancho) && $previo_ancho > 0)) {
       // echo "El ancho es inválido";
            return false;
    }

    if (!(is_integer($previo_alto) && $previo_alto > 0)) {
        //echo "El alto es inválido";
            return false;
    }
    $extension = pathinfo($direccion_imagen, PATHINFO_EXTENSION);
   switch ($extension) {
        case "jpg":
            $imagen_original = imagecreatefromjpeg($direccion_imagen);
            break;
        case "jpeg":
            $imagen_original = imagecreatefromjpeg($direccion_imagen);
            break;
        case "gif":
            $imagen_original = imagecreatefromgif($direccion_imagen);
            break;
        case "png":
            $imagen_original = imagecreatefrompng($direccion_imagen);
            break;
        default:
            return false;
            //echo "Tipo de archivo inválido";
            break;
    }
    $original_ancho = imageSX($imagen_original);
    $original_alto = imageSY($imagen_original);
    if ((($original_ancho / $original_alto) - ($previo_ancho / $previo_alto)) == 0) {
        $original_x = 0;
        $original_y = 0;
    }
    if (($original_ancho / $original_alto) > ($previo_ancho / $previo_alto)) {
        $original_y = 0;
        $temporal_ancho = ceil($original_alto * $previo_ancho / $previo_alto);
        $original_x = ceil(($original_ancho - $temporal_ancho) / 2);
        $original_ancho = $temporal_ancho;
    }
    if (($original_ancho / $original_alto) < ($previo_ancho / $previo_alto)) {
        $original_x = 0;
        $temporal_alto = ceil($original_ancho * $previo_alto / $previo_ancho);
        $original_y = ceil(($original_alto - $temporal_alto) / 2);
        $original_alto = $temporal_alto;
    }
    $imagen_destino = ImageCreateTrueColor($previo_ancho, $previo_alto);
    imagecopyresampled($imagen_destino, $imagen_original, 0, 0, $original_x, $original_y, $previo_ancho, $previo_alto, $original_ancho, $original_alto);
    $archivo_destino = pathinfo($direccion_imagen, PATHINFO_DIRNAME) . "/";
    $archivo_destino .= $prefijo . "_" . pathinfo($direccion_imagen, PATHINFO_FILENAME).".".$extension; //he añadido extension
    switch ($extension) {
        case "jpg":
            imagejpeg($imagen_destino, $archivo_destino);
            break;
        case "jpeg":
            imagejpeg($imagen_destino, $archivo_destino);
            break;
        case "gif":
            imagegif($imagen_destino, $archivo_destino);
            break;
        case "png":
            imagepng($imagen_destino, $archivo_destino);
            break;
		
    }
    imagedestroy($imagen_destino);
    imagedestroy($imagen_original);
	return true;
}
// Parámetros: string $data:  Fecha en formato dd/mm/aaaa o timestamp
//             int    $tipus: Tipo de fecha (0-timestamp, 1-dd/mm/aaaa)
//
// Retorna:    string  Fecha en formato largo (x, dd mm de yyyy)
function data_text($data, $tipus=1)
{
  if ($data != '' && $tipus == 0 || $tipus == 1)
  {
    $setmana = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
    $mes = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'); 

    if ($tipus == 1)
    {
      ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', $data, $data);
      $data = mktime(0,0,0,$data[2],$data[1],$data[3]);
    } 

    return date('d', $data).' de '.$mes[date('m',$data)-1].' de '.date('Y', $data);
  }
  else
  {
    return 0;
  }
}
function nombre_corto_mes($mes)
{
    $meses = array('ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic'); 
    return $meses[$mes-1];
}
function nombre_mes($mes)
{
    $meses = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'); 
    return $meses[$mes-1];
}

function compara_fechas($fecha1,$fecha2)
{
      if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha1))
              list($dia1,$mes1,$año1)=split("/",$fecha1);
      if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha1))
              list($dia1,$mes1,$año1)=split("-",$fecha1);
        if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha2))
              list($dia2,$mes2,$año2)=split("/",$fecha2);
      if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha2))
              list($dia2,$mes2,$año2)=split("-",$fecha2);
        $dif = mktime(0,0,0,$mes1,$dia1,$año1) - mktime(0,0,0, $mes2,$dia2,$año2);
        return ($dif);                         
}
 

function cambiaf_a_normal($fecha){
    ereg( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fecha, $mifecha);
    $lafecha=$mifecha[3]."/".$mifecha[2]."/".$mifecha[1];
	if($lafecha == "//") $lafecha = ""; 
	return $lafecha;
}
function cambiaf_a_normal_todo($fecha){
	if ($fecha){
		$p=split(" ",$fecha);
		$f=split("-",$p[0]);
		//$h=split(":",$p[1]);
		//cambio de fecha
		$lafecha=cambiaf_a_normal($p[0]);
		return $lafecha." ".$p[1];
		
	}
	//formato entrante : 2007-10-02 11:36:59
	//formato saliente : 02/10/2007 11:36:59
}
function cambiaf_a_normal_ingles($fecha){
    ereg( "([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})", $fecha, $mifecha);
    $lafecha=$mifecha[2]."/".$mifecha[3]."/".$mifecha[1];
    return $lafecha;
}
function cambiaf_a_normal_todo_pg($fecha){
	if ($fecha){
		$p=split(" ",$fecha);
		$f=split("-",$p[0]);
		//$h=split(":",$p[1]);
		//cambio de fecha
		$lafecha=cambiaf_a_normal($p[0]);
		$rest = substr($p[1], 0, 8);  // substr('abcdef', 0, 4);  // abcd
		return $lafecha." ".$rest;
		
	}
	//formato entrante : 2007-10-02 11:36:59
	//formato saliente : 02/10/2007 11:36:59
}


//Convierte fecha de normal a mysql
function cambiaf_a_mysql($fecha){
    ereg( "([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $fecha, $mifecha);
    $lafecha=$mifecha[3]."-".$mifecha[2]."-".$mifecha[1];
	if($lafecha == "--") $lafecha = ""; 
    return $lafecha;
}
function fecha_a_mysql($fecha){ 
		ereg( "([0-9]{1,2})-([0-9]{1,2})-([0-9]{2,4})", $fecha, $mifecha); 
		$lafecha=$mifecha[3]."-".$mifecha[2]."-".$mifecha[1]; 
		if($lafecha == "--") $lafecha = ""; 
		return $lafecha; 
	} 
function suma_fechas($fecha,$ndias)
{
	if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha))
	list($dia,$mes,$año)=split("/", $fecha);
	if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha))
	list($dia,$mes,$año)=split("-",$fecha);
	$nueva = mktime(0,0,0, $mes,$dia,$año) + $ndias * 24 * 60 * 60;
	$nuevafecha=date("d/m/Y",$nueva);
	return ($nuevafecha);
}

function resta_fechas($fecha,$ndias){         
		if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha)) 
			list($dia,$mes,$anno)=split("/", $fecha); 
		if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha)) 
			list($dia,$mes,$anno)=split("-",$fecha); 
		//$nuevafecha = date("d/m/Y", strtotime("$fecha + $ndias days")); 
		$nuevafecha = date("Y-m-d", strtotime("$fecha - $ndias days")); 
		return ($nuevafecha);   
	} 
function a_fechas($fecha,$ndias){         
		if (preg_match("/[0-9]{1,2}\/[0-9]{1,2}\/([0-9][0-9]){1,2}/",$fecha)) 
			list($dia,$mes,$anno)=split("/", $fecha); 
		if (preg_match("/[0-9]{1,2}-[0-9]{1,2}-([0-9][0-9]){1,2}/",$fecha)) 
			list($dia,$mes,$anno)=split("-",$fecha); 
		//$nuevafecha = date("d/m/Y", strtotime("$fecha + $ndias days")); 
		$nuevafecha = date("Y-m-d", strtotime("$fecha + $ndias days")); 
		return ($nuevafecha);   
	} 
//Variables de Fechas	
setlocale(LC_TIME,"es_ES"); 
setlocale(LC_TIME,"spanish"); 
$fecha=strftime("%d/%m/%Y");
$hoy=strftime("%A"); 
$fecha_hoy= date("d/m/Y");
$fecha_hoy_bd= date("Y-m-d"); // Fecha mysql para comparar
$fecha_hoy_bd_completa=date("Y-m-d H:i:s"); // Es como NOW() de mysql  '2012-02-29 12:11:10'	
$fecha_hoy_bd_ano_mes= date("Y-m"); // Fecha mysql para comparar


switch ($hoy) {
   case lunes:
		$fecha1=$fecha;	
		$fecha2=suma_fechas(fecha_a_mysql("$fecha"),6);	
	   break;
   case martes:
		$fecha1=suma_fechas(fecha_a_mysql("$fecha"),-1);	
		$fecha2=suma_fechas(fecha_a_mysql("$fecha"),5);	
	   break;
   case miercoles:
		$fecha1=suma_fechas(fecha_a_mysql("$fecha"),-2);	
		$fecha2=suma_fechas(fecha_a_mysql("$fecha"),4);	
	   break;
   case jueves:
		$fecha1=suma_fechas(fecha_a_mysql("$fecha"),-3);	
		$fecha2=suma_fechas(fecha_a_mysql("$fecha"),3);	
	   break;
   case viernes:
		$fecha1=suma_fechas(fecha_a_mysql("$fecha"),-4);	
		$fecha2=suma_fechas(fecha_a_mysql("$fecha"),2);	
	   break;
   case sabado:
		$fecha1=suma_fechas(fecha_a_mysql("$fecha"),-5);	
		$fecha2=suma_fechas(fecha_a_mysql("$fecha"),1);	
	   break;		   
   case domingo:
		$fecha1=suma_fechas(fecha_a_mysql("$fecha"),-6);	
		$fecha2=suma_fechas(fecha_a_mysql("$fecha"),0);	
	   break;

}


function encrypt($Incoming)
	{
		$Codes = array("32", "33", "34", "35", "36", "37", "38", "39", "40", 
					"41", "42", "43", "44", "45", "46", "47", "48", "49", 
					"50", "51", "52", "53", "54", "55", "56", "57", "58", 
					"59", "60", "61", "62", "63", "64", "65", "66", "67", 
					"68", "69", "70", "71", "72", "73", "74", "75", "76", 
					"77", "78", "79", "80", "81", "82", "83", "84", "85", 
					"86", "87", "88", "89", "90", "91", "92", "93", "94", 
					"95", "96", "97", "98", "99", "100", "101", "102", "103", 
					"104", "105", "106", "107", "108", "109", "110", "111", 
					"112", "113", "114", "115", "116", "117", "118", "119", 
					"120", "121", "122", "123", "124", "125", "126");
		shuffle($Codes);
		$count = 0;
		for ( $i = 32; $i < 127; $i++ )
			{
				$CryptTable[$i] = $Codes[$count];
				$count++;
			}
		for ( $i = 0; $i < strlen($Incoming); $i++)
			{
				$NewString = $NewString . chr($CryptTable[ord(substr($Incoming, $i, 1))]);
			}
		for ( $i = 32; $i < 127; $i++ )
			{
				if ( $CryptTable[$i] < 100 )
					{
						$CryptTable[$i] = 'x' . $CryptTable[$i];
					}
				$Key = $Key . str_replace('0', 'x', $CryptTable[$i]);
			}
		$Keyed = substr($Key, 0, 144) . $NewString . substr($Key, -141);
		return($Keyed);
	}
	
function decrypt($incoming)
{
	$Key = substr($Incoming, 0, 144) . substr($Incoming, -141);
	$string = substr($Incoming, 144, ( strlen($Incoming) - 285 ));
	$count = 0;
	for ( $i = 32; $i < 127; $i++ )
		{
			$DeCryptTable[str_replace('x', '', substr($Key, $count, 3))] = $i;
			$count = $count + 3;
		}
	for ( $i = 0; $i < strlen($string); $i++ )
		{
			$NewString = $NewString . chr($DeCryptTable[ord(substr($string, $i, 1))]);
		}
	return($NewString);
}
function limpia_cad($cadena){
    //eliminamos los acentos
    $tofind = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
    $replac = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
    $cadena1 = strtr($cadena,$tofind,$replac);
     
    //eliminamos todo lo que no sean letras numeros o el punto de la extension
    $cadena2 = ereg_replace("[^._A-Za-z0-9]", "", $cadena1);
     
    //substituimos espacios blancos por un guion
    $cadena3 = str_replace(" ","-",$cadena2);
	$nombrearchivo = str_replace(array(
	"\x00", '*', '\\', '/', ':', '?', '¿', '<', '>', '|', '&', ',', ';', '~'
	), '', $cadena3);
	$nombrearchivo = str_replace(' ', '_', $nombrearchivo);
	if (strlen($nombrearchivo)>100){
		$nombrearchivo=substr($nombrearchivo, 0, 100);
	}	
    return($nombrearchivo);
}
function limpia_cad_upload($cadena){
    //eliminamos los acentos
	$cadena0 = explode(".",$cadena);
	$cadenaa=substr(strrchr($cadena0, '.'), 1);
    $tofind = "ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ";
    $replac = "AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn";
    $cadena1 = strtr($cadenaa,$tofind,$replac);
     
    //eliminamos todo lo que no sean letras numeros o el punto de la extension
    $cadena2 = str_replace(" ","-",$cadena1);
    $cadena3 = ereg_replace("[^._A-Za-z0-9]", "", $cadena2);
     
    //substituimos espacios blancos por un guion
	$nombrearchivo = str_replace(array(
	"\x00", '*', '\\', '/', ':', '?', '¿', '<', '>', '|', '&', ',', ';', '~'
	), '', $cadena3);
	if (strlen($nombrearchivo)>100){
		$nombrearchivo=substr($nombrearchivo, 0, 100);
	}	
    return($nombrearchivo);
}

function tildesmayusculas($string) 
{ 
  return str_replace(array('À','Á','Â','Ã','Ä',  'È','É','Ê','Ë', 'Ì','Í','Î','Ï', 'Ñ', 'Ò','Ó','Ô','Õ','Ö', 'Ù','Ú','Û','Ü', 'Ý'),array('à','á','â','ã','ä', 'è','é','ê','ë', 'ì','í','î','ï', 'ñ', 'ò','ó','ô','õ','ö', 'ù','ú','û','ü', 'ý') , $string); 
} 

// Recorta decimales si están a 0
function recortardecimales($float, $separacion="."){
	$partes = explode($separacion, $float);
	if ($partes[1]=="00"){
		return $partes[0];
	}
	else return $float;
	
}

function solonumeros($cadena){
	$cad=preg_replace('/[^0-9]/','',$cadena); 
	return($cad);
}
function separartags($tags) {
        $trozos=explode(",",$tags);
        $numero=count($trozos); 
        $i = 0;
        while($i <= $numero):
        echo '<a href="tags/'.$trozos[$i].'">'.$trozos[$i].'</a>';
        $i += 1;
        endwhile;
 }
 function logOut() {
	session_unset();
	session_destroy();
	session_start();
	session_regenerate_id(true);
}

function myTruncate($string, $limit, $break='.', $pad='...') { 
	if(strlen($string) <= $limit){ 
		return $string; 
	}
	
	if(false !== ($breakpoint = strpos($string, $break, $limit))) { 
		if($breakpoint < strlen($string) - 1) { 
			$string = substr($string, 0, $breakpoint) . $pad; 
		} 
	}
	 
	return $string; 
}

function getRealIP(){
	if ($_SERVER) {
		if ( $_SERVER[HTTP_X_FORWARDED_FOR] ) {

			$realip = $_SERVER["HTTP_X_FORWARDED_FOR"];

		} elseif ( $_SERVER["HTTP_CLIENT_IP"] ) {

			$realip = $_SERVER["HTTP_CLIENT_IP"];

		} else {

			$realip = $_SERVER["REMOTE_ADDR"];

		}

	} else {
		if ( getenv( 'HTTP_X_FORWARDED_FOR' ) ) {
			$realip = getenv( 'HTTP_X_FORWARDED_FOR' );

		} elseif ( getenv( 'HTTP_CLIENT_IP' ) ) {

			$realip = getenv( 'HTTP_CLIENT_IP' );

		} else {

			$realip = getenv( 'REMOTE_ADDR' );
		}

	}
	
	return $realip;
}





?>
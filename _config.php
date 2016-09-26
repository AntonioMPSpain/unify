<?

/** Rutas **/

$baseUrl = "https://".$_SERVER[HTTP_HOST]."/";
$path = "web/";
$wwwpath = "/var/www/".$path;


$templatepath = "templates/";
$backendpath = "backend/";
$moodlepath = "../moodle/";
$actividadpath = "actividad/";
$rsspath = "rss/";


$imgpath = "img/";
$imglogospath = $imgpath."logos/";
$imgcursospath = $imgpath."cursos/";
$imgbannerspath = $imgpath."banners/";
$imgcursosbackendpath = $backendpath."imagen/";


/** Includes **/

setlocale(LC_TIME, 'es_ES.UTF-8');
include_once "_cone.php";
include_once "_funciones.php";
include_once "_funciones_activatie.php";
include_once "_twig.php";
include_once "_twitter.php";


/** Fechas **/
$year = date("Y");



/** Colores **/

$coloractivatie = "#e74c3c";


?>
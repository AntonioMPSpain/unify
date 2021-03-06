<? 
include_once "_config.php";

// Make the page validate
ini_set('session.use_trans_sid', '0');

// Create a random string, leaving out 'o' to avoid confusion with '0'
$char = strtoupper(substr(str_shuffle('abcdefghjkmnpqrstuvwxyz'), 0, 4));

// Concatenate the random string onto the random numbers
// The font 'Anorexia' doesn't have a character for '8', so the numbers will only go up to 7
// '0' is left out to avoid confusion with 'O'
$str = rand(1, 7) . rand(1, 7) . $char;

// Begin the session
session_start();

// Set the session contents
$_SESSION['captcha_id'] = $str;

// Select colegios
$colegios = getColegios();

/** BREADSCRUMBS **/

$breadcrumbs = "";

$breadcrumb1["titulo"] = "Contacto";
$breadcrumbs["p1"] = $breadcrumb1;

/** FIN BREADCRUMBS **/

include ($templatepath."header.php");
$twig->display('contacto.php', array('colegios'=>$colegios,'breadcrumbs'=>$breadcrumbs));
include ($templatepath."footer.php");
?>
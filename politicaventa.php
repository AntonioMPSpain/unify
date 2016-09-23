<? 
include_once "_config.php";

/** BREADSCRUMBS **/

$breadcrumbs = "";

$breadcrumb1["titulo"] = "Política de venta";
$breadcrumbs["p1"] = $breadcrumb1;

/** FIN BREADCRUMBS **/

include ($templatepath."header.php");
$twig->display('politicaventa.php', array('breadcrumbs'=>$breadcrumbs));
include ($templatepath."footer.php");

?>

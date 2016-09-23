<? 
include_once "_config.php";

/** BREADSCRUMBS **/

$breadcrumbs = "";

$breadcrumb1["titulo"] = "Aviso legal";
$breadcrumbs["p1"] = $breadcrumb1;

/** FIN BREADCRUMBS **/

include ($templatepath."header.php");
$twig->display('avisolegal.php', array('breadcrumbs'=>$breadcrumbs));
include ($templatepath."footer.php");

?>

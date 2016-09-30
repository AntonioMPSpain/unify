<? 

include_once "_config.php";

$materiales = "";

$material["nombre"] = "Lámina de impermeabilización exterior Dry80 30";
$material["imagen"] = $imgmaterialespath."i1.jpg";
$material["marca"] = "REVESTECH";
$material["empresa"] = "REVESTECH";
$material["categoria"] = "Láminas impermeables";
$material["precio"] = "20";
$material["preciotachado"] = "30";
$material["estrellas"] = "5";
$material["nuevo"] = "0";

$materiales[0] = $material;


$material["nombre"] = "Banda de unión perimetral Dry80 Banda 50";
$material["imagen"] = $imgmaterialespath."i2.jpg";
$material["marca"] = "REVESTECH";
$material["empresa"] = "REVESTECH";
$material["categoria"] = "Láminas impermeables";
$material["precio"] = "0";
$material["preciotachado"] = "0";
$material["estrellas"] = "2";
$material["nuevo"] = "0";

$materiales[1] = $material;



$material["nombre"] = "Chimenea de ventilación para cubiertas";
$material["imagen"] = $imgmaterialespath."i3.jpg";
$material["marca"] = "REVESTECH";
$material["empresa"] = "REVESTECH";
$material["categoria"] = "Conductos y chimeneas";
$material["precio"] = "155";
$material["preciotachado"] = "0";
$material["estrellas"] = "4";
$material["nuevo"] = "1";

$materiales[2] = $material;


$material["nombre"] = "Dry50 Sumisquare 144";
$material["imagen"] = $imgmaterialespath."i4.jpg";
$material["marca"] = "REVESTECH";
$material["empresa"] = "REVESTECH";
$material["categoria"] = "Canales y rejillas";
$material["precio"] = "123";
$material["preciotachado"] = "999";
$material["estrellas"] = "3";
$material["nuevo"] = "1";

$materiales[3] = $material;

$material["nombre"] = "Dry80 Cornerin";
$material["imagen"] = $imgmaterialespath."i5.jpg";
$material["marca"] = "REVESTECH";
$material["empresa"] = "REVESTECH";
$material["categoria"] = "Láminas impermeables";
$material["precio"] = "1";
$material["preciotachado"] = "0";
$material["estrellas"] = "5";
$material["nuevo"] = "0";

$materiales[4] = $material;


$material["nombre"] = "Parahojas";
$material["imagen"] = $imgmaterialespath."i6.jpg";
$material["marca"] = "REVESTECH";
$material["empresa"] = "REVESTECH";
$material["categoria"] = "";
$material["precio"] = "11111";
$material["preciotachado"] = "222222";
$material["estrellas"] = "1";
$material["nuevo"] = "0";

$materiales[5] = $material;

$material["nombre"] = "Adhesivo especial para juntas Seal Plus 6";
$material["imagen"] = $imgmaterialespath."i7.jpg";
$material["marca"] = "REVESTECH";
$material["empresa"] = "REVESTECH";
$material["categoria"] = "Adhesivos";
$material["precio"] = "0";
$material["preciotachado"] = "0";
$material["estrellas"] = "1";
$material["nuevo"] = "1";

$materiales[6] = $material;



/** BANNERS **/
include_once($backendpath."p_funciones.php"); 
$idbanner = 4;
$banner1 = getBanner($idbanner);
$idbanner = 6;
$banner2 = getBanner($idbanner);
/** FIN BANNERS **/

/** BREADSCRUMBS **/

$breadcrumbs = "";

$breadcrumb1["titulo"] = "Materiales";
$breadcrumbs["p1"] = $breadcrumb1;

/** FIN BREADCRUMBS **/

include ($templatepath."header.php");
$twig->display('materiales.php', array('materiales'=>$materiales, 'etiquetas'=>$etiquetas ,'breadcrumbs'=>$breadcrumbs,'banner1'=>$banner1, 'banner2'=>$banner2));
include ($templatepath."footer.php");



  

?>
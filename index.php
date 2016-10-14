<?

include_once "_config.php";
include_once "_funciones.php";
include ($templatepath."header.php");

$salir=$_GET['salir']; 
if ($salir==true){
	header("Location: _control-user.php?logout");
	exit();
}
	
/** BANNERS **/
include_once($backendpath."p_funciones.php"); 
$idbanner = 1;
$banner1 = getBanner($idbanner);
$idbanner = 3;
$banner2 = getBanner($idbanner);
/** FIN BANNERS **/

include ($templatepath."slider.php");
//include ("index-formacion.php");
include ("index-navegacion.php");

?>

<!--=== Content ===-->
<div class="container">
	<div class="margin-bottom-20 margin-top-20 col-md-offset-2"><?=$banner2?></div>
</div>
<!--=== End Content ===-->

<?

include ($templatepath."footer.php");

?>
<?
include("_funciones.php"); 
include("_cone.php"); 

$safe="Informes";

$titulo1="informes ";
$titulo2="activatie";

////////// Filtros de nivel por usuario //////////////////////
session_start(); 
if ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
//		$sql="  (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy admin de colegio";
	}else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
}else{
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

include("plantillaweb01admin.php"); 
?>
<!--Arriba plantilla1-->
<h2 class="titulonoticia">Resumen espacio libre en servidor</h2>
<div class="bloque-lateral acciones">		
	<p>
		<a href="zona-privada_admin_informes_1.php" class="btn btn-success" type="button"> Volver <i class="icon-circle-arrow-left"></i></a> |
	</p>
</div>
<? include("_aya_mensaje_session.php"); 
		
		echo date("r");
	
        $totalGB = disk_total_space("/") / 1024 / 1024 / 1024;
        $restanteGB = disk_free_space("/") / 1024 / 1024 / 1024;
      
	    $body = "<br><br>";
 
		$body.= "TOTAL: ". number_format($totalGB, 2, '.', ',') . " GB<br><br>";
		$body.= "RESTANTE: <b>". number_format($restanteGB, 2, '.', ',') . " GB</b><br><br>";
		
		$body.= "PORCENTAJE LIBRE: ". number_format((($restanteGB/$totalGB) * 100), 2, '.',',') ."%<br>";

		echo $body; 
	
	
	?>
	
	

		<div id="volverarriba">
			<hr />
			<a href="#" title="Volver al inicio de la página">Volver arriba <i class="icon-circle-arrow-up"></i></a>
		</div>
		<br />
	</div>
	<!--fin pagina-->
<div class="clearfix"></div>
</div>
<!--fin contenido-principal-->
<?

include("plantillaweb02admin.php"); 
?>
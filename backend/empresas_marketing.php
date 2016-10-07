<?



include("_funciones.php"); 
include("_cone.php");

////////// Filtros de nivel por usuario //////////////////////
session_start();

if ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$idusuario = $idcolegio;
		$sqlcolegio="";
		$sqlcolegio = " AND idcurso IN (SELECT id FROM curso WHERE idcolegio='$idcolegio') ";
	}else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
	$sqlcolegio="";
}
else{
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}
////////// FIN Filtros de nivel por usuario ////////////////////// 



$titulo1="marketing";
$titulo2="empresas";
$safe="Marketing Empresas";
include("plantillaweb01admin.php");

?>

	<h2 class="titulonoticia"><?=$safe?></h2>
	<br>
	<div class="bloque-lateral acciones">		
		<p>
			<a href="empresas_nueva.php" class="btn btn-success">Nueva empresa <i class="icon-plus"></i></a>
			
			<a href="empresas_marketing.php?xls" class="btn btn-success">Descargar Excel <i class="icon-book"></i></a>
		</p>
	</div>
	
	<div class="bloque-lateral buscador">		
		<h4>Buscar empresa</h4>
		<form action="empresas_marketing.php?accion=buscar" method="post">
			<fieldset>
	    		<div class="input-append">
 				<input type="text" class="span5" id="terminobusqueda" name="texto" placeholder="CIF, Nombre" value="<?=$texto?>" />
					<input class="btn" type="submit" value="Buscar" />
					<a class="busqueda-avanzada-link" href="empresas_busqueda_avanzada.php">Búsqueda Avanzada <i class="icon-search"></i></a>
				
			</div>		
		    </fieldset>
	    </form>
	</div>
	<br>
	<!--fin buscador-->
		
	<h2>Empresas</h2>
	
	<table class="align-center" border="0" cellpadding="0" cellspacing="0">
		<tbody><tr>
			<th>Nombre</th>
			<th>Familias</th>
			<th>Persona contacto</th>
			<th>Dirección</th>
			<th>Localidad</th>
			<th>Provincia</th>
			<th>Teléfono</th>
			<th>Teléfono </th>
			<th>Email</th>
			<th>Email</th>
			<th>Web</th>
			<th>Colegio contacto</th>
			<th>Trámites realizados</th>
			<th>Contestaciones</th>
			<th width='20%'>Acciones</th>
		</tr>
		<tr>
			<td>A</td>
			<td>B</td>
			<td>C</td> 
			<td>D</td>
			<td>E</td>
			<td>F</td>
		</tr>
	
	</table>
	<?

include("plantillaweb02admin.php"); 
?>
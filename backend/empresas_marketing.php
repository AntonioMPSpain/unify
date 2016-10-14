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
			<th>Email</th>
			<th>Web</th>
			<th>Colegio</th>
			<th>Notas</th>
			<th>Acciones</th>
		</tr>
		<tr>
			<td>Revestech</td>
			<td>Aislamiento<br>
				Iluminación
			</td>
			<td>Antonio</td> 
			<td>C/ Falsa 123</td>
			<td>Murcia</td>
			<td>Murcia</td>
			<td>666111222</td>
			<td>asd@asd.com</td>
			<td>www.falsa.com</td>
			<td>COAATIE_Murcia</td>
			<td><a>1</a></td>
			<td><a href="empresas_nueva.php?accion=editar&amp;id=691375&amp;idmoodleduplica=225" class="btn btn-primary">editar</a></td>
			
		</tr>
	<tr>
			<td>ACTIU Logísitica</td>
			<td>Revestimientos<br>
				Invernaderos
			</td>
			<td>Jose</td> 
			<td>C/ Verdadera 123</td>
			<td>Alicante</td>
			<td>Alicante</td>
			<td>666111222</td>
			<td>qwerty@qwerty.com</td>
			<td>www.verdadera.com</td>
			<td>COAATIE_Alicante</td>
			<td><a>3</a></td>
			<td><a href="empresas_nueva.php?accion=editar&amp;id=691375&amp;idmoodleduplica=225" class="btn btn-primary">editar</a></td>
			
		</tr>
		<tr>
			<td>Yantram Animation Studio</td>
			<td>Aislamiento<br>
				Iluminación<br>
				Sillones<br>
				Sillas de espera<br>
				Archivo<br>
				Mesas de oficina
			</td>
			<td>Antonio</td> 
			<td>C/ Falsa 123</td>
			<td>Gandía</td>
			<td>País Valencià</td>
			<td>789798798</td>
			<td>asd@asd.com</td>
			<td>www.falsa.com</td>
			<td>COAATIE_Valencia</td>
			<td><a>1</a></td>
			<td><a href="empresas_nueva.php?accion=editar&amp;id=691375&amp;idmoodleduplica=225" class="btn btn-primary">editar</a></td>
			
		</tr>
		
	</table>
	<?

include("plantillaweb02admin.php"); 
?>
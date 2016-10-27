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
	$idcolegio = 0;
	$sqlcolegio="";
}
else{
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}
////////// FIN Filtros de nivel por usuario ////////////////////// 


$idempresa = $_REQUEST['id'];

if ((isset($_GET['eliminar'])) && ($idempresa>0)){
	$sql = "UPDATE empresas_marketing SET borrado=1 WHERE id='$idempresa'";
	posgre_query($sql);
	header ('Location: empresas_marketing.php');
	exit();
}


$texto = $_REQUEST['texto'];

$sqlbusqueda="";
if ($texto!=""){
	$sqlbusqueda = " AND (nombre LIKE '%$texto%' OR CIF LIKE '%texto%')";
}

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
			
			<!--<a href="empresas_marketing.php?xls" class="btn btn-success">Descargar Excel <i class="icon-book"></i></a>-->
		</p>
	</div>
	
	<div class="bloque-lateral buscador">		
		<h4>Buscar empresa</h4>
		<form action="empresas_marketing.php?accion=buscar" method="post">
			<fieldset>
	    		<div class="input-append">
 				<input type="text" class="span5" id="terminobusqueda" name="texto" placeholder="CIF, Nombre" value="<?=$texto?>" />
					<input class="btn" type="submit" value="Buscar" />
					<!--<a class="busqueda-avanzada-link" href="empresas_busqueda_avanzada.php">Búsqueda Avanzada <i class="icon-search"></i></a>-->
				
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
			<th>CIF</th>
			<th>Localidad</th>
			<th>Provincia</th>
			<th>Persona contacto</th>
			<th>Teléfono</th>
			<th>Email</th>
			<th>Añadido por</th>
			<th>Familias</th>
			<th>Comentarios</th>
			<th>Acciones</th>
		</tr>
		
		
		
		<?
		
		$sql = "SELECT * FROM empresas_marketing WHERE borrado=0 $sqlbusqueda ORDER BY id DESC";
		$result = posgre_query($sql);
		while ($row = pg_fetch_array($result)){
			$id = $row['id'];
			$nombre = $row['nombre'];
			$cif = $row['cif'];
			$domicilio = $row['domicilio'];
			$localidad = $row['localidad'];
			$cp = $row['cp'];
			$provincia = $row['provincia'];
			$persona = $row['persona'];
			$email = $row['email'];
			$movil = $row['movil'];
			$telefono = $row['telefono'];
			$fax = $row['fax'];
			$web = $row['web'];
			$fechainserccion = cambiaf_a_normal($row['fecha']);
			$idcolegiocreador = $row['idcolegio'];
			
			if ($idcolegiocreador==0){
				$colegiocreador="Admin";
			}
			else{
				
				$sql = "SELECT nombre FROM usuario WHERE id='$idcolegiocreador'";
				$result4 = posgre_query($sql);
				if ($row4 = pg_fetch_array($result4)){
					$colegiocreador=$row4['nombre'];
				}
			}
		
			?>
		
			
			<tr>
				<td><?=$nombre?></td>
				<td><?=$cif?></td>
				<td><?=$localidad?></td>
				<td><?=$provincia?></td>
				<td><?=$persona?></td> 
				<td><?=$movil?></td>
				<td><?=$email?></td>
				<td><?=$colegiocreador?></td>
				<td>
				<?
					$familias="";
					$sql2 = "SELECT * FROM empresas_marketing_familias WHERE idempresa='$id'";
					$result2 = posgre_query($sql2);
						echo pg_last_error();
					while ($row2 = pg_fetch_array($result2)){
						$idfamilia = $row2['idfamilia'];		
						$sql3 = "SELECT * FROM materiales_familias WHERE id='$idfamilia'";	
						echo pg_last_error();
						$result3 = posgre_query($sql3);
						
						if ($row3 = pg_fetch_array($result3)){
							$familias .= $row3['nombre']."<br>";
						}
					}
					
					echo $familias; 
				?>
					
				</td>
				<td>
				<?
					$sql2 = "SELECT * FROM empresas_marketing_comentarios WHERE idempresa='$id' AND borrado=0";
					$result2 = posgre_query($sql2);
					$numComentarios = pg_num_rows($result2);	
					
				?>
					
					
					<a href="empresas_nueva.php?id=<?=$id?>#comentarios"><?=$numComentarios?></a>
				</td>
				<td>
					<? if (($idcolegio==0)||($idcolegio == $idcolegiocreador)){ ?> 
					
						<a href="empresas_nueva.php?id=<?=$id?>" class="btn btn-primary">editar</a>
						<a onclick="return confirm('Estas seguro?')" href="empresas_marketing.php?eliminar&id=<?=$id?>" class="btn btn-primary">eliminar</a>
					<? } ?>
				</td>
				
			</tr>
		
		<? } ?>
		
		
	</table>
	<?

include("plantillaweb02admin.php"); 
?>
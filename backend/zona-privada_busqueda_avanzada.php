<?
include("_funciones.php"); 
include("_cone.php"); 
////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==4) { //Alumno
	echo "Error: aqui no deberia entrar.";
	exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo los alumnos de su colegio
	echo "Error: profe aqui no deberia entrar.";
	exit();
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$sql="  (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy admin de colegio";
	}else{
		echo "Error de sesion2";
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
}else{
	echo "Error: aqui no deberia entrar.";
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

$accion=strip_tags($_GET['accion']); 
$a = $_GET["a"];

if ($a == "facturacion"){
	$link = "a_facturacion_curso.php";
}
elseif ($a == "informe"){
	$link = "informe_curso.php";
}
else{
	$link = "zona-privada_admin_cursos_1.php";
}

$est=strip_tags($_GET['est']);
$titulo1="formación ";
$titulo2="administración";

$migas = array();
$migas[] = array('#', 'Búsqueda avanzada');
 
//########################################## RECOGIDA DE PARAMETOS #####################################################
$sqlbuscar="";

$nombre="";
$nombre=(strip_tags($_POST['nombre']));
if ($nombre<>""){
	$sqlbuscar=$sqlbuscar." AND (sp_asciipp(nombre) ILIKE sp_asciipp('%$nombre%')) ";
}

$estado="";
$estado=strip_tags($_POST['estado']); 
if ($estado<>""){
	
	if ($estado > 20){
		$tipo = $estado[1];
		
		if ($tipo==1){ 		// Pre curso
			$sqlbuscar=$sqlbuscar." AND (modalidad!=3 AND fecha_inicio>NOW()) ";
		}
		elseif($tipo==2){	// En curso
			$sqlbuscar=$sqlbuscar." AND ((modalidad=3) OR (modalidad!=3 AND fecha_inicio<=NOW() AND fecha_fin>=NOW())) ";
		}
		elseif($tipo==3){	// Finalizado
			$sqlbuscar=$sqlbuscar." AND (modalidad!=3 AND fecha_fin<NOW()) ";
		}
		
		
		
		
		$estado=2;
		
	}
	
	$sqlbuscar=$sqlbuscar." AND (estado='$estado') ";
}

$idreferencia=strip_tags($_POST['idreferencia']); 
if ($idreferencia<>""){
	$porciones = explode("/", $idreferencia);
	$ref = $porciones[0];
	//$sqlbuscar=$sqlbuscar." AND (id = '$porciones[0]') ";
	$sqlbuscar=$sqlbuscar." AND id = '$ref' ";
	
}

$anno=strip_tags($_POST['anno']); 
if ($anno<>""){
	//$sqlbuscar=$sqlbuscar." AND (fecha_inicio LIKE '%$anno%') ";
	$sqlbuscar=$sqlbuscar." AND (((date_part('year', fecha_publicacion)  = '$anno') AND (modalidad=3)) OR ((date_part('year', fecha_inicio)  = '$anno') AND (modalidad<>3))) ";
	//date_part(‘year’, start_date)
}
//fechas update
$fecha_inicio=$_POST['fecha_inicio'];
if ($fecha_inicio=="") $fecha_inicio="NULL"; 
if ($fecha_inicio<>"NULL"){
	
	if ($fecha_inicio[5]=="/"){
		$fechas = explode("/", $fecha_inicio);
		$fecha_inicio = $fechas[2]."-".$fechas[1]."-".$fechas[0];
	}
	
	$sqlbuscar=$sqlbuscar." AND (fecha_inicio>='$fecha_inicio') ";
}

$fecha_fin=$_POST['fecha_fin'];
if ($fecha_fin=="") $fecha_fin="NULL"; 
if ($fecha_fin<>"NULL"){
	
	if ($fecha_fin[5]=="/"){
		$fechas = explode("/", $fecha_fin);
		$fecha_fin = $fechas[2]."-".$fechas[1]."-".$fechas[0];
	}
	
	$sqlbuscar=$sqlbuscar." AND (fecha_inicio<='$fecha_fin') ";
}

//fin fechas

$tipo=trim(strip_tags($_POST['tipo'])); 
if ($tipo<>""){
	$sqlbuscar=$sqlbuscar." AND (tipo='$tipo') ";
}

$modalidad=strip_tags($_POST['modalidad']);
if ($modalidad<>""){
	$sqlbuscar=$sqlbuscar." AND (modalidad='$modalidad') ";
}

$organizador=strip_tags($_POST['organizador']);
if ($organizador<>""){
	$sqlbuscar=$sqlbuscar." AND (idcolegio='$organizador') ";
}

//########################################## FIN RECOGIDA DE PARAMETOS #####################################################
if ($accion=="buscar"){
	$sqlbuscar = urlencode($sqlbuscar);
	header("Location: $link?avanzada=$sqlbuscar");
	exit();
	
}

include("plantillaweb01admin.php"); 
?>


</script> 
<!------------Arriba pantilla1---------->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
		<h2 class="titulonoticia">Cursos</h2>
		<? include("_aya_mensaje_session.php"); ?>
		<br />
		<!--Acciones-->
		<div class="bloque-lateral acciones">		
			<p>
				<a href="<?=$link?>" class="btn btn-success" type="button">Volver </a> 

			</p>
		</div>
		<!--fin acciones-->
		<div class="bloque-lateral buscador">		
			<h4>Buscador avanzado de cursos</h4>
	
			<form action="zona-privada_busqueda_avanzada.php?accion=buscar&a=<?=$a?>" method="post" enctype="multipart/form-data">
				<fieldset>
					<label>Referencia:</label>
					<input type="text" id="texto" name="idreferencia" class="input-small" value="<?=$idreferencia?>"  />
					<label>Título:</label>
					<input type="text" id="texto" name="nombre" class="input-xxlarge" placeholder="Título" value="<?=$nombre?>"  />
					<label>Año</label>
					<input type="number" id="2" class="input-small" name="anno" min="2015" value="<?=$anno?>" />
					
					<label>Desde(no permanentes):</label>
					<input type="date" id="2" class="input" name="fecha_inicio" placeholder="01/01/2000" value="<?=$fecha_inicio?>" />
					<label>Hasta(no permanentes):</label>
					<input type="date" id="d2" class="input" name="fecha_fin" placeholder="01/01/2000" value="<?=$fecha_fin?>" />
					
					<label>Organizador:</label>
						<select name="organizador" class="input-xlarge" >
							<option class="input-xlarge" value="" <? if ($organizador=='') echo " selected "; ?>>[-seleccione-]</option>
						
							<?
							$sql = "SELECT * FROM usuario WHERE nivel=2 AND borrado=0";
							$result = posgre_query($sql);
							while ($row = pg_fetch_array($result)){
								$idcolegio = $row['id'];
								$nombrecolegio = $row['nombre'];
								?> <option class="input-xlarge" value="<?=$idcolegio?>" ><?=$nombrecolegio?></option> <?
							}
							?>

						</select>
					<label>Estado:</label>
						<select name="estado" class="input-xlarge" >
							<option class="input-xlarge" value="" <? if ($estado=='') echo " selected "; ?>>[-seleccione-]</option>
							<option class="input-xlarge" value="2" <? if ($estado==2) echo " selected "; ?>>Abierto</option>
							<option class="input-xlarge" value="21" <? if ($estado==21) echo " selected "; ?>>  - Pre curso</option>
							<option class="input-xlarge" value="22" <? if ($estado==22) echo " selected "; ?>>  - En curso</option>
							<option class="input-xlarge" value="23" <? if ($estado==23) echo " selected "; ?>>  - Finalizado</option>
							<option class="input-xlarge" value="1" <? if ($estado==1) echo " selected "; ?>>Cerrado</option>
							<option class="input-xlarge" value="0" <? if ($estado=='0') echo " selected "; ?>>Oculto</option>
							<option class="input-xlarge" value="5" <? if ($estado=='5') echo " selected "; ?>>Cancelado</option>
						</select>
					<? /*<label>Áreas:</label>
						<select name="area" class="input-xlarge" >
							<option class="input-xlarge" placeholder="2015" value="">[-seleccione-]</option>
						<?
						// Generar listado 
							$consulta = "SELECT * FROM etiqueta WHERE borrado = 0 ORDER BY texto,id;";
							$link=iConectarse(); 
							$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
							while($rowdg= pg_fetch_array($r_datos)) {	
								?>
								<option class="input-large" value="<?=$rowdg['id']?>"<? if (($idetiqueta == $rowdg['id'])&&($idetiqueta <> "")) { echo " selected "; } ?>><? echo ($rowdg['texto']); ?></option>
								<? 
							} ?>
						</select>*/?>	
					<label>Modalidad:</label>
						<select name="modalidad" class="input-xlarge" >
							<option class="input-xlarge" value="" <? echo " selected "; ?>>[-seleccione-]</option>
							<option class="input-xlarge" value="0" >on-line</option>
							<option class="input-xlarge" value="1" <? if ($modalidad==1) echo " selected "; ?>>presencial</option>
							<option class="input-xlarge" value="2" <? if ($modalidad==2) echo " selected "; ?>>presencial y on-line</option>
							<option class="input-xlarge" value="3" <? if ($modalidad==3) echo " selected "; ?>>permanente</option>
						</select>

					<label>Tipo:</label>
						<select name="tipo" class="input-xlarge" >
							<option class="input-xlarge" value="">[-seleccione-]</option>
							<option class="input-xlarge" value="0" <? if ($tipo=='0') echo " selected "; ?>>Curso</option>
							<option class="input-xlarge" value="1" <? if ($tipo==1) echo " selected "; ?>>Curso universitario</option>
							<option class="input-xlarge" value="2" <? if ($tipo==2) echo " selected "; ?>>Taller</option>
							<option class="input-xlarge" value="3" <? if ($tipo==3) echo " selected "; ?>>Seminario</option>
							<option class="input-xlarge" value="4" <? if ($tipo==4) echo " selected "; ?>>Jornada</option>
						</select>
					<hr />
					<button type="submit" class="btn btn-important">Buscar</button>
				</fieldset> 
			</form>
		</div>
		<!--fin buscador-->



	</div>
	<!--fin pagina-->
<div class="clearfix"></div>
</div>
<!--fin contenido-principal-->
<?
include("plantillaweb02admin.php"); 
?>
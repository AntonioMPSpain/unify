<?php
include("_funciones.php"); 
include("_cone.php"); 
$safe="Sistema de calidad";
$titulo1="sistema";
$titulo2="calidad";

////////// Filtros de nivel por usuario //////////////////////
session_start();
if ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$sqlcolegio=" AND idcolegio='$idcolegio' ";
	}else{
		$_SESSION[error]="No dispone de permisos";	
		header("Location: index.php?error=true&est=ko#1");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
	$sqlcolegio="";
	$idcolegio=0;
}else{
	$_SESSION[error]="No dispone de permisos";	
	header("Location: index.php?error=true&est=ko#1");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////


$accion=strip_tags($_GET['accion']); 
$id=strip_tags($_GET['id']);
$idcurso = $_REQUEST['idcurso']; 
$idprofesor = $_REQUEST['idprofesor']; 

function getNinforme(){
	
	$i=1;
	while (true){
	
		$sql = "SELECT * FROM sc_noconformidades WHERE borrado=0 AND ninforme='$i' ORDER BY ninforme";
		$result = posgre_query($sql);

		if (pg_num_rows($result)==0){
			return $i;
		}
		
		$i++;	
		

	}
	
}



if ($accion=="guardar"){
		
	$detectadapor=($_POST['detectadapor']);
	$fecha=($_POST['fecha']);
	$area=($_POST['area']);
	$analisis=($_POST['analisis']);
	$solucion=($_POST['solucion']);
	$correctora=($_POST['correctora']);
	$preventiva=($_POST['preventiva']);
	$responsable=($_POST['responsable']);
	$revision=($_POST['revision']);
	$idcurso = $_POST['idcurso'];
	$ninforme = getNinforme();
	
	
	
	
	if ($idprofesor==""){ $idprofesor=0; }
	if ($idcurso==""){ $idcurso=0; }
		
	if ($id>0){				// UPDATE
		$Query = posgre_query("UPDATE sc_noconformidades SET idcolegio='$idcolegio' ,detectadapor = '$detectadapor', fecha = '$fecha', area = '$area', analisis = '$analisis', solucion = '$solucion', correctora = '$correctora', preventiva = '$preventiva', responsable = '$responsable', revision = '$revision' WHERE id ='$id'; "); 
	}
	else{					// INSERT
		$Query = posgre_query("INSERT INTO sc_noconformidades (idprofesor, ninforme, idcurso, idcolegio, detectadapor, fecha, area, analisis, solucion, correctora, preventiva, responsable, revision) VALUES ('$idprofesor','$ninforme','$idcurso','$idcolegio','$detectadapor', '$fecha', '$area', '$analisis', '$solucion', '$correctora', '$preventiva', '$responsable', '$revision');");
	}
	
	
	if ($Query){
		$_SESSION[esterror]="Guardado correctamente";	
	}
	else{
		
		$_SESSION[esterror]="Fallo al guardar".pg_last_error();	
	}
	
	header("Location: sc_noconformidades.php");
	exit();
	
}

if ($id>0){
	
	$sql = "SELECT * FROM sc_noconformidades WHERE borrado=0 AND id='$id' $sqlcolegio";
	$result = posgre_query($sql);
	if ($row = pg_fetch_array($result)){
		
		$detectadapor=($row['detectadapor']);
		$fecha=($row['fecha']);
		$area=($row['area']);
		$analisis=($row['analisis']);
		$solucion=($row['solucion']);
		$correctora=($row['correctora']);
		$preventiva=($row['preventiva']);
		$responsable=($row['responsable']);
		$revision=($row['revision']);
		$idcurso=($row['idcurso']);
		$idprofesor=($row['idprofesor']);
		$ninforme = $row['ninforme'];
		
	}
}
else{
	$fecha = date("Y-m-d"); 
}

if (($idprofesor<>"")&&($idprofesor<>0)){
	$sql = "SELECT * FROM usuario WHERE id='$idprofesor'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$nombreprofesor = $row['nombre']." ".$row["apellidos"];
}

if (($idcurso<>"")&&($idcurso<>0)){
	$sql = "SELECT * FROM curso WHERE id='$idcurso'";
	$result = posgre_query($sql);
	$row = pg_fetch_array($result);
	$nombrecurso = $row['nombre'];
}

include("plantillaweb01admin.php");

?>


<!--Arriba plantilla1-->
<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia"><?=$safe?></h2>
		<br />
		<? include("_aya_mensaje_session.php"); ?>



		<div class="bloque-lateral acciones">		
			<p>
				<a href="sc_noconformidades.php" class="btn btn-success" type="button">Volver</a>
			</p>
		</div>
		<br>
		<? if ($id>0){ ?>
			<legend><strong>NºInforme <?=$ninforme?></strong></legend>
		<? } else { ?>
			<legend><strong>PG-04-F-01 Informe de no conformidades, acciones correctivas y/o preventivas</strong></legend>
		<? } ?>
		
		<form id="formcontacto" method="post" action="sc_noconformidad.php?accion=guardar&id=<?=$id?>" enctype="multipart/form-data" >
		<h3>Descripci&oacute;n de la no conformidad</h3>			
		
		<p>
			<div class="control-group">
				<label class="control-label" for="curso">Curso(rellenar lo primero):</label>
					<div class="controls">
						<input disabled type="text" id="curso" class="input-xxlarge" name="curso" value="<?=$nombrecurso?>"/><? if ($idcurso=="") { ?> <a class="btn btn-primary" href="sc_curso.php">Seleccionar curso</a> <? } ?>
						<input type="hidden" id="idcurso" name="idcurso" value="<?=$idcurso?>"/>						
						<input type="hidden" id="id" name="id" value="<?=$id?>"/>
						
					</div>
			</div>
		</p>
		
		<? if (($idprofesor<>0)&&($idprofesor<>"")){ ?>
			<p>
				<label class="control-label" for="idprofesor">Profesor:</label>
				<input disabled type="text" id="profesor" class="input-xxlarge" name="profesor" value="<?=$nombreprofesor?>"/>
				<input type="hidden" id="idprofesor" name="idprofesor" value="<?=$idprofesor?>"/>
			</p>
	<? } ?>
		
		<p>
			<label class="description" for="detectadapor">Detectada por:<br />
				<input id="detectadapor" name="detectadapor" type="text" maxlength="255"  size="80" class="inputtextarea input-xxlarge" value="<?=$detectadapor?>" />  
			</label>
		</p>
		<p>
			<label class="description" for="fecha">Fecha de detecci&oacute;n:<br />
				<input id="fecha" name="fecha" type="date" value="<?=$fecha?>"  />  
			</label>
		</p>

		<p>
			<label class="description" for="area">&Aacute;rea trabajo:<br />
				<input id="area" name="area" type="text" class="inputtextarea input-xxlarge" value="<?=$area?>" />  
			</label>
		</p>
		<p>
			<label class="description" for="analisis">Descripci&oacute;n y / o Investigación completa de la incidencia (An&aacute;lisis de causas):<br />
				<textarea style="width:600px;" id="analisis" name="analisis" rows="5" cols="95" class="inputtextarea" ><?=$analisis?></textarea> 
			</label>
		</p>
		<p>
			<label class="description" for="solucion">Soluci&oacute;n inmediata y su seguimiento:<br />
				<textarea style="width:600px;" id="solucion" name="solucion" rows="5" cols="95" class="inputtextarea" ><?=$solucion?></textarea> 
			</label>
		</p>
		
		<h3>Acci&oacute;n a tomar</h3>			
						
		<p>
			<label class="description" for="correctora">Acción correctora:<br />
				<select name="correctora">
				  <option <? if ($correctora==0){ echo 'selected'; }?> value='0'>Si</option>
				  <option <? if ($correctora==1){ echo 'selected'; }?> value='1'>No</option>
				</select>									
			</label>
		</p>
		<p>
			<label class="description" for="preventiva">Acción preventiva:<br />
				<select name="preventiva">
				  <option <? if ($preventiva==0){ echo 'selected'; }?> value='0'>Si</option>
				  <option <? if ($preventiva==1){ echo 'selected'; }?> value='1'>No</option>
				</select>									
			</label>
		</p>
		<p>
			<label class="description" for="responsable">Responsable de llevarla a cabo:<br />
				<textarea style="width:600px;" id="responsable" name="responsable" rows="5" cols="45" class="inputtextarea" ><?=$responsable?></textarea> 
			</label>
		</p>
		<p>
			<label class="description" for="preventiva">Revisión de cierre:<br />
				<select name="revision">
				  <option <? if ($revision==0){ echo 'selected'; }?> value='0'>Abierta</option>
				  <option <? if ($revision==1){ echo 'selected'; }?> value='1'>Cerrada</option>
				</select>									
			</label>
		</p>
		<p>
			<label class="description" for="ostos">
				<input class="btn btn-primary btn-large" name="enviar" value="Guardar" type="submit" />
			</label>
		</p>
		</form>					
		
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
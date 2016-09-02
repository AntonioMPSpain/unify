<?
session_start();

$idcurso=strip_tags($_REQUEST['idcurso']); 
$cursodual=strip_tags($_REQUEST['cursodual']);
if ($idcurso=="") {
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}

////////// Filtros de nivel por usuario //////////////////////
if ($_SESSION[nivel]==4) { //Alumno
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}elseif ($_SESSION[nivel]==3) { //Profe puede ver solo los alumnos de su colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		//$sql=" (idcolegio='$idcolegio') AND ";
		$textoinfo="Soy profe";
	}else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		//$sql="  (idcolegio='$idcolegio') AND ";
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

include("_funciones.php"); 
include("_cone.php"); 

$safe="Resumen de inscritos";
$titulo1="informe ";
$titulo2="cursos";

$link=iConectarse();
$sql = "SELECT * FROM curso WHERE id='$idcurso'";
$result = posgre_query($sql);
$row = pg_fetch_array($result);
$nombrecurso = $row['nombre'];
$modalidad = $row['modalidad'];
$plazas=$row["plazas"];
$plazaso=$row["plazaso"];
$plazasperma=$row["plazasperma"];

switch($modalidad) { 
  case "0": $plazas=$plazaso; break; //" on-line ";
  case "1": $plazas; break; //" presencial ";
  case "2": {if ($cursodual==1){$plazas=$plazaso;}else{$plazas;}}break; // " presencial y on-line ";
  case "3": $plazas=$plazasperma; break; //" permanente ";
}

include("plantillaweb01admin.php"); 
?>


<!--Arriba plantilla1-->
<div class="grid-9 contenido-principal">
<div class="clearfix"></div>
<div class="pagina zonaprivada blog">
<h2 class="titulonoticia">Resumen inscritos en <?=$nombrecurso?></h2>

<div class="bloque-lateral acciones">		
	<p>
		<a href="informe_curso.php" class="btn btn-success" type="button"> Volver <i class="icon-circle-arrow-left"></i></a> |
	</p>
</div>

<br />
<span>Plazas: <?=$plazas?></span><br>

<h3>Inscritos</h3>
<TABLE > 
	<TR>
		<th>Estado</th>
		<th>Número</th>
		<th>% número</th>
	</TR> 
	
	<?
	
	if ($modalidad==2){
		if ($cursodual==1){	// Saca modo online
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE  (modalidad=2) AND inscripciononlinepresencial=2 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0  ") ;//or die (pg_error());  
		}
		else{				// Presencial
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE (modalidad=2) AND inscripciononlinepresencial=1 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0  ") ;//or die (pg_error());  
		}
		$totaltotal_registros = pg_num_rows($result); 
	}else{
		$result=pg_query($link,"SELECT * FROM curso_usuario WHERE (modalidad<>2) AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0  ") ;//or die (pg_error());  
		$totaltotal_registros = pg_num_rows($result); 
	}
	
	if ($modalidad==2){
		if ($cursodual==1){	// Saca modo online
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND estado=0 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=2 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0  ") ;//or die (pg_error());  
		}
		else{				// Presencial
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND  estado=0 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=1 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0  ") ;//or die (pg_error());  
		}
		$total_registros = pg_num_rows($result); 
	}else{
		$result=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND  estado=0 AND estado=0 AND  (modalidad<>2) AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0  ") ;//or die (pg_error());  
		$total_registros = pg_num_rows($result); 
	}
	$totalinscritos = $total_registros;
	
	$porcentaje=number_format((($total_registros/$totaltotal_registros)*100), 2, '.',',');
	?>
	
	<TR>
		<td>Inscritos</th>
		<td><?=$total_registros?></th>
		<td><?=$porcentaje?></th>
	</TR> 
	
	<?
	
	if ($modalidad==2){
	
		if ($cursodual==1){	// Saca modo online
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=1 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=2 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 ") ;//or die (pg_error());  
		}
		else{				// Presencial
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=1 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=1 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0  ") ;//or die (pg_error());  
		}
		
		$total_registros = pg_num_rows($result); 
	}
	else{
		$result=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=1 AND estado=0 AND  (modalidad<>2) AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 ") ;//or die (pg_error());  
		$total_registros = pg_num_rows($result); 
	}
	
	$porcentaje=number_format((($total_registros/$totaltotal_registros)*100), 2, '.',',');
	?>
	
	<TR>
		<td>Lista de espera</th>
		<td><?=$total_registros?></th>
		<td><?=$porcentaje?></th>
	</TR> 
	
		
	<?
	
	if ($modalidad==2){
	
		if ($cursodual==1){	// Saca modo online
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE estado<>0 AND  (modalidad=2) AND inscripciononlinepresencial=2 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0   ") ;//or die (pg_error());  
		}
		else{				// Presencial
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE estado<>0 AND  (modalidad=2) AND inscripciononlinepresencial=1 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0    ") ;//or die (pg_error());  
		}
		$total_registros = pg_num_rows($result); 
	}
	else{
			$result=pg_query($link,"SELECT * FROM curso_usuario WHERE estado<>0 AND  (modalidad<>2 OR modalidad is Null) AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0  ") ;//or die (pg_error());  
			$total_registros = pg_num_rows($result); 
	}
	
	$porcentaje=number_format((($total_registros/$totaltotal_registros)*100), 2, '.',',');
	?>
	
	<TR>
		<td>Bajas</th>
		<td><?=$total_registros?></th>
		<td><?=$porcentaje?></th>
	</TR> 
		
	<TR>
		<td>Total</th>
		<td><?=$totaltotal_registros?></th>
		<td>100</th>
	</TR> 

</TABLE>

<h3>Inscritos por colegio(no incluye lista de espera ni bajas)</h3>
<TABLE> 
	<TR>
		<th>Colegio</th>
		<th>Num inscritos</th>
		<th>% inscritos</th>
	</TR> 


	<?
	$result=posgre_query("SELECT * FROM usuario WHERE borrado=0 AND nivel=2 ORDER BY nombre, id DESC");// or die (mysql_error());  

	while($row = pg_fetch_array($result)) { 
		$idcolegio = $row["id"];
		$nombrecolegiado = $row["nombre"];	
	
		if ($modalidad==2){
			if ($cursodual==1){	// Saca modo online
				$result2=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND estado=0 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=2 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio='$idcolegio')  ") ;//or die (pg_error());  
			}
			else{				// Presencial
				$result2=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND  estado=0 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=1 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio='$idcolegio')  ") ;//or die (pg_error());  
			}
			$total_registros = pg_num_rows($result2); 
		}else{
			$result2=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND  estado=0 AND estado=0 AND  (modalidad<>2) AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio='$idcolegio') ") ;//or die (pg_error());  
			$total_registros = pg_num_rows($result2); 
		}
		
		$porcentaje=number_format((($total_registros/$totalinscritos)*100), 2, '.',',');
		
		if ($total_registros>0){
		
			?>
			<TR>
				<td><?=$nombrecolegiado?></th>
				<td><?=$total_registros?></th>
				<td><?=$porcentaje?></th>
			</TR> 
			
			<?
		}
	
	} 
	
	
	if ($modalidad==2){
		if ($cursodual==1){	// Saca modo online
			$result2=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND estado=0 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=2 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio=0 OR idcolegio IS NULL)  ") ;//or die (pg_error());  
		}
		else{				// Presencial
			$result2=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND  estado=0 AND estado=0 AND  (modalidad=2) AND inscripciononlinepresencial=1 AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio=0 OR idcolegio IS NULL)  ") ;//or die (pg_error());  
		}
		$total_registros = pg_num_rows($result2); 
	}else{
		$result2=pg_query($link,"SELECT * FROM curso_usuario WHERE espera=0 AND  estado=0 AND estado=0 AND  (modalidad<>2) AND nivel<>'3' AND idcurso='$idcurso' AND borrado=0 AND idusuario IN (SELECT id FROM usuario WHERE idcolegio=0 OR idcolegio IS NULL) ") ;//or die (pg_error());  
		$total_registros = pg_num_rows($result2); 
	}
	
	$porcentaje=number_format((($total_registros/$totalinscritos)*100), 2, '.',',');
	
	?>
	
	<TR>
		<td>No colegiados</th>
		<td><?=$total_registros?></th>
		<td><?=$porcentaje?></th>
	</TR> 
	
	<TR>
		<td>Total</th>
		<td><?=$totalinscritos?></th>
		<td>100</th>
	</TR> 

</TABLE>


<a href="informe_curso_inscritos_pdf.php?modo=pdf&idcurso=<?=$idcurso?>" title="resumen" class="btn btn-primary">Descargar PDF</a>

</div>
<!--fin pagina-->
<div class="clearfix"></div>
</div>
<!--fin contenido-principal-->

<?



include("plantillaweb02admin.php"); 
?>
<?
include("_funciones.php"); 
include("_cone.php");

session_start();

$id=strip_tags(trim($_REQUEST['id'])); //idencuesta
$token=strip_tags(trim($_REQUEST['t'])); 
$accion=strip_tags(trim($_REQUEST['accion'])); 

////////// Filtros de nivel por usuario //////////////////////

if (($_SESSION[nivel]==1)||($_SESSION[nivel]==2)) { //Admin y Admin Colegio 
	$idusuario = $_SESSION['idusuario'];
	$sqlcerrada="";
	$vistaprevia=true;
}elseif (($_SESSION[nivel]==4)||($_SESSION[nivel]==3)){ //Admin Total
	$sqlcerrada=" AND estado=1 ";
	$vistaprevia=false;
	$idusuario = $_SESSION['idusuario'];
	$sql = "SELECT * FROM encuestas_respuestas WHERE idusuario='$idusuario' AND idopcion IN (SELECT id FROM encuestas_opciones WHERE idpregunta IN (SELECT id FROM encuestas_preguntas WHERE idencuesta='$id'))";
	$result=posgre_query($sql);
	if (pg_num_rows($result)>0){
		$_SESSION[esterror]= "Ya ha realizado esta encuesta. Gracias por su colaboraci&oacute;n";
		header("Location: index.php");
		exit();
	}
}
else{
	/*
	$sqlcerrada=" AND estado=1 ";
	$sql = "SELECT * FROM encuestas WHERE borrado=0 AND id='$id' $sqlcerrada AND idcurso=0 AND plantilla=0 AND tokenacceso='$token' ;";
	$result=posgre_query($sql);
	if (pg_num_rows($result)>0){
		
	}
	else{
	*/	
	$_SESSION[esterror]="Debe estar logueado para realizar la encuesta";	
	header("Location: acceso.php?id=$id&t=$token");
	exit();
	
	/* } */
}
////////// FIN Filtros de nivel por usuario ////////////////////// 

if ($accion=="guardar"){
	
	foreach($_POST as $key => $value){
		$pos=strpos($key,"opcion_1_");
		if($pos!==false){
			$pieces = explode("opcion_1_", $value);
			$idopcion = $pieces[1];
			$sql = "INSERT INTO encuestas_respuestas (idopcion, idusuario) VALUES ('$idopcion', '$idusuario') ";
			posgre_query($sql);
		}
		
		
		$pos2=strpos($key,"opcion_2_");
		if($pos2!==false){
			
			$pieces = explode("opcion_2_", $value);
			$opciones = $pieces[1];
			$pieces = explode("_", $opciones);
			$idopcion = $pieces[0];
			$idcolumna = $pieces[1];
			
			$sql = "INSERT INTO encuestas_respuestas (idopcion, idopcioncolumna, idusuario) VALUES ('$idopcion','$idcolumna', '$idusuario') ";
			posgre_query($sql);	
		}
		
		
		$pos3=strpos($key,"opcion_3_");
		if($pos3!==false){
			
			$pieces = explode("opcion_3_", $key);
			$idopcion = $pieces[1];
			
			$sql = "INSERT INTO encuestas_respuestas (idopcion, idusuario, textoabierto) VALUES ('$idopcion', '$idusuario', '$value') ";
			posgre_query($sql);
		}
		
		$pos5=strpos($key,"select_5_");
		if($pos5!==false){
						
			$pieces = explode("select_5_", $key);
			$idopcion = $pieces[1];
						
			$pieces2 = explode("opcion_5_".$idopcion."_", $value);
			$preferencia = $pieces2[1];
						
			$sql = "INSERT INTO encuestas_respuestas (idopcion, idusuario, preferencia) VALUES ('$idopcion', '$idusuario', '$preferencia') ";
			posgre_query($sql);
		}
		
		$pos5otra=strpos($key,"opcionotra_");
		if($pos5otra!==false){
						
			$pieces = explode("opcionotra_", $key);
			$idopcion = $pieces[1];
						
			$sql = "UPDATE encuestas_respuestas SET textoabierto='$value' WHERE idopcion='$idopcion' AND idusuario= '$idusuario'";
			posgre_query($sql);
		}
	}
	$_SESSION[esterror]="Encuesta enviada correctamente";
	if (($_SESSION[nivel]==4)||($_SESSION[nivel]==3)){ //Admin Total
	
		header("Location: index.php");
	
	}	
	else{
		
		header("Location: index.php");
	}
	exit();
}




if (($id=="")||($token=="")){
	echo "Error de ID1";
	exit();
}

$sql = "SELECT * FROM encuestas WHERE borrado=0 AND id='$id' $sqlcerrada AND tokenacceso='$token' ;";
$result=posgre_query($sql);
if (pg_num_rows($result)==0){

	$_SESSION[esterror]= "No puede realizar la encuesta ya que se encuentra cerrada";
	header("Location: index.php");
	exit();
}
$row = pg_fetch_array($result); 
$nombre = $row['nombre'];
$idcurso = $row['idcurso'];
$plantilla = $row['plantilla'];

if (($_SESSION['nivel']==4)&&($idcurso<>0)){
	$sql="SELECT * FROM curso_usuario WHERE nivel='5' AND idcurso='$idcurso' AND idusuario='$idusuario' AND borrado=0 AND estado=0 AND espera=0 AND (precio=0 OR pagado=1) ORDER BY (SELECT id FROM curso WHERE borrado=0 AND curso_usuario.id=curso.id ORDER BY fecha_inicio DESC)";
	$result=posgre_query($sql) ;
	if (pg_num_rows($result)==0){
		$_SESSION[esterror]= "Acceso denegado: No ha realizado el curso";
		header("Location: index.php");
		exit();
	}
}

if ($idcurso<>""){
	$sql2 = "SELECT * FROM curso WHERE id='$idcurso'";
	$result2 = posgre_query($sql2);
	$row2 = pg_fetch_array($result2);
	$nombrecurso = $row2['nombre'];
}

$sql3 = "SELECT * FROM encuestas_preguntas WHERE borrado=0 AND idencuesta='$id' ORDER BY orden";
$result3 = posgre_query($sql3);
if (pg_num_rows($result3)==0){
	echo "No hay preguntas para esta encuesta";
	exit();
}

$titulo1="encuestas";
$titulo2="activatie";
include("plantillaweb01.php");
?>		



<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" /> 
<script src="https://code.jquery.com/jquery-1.9.1.js"></script> 
<script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script>

$(function() { 
	$('.selectpreferencia').on('change', function () {
		var select = $(this).attr("id");
		var actualoption = $(this).find('option:selected').text();
		var actualvalue = $(this).find('option:selected').val();
		console.log(actualoption);
		console.log(actualvalue);
		$('select[id='+select+']').each(function() {
			var value = $(this).find('option:selected').val();
		console.log(value);
			if (actualvalue!==value){
				var option = $(this).find('option:selected').text();
		console.log("opcion:"+option);
				if (option!==""){
					
		console.log(option);
					if (option==actualoption){
						
						$(this).val("0");
					}
				}
				
			}
			
		});
	});
}); 
</script>
<div class="grid-12 contenido-principal">
	<? include("_aya_mensaje_session.php"); ?>
	<div class="clearfix"></div>
	<div class="pagina blog">	
	<img src="img/logo-encuestas.png"></img><span style="font-size:24px;"></span>
	<? if ($idcurso<>""){ ?>
		<br><br><h2><?=$nombrecurso?></h2><br>
	<? } ?> 
	
	
	<? if ($plantilla==0){ ?>
		<h2><?=$nombre?></h2><br>
	<? } ?>
	
	<? if (!$vistaprevia) { 
	
	} 
	else { 
	$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	?>
		<br><p><b>ENCUESTA EN VISTA PREVIA. <br>
		SU TIPO DE USUARIO NO PUEDE CUMPLIMENTAR LA ENCUESTA.<br>
		LINK PARA USUARIOS: <br><a href="<?=$actual_link?>"><?=$actual_link?></a></b></p><br>

	<? } ?>
	<? ?>
	<div class="alert">
		Escala de valoraci&oacute;n:<br />
		1 - Muy deficiente<br />
		2 - Deficiente<br />
		3 - Regular<br />
		4 - Bueno<br />
		5 - Excelente<br />
	</div>
	<form action="encuesta.php?accion=guardar" method="POST">
		<input type="hidden" name="id" value=<?=$id?>>
		<? 
		$i = 1;
		while ($rowpreguntas = pg_fetch_array($result3)){ 
		
			$idpregunta = $rowpreguntas['id'];
			$pregunta = $rowpreguntas['texto'];
			$tipo = $rowpreguntas['tipo'];
			$obligatorio = $rowpreguntas['obligatorio'];
			$respuesta = $rowpreguntas['respuestas'];
			
			if ($respuesta==2){
				$tipoinput = "checkbox";
			}
			else{
				$tipoinput = "radio";
			}	
			
			$textoobligatorio="";
			if ($obligatorio<>"1"){
				$textoobligatorio="[Opcional]";
			}
		
			?>
			<? if ($tipo<>"6"){	?>
				<h4><img src="img/messageedit.png"></img> <?=$i?>. <?=$textoobligatorio?> <?=$pregunta?></h4>
				<hr>	
				<?
			}			
			$asteriscos=1;
			$pieasteriscos="<br>";
			$sql4 = "SELECT * FROM encuestas_opciones WHERE borrado=0 AND idpregunta='$idpregunta' ORDER BY orden";
			$result4 = posgre_query($sql4);
			if ($tipo==1){
				while ($rowopciones = pg_fetch_array($result4)){
					$idopcion = $rowopciones['id'];
					$nombreopcion = $rowopciones['fila'];
					$opcioncontexto = $rowopciones['opcioncontexto'];
					
					/** Asteriscos **/
					$notaasterisco = $rowopciones['notaasterisco'];
					$textoasterisco1="";
					$textoasterisco2="";
					if ($notaasterisco<>""){
						$textoasterisco1="<span title='$notaasterisco'>";
						$numasteriscos = "";
						for ($k=0;$k<$asteriscos;$k++){
							
							$textoasterisco2.="*";
							$numasteriscos .= "*";
						}
						$textoasterisco2.="</span>";
						$asteriscos++;
						$pieasteriscos .= $numasteriscos." ".$notaasterisco."<br>";
					}
					
					
					?> <?=$textoasterisco1?><input <? if (($obligatorio==1)&&($respuesta!=2)) echo 'required'?> style="margin:7px; padding:3px; vertical-align: middle; " <? if ($respuesta==2){ ?> name="opcion_<?=$tipo?>_<?=$idopcion?>" <? } else { ?> name="opcion_<?=$tipo?>_<?=$idpregunta?>" <? } ?> value="opcion_<?=$tipo?>_<?=$idopcion?>" type="<?=$tipoinput?>"><?=$nombreopcion?><?=$textoasterisco2?> 
					<? if ($opcioncontexto==1){ ?>
						<input type="text" name="opcionotra_<?=$idopcion?>">
					<? } ?>
					&nbsp;&nbsp;<br><?
				}
				?> <br> <?
			}
			elseif (($tipo==2)||($tipo==4)){
				?>
				<table class="align-center" border="0" cellpadding="0" cellspacing="0">
				<tbody><tr>
				<th> </th>
				<? 
				$sql5 = "SELECT * FROM encuestas_opciones WHERE idpregunta='$idpregunta' AND borrado=0 AND columna='1' ORDER BY orden, id";
				$result5=posgre_query($sql5); 
				while($row = pg_fetch_array($result5)) { 
					$col = $row['fila'];
				?>
					<th><?=$col?></th>
				<?
				}
				
				$result=posgre_query("SELECT * FROM encuestas_opciones WHERE idpregunta='$idpregunta' AND borrado=0 AND columna='0' ORDER BY orden, id"); 
				while($row = pg_fetch_array($result)) { 
					$idopcion = $row['id'];
					$fila = $row['fila'];
		
				?>
					<tr>
						<td><?=$fila?></td>
						
						<?
						$result5=posgre_query($sql5); 
						while($rowcolumnas = pg_fetch_array($result5)) {
							$idopcioncolumna = $rowcolumnas['id'];
						?>
							<td><input <? if (($obligatorio==1)&&($respuesta!=2)) echo 'required'?> value="opcion_2_<?=$idopcion?>_<?=$idopcioncolumna?>" name="opcion_2_<?=$idopcion?><? if ($respuesta==2) echo rand(0,9999);?>" type="<?=$tipoinput?>"></td>
						<? } ?>
							
				
					</tr>	
				<? } ?>
				</tbody>
				</table>
				<?
			}
			elseif ($tipo==3){
				
				$sql6 = "SELECT * FROM encuestas_opciones WHERE borrado=0 AND idpregunta='$idpregunta' ORDER BY orden";
				$result6 = posgre_query($sql6);
				$rowopciones = pg_fetch_array($result6);
				$idopcion = $rowopciones['id'];
				
				?>
				<textarea <? if (($obligatorio==1)) echo 'required'?> name="opcion_<?=$tipo?>_<?=$idopcion?>" style="margin-left:1em;" class="inputtextarea input-xxlarge" rows="3" ></textarea><br>
				<?
			}
			elseif ($tipo==5){
				$numopciones = pg_num_rows($result4);
				echo '<div style="font-size:12px;">Escala de valoración: 1 (más alta prioridad) al '.$numopciones.' (más baja prioridad)</div><br>';
				
				while ($rowopciones = pg_fetch_array($result4)){
					$idopcion = $rowopciones['id'];
					$nombreopcion = $rowopciones['fila'];
					$opcioncontexto = $rowopciones['opcioncontexto'];
					
					/** Asteriscos **/
					$notaasterisco = $rowopciones['notaasterisco'];
					$textoasterisco1="";
					$textoasterisco2="";
					if ($notaasterisco<>""){
						$textoasterisco1="<span title='$notaasterisco'>";
						$numasteriscos = "";
						for ($j=0;$j<$asteriscos;$j++){
							
							$textoasterisco2.="*";
							$numasteriscos .= "*";
						}
						$textoasterisco2.="</span>";
						$asteriscos++;
						$pieasteriscos .= $numasteriscos." ".$notaasterisco."<br>";
					}
					
					
					?>
					<?=$textoasterisco1?><select name="select_<?=$tipo?>_<?=$idopcion?>" id="select_<?=$tipo?>_<?=$idpregunta?>" class="selectpreferencia" <? if ($obligatorio==1) echo 'required'?> style="width:46px;">
					<option></option>
					<? for ($j=1; $j<=$numopciones; $j++){ ?>
						<option class="opcion_<?=$tipo?>_<?=$idpregunta?>" name="opcion_<?=$tipo?>_<?=$idpregunta?>" value="opcion_<?=$tipo?>_<?=$idopcion?>_<?=$j?>"><?=$j?></option>
					<? } ?>
					</select>  <?=$nombreopcion?><?=$textoasterisco2?>
					
					<? if ($opcioncontexto==1){ ?>
						<input type="text" name="opcionotra_<?=$idopcion?>">
					<? } ?>
 					
					<br> <?
				}
			}
			elseif ($tipo==6){ ?>
				<b><?=$pregunta?></b>
			<? }
			
			
			echo $pieasteriscos; ?>
			

			<br>		
			<? 
			if ($tipo<>6){
				$i++;
			}
		} 
		?> 
		<? if (!$vistaprevia) { ?>
		<div class="form-actions">
			<button type="submit" style="margin-right:1em;" class="btn btn-primary btn-large">Enviar</button>
			<button type="reset"  class="btn" >Resetear respuestas</button>
		</div>
		<? 
		} 
		else { 
		$actual_link = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		?>
			<br><br><p><b>ENCUESTA EN VISTA PREVIA. <br>
			SU TIPO DE USUARIO NO PUEDE CUMPLIMENTAR LA ENCUESTA.<br>
			LINK PARA USUARIOS: <br><a href="$<?=$actual_link?>"><?=$actual_link?></a></b></p><br>
		<? } 
		?>
	</form>
			
			
			

	</div>
</div>

<? 
include("plantillaweb02.php"); 
?>
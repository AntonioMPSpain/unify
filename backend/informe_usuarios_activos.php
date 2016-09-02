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
$fecha1 = $_POST['fecha1'];
$fecha2 = $_POST['fecha2'];

$fecha1copia = $fecha1;
$fecha2copia = $fecha2;

if ($fecha1[5]=="/"){
	$fechas = explode("/", $fecha1);
	$fecha1 = $fechas[2]."-".$fechas[1]."-".$fechas[0];
}

if ($fecha2[5]=="/"){
	$fechas = explode("/", $fecha2);
	$fecha2 = $fechas[2]."-".$fechas[1]."-".$fechas[0];
}

$sqlfecha1="";
if ($fecha1<>""){
	$sqlfecha1 = " AND fechaalta>='$fecha1' ";
}

$sqlfecha2="";
if ($fecha2<>""){
	$sqlfecha2 = " AND fechaalta<='$fecha2' ";
}


include("plantillaweb01admin.php"); 
?>
<!--Arriba plantilla1-->
<h2 class="titulonoticia">Resumen de usuarios registrados</h2>
<div class="bloque-lateral acciones">		
	<p>
		<a href="zona-privada_admin_informes_1.php" class="btn btn-success" type="button"> Volver <i class="icon-circle-arrow-left"></i></a> |
	</p>
</div>
<? include("_aya_mensaje_session.php"); ?>
<TABLE > 
	<TR>
		<th>Colegio</th>
		<th>Usuarios en la plataforma</th>
		<th>Usuarios activados</th>
		<th>% que representa cada colegio</th>
	</TR> 
	<?  
	$link=conectar(); //Postgresql
	$result=pg_query($link,"SELECT * FROM usuario WHERE borrado=0 AND nivel=2 ORDER BY nombre DESC, id DESC");// or die (mysql_error());  
	$usuariosactivostotal=0;
	$usuariostotal=0;
	while($row = pg_fetch_array($result)) { 
		$id = $row["id"];
		$nombre = $row["nombre"];	
			
		$sql = "SELECT * FROM usuario WHERE borrado=0 AND idcolegio='$id' ";
		$result2 = posgre_query($sql);
		$usuarios = pg_num_rows($result2);	
			
						
		$sql = "SELECT * FROM usuario WHERE borrado=0 AND (idcolegio!=0 OR idcolegio IS NOT NULL) AND (pass IS NOT NULL OR pass!='' $sqlfecha1 $sqlfecha2)";
		$result4 = posgre_query($sql);
		$usuariosactivostotal2 = pg_num_rows($result4);	
		
		$sql = "SELECT * FROM usuario WHERE borrado=0 AND idcolegio='$id' AND (pass IS NOT NULL OR pass!='') $sqlfecha1 $sqlfecha2";
		$result3 = posgre_query($sql);
		$usuariosactivos = pg_num_rows($result3);	
		
		$porcentajeactivados=number_format((($usuariosactivos/$usuarios)*100), 2, '.',',');
		$porcentajeactivatietotal=number_format((($usuariosactivos/$usuariosactivostotal2)*100), 2, '.',',');
			
		?><tr bgcolor="<?=$bgcolor?>">
			<td style="text-align:center;"><?=$nombre?></td>
			<td style="text-align:center;"><?=$usuarios?></td>
			<td style="text-align:center;"><?=$usuariosactivos?>(<?=$porcentajeactivados?>%)</td>
			
			<td style="text-align:center;"><?=$porcentajeactivatietotal?>%</td>
		</tr>
		<? 
		$usuariostotal+=$usuarios;
		$usuariosactivostotal+=$usuariosactivos;
		
	} 
	
	$porcentajeactivadostotal=number_format((($usuariosactivostotal/$usuariostotal)*100), 2, '.',',');
	
	$sql = "SELECT * FROM usuario WHERE borrado=0 AND (idcolegio=0 OR idcolegio IS NULL) $sqlfecha1 $sqlfecha2";
	$result4 = posgre_query($sql);
	$usuariosNocolegiados = pg_num_rows($result4);	
	
	$sql = "SELECT * FROM usuario WHERE borrado=0 AND (idcolegio=0 OR idcolegio IS NULL) AND (pass IS NOT NULL OR pass!='') $sqlfecha1 $sqlfecha2";
	$result5 = posgre_query($sql);
	$usuariosNocolegiadosActivos = pg_num_rows($result5);	
	
	$porcentajeactivosNocolegiados = number_format((($usuariosNocolegiadosActivos/$usuariosNocolegiados)*100), 2, '.',',');
	
	$usuariostotalactivatie = $usuariostotal+$usuariosNocolegiados;
	$usuariostotalactivatieactivos = $usuariosactivostotal + $usuariosNocolegiadosActivos;
	$porcentajeusuariostotalactivatie =number_format((($usuariostotalactivatieactivos/$usuariostotalactivatie)*100), 2, '.',',');
	
	$sql = "SELECT * FROM usuario WHERE baja=1 $sqlfecha1 $sqlfecha2";
	$resultb = posgre_query($sql);
	$usuariosBaja = pg_num_rows($resultb);
	
	?><tr bgcolor="<?=$bgcolor?>">
		<td style="text-align:center;">TOTAL COLEGIADOS</td>
		<td style="text-align:center;"><?=$usuariostotal?></td>
		<td style="text-align:center;"><?=$usuariosactivostotal2?>(<?=$porcentajeactivadostotal?>%)</td>
		<td style="text-align:center;">100%</td>
	</tr>
	
	<tr bgcolor="<?=$bgcolor?>">
		<td style="text-align:center;">NO COLEGIADOS</td>
		<td style="text-align:center;"><?=$usuariosNocolegiados?></td>
		<td style="text-align:center;"><?=$usuariosNocolegiadosActivos?>(<?=$porcentajeactivosNocolegiados?>%)</td>
	</tr>
	
	<tr bgcolor="<?=$bgcolor?>">
		<td style="text-align:center;">TOTAL ACTIVATIE</td>
		<td style="text-align:center;"><?=$usuariostotalactivatie?></td>
		<td style="text-align:center;"><?=$usuariostotalactivatieactivos?>(<?=$porcentajeusuariostotalactivatie?>%)</td>
	</tr>
	
	
	<tr bgcolor="<?=$bgcolor?>">
		<td style="text-align:center;">USUARIOS BAJA</td>
		<td style="text-align:center;"><?=$usuariosBaja?></td>
	</tr>
</table>


<a href="informe_usuarios_activos_pdf.php?modo=pdf" title="resumen" class="btn btn-primary">Descargar PDF</a>


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
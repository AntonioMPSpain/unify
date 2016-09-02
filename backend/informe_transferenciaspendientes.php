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
		$sqlcolegio=" AND idcolegio='$idcolegio' ";
	}
}
elseif ($_SESSION[nivel]==1) { //Admin Total
	$sqlcolegio="";
}
else{
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}
////////// FIN Filtros de nivel por usuario //////////////////////

include("plantillaweb01admin.php"); 
?>
<!--Arriba plantilla1-->
<h2 class="titulonoticia">Cursos en los que hay transferencias pendientes de validar</h2>
<div class="bloque-lateral acciones">		
	<p>
		<a href="zona-privada_admin_informes_1.php" class="btn btn-success" type="button"> Volver <i class="icon-circle-arrow-left"></i></a> |
	</p>
</div>
<? include("_aya_mensaje_session.php"); ?>
<TABLE > 
	<TR>
		<th>Curso</th>
		<? if ($_SESSION['nivel']==1){ ?>
			<th>Colegio</th>
		<? } ?>
		<th>Inscritos/Modalidad</th>
	</TR> 
	
	<?
	
	$sql0 = "SELECT * FROM curso WHERE borrado=0 AND estado=2 $sqlcolegio";
	
	$result0 = posgre_query($sql0);
	
	while ($row0 = pg_fetch_array($result0)){
		
		$idcurso = $row0['id'];
		$titulo = $row0['nombre'];
		$modalidad = $row0['modalidad'];
		$idcolegio = $row0['idcolegio'];
		
		$sqlc = "SELECT * FROM usuario WHERE id='$idcolegio'";
		$resultc = posgre_query($sqlc);
		if ($rowc = pg_fetch_array($resultc)){
			$nombrecolegio = $rowc['nombre'];
		}
		
	
		$enOnline = false;
		$enPresencial = false;
		
		$sql = "SELECT * FROM curso_usuario WHERE estado=0 AND espera=0 AND idcurso='$idcurso' AND tipoinscripcion=0 AND pagado=0 AND borrado=0";
	
		$result = posgre_query($sql);
		echo pg_last_error();
		while ($row = pg_fetch_array($result)){
			$idusuario = $row['idusuario'];
			$inscripciononlinepresencial = $row['inscripciononlinepresencial'];
			
			$result2 = posgre_query("SELECT id,archivo,nombre FROM archivo WHERE idcurso='$idcurso' AND idusuario='$idusuario' AND borrado=0 ORDER BY fecha DESC") ;//or die (mysql_error());  
			if ($row2 = pg_fetch_array($result2)){
				if ($modalidad==2){
					
					if ($inscripciononlinepresencial==1){
						$enPresencial = true;
					}
					elseif($inscripciononlinepresencial==2){
						$enOnline = true;
					}
				}
				else{
					$enOnline = true;
				}
			}	

			


			
		}
	
		if (($enOnline)||($enPresencial)){
			
		
			?>
			<tr>
				<td><?=$titulo?></td>
				
				<? if ($_SESSION['nivel']==1){ ?>
					<td><?=$nombrecolegio?></td>
				<? } ?>
				
				<td> <?
				if ($modalidad==2){
					
					if ($enOnline){ echo "<a href=\"zpa_usuario_curso.php?cursodual&idcurso=$idcurso\">- online</a><br><br>"; } 
					if ($enPresencial){ echo "<a href=\"zpa_usuario_curso.php?idcurso=$idcurso\">- presencial</a>"; } 
					
				
						
					
					
				} 
				elseif ($enOnline){
					echo "<a href=\"zpa_usuario_curso.php?idcurso=$idcurso\">- inscritos</a>"; 
				}
				?>
				</td> 
			</tr>
		
		
		<?
		}
	}
	
	
	
	?>
	
	
</table>


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
<?
session_start();

$idcurso=strip_tags($_REQUEST['idcurso']); 
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
include_once "a_facturas.php"; 

$safe="Resumen de ingresos";
$titulo1="informe ";
$titulo2="cursos";

$linka=iConectarse(); 
$rowcurso=pg_query($linka,"SELECT * FROM curso WHERE id='$idcurso' AND borrado=0;") ;//or die ("ERROR AL MODIFICAR. ".mysql_error()); 
$curso= pg_fetch_array($rowcurso);

include("plantillaweb01admin.php"); 
?>
<!--Arriba plantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<div class="bloque-lateral acciones">		
	<!--<p>
		<a href="informe_curso.php" class="btn btn-success" type="button"> Volver <i class="icon-circle-arrow-left"></i></a> |
	</p>-->
	</div>
	<h2 class="titulonoticia">Resumen de ingresos de <?=$curso["nombre"];?></h2>
		<br />
		<? 
		include("_aya_mensaje_session.php"); 
		$_SESSION[error]=""; 
		?>
		
		<h3>Ingresos inscritos</h3>
		<TABLE style = "text-align:center;"> 
		<TR>
			<th>Alumnos inscritos</th>
			<th>Pagos realizados</th>
			<th>Importe</th>
			<th>Potencial inscritos</th>
			<th>Total</th>
		</TR> 
				
		<? 
		$totalpagado = 0;
		$sql = "SELECT precio, count(*) as alumnos FROM curso_usuario WHERE estado=0 AND espera=0 AND idcurso='$idcurso' AND nivel<>'3' GROUP BY precio";
		$result = posgre_query($sql);
		
		while ($row = pg_fetch_array($result)){
		
			$alumnos = $row['alumnos'];
			$precio = $row['precio'];
			
		
			$sql2 = "SELECT count(*) as pagados FROM curso_usuario WHERE estado=0 AND espera=0 AND pagado=1 AND precio='$precio' AND idcurso='$idcurso' AND nivel<>'3' GROUP BY precio";
			$result2 = posgre_query($sql2);
			
			$pagados = 0;
			if ($row2 = pg_fetch_array($result2)){
				$pagados = $row2['pagados'];
			}
			
			?>
			<TR>
				<td><?=$alumnos?></td>
				<td><?=$pagados?></td>
				<td><?=$precio?>€</td>
				<td><?=$precio*$alumnos?>€</td>
				<td><?=$precio*$pagados?>€</td>
			</TR> 
			
			<?
			
			$totalpagado += $precio*$pagados;
		}
		?>

		</TABLE>

		<strong>Total: <?=$totalpagado?>€</strong><br /><br /><br />	
		
		
		<h3>Ingresos inscritos por colegio</h3>
		<TABLE style = "text-align:center;"> 
		<TR>
			<th>Colegio</th>
			<th>Alumnos inscritos</th>
			<th>Pagos realizados</th>
			<th>Importe</th>
			<th>Potencial inscritos</th>
			<th>Total</th>
		</TR> 
		<? 
		
		
		
		$sql3 = "SELECT * FROM usuario WHERE borrado=0 AND nivel=2 ORDER BY nombre";
		$result3 = posgre_query($sql3);
		
		$totalpagado = 0;
			
		while ($row3 = pg_fetch_array($result3)){
			$idcolegio = $row3['id'];
			$nombrecolegio = $row3['nombre'];
			
			$sql = "SELECT precio, count(*) as alumnos FROM curso_usuario WHERE idusuario IN (SELECT id FROM usuario WHERE idcolegio='$idcolegio') AND estado=0 AND espera=0 AND idcurso='$idcurso' AND nivel<>'3' GROUP BY precio";
			$result = posgre_query($sql);
		
			while ($row = pg_fetch_array($result)){
			
				$alumnos = $row['alumnos'];
				$precio = $row['precio'];
				
			
				$sql2 = "SELECT count(*) as pagados FROM curso_usuario WHERE idusuario IN (SELECT id FROM usuario WHERE idcolegio='$idcolegio') AND estado=0 AND espera=0 AND pagado=1 AND precio='$precio' AND idcurso='$idcurso' AND nivel<>'3' GROUP BY precio";
				$result2 = posgre_query($sql2);
				
				$pagados = 0;
				if ($row2 = pg_fetch_array($result2)){
					$pagados = $row2['pagados'];
				}
							
				?>
				<TR>
					<td><?=$nombrecolegio?></td>
					<td><?=$alumnos?></td>
					<td><?=$pagados?></td>
					<td><?=$precio?>€</td>
					<td><?=$precio*$alumnos?>€</td>
					<td><?=$precio*$pagados?>€</td>
				</TR> 
				
				<?
				
				$totalpagado += $precio*$pagados;
			}
		}
		
		
		$sql = "SELECT precio, count(*) as alumnos FROM curso_usuario WHERE idusuario IN (SELECT id FROM usuario WHERE idcolegio=0 OR idcolegio IS NULL) AND estado=0 AND espera=0 AND idcurso='$idcurso' AND nivel<>'3' GROUP BY precio";
		$result = posgre_query($sql);
	
		while ($row = pg_fetch_array($result)){
		
			$alumnos = $row['alumnos'];
			$precio = $row['precio'];
			
		
			$sql2 = "SELECT count(*) as pagados FROM curso_usuario WHERE idusuario IN (SELECT id FROM usuario WHERE idcolegio=0 OR idcolegio IS NULL) AND estado=0 AND espera=0 AND pagado=1 AND precio='$precio' AND idcurso='$idcurso' AND nivel<>'3' GROUP BY precio";
			$result2 = posgre_query($sql2);
			
			$pagados = 0;
			if ($row2 = pg_fetch_array($result2)){
				$pagados = $row2['pagados'];
			}
						
			?>
			
			
			
			<TR>
				<td>No colegiados</td>
				<td><?=$alumnos?></td>
				<td><?=$pagados?></td>
				<td><?=$precio?>€</td>
				<td><?=$precio*$alumnos?>€</td>
				<td><?=$precio*$pagados?>€</td>
			</TR> 
			
			<?
			
			$totalpagado += $precio*$pagados;
		}
		
		
		
		?>
				
		</TABLE>


		<strong>Total: <?=$totalpagado?>€</strong><br /><br /><br />
		<? /*
		<h3>Devoluciones</h3>
		<TABLE style = "text-align:center;"> 
		<TR>
			<th>Alumnos baja</th>
			<th>Importe</th>
		</TR> 
		<? 
		$totalpagado = 0;
		$sql = "SELECT precio, count(*) as alumnos FROM curso_usuario WHERE estado=1 AND pagado=1 AND idcurso='$idcurso' AND nivel<>'3' GROUP BY precio";
		$result = posgre_query($sql);
		
		while ($row = pg_fetch_array($result)){
		
			$alumnos = $row['alumnos'];
			$precio = $row['precio'];
			
		
			$sql2 = "SELECT count(*) as pagados FROM factura_factura WHERE rectificativa=1 AND tipo=1 AND idgenerica='$idcurso' AND \"InvoiceNumber\" IN (SELECT \"InvoiceNumber\" FROM factura_subfactura WHERE \"UnitPriceWithoutTax\"='-$precio')";
			$result2 = posgre_query($sql2);
			$pagados = 0;
			if ($row2 = pg_fetch_array($result2)){
				$pagados = $row2['pagados'];
			}
			
			?>
			<TR>
				<td><?=$alumnos?></td>
				<td>-<?=$precio?>€</td>
			</TR> 
			
			<?
			
			$totalpagado += $alumnos*$precio;
		}
		?>

		</TABLE>
		
		<strong>Total a devolver: <?=$totalpagado?>€</strong><br /><br /><br />
		*/ ?>
		<a href="informe_curso_ingresos_pdf.php?modo=pdf&idcurso=<?=$idcurso?>" title="resumen" class="btn btn-primary">Descargar PDF</a>
		<br />			<br />
		
	</div>
	<!--fin pagina-->
<div class="clearfix"></div>
</div>
<!--fin contenido-principal-->
<?
include("plantillaweb02admin.php"); 
?>
<?
include("_funciones.php"); 
include("_cone.php");

session_start();

$titulo1="formación ";
$titulo2="administración";
$migas = array();

$migas = array();
$migas[] = array('#', 'Informes');
include("plantillaweb01admin.php"); 
?><!--Arriba pantilla1-->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
		<h2 class="titulonoticia">Informes</h2>
				<!--<div class="mensaje ko">
					<h3>Disculpe las molestias, nos encontramos reordenando esta sección.</h3>	
				</div>-->		
		<br />
		<div class="informes">		
			<h4>Usuarios:</h4>
				<ol>
					<li><a href="informe_usuarios_activos.php"><i class="icon-file"></i>Resumen de usuarios registrados</a></li>
				</ol>  
				
			<h4>Cursos:</h4>
				<ol>
					<li><a href="informe_curso.php"><i class="icon-file"></i>Informes por curso</a></li>
					<li><a href="informe_inscritoscursosglobal.php"><i class="icon-file"></i>Resumen de inscritos a todos los cursos</a></li>
					<li><a href="informe_diplomaspendientes.php"><i class="icon-file"></i>Diplomas pendientes de validar</a></li>
					
			</ol>  
			<?	if ($_SESSION[nivel]==1){	?>			
				<h4>Beneficios:</h4>
					<ol>
						<li><a href="informe_curso_repartoglobal.php"><i class="icon-file"></i>Resumen reparto de beneficios por SOCIO</a></li>
						<li><a href="informe_curso_repartoorganizadorglobal.php"><i class="icon-file"></i>Resumen reparto de beneficios por ORGANIZADOR</a></li>
					
					</ol> 
			
			<? } ?>			
				<h4>Pagos:</h4>
					<ol>
					
					<?	if (($_SESSION[nivel]==1)||($_SESSION[idcolegio]==114)){	?>		
						<li><a href="informe_pagotpv.php"><i class="icon-file"></i>Pagos con tarjeta bancaria</a></li>
					<? } ?>	
					<li><a href="informe_transferenciaspendientes.php"><i class="icon-file"></i>Transferencias pendientes de validar</a></li>
					
					</ol> 
			
			<?	if ($_SESSION[nivel]==1){	?>			
				<h4>Servidor:</h4>
					<ol>
						<li><a href="informe_espaciolibre.php"><i class="icon-file"></i>Espacio libre</a></li> 
					
					</ol> 
			<? } ?>	
			<!--<h4>Publicaciones:</h4>
				<ol>
					<li><a href="admin_contenido_historico.php?tipo=publicacion"><i class="icon-file"></i>Histórico de compras</a></li>
				</ol>-->   	 				
			<!--<h4>Historico de actividades:</h4>
				<ol>
					<li><a href="zona-privada_admin_ingresos_curso.php"><i class="icon-file"></i>Listado de alumnos por curso </a> (firmas, asistencias,...)</li>
					<li><a href="zona-privada_admin_usuario_in.php"><i class="icon-file"></i>Listado de cursos por Alumno</a></li>
					<li><a href="zona-privada_admin_profesores_in.php"><i class="icon-file"></i>Listado de cursos por Profesor</a></li>
					<li><a href="#"><i class="icon-file"></i>Listado de cursos por año</a></li>
				</ol> -->
		</div>
		<!--fin informes-->
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
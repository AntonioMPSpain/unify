<?
include("_funciones.php"); 
include("_cone.php"); 
include_once "_config.php";

$safe="Gestion de publicaciones"; 
$accion=strip_tags($_GET['accion']); 
$est=strip_tags($_GET['est']);
$tipo="publicacion";

$id=($_GET['id']);
$tipo=strip_tags($_REQUEST['tipo']);
if (($id=='')||($id==0)||($tipo=="")){ 
	//header("Location: index.php?salir=true");
	echo "parametros incorr";
	exit();
}
$titulo1="publicaciones ";
$titulo2="administracion";

include("plantillaweb01admin.php"); 
			?>	
			<style>
iframe
	 {
	    top: 0;
	    left: 0;
	    width: 100%;
	    height: 100%;
	}			
			</style>
<!-- Arriba pantilla1 -->
	<div class="grid-9 contenido-principal">
	<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
		<h2 class="titulonoticia">Imagen</h2>
		<br>
		<div class="bloque-lateral acciones">		
			<p><strong>Acciones:</strong>
			<?
			if ($tipo=="portadapublicacion"){		
				?>
				<a class="btn btn-success" href="admin_contenido3_imagen.php?id=<?=$id?>"><i class="icon-chevron-left"></i> Volver</a>
				<?
			}
			elseif ($tipo=="fichapublicacion"){
				?>
				<a class="btn btn-success" href="admin_contenido3_imagen.php?id=<?=$id?>"><i class="icon-chevron-left"></i> Volver</a>
				<?
			}
			elseif ($tipo=="portadacurso"){
				?>
				<a class="btn btn-success" href="curso_alta_mas.php?id=<?=$id?>"><i class="icon-chevron-left"></i> Volver</a>
				<?
			}
			elseif ($tipo=="fichacurso"){
				?>
				<a class="btn btn-success" href="curso_alta_mas.php?id=<?=$id?>"><i class="icon-chevron-left"></i> Volver</a>
				<?
			}
			
			
			?>
			</p>
		</div>
			<!--fin acciones-->
			<iframe scrolling="no" style="overflow: hidden; border:none; width:100%; height:1200px;" frameBorder="0" src="<?=$b_libspath?>cropimagen/index.php?id=<?=$id?>&tipo=<?=$tipo?>&bd=generica"></iframe>
		
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
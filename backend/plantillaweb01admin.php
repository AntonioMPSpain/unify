<?
//error_reporting(E_ERROR);
//error_reporting(E_ERROR | E_WARNING | E_PARSE);
session_start();
$archivo_web=basename($_SERVER["PHP_SELF"]);
if ($safe==''){
	$safe="activatie.es";
}
//Sacar el nombre del archivo en $menu
$f = explode("/", $_SERVER['PHP_SELF']);
$menu=$f[count($f)-1];
settype($id, 'integer');
$link_c=conectar();
$Query_c = pg_query($link_c,"SELECT * FROM configuracion WHERE id=1;");// or die ("e1-".pg_error());  
$data_c = pg_fetch_array($Query_c);
$c_telefono=$data_c["c_telefono"];
$c_email=$data_c["c_email"];
$c_web=$data_c["c_web"];
$c_ano=$data_c["c_ano"];

?><!DOCTYPE html>
<html lang="es-es">
<head>
	<meta charset="utf-8">
	<title><?=$safe." ".$data_c["c_title"];?></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0"  />
	<meta name="description" content="<?=$safe?> <?=$data_c["c_description"];?>" />
	<meta name="keywords" content="<?=$safe?>, <?=$data_c["c_keywords"];?>" />
	<meta name="robots" content="all" />
	<meta name="viewport" content="width=device-width" />	
	<meta name="author" lang="es" content="activatie.es | newsisco.com + pixelpenguins.com + cayetano" />
	<!-- CSS -->
	<link rel="stylesheet" href="css/inuit.css" />
	<link rel="stylesheet" href="css/igloos.css" />
	<link rel="stylesheet" href="css/fonts.css" />
	<link rel="stylesheet" href="css/style-pp.css" />
	<link rel="stylesheet" href="css/style.css" />
	<link media="print" rel="stylesheet" href="css/print.css" type="text/css" /> 		
	
	<!-- FONTS-->
	<link rel="stylesheet" href="css/fonts.css" />
	<!-- Favicons-->
	<link rel="shortcut icon" href="favicon.ico" />
	<link rel="apple-touch-icon-precomposed" href="favicon.png" />
	<!-- JAVASCRIPT-->
	<!--Se cargan al final de la pagina con head.js-->
	<link rel="stylesheet" href="js/shadowbox/shadowbox.css" />
	<script language="javascript">
	function confirmar ( mensaje ) {
		return confirm( mensaje );
		}
	</script>
	<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
	<script type="text/javascript" src="js/z_usuario_curso_comunicacion.js"></script>
</head>

<!--[if lte IE 7>
	<script type="text/javascript">
	var IE6UPDATE_OPTIONS = {
	icons_path: "js/ie6update/images/"
	}
	</script>
	<script type="text/javascript" src="js/ie6update/ie6update.js"></script>
	<![endif]-->

<body class="background-main">
<div class="wrapper zonaadmin">
	<div class="grids" id="cabecera">
		<div class="row">
			<div class="grid-4 logotipo">
				<h1><a href="../index.php" ><img src="../img/activatie-logo.png" alt="activATIE" title="Volver a la p&aacute;gina de inicio" /></a></h1>
			</div>
			<div class="grid-1"> </div>
			<div class="grid-7">
				<!--
				<ul class="menu mainmenu">
					<li><a href="index.php"  title="Volver a la p&aacute;gina principal"><i class="icon-home"></i> Inicio</a></li>
					<? // <li><a href="buzon_sugerencias.php"  title="Buzón de sugerencias"><i class="icon-envelope"></i>  Buzón de sugerencias</a></li> ?>
					<li class="last-item"><a href="contacto.php"  title="Datos de contacto de ActivATIE"><i class="icon-envelope"></i>  Contacto</a></li>
				</ul>
				-->
				<!--fin mainmenu-->
				<!--
				<div class="menu mainmenu botones-sociales">
					<ul>
						<li class="btn-favorite"><a href="javascript:bookmark('http://www.activatie.org','activATIE | Portal de formación para Arquitectos Técnicos y Aparejadores');">Añadir esta web a mis marcadores</a></li>
						<li class="btn-facebook"><a href="https://www.facebook.com/pages/Activatie/783928711720244"  title="Facebook">Facebook</a></li>	
						<li class="btn-linkedin"><a href="https://es.linkedin.com/pub/activatie/ba/7b/ab9"  title="Linkedin">Linkedin</a></li>								
						<li class="btn-youtube"><a href="https://www.youtube.com/channel/UCFwdS1lgUFVRIejymj-8UYA"  title="youtube">Youtube</a></li>								
						<li class="btn-twitter"><a href="http://www.twitter.com/activatie"  title="Twitter">Twitter</a></li>
						<li class="btn-rss"><a href="rss/rss.xml" title="Suscribirse por RSS">RSS</a></li>		
					</ul>	
				</div>
				-->
				<!--fin botones sociales-->			
			</div>
		</div>
	</div>
	<!-- fin cabecera -->
	<div class="clearfix"></div>
	<!--
	<ul class="menu-sections cl-effect-3">
		<li class="menu-01"><a href="formacion.php">Actividades <span>formativas</span></a></li>
		<li class="menu-02"><a href="publicaciones.php">Catálogo de <span>publicaciones</span></a></li>
		<li class="menu-03"><a href="trabajos.php">Ofertas <span>de trabajo</span></a></li>
	</ul>
	<div class="clearfix"></div>
	-->
	<div class="grids">
		<div class="row">
			
			<div class="grid-12 titulo-seccion<?=$textocabezah?>">
				<h2><span>panel de</span> administración</h2>
			</div>
		</div>
	</div><!-- fin grids-->
	<div class="clearfix"></div>
	
	
	<div class="grids">
	<div class="row">
		<div class="grid-12 contenido-principal">
		<div class="clearfix"></div>
		<div class="pagina zonaprivada blog">
		<!-- MENU HORIZONTAL -->
		<div id="menuadmin">
			<ul>
				<?
				if (!isset ($_COOKIE[ini_get(nivel_us)])){
						session_start();
				}
								
					if ($_SESSION[nivel]==5) { //Directivo
						?>
						<li><a href="zona-privada_admin_cursos_1.php"><i class="icon-education"></i> Cursos</a></li>
						<?
					}elseif ($_SESSION[nivel]==2) { //Admin Colegio
						?>
						<li><a href="zona-privada_admin_usuario.php"><i class="icon-user"></i> Usuarios</a></li>		
						<li><a href="zona-privada_admin_cursos_1.php"><i class="icon-calendar"></i> Cursos</a>	
							<ul>
								<li><a href="zona-privada_admin_cursos_1.php"><i class="icon-calendar"></i> Cursos</a></li>
								<li><a href="zona-privada_admin_profesores_1.php"><i class="icon-user"></i> Profesores</a></li>
								<li><a href="e_inicio.php"><i class="icon-check"></i> Encuestas</a></li>
							</ul>
						</li>					
						<li><a href="admin_contenido.php"><i class="icon-book"></i> Publicaciones</a></li>	
						<li><a href="admin_trabajo.php"><i class="icon-wrench"></i> Trabajo</a></li>	
						<li><a href="zona-privada_admin_comunicaciones_5_historico-de-envios.php"><i class="icon-envelope"></i> Comunicaciones</a></li>													
						<li><a href="zona-privada_admin_informes_1.php"><i class="icon-list-alt"></i> Informes</a></li>	
						<? if ($_SESSION[idcolegio]==111) { //Murcia ?>	
							<li><a href="a_facturacion.php"><i class="icon-shopping-cart"></i> Facturación</a></li>	
						<? } ?>
						<li><a href="sc_noconformidades.php"><i class="icon-thumbs-up"></i> Sistema calidad</a></li>
						<li><a href="zona-privada_colegio_3.php"><i class="icon-user"></i> Datos</a></li>
						<li><a href="http://www.activatie.org/interno"><i class="icon-edit"></i> Interno</a></li>
						<li><a href="empresas_marketing.php"><i class="icon-briefcase"></i> Empresas</a></li>
						<?
					}elseif ($_SESSION[nivel]==1) { //Admin Total 
						?>	
						<li><a href="zona-privada_admin_usuario.php"><i class="icon-user"></i> Usuarios</a></li> 								
						<li><a href="zona-privada_admin_cursos_1.php"><i class="icon-calendar"></i> Cursos</a>	
							<ul>
								<li><a href="zona-privada_admin_cursos_1.php"><i class="icon-calendar"></i> Cursos</a></li>
								<li><a href="zona-privada_admin_profesores_1.php"><i class="icon-user"></i> Profesores</a></li>
								<li><a href="e_inicio.php"><i class="icon-check"></i> Encuestas</a></li>
							</ul>
						</li>				
						<li><a href="m_materiales.php"><i class="icon-pencil"></i> Materiales</a></li> 					
						<li><a href="admin_contenido.php"><i class="icon-book"></i> Publicaciones</a></li> 											
						<li><a href="admin_trabajo.php"><i class="icon-wrench"></i> Trabajo</a></li> 	
						<li><a href="zona-privada_admin_comunicaciones_5_historico-de-envios.php"><i class="icon-envelope"></i> Comunicaciones</a></li>						
						<li><a href="zona-privada_admin_informes_1.php"><i class="icon-list-alt"></i> Informes</a></li>												
						<li><a href="#"><i class="icon-cog"></i> Configuración</a>	
							<ul>
								<li><a href="zona-privada_admin_cuentas.php"><i class="icon-home"></i> Colegios</a></li>
								<li><a href="empresas_marketing.php"><i class="icon-briefcase"></i> Empresas</a></li>
								<li><a href="a_facturacion.php"><i class="icon-shopping-cart"></i> Facturación</a></li>
								<li><a href="p_anuncios.php"><i class="icon-align-justify"></i> Banners</a></li>
								<li><a href="sc_noconformidades.php"><i class="icon-thumbs-up"></i> Sistema calidad</a></li>
								<li><a href="etiqueta.php"><i class="icon-tags"></i> Etiquetas</a></li>  
								<li><a href="familias.php"><i class="icon-tags"></i> Familias</a></li> 
								<li><a href="generica_correo0.php"><i class="icon-font"></i> Cursos: Textos Emails</a></li>
								<li><a href="z_admin_SMS_genericos.php"><i class="icon-font"></i> Cursos: Textos SMS</a></li> 
								
							</ul>
						</li>
						<?
					}else{
						
						
					} 
					session_start();
					?>
					<li><a href="../_control-user.php?logout"><i class="icon-off"></i> Salir</a></li>	
			</ul>
		</div>
		<!--FIN MENU HORIZONTAL-->
	
	
	
	

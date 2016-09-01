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
	<!--[if IE 6]>
	<link rel="stylesheet" type="text/css" href="css/hacksie6.css" />
	<![endif]-->
	
	<!--[if IE 7]>
	<link rel="stylesheet" type="text/css" href="css/hacksie7.css" />
	<![endif]-->
	
	<!--[if IE 8]>
	<link rel="stylesheet" type="text/css" href="css/hacksie8.css" />
	<![endif]-->
	
	<!--[if IE 9]>
	<link rel="stylesheet" type="text/css" href="css/hacksie9.css" />
		<script src="js/html5.js"></script>
	<![endif]--> 
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
			<?
			//iconos de cabecera
			/*
			<div class="grids">
				<div class="row">
				<div class="grid-12 titulo-seccion index-destacados">
					<!-- añadiremos al div.titulo-seccion las clases:
						ofertas-de-trabajo, publicaciones, formacion
						para que el gráfico del H2 sea el correspondiente a
						cada sección.-->
					<!-- pej: <div class="grid-12 titulo-seccion formacion index-destacados"> -->
					<h2><span>Información</span> destacada</h2>
				</div>
				</div>
			</div>			
						*/
			if ($menu=="formacion.php"){
				$textocabezah=" formacion";
			}elseif ($menu=="publicaciones.php"){
				$textocabezah=" publicaciones";				
			}elseif ($menu=="trabajos.php"){
				$textocabezah=" ofertas-de-trabajo";				
			}else{
				 $textocabezah=" index-destacados";
			}	
			?>
			<div class="grid-12 titulo-seccion<?=$textocabezah?>">
				<h2><span><?=$titulo1?></span> <?=$titulo2?></h2>
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
								
					if ($_SESSION[nivel]==4) { //Alumno    
						/*
								<li><i class="icon-book"></i> <a href="zona-privada_usuario_1.php">Mis Cursos</a></li>
								<li><i class="icon-book"></i> <a href="z-privada_usuario_5.php">Mis Compras</a></li>
								<li><i class="icon-user"></i> <a href="zona-privada_usuario_3.php">Mis Datos</a></li>
								<li><i class="icon-bell"></i> <a href="zona-privada_usuario_alertas.php">Mis Alertas</a></li>	
								<li><i class="icon-off"></i> <a href="http://www.activatie.org/moodle/login/logout.php?sesskey=<?=$_SESSION[sesskey]?>">Salir</a></li>
						
						*/
						?>
						<li><a href="zona-privada_usuario_1.php"><i class="icon-book"></i> Mis Cursos</a></li>
						<li><a href="z-privada_usuario_5.php"><i class="icon-user"></i> Mis Compras</a></li>
						<li><a href="zona-privada_usuario_3.php"><i class="icon-user"></i> Mis Datos</a></li>
						<li><a href="zona-privada_usuario_alertas.php"><i class="icon-bell"></i> Mis Alertas</a></li>	
						<? 
					}elseif ($_SESSION[nivel]==3) { //Profe
						?>
						<li><a href="zona-privada_usuario_1.php"><i class="icon-book"></i> Mis Cursos</a></li>
						<li><a href="z-privada_usuario_5.php"><i class="icon-user"></i> Mis Compras</a></li>
						<li><a href="zona-privada_usuario_3.php"><i class="icon-user"></i> Mis Datos</a></li>
						<li><a href="zona-privada_usuario_alertas.php"><i class="icon-bell"></i> Mis Alertas</a></li>	
						<li><a href="zona-privada_admin_cursos_1-profe.php"><i class="icon-book"></i> Cursos</a>
						
						<?
					}elseif ($_SESSION[nivel]==5) { //Directivo
						?>
						<li><a href="zona-privada_admin_cursos_1.php"><i class="icon-book"></i> Cursos</a></li>
						<!--<li><a href="zona-privada_admin_informes_1.php"><i class="icon-edit"></i> Informes</a></li>-->
						
						<?
					}elseif ($_SESSION[nivel]==2) { //Admin Colegio
						?>
						<li><a href="zona-privada_admin_cursos_1.php"><i class="icon-book"></i> Cursos</a></li>
						<li><a href="zona-privada_admin_usuario.php"><i class="icon-th"></i> Usuarios</a></li>	
						<li><a href="zona-privada_admin_profesores_1.php"><i class="icon-user"></i> Profesores</a></li>
						<li><a href="e_inicio.php"><i class="icon-book"></i> Encuestas</a></li>			
						<li><a href="zona-privada_admin_comunicaciones_5_historico-de-envios.php"><i class="icon-envelope"></i> Comunicaciones</a></li>							
						<li><a href="admin_contenido.php"><i class="icon-edit"></i> Publicaciones</a></li>	
						<li><a href="admin_trabajo.php"><i class="icon-edit"></i> Ofertas de trabajo</a></li>											
						<li><a href="zona-privada_admin_informes_1.php"><i class="icon-edit"></i> Informes</a></li>	
						<? if ($_SESSION[idcolegio]==111) { //Murcia ?>	
							<li><a href="a_facturacion.php"><i class="icon-edit"></i> Facturación</a></li>	
						<? } ?>
						<li><a href="sc_noconformidades.php"><i class="icon-edit"></i> Sistema calidad</a></li>
						<li><a href="zona-privada_colegio_3.php"><i class="icon-user"></i> Datos</a></li>
						<li><a href="http://www.activatie.org/interno"><i class="icon-edit"></i> Interno</a></li>
						<?
					}elseif ($_SESSION[nivel]==1) { //Admin Total 
						?>
						<li><a href="zona-privada_admin_cursos_1.php"><i class="icon-book"></i> Cursos</a></li>
						<li><a href="zona-privada_admin_profesores_1.php"><i class="icon-user"></i> Profesores</a></li>
						<li><a href="zona-privada_admin_usuario.php"><i class="icon-th"></i> Usuarios</a>
							<ul>
								<li><a href="usuario_desde_web.php"><i class="icon-th"></i> Nuevos Usuarios Web</a></li>
								<li><a href="zona-privada_admin_suscrito.php"><i class="icon-th"></i> Suscritos Web</a></li>
							</ul>
						</li>
						<li><a href="e_inicio.php"><i class="icon-book"></i> Encuestas</a></li>
						<li><a href="zona-privada_admin_comunicaciones_5_historico-de-envios.php"><i class="icon-envelope"></i> Comunicaciones</a></li>						
						<li><a href="zona-privada_admin_informes_1.php"><i class="icon-edit"></i> Informes</a></li>											
						<li><a href="admin_trabajo.php"><i class="icon-edit"></i> Ofertas de trabajo</a></li> 											
						<li><a href="admin_contenido.php"><i class="icon-edit"></i> Publicaciones</a></li>											
						<li><a href="#"><i class="icon-edit"></i> Configuración</a>	
							<ul>
								<li><a href="zona-privada_admin_cuentas.php"><i class="icon-lock"></i> Gestión de Cuentas</a></li>
								<li><a href="a_facturacion.php">Facturación</a></li>
								<li><a href="p_anuncios.php">Banners publicitarios</a></li>
								<li><a href="sc_noconformidades.php">Sistema calidad</a></li>
								<li><a href="generica_correo.php?id=95">Texto Publicaciones</a></li>
								<li><a href="generica_correo0.php">Textos Emails Cursos</a></li>
								<li><a href="z_admin_SMS_genericos.php">Textos SMS</a></li>
								<li><a href="etiqueta.php"><i class="icon-film"></i> Áreas/Etiquetas</a></li>
								<li><a href="provincia_comunidad.php"><i class="icon-film"></i>Comunidad/Provincia</a></li>
								<li><a href="provincia.php"><i class="icon-film"></i>Provincias</a></li>
								<li><a href="etiqueta.php">Áreas/Etiquetas</a></li>
								<li><a href="c_n_noticias.php">Nº Noticias Inicio</a></li>
								<li><a href="__informes.php">Textos Informes</a></li>
								<li><a href="z_admin_boletin_oficial.php">Boletines oficiales</a></li>
								<li><a href="generica_aviso_legal.php">Aviso Legal</a></li>
								<li><a href="generica_politica_venta.php">Política de venta</a></li>
								<li><a href="generica_sobre_ActivATIE.php">Sobre activATIE</a></li>
								<li><a href="generica_preguntas_frecuentes.php">Preguntas Frecuentes</a></li>
								<li><a href="_z_visitas.php">Estadísticas</a></li>
								<li><a href="c_pass.php">Cambiar Contraseña</a></li>
							</ul>
						</li>
						<?
					}else{
						
						
					} 
					session_start();
					?>
					<li><a href="http://www.activatie.org/moodle/login/logout.php?sesskey=<?=$_SESSION[sesskey]?>"><i class="icon-off"></i> Salir Seguro</a></li>	
			</ul>
		</div>
		<!--FIN MENU HORIZONTAL-->
	
	
	
	

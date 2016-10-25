<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
<head>
	
	<?php
		session_start();
		global $title;
		
		/** pestañas activas **/
		
		$portadaactive="";
		$formacionactive="";
		
		$uri=getUri();
		if (contieneUri("publicacion") !== false){
			
		}
		elseif (contieneUri("formacion") !== false){
			$formacionactive=" active ";
			$title = "Formación | ".$title;
		}	
		elseif (contieneUri("contacto") !== false){ 
			$title = "Contacto | ".$title;
		}			
		elseif (contieneUri("perfil") !== false){
			$title = "Perfil | ".$title;
		}			
		elseif (contieneUri("404") !== false){
			$title = "404 | ".$title;
		}	
		elseif ((contieneUri("portada") !== false) || ($uri=="/web/")){
			$portadaactive=" active ";
			$title = "Portada | ".$title;
		}
		else{
			
		}
		
		
	?>
	
	<title> <?=$title?> activatie</title>

	<!-- Meta -->
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<!-- Favicon -->
	<link rel="shortcut icon" href="<?=$baseUrl.$path.$imgpath?>favicon.ico">

	<!-- Web Fonts -->
	<link rel='stylesheet' type='text/css' href='//fonts.googleapis.com/css?family=Open+Sans:400,300,600&amp;subset=cyrillic,latin'>

	<!-- CSS Global Compulsory -->
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/plugins/bootstrap/css/bootstrap.min.css">
 	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/css/shop.style.css">
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/css/blog.style.css">
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/css/style.css">

	<!-- CSS Header and Footer -->
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/css/headers/header-default.css">
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/css/footers/footer-v2.css">

	<!-- CSS Implementing Plugins -->
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/plugins/animate.css">
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/plugins/line-icons/line-icons.css">
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/plugins/font-awesome/css/font-awesome.min.css">
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/plugins/owl-carousel/owl-carousel/owl.carousel.css">
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/plugins/login-signup-modal-window/css/style.css">
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/plugins/sky-forms-pro/skyforms/css/sky-forms.css">
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/plugins/sky-forms-pro/skyforms/custom/custom-sky-forms.css">
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/plugins/revolution-slider/rs-plugin/css/settings.css" type="text/css" media="screen">
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/plugins/master-slider/masterslider/style/masterslider.css">
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/plugins/master-slider/masterslider/skins/default/style.css">
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/plugins/cube-portfolio/cubeportfolio/css/cubeportfolio.min.css">
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/plugins/cube-portfolio/cubeportfolio/custom/custom-cubeportfolio.css">
	
	<!-- CSS Page Style -->
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/css/pages/profile.css">
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/css/pages/page_404_error.css">
  	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/css/pages/page_intro.css">
	
	<!-- CSS Theme -->
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/css/theme-colors/red.css" id="style_color">
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/css/theme-skins/dark.css">

	<!-- CSS Customization -->
	<link rel="stylesheet" href="<?=$baseUrl.$path?>assets/css/custom.css">
</head>


<body class="header-fixed header-fixed-space-default">
	<div class="wrapper">
		<!--=== Header ===-->
		<div class="header header-sticky">
			<div class="container">
				<!-- Logo -->
				<a class="logo" href="<?=$baseUrl.$path?>">
					<img src="<?=$baseUrl.$path.$imgpath?>activatie-logo.png" alt="Logo">
				</a>
				<!-- End Logo -->

				<!-- Topbar -->
				<div class="topbar">
					<ul class="loginbar pull-right">
					<? /* 
						<li class="hoverSelector">
							<i class="fa fa-globe"></i>
							<a>Languages</a>
							<ul class="languages hoverSelectorBlock">
								<li class="active">
									<a href="#">English <i class="fa fa-check"></i></a>
								</li>
								<li><a href="#">Spanish</a></li>
								<li><a href="#">Russian</a></li>
								<li><a href="#">German</a></li>
							</ul>
						</li>
						<li class="topbar-devider"></li>
						<li><a href="page_faq.html">Help</a></li>
						<li class="topbar-devider"></li>
						*/ ?>
						
						<? if ($_SESSION[controlactiva]){ ?> 
							<? if (($_SESSION[nivel]==1)||($_SESSION[nivel]==2)||($_SESSION[nivel]==5)){ ?>
								
								<li><a href="<?=$backendpath?>zona-privada_admin_cursos_1.php">Administración</a></li>
								<li class="topbar-devider"></li>
						
							<? } ?>
							<li class="hoverSelector">
								<i class="fa fa-user"></i>
								<a></a>
								<ul class="languages hoverSelectorBlock">
									<li class="active">
										<a href="#">Mis cursos </i></a>
									</li>
									<li><a href="perfil">Perfil</a></li>
									<li></li>
								</ul>
							</li>
							
							<li class="topbar-devider"></li>
							
							<li class="hoverSelector">
								<i class="fa fa-bell-o"></i>
								<span class="badge badge-red rounded">2</span>
							</li>
							
							<li class="topbar-devider"></li>
							
							<li>
								<a title="Cerrar sesión" href="_control-user.php?logout"><i class="fa fa-sign-out"></i></a>
								
							</li>

						<? } 
						else {
							
							include ($templatepath."login-register.php");
						?> 
							<li class="cd-log_reg"><a class="cd-signin" href="javascript:void(0);">Login</a></li>
							<li class="topbar-devider"></li>
							<li class="cd-log_reg"><a class="cd-signup" href="javascript:void(0);">Registro</a></li>
							<li class="topbar-devider"></li>
							<li><a href="Contacto">Contacto</a></li>
						<? } ?>
					
						
					</ul>
					
					<? 
						echo file_get_contents ($baseUrl.$path.$templatepath."social-links.php?right"); 
					?>
					
				</div>
				<!-- End Topbar -->

				<!-- Toggle get grouped for better mobile display -->
				<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
					<span class="sr-only">Toggle navigation</span>
					<span class="fa fa-bars"></span>
				</button>
				<!-- End Toggle -->
			</div><!--/end container-->

			<!-- Collect the nav links, forms, and other content for toggling -->
			<div class="collapse navbar-collapse mega-menu navbar-responsive-collapse">
				<div class="container">
					<ul class="nav navbar-nav">
						
						<!-- Ahora -->
						<li class="dropdown mega-menu-fullwidth">
							<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
								Ahora
							</a>
							<ul class="dropdown-menu">
								<li>
									<div class="mega-menu-content disable-icons">
										<div class="container">
											<div class="row equal-height">
												<div class="col-md-4 equal-height-in">
													<ul class="list-unstyled equal-height-list">
														<li><h3>Noticias</h3></li>

														<li><a href="<?=$baseUrl.$path.$actividadpath?>m/noticias"><i class="fa fa-sort-alpha-asc"></i> Portada</a></li>
														<li><a href="shortcode_typo_headings.html"><i class="fa fa-magic"></i> Nuevas</a></li>
														<li><a href="shortcode_typo_dividers.html"><i class="fa fa-ellipsis-h"></i> Populares</a></li>
														<li><a href="shortcode_typo_blockquote.html"><i class="fa fa-quote-left"></i> Más visitadas</a></li>
														<li><a href="shortcode_typo_boxshadows.html"><i class="fa fa-asterisk"></i> Destacadas</a></li>
														<li><a href="shortcode_typo_testimonials.html"><i class="fa fa-comments"></i> Enviar noticia</a></li>

													</ul>
												</div>
												<div class="col-md-4 equal-height-in">
													<ul class="list-unstyled equal-height-list">
														<li><h3>Consultas técnicas</h3></li>


														<li><a href="shortcode_typo_general.html"><i class="fa fa-sort-alpha-asc"></i> Portada</a></li>
														<li><a href="shortcode_typo_headings.html"><i class="fa fa-magic"></i> Nuevas</a></li>
														<li><a href="shortcode_typo_dividers.html"><i class="fa fa-ellipsis-h"></i> Populares</a></li>
														<li><a href="shortcode_typo_blockquote.html"><i class="fa fa-quote-left"></i> Más visitadas</a></li>
														<li><a href="shortcode_typo_boxshadows.html"><i class="fa fa-asterisk"></i> Destacadas</a></li>
														<li><a href="shortcode_typo_testimonials.html"><i class="fa fa-comments"></i> Enviar consulta técnica</a></li>

													</ul>
												</div>
												<div class="col-md-4 equal-height-in">
													<ul class="list-unstyled equal-height-list">
														<li><h3>Foros de debate</h3></li>

														<li><a href="shortcode_typo_general.html"><i class="fa fa-sort-alpha-asc"></i> Portada</a></li>
														<li><a href="shortcode_typo_headings.html"><i class="fa fa-magic"></i> Nuevas</a></li>
														<li><a href="shortcode_typo_dividers.html"><i class="fa fa-ellipsis-h"></i> Populares</a></li>
														<li><a href="shortcode_typo_blockquote.html"><i class="fa fa-quote-left"></i> Más visitadas</a></li>
														<li><a href="shortcode_typo_boxshadows.html"><i class="fa fa-asterisk"></i> Destacadas</a></li>
														<li><a href="shortcode_typo_testimonials.html"><i class="fa fa-comments"></i> Abrir debate</a></li>

													</ul>
												</div>
												
											</div>
										</div>
									</div>
								</li>
							</ul>
						</li>
						<!-- End Ahora -->

						<!-- Formación -->
						<li class="dropdown sindesplegable <?=$formacionactive?>">
							<a href="Formacion" >
								Formación
							</a>
						</li>
						<!-- End Formación -->

						<!-- Materiales -->
						<li class="dropdown mega-menu-fullwidth">
							<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
								Materiales
							</a>
							<ul class="dropdown-menu">
								<li>
									<div class="mega-menu-content disable-icons">
										<div class="container">
											<div class="row equal-height">
												<div class="col-md-4 equal-height-in">
													
													
													
													
													
													
													
													<ul class="list-unstyled equal-height-list">
														<li><a href="Materiales">Todos las categorías</a></li>
														<li><a href="One-Pages/Travel/index.html">Aislamiento</a></li>
														<li><a href="One-Pages/RealEstate/index.html">Bloques de vidrio <small class="color-red">New</small></a></li>
						                				<li><a href="One-Pages/Courses/index.html">Cementos, morteros y yesos</a></li>
														<li><a href="One-Pages/Business/index.html">Circulación y accesos</a></li>
													</ul>
												</div>

												<div class="col-md-4 equal-height-in">
													<ul class="list-unstyled equal-height-list">
														<li><a href="One-Pages/App/index.html">Cubiertas y tejados</a></li>
														<li><a href="One-Pages/Gym/index.html">Estructuras <small class="color-red">New</small></a></a></li>
						                <li><a href="One-Pages/Construction/index.html">Gravas y áridos </a></li>
														<li><a href="One-Pages/Charity/index.html">Herramientas para construcción <small class="color-red">New</small></a></a></li>
														
													</ul>
												</div>
												<div class="col-md-4 equal-height-in">
													<ul class="list-unstyled equal-height-list">
														<li><a href="One-Pages/Agency/index.html">Iluminación </a></li>
														<li><a href="One-Pages/Spa/index.html">Impermeabilización</a></li>
														<li><a href="One-Pages/Spa/index.html">Instalaciones </a></li>
														<li><a href="One-Pages/Restaurant/index.html">Particiones y elementos de división espacial <small class="color-red">New</small></a></a></li>
														
													</ul>
												</div>
											</div>
										</div>
									</div>
								</li>
							</ul>
						</li>
						<!-- End Materiales -->
						
						<!-- Profesionales -->
						<li class="dropdown sindesplegable">
							<a href="profesionales" >
								Arquitectos técnicos
							</a>
						</li>
						<!-- End Profesionales -->

						<!-- Publicaciones -->
						<li class="dropdown sindesplegable">
							<a href="publicaciones" >
								Publicaciones
							</a>
						</li>
						<!-- End Publicaciones -->

						<!-- Trabajo -->
						<li class="dropdown">
							<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown">
								Trabajo
							</a>
							<ul class="dropdown-menu">
								<li><a href="ofertas">Ofertas</a></li>
								<li><a href="tutorias">Tutorías profesionales</a></li>
							</ul>
						</li>
						<!-- End Trabajo -->

						<!-- APPs -->
						<li class="dropdown sindesplegable <?=$appsactive?>">
							<a href="Apps" >
								APPs
							</a>
						</li>
						<!-- End APPs -->
						
						<!-- Search Block -->
						<!--
						<li>
							<i class="search fa fa-search search-btn"></i>
							<div class="search-open">
								<div class="input-group animated fadeInDown">
									<input type="text" class="form-control" placeholder="Search">
									<span class="input-group-btn">
										<button class="btn-u" type="button">Go</button>
									</span>
								</div>
							</div>
						</li>
						-->
						<!-- End Search Block -->
					</ul>
				</div><!--/end container-->
			</div><!--/navbar-collapse-->
		</div>
		<!--=== End Header ===-->

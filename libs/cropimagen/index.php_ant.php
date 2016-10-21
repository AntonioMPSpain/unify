<?
include("../../_seguridadfiltro.php"); 
$accion=$_GET['accion'];
$id=strip_tags($_REQUEST['id']);
$tipo=strip_tags($_REQUEST['tipo']);
if (($id=='')||($tipo=="")){
	//header("Location: index.php?salir=true");
	echo "parametros incorr";
	exit();
}
include("../../_cone.php"); 
if($accion=='guardar'){
	$img_src=strip_tags($_REQUEST['img_src']);
	
	$origen = $img_src;
	$nombre = str_replace("uploads/ready/","",$img_src );
	$destino = '../'.$nombre;
	
	rename($origen, $destino);	
	
	
	if ($tipo=="portadapublicacion"){
		$sql="UPDATE generica SET img2='$nombre' WHERE  id='$id';";
	}
	elseif ($tipo=="fichapublicacion"){
		$sql="UPDATE generica SET img1='$nombre' WHERE id='$id';";
	}
	elseif ($tipo=="portadacurso"){
		$sql="UPDATE curso SET imagen='$nombre' WHERE id='$id';";
	}
	elseif ($tipo=="fichacurso"){
		$sql="UPDATE curso SET img2='$nombre' WHERE id='$id';";
	}
	
	$link=conectar(); //Postgresql
	$Query = pg_query($link,$sql);
	
	if ($Query){
	
		$_SESSION[esterror]="Guardado correctamente";
	}
	else{
		$_SESSION[esterror]="No se ha podido guardar, contacte con administrador.";
	}
	header("Location: ../../admin_contenido3_imagen.php?est=ok&id=$id&tipo=publicacion");
	exit();
}


// load mobile detect class
require_once 'includes/mobileDetect.php';
$detect = new Mobile_Detect;
$deviceType = ($detect->isMobile() ? ($detect->isTablet() ? 'tablet' : 'phone') : 'computer');

// load configuration


if ($tipo=="portadapublicacion"){
	require_once "includes/configPubliPortada.php";
}
elseif ($tipo=="fichapublicacion"){
	include_once('includes/configPubliFicha.php');
}
elseif ($tipo=="portadacurso"){
	include_once('includes/configCursoPortada.php');
}
elseif ($tipo=="fichacurso"){
	include_once('includes/configCursoFicha.php');
}

?><!DOCTYPE html>
<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--><html class="no-js"><!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<title>Subir imagen y guardar</title>
		<link rel="stylesheet" type="text/css" href="css/reset.css" />
		<link rel="stylesheet" type="text/css" href="css/imgareaselect-default.css" />
		<link rel="stylesheet" type="text/css" href="css/styles.css" />
		<script type="text/javascript">
			var useMobile=<?php if($deviceType=='tablet'||$deviceType=='phone') { echo 'true';} else {echo 'false';}?>;
		</script>
	</head>

	<body>
		<!-- content wrapper begin -->
		<div class="wrapper">

			<div class="uploader">

				<!-- first step upload image begin -->
				<div id="big_uploader">
					<form name="upload_big" id="upload_big" class="uploaderForm" method="post" enctype="multipart/form-data" action="upload.php?act=upload&tipo=<?=$tipo?>" target="upload_target">
						<h3><strong>Paso 1: Subir imagen original</strong></h3>
						<div class="fileWrapper">
							<a class="fileButton">Seleccionar</a>
							<input name="photo" id="file" class="fileInput" size="27" type="file" />	
						</div>
						<input type="hidden" name="width" value="<?=$canvasWidth?>" />
						<input type="hidden" name="height" value="<?=$canvasHeight?>" />              
						<input type="submit" name="action" value="Subir imagen" class="inputSubmit" />
					</form>
					<div id="notice" class="notice">Subiendo...</div>
				</div>
				<!-- first step upload image end -->
				
				<div class="content">
					
					<!-- second step selection begin -->
					<div id="uploaded">
						<h3><strong>Paso 2: Imagen subida - Seleccione área</strong></h3>
						<div id="div_upload_big" style="width:<?=$bigWidthPrev?>px;height:<?=$bigHeightPrev?>px;"></div>
						<div class="uploadThumbWrapper">
							<?php if($deviceType=='tablet'||$deviceType=='phone') { ?>
							<div class="mobileSelection">
								<a id="selLeft">izquierda</a>
								<a id="selRight">derecha</a>
								<a id="selUp">arriba</a>
								<a id="selDown">abajo</a>
								<a id="selResize">grande</a>
								<a id="selResizeSmall">pequeño</a>
							</div>
							<?php } ?>
							<form name="upload_thumb" id="upload_thumb" class="uploaderForm" method="post" action="upload.php?act=thumb&tipo=<?=$tipo?>" target="upload_target">
								<input type="hidden" name="img_src" id="img_src" class="img_src" /> 
								<input type="hidden" name="height" value="0" id="height" class="height" />
								<input type="hidden" name="width" value="0" id="width" class="width" />
								<input type="hidden" id="y1" class="y1" name="y" />
								<input type="hidden" id="x1" class="x1" name="x" />
								<input type="hidden" id="y2" class="y2" name="y1" />
								<input type="hidden" id="x2" class="x2" name="x1" />                         
								<input type="submit" value="Generar imagen" class="fileButton" />
							</form>
							<div id="notice2" class="notice">Generando imagen...</div>
						</div>
					</div>
					<!-- second step selection end -->
					
					<!-- preview the selection begin -->
					<div class="previewWindow">
						<h3>Visualización previa</h3>
						<div class="previewWrapper" style="width:<?=$bigWidthPrev?>px;height:<?=$bigHeightPrev?>px;">
							<div id="preview"></div>
						</div>
					</div>
					<!-- preview the selection end -->

					<!-- third step generated thumb begin -->
					<div id="thumbnail">
						<div hidden class="thumbWrapper" style="width:<?=$bigWidthPrev?>px;height:<?=$bigHeightPrev?>px;">
							<div  id="div_upload_thumb"></div>
						</div>
						<div class="detailWrapper">
							<div id="details">
							<form action="index.php?accion=guardar&id=<?=$id?>&tipo=<?=$tipo?>" method="post" enctype="multipart/form-data">
								<input type="hidden" name="img_src" class="img_src" size="40" value="" />
								<div id="savebutton"class="form-actions">
									<button type="submit"  class="fileButton">Guardar</button>
								</div>
							</form>
							</div>
						</div>
					</div>
					<!-- third step generated thumb end -->
					
				</div>
				
				<!-- hidden iframe begin -->
				<iframe id="upload_target" name="upload_target" src=""></iframe>
				<!-- hidden iframe end -->

			</div>

		</div>
		<!-- content wrapper end -->
		
		<script type="text/javascript" src="js/jquery-1.8.2.min.js"></script>
		<script type="text/javascript" src="js/jquery.imgareaselect.min.js"></script>
		<?
		if ($tipo=="portadapublicacion"){
			?><script type="text/javascript" src="js/effectsPubliPortada.js"></script><?
		}
		elseif ($tipo=="fichapublicacion"){
			?><script type="text/javascript" src="js/effectsPubliFicha.js"></script><?
		}
		elseif ($tipo=="portadacurso"){
			?><script type="text/javascript" src="js/effectsCursoPortada.js"></script><?
		}
		elseif ($tipo=="fichacurso"){
			?><script type="text/javascript" src="js/effectsCursoFicha.js"></script><?
		}
		?>
		<?php if($deviceType=='tablet'||$deviceType=='phone') { ?>
		<script type="text/javascript" src="js/touch.js"></script>
		<?php } ?>

	</body>
</html>
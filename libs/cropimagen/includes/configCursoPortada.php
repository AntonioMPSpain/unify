<?php
// factor for the real size of the uploaded image
$sizefactor = "5"; 

// size of the big, preview and thumb container
$bigWidthPrev = 900;
$bigHeightPrev = 600;

$anchurafinal = 450;
$alturafinal = 300;

// canvas size for the uploaded image
$canvasWidth = $bigWidthPrev * $sizefactor;
$canvasHeight = $bigHeightPrev * $sizefactor;

// file type error
$fileError = 'Archivo no permitido. Formatos permitidos GIF, JPG y PNG.'; 
$sizeError = 'Archivo muy grande. Maximo 1.3MB.';

// image upload folders
$imgthumb = 'uploads/ready/'; // folder for the uploads after cropping
$imgtemp = 'uploads/temp/'; // temp-folder before cropping
$imgbig = 'uploads/big/'; // folder with big uploaded images

// max file-size for upload in bytes, default: 3mb
$maxuploadfilesize = 33200000;

// background color of the canvas as rgb, default:white
$canvasbg = array(
	'r' => 255,
	'g' => 255,
	'b' => 255
);
?>

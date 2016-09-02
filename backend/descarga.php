<?php
//Ruta
	$f = explode("/", $_SERVER['PHP_SELF']);
	$archivophp=$f[count($f)-1];
	$ruta_temp=str_replace($archivophp,"",$_SERVER['PHP_SELF']);
	$ruta=$_SERVER['DOCUMENT_ROOT']."".$ruta_temp;
//fin ruta
$carpeta="files/"; // Para la carpeta 
$documento=$_GET["documento"]; 
$trozos = explode(".", $documento); 
$extension = end($trozos);
if (($extension=="odt") ||($extension=="ods") ||($extension=="rar") ||($extension=="zip") ||($extension=="csv") ||($extension=="rtf") ||($extension=="pptx") ||($extension=="ppt") ||($extension=="pdf") ||($extension=="doc")||($extension=="docx")||($extension=="xls")||($extension=="xlsx")||($extension=="jpg")){
	$ruta2=$_GET["ruta"];
	$ruta2=str_replace("\\","/",$ruta2);
	$enlace = $ruta.$carpeta.$ruta2.$documento; 
	if (!is_file ($enlace)){
		$carpeta="files_/"; // Para la carpeta 
		$enlace = $ruta.$carpeta.$ruta2.$documento; 
	}
	if (!is_file ($enlace)){
		$carpeta="email_archivos/"; // Para la carpeta 
		$enlace = $ruta.$carpeta.$ruta2.$documento; 
	}
	if (is_file ($enlace)){
		header ("Content-Disposition: attachment; filename=".$documento."\n\n"); 
		header ("Content-Type: application/octet-stream");
		header ("Content-Length: ".filesize($enlace));
		@readfile($enlace);
	}else{
		echo "Archivo no encontrado.";
	}
}else{
	echo $extension;
	echo " Extension no permitida.";
}

?>

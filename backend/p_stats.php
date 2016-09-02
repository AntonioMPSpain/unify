<?php

include_once("_funciones.php"); 
include_once("_cone.php"); 

$idanuncio = $_POST['idanuncio'];
$idbanner = $_POST['idbanner']; 
$ip = getRealIP();

$sql = "INSERT INTO p_stats(idbanner,idanuncio, ip) VALUES ('$idbanner', '$idanuncio', '$ip')";
posgre_query($sql);

?>
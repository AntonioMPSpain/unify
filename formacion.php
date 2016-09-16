<? 

$c_directorio_img = "/var/www/web";

include_once "_config.php";

$accion=($_REQUEST['accion']); 
$texto=$_REQUEST['texto'];
$m=$_REQUEST['m'];

if ($m=="historico"){
	$asc=" DESC ";
	$sqlquitar=" AND fecha_fin_publicacion<'NOW()' ";
}
else{
	$asc=" ASC ";
	$sqlquitar=" AND fecha_publicacion<='NOW()' AND fecha_fin_publicacion>='NOW()' ";
}



$result=posgre_query("SELECT * FROM curso WHERE borrado=0 AND (estado=1 OR estado=2) $sqlquitar $busqueda ORDER BY fecha_inicio $asc, RANDOM();") ;

$i=0;
while ($row = pg_fetch_array($result)){
	
	$curso = "";
	
	$curso['id'] = $idcurso = $row['id'];
	$curso['nombre'] = $row['nombre'];
	$curso['modalidad'] = $row['modalidad'];
	$curso['fecha_inicio'] = getFechaConMes($row['fecha_inicio']);
	$curso['imagen'] = $imgcursosbackendpath.$row['imagen'];
	
	$resultet=posgre_query("SELECT etiqueta.color, etiqueta.tipo,etiqueta.texto FROM etiqueta,curso_etiqueta WHERE curso_etiqueta.idcurso='$idcurso' AND etiqueta.id=curso_etiqueta.idetiqueta AND etiqueta.borrado=0;") ; 
	if ($rowet = pg_fetch_array($resultet)){
		$curso['area'] = $rowet["texto"];
		$curso['color'] = $rowet["color"];
	}
	
	$cursos[$i] = $curso;
	$i++;
}

include ($templatepath."header.php");
$twig->display('formacion.php', array('cursos'=>$cursos));


/** BANNER **/
include_once($backendpath."p_funciones.php"); 
$idbanner = 4;
$banner = getBanner($idbanner);
echo $banner;
/** FIN BANNER **/

//include ($templatepath."footer.php");

		
		$c1=1;
		$pagina=strip_tags(($_GET['pagina']));	
		if ($registros==""){
			$registros =21;
		}
		if (!$pagina) { 
			$inicio = 0; 
			$pagina = 1; 
		}else{ 
			$inicio = ($pagina - 1) * $registros; 
		} 
		
		if (($accion=="buscar")&&($texto<>"")){
			$busqueda = " AND sp_asciipp(nombre) ILIKE sp_asciipp('%$texto%')"; 
		}
		
		$numcursos=0;
		$numcursospermanente=0;
		$numcursospermanente2=0;
		$numcursospermanente3=0;
		
		
		if ($m<>"online"){
			
			$result2=posgre_query("SELECT * FROM curso WHERE borrado=0 AND (estado=1 OR estado=2) AND modalidad=3 $sqlquitar $busqueda ORDER BY RANDOM();") ;
			$numcursospermanente3 = pg_num_rows($result2);
			$result2=posgre_query("SELECT * FROM curso WHERE borrado=0 AND (estado=1 OR estado=2) AND modalidad=3 $sqlquitar $busqueda ORDER BY RANDOM() limit $registros offset $inicio;") ;
			$numcursospermanente = pg_num_rows($result2);
			
			$numcursospermanente2=0;
			if ($pagina>1){
				$inicio2 = ($pagina - 2) * $registros;
				$result2=posgre_query("SELECT * FROM curso WHERE borrado=0 AND (estado=1 OR estado=2) AND modalidad=3 $sqlquitar $busqueda ORDER BY RANDOM() limit $registros offset $inicio2;") ;
				$numcursospermanente2 = pg_num_rows($result2);
			}
		}
		
		if ($m<>"permanente"){
			
			$result=posgre_query("SELECT * FROM curso WHERE borrado=0 AND (estado=1 OR estado=2) AND modalidad<>3 $sqlquitar $busqueda ORDER BY fecha_inicio $asc, RANDOM();") ;
			$numcursos2 = pg_num_rows($result);
			$result=posgre_query("SELECT * FROM curso WHERE borrado=0 AND (estado=1 OR estado=2) AND modalidad<>3 $sqlquitar $busqueda ORDER BY fecha_inicio $asc, RANDOM() limit ($registros-$numcursospermanente) offset ($inicio-$numcursospermanente2);") ;
			$numcursos = pg_num_rows($result);
		}
		
		$totalcursos2 = $numcursos2+$numcursospermanente3;
		$total_paginas = ceil($totalcursos2 / $registros); 
		$totalcursos = $numcursos+$numcursospermanente;
		$cadencia = ceil($numcursos/$numcursospermanente);
		
		$contadorpermanente=0;
		$permas=0;
		$rand = rand(0,$cadencia);
		$cursos = $numcursos;	
			
		if ($totalcursos>$registros){
			$totalcursos=$registros;
		}
				
		for ($i=0;$i<$totalcursos;$i++){

			if (($contadorpermanente==$rand)&&($permas<$numcursospermanente)||($m=="permanente")||($cursos==0)){
				$permas++;
				$row = pg_fetch_array($result2);
				//$contadorpermanente=1;
			}
			else{
				$row = pg_fetch_array($result);
				$cursos--;
			}
		
		
			if ($contadorpermanente==$cadencia){
				$contadorpermanente=-1;
			}
			
			$contadorpermanente++;
			
			
			$id=strip_tags($row["id"]);
			$presentacion=strip_tags($row["presentacion"], '<a>,<br />,<br>');
			$cantidadCaracteres = 35; 
			$cantidadCaracteres2 = 340; 
			$modalidad=$row["modalidad"];
			$fecha_inicio=$row['fecha_inicio'];
			
			$precio = "";
			if ($modalidad==0){
				$preciotachadoc = $row['preciotachadooc'];
				$preciotachadon = $row['preciotachadoon'];
				$precio = $row["precioco"];
			}
			elseif ($modalidad==1){
				$preciotachadoc = $row['preciotachadoc'];
				$preciotachadon = $row['preciotachadon'];
				$precio = $row["precioc"];
			}
			elseif ($modalidad==3){
			
				$preciotachadoc = $row['preciotachadopc'];
				$preciotachadon = $row['preciotachadopn'];
				$precio = $row["preciocp"];
			}
			else{
				$preciotachadoc = $row['preciotachadooc'];
				$preciotachadon = $row['preciotachadoon'];
				
				if (($preciotachadoc==0)||($preciotachadon==0)){
					$preciotachadoc = $row['preciotachadoc'];
					$preciotachadon = $row['preciotachadon'];
				}
				
				$precio = $row["precioco"];
			}
			
			$preciotexto="";
			if ($precio==0){
				$preciotexto = "<b>¡Gratuito!</b>";
			}
			
			$preciopromociontexto = "";
			if (($preciotachadoc<>0)||($preciotachadon<>0)){
				$preciopromociontexto = "<b>¡Precio en promoción!</b>";
			}
			
			$privado = $row["privado"];
			
			$privadotexto="";
			if ($privado==1){
				$privadotexto = " privado ";
			}
			
			
			/* include ("_cortaparrafo.php"); */
			$enlace=substr($row["nombre"],0,strrpos(substr($row["nombre"],0,$cantidadCaracteres)," "));
			$descri=substr($presentacion,0,strrpos(substr($presentacion,0,$cantidadCaracteres2)," "))." ";
	
			$destino=$c_directorio_img."/imagen/".$row["img2"];
			//Video
			/*
			$link2v=iConectarse(); 
			$result2=pg_query($link2v,"SELECT id,nombre,codigo FROM video WHERE padre='$id' AND borrado=0;");// or die (mysql_error());  
			if (($result2)&&(pg_num_rows($result2)!=0)) {
				$masvideo='<span class="video"></span>';
			}else{
				$masvideo='';
			}
			*/
			$video=trim($row["video"]);
			if ($video<>"") {
				$masvideo='<span class="video"></span>';
			}else{
				$masvideo='';
			}
			
			//imagen
			if(file_exists($destino)&&($row["imagen"]<>"")){ //comprobamos que existe la foto
				$imagen="imagen/".$row["imagen"];
			}else{
				$imagen="nofoto.png";
			}
			if (($c1==1)||(($c1==2))){
				$direccioncss="left";
				$mascss1='';
			}else{
				$direccioncss="right";
				$mascss1='<!--Después de cada articulo-magazine-right, tienes que colocar un div.clearfix como este:-->
							<div class="clearfix"></div>';
				$c1=0;	
			}
			if ($row["modalidad"]==0){ 
				$modalidadtexto="[on-line]";
			}
			if ($row["modalidad"]==1){
				$modalidadtexto="[presencial]";
			}
			if ($row["modalidad"]==2){
				$modalidadtexto="[presencial y on-line]";
			}
			if ($row["modalidad"]==3){
				$modalidadtexto="[permanente]";
			}
			$c1++;
			?> 
			<div class="row blog-item articulo-publicacion articulo-publicacion-<?=$direccioncss?> <?=$privadotexto?> grid-3 ">
			<!-- Nota: se añade la clase articulo-publicacion-left al primero y segundo, articulo-publicacion-right al tercer, articulo-publicacion-left al cuarto y quinto, y así sucesivamente.-->
			<!-- Nota2: los artículos que sean privados, también llevan asociada la clase "privado" -->
				<div class="grid-12 blog-foto">
					<a href="curso.php?id=<?=$row["id"]?>"><img src="<?=$imagen?>" alt="<?=$row["nombre"]?>" /><?=$masvideo?></a>
					<!-- no pasa nada porque la imagen siga teniendo 540x400 de tamaño. Ya lo ajusto yo vía CSS para que quede bien y te ahorro trabajo a ti -->
				</div>
				<div class="clearfix"></div>
				<h3 class="noticia"><a href="curso.php?id=<?=$row["id"]?>"><?=$row["nombre"]?></a> <?=$modalidadtexto?> </h3>
				<p class="descripcion">
				<? 
				$resultet=posgre_query("SELECT etiqueta.tipo as tipo,etiqueta.texto as texto, etiqueta.id as id FROM etiqueta,curso_etiqueta WHERE curso_etiqueta.idcurso='$id' AND etiqueta.id=curso_etiqueta.idetiqueta AND etiqueta.borrado=0;") ;//or die ("Erro_".mysql_error()); 
				if (pg_num_rows($resultet)>0){?>
						<?
						$rowet = pg_fetch_array($resultet);
							?>
							[<?=$rowet["texto"]?>] 	
							<? 
							?>
				</p>
				<? }
				if ($fecha_inicio<>""){ ?>
					<p style="margin:5px 0 0 0; text-align:center; font-size:11px;">Inicio: <?=cambiaf_a_normal($fecha_inicio)?></p>
				<? } ?>
				<p style="font-size:14px; margin:3px 0 5px 0; text-align:center;"><?=$preciotexto?><?=$preciopromociontexto?></p>
			</div>
			<!--fin ficha breve curso-->
			<?=$mascss1?>
		 <?
		} //fin del while
		

		
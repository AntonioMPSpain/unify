<?

include_once("_funciones.php"); 
include_once("_cone.php");
include_once "_config.php";

global $imgbannerspath;

?>

<script type="text/javascript">
	function onClick(e) {
		var idanuncio = e.getAttribute('id');
		var idbanner = e.getAttribute('idbanner');
		var post = 'idanuncio='+idanuncio+'&idbanner='+idbanner;
		
		var req = new XMLHttpRequest();
		req.open('POST', '<?php echo ($backendpath); ?>p_stats.php', true);
		req.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
		req.onreadystatechange = function(){
			if (req.readyState !== 4 || req.status !== 200) return;
				
		 };
		req.send(post);
	 };
	 
	function changeImage(idbanner, anuncios, images, links, x, tam){
		
		if (tam>2){
			
			setTimeout(function () {
				
				var img = document.getElementById("img_"+idbanner);
				
				img.onload="";
				img.src = <?php echo json_encode($imgbannerspath); ?>+images[x];
				
				var b = document.getElementsByClassName("banner_"+idbanner);
				b[0].id = anuncios[x];
				
				var a = document.getElementById("link_"+idbanner);
				a.href = links[x];
				
				
				
				x++;
				
				if (x>=tam){
					x=1;
				}
				
				changeImage(idbanner, anuncios, images, links, x, tam);
			}, 8000);
			
		}
	};
	
</script>


<?

function esBannerActivo($idbanner){
	
	$sql = "SELECT id FROM p_banners WHERE activo=1 AND id='$idbanner'";
	$result  = posgre_query($sql);
	if (pg_num_rows($result)>0){
		return true;
	}
	return false;
	
}

function getTipo($idbanner){
	
	$sql = "SELECT * FROM p_banners WHERE id='$idbanner'";
	$result  = posgre_query($sql);
	echo pg_last_error();
	if ($row = pg_fetch_array($result)){
		$tipo=$row["tipo"];
	}
	
	return $tipo;
}



function getAnuncios($idbanner){
	
	if (esBannerActivo($idbanner)){
		$sql = "SELECT * FROM p_bannersanuncios WHERE idbanner='$idbanner' AND borrado=0 AND idanuncio IN ( SELECT id FROM p_anuncios WHERE borrado=0 AND estado=0 AND fechainicio<='NOW()' AND fechafin>='NOW()') ORDER BY RANDOM()";
		$result = posgre_query($sql);
		return $result;
	}
	
	return "";
	
}



function getBanner($idbanner){
	$banner="";
		
	if (esBannerActivo($idbanner)){	
	
		$tipo = getTipo($idbanner);
			
		$result = getAnuncios($idbanner);
		
		$primer=true;
		$x=1;
		$anuncios = array();
		$links = array();
		$images = array();
		
		while ($row = pg_fetch_array($result)){
			
			$idanuncio=$row['idanuncio'];
			$sql2 = "SELECT * FROM p_anuncios WHERE id='$idanuncio'";
			$result2 = posgre_query($sql2);
			if ($row2 = pg_fetch_array($result2)){
				$url=$row2["url"];

				if($tipo==1){
					$width = 250;
					$height = 250;
					$imagen=$row2["imagen1"];
					$classbanner = " bloque-lateral ";
				}
				else{
					$width = 790;
					$height = 90;
					$imagen=$row2["imagen0"];
					$classbanner = "";
				}
				
				if ($imagen<>""){
					
					if ($primer){
						$primer = false;
						$primeridanuncio = $idanuncio;
						$primerimg = $imagen;
						$primerlink = $url;
						
					}
					
					$anuncios[$x] = $idanuncio;
					$images[$x] = $imagen;
					$links[$x] = $url;
					
					$x++;
					
				}
			}
		}
		
		if (!$primer){	
		
		
			$anuncios = json_encode($anuncios);
			$images = json_encode($images);
			$links = json_encode($links); 
			
			global $imgbannerspath;
			$banner = '<div class="banner_'.$idbanner.' publicidad '.$classbanner.'" id="'.$primeridanuncio.'" idbanner='.$idbanner.' onClick="onClick(this)" style="margin-bottom:10px;"><a id="link_'.$idbanner.'" href="'.$primerlink.'" target="_blank">
						<img class="img-responsive full-width" onload=\'changeImage('.$idbanner.','.$anuncios.','.$images.','.$links.',2, '.$x.');\' id="img_'.$idbanner.'" style="width:'.$width.'px; height='.$height.'px;" width="'.$width.'" height="'.$height.'" src="'. $imgbannerspath.$primerimg .'" alt=""></a></div>';	
		
		}
		
	}
	
	return $banner;
	
}

function getTextoTipoBanner($tipo){
	
	$textotipo;		
	if ($tipo==0){
		$textotipo = "Banner horizontal";
	}
	elseif($tipo==1){
		$textotipo = "Banner cuadrado";
	}
	else{
		$textotipo = "No existe tipo";
	}
	
	return $textotipo;
}


?>
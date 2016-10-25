			<!--.Abajo pantilla2.-->
<div class="clearfix"></div>
<!--fin contenido-principal-->
</div>
<!--fin pagina-->


	<br />
	<!--<div class="navbar-inner2 nav logo-sponsors">
		<ul>
			<li><a href="http://www.aparejadoresalbacete.es" ><img src="img/logo-albacete.png" alt="APAREJADORES ALBACETE" title="COAATIIE ALBACETE"></a></li>
			<li><a href="http://www.coaatalicante.org" ><img src="img/logo-alicante.png" alt="APAREJADORES ALICANTE" title="COAATIE ALICANTE"></a></li>
			<li><a href="http://www.coaatba.com" ><img src="img/logo-badajoz.png" alt="APAREJADORES BADAJOZ" title="COAATIE BAJADOZ"></a></li>
			<li><a href="http://www.coaatcan.com" ><img src="img/logo-cantabria.png" alt="APAREJADORES CANTABRIA" title="COAATIE CANTABRIA"></a></li>
			<li><a href="http://www.coaatcuenca.com" ><img src="img/logo-cuenca.png" alt="APAREJADORES CUENCA" title="COAATIE CUENCA"></a></li>		
			<li><a href="http://www.coaatcordoba.com" ><img src="img/logo-cordoba.png" alt="APAREJADORES CÓRDOBA" title="COAATIE CÓRDOBA"></a></li>
			<li><a href="http://www.coaatgr.es" ><img src="img/logo-granada.png" alt="APAREJADORES GRANADA" title="COAATIE GRANADA"></a></li>			
			<li><a href="http://www.coaatiemu.es" ><img src="img/logo-murcia.png" alt="APAREJADORES MURCIA" title="COAATIE MURCIA"></a></li>
			<li><a href="http://www.coaatsa.org" ><img src="img/logo-salamanca.png" alt="APAREJADORES SALAMANCA"  title="COAATIE SALAMANCA"></a></li>
			<li><a href="http://www.caatvalencia.es" ><img src="img/logo-valencia.png" alt="APAREJADORES VALENCIA"  title="COAATIE VALENCIA"></a></li>
		</ul>
	</div>
	-->
	<!--fin logo-sponsors-->
	<!--<br />
	<div id="menu-main" class="navbar-inner2 nav menu-bottom">
		<ul>
			<li><a href="index.php" >Inicio</a> · </li>
			<li><a href="sobre_ActivATIE.php" >Sobre activATIE</a> · </li>
			<li><a href="buzon_sugerencias.php" >Buzón de sugerencias</a> · </li>
			<li><a href="contacto.php" >Contacto</a> · </li>
			<li><a href="aviso_legal.php" >Aviso legal</a> · </li>
			<!--<li><a href="trabajo_solicita.php" >Trabajo solicitar</a> · </li>
			<li><a href="trabajos.php" >TRABAJOS</a> · </li>
			<li><a href="trabajo_boe.php" >BOE</a> · </li>-->
			<!--<li><a href="#" >Acceso Usuarios</a> · </li>-->
			<!--<li><a href="#" >Mapa web</a> · </li>-->
	<!--	</ul>
	</div>
	-->
	<!--fin menu-main-->
	<div class="clearfix"></div>
</div>
<!--fin wrapper-->
<div id="footer">
        <p>2016 &copy; ACTIVATIE</p>
</div>
<script type="text/javascript" >
	head.js( "js/jquery-1.7.2.min.js","js/plugins.js", "js/scripts.js" );
	head.js("js/jquery-ui-1.10.0.custom.min.js", function() {
					$( "#accordion" ).accordion();
	});
	head.js("js/shadowbox/shadowbox.js", function() {
		Shadowbox.init();
	});
</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-64073754-1', 'auto');
  ga('send', 'pageview');

</script>
</body>
</html><?
include("_conweb.php");
pg_close($link);
pg_close($link_c);
$_POST = array();
?>


		<!--=== Footer v2 ===-->
		<div id="footer-v2" class="footer-v2">
			<div class="footer">
				<div class="container">
					
				<? include ($templatepath."slider-colegios.php"); ?>
					<div class="row">

						<!-- Link List -->
						<div class="col-md-3 md-margin-bottom-40">
							<div class="headline"><h2 class="heading-sm">Enlaces</h2></div>
							<div class="margin-bottom-5">
							<? 
								echo file_get_contents ($baseUrl.$path.$templatepath."social-links.php?movil"); 
							?>
							</div>
							<ul class="list-unstyled link-list">
								<li><a href="#">Conoce activatie</a><i class="fa fa-angle-right"></i></li>
								<li><a href="#">Preguntas frecuentes</a><i class="fa fa-angle-right"></i></li>
								<li><a href="Contacto">Contacto</a><i class="fa fa-angle-right"></i></li>
								<li><a href="AvisoLegal">Aviso legal</a><i class="fa fa-angle-right"></i></li>
								<li><a href="PoliticaVenta">Política de venta</a><i class="fa fa-angle-right"></i></li>
							</ul>
						</div>
						<!-- End Link List -->

					<?	
					$tweets = getFooterTweets();
					$twig->display('tweets.php', array('tweets'=>$tweets));
					?>
					
					</div>
				</div>
			</div><!--/footer-->

			<div class="copyright">
				
				<div class="container">
					<p class="text-center">PLATAFORMA COLEGIAL ACTIVATIE S.L. <?=$year?> &copy; Todos los derechos reservados</p>
				</div>

				
			</div><!--/copyright-->
			
		</div>
		<!--=== End Footer v2 ===-->
		
	<? include ("login-register.html"); ?>	
		
	</div><!--/wrapper-->
	
	<!-- JS Global Compulsory -->
	<script type="text/javascript" src="assets/plugins/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="assets/plugins/jquery/jquery-migrate.min.js"></script>
	<script type="text/javascript" src="assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	<!-- JS Implementing Plugins -->
	<script type="text/javascript" src="assets/plugins/back-to-top.js"></script>
	<script type="text/javascript" src="assets/plugins/smoothScroll.js"></script>
	<script type="text/javascript" src="assets/plugins/owl-carousel/owl-carousel/owl.carousel.js"></script>
	<script type="text/javascript" src="assets/plugins/login-signup-modal-window/js/main.js"></script> <!-- Gem jQuery -->
	
	<script type="text/javascript" src="assets/plugins/sky-forms-pro/skyforms/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="assets/plugins/sky-forms-pro/skyforms/js/jquery.maskedinput.min.js"></script>
	<script type="text/javascript" src="assets/plugins/sky-forms-pro/skyforms/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="assets/plugins/sky-forms-pro/skyforms/js/jquery.form.min.js"></script>
	
	<script type="text/javascript" src="assets/plugins/revolution-slider/rs-plugin/js/jquery.themepunch.tools.min.js"></script>
	<script type="text/javascript" src="assets/plugins/revolution-slider/rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
	
	<script src="assets/plugins/master-slider/masterslider/masterslider.js"></script>
	<script src="assets/plugins/master-slider/masterslider/jquery.easing.min.js"></script>
	<script src="assets/js/plugins/master-slider-showcase2.js"></script>
	
	<!-- JS Customization -->
	<script type="text/javascript" src="assets/js/custom.js"></script>
	<!-- JS Page Level -->
	<script type="text/javascript" src="assets/js/app.js"></script>
	<script type="text/javascript" src="assets/js/plugins/owl-carousel.js"></script>
	<script type="text/javascript" src="assets/js/plugins/style-switcher.js"></script>
	<script type="text/javascript" src="assets/js/forms/reg.js"></script>
	<script type="text/javascript" src="assets/js/forms/checkout.js"></script>
	<script type="text/javascript" src="assets/js/plugins/revolution-slider.js"></script>
	<script type="text/javascript" src="assets/js/plugins/form-sliders.js"></script>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			App.init();
			OwlCarousel.initOwlCarousel();
			RevolutionSlider.initRSfullWidth();
			MasterSliderShowcase2.initMasterSliderShowcase2();
			FormSliders.initFormSliders();
		});
	</script>
	<!--[if lt IE 9]>
	<script src="assets/plugins/respond.js"></script>
	<script src="assets/plugins/html5shiv.js"></script>
	<script src="assets/plugins/placeholder-IE-fixes.js"></script>
	<![endif]-->
</body>
</html>

		<!--=== Footer v2 ===-->
		<div id="footer-v2" class="footer-v2">
			<div class="footer">
				<div class="container">
					
				<? include ($templatepath."slider-colegios.php"); ?>
					<div class="row">

						<!-- Link List -->
						<div class="col-md-4 md-margin-bottom-40">
							<div class="headline"><h2 class="heading-sm">Links</h2></div>
							<ul class="list-unstyled link-list">
								<li><a href="#">Sobre activatie</a><i class="fa fa-angle-right"></i></li>
								<li><a href="#">Preguntas frecuentes</a><i class="fa fa-angle-right"></i></li>
								<li><a href="#">Contacto</a><i class="fa fa-angle-right"></i></li>
								<li><a href="#">Aviso legal</a><i class="fa fa-angle-right"></i></li>
								<li><a href="#">Política de venta</a><i class="fa fa-angle-right"></i></li>
							</ul>
						</div>
						<!-- End Link List -->

						<!-- Latest Tweets -->
						<div class="col-md-4 md-margin-bottom-40">
							<div class="latest-tweets">
								<div class="headline"><h2 class="heading-sm">Latest Tweets</h2></div>
								<div class="latest-tweets-inner">
									<i class="fa fa-twitter"></i>
									<p>
										<a href="#">@htmlstream</a>
										At vero seos etodela ccusamus et
										<a href="#">http://t.co/sBav7dm</a>
										<small class="twitter-time">2 hours ago</small>
									</p>
								</div>
								<div class="latest-tweets-inner">
									<i class="fa fa-twitter"></i>
									<p>
										<a href="#">@htmlstream</a>
										At vero seos etodela ccusamus et
										<a href="#">http://t.co/sBav7dm</a>
										<small class="twitter-time">4 hours ago</small>
									</p>
								</div>
							</div>
						</div>
						<!-- End Latest Tweets -->

						<!-- Address -->
						<div class="col-md-4 md-margin-bottom-40">
							<div class="headline"><h2 class="heading-sm">Contact Us</h2></div>
							<address class="md-margin-bottom-40">
								<i class="fa fa-home"></i>25, Lorem Lis Street, California, US <br />
								<i class="fa fa-phone"></i>Phone: 800 123 3456 <br />
								<i class="fa fa-globe"></i>Website: <a href="#">www.htmlstream.com</a> <br />
								<i class="fa fa-envelope"></i>Email: <a href="mailto:info@anybiz.com">info@anybiz.com</a>
							</address>
							
							<? include $templatepath."social-links.php"; ?>

						</div>
						<!-- End Address -->
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
	<script type="text/javascript" src="assets/plugins/jquery.parallax.js"></script>
	<script type="text/javascript" src="assets/plugins/parallax-slider/js/modernizr.js"></script>
	<script type="text/javascript" src="assets/plugins/parallax-slider/js/jquery.cslider.js"></script>
	<script type="text/javascript" src="assets/plugins/owl-carousel/owl-carousel/owl.carousel.js"></script>
	<script type="text/javascript" src="assets/plugins/login-signup-modal-window/js/main.js"></script> <!-- Gem jQuery -->
	<script type="text/javascript" src="assets/plugins/sky-forms-pro/skyforms/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="assets/plugins/sky-forms-pro/skyforms/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="assets/plugins/sky-forms-pro/skyforms/js/jquery.maskedinput.min.js"></script>
	
	<!-- JS Customization -->
	<script type="text/javascript" src="assets/js/custom.js"></script>
	<!-- JS Page Level -->
	<script type="text/javascript" src="assets/js/app.js"></script>
	<script type="text/javascript" src="assets/js/plugins/owl-carousel.js"></script>
	<script type="text/javascript" src="assets/js/plugins/style-switcher.js"></script>
	<script type="text/javascript" src="assets/js/plugins/parallax-slider.js"></script>
	<script type="text/javascript" src="assets/js/forms/reg.js"></script>
	<script type="text/javascript" src="assets/js/forms/checkout.js"></script>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			App.init();
			OwlCarousel.initOwlCarousel();
			StyleSwitcher.initStyleSwitcher();
			ParallaxSlider.initParallaxSlider();
		});
	</script>
	<!--[if lt IE 9]>
	<script src="assets/plugins/respond.js"></script>
	<script src="assets/plugins/html5shiv.js"></script>
	<script src="assets/plugins/placeholder-IE-fixes.js"></script>
	<![endif]-->
</body>
</html>
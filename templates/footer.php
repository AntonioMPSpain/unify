
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
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/plugins/jquery/jquery.min.js"></script>
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/plugins/jquery/jquery-migrate.min.js"></script>
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>
	<!-- JS Implementing Plugins -->
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/plugins/back-to-top.js"></script>
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/plugins/smoothScroll.js"></script>
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/plugins/owl-carousel/owl-carousel/owl.carousel.js"></script>
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/plugins/login-signup-modal-window/js/main.js"></script> <!-- Gem jQuery -->
	
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/plugins/sky-forms-pro/skyforms/js/jquery.validate.min.js"></script>
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/plugins/sky-forms-pro/skyforms/js/jquery.maskedinput.min.js"></script>
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/plugins/sky-forms-pro/skyforms/js/jquery-ui.min.js"></script>
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/plugins/sky-forms-pro/skyforms/js/jquery.form.min.js"></script>
	
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/plugins/revolution-slider/rs-plugin/js/jquery.themepunch.tools.min.js"></script>
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/plugins/revolution-slider/rs-plugin/js/jquery.themepunch.revolution.min.js"></script>
	
	<script src="<?=$baseUrl.$path?>assets/plugins/master-slider/masterslider/masterslider.js"></script>
	<script src="<?=$baseUrl.$path?>assets/plugins/master-slider/masterslider/jquery.easing.min.js"></script>
	<script src="<?=$baseUrl.$path?>assets/js/plugins/master-slider-showcase2.js"></script>
	
	<script src="<?=$baseUrl.$path?>assets/plugins/skrollr/skrollr-ini.js"></script>
	<script src="<?=$baseUrl.$path?>assets/plugins/counter/waypoints.min.js"></script>
	<script src="<?=$baseUrl.$path?>assets/plugins/counter/jquery.counterup.min.js"></script>
	<script src="<?=$baseUrl.$path?>assets/plugins/cube-portfolio/cubeportfolio/js/jquery.cubeportfolio.min.js"></script>

	<!-- JS Customization -->
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/js/custom.js"></script>
	<!-- JS Page Level -->
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/js/app.js"></script>
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/js/plugins/owl-carousel.js"></script>
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/js/plugins/style-switcher.js"></script>
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/js/forms/reg.js"></script>
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/js/forms/checkout.js"></script>
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/js/plugins/revolution-slider.js"></script>
	<script type="text/javascript" src="<?=$baseUrl.$path?>assets/js/plugins/form-sliders.js"></script>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			App.init();
			OwlCarousel.initOwlCarousel();
			RevolutionSlider.initRSfullWidth();
			MasterSliderShowcase2.initMasterSliderShowcase2();
			FormSliders.initFormSliders();
		});
	</script>
	
	<!-- Package Nav -->
	  <script>
	  jQuery(window).scroll(function() {
	    if (jQuery(window).scrollTop() > 642) {
	    	jQuery('.package__nav').addClass('package__nav-is-fixed');
	    } else {
	    	jQuery('.package__nav').removeClass('package__nav-is-fixed');
	    }
	  });
	
	  $(function() {
	    $('a[href*=#pkg-]:not([href=#pkg-])').click(function() {
	      if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
			var target = $(this.hash);
	        target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
	        if (target.length) {
	          $('html,body').animate({
	            scrollTop: target.offset().top
	          }, 1500);
	          return false;
	        }
	      }
	    });
	  });
	  </script>
  <!-- Package Nav -->
  
	<!--[if lt IE 9]>
	<script src="assets/plugins/respond.js"></script>
	<script src="assets/plugins/html5shiv.js"></script>
	<script src="assets/plugins/placeholder-IE-fixes.js"></script>
	<![endif]-->
</body>
</html>
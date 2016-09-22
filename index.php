<?

include_once "_config.php";
include_once "_funciones.php";
include ($templatepath."header.php");


$salir=$_GET['salir']; 
if ($salir==true){
	header("Location: _control-user.php?logout");
	exit();
}
	
		include ($templatepath."slider.php");
?>

	
		<!--=== Slider ===-->	
<!--
		<div class="slider-inner">
			<div id="da-slider" class="da-slider">
				<div class="da-slide">
					<h2><i>CLEAN &amp; FRESH</i> <br /> <i>FULLY RESPONSIVE</i> <br /> <i>DESIGN</i></h2>
					<p><i>Lorem ipsum dolor amet</i> <br /> <i>tempor incididunt ut</i> <br /> <i>veniam omnis </i></p>
					<div class="da-img"><img class="img-responsive" src="assets/plugins/parallax-slider/img/1.png" alt=""></div>
				</div>
				<div class="da-slide">
					<h2><i>RESPONSIVE VIDEO</i> <br /> <i>SUPPORT AND</i> <br /> <i>MANY MORE</i></h2>
					<p><i>Lorem ipsum dolor amet</i> <br /> <i>tempor incididunt ut</i></p>
					<div class="da-img">
						<iframe src="https://player.vimeo.com/video/47911018" width="530" height="300" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
					</div>
				</div>
				<div class="da-slide">
					<h2><i>USING BEST WEB</i> <br /> <i>SOLUTIONS WITH</i> <br /> <i>HTML5/CSS3</i></h2>
					<p><i>Lorem ipsum dolor amet</i> <br /> <i>tempor incididunt ut</i> <br /> <i>veniam omnis </i></p>
					<div class="da-img"><img src="assets/plugins/parallax-slider/img/html5andcss3.png" alt="image01" /></div>
				</div>
				<div class="da-arrows">
					<span class="da-arrows-prev"></span>
					<span class="da-arrows-next"></span>
				</div>
			</div>
		</div>
-->
		<!--/slider-->
		<!--=== End Slider ===-->
		
		<!--=== Content ===-->
		<div class="container content-md">

			
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis ornare magna neque, quis sodales ex luctus at. In feugiat finibus sem. Ut nisi est, cursus vitae risus non, sagittis auctor nisl. Aenean sit amet luctus ante. Phasellus pellentesque tempor libero nec rhoncus. Nam sed diam eget ante rhoncus volutpat vitae ac dui. Vestibulum massa velit, pretium pellentesque vestibulum et, pharetra at magna. Praesent sit amet arcu malesuada, malesuada dui et, fringilla neque. Integer sapien elit, gravida in malesuada consectetur, sagittis ac erat. Praesent vel vestibulum eros, ac interdum diam. Cras vitae massa neque. Duis dapibus auctor dui, eu pulvinar odio efficitur non. Sed quis nibh in risus gravida hendrerit.</p>

			<p>In volutpat tristique rutrum. Vestibulum sollicitudin at nulla sit amet pulvinar. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam dictum at diam eu commodo. Mauris vehicula sapien lectus, ac molestie mauris commodo sit amet. Aliquam non est leo. Mauris sagittis sagittis urna, in lacinia lectus efficitur in. Proin sollicitudin mattis sodales. Cras et faucibus neque, non ornare metus. Vestibulum id semper est. Maecenas vestibulum varius sapien, id elementum tellus euismod at. Vestibulum auctor ante a efficitur sagittis. Proin tempus lacus risus, quis dapibus nunc eleifend sit amet.</p>

			<p>Aliquam quis varius nisl. Praesent egestas magna efficitur enim posuere rutrum. Vivamus nulla velit, tempus id tempus nec, lacinia et leo. Nullam fringilla commodo facilisis. Maecenas mi eros, feugiat eget arcu sit amet, posuere efficitur ex. Nulla vel felis facilisis turpis porta euismod. Aenean vel enim nec nisl varius feugiat vitae nec urna. Duis feugiat molestie ex, non tincidunt tortor finibus in. Pellentesque quis ultricies augue. Nulla et lacus semper quam ornare cursus sit amet a sem. Vivamus semper lectus dui, faucibus dignissim enim feugiat vitae. Morbi fermentum odio neque, a molestie nunc volutpat ac. In ut aliquam est. Pellentesque est elit, pulvinar a magna sit amet, vestibulum fermentum neque. Aliquam vel lorem ut tortor tristique pretium eget vitae dolor.</p>

			<p>Vestibulum dictum nec libero ut euismod. Fusce posuere sit amet libero vitae ornare. Curabitur posuere semper ipsum in efficitur. Etiam pellentesque felis sodales, blandit eros vel, dignissim turpis. Phasellus ullamcorper rhoncus enim, a consequat libero sagittis at. Mauris in pharetra elit. Suspendisse in urna dictum, tincidunt odio eget, vehicula turpis. Sed eget quam consequat, aliquam magna non, aliquam ipsum.</p>
			
		</div>
		<!--=== End Content ===-->



<?


include ($templatepath."footer.php");

?>
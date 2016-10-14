
    <!--=== Package Description ===-->
    <div style="position: relative;">
      <!-- Package Navigation -->
      <header class="package__nav">
        <nav class="text-center">
          <ul class="list-inline no-margin">
            <li><a class="package__nav-link" href="#pkg-formacion">Formación</a></li>
            <li><a class="package__nav-link" href="#pkg-materiales">Materiales</a></li>
            <li><a class="package__nav-link" href="#pkg-ahora">Ahora</a></li>
            <li><a class="package__nav-link" href="#pkg-profesionales">Arquitectos Técnicos</a></li>
            <li><a class="package__nav-link" href="#pkg-publicaciones">Publicaciones</a></li>
            <li><a class="package__nav-link" href="#pkg-trabajo">Trabajo</a></li>
            <li><a class="package__nav-link" href="#pkg-apps">APPs</a></li>
          </ul>
        </nav>
      </header>
      <!-- End Package Navigation -->

      <!-- #1 Formación -->
      <div id="pkg-formacion" class="content-md" style="padding-right: 30px; padding-left: 30px; padding-top: 120px">
        <div class="container text-center margin-bottom-50">
          <h2 class="margin-bottom-20" style="font-weight: bold; letter-spacing: 2px;"><span class="color-green">#1</span> FORMACIÓN</h2>
          <p style="font-size: 15px; letter-spacing: 1px;">Descrubre las magnificas <strong>actividades y jornadas gratuitas</strong>. <br> Enfocados para mejorar en la profesión de arquitecto técnico.</p>
        </div>
        <!-- Section Row -->
        <div class="row margin-bottom-40">
        	
	        <div class="headline">
	        	<h3>Próximas actividades</h3>
	        </div>
			
			<div class="row news-v1">
				{% for curso in cursos %}
						<div class="col-md-3 md-margin-bottom-40">
							<div class="news-v1-in bg-grey">
								<img class="img-responsive " src="{{ curso.imagen }}" alt="{{ curso.nombre }}">
								<h3 class="font-normal"><a href="{{ curso.link }}"> {{ curso.nombre }} </a></h3>
								<ul class=" news-v1-info no-margin-bottom">
									{% if curso.privado==1 %}
										<li><i class="fa fa-lock"></i> Privado: Solo <strong>colegiados activatie</strong></li>
									{% endif %}
									<li><i class="fa fa-info-circle"></i> Modalidad: <strong>{{ curso.modalidadtexto }}</strong></li>
									<li><i class="fa fa-clock-o"></i> Inicio: <strong>{{ curso.fecha_inicio }}</strong></li>
									{% if curso.modalidad==3 %} 
										<li><i class="fa fa-clock-o"></i> Plazo de realización: <strong>{{ curso.realizacion }} días</strong></li> 
									{% else %}
										{% if (curso.realizacion < 30) and (curso.realizacion >= 0) %}
											<li>
												<i class="fa fa-times-circle-o"></i> Fin inscripción: 
												{% if curso.realizacion==0 %}
													<strong>¡Hoy!</strong>
												{% elseif curso.realizacion==1 %}
													<strong>{{ curso.realizacion }}</strong> día restante
												{% else %}
													<strong>{{ curso.realizacion }}</strong> días restantes
												{% endif %}
											</li> 
										{% endif %}
									{% endif %}
								</ul>
							</div>
						</div>
				{% endfor %}
			</div>	
				
        </div>
        <!-- End Section Row -->
        
        
        <div class="row margin-bottom-40">
			{% for curso in cursos %}
				<div class="col-md-3 col-sm-6 md-margin-bottom-40">
					<a class="intro-page__link" href="{{ curso.link }}">
						<img class="img-responsive intro-page__img" src="{{ curso.imagen }}" alt="{{ curso.nombre }}">
						{{ curso.nombre }}
					</a>
				</div>
			{% endfor %}
        </div>
        
        
      </div>
      <!-- End #1 Formación -->

      <hr class="no-margin">

      <!-- #2 Materiales -->
      <div id="pkg-materiales" class="content-md" style="padding-right: 30px; padding-left: 30px;">
        <div class="container text-center margin-bottom-50">
          <h2 class="margin-bottom-20" style="font-weight: bold; letter-spacing: 2px;"><span class="color-green">#2</span> MATERIALES</h2>
          <p style="font-size: 15px; letter-spacing: 1px;">Package contains classic pages based on Unify Template and <strong>over 15 thematic designs</strong> such as Travel, Business, <br> Wedding, Courses, Lawyer, Agency, Architecture, Shipping, Spa, Mobile App and others.</p>
        </div>

        <!-- Section Row -->
        <div class="row">
        	
			{% for material in materiales %}
        	<div class="col-md-3 col-sm-6 sm-margin-bottom-40">
          		<a class="intro-page__link" target="_blank" href="One-Pages/RealEstate/index.html">
            		<img class="img-responsive intro-page__img" src="{{ material.imagen }}" alt="">
            		{{ material.nombre }} {% if material.nuevo==1 %} <span class="label label-red margin-left-5">New</span> {% endif %}
          		</a>
        	</div>
        	{% endfor %}
        </div>
        <!-- End Section Row -->

      </div>
      <!-- End #2 Materiales -->

      <hr class="no-margin">

      <!-- #3 Ahora -->
      <div id="pkg-ahora" class="content-md" style="padding-right: 30px; padding-left: 30px;">
        <div class="container text-center margin-bottom-50">
          <h2 class="margin-bottom-20" style="font-weight: bold; letter-spacing: 2px;"><span class="color-green">#3</span> AHORA</h2>
          <p style="font-size: 15px; letter-spacing: 1px;">The package includes <strong>more than 25 template pages</strong> with tons of design and layout options. <br> In addition, It includes boxed layout with 12 predefined theme color options.</p>
        </div>

        
		<!-- News v3 Light -->
			<div class="row margin-bottom-40">
		        <div class="headline">
		        	<h3>Noticias</h3>
		        </div>
				<div class="col-md-3 col-sm-6 sm-margin-bottom-40 news-v3">
					<img class="img-responsive intro-page__img" src="assets/img/main/img15.jpg" alt="">
					<div class="news-v3-in-sm bg-color-white">
						<ul class="list-inline posted-info-sm">
							<li>By: <a href="#">Alex</a></li>
							<li>In: <a href="#">Design</a></li>
							<li>Posted: Jan 24, 2015</li>
						</ul>
						<h2><a href="#">Incredible standard post</a></h2>
						<p>Nullam elementum tincidunt massa, a pulvinar leo ultricies ut. Ut fringilla lectus tellus, imperdiet molestie est volutpat at. Sed viverra cursus nibh, sed consectetur ipsum sollicitudin sed.</p>
						<ul class="post-shares">
							<li>
								<a href="#">
									<i class="rounded-x icon-heart"></i>
									<span>30</span>
								</a>
							</li>
							
							<li>
								<a href="#">
									<i class="rounded-x icon-speech"></i>
									<span>26</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 sm-margin-bottom-40 news-v3">
					<img class="img-responsive intro-page__img" src="assets/img/main/img15.jpg" alt="">
					<div class="news-v3-in-sm bg-color-white">
						<ul class="list-inline posted-info-sm">
							<li>By: <a href="#">Alex</a></li>
							<li>In: <a href="#">Design</a></li>
							<li>Posted: Jan 24, 2015</li>
						</ul>
						<h2><a href="#">Incredible standard post</a></h2>
						<p>Nullam elementum tincidunt massa, a pulvinar leo ultricies ut. Ut fringilla lectus tellus, imperdiet molestie est volutpat at. Sed viverra cursus nibh, sed consectetur ipsum sollicitudin sed.</p>
						<ul class="post-shares">
							<li>
								<a href="#">
									<i class="rounded-x icon-heart"></i>
									<span>30</span>
								</a>
							</li>
							
							<li>
								<a href="#">
									<i class="rounded-x icon-speech"></i>
									<span>26</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 news-v3">
					<img class="img-responsive intro-page__img" src="assets/img/main/img16.jpg" alt="">
					<div class="news-v3-in-sm bg-color-white">
						<ul class="list-inline posted-info-sm">
							<li>By: <a href="#">Susan</a></li>
							<li>In: <a href="#">Wordpress</a></li>
							<li>Posted: Jan 24, 2015</li>
						</ul>
						<h2><a href="#">Imperdiet molesti volutpa</a></h2>
						<p>Nullam elementum tincidunt massa, a pulvinar leo ultricies ut. Ut fringilla lectus tellus, imperdiet molestie est volutpat at. Sed viverra cursus nibh, sed consectetur ipsum sollicitudin sed.</p>
						<ul class="post-shares">
							<li>
								<a href="#">
									<i class="rounded-x icon-heart"></i>
									<span>30</span>
								</a>
							</li>
							
							<li>
								<a href="#">
									<i class="rounded-x icon-speech"></i>
									<span>26</span>
								</a>
							</li>
						</ul>
					</div>
				</div>

				<div class="col-md-3 col-sm-6 news-v3">
					<img class="img-responsive intro-page__img" src="assets/img/main/img13.jpg" alt="">
					<div class="news-v3-in-sm bg-color-white">
						<ul class="list-inline posted-info-sm">
							<li>By: <a href="#">Joe</a></li>
							<li>In: <a href="#">Unify</a></li>
							<li>Posted: Jan 24, 2015</li>
						</ul>
						<h2><a href="#">Nullam non metus inmi</a></h2>
						<p>Nullam elementum tincidunt massa, a pulvinar leo ultricies ut. Ut fringilla lectus tellus, imperdiet molestie est volutpat at. Sed viverra cursus nibh, sed consectetur ipsum sollicitudin sed.</p>
						<ul class="post-shares">
							<li>
								<a href="#">
									<i class="rounded-x icon-heart"></i>
									<span>30</span>
								</a>
							</li>
							
							<li>
								<a href="#">
									<i class="rounded-x icon-speech"></i>
									<span>26</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		<!-- End News v3 Light -->

    

		<!-- News v3 Light -->
			<div class="row margin-bottom-40">
		        <div class="headline">
		        	<h3>Consultas técnicas</h3>
		        </div>
				<div class="col-md-3 col-sm-6 sm-margin-bottom-40 news-v3">
					<div class="news-v3-in-sm bg-color-white">
						<ul class="list-inline posted-info-sm">
							<li>By: <a href="#">Alex</a></li>
							<li>In: <a href="#">Design</a></li>
							<li>Posted: Jan 24, 2015</li>
						</ul>
						<h2><a href="#">Incredible standard post</a></h2>
						<p>Nullam elementum tincidunt massa, a pulvinar leo ultricies ut. Ut fringilla lectus tellus, imperdiet molestie est volutpat at. Sed viverra cursus nibh, sed consectetur ipsum sollicitudin sed.</p>
						<ul class="post-shares">
							<li>
								<a href="#">
									<i class="rounded-x icon-heart"></i>
									<span>30</span>
								</a>
							</li>
							
							<li>
								<a href="#">
									<i class="rounded-x icon-speech"></i>
									<span>26</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 sm-margin-bottom-40 news-v3">
					<div class="news-v3-in-sm bg-color-white">
						<ul class="list-inline posted-info-sm">
							<li>By: <a href="#">Alex</a></li>
							<li>In: <a href="#">Design</a></li>
							<li>Posted: Jan 24, 2015</li>
						</ul>
						<h2><a href="#">Incredible standard post</a></h2>
						<p>Nullam elementum tincidunt massa, a pulvinar leo ultricies ut. Ut fringilla lectus tellus, imperdiet molestie est volutpat at. Sed viverra cursus nibh, sed consectetur ipsum sollicitudin sed.</p>
						<ul class="post-shares">
							<li>
								<a href="#">
									<i class="rounded-x icon-heart"></i>
									<span>30</span>
								</a>
							</li>
							
							<li>
								<a href="#">
									<i class="rounded-x icon-speech"></i>
									<span>26</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 news-v3">
					<div class="news-v3-in-sm bg-color-white">
						<ul class="list-inline posted-info-sm">
							<li>By: <a href="#">Susan</a></li>
							<li>In: <a href="#">Wordpress</a></li>
							<li>Posted: Jan 24, 2015</li>
						</ul>
						<h2><a href="#">Imperdiet molesti volutpa</a></h2>
						<p>Nullam elementum tincidunt massa, a pulvinar leo ultricies ut. Ut fringilla lectus tellus, imperdiet molestie est volutpat at. Sed viverra cursus nibh, sed consectetur ipsum sollicitudin sed.</p>
						<ul class="post-shares">
							<li>
								<a href="#">
									<i class="rounded-x icon-heart"></i>
									<span>30</span>
								</a>
							</li>
							
							<li>
								<a href="#">
									<i class="rounded-x icon-speech"></i>
									<span>26</span>
								</a>
							</li>
						</ul>
					</div>
				</div>

				<div class="col-md-3 col-sm-6 news-v3">
					<div class="news-v3-in-sm bg-color-white">
						<ul class="list-inline posted-info-sm">
							<li>By: <a href="#">Joe</a></li>
							<li>In: <a href="#">Unify</a></li>
							<li>Posted: Jan 24, 2015</li>
						</ul>
						<h2><a href="#">Nullam non metus inmi</a></h2>
						<p>Nullam elementum tincidunt massa, a pulvinar leo ultricies ut. Ut fringilla lectus tellus, imperdiet molestie est volutpat at. Sed viverra cursus nibh, sed consectetur ipsum sollicitudin sed.</p>
						<ul class="post-shares">
							<li>
								<a href="#">
									<i class="rounded-x icon-heart"></i>
									<span>30</span>
								</a>
							</li>
							
							<li>
								<a href="#">
									<i class="rounded-x icon-speech"></i>
									<span>26</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		<!-- End News v3 Light -->
       
		<!-- News v3 Light -->
			<div class="row margin-bottom-40">
		        <div class="headline">
		        	<h3>Foros de debate</h3>
		        </div>
				<div class="col-md-3 col-sm-6 sm-margin-bottom-40 news-v3">
					<div class="news-v3-in-sm bg-color-white">
						<ul class="list-inline posted-info-sm">
							<li>By: <a href="#">Alex</a></li>
							<li>In: <a href="#">Design</a></li>
							<li>Posted: Jan 24, 2015</li>
						</ul>
						<h2><a href="#">Incredible standard post</a></h2>
						<p>Nullam elementum tincidunt massa, a pulvinar leo ultricies ut. Ut fringilla lectus tellus, imperdiet molestie est volutpat at. Sed viverra cursus nibh, sed consectetur ipsum sollicitudin sed.</p>
						<ul class="post-shares">
							<li>
								<a href="#">
									<i class="rounded-x icon-heart"></i>
									<span>30</span>
								</a>
							</li>
							
							<li>
								<a href="#">
									<i class="rounded-x icon-speech"></i>
									<span>26</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 sm-margin-bottom-40 news-v3">
					<div class="news-v3-in-sm bg-color-white">
						<ul class="list-inline posted-info-sm">
							<li>By: <a href="#">Alex</a></li>
							<li>In: <a href="#">Design</a></li>
							<li>Posted: Jan 24, 2015</li>
						</ul>
						<h2><a href="#">Incredible standard post</a></h2>
						<p>Nullam elementum tincidunt massa, a pulvinar leo ultricies ut. Ut fringilla lectus tellus, imperdiet molestie est volutpat at. Sed viverra cursus nibh, sed consectetur ipsum sollicitudin sed.</p>
						<ul class="post-shares">
							<li>
								<a href="#">
									<i class="rounded-x icon-heart"></i>
									<span>30</span>
								</a>
							</li>
							
							<li>
								<a href="#">
									<i class="rounded-x icon-speech"></i>
									<span>26</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-md-3 col-sm-6 news-v3">
					<div class="news-v3-in-sm bg-color-white">
						<ul class="list-inline posted-info-sm">
							<li>By: <a href="#">Susan</a></li>
							<li>In: <a href="#">Wordpress</a></li>
							<li>Posted: Jan 24, 2015</li>
						</ul>
						<h2><a href="#">Imperdiet molesti volutpa</a></h2>
						<p>Nullam elementum tincidunt massa, a pulvinar leo ultricies ut. Ut fringilla lectus tellus, imperdiet molestie est volutpat at. Sed viverra cursus nibh, sed consectetur ipsum sollicitudin sed.</p>
						<ul class="post-shares">
							<li>
								<a href="#">
									<i class="rounded-x icon-heart"></i>
									<span>30</span>
								</a>
							</li>
							
							<li>
								<a href="#">
									<i class="rounded-x icon-speech"></i>
									<span>26</span>
								</a>
							</li>
						</ul>
					</div>
				</div>

				<div class="col-md-3 col-sm-6 news-v3">
					<div class="news-v3-in-sm bg-color-white">
						<ul class="list-inline posted-info-sm">
							<li>By: <a href="#">Joe</a></li>
							<li>In: <a href="#">Unify</a></li>
							<li>Posted: Jan 24, 2015</li>
						</ul>
						<h2><a href="#">Nullam non metus inmi</a></h2>
						<p>Nullam elementum tincidunt massa, a pulvinar leo ultricies ut. Ut fringilla lectus tellus, imperdiet molestie est volutpat at. Sed viverra cursus nibh, sed consectetur ipsum sollicitudin sed.</p>
						<ul class="post-shares">
							<li>
								<a href="#">
									<i class="rounded-x icon-heart"></i>
									<span>30</span>
								</a>
							</li>
							
							<li>
								<a href="#">
									<i class="rounded-x icon-speech"></i>
									<span>26</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		<!-- End News v3 Light -->

      </div>
      <!-- End #3 Ahora -->

      <hr class="no-margin">

      <!-- #4 Arquitectos Técnicos -->
      <div id="pkg-profesionales" class="content-md" style="padding-right: 30px; padding-left: 30px;">
        <div class="container text-center margin-bottom-50">
          <h2 class="margin-bottom-20" style="font-weight: bold; letter-spacing: 2px;"><span class="color-green">#4</span> ARQUITECTOS TÉCNICOS</h2>
          <p style="font-size: 15px; letter-spacing: 1px;">The newest package in Unify Template, currently includes two demos <strong>Wealth</strong> and <strong>Consulting</strong> <br> more demoes will be added soon in coming future updates.</p>
        </div>
			<!--=== Team v6 ===-->
			<div>
				<div class="row team-v6">
					<div class="col-md-2 col-sm-6 md-margin-bottom-50">
						<img class="img-responsive intro-page__img" src="assets/img/team/img23-md.jpg" alt="">
						<span>Marketing</span>
						<small>Graphic Designer</small>
					</div>
					<div class="col-md-2 col-sm-6 md-margin-bottom-50">
						<img class="img-responsive intro-page__img" src="assets/img/team/img31-md.jpg" alt="">
						<span>Sara Lisbon</span>
						<small>Community</small>
					</div>
					<div class="col-md-2 col-sm-6 sm-margin-bottom-50">
						<img class="img-responsive intro-page__img" src="assets/img/team/img29-md.jpg" alt="">
						<span>John Doe</span>
						<small>Support</small>
					</div>
					<div class="col-md-2 col-sm-6">
						<img class="img-responsive intro-page__img" src="assets/img/team/img24-md.jpg" alt="">
						<span>Alice Williams</span>
						<small>Marketing</small>
					</div>
					<div class="col-md-2 col-sm-6">
						<img class="img-responsive intro-page__img" src="assets/img/team/img24-md.jpg" alt="">
						<span>Alice Williams</span>
						<small>Marketing</small>
					</div>
					<div class="col-md-2 col-sm-6">
						<img class="img-responsive intro-page__img" src="assets/img/team/img24-md.jpg" alt="">
						<span>Alice Williams</span>
						<small>Marketing</small>
					</div>
					
				</div><!--/end team v6-->
			</div>
			<!--=== End Team v6 ===-->
        
      </div>
      <!-- End #4 Arquitectos Técnicos -->

      <hr class="no-margin">

      <!-- #5 Publicaciones -->
      <div id="pkg-publicaciones" class="content-md" style="padding-right: 30px; padding-left: 30px;">
        <div class="container text-center margin-bottom-50">
          <h2 class="margin-bottom-20" style="font-weight: bold; letter-spacing: 2px;"><span class="color-green">#5</span> PUBLICACIONES</h2>
          <p style="font-size: 15px; letter-spacing: 1px;">The Package contains more than 6 ready to use shop pages, including but not limited to <br> <strong>login</strong>, <strong>registration</strong>, <strong>product list/grid</strong> views and also comes with 12 predefined theme color options.</p>
        </div>

        
        <div class="row margin-bottom-40">
			{% for publicacion in publicaciones %}
				<div class="col-md-3 col-sm-6 md-margin-bottom-40">
					<a class="intro-page__link" href="{{ publicacion.link }}">
						<img class="img-responsive intro-page__img" src="{{ publicacion.imagen }}" alt="{{ publicacion.nombre }}">
						{{ publicacion.nombre }}
					</a>
				</div>
			{% endfor %}
        </div>
        <!-- End Section Row -->

      </div>
      <!-- End #5 Publicaciones -->

      <hr class="no-margin">

      <!-- #6 Trabajo -->
      <div id="pkg-trabajo" class="content-md" style="padding-right: 30px; padding-left: 30px;">
        <div class="container text-center margin-bottom-50">
          <h2 class="margin-bottom-20" style="font-weight: bold; letter-spacing: 2px;"><span class="color-green">#6</span> TRABAJO</h2>
          <p style="font-size: 15px; letter-spacing: 1px;">Si eres colegiado de activatie encuentra trabajo en nuestra sección de <strong>ofertas</strong>.</p>
        </div>

        <!-- Section Row -->
        <div class="row margin-bottom-40">
          <div class="row news-v1">
          	
				{% for trabajo in trabajos %}
					<div class="bg-grey col-md-3 md-margin-bottom-40">
						<div class="bg-grey news-v1-in">
							<h3 class="font-normal"><a href="#">{{ trabajo.titulo }}</a></h3>
							<p> {{ trabajo.descripcion }}</p>
							<ul class="news-v1-info no-margin-bottom">
								<li><i class="fa fa-map-marker"></i> Zona: <strong>{{ trabajo.zona }}</strong></li>
								<li><i class="fa fa-clock-o"></i> Fecha fin: <strong>{{ trabajo.fecha }}</strong></li>
								<li><i class="fa fa-lock"></i> Contacto: <strong>solo colegiados activatie</strong></li>
							</ul>
						</div>
					</div>
				{% endfor %}	
					
			</div>
        </div>
        <!-- End Section Row -->
      </div>
      <!-- End #6 Trabajo -->
      
      <!-- #6 APPs -->
      <div id="pkg-apps" class="content-md" style="padding-right: 30px; padding-left: 30px;">
        <div class="container text-center margin-bottom-50">
          <h2 class="margin-bottom-20" style="font-weight: bold; letter-spacing: 2px;"><span class="color-green">#7</span> APPs</h2>
          <p style="font-size: 15px; letter-spacing: 1px;">Comes with three design options <strong>Corporate</strong>, <strong>Flat</strong>, <strong>Modern</strong> and 10 predefined layout colors. <br> Will be added more demo options in future updates.</p>
        </div>

        <!-- Section Row -->
        <div class="row margin-bottom-40">
          <div class="col-md-3 col-sm-6 md-margin-bottom-40">
            <a class="intro-page__link" target="_blank" href="Email-Templates/modern/email_modern_red.html">
              <img class="img-responsive intro-page__img" src="assets/img/intro/email/email-modern.jpg" alt="">
                Modern
            </a>
          </div>
        </div>
        <!-- End Section Row -->
      </div>
      <!-- End #6 APPs -->
      </div>
    <!--=== End Package Description ===-->
<!-- Master Slider -->
<div class="blog-ms-v1 blog-ms-v1-extend bg-color-darker">
	<div class="master-slider ms-skin-default" id="masterslider">
		
		{% for curso in cursos %}
			<div class="ms-slide blog-slider">
				<a href=" {{ curso.link}} " ></a><img src="assets/plugins/master-slider/masterslider/style/blank.gif" data-src="{{ curso.imagen }}" alt=" {{ curso.nombre }} "/></a>
				<span style="opacity:0.8; background-color: {{ curso.color }} ;" class="blog-slider-badge">{{ curso.area }}</span>
				<div class="ms-info"></div>
				<div style="opacity:0.8; background-color: {{ curso.color }} ;" class="blog-slider-title">
					
					<span class="blog-slider-posted">Inicio: <strong>{{ curso.fecha_inicio }}</strong></span>
					<h2>
						{% if curso.privado==1 %}
							<i class="fa fa-lock" title="Privado: Solo <strong>colegiados activatie"></i>
						{% endif %}
						<a href=" {{ curso.link }} "> {{ curso.nombre }} </a>
						
						
					</h2>
				</div>
			</div>
		{% endfor %}

	</div>
</div>
<!-- End Master Slider -->
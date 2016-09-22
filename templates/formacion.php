{% include 'breadcrumbs.php' %}

<!--=== Profile ===-->
<div class="container content profile">
	<div class="row">
		<!-- Profile Content -->
		<div class="col-md-12"> 
			<div class="margin-bottom-20 col-md-offset-2">{{ banner1|raw }}</div>
			<div class="profile-body">
				
					{% for curso in cursos %}
						{% if (loop.index % 4) == 1 %} 
							<div class="row">
						{% endif %}
					
					    {% block curso %}
						    <div class="col-sm-3">
								<div class="easy-block-v1">
									<a href="{{ curso.link }}"><img class="img-responsive" src="{{ curso.imagen }}" alt=" {{ curso.nombre }} "></a>
									<div class="mascara-video"></div>
									{% if curso.area!="" %} <a href=" {{ curso.linkarea }}"><div style="background-color: {{ curso.color }} ;" class="easy-block-v1-badge rgba-red"> {{ curso.area }} </div></a> {% endif %}
								</div>
								<div class="projects">
									
									<h2><a class="color-dark" href=" {{ curso.link }}"> {{ curso.nombre }} </a></h2>
									<ul class="list-unstyled ">
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
					    {% endblock %}
					    
					    {% if (loop.last) %} 
					    
										</div><!--/end row-->
				
										</div>
										<div class="margin-top-20 col-md-offset-2">{{ banner2|raw }}</div>
									</div>
									<!-- End Profile Content -->
								</div>
							</div>
							<!--=== End Profile ===-->

					    {% else %}
					    
					    
						    {% if ((loop.index % 4) == 0) %} 
							
								
								</div>
								<!--/end row-->
								<hr>
								
								<!--
						   		{% if ((loop.index % 8) == 0) %} 
									<div class="margin-top-20 margin-bottom-20 col-md-offset-2">{{ banner2|raw }}</div>
								{% else %}
									<hr>
								{% endif %}
								-->
							
							{% endif %}
						{% endif %}
					{% endfor %}

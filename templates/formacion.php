<!--=== Profile ===-->
<div class="container content profile">
	<div class="row">
		
		<!-- Profile Content -->
		<div class="col-md-12"> 
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
										<li><i class="fa fa-info-circle"></i> Modalidad: <strong>{{ curso.modalidad }}</strong></li>
										<li><i class="fa fa-clock-o"></i> Inicio: <strong>{{ curso.fecha_inicio }}</strong></li>
										{% if curso.modalidad==3 %} 
											<li><i class="fa fa-clock-o"></i> Plazo de realización: <strong>{{ curso.realizacion }} días</strong></li> 
										{% else %}
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
									</ul>

									
									
								</div>
							</div>
					    {% endblock %}
					    
					    {% if (loop.last) %} 
					    
										</div><!--/end row-->
				
										</div>
									</div>
									<!-- End Profile Content -->
								</div>
							</div>
							<!--=== End Profile ===-->

					    {% else %}
					    
					    
						    {% if ((loop.index % 4) == 0) %} 
							
								</div><!--/end row-->
								<hr>
								    
							{% endif %}
						{% endif %}
					{% endfor %}

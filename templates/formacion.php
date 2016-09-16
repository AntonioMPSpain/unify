<!--=== Profile ===-->
<div class="container content profile">
	<div class="row">
		
		<!-- Profile Content -->
		<div class="col-md-12"> 
			<div class="profile-body">
					
					
					{% for curso in cursos %}
					
						{% if (loop.index % 3) == 1 %} 
						
							<div class="row">
							    
						{% endif %}
					
					    {% block curso %}
						    <div class="col-sm-4">
								<div class="easy-block-v1">
									<img class="img-responsive" src="{{ curso.imagen }}" alt=" {{ curso.nombre }} ">
									<div class="mascara-video"></div>
									{% if curso.area!="" %} <div style="background-color: {{ curso.color }} ;" class="easy-block-v1-badge rgba-red"> {{ curso.area }} </div> {% endif %}
								</div>
								<div class="projects">
									<h2><a class="color-dark" href="#"> {{ curso.nombre }} </a></h2>
									<ul class="list-unstyled list-inline blog-info-v2">
										<li><a class="color-green" href="#"> {{ curso.modalidad }} </a></li>
									</ul>
									
									<ul class="list-unstyled list-inline blog-info-v2">
										<li><i class="fa fa-clock-o"></i> Inicio: {{ curso.fecha_inicio }} </li>
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
					    
					    
						    {% if ((loop.index % 3) == 0) %} 
							
								</div><!--/end row-->
								<hr>
								    
							{% endif %}
						{% endif %}
					{% endfor %}

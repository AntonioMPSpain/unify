{% include 'breadcrumbs.php' %}

<!--=== Content Part ===-->
<div class="content container">
	<div class="row">
		<div class="col-md-12">
			<div class="margin-bottom-40 col-md-offset-2">
				{{ banner1|raw }}
			</div>
			<div class="row margin-bottom-5">
				<div class="col-sm-4 result-category">
					<h2>REVESTECH</h2>
					<small class="shop-bg-red badge-results">7 Resultados</small>
				</div>
				<div class="col-sm-8">
					<ul class="list-inline clear-both">
						<li class="grid-list-icons">
							<a href="shop-ui-filter-list.html"><i class="fa fa-th-list"></i></a>
							<a href="shop-ui-filter-grid.html"><i class="fa fa-th"></i></a>
						</li>
						<li class="sort-list-btn">
							<h3>Ordenador por :</h3>
							<div class="btn-group">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									Popularidad <span class="caret"></span>
								</button>
								<ul class="dropdown-menu" role="menu">
									<li>
										<a href="#">Todo</a>
									</li>
									<li>
										<a href="#">Mejores ventas</a>
									</li>
									<li>
										<a href="#">Nuevas</a>
									</li>
								</ul>
							</div>
						</li>
						<li class="sort-list-btn">
							<h3>Mostrar :</h3>
							<div class="btn-group">
								<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
									20 <span class="caret"></span>
								</button>
								<ul class="dropdown-menu" role="menu">
									<li>
										<a href="#">Todo</a>
									</li>
									<li>
										<a href="#">10</a>
									</li>
									<li>
										<a href="#">5</a>
									</li>
									<li>
										<a href="#">3</a>
									</li>
								</ul>
							</div>
						</li>
					</ul>
				</div>
			</div><!--/end result category-->

			<div class="filter-results">

				{% for material in materiales %}

				{% if (loop.index % 4) == 1 %}
				<div class="row illustration-v2 margin-bottom-30">
					{% endif %}

					{% block material %}
					<div class="col-md-3">
						<div class="product-img product-img-brd">
							<a href="#"><img class="full-width img-responsive" src="{{ material.imagen }}" alt=""></a>
							{% if material.nuevo==1 %}
							<div class="shop-rgba-dark-green rgba-banner">
								Nuevo
							</div>
							{% endif %}
						</div>
						<div class="product-description product-description-brd margin-bottom-30">
							<div class="overflow-h margin-bottom-5">
								<div class="pull-left">
									<h4 class="title-price"><a href="shop-ui-inner.html"> {{ material.nombre }}</a></h4>
									<span class="gender text-uppercase"> {{ material.marca }} </span>
									<span class="gender"> {{ material.categoria }} </span>
								</div>
								<div class="product-price">
									{% if material.precio!="0" %}
									<span class="title-price"> {{ material.precio }} €</span>
									{% endif %}
									{% if material.preciotachado!="0" %}
									<span class="title-price line-through"> {{ material.preciotachado }} €</span>
									{% endif %}
								</div>
							</div>
							<ul class="list-inline product-ratings">
								
								{% for i in 1..5 %}
								    <li>
										<i class="rating{% if i<=material.estrellas %}-selected{% endif %} fa fa-star "></i>
									</li>
								{% endfor %}
								
							</ul>
						</div>
					</div>
					{% endblock %}

					{% if (loop.last) %}

					{% else %}

					{% if ((loop.index % 4) == 0) %}

				</div><!--/end filter resilts-->

				{% endif %}
				{% endif %}
				{% endfor %}
			</div>
		</div>
	</div>
</div><!--/end row-->
</div><!--/end container--


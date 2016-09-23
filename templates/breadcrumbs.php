{% if breadcrumbs.p1.titulo != "" %}

	<!--=== Breadcrumbs ===-->
	<div class="breadcrumbs">
		<div class="container">
			<h1 class="pull-left">
				{% if breadcrumbs.p4.titulo != "" %}
					{{ breadcrumbs.p4.titulo }}
				{% elseif breadcrumbs.p3.titulo != ""  %}
					{{ breadcrumbs.p3.titulo }}
				{% elseif breadcrumbs.p2.titulo != ""  %}
					{{ breadcrumbs.p2.titulo }}
				{% else %}
					{{ breadcrumbs.p1.titulo }}
				{% endif %}
			</h1>
			{% if breadcrumbs.p2.titulo != "" %}
			<ul class="pull-right breadcrumb">
				<li><a href="{{ breadcrumbs.p1.link}}"> {{ breadcrumbs.p1.titulo }}</a></li>
				<li {% if breadcrumbs.p3.titulo == "" %} class="active" {% endif %} > {% if breadcrumbs.p3.titulo != "" %} <a href="{{ breadcrumbs.p2.link}}"> {% endif %} {{ breadcrumbs.p2.titulo}} {% if breadcrumbs.p3.titulo != "" %} </a> {% endif %} </li>
				{% if breadcrumbs.p3.titulo != "" %} <li {% if breadcrumbs.p4.titulo == "" %} class="active" {% endif %} > {% if breadcrumbs.p4.titulo != "" %} <a href="{{ breadcrumbs.p3.link}}"> {% endif %} {{ breadcrumbs.p3.titulo}} {% if breadcrumbs.p4.titulo != "" %} </a> {% endif %} </li> {% endif %}
				{% if breadcrumbs.p4.titulo != "" %} <li class="active"> {{ breadcrumbs.p4.titulo}} </li> {% endif %}
			</ul>
			{% endif %}
		</div>
	</div><!--/breadcrumbs-->
	<!--=== End Breadcrumbs ===-->

{% endif %}

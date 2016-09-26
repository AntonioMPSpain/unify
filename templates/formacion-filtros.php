<div class="filter-by-block">
	<div class="panel-group" id="accordion">
		<div class="panel panel-default">
			<div class="panel-heading-filtros">
				<h2 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"> Filtros <i class="fa fa-angle-down"></i> </a></h2>
			</div>
			<div id="collapseOne" class="panel-collapse collapse">

				<form action="#" id="sky-form2" class="sky-form label-rounded">
		
					<fieldset>
						<div class="col-md-12">
							<section class="col-md-8">
								<label class="label">Texto</label>
								<label class="input">
									<input type="text">
								</label>
							</section>
							
							<section class="col-md-4">
								<label class="label">Categoría</label>
								<label class="select">
									<select>
								    {% for etiqueta in etiquetas %}
								        <option value="{{ etiqueta.id }}">{{ etiqueta.texto }}</option>
								    {% endfor %}
									</select>
									<i></i>
								</label>
							</section>
						</div>
						<div class="col-md-12">
						<section class="col-md-6">
							<label class="label">Modalidad</label>
							<div class="inline-group">
								<label class="checkbox"><input type="checkbox" name="checkbox-inline" checked><i></i>Presencial y On-line</label>
								<label class="checkbox"><input type="checkbox" name="checkbox-inline" checked><i></i>Permanente</label>
								<label class="checkbox"><input type="checkbox" name="checkbox-inline"><i></i>Histórico</label>
							</div>
						</section>
						
						<section class="col-md-6">
							<label class="label rounded-x">Precio (<span id="slider2-value1-rounded">50</span> - <span id="slider2-value2-rounded">300</span>)</label>
							<div id="slider2-rounded"></div>
						</section>
						<section>
							<button type="submit" class="pull-right btn-u margin-bottom-10">Filtrar</button>
						</section>
						</div>
					</fieldset>
				</form>
			</div>
		</div>
	</div><!--/end panel group-->
</div>


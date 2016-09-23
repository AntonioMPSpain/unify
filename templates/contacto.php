
{% include 'breadcrumbs.php' %}

<!--=== Content Part ===-->
<div class="container content">
	<div class="row margin-bottom-30">
		<div class="col-md-12 mb-margin-bottom-30">
					
			<form action="assets/php/sky-forms-pro/demo-contacts-process.php" method="post" id="sky-form3" class="sky-form sky-changes-3">
				<fieldset>
					
					<!--Tag Box v2-->
					<div class="tag-box tag-box-v2 margin-bottom-40">
						<p>Para efectuar la consulta, <strong>seleccione en el desplegable su colegio</strong>, y si no pertenece a estos colegios, seleccione activatie.</p>
					</div>
					<!--End Tag Box v2-->
					
					<section>
						<label class="label">Colegio</label> 
						<label class="select">
							<i class="icon-append fa"></i>
							<select name="colegio" data-width="100%">
							    {% for colegio in colegios %}
							        <option value="{{ colegio.id }}">{{ colegio.nombre }}</option>
							    {% endfor %}
							</select>	
						</label>
					</section>
					<div class="row">
						<section class="col col-6">
							<label class="label">Nombre y apellidos</label>
							<label class="input">
								<i class="icon-prepend fa fa-user"></i>
								<input type="text" name="name" id="name">
							</label>
						</section>
						<section class="col col-6">
							<label class="label">E-mail</label>
							<label class="input">
								<i class="icon-prepend fa fa-envelope-o"></i>
								<input type="email" name="email" id="email">
							</label>
						</section>
					</div>

					<section>
						<label class="label">Mensaje</label>
						<label class="textarea">
							<textarea rows="4" name="message" id="message"></textarea>
						</label>
					</section>

					<section>
						<label class="label">Introduzca los carácteres:</label>
						<label class="input input-captcha">
							<img src="assets/plugins/sky-forms-pro/skyforms/captcha/image.php?<?php echo time(); ?>" width="100" height="32" alt="Captcha image" />
							<input type="text" maxlength="6" name="captcha" id="captcha">
						</label>
					</section>

					<button type="submit" class="btn-u pull-right">Enviar</button>
					
				</fieldset>


				<div class="message">
					<i class="rounded-x fa fa-check"></i>
					<p>Your message was successfully sent!</p>
				</div>
			</form>
		</div><!--/col-md-12-->

		
	</div><!--/row-->
</div><!--/container-->
<!--=== End Content Part ===-->
	

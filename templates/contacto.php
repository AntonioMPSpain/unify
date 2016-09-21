<?php

// Make the page validate
ini_set('session.use_trans_sid', '0');

// Create a random string, leaving out 'o' to avoid confusion with '0'
$char = strtoupper(substr(str_shuffle('abcdefghjkmnpqrstuvwxyz'), 0, 4));

// Concatenate the random string onto the random numbers
// The font 'Anorexia' doesn't have a character for '8', so the numbers will only go up to 7
// '0' is left out to avoid confusion with 'O'
$str = rand(1, 7) . rand(1, 7) . $char;

// Begin the session
session_start();

// Set the session contents
$_SESSION['captcha_id'] = $str;

?>
<!--=== Breadcrumbs ===-->
<div class="breadcrumbs">
	<div class="container">
		<h1 class="pull-left">Contacto</h1>
	</div>
</div><!--/breadcrumbs-->
<!--=== End Breadcrumbs ===-->
<!--=== Content Part ===-->
<div class="container content">
	<div class="row margin-bottom-30">
		<div class="col-md-12 mb-margin-bottom-30">
			<p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas feugiat. Et harum quidem rerum facilis est et expedita distinctio lorem ipsum dolor sit amet, consectetur adipiscing elit landitiis.</p>
			<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut non libero magna. Sed et quam lacus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas feugiat.</p><br>

			<form action="assets/php/sky-forms-pro/demo-contacts-process.php" method="post" id="sky-form3" class="sky-form sky-changes-3">
				<fieldset>
					<div class="row">
						<section class="col col-12">
							<label class="label">Colegio</label>
							<label class="input">
								<i class="icon-append fa"></i>
								<select name="colegio" id="colegio">
									<option value="0">Choose name</option>
									
								</select>	
							</label>
						</section>
						<section class="col col-6">
							<label class="label">Nombre</label>
							<label class="input">
								<i class="icon-append fa fa-user"></i>
								<input type="text" name="name" id="name">
							</label>
						</section>
						<section class="col col-6">
							<label class="label">Apellidos</label>
							<label class="input">
								<i class="icon-append fa fa-user"></i>
								<input type="text" name="surname" id="surname">
							</label>
						</section>
						<section class="col col-6">
							<label class="label">E-mail</label>
							<label class="input">
								<i class="icon-append fa fa-envelope-o"></i>
								<input type="email" name="email" id="email">
							</label>
						</section>
						<section class="col col-6">
							<label class="label">Teléfono</label>
							<label class="input">
								<i class="icon-append fa fa-phone"></i>
								<input type="email" name="phone" id="phone">
							</label>
						</section>
					</div>

					<section>
						<label class="label">Mensaje</label>
						<label class="textarea">
							<i class="icon-append fa fa-comment"></i>
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

					<section>
						<label class="checkbox"><input type="checkbox" name="copy"><i></i>Enviar copia a mi dirección de e-mail</label>
					</section>
				</fieldset>

				<footer>
					<button type="submit" class="btn-u">Enviar</button>
				</footer>

				<div class="message">
					<i class="rounded-x fa fa-check"></i>
					<p>Your message was successfully sent!</p>
				</div>
			</form>
		</div><!--/col-md-12-->

		
	</div><!--/row-->
</div><!--/container-->
<!--=== End Content Part ===-->

	

<div class="cd-user-modal"> <!-- this is the entire modal form, including the background -->
	<div class="cd-user-modal-container"> <!-- this is the container wrapper -->
		<ul class="cd-switcher">
			<li><a href="javascript:void(0);">Login</a></li>
			<li><a href="javascript:void(0);">Registro</a></li>
		</ul>

		<div id="cd-login"> <!-- log in form -->
			<form class="cd-form" action="_control-user.php" method="post">

				<p class="fieldset">
					<label class="image-replace cd-username" for="signin-email">NIF</label>
					<input class="full-width has-padding has-border" id="signin-login" name="signin-login" type="text" placeholder="NIF (sin letra)">
					<span class="cd-error-message">Error message here!</span>
				</p>

				<p class="fieldset">
					<label class="image-replace cd-password" for="signin-password">Contraseña</label>
					<input class="full-width has-padding has-border" id="signin-password" name="signin-password" type="password"  placeholder="Contraseña">
					<span class="cd-error-message">Error message here!</span>
				</p>

				<p class="fieldset">
					<input class="full-width" type="submit" value="Acceder">
				</p>
				
			</form>

			<p class="cd-form-bottom-message"><a href="javascript:void(0);">¿Olvidó su contraseña?</a></p>
			<a href="javascript:void(0);" class="cd-close-form">Close</a>
		</div> <!-- cd-login -->

		<div id="cd-signup"> <!-- sign up form -->
			<form class="cd-form">

				<p class="fieldset">
					<label class="image-replace cd-username" for="signup-username">NIF</label>
					<input class="full-width has-padding has-border" id="signup-username" type="text" placeholder="NIF">
					<span class="cd-error-message">Error message here!</span>
				</p>
				
				<p class="fieldset">
					<label class="image-replace cd-username" for="signup-username">Nombre</label>
					<input class="full-width has-padding has-border" id="signup-nombre" type="text" placeholder="Nombre">
					<span class="cd-error-message">Error message here!</span>
				</p>
				
				<p class="fieldset">
					<label class="image-replace cd-username" for="signup-username">Apellidos</label>
					<input class="full-width has-padding has-border" id="signup-apellidos" type="text" placeholder="Apellidos">
					<span class="cd-error-message">Error message here!</span>
				</p>
				
				<p class="fieldset">
					<label class="image-replace cd-email" for="signup-email">E-mail</label>
					<input class="full-width has-padding has-border" id="signup-email" type="email" placeholder="E-mail">
					<span class="cd-error-message">Error message here!</span>
				</p>
				
				<p class="fieldset">
					<label class="image-replace cd-email" for="signup-email">Repetir e-mail</label>
					<input class="full-width has-padding has-border" autocomplete="off" id="signup-repetiremail" type="email" placeholder="Repetir e-mail">
					<span class="cd-error-message">Error message here!</span>
				</p>
				
				<p class="fieldset">
					<label class="image-replace cd-password" for="signup-password">Contraseña</label>
					<input class="full-width has-padding has-border" id="signup-password" type="text"  placeholder="Contraseña">
					<span class="cd-error-message">Error message here!</span>
				</p>

				<p class="fieldset">
					<input type="checkbox" id="accept-terms">
					<label for="accept-terms">Acepto las <a href="page_terms.html">condiciones de servicio</a></label>
				</p>

				<p class="fieldset">
					<input class="full-width has-padding" type="submit" value="Crear cuenta">
				</p>
			</form>

			<!-- <a href="javascript:void(0);" class="cd-close-form">Close</a> -->
		</div> <!-- cd-signup -->

		<div id="cd-reset-password"> <!-- reset password form -->
			<p class="cd-form-message">¿Olvidó su contraseña? Introduce tu email de registro, recibirá un enlace para establecer una nueva contraseña.</p>

			<form class="cd-form">
				<p class="fieldset">
					<label class="image-replace cd-email" for="reset-email">E-mail</label>
					<input class="full-width has-padding has-border" id="reset-email" type="email" placeholder="E-mail">
					<span class="cd-error-message">Error message here!</span>
				</p>

				<p class="fieldset">
					<input class="full-width has-padding" type="submit" value="Reestablecer contraseña">
				</p>
			</form>

			<p class="cd-form-bottom-message"><a href="javascript:void(0);">Volver atrás</a></p>
		</div> <!-- cd-reset-password -->
		<a href="javascript:void(0);" class="cd-close-form">Close</a>
	</div> <!-- cd-user-modal-container -->
</div> <!-- cd-user-modal -->
<?
include_once "_funciones.php"; 
?>
<form action="alumno_alta.php?accion=guardar<?=$actionform?>" method="post" enctype="multipart/form-data">
			<fieldset>				    
				<legend><?=$legendform?></legend>
				<? if (($_SESSION[nivel]==1)||($_SESSION[nivel]==2)) { //Admin Total ?>				
					<div class="control-group">
						<label class="control-label" for="inputName">Colegio profesional:</label>
							<div class="controls">
								<select name="idcolegio" class="input-large" >
								<?
								// Generar listado 
									if ($_SESSION[nivel]==2){
										if ($idcolegio<>""){
											
											$consulta = "SELECT * FROM usuario WHERE borrado=0 AND id IN('$_SESSION[idcolegio]', '$idcolegio')";
										}
										else{
											
											$consulta = "SELECT * FROM usuario WHERE borrado=0 AND id IN('$_SESSION[idcolegio]')";
										}
									}
									else{
										$consulta = "SELECT * FROM usuario WHERE nivel=2 AND borrado = 0 ORDER BY nombre,id;";
									}
									$link=iConectarse(); 
									$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
									
									while($rowdg= pg_fetch_array($r_datos)) {	
										?>
										<option class="input-large" value="<?=$rowdg['id']?>"<? if  ($idcolegio == $rowdg['id']) { echo " selected "; } ?>><? echo ($rowdg['nombre']); ?></option>
										<? 
									} ?>
							  
									<option <? if  ($idcolegio == 0) { echo " selected "; } ?> class="input-large" value="0">[sin asignar] NO COLEGIADO</option> 
							  </select>
							</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="inputName">Nivel:</label>
							<div class="controls">
								<select name="nivel" class="input-large" >
									<option class="input-large" <? if ($nivel==4) echo " selected "; ?>  value="4">Alumno</option>
									<option class="input-large" <? if (($nivel==3)||($nuevo==3)) echo " selected "; ?>  value="3">Profesor</option>
									
									<? if ($_SESSION[nivel]==1){ ?>
									
										<option class="input-large" <? if ($nivel==1) echo " selected "; ?>  value="1">Administrador Total</option>
										<option class="input-large" <? if ($nivel==2) echo " selected "; ?>  value="2">Administrador Colegio</option>
										
									<? } ?>
								</select>
							</div>
					</div>
				<? } ?>
				 <? /*
				  <div class="control-group">
					<label class="control-label" for="inputName">Tipo:</label>
						<div class="controls">
							<select name="tipo" class="input-large" >
								<option class="input-large" <? if ($tipo==0) echo " selected "; ?> value="0">[sin definir]</option>
								<option class="input-large" <? if ($tipo==1) echo " selected "; ?>  value="1">Colegiado</option>
								<option class="input-large" <? if ($tipo==2) echo " selected "; ?>  value="2">Pre-colegiado</option>
								<option class="input-large" <? if ($tipo==3) echo " selected "; ?>  value="3">Estudiante</option>
								<option class="input-large" <? if ($tipo==4) echo " selected "; ?>  value="4">No colegiado</option>
							</select>
						</div>
				</div>  */ ?>
				<? if ($confirmado==0){ ?>
				<div class="control-group">
					<label class="control-label" for="inputName">Confirmado:</label>
						<div class="controls">
							<select name="confirmado" class="input-large" >
								<option class="input-large" <? if ($confirmado==1) echo " selected "; ?>  value="1">SI</option>
								<option class="input-large" <? if ($confirmado==0) echo " selected "; ?> value="0">NO</option>
							</select>
						</div>
				</div>
				<? } 
				else {?>
					<input type="hidden" name="confirmado" id="confirmado" value="1" />	
				
				<? } ?>
				<hr />
				<div class="control-group">
					<label class="control-label" for="inputName">NIF: [login] (obligatorio)</label>
						<div class="controls">
							<input type="text" id="inputName" placeholder="NIF" class="input-small" name="nif" value="<?=$nif?>" />
						<? 
						//Returns: 1 = NIF ok, 2 = CIF ok, 3 = NIE ok,	
						if (($nif!="") && (valida_nif_cif_nie($nif)<1)) { //si el nif_cif_nie es incorrecto
							?> <span style="color:#FF0000">* nif/cif/nie no es correcto</span><?
						}
						if ($est2=="ko5"){ ?> <span class="rojo"> * error</span><? }?>

						</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputName">Nombre: (obligatorio)</label>
						<div class="controls">
							<input type="text" id="inputName" placeholder="Nombre" class="input-xlarge" name="nombre" value="<?=$nombre?>" />
						</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputName">Apellidos: (obligatorio)</label>
						<div class="controls">
							<input type="text" id="inputa" placeholder="Apellidos" class="input-xlarge" name="apellidos" value="<?=$apellidos?>" />
						</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="inputName">E-mail: (obligatorio)
						<? if ($est2=="ko2"){ ?> <span class="rojo"> * error</span><? }?>
						<? if ($est2=="ko4"){ ?> <span class="rojo"> * error</span><? }?>
					</label>
					<div class="controls">
						<input type="text" id="inputEmail" placeholder="correo@electronico.com" class="input-xlarge" name="email" value="<?=$email?>" />
					</div>
					<?
					if (($email!="") && (comprobar_email($email)<1)) { //si el nif_cif_nie es incorrecto
						?> <span style="color:#FF0000">* email no es correcto</span><?
					}
					?>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="inputName">Tel&eacute;fono:</label>
						<div class="controls">
							<input type="text" id="inputName" class="input-small" placeholder="Teléfono" name="telefono" value="<?=$telefono?>" />
						</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputte2">Tel&eacute;fono 2:</label>
						<div class="controls">
							<input type="text" id="input2e" class="input-small" placeholder="Teléfono" name="telefono2" value="<?=$telefono2?>" />
						</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="inputName">Direcci&oacute;n:</label>
						<div class="controls">
							<input type="text" id="inputName" class="input-xxlarge" placeholder="Dirección" name="direccion" value="<?=$direccion?>" />
						</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="inputName">Municipio:</label>
						<div class="controls">
							<input type="text" id="inputName" class="input-xlarge" placeholder="Municipio" name="municipio" value="<?=$municipio?>" />
						</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputName">Código postal:</label>
					<div class="controls">
						<input type="text" id="inputName" class="input-small" placeholder="Código postal" name="cp" value="<?=$cp?>" />
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="inputName">País:</label>
					<select name="pais">
						<option <? if ($pais=="ES"){ echo 'selected';} ?> value="ES">España</option>
						<option <? if ($pais=="AF"){ echo 'selected';} ?> value="AF">Afganistán</option>
						<option <? if ($pais=="AL"){ echo 'selected';} ?> value="AL">Albania</option>
						<option <? if ($pais=="DE"){ echo 'selected';} ?> value="DE">Alemania</option>
						<option <? if ($pais=="AD"){ echo 'selected';} ?> value="AD">Andorra</option>
						<option <? if ($pais=="AO"){ echo 'selected';} ?> value="AO">Angola</option>
						<option <? if ($pais=="AI"){ echo 'selected';} ?> value="AI">Anguilla</option>
						<option <? if ($pais=="AQ"){ echo 'selected';} ?> value="AQ">Antártida</option>
						<option <? if ($pais=="AG"){ echo 'selected';} ?> value="AG">Antigua y Barbuda</option>
						<option <? if ($pais=="AN"){ echo 'selected';} ?> value="AN">Antillas Holandesas</option>
						<option <? if ($pais=="SA"){ echo 'selected';} ?> value="SA">Arabia Saudí</option>
						<option <? if ($pais=="DZ"){ echo 'selected';} ?> value="DZ">Argelia</option>
						<option <? if ($pais=="AR"){ echo 'selected';} ?> value="AR">Argentina</option>
						<option <? if ($pais=="AM"){ echo 'selected';} ?> value="AM">Armenia</option>
						<option <? if ($pais=="AW"){ echo 'selected';} ?> value="AW">Aruba</option>
						<option <? if ($pais=="AU"){ echo 'selected';} ?> value="AU">Australia</option>
						<option <? if ($pais=="AT"){ echo 'selected';} ?> value="AT">Austria</option>
						<option <? if ($pais=="AZ"){ echo 'selected';} ?> value="AZ">Azerbaiyán</option>
						<option <? if ($pais=="BS"){ echo 'selected';} ?> value="BS">Bahamas</option>
						<option <? if ($pais=="BH"){ echo 'selected';} ?> value="BH">Bahrein</option>
						<option <? if ($pais=="BD"){ echo 'selected';} ?> value="BD">Bangladesh</option>
						<option <? if ($pais=="BB"){ echo 'selected';} ?> value="BB">Barbados</option>
						<option <? if ($pais=="BE"){ echo 'selected';} ?> value="BE">Bélgica</option>
						<option <? if ($pais=="BZ"){ echo 'selected';} ?> value="BZ">Belice</option>
						<option <? if ($pais=="BJ"){ echo 'selected';} ?> value="BJ">Benin</option>
						<option <? if ($pais=="BM"){ echo 'selected';} ?> value="BM">Bermudas</option>
						<option <? if ($pais=="BY"){ echo 'selected';} ?> value="BY">Bielorrusia</option>
						<option <? if ($pais=="MM"){ echo 'selected';} ?> value="MM">Birmania</option>
						<option <? if ($pais=="BO"){ echo 'selected';} ?> value="BO">Bolivia</option>
						<option <? if ($pais=="BA"){ echo 'selected';} ?> value="BA">Bosnia y Herzegovina</option>
						<option <? if ($pais=="BW"){ echo 'selected';} ?> value="BW">Botswana</option>
						<option <? if ($pais=="BR"){ echo 'selected';} ?> value="BR">Brasil</option>
						<option <? if ($pais=="BN"){ echo 'selected';} ?> value="BN">Brunei</option>
						<option <? if ($pais=="BG"){ echo 'selected';} ?> value="BG">Bulgaria</option>
						<option <? if ($pais=="BF"){ echo 'selected';} ?> value="BF">Burkina Faso</option>
						<option <? if ($pais=="BI"){ echo 'selected';} ?> value="BI">Burundi</option>
						<option <? if ($pais=="BT"){ echo 'selected';} ?> value="BT">Bután</option>
						<option <? if ($pais=="CV"){ echo 'selected';} ?> value="CV">Cabo Verde</option> 
						<option <? if ($pais=="KH"){ echo 'selected';} ?> value="KH">Camboya</option>
						<option <? if ($pais=="CM"){ echo 'selected';} ?> value="CM">Camerún</option>
						<option <? if ($pais=="CA"){ echo 'selected';} ?> value="CA">Canadá</option>
						<option <? if ($pais=="TD"){ echo 'selected';} ?> value="TD">Chad</option>
						<option <? if ($pais=="CL"){ echo 'selected';} ?> value="CL">Chile</option>
						<option <? if ($pais=="CN"){ echo 'selected';} ?> value="CN">China</option>
						<option <? if ($pais=="CY"){ echo 'selected';} ?> value="CY">Chipre</option>
						<option <? if ($pais=="VA"){ echo 'selected';} ?> value="VA">Ciudad del Vaticano (Santa Sede)</option>
						<option <? if ($pais=="CO"){ echo 'selected';} ?> value="CO">Colombia</option>
						<option <? if ($pais=="KM"){ echo 'selected';} ?> value="KM">Comores</option>
						<option <? if ($pais=="CG"){ echo 'selected';} ?> value="CG">Congo</option>
						<option <? if ($pais=="CD"){ echo 'selected';} ?> value="CD">Congo, República Democrática del</option>
						<option <? if ($pais=="KR"){ echo 'selected';} ?> value="KR">Corea</option>
						<option <? if ($pais=="KP"){ echo 'selected';} ?> value="KP">Corea del Norte</option>
						<option <? if ($pais=="CI"){ echo 'selected';} ?> value="CI">Costa de Marfíl</option>
						<option <? if ($pais=="CR"){ echo 'selected';} ?> value="CR">Costa Rica</option>
						<option <? if ($pais=="HR"){ echo 'selected';} ?> value="HR">Croacia (Hrvatska)</option>
						<option <? if ($pais=="CU"){ echo 'selected';} ?> value="CU">Cuba</option>
						<option <? if ($pais=="DK"){ echo 'selected';} ?> value="DK">Dinamarca</option>
						<option <? if ($pais=="DJ"){ echo 'selected';} ?> value="DJ">Djibouti</option>
						<option <? if ($pais=="DM"){ echo 'selected';} ?> value="DM">Dominica</option>
						<option <? if ($pais=="EC"){ echo 'selected';} ?> value="EC">Ecuador</option>
						<option <? if ($pais=="EG"){ echo 'selected';} ?> value="EG">Egipto</option>
						<option <? if ($pais=="SV"){ echo 'selected';} ?> value="SV">El Salvador</option>
						<option <? if ($pais=="AE"){ echo 'selected';} ?> value="AE">Emiratos Árabes Unidos</option>
						<option <? if ($pais=="ER"){ echo 'selected';} ?> value="ER">Eritrea</option>
						<option <? if ($pais=="SI"){ echo 'selected';} ?> value="SI">Eslovenia</option>
						<option <? if ($pais=="US"){ echo 'selected';} ?> value="US">Estados Unidos</option>
						<option <? if ($pais=="EE"){ echo 'selected';} ?> value="EE">Estonia</option>
						<option <? if ($pais=="ET"){ echo 'selected';} ?> value="ET">Etiopía</option>
						<option <? if ($pais=="FJ"){ echo 'selected';} ?> value="FJ">Fiji</option>
						<option <? if ($pais=="PH"){ echo 'selected';} ?> value="PH">Filipinas</option>
						<option <? if ($pais=="FI"){ echo 'selected';} ?> value="FI">Finlandia</option>
						<option <? if ($pais=="FR"){ echo 'selected';} ?> value="FR">Francia</option>
						<option <? if ($pais=="GA"){ echo 'selected';} ?> value="GA">Gabón</option>
						<option <? if ($pais=="GM"){ echo 'selected';} ?> value="GM">Gambia</option>
						<option <? if ($pais=="GE"){ echo 'selected';} ?> value="GE">Georgia</option>
						<option <? if ($pais=="GH"){ echo 'selected';} ?> value="GH">Ghana</option>
						<option <? if ($pais=="GI"){ echo 'selected';} ?> value="GI">Gibraltar</option>
						<option <? if ($pais=="GD"){ echo 'selected';} ?> value="GD">Granada</option>
						<option <? if ($pais=="GR"){ echo 'selected';} ?> value="GR">Grecia</option>
						<option <? if ($pais=="GL"){ echo 'selected';} ?> value="GL">Groenlandia</option>
						<option <? if ($pais=="GP"){ echo 'selected';} ?> value="GP">Guadalupe</option>
						<option <? if ($pais=="GU"){ echo 'selected';} ?> value="GU">Guam</option>
						<option <? if ($pais=="GT"){ echo 'selected';} ?> value="GT">Guatemala</option>
						<option <? if ($pais=="GY"){ echo 'selected';} ?> value="GY">Guayana</option>
						<option <? if ($pais=="GF"){ echo 'selected';} ?> value="GF">Guayana Francesa</option>
						<option <? if ($pais=="GN"){ echo 'selected';} ?> value="GN">Guinea</option>
						<option <? if ($pais=="GQ"){ echo 'selected';} ?> value="GQ">Guinea Ecuatorial</option>
						<option <? if ($pais=="GW"){ echo 'selected';} ?> value="GW">Guinea-Bissau</option>
						<option <? if ($pais=="HT"){ echo 'selected';} ?> value="HT">Haití</option>
						<option <? if ($pais=="HN"){ echo 'selected';} ?> value="HN">Honduras</option>
						<option <? if ($pais=="HU"){ echo 'selected';} ?> value="HU">Hungría</option>
						<option <? if ($pais=="IN"){ echo 'selected';} ?> value="IN">India</option>
						<option <? if ($pais=="ID"){ echo 'selected';} ?> value="ID">Indonesia</option>
						<option <? if ($pais=="IQ"){ echo 'selected';} ?> value="IQ">Irak</option>
						<option <? if ($pais=="IR"){ echo 'selected';} ?> value="IR">Irán</option>
						<option <? if ($pais=="IE"){ echo 'selected';} ?> value="IE">Irlanda</option>
						<option <? if ($pais=="BV"){ echo 'selected';} ?> value="BV">Isla Bouvet</option>
						<option <? if ($pais=="CX"){ echo 'selected';} ?> value="CX">Isla de Christmas</option>
						<option <? if ($pais=="IS"){ echo 'selected';} ?> value="IS">Islandia</option>
						<option <? if ($pais=="KY"){ echo 'selected';} ?> value="KY">Islas Caimán</option>
						<option <? if ($pais=="CK"){ echo 'selected';} ?> value="CK">Islas Cook</option>
						<option <? if ($pais=="CC"){ echo 'selected';} ?> value="CC">Islas de Cocos o Keeling</option>
						<option <? if ($pais=="FO"){ echo 'selected';} ?> value="FO">Islas Faroe</option>
						<option <? if ($pais=="HM"){ echo 'selected';} ?> value="HM">Islas Heard y McDonald</option>
						<option <? if ($pais=="FK"){ echo 'selected';} ?> value="FK">Islas Malvinas</option>
						<option <? if ($pais=="MP"){ echo 'selected';} ?> value="MP">Islas Marianas del Norte</option>
						<option <? if ($pais=="MH"){ echo 'selected';} ?> value="MH">Islas Marshall</option>
						<option <? if ($pais=="UM"){ echo 'selected';} ?> value="UM">Islas menores de Estados Unidos</option>
						<option <? if ($pais=="PW"){ echo 'selected';} ?> value="PW">Islas Palau</option>
						<option <? if ($pais=="SB"){ echo 'selected';} ?> value="SB">Islas Salomón</option>
						<option <? if ($pais=="SJ"){ echo 'selected';} ?> value="SJ">Islas Svalbard y Jan Mayen</option>
						<option <? if ($pais=="TK"){ echo 'selected';} ?> value="TK">Islas Tokelau</option>
						<option <? if ($pais=="TC"){ echo 'selected';} ?> value="TC">Islas Turks y Caicos</option>
						<option <? if ($pais=="VI"){ echo 'selected';} ?> value="VI">Islas Vírgenes (EEUU)</option>
						<option <? if ($pais=="VG"){ echo 'selected';} ?> value="VG">Islas Vírgenes (Reino Unido)</option>
						<option <? if ($pais=="WF"){ echo 'selected';} ?> value="WF">Islas Wallis y Futuna</option>
						<option <? if ($pais=="IL"){ echo 'selected';} ?> value="IL">Israel</option>
						<option <? if ($pais=="IT"){ echo 'selected';} ?> value="IT">Italia</option>
						<option <? if ($pais=="JM"){ echo 'selected';} ?> value="JM">Jamaica</option>
						<option <? if ($pais=="JP"){ echo 'selected';} ?> value="JP">Japón</option>
						<option <? if ($pais=="JO"){ echo 'selected';} ?> value="JO">Jordania</option>
						<option <? if ($pais=="KZ"){ echo 'selected';} ?> value="KZ">Kazajistán</option>
						<option <? if ($pais=="KE"){ echo 'selected';} ?> value="KE">Kenia</option>
						<option <? if ($pais=="KG"){ echo 'selected';} ?> value="KG">Kirguizistán</option>
						<option <? if ($pais=="KI"){ echo 'selected';} ?> value="KI">Kiribati</option>
						<option <? if ($pais=="KW"){ echo 'selected';} ?> value="KW">Kuwait</option>
						<option <? if ($pais=="LA"){ echo 'selected';} ?> value="LA">Laos</option>
						<option <? if ($pais=="LS"){ echo 'selected';} ?> value="LS">Lesotho</option>
						<option <? if ($pais=="LV"){ echo 'selected';} ?> value="LV">Letonia</option>
						<option <? if ($pais=="LB"){ echo 'selected';} ?> value="LB">Líbano</option>
						<option <? if ($pais=="LR"){ echo 'selected';} ?> value="LR">Liberia</option>
						<option <? if ($pais=="LY"){ echo 'selected';} ?> value="LY">Libia</option>
						<option <? if ($pais=="LI"){ echo 'selected';} ?> value="LI">Liechtenstein</option>
						<option <? if ($pais=="LT"){ echo 'selected';} ?> value="LT">Lituania</option>
						<option <? if ($pais=="LU"){ echo 'selected';} ?> value="LU">Luxemburgo</option>
						<option <? if ($pais=="MK"){ echo 'selected';} ?> value="MK">Macedonia, Ex-República Yugoslava de</option>
						<option <? if ($pais=="MG"){ echo 'selected';} ?> value="MG">Madagascar</option>
						<option <? if ($pais=="MY"){ echo 'selected';} ?> value="MY">Malasia</option>
						<option <? if ($pais=="MW"){ echo 'selected';} ?> value="MW">Malawi</option>
						<option <? if ($pais=="MV"){ echo 'selected';} ?> value="MV">Maldivas</option>
						<option <? if ($pais=="ML"){ echo 'selected';} ?> value="ML">Malí</option>
						<option <? if ($pais=="MT"){ echo 'selected';} ?> value="MT">Malta</option>
						<option <? if ($pais=="MA"){ echo 'selected';} ?> value="MA">Marruecos</option>
						<option <? if ($pais=="MQ"){ echo 'selected';} ?> value="MQ">Martinica</option>
						<option <? if ($pais=="MU"){ echo 'selected';} ?> value="MU">Mauricio</option>
						<option <? if ($pais=="MR"){ echo 'selected';} ?> value="MR">Mauritania</option>
						<option <? if ($pais=="YT"){ echo 'selected';} ?> value="YT">Mayotte</option>
						<option <? if ($pais=="MX"){ echo 'selected';} ?> value="MX">México</option>
						<option <? if ($pais=="FM"){ echo 'selected';} ?> value="FM">Micronesia</option>
						<option <? if ($pais=="MD"){ echo 'selected';} ?> value="MD">Moldavia</option>
						<option <? if ($pais=="MC"){ echo 'selected';} ?> value="MC">Mónaco</option>
						<option <? if ($pais=="MN"){ echo 'selected';} ?> value="MN">Mongolia</option>
						<option <? if ($pais=="MS"){ echo 'selected';} ?> value="MS">Montserrat</option>
						<option <? if ($pais=="MZ"){ echo 'selected';} ?> value="MZ">Mozambique</option>
						<option <? if ($pais=="NA"){ echo 'selected';} ?> value="NA">Namibia</option>
						<option <? if ($pais=="NR"){ echo 'selected';} ?> value="NR">Nauru</option>
						<option <? if ($pais=="NP"){ echo 'selected';} ?> value="NP">Nepal</option>
						<option <? if ($pais=="NI"){ echo 'selected';} ?> value="NI">Nicaragua</option>
						<option <? if ($pais=="NE"){ echo 'selected';} ?> value="NE">Níger</option>
						<option <? if ($pais=="NG"){ echo 'selected';} ?> value="NG">Nigeria</option>
						<option <? if ($pais=="NU"){ echo 'selected';} ?> value="NU">Niue</option>
						<option <? if ($pais=="NF"){ echo 'selected';} ?> value="NF">Norfolk</option>
						<option <? if ($pais=="NO"){ echo 'selected';} ?> value="NO">Noruega</option>
						<option <? if ($pais=="NC"){ echo 'selected';} ?> value="NC">Nueva Caledonia</option>
						<option <? if ($pais=="NZ"){ echo 'selected';} ?> value="NZ">Nueva Zelanda</option>
						<option <? if ($pais=="OM"){ echo 'selected';} ?> value="OM">Omán</option>
						<option <? if ($pais=="NL"){ echo 'selected';} ?> value="NL">Países Bajos</option>
						<option <? if ($pais=="PA"){ echo 'selected';} ?> value="PA">Panamá</option>
						<option <? if ($pais=="PG"){ echo 'selected';} ?> value="PG">Papúa Nueva Guinea</option>
						<option <? if ($pais=="PK"){ echo 'selected';} ?> value="PK">Paquistán</option>
						<option <? if ($pais=="PY"){ echo 'selected';} ?> value="PY">Paraguay</option>
						<option <? if ($pais=="PE"){ echo 'selected';} ?> value="PE">Perú</option>
						<option <? if ($pais=="PN"){ echo 'selected';} ?> value="PN">Pitcairn</option>
						<option <? if ($pais=="PF"){ echo 'selected';} ?> value="PF">Polinesia Francesa</option>
						<option <? if ($pais=="PL"){ echo 'selected';} ?> value="PL">Polonia</option>
						<option <? if ($pais=="PT"){ echo 'selected';} ?> value="PT">Portugal</option>
						<option <? if ($pais=="PR"){ echo 'selected';} ?> value="PR">Puerto Rico</option>
						<option <? if ($pais=="QA"){ echo 'selected';} ?> value="QA">Qatar</option>
						<option <? if ($pais=="UK"){ echo 'selected';} ?> value="UK">Reino Unido</option>
						<option <? if ($pais=="CF"){ echo 'selected';} ?> value="CF">República Centroafricana</option>
						<option <? if ($pais=="CZ"){ echo 'selected';} ?> value="CZ">República Checa</option>
						<option <? if ($pais=="ZA"){ echo 'selected';} ?> value="ZA">República de Sudáfrica</option>
						<option <? if ($pais=="DO"){ echo 'selected';} ?> value="DO">República Dominicana</option>
						<option <? if ($pais=="SK"){ echo 'selected';} ?> value="SK">República Eslovaca</option>
						<option <? if ($pais=="RE"){ echo 'selected';} ?> value="RE">Reunión</option>
						<option <? if ($pais=="RW"){ echo 'selected';} ?> value="RW">Ruanda</option>
						<option <? if ($pais=="RO"){ echo 'selected';} ?> value="RO">Rumania</option>
						<option <? if ($pais=="RU"){ echo 'selected';} ?> value="RU">Rusia</option>
						<option <? if ($pais=="EH"){ echo 'selected';} ?> value="EH">Sahara Occidental</option>
						<option <? if ($pais=="KN"){ echo 'selected';} ?> value="KN">Saint Kitts y Nevis</option>
						<option <? if ($pais=="WS"){ echo 'selected';} ?> value="WS">Samoa</option>
						<option <? if ($pais=="AS"){ echo 'selected';} ?> value="AS">Samoa Americana</option>
						<option <? if ($pais=="SM"){ echo 'selected';} ?> value="SM">San Marino</option>
						<option <? if ($pais=="VC"){ echo 'selected';} ?> value="VC">San Vicente y Granadinas</option>
						<option <? if ($pais=="SH"){ echo 'selected';} ?> value="SH">Santa Helena</option>
						<option <? if ($pais=="LC"){ echo 'selected';} ?> value="LC">Santa Lucía</option>
						<option <? if ($pais=="ST"){ echo 'selected';} ?> value="ST">Santo Tomé y Príncipe</option>
						<option <? if ($pais=="SN"){ echo 'selected';} ?> value="SN">Senegal</option>
						<option <? if ($pais=="SC"){ echo 'selected';} ?> value="SC">Seychelles</option>
						<option <? if ($pais=="SL"){ echo 'selected';} ?> value="SL">Sierra Leona</option>
						<option <? if ($pais=="SG"){ echo 'selected';} ?> value="SG">Singapur</option>
						<option <? if ($pais=="SY"){ echo 'selected';} ?> value="SY">Siria</option>
						<option <? if ($pais=="SO"){ echo 'selected';} ?> value="SO">Somalia</option>
						<option <? if ($pais=="LK"){ echo 'selected';} ?> value="LK">Sri Lanka</option>
						<option <? if ($pais=="PM"){ echo 'selected';} ?> value="PM">St Pierre y Miquelon</option>
						<option <? if ($pais=="SZ"){ echo 'selected';} ?> value="SZ">Suazilandia</option>
						<option <? if ($pais=="SD"){ echo 'selected';} ?> value="SD">Sudán</option>
						<option <? if ($pais=="SE"){ echo 'selected';} ?> value="SE">Suecia</option>
						<option <? if ($pais=="CH"){ echo 'selected';} ?> value="CH">Suiza</option>
						<option <? if ($pais=="SR"){ echo 'selected';} ?> value="SR">Surinam</option>
						<option <? if ($pais=="TH"){ echo 'selected';} ?> value="TH">Tailandia</option>
						<option <? if ($pais=="TW"){ echo 'selected';} ?> value="TW">Taiwán</option>
						<option <? if ($pais=="TZ"){ echo 'selected';} ?> value="TZ">Tanzania</option>
						<option <? if ($pais=="TJ"){ echo 'selected';} ?> value="TJ">Tayikistán</option>
						<option <? if ($pais=="TF"){ echo 'selected';} ?> value="TF">Territorios franceses del Sur</option>
						<option <? if ($pais=="TP"){ echo 'selected';} ?> value="TP">Timor Oriental</option>
						<option <? if ($pais=="TG"){ echo 'selected';} ?> value="TG">Togo</option>
						<option <? if ($pais=="TO"){ echo 'selected';} ?> value="TO">Tonga</option>
						<option <? if ($pais=="TT"){ echo 'selected';} ?> value="TT">Trinidad y Tobago</option>
						<option <? if ($pais=="TN"){ echo 'selected';} ?> value="TN">Túnez</option>
						<option <? if ($pais=="TM"){ echo 'selected';} ?> value="TM">Turkmenistán</option>
						<option <? if ($pais=="TR"){ echo 'selected';} ?> value="TR">Turquía</option>
						<option <? if ($pais=="TV"){ echo 'selected';} ?> value="TV">Tuvalu</option>
						<option <? if ($pais=="UA"){ echo 'selected';} ?> value="UA">Ucrania</option>
						<option <? if ($pais=="UG"){ echo 'selected';} ?> value="UG">Uganda</option>
						<option <? if ($pais=="UY"){ echo 'selected';} ?> value="UY">Uruguay</option>
						<option <? if ($pais=="UZ"){ echo 'selected';} ?> value="UZ">Uzbekistán</option>
						<option <? if ($pais=="VU"){ echo 'selected';} ?> value="VU">Vanuatu</option>
						<option <? if ($pais=="VE"){ echo 'selected';} ?> value="VE">Venezuela</option>
						<option <? if ($pais=="VN"){ echo 'selected';} ?> value="VN">Vietnam</option>
						<option <? if ($pais=="YE"){ echo 'selected';} ?> value="YE">Yemen</option>
						<option <? if ($pais=="YU"){ echo 'selected';} ?> value="YU">Yugoslavia</option>
						<option <? if ($pais=="ZM"){ echo 'selected';} ?> value="ZM">Zambia</option>
						<option <? if ($pais=="ZW"){ echo 'selected';} ?> value="ZW">Zimbabue</option>
					</select>
				</div>
				
				
				<div class="control-group">
					<label class="control-label" for="inputName">Provincia:</label>
						<div class="controls">
							<select name="idprovincia" class="input-large" >
							<?
							// Generar listado 
								$consulta = "SELECT * FROM etiqueta_provincia WHERE borrado = 0 ORDER BY id;";
								$link=iConectarse(); 
								$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
								while($rowdg= pg_fetch_array($r_datos)) {	
									?>
									<option class="input-large" value="<?=$rowdg['id']?>"<? if  ($idprovincia == $rowdg['id']) { echo " selected "; } ?>><? echo ($rowdg['deno']); ?></option>
									<? 
								} ?>
						  </select>
						</div>
				</div>
				
				
				
				<div class="control-group">
					<label class="control-label" for="inputName">Contrase&ntilde;a:
						<? if ($est2=="ko3"){ ?> <span class="rojo"> * error</span><? }?>
						<? if ($est2=="ko6"){ ?> <span class="rojo"> * error</span><? }?>
					</label>
						<div class="controls">
							<input id="inputName" class="input-small" type="password" name="pass"  />
						</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="inputName">Repetir Contrase&ntilde;a:
						<? if ($est2=="ko3"){ ?> <span class="rojo"> * error</span><? }?>
						<? if ($est2=="ko6"){ ?> <span class="rojo"> * error</span><? }?>
					</label>
					<div class="controls">
						<input id="inputName" class="input-small" type="password" name="pass2"  />
					</div>
				</div>
				
				<hr>
				<div class="control-group">
					<label class="control-label" for="inputName">N. Colegiado: (si dispone)</label>
					<div class="controls">
						<input type="text" id="inputName" class="input-mini" name="ncolegiado" value="<?=$ncolegiado?>" />
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label" for="procedencia">Colegio de procedencia: </label>
						<div class="controls">
							<input type="text" id="procedencia" class="input-xxlarge" name="procedencia" value="<?=$procedencia?>" />
						</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="universidad">Universidad de procedencia: </label>
						<div class="controls">
							<input type="text" id="universidad" class="input-xxlarge" name="universidad" value="<?=$universidad?>" />
						</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="titulacion">Titulación: </label>
						<div class="controls">
							<input type="text" id="titulacion" class="input-xxlarge" name="titulacion" value="<?=$titulacion?>" />
						</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="titulacion">Breve curriculum / Titulaciones: </label>
						<div class="controls">
							<textarea name="curriculum" id="curriculum" class="inputtextarea input-xxlarge" cols="45" rows="4" ><?=$curriculum?></textarea>
						</div>
				</div>

				<div class="control-group">
				    <label class="checkbox">
				    <input type="checkbox" name="acepto" checked="checked" /> Acepto las <a href="aviso_legal.php">condiciones del servicio</a>.
						<? if ($est2=="ko1"){ ?> <span class="rojo"> * error</span><? }?>
				    </label>
				</div>
			</fieldset>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary btn-large">Guardar</button>
				<? if ($_SESSION['nivel']==1){ ?>
					<button style="float:right;" type="submit" name="baja" id="baja" onclick="return confirm('Seguro que desea dar de baja al usuario?')" class="btn btn-primary btn-large">Dar de baja</button>
				<? } ?>
			</div>
			</form>

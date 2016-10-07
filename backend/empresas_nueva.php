<?

include("_funciones.php"); 
include("_cone.php");

////////// Filtros de nivel por usuario //////////////////////
session_start();

if ($_SESSION[nivel]==2) { //Admin Colegio
	if ($_SESSION[idcolegio]<>"") {
		$idcolegio=strip_tags($_SESSION[idcolegio]);
		$idusuario = $idcolegio;
		$sqlcolegio="";
		$sqlcolegio = " AND idcurso IN (SELECT id FROM curso WHERE idcolegio='$idcolegio') ";
	}else{
		$_SESSION[esterror]="Parámetros incorrectos";	
		header("Location: index.php?salir=true");
		exit();
	}
}elseif ($_SESSION[nivel]==1) { //Admin Total
	$sqlcolegio="";
}
else{
	$_SESSION[esterror]="Parámetros incorrectos";	
	header("Location: index.php?salir=true");
	exit();
}
////////// FIN Filtros de nivel por usuario ////////////////////// 



$titulo1="nueva";
$titulo2="empresa";
$safe="Marketing Empresas";
include("plantillaweb01admin.php");

?>
<!--Arriba plantilla1-->
<div class="grid-9 contenido-principal">
<div class="clearfix"></div>
	<div class="pagina zonaprivada blog">
	<h2 class="titulonoticia">Insertar Nueva Empresa</h2>
			
		<legend></legend>
		<form id="formcontacto" method="post" action="empresas_nueva.php?accion=guardar" enctype="multipart/form-data" >
		<p>
			<label class="description"><strong><br />Datos de la empresa</strong><br /></label>
		</p>		
		<p>
			<label class="description" for="nombre">Nombre*:<br />
				<input id="nombre" name="nombre" type="text" maxlength="255"  size="80"  class="input-xxlarge"  />  
			</label>
		</p>
							
		<p>
			<label class="description" for="nombre">CIF:<br />
				<input id="cif" name="cif" type="text" maxlength="255"  size="80"  class="input-xxlarge"  />  
			</label>
		</p>	
		
		<p>
			<label class="description" for="domicilio">Domicilio:<br />
				<input id="domicilio" name="domicilio" type="text" maxlength="255"  size="80" class="input-xxlarge" 	 />  
			</label>
		</p>
		<p>
			<label class="description" for="localidad">Localidad:<br />
				<input id="localidad" name="localidad" type="text" maxlength="255"  size="80" class="input-xxlarge" 	/>  
			</label>
		</p>
		
		<p>
			<label class="description" for="cp">Código Postal:<br />
				<input id="cp" name="cp" type="text" maxlength="255"  class="input-xxlarge" 	 />  
			</label>
		</p>
		
		<p>
			<label class="description" for="provincia">Ámbito/Provincia:<br />
				<select name="provincia" class="input-large" >
					<option class="input-large" value="Internacional">--Internacional--</option>
					<option class="input-large" value="Nacional">--Nacional--</option>
				<?
				// Generar listado 
					$consulta = "SELECT * FROM etiqueta_provincia WHERE borrado = 0 ORDER BY id,deno;";
					$link=iConectarse(); 
					$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
					while($rowdg= pg_fetch_array($r_datos)) {	
						?>
						<option class="input-large" value="<?=$rowdg['deno']?>"><? echo ($rowdg['deno']); ?></option>
						<? 
					} ?>
			  </select>
			</label>
		</p>
		
		<p>
			<label class="description" for="persona">Persona de contacto:<br />
				<input id="persona" name="persona" type="text" maxlength="255" size="80"  class="input-xxlarge"  />  
			</label>
		</p>
		 
		<p>
			<label class="description" for="fax">Email:<br />
				<input id="email" name="email" type="text" maxlength="255"  class="input-xxlarge"  />  
			</label>
		</p>
		
		<p>
			<label class="description" for="fax">Web:<br />
				<input id="web" name="web" type="text" maxlength="255"  class="input-xxlarge"  />  
			</label>
		</p>
		<p>
			<label class="description" for="telefono">Móvil:<br />
				<input id="movil" name="movil" type="text" maxlength="255"  size="80" class="input-xxlarge"  />  
			</label>
		</p>
		<p>
			<label class="description" for="telefono">Teléfono 1:<br />
				<input id="telefono" name="telefono" type="text" maxlength="255"  size="80" class="input-xxlarge"  />  
			</label>
		</p>
		<p>
			<label class="description" for="telefono">Teléfono 2:<br />
				<input id="telefono2" name="telefono2" type="text" maxlength="255"  size="80" class="input-xxlarge"  />  
			</label>
		</p>
		<p>
			<label class="description" for="fax">Fax:<br />
				<input id="fax" name="fax" type="text" maxlength="255"  class="input-xxlarge"  />  
			</label>
		</p>
		<hr>
		
		<p>
			<label class="description"><strong><br />Familias</strong><br /></label>
		</p>
		
		<p>
			<? $resultfam=posgre_query("SELECT * FROM materiales_familias WHERE borrado=0 ORDER BY nombre;") ;//or die (mysql_error()); 
			
			while ($rowfam = pg_fetch_array($resultfam)){ 
				$idfam = $rowfam['id'];	
				$nombrefam = $rowfam['nombre'];	
			?> &nbsp;&nbsp;	&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" value="<?=$idfam?>" /><?=$nombrefam?> <br><? } ?>
		</p>
		
		<hr>
	
		<p>
			<label class="description"><strong><br />Empresa en activatie</strong><br /></label>
		</p>		
			
		<p>
			<label class="description" for="fecha_insercion">Tipo cuenta:<br />
				<select name="tipocuenta" class="input-large" >
					<option value="0">Nada contratado(Cuenta no activa)</option>
					<option value="1">Pack Básico</option>
					<option value="2">Pack Premium</option>
					
					
				</select>	
			</label>
		</p>		
		
		<p>
			<label class="description" for="fecha_insercion">Fecha inserción:<br /> 
				<input size="10" id="fecha_insercion" name="fecha_insercion" type="text" maxlength="12"  class="input-xxlarge"  value="<?=cambiaf_a_normal($row["fecha_insercion"])?>"  />  
			</label>
		</p>
		
		<p>
			<label class="description" for="fecha_insercion">Colegio inserción:<br /> 
				<input size="10" id="fecha_insercion" name="fecha_insercion" type="text" maxlength="12"  class="input-xxlarge"  value="<?=cambiaf_a_normal($row["fecha_insercion"])?>"  />  
			</label>
		</p>
		
		<p>
			<label class="description" for="fecha_insercion">Fecha modificación:<br />
				<input size="10" id="fecha_insercion" name="fecha_insercion" type="text" maxlength="12"  class="input-xxlarge"  value="<?=cambiaf_a_normal($row["fecha_insercion"])?>"  />  
			</label>
		</p>		
		<p>
			<label class="description" for="requisitos">Requisitos*:<br />
				<textarea id="requisitos" name="requisitos" rows="2" cols="45" class="input-xxlarge" ></textarea> 
			</label>
		</p>
		
		<p>
			<label class="description"><strong><br />Notas</strong><br /></label>
		</p>	
		
		<p>
			<label class="description" for="otros_datos">Otros datos:<br />
				<textarea id="otros_datos" name="otros_datos" rows="2" cols="45" class="input-xxlarge" ></textarea> 
			</label>
		</p>
		
		
		
		<p>
			<label class="description" for="ostos">
				<input  class="btn btn-primary" name="enviar" value="Guardar" type="submit" />
			</label>
		</p>
		
		</form>		
<div class="clearfix"></div>
	</div>
</div>
<!--fin contenido-principal-->

<?

include("plantillaweb02admin.php"); 
?>
<?
session_start();
include("_funciones.php"); 
include("_cone.php"); 
require_once('lib_actv_api.php');

$safe="gestión de curso";
$accion=strip_tags($_REQUEST['accion']); 
$est=strip_tags($_GET['est']);
$est_texto=strip_tags($_GET['est_texto']);
$est_texto2=strip_tags($_GET['est_texto2']);

$id=strip_tags($_REQUEST['id']);
if (($id=="0")){
	$_SESSION[error]="Parametros incorrectos";	
	header("Location: zona-privada_admin_cursos_1.php");
	exit();
}
$idmoodleduplica=strip_tags($_REQUEST['idmoodleduplica']); //para la duplicaccion de la plantilla de curso en moodle 

$curso["nombre"]=$nombre=strip_tags($_POST['nombre']);
$curso["presentacion"]=$presentacion=nl2br(strip_tags($_POST['presentacion']));
$curso["observaciones"]=$observaciones=nl2br(strip_tags($_POST['observaciones']));
$curso["programa"]=$programa=nl2br(strip_tags($_POST['programa']));


$curso["duracion"]=$duracion=strip_tags($_POST['duracion']); if ($duracion=="") $duracion=0;
$curso["estado"]=$estado=strip_tags($_POST['estado']); if ($estado=="") $estado=0;
$curso["permanente"]=$permanente=strip_tags($_POST['permanente']); if ($permanente=="") $permanente=0;
//fechas update
	$fecha_fin_inscripcion="'".cambiaf_a_mysql($_POST['fecha_fin_inscripcion'])."'";
	if ($fecha_fin_inscripcion=="''") $fecha_fin_inscripcion="NULL"; 
	$curso["fecha_fin_inscripcion"]=$fecha_fin_inscripcion;
	
	$fecha_inicio="'".cambiaf_a_mysql($_POST['fecha_inicio'])."'"; 
	if ($fecha_inicio=="''") $fecha_inicio="NULL"; 
	$curso["fecha_inicio"]=$fecha_inicio;
	
	$fecha_publicacion="'".cambiaf_a_mysql($_POST['fecha_publicacion'])."'";
	if ($fecha_publicacion=="''") $fecha_publicacion="NULL"; 
	$curso["fecha_publicacion"]=$fecha_publicacion;
	
	$fecha_fin_publicacion="'".cambiaf_a_mysql($_POST['fecha_fin_publicacion'])."'";
	if ($fecha_fin_publicacion=="''") $fecha_fin_publicacion="NULL"; 
	$curso["fecha_fin_publicacion"]=$fecha_fin_publicacion;
//fin fechas
$curso["precioc"]=$precioc=strip_tags($_POST['precioc']); if ($precioc=="") $precioc=0;
$curso["precion"]=$precion=strip_tags($_POST['precion']); if ($precion=="") $precion=0;
$curso["precioe"]=$precioe=strip_tags($_POST['precioe']); if ($precioe=="") $precioe=0;
$curso["preciop"]=$preciop=strip_tags($_POST['preciop']); if ($preciop=="") $preciop=0;

$curso["precioco"]=$precioco=strip_tags($_POST['precioco']); if ($precioco=="") $precioco=0;
$curso["preciono"]=$preciono=strip_tags($_POST['preciono']); if ($preciono=="") $preciono=0;
$curso["precioeo"]=$precioeo=strip_tags($_POST['precioeo']); if ($precioeo=="") $precioeo=0;
$curso["preciopo"]=$preciopo=strip_tags($_POST['preciopo']); if ($preciopo=="") $preciopo=0;

$curso["preciocp"]=$preciocp=strip_tags($_POST['preciocp']); if ($preciocp=="") $preciocp=0;
$curso["precionp"]=$precionp=strip_tags($_POST['precionp']); if ($precionp=="") $precionp=0;
$curso["precioep"]=$precioep=strip_tags($_POST['precioep']); if ($precioep=="") $precioep=0;
$curso["preciopp"]=$preciopp=strip_tags($_POST['preciopp']); if ($preciopp=="") $preciopp=0;

$curso["preciotachadoc"]=$preciotachadoc=strip_tags($_POST['preciotachadoc']); if ($preciotachadoc=="") $preciotachadoc=0;
$curso["preciotachadon"]=$preciotachadon=strip_tags($_POST['preciotachadon']); if ($preciotachadon=="") $preciotachadon=0;
$curso["preciotachadooc"]=$preciotachadooc=strip_tags($_POST['preciotachadooc']); if ($preciotachadooc=="") $preciotachadooc=0;
$curso["preciotachadoon"]=$preciotachadoon=strip_tags($_POST['preciotachadoon']); if ($preciotachadoon=="") $preciotachadoon=0;
$curso["preciotachadopc"]=$preciotachadopc=strip_tags($_POST['preciotachadopc']); if ($preciotachadopc=="") $preciotachadopc=0;
$curso["preciotachadopn"]=$preciotachadopn=strip_tags($_POST['preciotachadopn']); if ($preciotachadopn=="") $preciotachadopn=0;


$curso["plazopermanente"]=$plazopermanente=strip_tags($_POST['plazopermanente']); if ($plazopermanente=="") $plazopermanente=0;

$curso["lugar"]=$lugar=nl2br(strip_tags($_POST['lugar']));
$curso["ponentes"]=$ponentes=nl2br(strip_tags($_POST['ponentes'])); if ($ponentes=="") $ponentes=0;
$curso["horariosyfechas"]=$horariosyfechas=nl2br(strip_tags($_POST['horariosyfechas'])); if ($horariosyfechas=="") $horariosyfechas=0;
$curso["tipo"]=$tipo=strip_tags($_POST['tipo']);
$curso["iva"]=$iva=strip_tags($_POST['iva']); if ($iva=="") $iva=0;
$curso["plazas"]=$plazas=strip_tags($_POST['plazas']); if ($plazas=="") $plazas=0;
$curso["plazaso"]=$plazaso=strip_tags($_POST['plazaso']); if ($plazaso=="") $plazaso=0;
$curso["plazasperma"]=$plazasperma=strip_tags($_POST['plazasperma']); if ($plazasperma=="") $plazasperma=0;
$curso["modalidad"]=$modalidad=strip_tags($_POST['modalidad']);
$curso["duracionminutos"]=$duracionminutos=strip_tags($_POST['duracionminutos']); if ($duracionminutos=="") $duracionminutos=0;
$curso["convocatoria"]=$convocatoria=strip_tags($_POST['convocatoria']);
if ($_POST['privadocolegiados']=="privadocolegiados"){
	$privadocolegiados=1;
	$checkedprivado=" checked ";
	$hiddenprecio = " style='display:none;'";
}
else{
	$privadocolegiados=0;
	$checkedprivado="";
	$hiddenprecio = "";
}

if ($_POST['diploma']=="diploma"){
	$diploma=1;
	$checkeddiploma=" checked ";
}
else{
	$diploma=0;
	$checkeddiploma="";
}



if ($_SESSION[nivel]==1) { //Admin Total 
	//$id_categoria_moodle=strip_tags($_POST['id_categoria_moodle']);
	$idcolegio=strip_tags($_POST['idcolegio']);
	if ($idcolegio=="") $idcolegio=0;
	//SAcar $id_categoria_moodle de tabla usuarios. El usuario es el idcolegio
	$link=iConectarse(); 
	$result=pg_query($link,"SELECT * FROM usuario WHERE borrado=0 AND id='$idcolegio' ORDER BY id DESC LIMIT 1;"); 
	$cat = pg_fetch_array($result);						
	$id_categoria_moodle=$cat["id_categoria_moodle"]; 

	/*
	$link=iConectarse(); 
	$result=pg_query($link,"SELECT * FROM curso WHERE borrado=0 AND id='$id' ORDER BY id DESC LIMIT 1;"); 
	$cat = pg_fetch_array($result);						
	$id_categoria_moodle=$cat["id_categoria_moodle"];
	*/
}else{
	$idcolegio=$_SESSION[idcolegio];
	$id_categoria_moodle=$_SESSION[id_categoria_moodle];
	if(($id_categoria_moodle=="")||($idcolegio=="")){
		$_SESSION[esterror]="Su usuario de Colegio no está dado de alta en moodle. Debe asignarle permiso el administrador general.";	
		header("Location: index.php?error=true");
		exit();
	}
}
$curso["id_curso_plantilla"]=$id_curso_plantilla=strip_tags($_POST['id_curso_plantilla']);

$curso["idcolegio"]=$idcolegio;
$curso["iddocente"]=$iddocente;



if($accion=="guardar"){
	$accion="";
	$est="ok";
	/*if (comprobar_email($email)==0) { //si el emial es incorrecto
		$est_texto="* E-mail incorrecto";
		$est="ko";
	}*/
	//fechas insert
		$fecha_fin_inscripcion=cambiaf_a_mysql($_POST['fecha_fin_inscripcion']);
		if (($fecha_fin_inscripcion<>"")){
			$sqlfechaA="fecha_fin_inscripcion,";
			$curso["fecha_fin_inscripcion"]=$fecha_fin_inscripcion;			
			$sqlfechaB="'$fecha_fin_inscripcion',";
		}
		$fecha_inicio=cambiaf_a_mysql($_POST['fecha_inicio']);
		if (($fecha_inicio<>"")){
			$sqlfechaA=$sqlfechaA."fecha_inicio,";
			$curso["fecha_inicio"]=$fecha_inicio;			
			$sqlfechaB=$sqlfechaB."'$fecha_inicio',";
		}
		$fecha_fin=cambiaf_a_mysql($_POST['fecha_fin']);
		if (($fecha_fin<>"")){
			$sqlfechaA=$sqlfechaA."fecha_fin,";
			$curso["fecha_fin"]=$fecha_fin;			
			$sqlfechaB=$sqlfechaB."'$fecha_fin',";
		}
		$fecha_publicacion=cambiaf_a_mysql($_POST['fecha_publicacion']);
		if (($fecha_publicacion<>"")){
			$sqlfechaA=$sqlfechaA."fecha_publicacion,";
			$curso["fecha_publicacion"]=$fecha_publicacion;			
			$sqlfechaB=$sqlfechaB."'$fecha_publicacion',";
		}
		$fecha_fin_publicacion=cambiaf_a_mysql($_POST['fecha_fin_publicacion']);
		if (($fecha_fin_publicacion<>"")){
			$sqlfechaA=$sqlfechaA."fecha_fin_publicacion,";
			$curso["fecha_fin_publicacion"]=$fecha_fin_publicacion;			
			$sqlfechaB=$sqlfechaB."'$fecha_fin_publicacion',";
		}
	//fin fechas
	
	
	
	$_SESSION[curso]=$curso;	
	if ($est=="ok"){
		$nombre = pg_escape_string($nombre);
		// RETURNING Currval('curso_id_seq')
		$ssql="INSERT INTO curso (".$sqlfechaA." idcolegio,observaciones,id_categoria_moodle,plazas, plazaso,plazasperma, nombre, presentacion, programa,duracion,modalidad, estado, permanente,precioc, precion, precioe, preciop,precioco, preciono, precioeo, preciopo,preciocp, precionp, precioep, preciopp, lugar, ponentes, horariosyfechas, tipo,iva,plazopermanente,privado,duracionminutos,preciotachadoc,preciotachadon,preciotachadooc,preciotachadoon,preciotachadopc,preciotachadopn, diploma, convocatoria) VALUES (".$sqlfechaB."'$idcolegio','$observaciones','$id_categoria_moodle','$plazas','$plazaso','$plazasperma','$nombre', '$presentacion','$programa', '$duracion', '$modalidad','$estado','$permanente', '$precioc', '$precion', '$precioe', '$preciop', '$precioco', '$preciono', '$precioeo', '$preciopo', '$preciocp', '$precionp', '$precioep', '$preciopp', '$lugar', '$ponentes', '$horariosyfechas', '$tipo','$iva', '$plazopermanente', '$privadocolegiados', '$duracionminutos', '$preciotachadoc', '$preciotachadon', '$preciotachadooc', '$preciotachadoon', '$preciotachadopc', '$preciotachadopn', '$diploma', '$convocatoria') RETURNING Currval('curso_id_seq');";	
		
		$link=iConectarse(); 
		$Query = pg_query($link, $ssql);// or die ("E1".mysql_error()); 

		$insert_row = pg_fetch_row($Query); //id ultima insertada
		$idcurso=$insert_row[0]; // RETURNING Currval('curso_id_seq')
		if ($idcurso>0){
		   $datos_curso = array ( 
                        "nombre_largo" => $nombre,
                        "nombre_corto" => $nombre,
                        "fecha_inicio" => strtotime($fecha_inicio),
                        "id_curso_externo" => $idcurso,
                        "id_categoria" => $id_categoria_moodle
                        );
		  	if ($id_curso_plantilla==""){ //Puede estar vacio ya que quiere duplicar curso
				if ($idmoodleduplica<>""){ //duplica ese curso de moodle
					$id_curso_plantilla = $idmoodleduplica;
				}else{
					$id_curso_plantilla = 9; //para crear en moodle algun curso
				}
			}
			
			if ($idmoodleduplica<>""){ //duplica ese curso de moodle
				$id_curso_plantilla = $idmoodleduplica;
			}
			
		   /*
			> Plantilla curso permanente		http://www.activatie.org/moodle/course/view.php?id=9
			> Plantilla curso online		http://www.activatie.org/moodle/course/view.php?id=76
			> Plantilla vacía		http://www.activatie.org/moodle/course/view.php?id=77
		   */
		   //echo $id_curso_plantilla;
		 // var_dump($datos_curso);
		 //  exit();

		   $clonado = clona_curso_moodle($id_curso_plantilla, $datos_curso);
			if ($clonado>0) {
				$ssql="UPDATE curso SET  idmoodle = '$clonado'  WHERE id='$idcurso' AND borrado='0' ;";	
				$Query = pg_query($link, $ssql);// or die ("E1".mysql_error());  
				$id=$_SESSION[idcolegio];
				$idusuariomoodle=get_iduser_moodle($id);
				$idcursomoodle=$clonado;
				$resultado = matricula_usuario_curso($idcursomoodle,$idusuariomoodle,3); //matricula profeeditor
				if ($resultado==1){				
					$sqlacc="INSERT INTO curso_usuario (borrado,enmoodle,idusuario,idcurso,idmoodle,nivel,rol) VALUES ('0','1','$id','$idcurso','$idcursomoodle','3','3')";
					$Query = posgre_query($sqlacc);  // or die (mysql_error())
					$_SESSION[error]="Se ha insertado correctamente.";
				}else{
					$_SESSION[error]="Se ha insertado correctamente. Pero no tiene permiso de editar el curso en Moodle".$idcursomoodle.":".$idusuariomoodle;
				}
				$est="ok";
			}else{
				$_SESSION[error]="No se ha clonado correctamente: ".$id_curso_plantilla;
				$est="ko";
			}
				
			//copiamos tablas 2/////////////////////////////////////////////////////////////////////////
			$idcopia=strip_tags($_REQUEST['idcopia']); 
			if ($idcopia>0){
				$linkc=iConectarse();
				//Tabla curso_horario
					$link2=iConectarse(); 
					$result2=pg_query($link2,"SELECT * FROM curso_horario WHERE idcurso='$idcopia' AND borrado=0 ORDER BY fecha; ");// or die (mysql_error());  
					if ($result2){
						while($row2= pg_fetch_array($result2)) {								
							$hora=$row2["hora"];
							$horafin=$row2["horafin"];
							$fecha=($row2["fecha"]);
							$diasemana=$row2["diasemana"];
							if ($hora<>""){
								$sqlic="INSERT INTO curso_horario (hora,horafin,fecha,idcurso,idusuario,diasemana) VALUES ('$hora','$horafin','$fecha','$idcurso','$idcolegio','$diasemana')";		
								$Query =pg_query($linkc,$sqlic);
								$_SESSION[esterror].="Horario ok.";
							}
						}
					}
				//tabla profes
					$consulta = "SELECT u.id AS idFROM curso_docente_web AS c,usuario AS u WHERE c.idusuario=u.id AND c.idcurso='$idcopia' AND c.borrado = 0 AND u.borrado = 0 ORDER BY u.apellidos;";
					$result2=pg_query($link2,$consulta) ;//or die (pg_error());  
					while($row = pg_fetch_array($result2)) { 
						$idusuario=$row["id"];
						if ($idusuario<>''){
							$Query = pg_query($linkc,"INSERT INTO curso_docente_web (idcurso,idusuario) VALUES ('$idcurso','$idusuario');" );// or die (mysql_error()); 
								$_SESSION[esterror].="Docenteweb ok.";
						}
					}
				//tabla etiquetas
				   $result2=pg_query($link2,"SELECT etiqueta.tipo as tipo,etiqueta.texto as texto, etiqueta.id as id FROM etiqueta,curso_etiqueta WHERE curso_etiqueta.idcurso='$idcopia' AND etiqueta.id=curso_etiqueta.idetiqueta AND etiqueta.borrado=0;") ;//or die ("Erro_".mysql_error()); 
					while($row = pg_fetch_array($result2)) { 
						$idetiqueta=$row["id"];
						$Query = pg_query($linkc,"INSERT INTO curso_etiqueta (idcurso,idetiqueta) VALUES ('$idcurso','$idetiqueta')");// or die (mysql_error()); 
						$_SESSION[esterror].="Etiquetas ok.";
				}
				//Tablas de adjuntos
					//fotos
				   $result2=pg_query($link2,"SELECT * FROM foto WHERE padre='$idcopia' AND borrado=0;") ;//or die ("Erro_".mysql_error()); 
					while($row = pg_fetch_array($result2)) { 
						$foto=$row["foto"];
						$comentario=$row["comentario"];
						$idcolegio=$row["idcolegio"];
						$Query = pg_query($linkc,"INSERT INTO foto (foto,comentario,padre,tipo,idcolegio) VALUES ('$foto','$comentario','$idcurso','curso','$idcolegio')" ) ;//or die (mysql_error()); 
						$_SESSION[esterror].="Fotos ok.";
					}
					//archivos
				   $result2=pg_query($link2,"SELECT * FROM archivo WHERE padre='$idcopia' AND borrado=0;") ;//or die ("Erro_".mysql_error()); 
					while($row = pg_fetch_array($result2)) { 
						$archivo=$row["archivo"];
						$nombre=$row["nombre"];
						$idcolegio=$row["idcolegio"];
						$Query = pg_query($linkc,"INSERT INTO archivo (archivo,nombre,padre,idcolegio) VALUES ('$archivo','$nombre','$idcurso','$idcolegio')" );// or die (mysql_error()); 
						$_SESSION[esterror].="Archivos ok.";
					}
					//videos
				   $result2=pg_query($link2,"SELECT * FROM video WHERE padre='$idcopia' AND borrado=0;") ;//or die ("Erro_".mysql_error()); 
					while($row = pg_fetch_array($result2)) { 
						$codigo=$row["codigo"];
						$nombre=$row["nombre"];
						$idcolegio=$row["idcolegio"];
						$Query = pg_query($linkc,"INSERT INTO video (codigo,nombre,padre,idcolegio) VALUES ('$codigo','$nombre','$idcurso','$idcolegio')"); 
						$_SESSION[esterror].="Videos ok.";
					}
			
				//Actualizamos curso con video principal e imagen
					//Repito esto para tenerlo cerca
					$result2=pg_query($link2,"SELECT * FROM curso WHERE $ssql id='$idcopia' AND borrado=0;");// or die (pg_error());
					if (pg_num_rows($result2)!=0){
						$row = pg_fetch_array($result2);
						$codigo=$row["video"];
						$imagen=$row["imagen"];
						$imagen2=$row["imagen2"];
						//exit();
						$Query = pg_query($linkc,"UPDATE curso SET imagen2='$imagen2',imagen='$imagen',video='$codigo' WHERE $sql id='$idcurso'");// or die (mysql_error());  	
						$_SESSION[esterror].="Imagenes1 y video1 ok.";
					}
					//video principal e imagen
			}
			//fin copiamos tablas 2/////////////////////////////////////////////////////////////////////////
			
			include("__rss2.0.php");
			include("_rss2.0_curso.php");
		}else{
			echo pg_last_error();
			exit();
			$_SESSION[error]="No se ha insertado correctamente.2";
			$est="ko";
			header("Location: curso_alta.php?est=$est"); 
			exit();
		}
	}else{
		$_SESSION[error]="No se ha podido insertar.";
		$est="ko";
		header("Location: curso_alta.php?est=$est"); 
		exit();
	}
	//Para la cache del navegador al volver atras
	//meter en sesion los datos POST
	header("Location: zona-privada_admin_cursos_1.php?est=$est"); 
	//echo $est.$est_texto.$est_texto2;
	exit();
	
}//Fin de accion==guardar


if($accion=="guardarm"){
	$accion="";
	$est="ok";
	/*if (comprobar_email($email)==0) { //si el emial es incorrecto
		$est_texto="* E-mail incorrecto";
		$est="ko";
	}*/
	//fechas update
		$fecha_fin_inscripcion=cambiaf_a_mysql($_POST['fecha_fin_inscripcion']);
		if (($fecha_fin_inscripcion<>"")){
			$curso["fecha_fin_inscripcion"]=$fecha_fin_inscripcion;			
			$sqlfechaC="fecha_fin_inscripcion='$fecha_fin_inscripcion',";
		}
		$fecha_inicio=cambiaf_a_mysql($_POST['fecha_inicio']);
		if (($fecha_inicio<>"")){
			$curso["fecha_inicio"]=$fecha_inicio;			
			$sqlfechaC=$sqlfechaC."fecha_inicio='$fecha_inicio',";
		}
		$fecha_fin=cambiaf_a_mysql($_POST['fecha_fin']);
		if (($fecha_fin<>"")){
			$curso["fecha_fin"]=$fecha_fin;			
			$sqlfechaC=$sqlfechaC."fecha_fin='$fecha_fin',";
		}
		$fecha_publicacion=cambiaf_a_mysql($_POST['fecha_publicacion']);
		if (($fecha_publicacion<>"")){
			$curso["fecha_publicacion"]=$fecha_publicacion;			
			$sqlfechaC=$sqlfechaC."fecha_publicacion='$fecha_publicacion',";
		}
		$fecha_fin_publicacion=cambiaf_a_mysql($_POST['fecha_fin_publicacion']);
		if (($fecha_fin_publicacion<>"")){
			$curso["fecha_fin_publicacion"]=$fecha_fin_publicacion;			
			$sqlfechaC=$sqlfechaC."fecha_fin_publicacion='$fecha_fin_publicacion',";
		}
	//fin fechas
	$id=strip_tags($_REQUEST['id']);
	if (($est=="ok")&&($id<>"")){
		//comprobamos plazas para en envio de pago a los que esten en lista de espera
		//include("_ay_curso_aumenta_plazas.php");
		//$ssql="UPDATE curso SET  $sqlfechaC nombre = '$nombre',observaciones='$observaciones', presentacion = '$presentacion', duracion = '$duracion', modalidad = '$modalidad', estado = '$estado', permanente='$permanente' , precioc = '$precioc', precion = '$precion', precioe = '$precioe', preciop = '$preciop', precioco = '$precioco', preciono = '$preciono', precioeo = '$precioeo', preciopo = '$preciopo', preciocp = '$preciocp', precionp = '$precionp', precioep = '$precioep', preciopp = '$preciopp', lugar = '$lugar', ponentes = '$ponentes', horariosyfechas = '$horariosyfechas', tipo = '$tipo', iva='$iva',plazas = '$plazas',plazaso = '$plazaso', plazasperma = '$plazasperma', idcolegio = '$idcolegio' WHERE id ='$id';";	

		$nombre = pg_escape_string($nombre);
		$ssql="UPDATE curso SET  $sqlfechaC nombre = '$nombre',observaciones='$observaciones', presentacion = '$presentacion',programa = '$programa', duracion = '$duracion', estado = '$estado', permanente='$permanente' , precioc = '$precioc', precion = '$precion', precioe = '$precioe', preciop = '$preciop', precioco = '$precioco', preciono = '$preciono', precioeo = '$precioeo', preciopo = '$preciopo', preciocp = '$preciocp', precionp = '$precionp', precioep = '$precioep', preciopp = '$preciopp', lugar = '$lugar', ponentes = '$ponentes', horariosyfechas = '$horariosyfechas', tipo = '$tipo', iva='$iva',plazas = '$plazas',plazaso = '$plazaso', plazasperma = '$plazasperma', idcolegio = '$idcolegio', plazopermanente='$plazopermanente', privado='$privadocolegiados', duracionminutos='$duracionminutos', preciotachadoc='$preciotachadoc', preciotachadon='$preciotachadon' , preciotachadooc='$preciotachadooc', preciotachadoon='$preciotachadoon',preciotachadopc='$preciotachadopc' ,preciotachadopn='$preciotachadopn', diploma='$diploma', convocatoria='$convocatoria' WHERE id ='$id';";	
		$link=iConectarse(); 

		$Query = pg_query($link, $ssql);// or die ("E1".mysql_error());  

		if ($Query){
			$_SESSION[error]="Se ha editado correctamente.";
			$est="ok";
			include("__rss2.0.php");
			include("_rss2.0_curso.php");
			
		}else{
			$_SESSION[error]="No se ha guardado.";
			$est="ko";
		}
	}else{
		$_SESSION[error]="No se ha podido insertar.";
		$_SESSION[curso]=$curso;	
		$est="ko";
	}
	//Para la cache del navegador al volver atras
	//meter en sesion los datos POST
	header("Location: zona-privada_admin_cursos_1.php?est=$est"); 
	exit();
	
}//Fin de accion==guardar

if($accion==""){
	$ti="Alta";
}elseif($accion=="copiar"){
	$ti="Duplicar";
}elseif($accion=="editar"){
	$ti="Editar";
}

$titulo1="formación ";
$titulo2="administración";

$migas = array();
$migas[] = array('zona-privada_admin_cursos_1.php', 'Gestión de Cursos');
include("plantillaweb01admin.php"); 
$id=strip_tags($_REQUEST['id']);
?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" /> 
<script src="https://code.jquery.com/jquery-1.9.1.js"></script> 
<script src="https://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script> 
$(function() { 
	$("#fecha1").datepicker( { dateFormat: "dd/mm/yy", dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ], dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ], firstDay: 1, gotoCurrent: true, monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Deciembre" ] } ); 
	$("#fecha2").datepicker( { dateFormat: "dd/mm/yy", dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ], dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ], firstDay: 1, gotoCurrent: true, monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Deciembre" ] } ); 
	$("#fecha3").datepicker( { dateFormat: "dd/mm/yy", dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ], dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ], firstDay: 1, gotoCurrent: true, monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Deciembre" ] } ); 
	$("#fecha4").datepicker( { dateFormat: "dd/mm/yy", dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ], dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ], firstDay: 1, gotoCurrent: true, monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Deciembre" ] } ); 
	$("#fecha5").datepicker( { dateFormat: "dd/mm/yy", dayNames: [ "Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado" ], dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ], firstDay: 1, gotoCurrent: true, monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Deciembre" ] } ); 
}); 
</script>
<script>
$(function() { 
	$("#guardardatoscurso").on( "click", function() {
		$('#guardardatoscurso').hide(); 
		setTimeout(function(){
			$('#guardardatoscurso').show(); 
		}, 2000);
	});
}); 
</script>



 
			<!--.Arriba pantilla1.-->
		<div class="grid-9 contenido-principal">
				<div class="clearfix"></div>
				<div class="pagina blog">
				<?
				if($id<>""){ ?>
					<div class="bloque-lateral acciones">		
								<p><strong>Acciones:</strong>
									<a href="etiqueta_inserta.php?idcurso=<?=$id?>" class="btn btn-success">Áreas <i class="icon-th"></i></a>
									<a href="curso_alta_mas.php?id=<?=$id?>" class="btn btn-success" type="button">Multimedia <i class="icon-film"></i></a> 
									<a href="asignar_docente_web.php?idcurso=<?=$id?>&idmoodle=<?=$idmoodle?>" class="btn btn-success" type="button">Docentes <i class="icon-user"></i></a> 
									<a href="curso_horarios.php?idcurso=<?=$id?>" class="btn btn-success" type="button">Horarios <i class="icon-plus"></i></a>  
									<a href="cursos_gastos.php?idcurso=<?=$id?>" class="btn btn-success" type="button">Gastos y devoluciones <i class="icon-plus"></i></a> 
									<a href="curso_alta.php?accion=copiar&id=<?=$id?>&idmoodleduplica=<?=$idmoodleduplica?>" class="btn btn-default" type="button">Duplicar Curso <i class="icon-plus"></i></a> 
								</p>
					</div>
					<!--fin acciones-->
					<?
				}?>
				<? if ($est=="ko"){ ?> 
						<? include("_aya_mensaje_session.php"); ?>
						<br />
						<?
						$curso=$_SESSION[curso];	
						$nombre=$curso["nombre"];
						$idmoodle=$curso["idmoodle"];
						$presentacion=br2nl($curso["presentacion"]);						
						$observaciones=br2nl($curso["observaciones"]);						
						$programa=br2nl($curso["programa"]);						
						$duracion=$curso["duracion"];
						$estado=$curso["estado"];
						$permanente=$curso["permanente"];
						$fecha_fin_publicacion=cambiaf_a_normal($curso["fecha_fin_publicacion"]);
						$fecha_publicacion=cambiaf_a_normal($curso["fecha_publicacion"]);
						$fecha_fin_inscripcion=cambiaf_a_normal($curso["fecha_fin_inscripcion"]);
						$fecha_inicio=cambiaf_a_normal($curso["fecha_inicio"]);
						$fecha_fin=cambiaf_a_normal($curso["fecha_fin"]);
						$precioc=$curso["precioc"];
						$precion=$curso["precion"];
						$precioe=$curso["precioe"];
						$preciop=$curso["preciop"];//
						$precioco=$curso["precioco"];
						$preciono=$curso["preciono"];
						$precioeo=$curso["precioeo"];
						$preciopo=$curso["preciopo"];//
						$preciocp=$curso["preciocp"];
						$precionp=$curso["precionp"];
						$precioep=$curso["precioep"];
						$preciopp=$curso["preciopp"];//
						
						/** Precio tachados **/
						$preciotachadoc=$curso["preciotachadoc"]; if ($preciotachadoc=="") $preciotachadoc=0;
						$preciotachadon=$curso["preciotachadon"]; if ($preciotachadon=="") $preciotachadon=0;
						$preciotachadooc=$curso["preciotachadooc"]; if ($preciotachadooc=="") $preciotachadooc=0;
						$preciotachadoon=$curso["preciotachadoon"]; if ($preciotachadoon=="") $preciotachadoon=0;
						$preciotachadopc=$curso["preciotachadopc"]; if ($preciotachadopc=="") $preciotachadopc=0;
						$preciotachadopn=$curso["preciotachadopn"]; if ($preciotachadopn=="") $preciotachadopn=0;
						/** **/
						
						$lugar=br2nl($curso["lugar"]);						
						$ponentes=br2nl($curso["ponentes"]);
						$horariosyfechas=br2nl($curso["horariosyfechas"]);
						$tipo=$curso["tipo"];
						$iva=$curso["iva"];
						$plazas=$curso["plazas"];
						$plazaso=$curso["plazaso"];
						$plazasperma=$curso["plazasperma"];
						$modalidad=$curso["modalidad"];
						$idcolegio=$curso["idcolegio"];
						$iddocente=$curso["iddocente"];
						$ref=$curso["id"]."/".$curso["id_categoria_moodle"];
						$id_curso_plantilla=$curso["id_curso_plantilla"];
						$plazopermanente=$curso["plazopermanente"];
						$duracionminutos=$curso["duracionminutos"];
						$convocatoria=$curso["convocatoria"];
						$privadocolegiados=$curso["privado"];
						if ($privadocolegiados==1){
							$checkedprivado=" checked ";
							$hiddenprecio = " style='display:none;'";
						}
						else{
							$checkedprivado="";
							$hiddenprecio = "";
						}
						
						$diploma=$curso["diploma"];
						if ($diploma==1){
							$checkeddiploma=" checked ";
						}
						else{
							$checkeddiploma="";
						}

				} //fin  if ($est=="ko"){ 				
				?>
				<? if ($est=="ok"){ ?> 
						<? include("_aya_mensaje_session.php"); ?>
				<? }
					if(($accion=="editar")||($accion=="copiar")){
							if ($id<>"") {
								$link=iConectarse(); 
							  	$result=pg_query($link,"SELECT * FROM curso WHERE borrado=0 AND id='$id' ORDER BY id DESC LIMIT 1;"); 
								$curso = pg_fetch_array($result);						
								//$i=$row["id"];
								$nombre=$curso["nombre"];
								$idmoodle=$curso["idmoodle"];
								$presentacion=br2nl($curso["presentacion"]);
								$observaciones=br2nl($curso["observaciones"]);	
								$programa=br2nl($curso["programa"]);					
								$duracion=$curso["duracion"];
								$estado=$curso["estado"];
								$permanente=$curso["permanente"];
								$fecha_fin_inscripcion=cambiaf_a_normal($curso["fecha_fin_inscripcion"]);
								$fecha_inicio=cambiaf_a_normal($curso["fecha_inicio"]);
								$fecha_fin=cambiaf_a_normal($curso["fecha_fin"]);
								$fecha_publicacion=cambiaf_a_normal($curso["fecha_publicacion"]);
								$fecha_fin_publicacion=cambiaf_a_normal($curso["fecha_fin_publicacion"]);
								$precioc=$curso["precioc"];
								$precion=$curso["precion"];
								$precioe=$curso["precioe"];
								$preciop=$curso["preciop"];//
								$precioco=$curso["precioco"];
								$preciono=$curso["preciono"];
								$precioeo=$curso["precioeo"];
								$preciopo=$curso["preciopo"];//
								$preciocp=$curso["preciocp"];
								$precionp=$curso["precionp"];
								$precioep=$curso["precioep"];
								$preciopp=$curso["preciopp"];//
								
								/** Precio tachados **/
								$preciotachadoc=$curso["preciotachadoc"]; if ($preciotachadoc=="") $preciotachadoc=0;
								$preciotachadon=$curso["preciotachadon"]; if ($preciotachadon=="") $preciotachadon=0;
								$preciotachadooc=$curso["preciotachadooc"]; if ($preciotachadooc=="") $preciotachadooc=0;
								$preciotachadoon=$curso["preciotachadoon"]; if ($preciotachadoon=="") $preciotachadoon=0;
								$preciotachadopc=$curso["preciotachadopc"]; if ($preciotachadopc=="") $preciotachadopc=0;
								$preciotachadopn=$curso["preciotachadopn"]; if ($preciotachadopn=="") $preciotachadopn=0;
								/** **/
						
								$lugar=br2nl($curso["lugar"]);						
								$ponentes=br2nl($curso["ponentes"]);
								$horariosyfechas=br2nl($curso["horariosyfechas"]);
								$tipo=$curso["tipo"];
								$iva=$curso["iva"];
								$plazas=$curso["plazas"];
								$plazaso=$curso["plazaso"];
								$plazasperma=$curso["plazasperma"];
								$modalidad=$curso["modalidad"];
								$idcolegio=$curso["idcolegio"];
								$idcursomoodle=$curso["idmoodle"];
								$idcurso=$curso["id"];
								$plazopermanente=$curso["plazopermanente"];
								$duracionminutos=$curso["duracionminutos"];
								$convocatoria=$curso["convocatoria"];
								$privadocolegiados=$curso["privado"];
								if ($privadocolegiados==1){
									$checkedprivado=" checked ";
									$hiddenprecio = " style='display:none;'";
								}
								else{
									$checkedprivado="";
									$hiddenprecio = "";
								}
								
								$diploma=$curso["diploma"];
								if ($diploma==1){
									$checkeddiploma=" checked ";
								}
								else{
									$checkeddiploma="";
								}
								
								//$idmoodle=$curso["idmoodle"];
								$ref=$curso["id"]."/".$curso["id_categoria_moodle"];
							}else{
								echo"Parametros incorrectos";
								exit();							
							}		
					}
				?>
				<h2><?=$ti?> Curso</h2>
					<?
					if(($accion=="editar")&&($id<>"")){ 
						?>
						<form action="curso_alta.php?accion=guardarm&id=<?=$id?>" method="post" enctype="multipart/form-data">
						<?
					}elseif(($accion=="copiar")&&($id<>"")){ {
						?>	
						<form action="curso_alta.php?accion=guardar&idmoodleduplica=<?=$idmoodleduplica?>&idcopia=<?=$id?>" method="post" enctype="multipart/form-data">
						<?
					}
					}else{
						?>	
						<form action="curso_alta.php?accion=guardar" method="post" enctype="multipart/form-data">
						<?
					}
					if ($fecha_fin_inscripcion=="NULL") $fecha_fin_inscripcion=""; 
					if ($fecha_inicio=="NULL") $fecha_inicio=""; 
					if ($fecha_publicacion=="NULL") $fecha_publicacion=""; 
					if ($fecha_fin_publicacion=="NULL") $fecha_fin_publicacion=""; 
					?>
											   
						<fieldset>				    
							<? if ($est=="ko"){ ?><span class="rojo">Error</span><? }?>

							
							<?
							if(($accion=="editar")&&($id<>"")){ 
								?>
								<br>
								<div class="control-group">
									<label class="control-label" for="inputName">Referencia:</label>
										<div class="controls">
											<input type="text" id="inputName" class="input-large" disabled="disabled" value="<?=$ref?>"  />
										</div>
								</div>
								<?
							}?>
							<br><h3>Datos del curso</h3>
							<div class="control-group">
								<label class="control-label" for="14">Tipo:</label>
									<div class="controls">
										<select name="tipo" class="input-xlarge" >
											<option class="input-xlarge" value="0" <? if ($tipo==0) echo " selected "; ?>>Curso</option>
											<option class="input-xlarge" value="1" <? if ($tipo==1) echo " selected "; ?>>Curso universitario</option>
											<option class="input-xlarge" value="2" <? if ($tipo==2) echo " selected "; ?>>Taller</option>
											<option class="input-xlarge" value="3" <? if ($tipo==3) echo " selected "; ?>>Seminario</option>
											<option class="input-xlarge" value="4" <? if ($tipo==4) echo " selected "; ?>>Jornada</option>
										</select>
									</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="14">Convocatoria:</label>
									<div class="controls">
										<select name="convocatoria" class="input-xlarge" >
											<option class="input-xlarge" value="0" <? if ($convocatoria==0) echo " selected "; ?>></option>
											<option class="input-xlarge" value="1" <? if ($convocatoria==1) echo " selected "; ?>>Cíclico</option>
											<option class="input-xlarge" value="2" <? if ($convocatoria==2) echo " selected "; ?>>Nuevo</option>
											<option class="input-xlarge" value="3" <? if ($convocatoria==3) echo " selected "; ?>>Aplazado</option>
											<option class="input-xlarge" value="4" <? if ($convocatoria==4) echo " selected "; ?>>Nuevo aplazado</option>
										</select>
									</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="inputName">Nombre del curso:</label>
									<div class="controls">
										<input required type="text" id="inputName" class="input-xxlarge" name="nombre" value='<?=$nombre?>' />
									</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="inputName">Presentación:</label>
									<div class="controls">
										<textarea required  name="presentacion" id="observaciones" class="inputtextarea input-xxlarge" cols="45" rows="10" ><?=$presentacion?></textarea>
									</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="inputName">Programa(solo para el reverso del diploma). No incluir ponentes(se incluyen aparte):</label>
									<div class="controls">
										<textarea name="programa" id="programa" class="inputtextarea input-xxlarge" cols="45" rows="10" ><?=$programa?></textarea>
									</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="1">Duración:</label>
									<div class="controls">
										<input type="number" id="1" min="0" class="input-mini" name="duracion" value="<?=$duracion?>" /> horas
										<input type="number" id="duracionminutos" min="0" class="input-mini" name="duracionminutos" value="<?=$duracionminutos?>" /> minutos
									</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="11">Lugar:</label>
									<div class="controls">
										<textarea name="lugar" id="lugar" class="inputtextarea input-xxlarge" cols="45" rows="1" ><?=$lugar?></textarea>
									</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="observaciones">Observaciones:</label>
									<div class="controls">
										<textarea name="observaciones" id="observaciones" class="inputtextarea input-xxlarge" cols="45" rows="3" ><?=$observaciones?></textarea>
									</div>
							</div>
							<hr>
							<br>
							
							<h3>Configuración del curso</h3>
							<? if ($_SESSION[nivel]==1) { //Admin Total ?>
								<div class="control-group">
									<label class="control-label" for="18">Colegio profesional:</label>
										<div class="controls">
											<select name="idcolegio" class="input-xlarge" >
											<?
											// Generar listado 
												$consulta = "SELECT * FROM usuario WHERE nivel='2' AND borrado = 0 ORDER BY nombre;";
												$link=iConectarse(); 
												$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
												while($rowdg= pg_fetch_array($r_datos)) {	
													?>
													<option class="input-xlarge" value="<?=$rowdg['id']?>"<? if  ($idcolegio == $rowdg['id']) { echo " selected "; } ?>><? echo ($rowdg['nombre']); ?></option>
													<? 
												} ?>
										  </select>
									</div>
								</div>
							
							<? }?>
							<div class="control-group">
								<label class="control-label" for="17">Estado:</label>
									<div class="controls">
										<select name="estado" class="input-xlarge" >
											<option class="input-large" value="2" <? if ($estado==2) echo " selected "; ?>>ABIERTO(Precurso, En curso, Finalizado)</option>
											<option class="input-large" value="0" <? if ($estado==0) echo " selected "; ?>>OCULTO</option>
											<option class="input-large" value="1" <? if ($estado==1) echo " selected "; ?>>CERRADO (se visualiza pero no permite inscripción)</option>
											<option class="input-large" value="5" <? if ($estado==5) echo " selected "; ?>>CANCELADO</option>
										</select>
									</div>
									<br>
									<div class="controls">
										<input <?=$checkeddiploma?> id="diploma"  type="checkbox" name="diploma" value="diploma" /><span > Diploma (<span style="font-size:10px">Criterios expedición diploma: Cursos de más de un día o de un solo día de pago)</span></span>
									</div>
									<br>
									<div class="controls">
										<input <?=$checkedprivado?> id="privadocolegiados"  type="checkbox" name="privadocolegiados" value="privadocolegiados" /><span > Privado (solo permite inscribirse a usuarios colegiados)</span>
									</div>
							</div><br>
							
							<?
							if($accion!=="editar"){ 
							?>
								<div class="control-group">
									<label class="control-label" for="15">Modalidad:</label>
										<div class="controls">
											<select id="select_modalidad" name="modalidad" class="input-xlarge" >
												<option class="input-large" value="0" <? if ($modalidad==0) echo " selected "; ?>>on-line</option>
												<option class="input-large" value="1" <? if ($modalidad==1) echo " selected "; ?>>presencial</option>
												<option class="input-large" value="2" <? if ($modalidad==2) echo " selected "; ?>>presencial y on-line</option>
												<option class="input-large" value="3" <? if ($modalidad==3) echo " selected "; ?>>permanente</option>
											</select>
										</div>
								</div>
							<?
							} 
							
							if(($accion<>"editar")&&($id=="")){ 
								?>
								<div class="control-group">
									<label class="control-label" for="id_curso_plantilla">Plantilla de Curso MOODLE:</label>
										<div class="controls">
											<select name="id_curso_plantilla" class="input-xlarge" >
												<option class="input-large" value="76" <? if ($id_curso_plantilla==76) echo " selected "; ?>>Plantilla curso corto</option>
												<option class="input-large" value="77" <? if ($id_curso_plantilla==77) echo " selected "; ?>>Plantilla jornada</option>
												<option class="input-large" value="9" <? if ($id_curso_plantilla==9) echo " selected "; ?>>Plantilla curso permanente</option>
											</select>
										</div>
								</div>
								<?
							}
							?>
							<br><div <? if ($modalidad==3) echo "hidden"?> id="fechascurso" class="control-group">
								<div class="controls">
								
									<span class="control-label" for="2">Fecha inicio del curso: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
									<input id="fecha1"  type="text" class="input-small" name="fecha_inicio" value="<?=$fecha_inicio?>"  />
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<span class="control-label" for="d2">Fecha fin del curso: &nbsp;&nbsp;&nbsp;</span>
									<input id="fecha2"  type="text" class="input-small" name="fecha_fin" value="<?=$fecha_fin?>" />
									&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									<span hidden class="control-label" for="3">Fecha fin inscripción: &nbsp;&nbsp;</span>
									<input style="visibility:hidden" id="fecha3"  type="text" class="input-small" name="fecha_fin_inscripcion" value="<?=$fecha_fin_inscripcion?>" />
																
								</div>
							</div>
							<div class="control-group">
								<span class="control-label" for="4">Fecha inicio publicación: &nbsp;&nbsp;</span>
								<input required id="fecha4"  type="text" class="input-small" name="fecha_publicacion" value="<?=$fecha_publicacion?>" />
								&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
								<span class="control-label" for="5">Fecha fin inscripción (y publicación en próximas actividades): &nbsp;&nbsp;</span>
								<input required id="fecha5"  type="text" class="input-small" name="fecha_fin_publicacion" value="<?=$fecha_fin_publicacion; //suma_fechas($fecha,60);?>" />
							
							</div>
							<? /*
							<div class="control-group">
								<label class="control-label" for="12">Ponentes/Docentes: 
									<?
									if(($accion=="editar")&&($id<>"")){ 
										?>
										<a href="asignar_docente_web.php?idcurso=<?=$idcurso?>&idmoodle=<?=$idcursomoodle?>">[+]</a></label>
										<?
									} 
									?>
									<div class="controls"><ul>
										<?
									// Genera
										$consultadg = "SELECT u.nombre AS nombre, u.apellidos AS apellidos FROM curso_docente_web AS c,usuario AS u WHERE c.idusuario=u.id AND c.idcurso='$id' AND c.borrado = 0 AND u.borrado = 0 ORDER BY u.apellidos;";
										$linkdg=iConectarse(); 
										$r_datosdg=pg_query($linkdg,$consultadg);// or die (mysql_error());  
										while($rowdg = pg_fetch_array($r_datosdg)) { 
											echo "<li>".$rowdg['nombre']." ".$rowdg['apellidos']."</li>";
										}
										?>
										</ul>
									</div>
									<div class="controls">Otros:<br />
										<textarea name="ponentes" id="ponentes" class="inputtextarea input-xxlarge" cols="45" rows="3" ><?=$ponentes?></textarea>
									</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="13">Horarios y fechas:
									<?
									if(($accion=="editar")&&($id<>"")){ 
										?>
										<a href="curso_horarios.php?idcurso=<?=$idcurso?>">[+]</a></label>
										<?
									} 
									?>
									<div class="controls">
										
										<?
										$link2=iConectarse(); 
										$result2=pg_query($link2,"SELECT * FROM curso_horario WHERE $ssql idcurso='$idcurso' AND borrado=0;");// or die (mysql_error());  
										if ($result2){
											?><div>Fecha y hora</div><?
											while($row2= pg_fetch_array($result2)) {								
												?><ul>
													<li>
														<span class="actions">
														<?=cambiaf_a_normal($row2["fecha"])?> <?=$row2["hora"]?> 
														<a href="curso_horarios_hora.php?idcurso=<?=$idcurso?>&id=<?=$row2["id"]?>" ><i class="icon-edit"></i> Editar</a> &middot; 
														<a onclick="return confirmar('&iquest;Eliminar elemento?')" href="curso_horarios.php?accion=borrar&id=<?=$row2["id"]?>&idcurso=<?=$idcurso?>"><i class="icon-trash"></i> Eliminar</a>
														</span>
													</li>									
												</ul>
												<? 
											}
										}?>
										<textarea name="horariosyfechas" id="horariosyfechas" class="inputtextarea input-xxlarge" cols="45" rows="3" ><?=$horariosyfechas?></textarea>
									</div>
							</div>
							*/?>

							
							

	
		<div class="control-group">
		<table>

			<th style="text-align:left; font-size:16px;" <? if (($modalidad!=1)&&($modalidad!=2)) echo "hidden"; ?>  class="modo1">Modalidad Presencial</th>
			<th style="text-align:left; font-size:16px;" <? if (($modalidad!=0)&&($modalidad!=2)) echo "hidden"; ?>  class="modo0">Modalidad On-line</th>
			<th style="text-align:left; font-size:16px;" <? if ($modalidad!=3) echo "hidden"; ?> class="modo3">Modalidad Permanente</th>
		<tr >
			<td <? if (($modalidad!=1)&&($modalidad!=2)) echo "hidden"; ?> class="modo1">
				<table>
					<tr>
						<th bgcolor="#FFF" style="border:none; text-align:left;">Precio (obligatorio: poner a 0 para gratuito)</th>
						<th bgcolor="#FFF" style="border:none; text-align:left;">Precio TACHADO (opcional: dejar a 0 para no activar promoción)</th>
					</tr>
					<tr>
						<td width="50%" bgcolor="#FFF" style="border:none;">
							<div class="control-group">
								<label class="control-label" for="6">Precio Colegiado Presencial:</label>
									<div class="controls">
										<input type="number" id="6" min="0" class="input-mini" name="precioc" value="<?=$precioc?>" /> €
									</div>
							</div>
							<div class="control-group">
								<label <?=$hiddenprecio?>  class="control-label precionocolegiado" for="9">Precio No Colegiado Presencial:</label>
									<div class="controls">
										<input <?=$hiddenprecio?>  type="number" id="9" min="0" class="input-mini precionocolegiado" name="precion"  value="<?=$precion?>" /> 
										<span <?=$hiddenprecio?> class="precionocolegiado">€</span>
									</div>
							</div>
						
						</td>
				
						<td bgcolor="#FFF" style="border:none;">

							<div class="control-group">
								<label class="control-label" for="6">Precio TACHADO Colegiado Presencial:</label>
									<div class="controls">
										<input type="number" id="6" min="0" class="input-mini" name="preciotachadoc" value="<?=$preciotachadoc?>" /> €
									</div>
							</div>
							
							<div class="control-group">
								<label <?=$hiddenprecio?>  class="control-label precionocolegiado" for="9">Precio TACHADO No Colegiado Presencial:</label>
									<div class="controls">
										<input <?=$hiddenprecio?>  type="number" id="9" min="0" class="input-mini precionocolegiado" name="preciotachadon"  value="<?=$preciotachadon?>" /> 
										<span <?=$hiddenprecio?> class="precionocolegiado">€</span>
									</div>
							</div>
						</td>
					</tr>
				</table>
			</td>
			<td  <? if (($modalidad!=0)&&($modalidad!=2)) echo "hidden"; ?>  class="modo0">
				<table>
					<tr>
						<th bgcolor="#FFF" style="border:none; text-align:left;">Precio (obligatorio: poner a 0 para gratuito)</th>
						<th bgcolor="#FFF" style="border:none; text-align:left;">Precio TACHADO (opcional: dejar a 0 para no activar promoción)</th>
					</tr>
					<tr>
						<td width="50%" bgcolor="#FFF" style="border:none;">
							<div class="control-group">
								<label class="control-label" for="63">Precio Colegiado On-Line:</label>
									<div class="controls">
										<input type="number" id="63" min="0" class="input-mini" name="precioco" value="<?=$precioco?>" /> €
									</div>
							</div>
							
							<div class="control-group">
								<label <?=$hiddenprecio?>  class="control-label precionocolegiado" for="92">Precio No Colegiado On-Line:</label>
									<div class="controls">
										<input <?=$hiddenprecio?> type="number" id="92" min="0" class="input-mini precionocolegiado" name="preciono" value="<?=$preciono?>" />
										<span <?=$hiddenprecio?> class="precionocolegiado">€</span>
									</div>
							</div>
								
						</td>
				
						<td bgcolor="#FFF" style="border:none;">
							<div class="control-group">
								<label class="control-label" for="63">Precio TACHADO Colegiado On-Line:</label>
									<div class="controls">
										<input type="number" id="63" min="0" class="input-mini" name="preciotachadooc" value="<?=$preciotachadooc?>" /> €
									</div>
							</div>
							
							<div class="control-group">
								<label <?=$hiddenprecio?>  class="control-label precionocolegiado" for="92">Precio TACHADO No Colegiado On-Line:</label>
									<div class="controls">
										<input <?=$hiddenprecio?> type="number" id="92" min="0" class="input-mini precionocolegiado" name="preciotachadoon" value="<?=$preciotachadoon?>" />
										<span <?=$hiddenprecio?> class="precionocolegiado">€</span>
									</div>
							</div>
												
						</td>
					</tr>
				</table>						

			</td>
			<td  <? if ($modalidad!=3) echo "hidden"; ?>  class="modo3">
			<table>
					<tr>
						<th bgcolor="#FFF" style="border:none; text-align:left;">Precio (obligatorio: poner a 0 para gratuito)</th>
						<th bgcolor="#FFF" style="border:none; text-align:left;">Precio TACHADO (opcional: dejar a 0 para no activar promoción)</th>
					</tr>
					<tr>
						<td width="50%" bgcolor="#FFF" style="border:none;">
							<div class="control-group">
								<label class="control-label" for="163">Precio Colegiado Permanente:</label>
									<div class="controls">
										<input type="number" id="163" min="0" class="input-mini" name="preciocp" value="<?=$preciocp?>" /> €
									</div>
							</div>
							<div class="control-group">
								<label <?=$hiddenprecio?> class="control-label precionocolegiado" for="192">Precio No Colegiado Permanente:</label>
									<div class="controls">
										<input <?=$hiddenprecio?> type="number" id="192" min="0" class="input-mini precionocolegiado" name="precionp" value="<?=$precionp?>" /> 
										<span <?=$hiddenprecio?> class="precionocolegiado">€</span>
									</div>
							</div>
				
						</td>
				
						<td bgcolor="#FFF" style="border:none;">
							<div class="control-group">
								<label class="control-label" for="163">Precio TACHADO Colegiado Permanente:</label>
									<div class="controls">
										<input type="number" id="163" min="0" class="input-mini" name="preciotachadopc" value="<?=$preciotachadopc?>" /> €
									</div>
							</div>
							<div class="control-group">
								<label <?=$hiddenprecio?> class="control-label precionocolegiado" for="192">Precio TACHADO No Colegiado Permanente:</label>
									<div class="controls">
										<input <?=$hiddenprecio?> type="number" id="192" min="0" class="input-mini precionocolegiado" name="preciotachadopn" value="<?=$preciotachadopn?>" /> 
										<span <?=$hiddenprecio?> class="precionocolegiado">€</span>
									</div>
							</div>
						
						
						
						</td>
					</tr>
				</table>	
			</td>
			</tr>
			<tr>
				<td  <? if (($modalidad!=1)&&($modalidad!=2)) echo "hidden"; ?>  class="modo1">							
							<div class="control-group">
								<label class="control-label" for="16">Plazas Presencial:</label>
									<div class="controls">
										<input type="number" min="0" id="16" class="input-mini" name="plazas" value="<?=$plazas?>" />
									</div>
							</div>
				</td>
				<td  <? if (($modalidad!=0)&&($modalidad!=2)) echo "hidden"; ?>  class="modo0">							
							<div class="control-group">
								<label class="control-label" for="162">Plazas On-Line:</label>
									<div class="controls">
										<input type="number" min="0" id="162" class="input-mini" name="plazaso" value="<?=$plazaso?>" />
									</div>
							</div>
				</td>
				<td  <? if ($modalidad!=3) echo "hidden"; ?>  class="modo3">							
							<div class="control-group">
								<label class="control-label" for="362">Plazas Permanente:</label>
									<div class="controls">
										<input type="number" min="0" id="362" class="input-mini" name="plazasperma" value="<?=$plazasperma?>" />
									</div>
							</div>
				</td>

			</tr>
			<tr>
				<td  <? if ($modalidad!=3) echo "hidden"; ?>  class="modo3">							
					<div class="control-group">
						<label class="control-label" for="plazopermanente"><strong>Plazo de realización del curso permanente:</strong></label>
							<div class="controls">
								<input <? if ($modalidad!=3) echo "required"; ?>  type="number" min="0" id="plazopermanente" class="input-mini" name="plazopermanente" value="<?=$plazopermanente?>" /> días
							</div>
					</div>
				</td>
			</tr>
			</table>
		
		<!--<div class="control-group">
			<div class="control-group">
				<label class="control-label" for="6">I.V.A.:</label>
					<div class="controls">
						<input align="right" type="number" min="0" id="62" class="input-mini" name="iva" value="<?=$iva?>" /> %
			</div>
		</div>-->
			
		</div>
							


							<? /*	<div class="control-group">
									<label class="control-label" for="19">Docente:</label>
										<div class="controls">
											<select name="iddocente" class="input-xlarge" >
											<option class="input-large" value="0"<? if  (($iddocente == "")||($iddocente == "0")) { echo " selected "; } ?>>[sin asignar]</option>
											<?
											// Generar listado 
												$consulta = "SELECT * FROM usuario WHERE nivel=3 AND borrado = 0 ORDER BY id;";
												$link=iConectarse(); 
												$r_datos=pg_query($link,$consulta);// or die (mysql_error());  
												while($rowdg= pg_fetch_array($r_datos)) {	
													?>
													<option class="input-large" value="<?=$rowdg['id']?>"<? if (($iddocente == $rowdg['id'])&&($iddocente <> "")) { echo " selected "; } ?>><? echo ($rowdg['nombre']); ?></option>
													<? 
												} ?>
										  </select>
										  
										</div>
								</div>
								<div class="control-group">
									<label class="control-label" for="20">Nivel:</label>
										<div class="controls">
											<select name="nivel" class="input-xlarge" >
												<option class="input-xlarge" value="0">[sin acceso]</option>
												<option class="input-xlarge" value="1">Administrador Total</option>
												<option class="input-xlarge" value="2">Administrador Colegio</option>
												<option class="input-xlarge" value="3">Profesor</option>
												<option class="input-xlarge" value="4">Alumno</option>
											</select>
										</div>
								</div>*/?>
							
						</fieldset>
						<div class="form-actions">
							<? if ($accion=="editar") { $textboton="Guardar cambios";} else{ $textboton="Guardar y clonar plantilla en Moodle";}?>
							<button id="guardardatoscurso" type="submit" class="btn btn-primary btn-large"><?=$textboton?></button>
						</div>
						</form>
				</div>
				<!--fin pagina blog-->
				<div class="clearfix"></div>
			</div>
			<!--fin grid-8 contenido-principal-->
			<!--Abajo plantilla2-->
	<?
include("plantillaweb02admin.php"); 
?>
<?php

/**
 * Libreria de wrapper/cliente para acceso a funcionalidades de moodle desde 
 *	aplicaciones externas php
 * 
 * Atencion: este fichero no forma parte de moodle
 *
 * Desarrollado y probado contra moodle 2.6
 *
 * TODO: securizar todas las funciones con una llamada a funcion de 
 * autenticacion  de usuarios
 *
 * @package lib_actv_api
 * @author Cayetano Reinaldos Duarte 
 * @date 
 * @version 0.9
**/ 

/**
 * @global string token de autenticacion para ws
**/
$token = '1a29ce90f1aa734e73370eb3389080fb';

/**
 * @global string token de autenticacion para ws
**/
$domainname = 'http://www.activatie.org/moodle';

//require_once('../moodle/actv_matricula_curso.php'); a incluir 
require_once('db_lib.php');

/**
 * function lista_categorias_moodle - Devuelve una o varias subcategorias de una determinada categoria padre
 * 
 * Recibe: id de la categoria padre --> string
 * 
 * Devuelve: subcategorias --> array bidimensional con indice i [0..n] / n = num de subcategorias devueltas  
 *
 * 		array[i]['id']: id de la subcategoria en moodle
 *
 *			array[i]['nombre']: nombre de la subcategoria en moodle
 *
 * 		array[i]['descripcion]: descripcion de la subcategoria en moodle
 *
 * @param string $id_categoria_padre
 * @return array devuelve los datos de las subcategorias en un array
 *
**/
function lista_categorias_moodle($id_categoria_padre) {
	global $token, $domainname;

	$functionname = 'core_course_get_categories';
	$restformat = 	'xml';

	if(!$id_categoria_padre) {
		return(0);
	}
				
	$obj_categoria_padre = new stdClass;
	$obj_categoria_padre->key = "parent";
	$obj_categoria_padre->value = $id_categoria_padre;

	$categoria_padre = array ("key" => $obj_categoria_padre->key, "value" => $obj_categoria_padre->value);
	$params = array ('criteria' => array($categoria_padre), 'addsubcategories' => 0); 
	//print_r($params);
	
	header('Content-Type: text/plain');
	$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
	require_once('./actv_curl.php');
	$curl = new curl;
	$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
	$resp = $curl->post($serverurl . $restformat, $params);
   //TODO: verificar si devuelve una excepcion de error por parametro mal pasado
	$obj_resp = new SimpleXMLElement($resp);
	//var_dump($obj_resp);
	$i = 0; //indice subcategoria en array
	foreach ($obj_resp->MULTIPLE[0]->SINGLE as $subcategoria) {
		$i++;
		foreach ($subcategoria->KEY as $clave) {
			switch ((string) $clave['name']) {
			case 'id': 
				//echo ($clave->VALUE. ':');
				$array_resp[$i]['id'] = $clave->VALUE;
				break;
			case 'name':
				//echo ($clave->VALUE.':');
				$array_resp[$i]['nombre'] = $clave->VALUE;
				break;
			case 'description':
				$array_resp[$i]['descripcion'] = $clave->VALUE;
				//echo ($clave->VALUE.'\n');
				break;
			}
		}
	}
	header('Content-Type: text/html');
	return($array_resp);
}


/**
 * function crea_categoria_moodle - Crea una categoria en moodle dentro de una categoria padre
 *
 * Recibe: datos de la categoria a crear --> array unidimensional asociativo
 *
 * 		array['nombre']: nombre de la categoraia a crear
 *
 * 		array['id_categoria_padre']: id moodle de la categoria padre
 *
 * 		array['id_categoria_externa']: id en aplicacion externa de la categoria a crear 
 *			
 *			array['descripcion']: descripcion de la categoria a crear
 *
 * Devuelve: id en moodle de la categoria creada o 0 si error
 *
 * @param array $datos_categoria
 * @return integer devuelve 0 si error y el id en moodle si exito
**/
function crea_categoria_moodle($datos_categoria) {
	
	global $token, $domainname;

	$functionname = 'core_course_create_categories';
	$restformat = 'xml'; 

	if (!$datos_categoria) {
		return(0);
	} 

	$obj_categoria = new stdClass;
	$obj_categoria->name = $datos_categoria['nombre'];
	$obj_categoria->parent = $datos_categoria['id_categoria_padre'];
	$obj_categoria->idnumber = $datos_categoria['id_categoria_externa'];
	$obj_categoria->description = $datos_categoria['descripcion'];
	$obj_categoria->descriptionformat = 1; //formato de la descripcion en formato html? 

	$categories = array ($obj_categoria);
	$params = array ('categories'=> $categories); 
	
	
	header('Content-Type: text/plain');
	$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
	require_once('./actv_curl.php');
	$curl = new curl;
	$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
	$resp = $curl->post($serverurl . $restformat, $params);
   //TODO: verificar si devuelve una excepcion de error
	$obj_resp = new SimpleXMLElement($resp);
	$id_categoria = $obj_resp->MULTIPLE->SINGLE->KEY[0]->VALUE;
	header('Content-Type: text/html');
	return($id_categoria);
}


/**
 * function borra_categoria_moodle - borra una categoria en moodle 
 *
 * Recibe: id moodle de la categoria a borrar --> string 
 * Recibe: 1/0 si el borrado es recursivo o no -> int (por defecto es no)
 *
 * Devuelve: 1 si se ha borrado correctamente y -1 si ha habido error
 *
 * @param int $id_categoria 
 * @param int $recursivo (opcional) 
 * @return integer devuelve 1 si si se ha borrado y -1 si ha habido error
**/
function borra_categoria_moodle($id_categoria, $recursivo=0) {

	global $token, $domainname;

	$functionname = 'core_course_delete_categories';
	$restformat = 'xml';

	if (!isset($id_categoria)) {
		return(0);
	}
	$params = array('categories' => array(array(	'id' => $id_categoria, 
																'newparent' => 1,
																'recursive' => $recursivo
															)
													)										
						);
	
	header('Content-Type: text/plain');
	$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
	require_once('./actv_curl.php');
	$curl = new curl;
	$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
	$resp = $curl->post($serverurl . $restformat, $params);
	//print_r($resp); 
	header('Content-Type: text/html');

	$obj_resp = new SimpleXMLElement($resp);
	$error_exception = $obj_resp->xpath("//EXCEPTION");
   if ($error_exception) { 
		return(-1);
	} else {
		return(1); //ok categoria borrada
	}
}

/**
 * function categoria_visible - Desoculta/oculta una categoria con opcion a realizarlo recursivamente en sus subcategorias
 *
 * Recibe: id de la categoria --> string
 * Recibe: visible (1 desoculta 0 oculta)
 * Recibe: recursivo 1/0 si la ocultacion es recursiva/no recursiva
 *
 * Devuelve: 1 si se ha realizado correctamente la acción y 0 si ha habido error
 *
 * @param int $id_categoria
 * @param int $visible 
 * @param int $recursivo (opcional)
 * @return integer 1 si se ha ocultado correctament 0 en otro caso
 *
**/
function categoria_visible($id_categoria, $visible, $recursivo = 0) {
 
	if(!db_init()) return (0);

   // echo($id_categoria);
   // echo($recursivo);
	
   //verifica si es recursivo para realizar la acción
   if ($recursivo == 1) {
      //obtiene y realiza la acción sobre los cursos de la categoria
      $sql = "where category=".$id_categoria;
      $cursos = select_cond('mdl_course', $sql);
      if ($cursos) {
         foreach($cursos as $curso) {
            $id_curso = $curso['id'];
            $update_sql = "set visible=$visible, visibleold=$visible where id=$id_curso";
            update_table('mdl_course', $update_sql);
         }
      }
      //obtiene las subcategorias y realiza la acción  de forma recursiva
      $sql = "where parent=$id_categoria";
      $categorias = select_cond('mdl_course_categories', $sql);
      if ($categorias) {
         foreach($categorias as $categoria) {
            categoria_visible($categoria['id'], $visible, $recursivo);
         }
      }
   }

   //realiza la acción sobre la categoria propiamente dicha
   $update_sql = "set visible=$visible, visibleold=$visible where id=$id_categoria";
   update_table('mdl_course_categories', $update_sql);
   return(1);      
}


/**
 * function lista_cursos_moodle - Devuelve los cursos de una determinada categoria
 * 
 * Recibe: id de la categoria --> string
 * 
 * Devuelve: datos de cursos de la categoria --> array asociativo bidimensional (i [0..n] / n = num cursos)
 * 
 * 		array[i]['id']: id del curso en moodle
 *
 * 		array[i]['id_categoria]: id de la categoria en moodle
 *
 * 		array[i]['nombre_corto']: nombre corto del curso en moodle
 *
 * 		array[i]['nombre_largo']: nombre largo del curso en moodle
 *	
 *			array[i]['id_curso_externo']: id del curso en aplicacion externa	
 *
 * 		array[i]['descripcion']: descripcion del curso
 *
 * 		array[i]['fecha_inicio']: fecha inicio curso en moodle en formato timestamp
 *
 * @param integer $id_categoria
 * @return array devuelve los datos de los cursos en un array
 *
**/
function lista_cursos_moodle($id_categoria) {
	global $token, $domainname;

	$functionname = 'core_course_get_courses';
	$restformat = 	'xml';

	if(!$id_categoria) {
		return(0);
	}
	//TODO: fijar $params a $categoria_id con formato para pasarlo			
	$params = null; //si no se le pasa ningun id devuelve todos los cursos de la categoria por defecto
	//print_r($params);
	
	header('Content-Type: text/plain');
	$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
	require_once('./actv_curl.php');
	$curl = new curl;
	$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
	//var_dump($params);
	$resp = $curl->post($serverurl . $restformat, $params);
   //TODO: verificar si devuelve una excepcion de error por parametro mal pasado
	$obj_resp = new SimpleXMLElement($resp);
	$array_temp = json_decode(json_encode($obj_resp),TRUE);
	$i = 0; //indice en el array creado de respuesta;
	//header('Content-Type: text/html');
	foreach($array_temp['MULTIPLE']['SINGLE'] as $curso) {
		if($curso['KEY'][2]['VALUE'] == $id_categoria) {
			$array_resp[$i]['id'] = $curso['KEY'][0]['VALUE'];
			$array_resp[$i]['id_categoria'] = $curso['KEY'][2]['VALUE'];
			$array_resp[$i]['nombre_corto'] = $curso['KEY'][1]['VALUE'];
			$array_resp[$i]['nombre_largo'] = $curso['KEY'][4]['VALUE'];
			$array_resp[$i]['id_curso_externo'] = $curso['KEY'][5]['VALUE'];
			$posible_descripcion = $curso['KEY'][6]['VALUE'];
			if (!is_array($posible_descripcion)) { //sino es array es que existe descripcion
				$array_resp[$i]['descripcion'] = $posible_descripcion;
			} else {
				$array_resp[$i]['descripcion'] = null; //si es un array es que no existe descripcion
			}
			$array_resp[$i]['fecha_inicio'] = $curso['KEY'][11]['VALUE']; //fechainicio en formato timestamp
			$i++; //si avanza el indice para el próximo elemento
		}
	}
	header('Content-Type: text/html');
	return($array_resp);
}

/**
 * function crea_curso_moodle - Crea un curso en moodle en una determinada categoria
 *
 * Recibe: Datos curso a crear --> array asociativo
 *
 * 		array['nombre_largo']: nombre largo del curso en moodle
 *
 * 		array['nombre_corto']: nombre corto del curso en moodle
 *
 * 		array['id_categoria']: id moodle de la categoria donde se crea el curso
 *
 * 		array['fecha_inicio']: fecha de inicio del curso en moodle (formato timestamp)
 *
 * 		array['id_curso_externo']: id del curso enaplicacion externa
 *
 * 		array['num_secciones']: num de secciones (temas) que se desea tener en curso por defecto 10
 *
 * Devuelve: Id en moodle del curso creado o 0 si error
 *
 * @param array $datos_curso
 * @return integer devuelve 0 si error y el id en moodle si exito
**/
function crea_curso_moodle($datos_curso) {
	
	global $token, $domainname;
	
	$functionname = 'core_course_create_courses';
	$restformat = 'xml'; 

	if (!$datos_curso) {
		return(0);
	}
	//Preparación de objeto a pasar a la función de creación de moodle
	$obj_curso = new stdClass;
	$obj_curso->fullname 	= $datos_curso['nombre_largo'];
	$obj_curso->shortname	= $datos_curso['nombre_corto'];
	$obj_curso->categoryid	= $datos_curso['id_categoria'];
	$obj_curso->idnumber		= $datos_curso['id_curso_externo'];
	$obj_curso->summary		= "";
	$obj_curso->summaryformat = 1;
	$obj_curso->format 		= "topics";
	$obj_curso->showgrades	= 1;
	$obj_curso->newsitems	= 5;
	$obj_curso->startdate	= $datos_curso['fecha_inicio'];
	$obj_curso->numsections	= $datos_curso['num_secciones'];
	$obj_curso->maxbytes = 0;
	$obj_curso->showreports = 0;
   $obj_curso->visible = 1;
	$obj_curso->groupmode = 1;
	$obj_curso->groupmodeforce = 0;
	$obj_curso->defaultgroupingid = 0;
	
	$courses = array ($obj_curso);
	$params = array ('courses'=> $courses);


	header('Content-Type: text/plain');
	$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
	require_once('./actv_curl.php');
	$curl = new curl;
	$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
	$resp = $curl->post($serverurl . $restformat, $params);
   //TODO: verificar si devuelve una excepcion de error
	$obj_resp = new SimpleXMLElement($resp);
	$id_curso = $obj_resp->MULTIPLE->SINGLE->KEY[0]->VALUE;
	header('Content-Type: text/html');
	return($id_curso);
}


/**
 * function clona_curso_moodle - crea curso desde un curso plantilla
 * 
 * Recibe: id_curso_plantilla - id del curso/plantilla curso a clonar
 *
 * Recibe: Datos específicos del curso nuevo clonado --> array asociativo
 * 
 *       array['nombre_largo']: nombre largo del curso en moodle                                               
 *                                                                                                             
 *       array['nombre_corto']: nombre corto del curso en moodle                                               
 *                                                                                                             
 *       array['id_categoria']: id moodle de la categoria donde se crea el curso                               
 *                                                                                                             
 *       array['fecha_inicio']: fecha de inicio del curso en moodle (formato timestamp)                        
 *                                                                                                             
 *       array['id_curso_externo']: id del curso enaplicacion externa                                         
 *   
 * Devuelve: Id en moodle del curso creado o 0 si error   
 *                         
 * @param array $datos_curso                                                                                   
 * @return integer devuelve 0 si error y el id del curso en moodle si exito                                   
 **/

function clona_curso_moodle($id_curso_plantilla, $datos_curso) {
   global $token, $domainname;

   $functionname = 'core_course_duplicate_course';
   $restformat = 'xml';

   if (!$id_curso_plantilla || !$datos_curso) {
      return(0);
   }
   
   //Preparación de objeto a pasar a la función de creación de moodle                                        
   //$obj_clone = new stdClass;
   //$obj_clone->courseid = $id_curso_plantilla;
   //$obj_clone->fullname   = $datos_curso['nombre_largo'];
   //$obj_clone->shortname  = $datos_curso['nombre_corto'];
   //$obj_clone->categoryid = $datos_curso['id_categoria'];
   //$obj_clone->visible    = "1";
   //$obj_clone->options = array(array('name'=>'blocks', 'value'=>1), array('name'=>'activities', 'value'=>1), array('name'=>'filters', 'value'=>1));
   //Preparación de array a pasar al web service de clonación de curso
   $datos_clonacion = array (
                             "courseid"   => $id_curso_plantilla,
                             "fullname"   => $datos_curso['nombre_largo'],
                             "shortname"  => $datos_curso['nombre_corto'],
                             "categoryid" => $datos_curso['id_categoria'],
                             "visible"    => "1",
                             "options"    => array(array('name'=>'blocks', 'value'=>1), 
                                                   array('name'=>'activities', 'value'=>1), 
                                                   array('name'=>'filters', 'value'=>1)
                                                   )
                             );

	$params = $datos_clonacion;


	header('Content-Type: text/plain');
   //print_r($params);
	$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
	require_once('./actv_curl.php');
	$curl = new curl;
	$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
	$resp = $curl->post($serverurl . $restformat, $params);
   //TODO: verificar si devuelve una excepcion de error
	$obj_resp = new SimpleXMLElement($resp);
	$id_curso = $obj_resp->SINGLE->KEY[0]->VALUE;

   //Ajustar fecha de inicio e id_curso_externo vía db,  no se puede hacer via webservices
   if(!db_init()) return (0);
   $fecha_inicio = $datos_curso['fecha_inicio'];
   $update_sql = "set startdate=$fecha_inicio where id=$id_curso";
   update_table('mdl_course', $update_sql);
   $id_curso_externo = $datos_curso['id_curso_externo'];
   $update_sql = "set idnumber=$id_curso_externo where id=$id_curso";
   update_table('mdl_course', $update_sql);
	header('Content-Type: text/html');
	return($id_curso);
}

/**
 borra_curso_moodle - borra un curso en moodle 
 *
 * Recibe: id moodle del curso a borrar --> string 
 *
 * Devuelve: 1 si se ha borrado correctamente y -1 si ha habido error
 *
 * @param $id_curso
 * @return integer devuelve 1 si si se ha borrado y -1 si ha habido error
**/
function borra_curso_moodle($id_curso) {

	global $token, $domainname;

	$functionname = 'core_course_delete_courses';
	$restformat = 'xml';

	if (!isset($id_curso)) {
		return(0);
	}
	$params = array('courseids' => array(0 => $id_curso)										
						);
	
	header('Content-Type: text/plain');
	$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
	require_once('./actv_curl.php');
	$curl = new curl;
	$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
	$resp = $curl->post($serverurl . $restformat, $params);
	//print_r($resp); 
	header('Content-Type: text/html');
	$obj_resp = new SimpleXMLElement($resp);
	$error_exception = $obj_resp->xpath("//EXCEPTION");
   if ($error_exception) { 
		return(-1);
	} else {
		return(1); //ok curso borrado
	}
}


/**
 * function matricula_usuario_curso matricula un usuario a curso  o suspende matricula anterior
 *
 * Recibe: id moodle del curso --> int 
 *
 * Recibe: id moodle del usuario --> int
 *
 * Recibe: rol con el que se quieren dar los permisos -->int
 *
 * Devuelve: 1 si se ha asignado y 0 si ha habido error
 *
 * @param $id_curso int identificador del curso en moodle
 *
 * @param $id_usuario int identificador del usuario en moodle
 *
 * @param $rol int (5 estudiante, 3 profesoreditor, 4 profesor normal) rol que se le da al usuario
 *
 * @param $f_inicio timestamp (opcional) fecha inicio de la matricula/permisos
 *
 * @param $f_fin timestamp (opcional) fecha fin de la matricula/permisos
 *
 * @param $suspendido int opcional (1 suspendido, 0 activo)
 *
 * @return integer devuelve 1 si éxito y 0 si ha habido error
**/
function matricula_usuario_curso ($id_curso, $id_usuario, $rol, $f_inicio=0, $f_fin=0, $suspendido=0) {

	global $token, $domainname;

	$functionname = 'enrol_manual_enrol_users';
	$restformat = 'xml';

	if (!isset($id_curso) || !isset($id_usuario) || !isset($rol)) {
		return(0);
	}
	$obj_enrolment = new StdClass();
	$obj_enrolment->roleid = $rol;
	$obj_enrolment->userid = $id_usuario;
	$obj_enrolment->courseid = $id_curso;
   $obj_enrolment->timestart = $f_inicio;
	$obj_enrolment->timeend = $f_fin;
	$obj_enrolment->suspend = $suspendido;

	$enrolments = array($obj_enrolment);
	$params = array('enrolments'=>$enrolments);

	
	header('Content-Type: text/plain');
	$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
	require_once('./actv_curl.php');
	$curl = new curl;
	$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
	$resp = $curl->post($serverurl . $restformat, $params);
	//print_r($params);
   //print_r($resp); 
	header('Content-Type: text/html');
	$obj_resp = new SimpleXMLElement($resp);
 	//print_r($obj_resp);
	$error_exception = $obj_resp->xpath("//EXCEPTION");
   if ($error_exception) { 
		return(0);
	} else {
		return(1); //ok permisos concedidos
	}
}



/** 
 * function quita_permisos_curso - quita permisos de un usuario a curso 
 *
 * Recibe: id moodle del curso --> int 
 *
 * Recibe: id moodle del usuario --> int
 *
 * Recibe: rol con el que se quiere quitar -->int
 *
 * Devuelve: 1 si se ha sido correcto y 0 si ha habido error
 *
 * @param $id_curso int identificador del curso en moodle
 *
 * @param $id_usuario int identificador del usuario en moodle
 *
 * @param $rol int (5 estudiante, 3 profesoreditor, 4 profesor normal) rol que se le quita al usuario 
 * @return integer devuelve 1 si éxito y 0 si ha habido error
**/
/**REVISAR NO FUNCIONAN LOS PARAMETROS
function quita_permisos_curso ($id_curso, $id_usuario, $rol) {

	global $token, $domainname;

	$functionname = 'core_role_unassign_roles';
	$restformat = 'xml';

	if (!isset($id_curso) || !isset($id_usuario) || !isset($rol)) {
		return(0);
	}
	$obj_unassign = new StdClass();
	$obj_unassign->roleid = $rol;
	$obj_unassign->userid = $id_usuario;
   $obj_unassign->contextid = '';
	$obj_unassign->contextlevel = 'CONTEXT_COURSE';
	$obj_unassign->instanceid = $id_curso;

	$unassignments = array($obj_unassign);
	$params = array('unassignments'=>$unassignments);

	
	header('Content-Type: text/plain');
	$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
	require_once('./actv_curl.php');
	$curl = new curl;
	$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
	$resp = $curl->post($serverurl . $restformat, $params);
   print_r($params);
   print_r($resp); 
	header('Content-Type: text/html');
	$obj_resp = new SimpleXMLElement($resp);
 	//print_r($obj_resp);
	$error_exception = $obj_resp->xpath("//EXCEPTION");
   if ($error_exception) { 
		return(0);
	} else {
		return(1); //ok permisos revocados
	}
}**/

function get_iduser_moodle($idnumber) {
	if (!db_init()) return (0);

	$sql = "where idnumber like '".$idnumber."'";

	$result = select_cond('mdl_user',$sql);
   
	if ($result) {
		$id = $result[0]['id'];
		return($id);
	} else {
		return(0);
	}
}

function borra_usuario_moodle($id_user) {

	global $token, $domainname;

	$functionname = 'core_user_delete_users';
	$restformat = 'xml';

	if (!isset($id_user)) {
		return(0);
	}
	$params = array('userids' => array(0 => $id_user)										
						);
   //echo($id_user);
	
	header('Content-Type: text/plain');
	$serverurl = $domainname . '/webservice/rest/server.php'. '?wstoken=' . $token . '&wsfunction='.$functionname;
	require_once('./actv_curl.php');
	$curl = new curl;
	$restformat = ($restformat == 'json')?'&moodlewsrestformat=' . $restformat:'';
	$resp = $curl->post($serverurl . $restformat, $params);
	//print_r($resp); 
	header('Content-Type: text/html');
	$obj_resp = new SimpleXMLElement($resp);
	$error_exception = $obj_resp->xpath("//EXCEPTION");
   //print_r($error_exception);
   if ($error_exception) { 
		return(-1);
	} else {
		return(1); //ok usuario borrado
	}
}
?>

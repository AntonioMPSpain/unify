<?php

$CFG = new stdClass();
$CFG->dbhost = 'localhost';
$CFG->dbname = 'moodle';
$CFG->dbuser = 'postgres';
$CFG->dbpasswd = 'admin_activatie_2014$';
$CFG->dblink ='';

function db_init() {
	global $CFG;

	$CFG->dblink = pg_connect("host=$CFG->dbhost dbname=$CFG->dbname user=$CFG->dbuser password=$CFG->dbpasswd");
   if(!$CFG->dblink) {
      die("Error de conexion a la base de datos moodle");
   } else {
      return($CFG->dblink);
   }
}

function select_all ($table) {
	global $CFG;
	$resultado = array();

	$sql = "select * from $table";

	$results = pg_query($CFG->dblink,$sql);
	if ($results) {
      $i=0;
		while($row = pg_fetch_assoc($results)) {
         $resultado[$i] = $row;
         $i++;
      }
		return($resultado);		
	} else {
		return(0);
	}
}


function select_row ($table, $id) {
	global $CFG;
	$resultado = array();

	$sql = "select * from $table where id=$id";

	$results = pg_query($CFG->dblink,$sql);
	if ($results) {
      $i=0;
		while($row = pg_fetch_assoc($results)) {
         $resultado[$i]=$row;
         $i++;
      }         
		return($resultado);		
	} else {
		return(0);
	}
}


function select_cond ($table, $cond) {
	global $CFG;
	$resultado = array();

	$sql = "select * from $table " . $cond;

	$results = pg_query($CFG->dblink,$sql);
	if ($results) {
      $i=0;
		while($row = pg_fetch_assoc($results)) { 
         $resultado[$i] = $row;
         $i++;
      }
		return($resultado);		
	} else {
		return(0);
	}
}

function update_table($table, $update_sql) {
   global $CFG;

   $sql = "update $table $update_sql";
   $result = pg_query($CFG->dblink,$sql);
   
   return($result);
}

?>

<?

include("_config.php"); 
include_once $actividadpath."config.php";

if (isset($_GET['test'])){
	// updateUser(6264);
	
	
	authenticateUser("22222222", "antonio");
	
	
	// logoutUser();
}
 
 

function authenticateUser($login, $pass){
	global $current_user;
	$current_user->Authenticate($login, $pass, $remember = false /* Just this session */);
}

function logoutUser($return=""){
	global $current_user;
	$current_user->Logout($return);
}

function updateUser($idusuario){

	global $db,$globals;
	
	$sql = "SELECT * FROM usuario WHERE id='$idusuario' AND borrado=0 AND baja=0 AND confirmado=1";
	$result = posgre_query($sql);
	if ($row = pg_fetch_array($result)){
		$idactivatie = $row['id'];
		$login = $row['login'];
		$email = $row['email'];
		$pass = $row['pass'];
		$password=UserAuth::hash(trim($pass));
		$user_ip = $globals['user_ip'];
	}
	
	echo $login."<br>";
	echo $email."<br>";
	echo $pass."<br>";
	echo $password."<br>";
	echo $user_ip."<br>";
	
	if ((!user_exists($login))||(!email_exists(trim($email)))) {
		echo "No existe<br>";
		if ($db->query("INSERT INTO users (user_login, user_login_register, user_email, user_email_register, user_pass, user_date, user_ip, user_validated_date) VALUES ('$login', '$login', '$email', '$email', '$password', now(), '$user_ip', now())")) {
			echo "Creado<br>";
		}	
		else{
			echo "No creado<br>";
		}
	}
	else{
		echo "Existe<br>";
		if ($db->query("UPDATE users SET user_email='$email', user_pass='$password', user_ip='$user_ip' WHERE user_login='$login'")) {
			echo "Actualizado<br>";
		}	
		else{
			echo "No actualizado<br>";
		}
	}
	

	

}

/*
	$res = $db->get_results("SELECT * FROM users");

	foreach ($res as $user) {
		echo $user->user_email;
		echo "<br>";
		
	}
*/

/*	
	$registered = (int) $db->get_var("SELECT count(*) FROM users");
	echo "registrados:".$registered; 
*/
		




?>
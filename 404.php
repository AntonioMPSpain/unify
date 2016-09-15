<? 
include_once "_config.php";

include ($templatepath."header.php");
$twig->display('404.php', array());
include ($templatepath."footer.php");

?>

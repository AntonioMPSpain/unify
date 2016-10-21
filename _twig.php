<?

include_once "_config.php"; 

require_once $libspath.'Twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem($templatepath);
$twig = new Twig_Environment($loader, array(
    // cache disabled, since this is just a testing project
    'cache' => false,
    'debug' => false,
    'strict_variables' => false 
));

?>
<?php
$application->registerModules(array(
	'minify' => array(
        'className' => 'Dsc\Minify\Module',
        'path' => __dir__ . '/src/Module.php'
    )
), true);

$di = $application->getDI();  
$di->get('theme')->registerViewPath( PATH_ROOT . 'vendor/dioscouri/phalcon-minify/src/Views/', 'Minify/Views' );
$di->setShared('minify', 'Dsc\Minify\Service'); // register shared service for this module

$router = $di->get('router');
$router->mount( new \Dsc\Minify\Routes() );
?>
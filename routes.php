<?php

$authenticate = function( \Slim\Route $route ) {

	$routeName = $route->getName();
	$app = \Slim\Slim::getInstance();

	$app->view()->appendData(array(
		'login' => SessionNative::check('USER') 
	));

	if ( SessionNative::check('USER') ) {
		( $routeName === 'index' || $routeName === 'login' ) && $app->redirect('/');
	} else {
		SessionNative::delete('USER');
		$app->redirect('/login');
	}
};

$authenticate_admin = function( \Slim\Route $route ) {
    $routeName = $route->getName();
    $app = \Slim\Slim::getInstance();

    if( SessionNative::check('ADMIN') ) {
        ( $routeName === 'index' || $routeName === 'login' ) && $app->redirect('/admin');
    } else {
        SessionNative::delete('ADMIN');
        $app->redirect('/admin/login');
    }
};

/* get current host */
function getCurrentHost() {
    if (isset($_SERVER['HTTPS']) &&
        ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
        isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
        $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
        $protocol = 'https://';
    }
    else {
        $protocol = 'http://';
    }
    // use port if non default
    $port = isset($parts['port']) &&
      (($protocol === 'http://' && $parts['port'] !== 80) ||
       ($protocol === 'https://' && $parts['port'] !== 443))
      ? ':' . $parts['port'] : '';
    
    // rebuild
    return $protocol . $_SERVER['HTTP_HOST'] . $port;
}

require 'routes/dashboard.php';
require 'routes/admin.php';

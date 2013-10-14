<?php
/**
 * This file is part of the Nocriz API (http://nocriz.com)
 *
 * Copyright (c) 2013  Nocriz API (http://nocriz.com)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

/**
 * Nocriz API 
 *
 * @package  Nocriz
 * @author   Ramon Barros <contato@ramon-barros.com>
 */

ini_set('display_errors',1);
error_reporting(E_ALL | E_STRICT );
date_default_timezone_set('America/Sao_Paulo');

define('DS', DIRECTORY_SEPARATOR);
define('APP_ROOT', realpath(__DIR__.DS.'..'.DS.'..'));
$time = time();
header('Last-Modified: '.gmdate('D, d M Y H:i:s', $time).'GMT');
/*
| ------------------------------------------------- -------------------------
| Check Extensions
| ------------------------------------------------- -------------------------
|
| The application requires some extensions to work
| And we will see if they are loaded.
|
*/

if ( ! extension_loaded('mcrypt'))
{
	die('Lemee requires the Mcrypt PHP extension.'.PHP_EOL);
	exit(1);
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| This application is installed by the Composer, 
| that provides a class loader automatically.
| Use it to seamlessly and feel free to relax.
|
*/

$composer_autoload = APP_ROOT.DS.'vendor'.DS.'autoload.php';
if(!file_exists($composer_autoload)){
    die('Please use the composer to install http://getcomposer.org');
}
require $composer_autoload;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use LewisB\ElephantIOServiceProvider\ElephantIOServiceProvider;
use Silex\Provider\TwigServiceProvider;

$app = new Silex\Application();
$app['app.url'] = 'http://localhost';
$app['app.path'] = DS.'src'.DS.'public'.DS.'index.php';
$app['app.assets'] = DS.'src'.DS.'public'.DS.'assets';
$app['elephant.address'] = 'http://localhost:4568';
$app['twig.templates.dir'] = APP_ROOT.'src'.DS.'private'.DS.'templates';

$app->register(new TwigServiceProvider(), array(
	'twig.path' => $app['twig.templates.dir']
));
$app->register(new ElephantIOServiceProvider(), array(
	'elephant.address' => $app['elephant.address']
));
$app->get('/', function() use($app){
	return "Hello";
	/*
	return $app['twig']->render('home.twig.html', array(
		'url' => $app['app.url'],
		'assets' => $app['app.assets'],
		'socket_address' => $app['elephant.address'],
		'app_path' => $app['app.path']
	));
	*/
});
$app->post('/', function() use($app){
	$message = array(
		'message' => $app['request']->get('message'),
		'user'	=> $app['request']->get('user')
	);
	$app['elephant']->init();
	$app['elephant']->emit('new_message', $message, null, null);
	$app['elephant']->close();
	return $app->json('Success!', 200);
});
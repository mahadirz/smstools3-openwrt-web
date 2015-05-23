<?php
namespace Swiftlet;
$startTime = microtime(true);

session_start();

use App\Libraries\Performance;

try {
	chdir(dirname(__FILE__) . '/..');

	require 'vendor/autoload.php';

    $performance = new Performance();
    $performance->setStartExecutionTime($startTime);
    $_SESSION['performance'] = $performance;

	$app = new App(new View, 'App');


	// Convert errors to ErrorException instances
    error_reporting(E_ALL & ~E_NOTICE& ~E_DEPRECATED);
	set_error_handler(array($app, 'error'), E_ALL & ~E_NOTICE & ~E_DEPRECATED);

	require 'config/main.php';

	date_default_timezone_set('Asia/Kuala_Lumpur');

	$app->loadPlugins(); // You may comment this out if you're not using plugins

	$app->dispatchController();

	ob_start();

	$app->serve();

	ob_end_flush();
} catch ( \Exception $e ) {
	if ( !headers_sent() ) {
		header('HTTP/1.1 503 Service Temporarily Unavailable');
		header('Status: 503 Service Temporarily Unavailable');
	}

	$errorCode = substr(sha1(uniqid(mt_rand(), true)), 0, 5);

	$errorMessage = $errorCode . date(' r ') . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine();

	if(isset($app))
        if($app->getConfig('developmentMode'))
        {
            echo '<pre>';
            echo $errorMessage."<br>\n";
            echo $e->getTraceAsString()."<br>\n";
            echo '</pre>';
            exit();
        }

    file_put_contents('log/exceptions.log', "\n" . $errorMessage . "\n" . $e->getTraceAsString() . "\n", FILE_APPEND);

	exit('Exception: ' . $errorCode . '<br><br><small>The issue has been logged. Please contact the website administrator.</small>');
}

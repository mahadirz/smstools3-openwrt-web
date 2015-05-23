<?php

namespace App\Controllers;

use \Swiftlet\Abstracts\Controller as ControllerAbstract;

/**
 * Error 404 controller
 */
class Error404 extends ControllerAbstract
{

	/**
	 * Default action
	 */
	public function index()
	{
        $name = 'errors/404';
        $this->view->setName($name);
		if ( !headers_sent() ) {
			header('HTTP/1.1 404 Not Found');
			header('Status: 404 Not Found');
		}
	}
}

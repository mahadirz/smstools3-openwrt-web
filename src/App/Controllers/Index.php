<?php

namespace App\Controllers;

use \App\Models\Example as ExampleModel;
use \Swiftlet\Abstracts\Controller as ControllerAbstract;

/**
 * Index controller
 */
class Index extends ControllerAbstract
{

    protected $routes = array(
        'login' => 'index',
        'logout' => 'logout'
    );



	/**
	 * Default action
	 * @param $args array
	 */
	public function index(array $args = array())
	{
        $name = 'login';
        $this->view->setName($name);

        if($_POST)
        {
            if($_POST['username'] == $this->app->getConfig('username')
            && $_POST['password'] == $this->app->getConfig('password'))
            {
                $_SESSION['authenticated'] = true;
                header('location:index.php?q=sms/inbox');
                exit();
            }
            else
            {
                $this->view->flashMessages->add('danger','Invalid username or password!');
            }
        }

	}

    public function logout(array $args = array())
    {
        session_destroy();
        header('location:index.php?q=login');
        exit();
    }
}

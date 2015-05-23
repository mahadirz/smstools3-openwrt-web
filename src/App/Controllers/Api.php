<?php
namespace App\Controllers;

use App\Libraries\UssdQuery;
use \Swiftlet\Abstracts\Controller as ControllerAbstract;

class Api extends ControllerAbstract
{
    protected $routes = array(
        'api/ussd' => 'getUssd',
    );

    public function __construct()
    {
        //only authenticated user can access all resources here
        if($_SESSION['authenticated'] != true)
        {
            header('Content-Type: application/json');
            echo json_encode(array(
                'success'=>false,
                'payload'=>array(),
                'error' => 'Unauthorized'
            ));
            exit;
        }
    }


    public function index(array $args = array() )
    {
        printr_die($_GET);
    }

    public function getUssd(array $args = array() )
    {
        $command = $_GET['command'];
        if ( !headers_sent() ) {
            header('Content-Type: application/json');
        }

        $ussdQuery = new UssdQuery($this->app->getConfig('device','/dev/ttyUSB1'));
        if($this->app->getConfig('developmentMode'))
        {
            $ussdQuery->setUSSDTextMessage('Balance 0111111111:RM 5.00,valid until: 18/07/2015,plan: IOX.');
            $ussdQuery->setUSSDRequireReply(false);
            $this->view->html = $ussdQuery->getJsonFormatted();
        }
        else
        {
            $ussdQuery->sendCommand($command);
            $this->view->html = $ussdQuery->getJsonResult();
        }


    }
}
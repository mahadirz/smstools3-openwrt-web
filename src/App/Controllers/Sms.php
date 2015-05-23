<?php
/**
 * Created by PhpStorm.
 * User: Mahadir
 * Date: 5/20/2015
 * Time: 2:27 PM
 */

namespace App\Controllers;

use App\Models\SimpleMessaging;
use \Swiftlet\Abstracts\Controller as ControllerAbstract;
use \Swiftlet\Interfaces\App as AppInterface;
use \App\Libraries\Sms as SmsLibrary;


class Sms extends ControllerAbstract {

    protected $routes = array(
        'sms/compose' => 'compose',
        'sms/inbox'    => 'index',
        'sms/outgoing'  => 'outgoing',
        'sms/sent' => 'sent',
        'sms/templates' => 'templates',
        'sms/ussd' => 'ussd'
    );

    /**
     * Sms library
     * @var \App\Libraries\Sms
     */
    private $smsLibrary;

    public function __construct(AppInterface $app)
    {
        //only authenticated user can access all resources here
        if($_SESSION['authenticated'] != true)
        {
            //printr_die($_SESSION);
            header('location:index.php?q=login');
            exit;
        }

        $sentDir = $app->getConfig('sentDir');
        $inboxDir = $app->getConfig('inboxDir');
        $outboxDir = $app->getConfig('outboxDir');
        $templateDir = $app->getConfig('templateDir');
        $this->smsLibrary = new SmsLibrary($sentDir,$inboxDir,$outboxDir,$templateDir);
    }

    public function setTotalBox()
    {
        $this->view->smarty->assign('totalInbox',$this->smsLibrary->getTotalInbox());
        $this->view->smarty->assign('totalSent',$this->smsLibrary->getTotalSentBox());
        $this->view->smarty->assign('totalOutgoing',$this->smsLibrary->getTotalOutgoingBox());
    }

    /**
     * Inbox
     * @param array $args
     */
    public function index(array $args = array()){
        $name = 'inbox';
        $this->view->setName($name);
        $this->view->smarty->assign('menu',$name);

        if(isset($_POST) && isset($_POST['inbox_delete']))
        {

            $count =0;
            foreach($_POST['filename'] as $index => $filename)
            {
                if($this->smsLibrary->deleteInboxMessage($filename))
                {
                    //echo 'delete '.$filename.'<br>';
                    $count++;
                }
            }
            $this->view->flashMessages->add('success','Successfully deleted '.$count.' message(s)');
        }

        /** @var \App\Models\SimpleMessaging[] $simpleMessagingArray */
        $simpleMessagingArray = $this->smsLibrary->getInBox();
        $this->view->smarty->assign('simpleMessagingArray',$simpleMessagingArray);
        $this->setTotalBox();
    }

    /**
     * Compose
     * @param array $args
     */
    public function compose(array $args = array()){
        $name = 'compose';
        $this->view->setName($name);
        $this->view->smarty->assign('menu',$name);

        if($_POST && isset($_POST['compose']))
        {
            if($_POST['save_template'])
            {
                $simpleMessaging = new SimpleMessaging();
                $simpleMessaging
                    ->setToNumber($_POST['msgtonumber'])
                    ->setText($_POST['msgtext']);
                if($this->smsLibrary->saveTextMessage($simpleMessaging))
                    $this->view->flashMessages->add('success','Text Message saved as template');
            }
            else if($_POST['send'])
            {
                $simpleMessaging = new SimpleMessaging();
                $simpleMessaging
                    ->setToNumber($_POST['msgtonumber'])
                    ->setText($_POST['msgtext']);
                if($this->smsLibrary->sendTextMessage($simpleMessaging))
                    $this->view->flashMessages->add('success','Text Message will be in sent box once
                    successfully sent!');
            }
        }
        $this->setTotalBox();
    }

    /**
     * Handle outbox request
     * @param array $args
     */
    public function sent(array $args = array()){
        $name = 'sent';
        $this->view->setName($name);
        $this->view->smarty->assign('menu',$name);

        if($_POST && isset($_POST['outbox_delete']))
        {

            $count =0;
            foreach($_POST['filename'] as $index => $filename)
            {
                if($this->smsLibrary->deleteSentMessage($filename))
                {
                    //echo 'delete '.$filename.'<br>';
                    $count++;
                }
            }
            $this->view->flashMessages->add('success','Successfully deleted '.$count.' message(s)');
        }

        /** @var \App\Models\SimpleMessaging[] $simpleMessagingArray */
        $simpleMessagingArray = $this->smsLibrary->getSentBox();
        $this->view->smarty->assign('simpleMessagingArray',$simpleMessagingArray);
        $this->setTotalBox();
    }

    /**
     * Show outgoing box
     * @param array $args
     */
    public function outgoing(array $args = array()){
        $name = 'outgoing';
        $this->view->setName($name);
        $this->view->smarty->assign('menu',$name);

        if($_POST && isset($_POST['outbox_delete']))
        {

            $count =0;
            foreach($_POST['filename'] as $index => $filename)
            {
                if($this->smsLibrary->deleteOutgoingMessage($filename))
                {
                    //echo 'delete '.$filename.'<br>';
                    $count++;
                }
            }
            $this->view->flashMessages->add('success','Successfully deleted '.$count.' message(s)');
        }

        /** @var \App\Models\SimpleMessaging[] $simpleMessagingArray */
        $simpleMessagingArray = $this->smsLibrary->getOutgoingBox();
        $this->view->smarty->assign('simpleMessagingArray',$simpleMessagingArray);
        $this->setTotalBox();
    }

    /**
     * Handle templates request
     * @param array $args
     */
    public function templates(array $args = array())
    {
        $name = 'templates';
        $this->view->setName($name);
        $this->view->smarty->assign('menu',$name);

        if($_POST)
        {
            if(isset($_POST['delete']))
            {
                $count =0;
                foreach($_POST['filename'] as $index => $filename)
                {
                    if($this->smsLibrary->deleteTemplateMessage($filename))
                    {
                        //echo 'delete '.$filename.'<br>';
                        $count++;
                    }
                }
                $this->view->flashMessages->add('success','Successfully deleted '.$count.' template(s)');
            }
            else if(isset($_POST['send']))
            {
                $count =0;
                foreach($_POST['filename'] as $filename)
                {
                    $destinationFileName = $this->smsLibrary->getOutboxDir()
                        .DIRECTORY_SEPARATOR
                        ."simplesms_"
                        . date("siH");;
                    copy($this->smsLibrary->getTemplateDir().DIRECTORY_SEPARATOR.$filename,$destinationFileName);
                    if(file_exists($destinationFileName))
                        $count++;
                }

                    $this->view->flashMessages->add('success',$count.' Text Message has been sent! ');
            }


        }

        /** @var \App\Models\SimpleMessaging[] $simpleMessagingArray */
        $simpleMessagingArray = $this->smsLibrary->getTemplates();
        $this->view->smarty->assign('simpleMessagingArray',$simpleMessagingArray);
        $this->setTotalBox();
    }

    /**
     * Compose
     * @param array $args
     */
    public function ussd(array $args = array()){
        $name = 'ussd';
        $this->view->setName($name);
        $this->view->smarty->assign('menu',$name);
        $this->setTotalBox();
    }



}
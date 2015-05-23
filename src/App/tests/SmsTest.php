<?php
/**
 * Created by PhpStorm.
 * User: Mahadir
 * Date: 5/21/2015
 * Time: 12:16 AM
 */

namespace App\tests;
use App\Libraries\Sms;
use App\Models\SimpleMessaging;

require_once 'vendor/autoload.php';



class SmsTest extends \PHPUnit_Framework_TestCase {
    /**
     * @var Sms
     */
    protected $sms;

    protected $workingDir;
    protected $sentDir;
    protected $inboxDir;
    protected $outboxDir;
    protected $templateDir;

    protected function setUp()
    {
        $this->workingDir = getcwd().DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."App".DIRECTORY_SEPARATOR."tests";
        $this->sentDir = $this->workingDir.DIRECTORY_SEPARATOR."sentDir";
        $this->inboxDir = $this->workingDir.DIRECTORY_SEPARATOR."inboxDir";
        $this->outboxDir = $this->workingDir.DIRECTORY_SEPARATOR."outboxDir";
        $this->templateDir = $this->workingDir.DIRECTORY_SEPARATOR."templateDir";
        $this->sms = new Sms(
            $this->sentDir,
            $this->inboxDir,
            $this->outboxDir,
            $this->templateDir);
    }

    protected function tearDown()
    {
        //clear outgoing directory
        array_map('unlink', glob($this->outboxDir.DIRECTORY_SEPARATOR."*"));
    }

    public function testDummy()
    {
        //echo getcwd().DIRECTORY_SEPARATOR."src".DIRECTORY_SEPARATOR."App".DIRECTORY_SEPARATOR."tests";
    }

    public function testGetTemplate()
    {
        $templates = $this->sms->getTemplates();
        $simpleMessaging = new SimpleMessaging();
        $simpleMessaging
            ->setToNumber('22188')
            ->setText('VOL STATUS')
            ->setFileName('simplesms_075509');
        $this->assertEquals($simpleMessaging, $templates[0],"testGetTemplate ");
    }

    public function testGetInBox()
    {
        $inboxSms = $this->sms->getInBox();
        $this->assertGreaterThan(0,count($inboxSms),"There must be more than 0 SimpleMessaging object");
        foreach($inboxSms as $simpleMessagingObject){
            $this->assertInstanceOf('App\Models\SimpleMessaging', $simpleMessagingObject,'$simpleMessagingObject must be instance of SimpleMessaging');
        }
    }

    public function testGetSentBox()
    {
        $outboxSms = $this->sms->getSentBox();
        $this->assertInstanceOf('App\Models\SimpleMessaging', $outboxSms[0],'$outboxSms must be instance of SimpleMessaging');
        $simpleMessaging = new SimpleMessaging();
        $simpleMessaging
            ->setFromNumber('+60175618221')
            ->setText('Hello there')
            ->setSentDateTime('15-05-21 11:47:31')
            ->setFileName('simplesms_204711');
        $this->assertEquals($simpleMessaging, $outboxSms[0],'$simpleMessaging should be equal object as $outboxSms[0] ');

    }

    public function testCompose()
    {
        $simpleMessaging = new SimpleMessaging();
        $simpleMessaging
            ->setToNumber('0175618221')
            ->setText('Hello there');
        $smsBody = $this->sms->compose($simpleMessaging);
        //check for string pattern that smsBody must have
        $this->assertRegExp('/^To:\s\+\d+?\n\n[\s\S]{0,160}/', $smsBody);
    }

    public function testDeleteSentMessage()
    {
        //create dummy file
        file_put_contents($this->sms->getSentDir().DIRECTORY_SEPARATOR."dummy","dummy");
        $outcome = $this->sms->deleteSentMessage("dummy");
        $this->assertTrue($outcome,'File should be deleted');
    }


}

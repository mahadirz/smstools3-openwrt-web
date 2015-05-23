<?php
/**
 * Created by PhpStorm.
 * User: Mahadir
 * Date: 5/23/2015
 * Time: 2:21 PM
 */

namespace App\tests;


use App\Libraries\UssdQuery;

class UssdQueryTest extends \PHPUnit_Framework_TestCase {

    private $param;

    /**
     * @var UssdQuery
     */
    private $ussdQuery;

    public function setUp()
    {
        $this->ussdQuery = new UssdQuery();
    }

    public function mockShell($param)
    {
        //mock the query result
        $this->param = $param;
        $result = file_get_contents(dirname(__FILE__).DIRECTORY_SEPARATOR.'gsm_ussd_result.txt');
        return $result;
    }

    public function sendCommand($command='*124#')
    {
        $this->ussdQuery->setShell(array($this,'mockShell'));
        $this->ussdQuery->sendCommand($command);
    }

    public function testSendCommand()
    {
        $command = '*124#';
        $this->sendCommand($command);
        $pattern = str_replace('*','\\*',$command);
        $this->assertRegExp('/\"AT\+CUSD=1, '.$pattern.' ,15\"/',
            $this->param,
            'Check if the send command exist in shell execute');
    }

    public function testGetRawUssdUCS2()
    {
        $this->sendCommand();
        $this->ussdQuery->parseResult();
        $this->assertNotEmpty($this->ussdQuery->getUSSDTextMessage());

    }


    /**
     * @expectedException \Exception
     */
    public function testGetRawUssdUCS2Exception()
    {
        $array = $this->ussdQuery->getRawUssdUCS2();
    }


    public function testGetTextResult()
    {
        $this->sendCommand();
        $textMessage = $this->ussdQuery->getTextResult();
        //var_dump($this->ussdQuery->getUSSDRequireReply());
        $this->assertNotEmpty($textMessage);
    }

    public function testGetJsonResult()
    {
        $this->sendCommand();
        $jsonMessage = $this->ussdQuery->getJsonResult();
        $obj = json_decode($jsonMessage);
        //var_dump($obj);

        $this->assertObjectHasAttribute('payload',$obj,'');
        $this->assertObjectHasAttribute('message',$obj->payload,'');
        $this->assertObjectHasAttribute('needReply',$obj->payload,'');
        $this->assertObjectHasAttribute('error',$obj,'');
        $this->assertObjectHasAttribute('success',$obj,'');

        $this->assertAttributeNotEmpty('message',$obj->payload,'');
        $this->assertTrue($obj->success,'Success attribute value should be true');
    }

    public function testErrorGetJsonResult()
    {
        $jsonMessage = $this->ussdQuery->getJsonResult();
        $obj = json_decode($jsonMessage);
        //var_dump($obj);

        $this->assertObjectHasAttribute('payload',$obj,'');
        $this->assertObjectHasAttribute('exception',$obj->error,'');
        $this->assertObjectHasAttribute('error',$obj,'');
        $this->assertObjectHasAttribute('success',$obj,'');

        $this->assertFalse($obj->success,'Success attribute value should be false');
    }


}

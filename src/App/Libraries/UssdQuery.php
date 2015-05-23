<?php
/**
 * Created by PhpStorm.
 * User: Mahadir
 * Date: 5/23/2015
 * Time: 12:42 PM
 */

namespace App\Libraries;

use \Swiftlet\Abstracts\Library as LibraryAbstract;
use Swiftlet\Exception;

class UssdQuery extends LibraryAbstract {

    /**
     * @var string
     */
    protected $chatFile = '/usr/sbin/chat';
    /**
     * @var string
     */
    protected $chatArgs = '-E -v -s TIMEOUT 3 2>&1';
    /**
     * @var array
     */
    protected $shell;
    /**
     * @var
     */
    protected $gsmResultReceived;
    /**
     * @var string
     */
    protected $serialDevice;

    /**
     * @var string
     */
    protected $uSSDTextMessage;
    /**
     * @var bool
     */
    protected $uSSDRequireReply;

    /**
     * @var string
     */
    protected $uSSDUCS2Raw;

    /**
     * @param string $uSSDTextMessage
     */
    public function setUSSDTextMessage($uSSDTextMessage)
    {
        $this->uSSDTextMessage = $uSSDTextMessage;
    }

    /**
     * @param boolean $uSSDRequireReply
     */
    public function setUSSDRequireReply($uSSDRequireReply)
    {
        $this->uSSDRequireReply = $uSSDRequireReply;
    }

    /**
     * @return string
     */
    public function getUSSDTextMessage()
    {
        return $this->uSSDTextMessage;
    }

    /**
     * @return bool
     */
    public function getUSSDRequireReply()
    {
        return $this->uSSDRequireReply;
    }

    /**
     * @return string
     */
    public function getChatFile()
    {
        return $this->chatFile;
    }

    /**
     * @param string $chatFile
     */
    public function setChatFile($chatFile)
    {
        $this->chatFile = $chatFile;
    }

    /**
     * @return string
     */
    public function getChatArgs()
    {
        return $this->chatArgs;
    }

    /**
     * @param string $chatArgs
     */
    public function setChatArgs($chatArgs)
    {
        $this->chatArgs = $chatArgs;
    }

    /**
     * @return array
     */
    public function getShell()
    {
        return $this->shell;
    }

    /**
     * @param array $shell
     */
    public function setShell($shell)
    {
        $this->shell = $shell;
    }

    /**
     * @return mixed
     */
    public function getGsmResultReceived()
    {
        return $this->gsmResultReceived;
    }

    /**
     * @param mixed $gsmResultReceived
     */
    public function setGsmResultReceived($gsmResultReceived)
    {
        $this->gsmResultReceived = $gsmResultReceived;
    }

    /**
     * @return string
     */
    public function getSerialDevice()
    {
        return $this->serialDevice;
    }

    /**
     * @param string $serialDevice
     */
    public function setSerialDevice($serialDevice)
    {
        $this->serialDevice = $serialDevice;
    }

    public function __construct($serialDevice = '/dev/ttyUSB1')
    {
        $this->serialDevice = $serialDevice;
        $this->shell = array($this,'_shellExecute');
    }


    /**
     * Send command to GSM
     * @param $command
     * @return $this
     */
    public function sendCommand($command)
    {
        $construct = sprintf('%s %s "" "AT&F&C1&D2" "OK" "AT+CMGF=0" "OK" "AT+CUSD=1, %s ,15" "xx"  >  %s < %s',
            $this->chatFile,
            $this->chatArgs,
            $command,
            $this->serialDevice,
            $this->serialDevice);
        $chatResult = call_user_func_array($this->shell,array($construct));
        $this->gsmResultReceived = $chatResult;
        return $this;
    }

    /**
     * @return string
     */
    public function getTextResult()
    {
        try
        {
            $this->parseResult();
            return $this->decodeUCS2_16($this->uSSDTextMessage);
        }
        catch (Exception $e)
        {
        }

        return '';
    }

    /**
     * @return string
     */
    public function getJsonResult()
    {
        $result = array('success'=>false,'payload'=>array(),'error'=>array());
        try
        {
            $this->parseResult();
            return $this->getJsonFormatted();

        }
        catch (Exception $e)
        {
            $result['error'] = array(
              'exception' => $e->getMessage()
            );
        }

        return json_encode($result);
    }

    /**
     * @return string
     */
    public function getJsonFormatted()
    {
        $result = array('success'=>false,'payload'=>array(),'error'=>array());
        $result['success'] = true;
        $result['payload'] = array(
            'message' => $this->uSSDTextMessage,
            'needReply' => $this->uSSDRequireReply
        );
        return json_encode($result);
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function parseResult()
    {
        preg_match('/\+CUSD: (\d),"([\s\S]+?)"/',$this->gsmResultReceived,$matches);
        if(count($matches)<1)
        {
            throw new Exception('GSM USSD result not recognized');
        }
        $this->uSSDRequireReply = $matches[0]==1 ? true:false;
        $this->uSSDTextMessage = $this->decodeUCS2_16($matches[2]);
        $this->uSSDUCS2Raw = $matches[2];
        return $this;
    }


    /**
     * @param $messages
     * @return string
     */
    protected function decodeUCS2_16($messages)
    {
        $decodedMessage = "";
        $charCounter = 0;

        //try to clean up the input
        $input = preg_replace('/[\n\s]/','',$messages);

        // Cut the input string into pieces of 4
        for($i=0;$i<strlen($input);$i=$i+4)
        {
            $hex1 = substr($input,$i,2);
            $hex2 = substr($input,$i+2,2);
            $charCounter++;
            $decimal = hexdec($hex1)*256+hexdec($hex2);
            $decodedMessage .=  chr($decimal);
        }
        return $decodedMessage;
    }

    /**
     * Execute shell command
     * @param $command
     * @return string
     */
    private function _shellExecute($command)
    {
        return shell_exec($command);
    }

}
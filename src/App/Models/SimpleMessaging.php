<?php

namespace App\Models;

use \Swiftlet\Abstracts\Model as ModelAbstract;
use Swiftlet\Exception;
use Carbon\Carbon;

/**
 * SimpleMessaging model
 */
class SimpleMessaging extends ModelAbstract
{

    /**
     * The body of the message
     * @var string
     */
    private $text;

    /**
     * Sender fully qualified telephone number
     * @var string
     */
    private $fromNumber;

    /**
     * Receiver fully qualified telephone number
     * @var string
     */
    private $toNumber;

    /**
     * The sent dd-mm-yyyy HH:M:s date time
     * @var Carbon;
     */
    private $SentDateTime;

    /**
     * The receive  date time
     * @var Carbon
     */
    private $ReceiveDateTime;


    /**
     * The filename of message on disk
     * @var string
     */
    private $fileName;


    /**
     * International Mobile Subscriber Identity
     * @var string
     */
    private $IMSI;


    /**
     * example: 15-05-19 15:37:53
     * @var string
     */
    private $gsmDateTimeFormat = 'y-m-d H:i:s';



    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return mixed
     */
    public function getFromNumber()
    {
        return $this->fromNumber;
    }

    /**
     * @param mixed $fromNumber
     * @return $this
     */
    public function setFromNumber($fromNumber)
    {
        $this->fromNumber = $fromNumber;
        return $this;
    }

    /**
     * @param mixed $text
     * @return $this
     * @throws Exception
     */
    public function setText($text)
    {
        if(strlen($text)>160)
        {
            throw new Exception('Simple Messaging text must not exceeds 160 characters');
        }
        $this->text = $text;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToNumber()
    {
        return $this->toNumber;
    }

    /**
     * @param mixed $toNumber
     * @return $this
     * @throws Exception
     */
    public function setToNumber($toNumber)
    {
        //probably forgot to append country code eg: +6
        if(preg_match("/^[\d]{10}$/",$toNumber))
        {
            $toNumber = "+6".$toNumber;
        }

        if(!$this->checkValidPhoneNumber($toNumber))
        {
            throw new Exception('Invalid phone number:'.$toNumber);
        }


        $this->toNumber = $toNumber;
        return $this;
    }

    /**
     * Check for valid mobile identification number (MIN)
     * @param $phoneNumber
     * @return bool
     */
    public function checkValidPhoneNumber($phoneNumber)
    {
        //valid phone number ex: +60196547332
        if(!preg_match("/^\+[\d]{11}$/",$phoneNumber)){
            //check for short code
            //this short code valid for Malaysia
            //5 in length
            if(preg_match("/^[\d]{5}$/",$phoneNumber)){
                //append leading s
                $phoneNumber = "s" . $phoneNumber;
            }
            else{
                return false;
            }
        }
        return true;
    }

    /**
     * @return Carbon
     */
    public function getSentDateTime()
    {
        return $this->SentDateTime;
    }

    /**
     * @param mixed $datetime
     * @return $this
     */
    public function setSentDateTime($datetime)
    {
        $this->SentDateTime = Carbon::createFromFormat($this->gsmDateTimeFormat, $datetime);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param mixed $fileName
     * @return $this
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getReceiveDateTime()
    {
        return $this->ReceiveDateTime;
    }

    /**
     * @param string $ReceiveDateTime
     * @return $this;
     */
    public function setReceiveDateTime($ReceiveDateTime)
    {
        $this->ReceiveDateTime = Carbon::createFromFormat($this->gsmDateTimeFormat, $ReceiveDateTime);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIMSI()
    {
        return $this->IMSI;
    }

    /**
     * @param mixed $IMSI
     * @return $this
     */
    public function setIMSI($IMSI)
    {
        $this->IMSI = $IMSI;
        return $this;
    }

}

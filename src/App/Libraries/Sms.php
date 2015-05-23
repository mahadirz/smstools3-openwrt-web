<?php

/**
 * @author Mahadir Ahmad
 * @copyright 2014
 */

namespace App\Libraries;

use \Swiftlet\Abstracts\Library as LibraryAbstract;
use \App\Models\SimpleMessaging;
use Swiftlet\Exception;

class Sms extends LibraryAbstract
{
    /**
     * SMS tools3 Sent directory
     * @var string
     */
    private $sentDir;

    /**
     * SMS tools3 inbox directory
     * @var string
     */
    private $inboxDir;

    /**
     * SMS tools3 outbox directory
     * @var string
     */
    private $outboxDir;

    /**
     * Directory to store SMS templates
     * @var string
     */
    private $templateDir;

    /**
     * @return string
     */
    public function getInboxDir()
    {
        return $this->inboxDir;
    }

    /**
     * @param string $inboxDir
     */
    public function setInboxDir($inboxDir)
    {
        $this->inboxDir = $inboxDir;
    }

    /**
     * @return string
     */
    public function getOutboxDir()
    {
        return $this->outboxDir;
    }

    /**
     * @param string $outboxDir
     */
    public function setOutboxDir($outboxDir)
    {
        $this->outboxDir = $outboxDir;
    }

    /**
     * @return string
     */
    public function getTemplateDir()
    {
        return $this->templateDir;
    }

    /**
     * @param string $templateDir
     */
    public function setTemplateDir($templateDir)
    {
        $this->templateDir = $templateDir;
    }



    /**
     * @param $sentDir
     * @param $inboxDir
     * @param $outboxDir
     * @param $templateDir
     */
    public function __construct($sentDir,$inboxDir,$outboxDir,$templateDir){
        $this->sentDir = $sentDir;
        $this->inboxDir = $inboxDir;
        $this->outboxDir = $outboxDir;
        $this->templateDir = $templateDir;
    }

    /**
     * @return string
     */
    public function getSentDir()
    {
        return $this->sentDir;
    }

    /**
     * @param string $sentDir
     */
    public function setSentDir($sentDir)
    {
        $this->sentDir = $sentDir;
    }



    /**
     * Sort array descending of datetime
     * @param SimpleMessaging $first
     * @param SimpleMessaging $second
     * @return int
     * @internal param array $firstArray
     * @internal param array $secondArray
     * @internal param $keyName
     */
    private function callBackSortInboxByDateTimeDesc(SimpleMessaging $first,SimpleMessaging $second){
        //print_r(array($second,$first));
        $val = $second->getReceiveDateTime()->timestamp - $first->getReceiveDateTime()->timestamp;
        return $val;
    }

    /**
     * Sort array descending of datetime
     * @param SimpleMessaging $first
     * @param SimpleMessaging $second
     * @return int
     * @internal param array $firstArray
     * @internal param array $secondArray
     * @internal param $keyName
     */
    private function callBackSortSentboxByDateTimeDesc(SimpleMessaging $first,SimpleMessaging $second){
        $val = strtotime($second->getSentDateTime()->timestamp)-strtotime($first->getSentDateTime()->timestamp);
        return $val;
    }

    /**
     * @param array $array
     * @return array
     */
    private function sortByDatetimeDesc(array &$array,$sorter='callBackSortInboxByDateTimeDesc'){
        usort($array,array($this, $sorter));
        return $array;
    }

    /**
     * @return array
     */
    public function getTemplates(){
        $arr = array();
        
        if ($handle = opendir($this->templateDir)){
            while (false !== ($file = readdir($handle))){
                if ($file != "." && $file != ".."){
                    $contents = file_get_contents($this->templateDir . "/" . $file);
                    preg_match('/To:[\s]s?([\s\S]+?)\n/', $contents, $matches_to);
                    preg_match('/[\n]{2}([\s\S]+)/', $contents, $matches_text);

                    if($matches_to || $matches_text )
                    {
                        $simpleMessaging = new SimpleMessaging();
                        $simpleMessaging
                            ->setToNumber($matches_to[1])
                            ->setText(trim($matches_text[1]))
                            ->setFileName($file);

                        $arr[] = $simpleMessaging;
                    }

                }
            }
        }
        return $arr;
    }

    /**
     * @return int
     */
    public function getTotalInbox()
    {
        return $this->getTotalBox($this->inboxDir);
    }

    /**
     * @return int
     */
    public function getTotalSentBox()
    {
        return $this->getTotalBox($this->sentDir);
    }

    /**
     * @return int
     */
    public function getTotalOutgoingBox()
    {
        return $this->getTotalBox($this->outboxDir);
    }

    /**
     * @param $box
     * @return int
     */
    protected function getTotalBox($box)
    {
        $count = 0;
        if ($handle = opendir($box))
        {
            while (false !== ($file = readdir($handle)))
            {
                if ($file != "." && $file != "..")
                {
                    $count++;
                }
            }
        }
        return $count;
    }


    /**
     * Get all SMS in inbox
     * @return array
     */
    public function getInBox()
    {
        
        $arr = array();
        
        if ($handle = opendir($this->inboxDir)) {
            
            /* loop to the directory */
            while (false !== ($file = readdir($handle))) {
                
                if ($file != "." && $file != "..") {
                    $contents = file_get_contents($this->inboxDir . "/" . $file);
                    preg_match('/From:[\s]([\s\S]+?)\n/', $contents, $matches_from);
                    preg_match('/Sent:[\s]([\s\S]+?)\n/', $contents, $matches_sentDateTime);
                    preg_match('/Received:[\s]([\s\S]+?)\n/', $contents, $matches_receiveDateTime);
                    preg_match('/IMSI:[\s]([\s\S]+?)\n/', $contents, $matches_IMSI);
                    preg_match('/[\n]{2}([\s\S]+)/', $contents, $matches_text);

                    if(count($matches_from)>0){
                        //change datetime format
                        //$sentDateTime = date("d/m/Y h:i:s A",strtotime(trim($matches_sentDateTime[1])));

                        $simpleMessaging = new SimpleMessaging();
                        $simpleMessaging
                            ->setFromNumber(trim($matches_from[1]))
                            ->setSentDateTime(trim($matches_sentDateTime[1]))
                            ->setReceiveDateTime(trim($matches_receiveDateTime[1]))
                            ->setText(trim($matches_text[1]))
                            ->setFileName($file);

                        $arr[] = $simpleMessaging;
                    }
                    

                }
                
                
            }
            closedir($handle);
        }
        $this->sortByDatetimeDesc($arr);
        return $arr;
    }

    /**
     * Get all SMS in outgoing box
     * @return array
     */
    public function getOutgoingBox()
    {

        $arr = array();

        if ($handle = opendir($this->outboxDir)) {


            /* loop to the directory */
            while (false !== ($file = readdir($handle))) {

                if ($file != "." && $file != "..") {
                    $contents = file_get_contents($this->outboxDir . DIRECTORY_SEPARATOR . $file);
                    preg_match('/To:[\s]s?([\s\S]+?)\n/', $contents, $matches_to);
                    preg_match('/[\n]{2}([\s\S]+)/', $contents, $matches_text);


                    if(count($matches_to)>0){
                        //change datetime format
                        //$sentDateTime = date("d/m/Y h:i:s A",strtotime(trim($matches_sentDateTime[1])));

                        $simpleMessaging = new SimpleMessaging();
                        $simpleMessaging
                            ->setFromNumber($matches_to[1])
                            ->setText(trim($matches_text[1]))
                            ->setFileName($file);

                        $arr[] = $simpleMessaging;
                    }
                }


            }


            closedir($handle);
        }
        $this->sortByDatetimeDesc($arr);
        return $arr;
    }


    /**
     * Get all SMS in sandbox
     * @return array
     */
    public function getSentBox()
    {
        
        $arr = array();
        
        if ($handle = opendir($this->sentDir)) {
            
            
            /* loop to the directory */
            while (false !== ($file = readdir($handle))) {
                
                if ($file != "." && $file != "..") {
                    $contents = file_get_contents($this->sentDir . "/" . $file);
                    preg_match('/To:[\s]s?([\s\S]+?)\n/', $contents, $matches_to);
                    preg_match('/Sent:[\s]([\s\S]+?)\n/', $contents, $matches_sentDateTime);
                    preg_match('/[\n]{2}([\s\S]+)/', $contents, $matches_text);


                    if(count($matches_to)>0){
                        //change datetime format
                        //$sentDateTime = date("d/m/Y h:i:s A",strtotime(trim($matches_sentDateTime[1])));

                        $simpleMessaging = new SimpleMessaging();
                        $simpleMessaging
                            ->setFromNumber(trim($matches_to[1]))
                            ->setSentDateTime(trim($matches_sentDateTime[1]))
                            ->setText(trim($matches_text[1]))
                            ->setFileName($file);

                        $arr[] = $simpleMessaging;
                    }
                }
                
                
            }
            
            
            closedir($handle);
        }
        $this->sortByDatetimeDesc($arr,'callBackSortSentboxByDateTimeDesc');
        return $arr;
    }


    /**
     * Compose new text message
     * @param SimpleMessaging $simpleMessaging
     * @return string
     * @throws Exception
     */
    public function compose(SimpleMessaging $simpleMessaging)
    {
        if($simpleMessaging->getToNumber() == "")
        {
            throw new Exception('Receiver Number is compulsory');
        }

        $smsBody = "To: "
            .$simpleMessaging->getToNumber()
            ."\n\n"
            .$simpleMessaging->getText()
            ."\n";

        return $smsBody;
    }

    /**
     * @param SimpleMessaging $simpleMessaging
     * @return bool
     * @throws Exception
     */
    public function sendTextMessage(SimpleMessaging $simpleMessaging)
    {
        $smsBody = $this->compose($simpleMessaging);
        $filename   = $this->outboxDir
            . "/simplesms_"
            . date("siH");
        file_put_contents($filename, $smsBody);

        if (file_exists($filename))
            return true;

        return false;
    }

    /**
     * @param SimpleMessaging $simpleMessaging
     * @return bool
     * @throws Exception
     */
    public function saveTextMessage(SimpleMessaging $simpleMessaging)
    {
        $smsBody = $this->compose($simpleMessaging);
        $filename   = $this->templateDir
            . "/simplesms_"
            . date("siH");
        file_put_contents($filename, $smsBody);

        if (file_exists($filename))
            return true;

        return false;
    }

    /**
     * Delete SMS from Sent Box
     * @param $filename
     * @return bool
     */
    public function deleteSentMessage($filename)
    {
        $fileNamePath = $this->sentDir.DIRECTORY_SEPARATOR."$filename";
        return unlink($fileNamePath);
    }

    /**
     * Delete SMS from InBox
     * @param $filename
     * @return bool
     */
    public function deleteInboxMessage($filename)
    {
        $fileNamePath = $this->inboxDir.DIRECTORY_SEPARATOR."$filename";
        return unlink($fileNamePath);
    }

    /**
     * Delete SMS from Templates
     * @param $filename
     * @return bool
     */
    public function deleteTemplateMessage($filename)
    {
        $fileNamePath = $this->templateDir.DIRECTORY_SEPARATOR."$filename";
        return unlink($fileNamePath);
    }

    /**
     * Delete SMS from outgoing
     * @param $filename
     * @return bool
     */
    public function deleteOutgoingMessage($filename)
    {
        $fileNamePath = $this->outboxDir.DIRECTORY_SEPARATOR."$filename";
        return unlink($fileNamePath);
    }

    
    
}



?>
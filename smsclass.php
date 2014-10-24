<?php

/**
 * @author Mahadir Ahmad
 * @copyright 2014
 */

class SMS
{
    private $sentDir = "/usr/share/smstools3/sms/sent";
    private $inboxDir = "/usr/share/smstools3/sms/incoming";
    private $outboxDir = "/usr/share/smstools3/sms/outgoing";
    private $templateDir = "templates";
    
    public static $last_error;
    
    private function sortBySubkey(&$array, $subkey, $sortType = SORT_ASC) {
        foreach ($array as $subarray) {
            $keys[] = $subarray[$subkey];
        }
        array_multisort($keys, $sortType, $array);
    }
    
    private function sortfunc($a,$b){
        //sort desc
        $val = strtotime($b["rdatetime"])-strtotime($a["rdatetime"]);
        return $val;
    }
    
    private function sortByDatetimeDesc(&$array){
        uasort($array,array($this, "sortfunc"));
        return $array;
    }
    
    public function get_templates(){
        $arr = array();
        
        if ($handle = opendir($this->templateDir)){
            while (false !== ($file = readdir($handle))){
                if ($file != "." && $file != ".."){
                    $contents = file_get_contents($this->templateDir . "/" . $file);
                    preg_match('/To:[\s]s?([\s\S]+?)\n/', $contents, $matches_to);
                    preg_match('/[\n]{2}([\s\S]+)/', $contents, $matches_text);
                    
                    $arr[] = array(
                        "to" => trim($matches_to[1]),
                        "text" => trim($matches_text[1]),
                        "filename" => $file
                    );
                }
            }
        }
        return $arr;
    }
    
    
    public function get_inbox()
    {
        
        $arr = array();
        
        if ($handle = opendir($this->inboxDir)) {
            
            /* loop to the directory */
            while (false !== ($file = readdir($handle))) {
                
                if ($file != "." && $file != "..") {
                    $contents = file_get_contents($this->inboxDir . "/" . $file);
                    preg_match('/From:[\s]([\s\S]+?)\n/', $contents, $matches_from);
                    preg_match('/Sent:[\s]([\s\S]+?)\n/', $contents, $matches_datetime);
                    preg_match('/[\n]{2}([\s\S]+)/', $contents, $matches_text);
                    
                    //change datetime format
                    $datetime = date("d/m/Y h:i:s A",strtotime(trim($matches_datetime[1])));
                    
                    $arr[] = array(
                        "datetime" => $datetime,
                        "rdatetime"=> trim($matches_datetime[1]),
                        "from" => trim($matches_from[1]),
                        "text" => trim($matches_text[1]),
                        "filename" => $file
                    );
                }
                
                
            }
            closedir($handle);
        }
        $this->sortByDatetimeDesc($arr);
        return $arr;
    }
    
    
    public function get_sent()
    {
        
        $arr = array();
        
        if ($handle = opendir($this->sentDir)) {
            
            
            /* loop to the directory */
            while (false !== ($file = readdir($handle))) {
                
                if ($file != "." && $file != "..") {
                    $contents = file_get_contents($this->sentDir . "/" . $file);
                    preg_match('/To:[\s]s?([\s\S]+?)\n/', $contents, $matches_to);
                    preg_match('/Sent:[\s]([\s\S]+?)\n/', $contents, $matches_datetime);
                    preg_match('/[\n]{2}([\s\S]+)/', $contents, $matches_text);
                    
                    //change datetime format
                    $datetime = date("d/m/Y h:i:s A",strtotime(trim($matches_datetime[1])));
                    
                    $arr[] = array(
                        "datetime" => $datetime,
                        "rdatetime"=> trim($matches_datetime[1]),
                        "to" => trim($matches_to[1]),
                        "text" => trim($matches_text[1]),
                        "filename" => $file
                    );
                }
                
                
            }
            
            
            closedir($handle);
        }
        $this->sortByDatetimeDesc($arr);
        return $arr;
    }
    
    
    public function compose_sms($to, $text,$location=null)
    {
        //default write location
        if(is_null($location)){
            $location=$this->outboxDir;
        }
        
        if (strlen($text >= 160)){
            SMS::$last_error = "Text length exceeded";
            return false;
        }
            
            
        //probably forgot to append +6
        if(preg_match("/^[\d]{10}$/",$to)){
            $to = "+6".$to;
        }
            
        //valid phone number ex: +60196547332
        if(!preg_match("/^\+[\d]{11}$/",$to)){
            //check for short code
            //this short code valid for Malaysia
            //5 in length
            if(preg_match("/^[\d]{5}$/",$to)){
                //append leading s
                $to = "s" . $to;
            }   
            else{
                SMS::$last_error = "Invalid Number";
                return false;
            }         
        }

        $smscontent = "To: $to\n\n$text\n";
        $filename   = $location . "/simplesms_" . date("siH");
        file_put_contents($filename, $smscontent);
        
        if (file_exists($filename))
            return 1;
        else{
            SMS::$last_error = "Failed write to ".$location;
            return false;
        }
    }
    
    public function delete_sms($type = 1, $filename)
    {
        if($type == 0){
            //template
            unlink($this->templateDir . "/$filename");
        }
        else if ($type == 1) {
            //inbox
            unlink($this->inboxDir . "/$filename");
        } else if($type == 2){
            //sent 
            unlink($this->sentDir."/$filename");
        }
        
    }
    
    
}



?>
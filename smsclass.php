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
    
    
    function get_inbox()
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
                    
                    $arr[] = array(
                        "datetime" => trim($matches_datetime[1]),
                        "from" => trim($matches_from[1]),
                        "text" => trim($matches_text[1]),
                        "filename" => $file
                    );
                }
                
                
            }
            closedir($handle);
        }
        
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
                    
                    
                    $arr[] = array(
                        "datetime" => trim($matches_datetime[1]),
                        "to" => trim($matches_to[1]),
                        "text" => trim($matches_text[1]),
                        "filename" => $file
                    );
                }
                
                
            }
            
            
            closedir($handle);
        }
        return $arr;
    }
    
    
    public function compose_sms($type, $to, $text)
    {
        if (strlen($text >= 160))
            return -1;
        
        if ($type == 2) {
            //short number
            //add leading s
            $to = "s" . $to;
        }
        $smscontent = "To: $to\n\n$text\n";
        $filename   = $this->outboxDir . "/simplesms_" . date("siH");
        file_put_contents($filename, $smscontent);
        
        if (file_exists($filename))
            return 1;
        else
            return 0;
    }
    
    public function delete_sms($type = 1, $filename)
    {
        if ($type == 1) {
            //inbox
            unlink($this->inboxDir . "/$filename");
        } else {
            //sent 
            unlink($this->sentDir."/$filename");
        }
        
    }
    
    
}



?>
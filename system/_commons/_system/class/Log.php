<?php
    namespace backend;
    
    class Log{

        public static function log($action, $msg, $oldData="..."){ 
            if( !file_exists($totemErrorFile = "logs/".date('Y-m')."_-_log.md") ){
               $md  = "| Date                | User                             | Action                      | Message                                                                                                                                                                               | Backup Data                                                                                                                                                                            |\r\n";
               $md .= "|:-------------------:| --------------------------------:|:---------------------------:|:------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |\r\n"; 
            }else{
               $md = file_get_contents($totemErrorFile);
            }
            
            $md = trim($md);
            $date = date("d/m/Y H:i:s");   
            
            $action = trim($action);
            $action = str_pad( strtoupper($action) , 33, " ");
            
            $msg = trim($msg);
            $msg = str_pad($msg, 184, " ");
            
            $oldData = (is_array($oldData)) ? json_encode($oldData) : $oldData;
            $oldData = trim($oldData);
            $oldData = str_pad($oldData, 182, " ");
            
            $user = (!empty($_SESSION['user']['login']))? $_SESSION['user']['login'] : "..." ;
            $user = str_pad($user, 32, " ", STR_PAD_LEFT);
            
            $md .= "\r\n| $date | $user | $action | $msg | $oldData |";
            
            file_put_contents($totemErrorFile, $md);
        }
        
    }
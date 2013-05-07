<?php
    namespace backend;
    
    class Log{

        public static function log($action, $msg, $oldData="..."){ 
            if( !file_exists($totemErrorFile = "logs/".date('Y-m')."_-_log.md") ){
               $md  = "| Date                | Session ID                       | User                             | Action                            | Message                                                                                                                                                                               | Backup Data  | \r\n";
               $md .= "|:-------------------:|:-------------------------------- | --------------------------------:|:---------------------------------:|:------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ------------ | \r\n"; 
            }else{
               $md = file_get_contents($totemErrorFile);
            }
            
            $md = trim($md);
            
            $date = date("d/m/Y H:i:s");   
            
            $action = utf8_decode($action); // POG: str_pad não calcula direito sem isso
            $action = str_pad( strtoupper($action) , 33, " ");
            $action = utf8_encode($action); // POG: str_pad não calcula direito sem isso
            
            $msg = utf8_decode($msg); // POG: str_pad não calcula direito sem isso
            $msg = str_pad($msg, 181, " ", STR_PAD_RIGHT);
            $msg = utf8_encode($msg); // POG: str_pad não calcula direito sem isso
            
            $oldData = (is_array($oldData)) ? json_encode($oldData) : utf8_decode($oldData);
            
            $user = (!empty($_SESSION['user']['login']))? $_SESSION['user']['login'] : "..." ;
            $user = utf8_decode($user); // POG: str_pad não calcula direito sem isso
            $user = str_pad($user, 32, " ", STR_PAD_LEFT);
            $user = utf8_encode($user); // POG: str_pad não calcula direito sem isso
            
            $session = session_id();
            $session = str_pad($session, 32, " ");
        
            $md .= "\r\n| $date | $session | $user | $action | $msg | $oldData |";
            
            file_put_contents($totemErrorFile, $md);
        }
        
    }
<?php
    namespace backend;

    use backend\Util;

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

            $action = Util::mb_str_pad( strtoupper($action) , 33, " ");

            $msg = Util::mb_str_pad($msg, 181, " ", STR_PAD_RIGHT);

            $oldData = (is_array($oldData)) ? json_encode($oldData) : utf8_decode($oldData);

            $user = (!empty($_SESSION['user']['login']))? $_SESSION['user']['login'] : "..." ;
            $user = Util::mb_str_pad($user, 32, " ", STR_PAD_LEFT);

            $session = session_id();
            $session = str_pad($session, 32, " ");

            $md .= "\r\n| $date | $session | $user | $action | $msg | $oldData |";

            file_put_contents($totemErrorFile, $md);
        }

    }
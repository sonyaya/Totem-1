<?php
    namespace backend;

    class Log{
        private static $logFile = "logs/aplication-errors.txt";

        public static function error($msg){
            error_log("[ ".date('Y-m-d H:i:s')." ][ error ] $msg \r\n", 3, self::$logFile);
            die("an error ocurred  please check the ". self::$logFile ." for more details!");
        }

        public static function warning($msg){
            error_log("[ ".date('Y-m-d H:i:s')." ][ warning ] $msg \r\n", 3, self::$logFile);
        }

    }
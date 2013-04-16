<?php
    namespace backend;

    class Log{
        private static $logFile = "logs/aplication-errors.txt";

        public static function error($no, $msg){ exit; }

        public static function warning($no, $msg){}
        
        public static function ajaxError($no, $msg){ exit; }

    }
<?php

    # -- START DEFAULT SESSION -------------------------------------------------

    session_start();

    # -- SET DEFAULT PHP CHARSET -----------------------------------------------

    ini_set('default_charset','UTF-8');

    # -- VERIFICAÇÃO DE DEPENDENCIAS PHP ---------------------------------------

    if( !isset($_SESSION['php_check']) || !$_SESSION['php_check'] ){
        // Extensões Carregadas
        $ext = get_loaded_extensions();

        // Extensões Necessárias
        $extNeeded = Array(
            "PDO",
            "pdo_mysql",
            "openssl",
            "session"
        );

        // Versão minima do php
        $minPHPVersion = "5.3.0";

        // Validação de requisitos minimos
        if( 
            $extNeeded == array_intersect($extNeeded , $ext) &&
            version_compare(phpversion(), $minPHPVersion, ">=") 
        ){
            $_SESSION['php_check'] = true;
        }else{
            $_SESSION['php_check'] = false;
        }
    }

    # -- AUTOLOAD --------------------------------------------------------------

    spl_autoload_register( 
        function($str){
            $pathOrig  = explode("\\", $str);
            $action    = array_shift($pathOrig);
            $file      = array_pop($pathOrig);
            $path      = implode("/", $pathOrig);

            switch($action){
                case "backend" :
                    if(file_exists($exists_file = "_system/class/$file.php")){
                       require_once $exists_file;
                    }else{
                        require_once "../_commons/_system/class/$file.php";
                    }
                    break;

                case "vendor" :
                    if(file_exists($exists_file = "_system/vendor/$path/$file.php")){
                       require_once $exists_file;
                    }else{
                        require_once "../_commons/_system/vendor/$path/$file.php";
                    }
                    break;
            }

        }
    );

    # -- CONFIGURAÇÕES ---------------------------------------------------------

    if(file_exists("../config.ini.php")){
        $_M_CONFIG = (object)parse_ini_file("../config.ini.php", true);

        // CAMINHO DO PATH-UPLOAD RELATIVO A PASTA SYSTEM
        $_M_CONFIG->system['upload-path'] = "../{$_M_CONFIG->system['upload-path']}";
        
        // CONFIGURAÇÕES PARA ESTA APLICAÇÃO
        $_M_THIS_CONFIG = $_M_CONFIG->backend;
    }else{
        die("Configuration file ../config.ini.php not found!");
    }

    # -- DEFINE TIME ZONE ATUAL ------------------------------------------------

    date_default_timezone_set( $_M_CONFIG->system['time-zone'] );

    # -- ERROR CONTROL ---------------------------------------------------------
    
    if( $_M_CONFIG->system['log-php-errors'] ){
        
        # -- SAVE PHP FATAL ERROS IN A FILE ------------------------------------
    
        error_reporting(E_ALL);
        ini_set ("display_errors" , "off");
        ini_set ("log_errors"     , "On");
        ini_set ("error_log"      , "logs/".date('Y-m')."_-_fatal-errors.txt");
        
        # -- SAVE ERROS IN A FILE ----------------------------------------------
        
        function error_handler($errno, $errstr, $errfile, $errline, $errcontext){
            
            //
            if( !file_exists($totemErrorFile = __DIR__ . "/logs/".date('Y-m')."_-_errors.md") ){
                $md  = "| Date                | System  | Error Num. | Error Type                                                           | Error Line | Description                                                                                                                                                                            | File                                                                                                                                                                                   | \r\n";
                $md .= "|:-------------------:|:-------:|:----------:|:--------------------------------------------------------------------:| ----------:|:-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |:-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | \r\n"; 
            }else{
                $md = "";
            }
            
            //
            $md = trim($md);
            $date = date("d/m/Y H:i:s");        
            $errno   = str_pad( str_pad($errno  , 4, " ", STR_PAD_LEFT) , 10 , " ", STR_PAD_BOTH);
            $errline = str_pad($errline, 10 , " ", STR_PAD_LEFT);
            $errstr  = str_pad(utf8_decode($errstr) , 182, " ");
            $errfile = str_pad(utf8_decode($errfile), 182, " ");

            //
            switch ($errno) {
                case E_ERROR:
                    $md .= "\r\n| $date |   PHP   | $errno | <span style='color:#FFFF00; background:#E13C26'> FATAL ERROR </span> | $errline | $errstr | $errfile |";

                    // Separando os traceback no log de erros fatais
                    $fileFatalErrors = fopen(__DIR__. "/logs/".date('Y-m')."_-_fatal-errors.txt", "a+");
                    fwrite($fileFatalErrors, "\r\n-------------------------------------------\r\n\r\n");
                    fclose($fileFatalErrors);
                    
                    break;
                
                case E_USER_ERROR:
                    $md .= "\r\n| $date |  TOTEM  | $errno | <span style='color:#E13C26'> E_USER_ERROR                    </span> | $errline | $errstr | $errfile |";
                    break;                  
            
                case E_WARNING:                  
                    $md .= "\r\n| $date |   PHP   | $errno | <span style='color:#F88B1C'> E_WARNING                       </span> | $errline | $errstr | $errfile |";
                    break;                  
            
                case E_USER_WARNING:                  
                    $md .= "\r\n| $date |  TOTEM  | $errno | <span style='color:#F88B1C'> E_USER_WARNING                  </span> | $errline | $errstr | $errfile |";
                    break;                  
            
                case E_NOTICE:                  
                    $md .= "\r\n| $date |   PHP   | $errno | <span style='color:#6A9D27'> E_NOTICE                        </span> | $errline | $errstr | $errfile |";
                
                case E_USER_NOTICE:                  
                    $md .= "\r\n| $date |  TOTEM  | $errno | <span style='color:#3171B2'> E_USER_NOTICE                   </span> | $errline | $errstr | $errfile |";
                    break;                  

                case E_RECOVERABLE_ERROR:
                    $md .= "\r\n| $date |   PHP   | $errno | <span style='color:#E13C26'> E_RECOVERABLE_ERROR             </span> | $errline | $errstr | $errfile |";
                    break;

                default:
                    $md .= "\r\n| $date |   ???   | $errno | <span style='color:#000000'> UNKNOWN                         </span> | $errline | $errstr | $errfile |";
                    break;
            }
            
            //
            $file = fopen($totemErrorFile,"a+");
            fwrite($file, $md);
            fclose($file);
            
            //
            return true;
        }
        
        set_error_handler("error_handler");
        
        register_shutdown_function(function(){
            $err = error_get_last();
            if(!empty($err))
                error_handler($err['type'], $err['message'], $err['file'], $err['line'], null);        
        });
        
    }
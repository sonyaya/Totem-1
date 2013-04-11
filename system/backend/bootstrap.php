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

        // CONFIGURAÇÕES PARA ESTA APLICAÇÃO
        $_M_THIS_CONFIG = $_M_CONFIG->backend;
    }else{
        die("Configuration file ../config.ini.php not found!");
    }

    # -- DEFINE TIME ZONE ATUAL ------------------------------------------------

    date_default_timezone_set( $_M_CONFIG->system['time-zone'] );

    # -- SAVE PHP ERROS IN A FILE ----------------------------------------------
    
    if( $_M_CONFIG->system['log-php-errors'] ){
        error_reporting(E_ALL);
        ini_set ("display_errors" , "off");
        ini_set ("log_errors"     , "On");
        ini_set ("error_log"      , "logs/".date('Y-m')."___backent-php-errors.txt");
    }
    
    
    echo $_M_CONFIG->system['log-php-error'];
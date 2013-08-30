<?php
/**
 * Description of bootstrap
 *
 * @author danielvarela
 */
class bootstrap {
    
    public $_M_CONFIG = "";
    public $_M_THIS_CONFIG = "";
    public $_M_LANGUAGE;
    public $module = "";
    
    /**
     * 
     * @return \bootstrap
     */
    public function __construct($module) {
        //
        session_start();
        
        //
        $this
            ->loadConfigFile($module)
            ->config()
        ;
        
        
        //
        return $this;
    }
    
    /**
     * 
     * @return \bootstrap
     */
    private function loadConfigFile($module) {
        //
        if(file_exists("../config.ini.php")){
            $_M_CONFIG = (object)parse_ini_file("../config.ini.php", true);

            // CAMINHO DO PATH-UPLOAD RELATIVO A PASTA SYSTEM
            $_M_CONFIG->system['upload-path'] = "../{$_M_CONFIG->system['upload-path']}";

            // CONFIGURAÇÕES PARA ESTA APLICAÇÃO
            $_M_THIS_CONFIG = $_M_CONFIG->$module;
        }else{
            die("Configuration file ../config.ini.php not found!");
        }
        
        //
        $this->_M_CONFIG = $_M_CONFIG;
        $this->_M_THIS_CONFIG = $_M_THIS_CONFIG;
        $this->_M_LANGUAGE = (file_exists($laguageIniFile = "../_commons/language/{$_M_THIS_CONFIG['language']}.ini")) 
                                ? parse_ini_file($laguageIniFile, true) 
                                : parse_ini_file("../_commons/language/.default.ini", true)
                             ;
        $this->module = $module;
        
        //
        return $this;
    }
    
    /**
     * 
     * @return \bootstrap
     */
    private function config() {
        //
        ini_set('default_charset','UTF-8');
        mb_internal_encoding("UTF-8");
        date_default_timezone_set( $this->_M_CONFIG->system['time-zone'] );
        
        //
        return $this;
    }
    
    /**
     * 
     * @return \bootstrap
     */
    public function autoloader() {
        spl_autoload_register( 
            function($str){
                $pathOrig  = explode("\\", $str);
                $action    = array_shift($pathOrig);
                $file      = array_pop($pathOrig);
                $path      = implode("/", $pathOrig);

                switch($action){
                    case "bridge"  :
                    case "console" :
                    case "backend" :
                        if(file_exists($exists_file = "../$action/_system/class/$file.php"))
                           require_once $exists_file;
                        else
                            require_once "../_commons/_system/class/$file.php";
                    break;

                    case "vendor" :
                        if(file_exists($exists_file = "_system/vendor/$path/$file.php"))
                           require_once $exists_file;
                        else
                            require_once "../_commons/_system/vendor/$path/$file.php";
                    break;
                }
            }
        );
        
        return $this;
    }
    
    /**
     * 
     * @return \bootstrap
     */
    public function errorHandler() {
        //
        if( $this->_M_CONFIG->system['log-php-errors'] ){

            # -- SAVE PHP FATAL ERROS IN A FILE ------------------------------------

            error_reporting(E_ALL);
            ini_set ("display_errors" , "off");
            ini_set ("log_errors"     , "On");
            ini_set ("error_log"      , "logs/".date('Y-m')."_-_fatal-errors.txt");

            # -- SAVE ERROS IN A FILE ----------------------------------------------

            function error_handler($errno, $errstr, $errfile, $errline, $errcontext){
                global $_M_MODULE_PATH;
                
                //
                chdir($_M_MODULE_PATH);
                
                //
                if( !file_exists($totemErrorFile = getcwd() . "/logs/".date('Y-m')."_-_errors.md") ){
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
                $errstr  = utf8_encode(str_pad(utf8_decode($errstr) , 182, " ")); // POG: str_pad não calcula direito sem isso
                $errfile = utf8_encode(str_pad(utf8_decode($errfile), 182, " ")); // POG: str_pad não calcula direito sem isso

                //
                switch ($errno) {
                    case E_ERROR:
                        $md .= "\r\n| $date |   PHP   | $errno | <span style='color:#FFFF00; background:#E13C26'> FATAL ERROR </span> | $errline | $errstr | $errfile |";

                        // Separando os traceback no log de erros fatais
                        $fileFatalErrors = fopen( getcwd() . "/logs/".date('Y-m')."_-_fatal-errors.txt", "a+");
                        fwrite($fileFatalErrors, "\r\n--------------------------------------------------------------------------------\r\n\r\n");
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

            //
            set_error_handler("error_handler");

            //
            register_shutdown_function(function(){
                $err = error_get_last();
                if(!empty($err))
                    error_handler($err['type'], $err['message'], $err['file'], $err['line'], null);        
            });

        }
        
        //
        return $this;
    }
    
    
    /**
     * 
     * @return \bootstrap
     */
    public function requirements($minPHPVersion, $extNeeded) {
        //
        if( !isset($_SESSION['php_check']) || !$_SESSION['php_check'] ){
            // Extensões Carregadas
            $ext = get_loaded_extensions();
            
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
        
        //
        return $this;
    }
}
<?php

    # INICIALIAZAÇÃO 
    require_once "../_commons/bootstrap.php";
    $bootstrap = new bootstrap("frontend");
    $bootstrap
      ->errorHandler()
      ->requirements( "5.3.0", Array("PDO", "pdo_mysql", "openssl", "session") )
      ->autoloader()
    ;

    # VARIAVEIS GLOBAIS
    $_M_CONFIG      = $bootstrap->_M_CONFIG;
    $_M_THIS_CONFIG = $bootstrap->_M_THIS_CONFIG;
    $_M_MODULE_PATH = getcwd();
    
    # DESTROI O OBJETO BOOTSTRAP
    unset($bootstrap);
    
    # USED CLASSES
    use backend\Frontend;
    
    # SYS VAR
    $sys = Array(
       "config" => array_merge(
           $_M_THIS_CONFIG,
           Array(
               "upload-path" => $_M_CONFIG->system['upload-path']
           )
       )
    );
    
    # LAYOUT
    if(  isset($_GET['_m_html']) &&  !empty($_GET['_m_html']) ){
        if( file_exists($file = "{$sys['config']['html-folder']}/{$_GET['_m_html']}") ){
            echo new Frontend($file, $sys);
        }else{
            trigger_error("O arquivo '$file' não foi encontrado.", E_USER_ERROR);
        }
    }else{
        if( file_exists($file = "{$sys['config']['html-folder']}/{$sys['config']['html-start']}") ){
            echo new Frontend($file, $sys);
        }else{
            trigger_error("O arquivo  padrão '$file' não foi encontrado.", E_USER_ERROR);
        }
    }
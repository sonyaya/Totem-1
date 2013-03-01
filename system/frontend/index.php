<?php

    # INICIALIAZAÇÃO 
    require_once "bootstrap.php";

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
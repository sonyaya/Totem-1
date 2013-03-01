<?php

    # INICIALIAZAÇÃO 
    require_once "bootstrap.php";

    # USED CLASSES
    use backend\Frontend;
    
    # SYS
    $sys = Array(
       "config" => array_merge(
           $_M_THIS_CONFIG,
           Array(
               "upload-path" => $_M_CONFIG->system['upload-path']
           )
       )
    );
    
    # MONTA LAYOUT
    $htmlFile = "example/index.html";
    echo new Frontend($htmlFile, $sys);
<?php

    # INICIALIAZAÇÃO 
    require_once "bootstrap.php";

    # USED CLASSES
    use backend\Frontend;

    # MONTA LAYOUT
    $htmlFile = "example/index.html";
    echo new Frontend($htmlFile);
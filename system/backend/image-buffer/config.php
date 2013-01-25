<?php // ♣

    # CONFIGURAÇÕES ADAPTADAS PARA O TOTEM
    if(file_exists($file = "../../config.ini.php")){
        $_M_CONFIG = (object)parse_ini_file($file, true);
    }else{
        die("Configuration file ../config.ini.php not found!");
    }
    
    // BASE DIR
    $options['basedir'] = "../" . $_M_CONFIG->system['upload-path'];
    
    // DEFAULT IMAGE SIZE
    $options['size']['width']  = 200;
    $options['size']['height'] = 150;
    
    // IF IMAGE IS NOT FOUND
    $options['notfound']['path']   = "notFound.jpg";
    $options['notfound']['width']  = 200;
    $options['notfound']['height'] = 150;
    
    // EXTERNAL IMAGES
    $options['external']['download-original-image'] = true;
    

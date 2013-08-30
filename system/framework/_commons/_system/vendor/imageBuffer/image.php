<?php

    // 
    include_once __DIR__ . "/imageBuffer.php";
   
    //
    $link = $_GET["link"];
 
    //
    //image.php?link=crop_90_100x100_centerxcenter/http/1.bp.blogspot.com/-w_MgjQxZGgg/UArgBwof6nI/AAAAAAAAA3o/kYXpQnSgqEA/s1600/DarkKnightRises.jpg
    //image.php?link=crop_90_100x100_centerxcenter/http/blog.sisea.com.br/wp-content/uploads/2013/04/michael-jackson-3.jpg
    //image.php?link=crop_90_100x100_centerxcenter/local/pasta/arquivo.jpg
    preg_match_all("/(?P<method>.*?)_(?P<quality>[0-9]*?)_(?P<width>[0-9]*?)x(?P<height>[0-9]*?)_(?P<top>.*?)x(?P<left>.*?)\/(?P<protocol>.*?)\/(?P<file>.*)/i", $link, $matches, PREG_SET_ORDER);
    
    //
    $storage  = "buffer/";                                                      # pasta onde será salvo as miniaturas
    $protocol = (strtolower($matches[0]['protocol'])=="http")? "http://" : "";
    $file     = $protocol . $matches[0]['file'];                                # nome do arquivo / ou endereço do site
    $method   = $matches[0]['method'];                                          # tipo de recorte
    $quality  = $matches[0]['quality'];                                         # qualidade da imagem
    $width    = $matches[0]['width'];                                           # lagura da nova imagem
    $height   = $matches[0]['height'];                                          # altura da nova imagem
    $top      = $matches[0]['top'];                                             # posição topo para as ações
    $left     = $matches[0]['left'];                                            # posição esquerda para as ações
    
    //
    $image = new imageBuffer($file);
    $image->setting($quality, $storage);
    $image->thumb($method, $width, $height, $top, $left);
    $image->redirect();
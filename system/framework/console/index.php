<?php

    # INICIALIAZAÇÃO 
    require_once "../_commons/bootstrap.php";
    $bootstrap = new bootstrap("console");
    $bootstrap
      ->errorHandler()
      ->requirements( "5.3.0", Array("PDO", "pdo_mysql", "session") )
      ->autoloader()
    ;
    
    # VARIAVEIS GLOBAIS
    $_M_CONFIG      = $bootstrap->_M_CONFIG;
    $_M_THIS_CONFIG = $bootstrap->_M_THIS_CONFIG;
    $_M_MODULE_PATH = getcwd();
    
    # DESTROI O OBJETO BOOTSTRAP
    unset($bootstrap);
    
    # USES
    use backend\Frontend;
    use backend\backendIndex;
    use vendor\Symfony\Component\Yaml\Yaml;
    
    # MENUS
    $_M_MENU        = Yaml::parse( file_get_contents("../backend/menu.yml") );;
    $_M_MENU_MODULE = "";
    $_M_MENU_PARTS  = "";
    backendIndex::createMenu();

    # LISTAR ARQUIVOS
    function dirRecursive($dirPath, $ident){
        $dir = scandir($dirPath);
        $idn1 = str_pad("", $ident+0); 
        $idn2 = str_pad("", $ident+2); 
        $idn3 = str_pad("", $ident+4); 
        $ret = "<ul>\r\n";
        foreach($dir as $item){
            if(is_file("$dirPath/$item")){
                if(preg_match("{.*?\.yml}i", $item)){
                    $ret .= "$idn2<li><a href='?path=$dirPath/$item'>$item</a></li>\r\n";  
                }
            }else{
                if($item != "." && $item != ".."){
                    $ret .= "$idn2<li>\r\n";
                    $ret .= "$idn3$item\r\n";
                    $ret .= "$idn3" . dirRecursive("$dirPath/$item", $ident+4) . "\r\n";
                    $ret .= "$idn2</li>\r\n";
                }
            }
        }
        $ret .= "$idn1</ul>";
        return $ret;
    }
    
    # ACTION
    $action = (isset($_GET['active']) && !empty($_GET['active'])) ? $_GET['active'] : "";
    $to_layout = array(
        "template" => $_M_THIS_CONFIG['template'],
        "menu"     => $_M_MENU,
        "files"    => dirRecursive("../backend/modules", 0)
    );

    switch($action){
        case "sei lá":
            break;

        default:
            echo new Frontend($_M_THIS_CONFIG['template'] . "index.html", $to_layout);
            break;
    }
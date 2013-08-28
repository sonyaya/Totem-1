<?php

    # INICIALIAZAÇÃO 
    require_once "../_commons/bootstrap.php";
    $bootstrap = new bootstrap("backend");
    $bootstrap
      ->errorHandler()
      ->requirements( "5.3.0", Array("PDO", "pdo_mysql", "openssl", "session") )
      ->autoloader()
    ;

    # VARIAVEIS GLOBAIS
    $_M_CONFIG      = $bootstrap->_M_CONFIG;
    $_M_THIS_CONFIG = $bootstrap->_M_THIS_CONFIG;
    $_M_MODULE_PATH = getcwd();
    $_M_LANGUAGE    = (file_exists($laguageIniFile = "language/{$_M_THIS_CONFIG['language']}.ini")) 
                          ? parse_ini_file($laguageIniFile, true) 
                          : Array()
                       ;
    
    # DESTROI O OBJETO BOOTSTRAP
    unset($bootstrap);
    
    # USED CLASSES
    use backend\backendIndex;
    use vendor\Symfony\Component\Yaml\Yaml;
    
    # EXECUTA SOMENTE SE EXISTIR USUÁRIO LOGADO 
    if( isset($_SESSION['user']) && !empty($_SESSION['user']) ){
        # VARIAVEIS GLOBAIS
        // Menus
        $_M_MENU        = Yaml::parse( file_get_contents("menu.yml") );;
        $_M_MENU_MODULE = "";
        $_M_MENU_PARTS  = "";
        
        // Configurações 
        // (estas variaveis estão representadas aqui somente 
        // para fins didaticos, eles são setados no bootstarp)
        $_M_CONFIG = $_M_CONFIG;
        $_M_THIS_CONFIG = $_M_THIS_CONFIG;
        
        // Dados do usuário logado 
        $_M_USER['id']          = ( isset($_SESSION['user']['id']          ) ) ? $_SESSION['user']['id']          : '' ;
        $_M_USER['login']       = ( isset($_SESSION['user']['login']       ) ) ? $_SESSION['user']['login']       : '' ;
        $_M_USER['first_name']  = ( isset($_SESSION['user']['first_name']  ) ) ? $_SESSION['user']['first_name']  : '' ;
        $_M_USER['middle_name'] = ( isset($_SESSION['user']['middle_name'] ) ) ? $_SESSION['user']['middle_name'] : '' ;
        $_M_USER['last_name']   = ( isset($_SESSION['user']['last_name']   ) ) ? $_SESSION['user']['last_name']   : '' ;
        $_M_USER['name']        = preg_replace("/\ {1,4}/i", " ", "{$_M_USER['first_name']} {$_M_USER['middle_name']} {$_M_USER['last_name']}");

        // EXECUTA ADD ON BOOTSTRAP 
        foreach(json_decode("{{$_M_THIS_CONFIG['bootstrap']}}") as $key=>$path){
           require_once $path;
           new $key($_M_THIS_CONFIG, $_M_USER, $_M_MENU);
        }
        
        # MONTA O MENU 
        backendIndex::createMenu();
    }

    # DECIDE QUAL AÇÃO EXECUTAR 
    $action = (isset($_GET['action']))? $_GET['action'] : "";
    $path   = (isset($_GET['path'])  )? $_GET['path']   : "";
    backendIndex::execAction($action, $path, $_GET, $_POST);
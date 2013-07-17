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
	
	backendIndex::createMenu();
	
	# ACTION
	$action = (isset($_GET['active']) && !empty($_GET['active'])) ? $_GET['active'] : "";
	$to_layout = array(
		"template" => $_M_THIS_CONFIG['template']
	);
	
	switch($action){
		case "sei lá":
			
		break;
		
		default:
            echo 
                new Frontend(
                    $_M_THIS_CONFIG['template'] . "index.html",
					$to_layout
                )
            ;
		break;
	}
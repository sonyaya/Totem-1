<?php

    namespace backend;
    
    use backend\Frontend;
    use backend\Util;
    use vendor\Symfony\Component\Yaml\Yaml;

    class DashboardComposer {
        
        private $dashboard = Array();
        private $dashboardData = Array();
        
        public function viewDashboard($dashFilename, $templateHtml){
            global $_M_CONFIG;
            global $_M_THIS_CONFIG;
            
            // IMPORTA E RODA A CLASSE DE DASHBOARD
            if(file_exists($filename = "dashboards/$dashFilename.php") ){
                require_once $filename;
                $this->dashboard = new \Dashboard();
                $this->dashboardData = $this->dashboard->getData();
            }else{
                echo "Dashboard $filename não foi encontrado.";
                exit;
            }
            
            // Array To Layout
            global $_M_THIS_CONFIG;
            global $_M_MENU;
            global $_M_MENU_PARTS;
            global $_M_MENU_MODULE;
            global $_M_USER;
            
            // monta o array
            $arrayBase = Array(  
                "user" => $_M_USER ,
                "main-menu" => $_M_MENU ,
                "main-menu-parts" => $_M_MENU_PARTS,
                "menu-modules" => $_M_MENU_MODULE,
                "dashboard" => $dashFilename
            );
            
            // GERA O HTML
            echo new 
                Frontend(
                    $_M_THIS_CONFIG['template'] ."/". $templateHtml, 
                    array_merge(
                        $_M_THIS_CONFIG,
                        $arrayBase,
                        $this->dashboardData
                    )
                )
            ;
        }
        
    }
<?php

    # -- INICIALIAZAÇÃO --------------------------------------------------------

    require_once "bootstrap.php";

    # -- USED NATIVE CLASSES ---------------------------------------------------

    use backend\User;
    use backend\Form;
    use backend\DashboardComposer;
    use backend\Frontend;
    use backend\backendIndex;

    # -- USED VENDORS CLASSES --------------------------------------------------

    use vendor\Symfony\Component\Yaml\Yaml;
    
    
    # -- EXECUTA SOMENTE SE EXISTIR USUÁRIO LOGADO -----------------------------
    
    if( isset($_SESSION['user']) && !empty($_SESSION['user']) ){
        
    
        # -- VARIAVEIS ---------------------------------------------------------

        $menuModule = "";
        $menuParts = "";
        $menu = Yaml::parse( file_get_contents("menu.yml") );

        # -- DADOS DO USUÁRIO LOGADO -------------------------------------------

        $_M_USER['id']          = ( isset($_SESSION['user']['id']          ) ) ? $_SESSION['user']['id']          : '' ;
        $_M_USER['login']       = ( isset($_SESSION['user']['login']       ) ) ? $_SESSION['user']['login']       : '' ;
        $_M_USER['first_name']  = ( isset($_SESSION['user']['first_name']  ) ) ? $_SESSION['user']['first_name']  : '' ;
        $_M_USER['middle_name'] = ( isset($_SESSION['user']['middle_name'] ) ) ? $_SESSION['user']['middle_name'] : '' ;
        $_M_USER['last_name']   = ( isset($_SESSION['user']['last_name']   ) ) ? $_SESSION['user']['last_name']   : '' ;
        $_M_USER['name']        = preg_replace("/\ {1,4}/i", " ", "{$_M_USER['first_name']} {$_M_USER['middle_name']} {$_M_USER['last_name']}");

        // -- EXECUTA ADD ON BOOTSTRAP -----------------------------------------

        foreach(json_decode("{{$_M_THIS_CONFIG['bootstrap']}}") as $key=>$path){
           require_once $path;
           new $key($_M_THIS_CONFIG, $_M_USER, $menu);
        }
        
        # -- MONTA O MENU ------------------------------------------------------

        function createMenuRecursive($array, $deep=0, $deepClass=1){
            $indent = 4;
            $pad0 = str_pad("", $deep);
            $pad1 = str_pad("", $deep+($indent) );
            $pad2 = str_pad("", $deep+($indent*2) );

            $ret = $pad0 . "<ul class='deep_$deepClass'>\r\n";
            foreach ($array as $key => $val) {
                
                if(isset($val['link'])){
                    //
                    parse_str( preg_replace("/^\?/", "", $val['link']), $mLnk);
                    
                    //
                    $hideMenuPath = 
                        isset($_SESSION['user']['permissions']['backend']['hide-menu-by-path']) 
                            ? $_SESSION['user']['permissions']['backend']['hide-menu-by-path'] 
                            : Array()
                        
                        ;
                    
                    //
                    if( !in_array($mLnk['path'], $hideMenuPath) ){
                        $ret .= $pad1 . "<li>\r\n";
                        // os arrays de comparação devem 
                        // ter no minimo estas chaves
                        $arrCompare['path'] = "";
                        $arrCompare['module'] = "";
                        $arrCompare['action'] = "";
                        $arrCompare['module'] = "";

                        // array do menu atual
                        $mLnk = array_replace($arrCompare, $mLnk);
                        $mLnk['module'] = preg_replace("/\/.*$/", "", $mLnk['path']);

                        // array da pagina/url atual (paginaa que esta sendo mostrada no browser)
                        $pLnk = array_replace($arrCompare, $_GET);
                        $pLnk['module'] = preg_replace("/\/.*$/", "", $pLnk['path']);

                        // comparações para adicionar classes
                        if($pLnk['module'] == $mLnk['module']){
                            $cssClassByModule = "active-by-module";
                            $cssClassByForm   = ( $pLnk['path']   == $mLnk['path']   ) ? "active-by-form"   : "";
                            $cssClassByAction = ( $pLnk['action'] == $mLnk['action'] ) ? "active-by-action" : "";
                            $cssClass         = ( $pLnk == $mLnk ) ? "active" : "" ;
                        }else{
                            $cssClassByModule = "";
                            $cssClassByForm   = "";
                            $cssClassByAction = "";
                            $cssClass         = "";
                        }
                        $cssClass = trim(preg_replace("/[ ]+/", " ", "$cssClassByModule $cssClassByAction $cssClassByForm $cssClass"));

                        // 
                        $ret .= $pad2 . "<a class='$cssClass' href='{$val['link']}'>{$val['label']}</a>\r\n";
                        $ret .= $pad1 . "</li>\r\n";
                    }
                  
                }else{
                    // Recursividade de modulo
                    if( isset($val['load-from-module']) && is_string($val['load-from-module']) ){
                        // verifica se o usuário pode ver este módulo
                        if( 
                            !isset( $_SESSION['user']['permissions']['backend']['show-module'] ) ||
                            $_SESSION['user']['permissions']['backend']['show-module'] === true ||
                            in_array('all', $_SESSION['user']['permissions']['backend']['show-module'] ) ||
                            in_array($val['load-from-module'], $_SESSION['user']['permissions']['backend']['show-module'] )
                        ){
                            $ret .= $pad1 . "<li>\r\n";
                            $ret .= $pad2 . "<span>{$val['label']}</span>\r\n";
                            
                            // variaveis comuns
                            global $menuParts;
                            global $menuModule;

                            // verifica se o menu do modulo é ativo
                            $cssClass = (isset($_GET['path']))? $_GET['path'] : "";
                            $cssClass = ( preg_replace("/\/.*$/", "", $cssClass) == $val['load-from-module'] )? "active" : "deactive";

                            // cria item do menu de modulo
                            $menuModule .= "\r\n    <li class='{$val['load-from-module']}'><a class='$cssClass' href='{$val['module-start-url']}'>{$val['label']}</a></li>";

                            // cria o itens filhos a partir do menu de formulário no main-menu
                            $smenu = Yaml::parse( file_get_contents("modules/{$val['load-from-module']}/menu.yml") );
                            $menuParts[ $val['load-from-module'] ] =  createMenuRecursive($smenu, $deep+($indent*2), $deepClass+1);

                            // retorna o menu
                            $ret .= $menuParts[ $val['load-from-module'] ];
                            $ret .= $pad1 . "</li>\r\n";
                        }
                    }

                    // Recursividade de menu
                    elseif( isset($val['submenu']) && is_array($val['submenu']) ){
                        $ret .= $pad1 . "<li>\r\n";
                        $ret .= $pad2 . "<span>{$val['label']}</span>\r\n";
                        $ret .= createMenuRecursive($val['submenu'], $deep+($indent*2), $deepClass+1);    
                        $ret .= $pad1 . "</li>\r\n";
                    }
                }


            }
            $ret .= $pad0 . "</ul>\r\n";
            return $ret;
        }

        $_M_MENU = createMenuRecursive($menu);
        $_M_MENU_MODULE = "<ul>$menuModule\r\n</ul>";
        $_M_MENU_PARTS = $menuParts;

        unset($menuModule);
        unset($menuParts);
        unset($menu);
        
    }
    
    // -- DECIDE QUAL AÇÃO EXECUTAR --------------------------------------------
    
    backendIndex::execAction($_GET['action'], $_GET['path']);
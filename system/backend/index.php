<?php

    # -- INICIALIAZAÇÃO --------------------------------------------------------

    require_once "bootstrap.php";
    
    # -- USED NATIVE CLASSES ---------------------------------------------------
    
    use backend\User;
    use backend\Form;
    use backend\Frontend;
    use backend\backendIndex;

    # -- USED VENDORS CLASSES --------------------------------------------------
    
    use vendor\Symfony\Component\Yaml\Yaml;

    # -- MONTA O MENU ----------------------------------------------------------
    
    $menu = Yaml::parse(file_get_contents("menu.yml"));
    function createMenuRecursive($array, $deep=0, $deepClass=1){
        $indent = 4;
        $pad0 = str_pad("", $deep);
        $pad1 = str_pad("", $deep+($indent) );
        $pad2 = str_pad("", $deep+($indent*2) );
        $ret = $pad0 . "<ul class='deep_$deepClass'>\r\n";
        foreach ($array as $key => $val) {
            $ret .= $pad1 . "<li>\r\n";
            if(isset($val['link'])){
                $ret .= $pad2 . "<a href='{$val['link']}'>{$val['label']}</a>\r\n";
            }else{
                $ret .= $pad2 . "<span>{$val['label']}</span>\r\n";
            }

            if( isset($val['submenu']) && is_array($val['submenu']) ){
                $ret .= createMenuRecursive($val['submenu'], $deep+($indent*2), $deepClass+1);
            }
            
            $ret .= $pad1 . "</li>\r\n";
        }
        $ret .= $pad0 . "</ul>\r\n";
        return $ret;
    }

    $_M_MENU = createMenuRecursive($menu);
    unset($menu);

    # -- DADOS DO USUÁRIO LOGADO -----------------------------------------------
    
    $_M_USER['login']       = ( isset($_SESSION['user']['login']       ) ) ? $_SESSION['user']['login']       : '' ;
    $_M_USER['first_name']  = ( isset($_SESSION['user']['first_name']  ) ) ? $_SESSION['user']['first_name']  : '' ;
    $_M_USER['middle_name'] = ( isset($_SESSION['user']['middle_name'] ) ) ? $_SESSION['user']['middle_name'] : '' ;
    $_M_USER['last_name']   = ( isset($_SESSION['user']['last_name']   ) ) ? $_SESSION['user']['last_name']   : '' ;
    $_M_USER['name']        = preg_replace("/\ {1,4}/i", " ", "{$_M_USER['first_name']} {$_M_USER['middle_name']} {$_M_USER['last_name']}");

    // -- DECIDE QUAL AÇÃO EXECUTAR --------------------------------------------
    
    $action = (isset($_GET['action']))? $_GET['action'] : "";
    switch( $action ) {
        // MOSTRA A INTERFACE GRÁFICA DA
        // TELA DE FORMULÁRIO DE INSERÇÃO
        case "view-insert-form":{
            User::check("backend", "view-insert-form", "html");
            backendIndex::viewFormInsert("form.html");
            break;
        }
        
        // MOSTRA A INTERFACE GRÁFICA DA
        // TELA DE FORMULÁRIO DE ATUAIZAÇÃO
        case "view-update-form":{
            User::check("backend", "view-update-form", "html");
            backendIndex::viewFormUpdate("form.html");
            break;
        }

        // BUSCA LISTA DE DADOS REFERENTE 
        // AO FORMULÁRIO NO BANCO DE DADOS
        case "view-list-form":{
            User::check("backend", "view-list-form", "html");
            backendIndex::viewFormList("list.html");
            break;
        }
        
        // BUSCA LISTA DE DADOS REFERENTE 
        // AO FORMULÁRIO NO BANCO DE DADOS
        // E O FORMULÁRIO DE INSERÇÃO
        case "view-listAndInsert-form":{
            User::check("backend", "view-insert-form", "html");
            User::check("backend", "view-list-form", "html");
            backendIndex::viewFormListAndInsert("listAndInsert.html");
            break;
        }
        
        // MOSTRA A INTERFACE GRÁFICA DA
        // TELA DE FORMULÁRIO DE INSERÇÃO
        // EM JANELA
        case "view-insert-window-form":{
            User::check("backend", "view-insert-form", "html");
            backendIndex::viewFormInsert("form-window.html");
            break;
        }
        
        // MOSTRA A INTERFACE GRÁFICA DA
        // TELA DE FORMULÁRIO DE ATUAIZAÇÃO
        // EM JANELA
        case "view-update-window-form":{
            User::check("backend", "view-update-form", "html");
            backendIndex::viewFormUpdate("form-window.html");
            break;
        }

        // BUSCA LISTA DE DADOS REFERENTE 
        // AO FORMULÁRIO NO BANCO DE DADOS
        // EM JANELA
        case "view-list-window-form":{
            User::check("backend", "view-list-form", "html");
            backendIndex::viewFormList("list-window.html");
            break;
        }
        
        // MOSTRA A INTERFACE GRÁFICA DA
        // TELA DE SOLICITAÇÃO DE RECUPERAÇÃO DE SENHA
        case "view-change-password":{
            backendIndex::viewrecoverPassword($_GET['hash']);
            break;
        }

        // DELETA UM FORMULÁRIO
        case "delete-form":{
            User::check("backend", "delete-form", "json");
            $form = new Form();
            echo json_encode( $form->deleteForm( @$_GET['form'], $_GET['id']) );
            break;
        }
        
        // INSERE OU ATUALIZA DADOS 
        // NO BANCO DE DADOS
        case "save-form":{
            User::check("backend", "save-form", "json");
            $form = new Form();
            echo json_encode( $form->saveForm( @$_GET['form'], $_POST) );
            break;
        }

        // EXECUTA AJAX DE ALGUM TYPE ESPECIFICO
        case "type-ajax":{
            $type = $_GET['type'];
            if( file_exists($fileType = "types/$type/$type.php") ){
                require_once $fileType;
                $obj = new $type();
                if( method_exists ( $obj , "ajax" ) ){
                    $obj->ajax();
                }else{
                    echo "Metodo ajax não foi encontrado em '$fileType'.";
                }
            }else{
                echo "Erro ao carregar ajax do type '$fileType'.";
            }
            break;
        }

        // EFETUA LOGIN DE USUÁRIO
        case "login":{
            echo json_encode( User::login( $_POST['login'], $_POST['password']) );
            break;
        }

        // EFETUA LOGOUT DE USUÁRIO
        case "logout":{
            User::logout();
            header("Location: . ");
            break;
        }

        // SOLICITA RECUPERAÇÃO DE SENHAS
        case "recover-password":{
            echo json_encode( User::recoverPassword($_POST['login']) );
            break;
        }
        
        // ALTERA A SENHA COM BASE
        // NA SOLICITAÇÃO DE RECUPERAÇÃO DE SENHA
        case "change-password":{
            echo json_encode( User::recoverPasswordChangePassword($_POST['recovery_hash'], $_POST['password'], $_POST['password-1']) );
            break;
        }
        
        // AÇÃO PADRÃO
        default:{
            header("Location: {$_M_THIS_CONFIG['start-place']}");
            break;
        }
        
    }
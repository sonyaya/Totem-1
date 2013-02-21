<?php

    namespace backend;

    class backendIndex{

        /**
         * Mostra tela de Inserção de dados
         * 
         * @param type $formLayout
         */
        static public function viewFormInsert($formLayout, $path){
            $Form = new Form();
            $Form
                ->setLayout($formLayout)
                ->viewForm($path)
                ->writeHTML()
            ;
        }

        /**
         * Mostra tela de atualização de dados
         * 
         * @param type $formLayout
         */
        static public function viewFormUpdate($formLayout, $path){
            $Form = new Form();
            $Form
                ->setLayout($formLayout)
                ->viewForm($path, $_GET['id'])
                ->writeHTML()
            ; 
        }

        /**
         * Mostra tela de listagem de dados
         * 
         * @param type $listLayout
         */
        static public function viewFormList($listLayout, $path){
            $orderBy = self::prepareOrderBy();
            $paginator = self::preparePaginator();
            $cond = self::prepareCondition();
            $page = $paginator['page'];
            $rowsPerPage = $paginator['rowsPerPage'];  
            
            $form = new Form();
            $form
                ->setLayout($listLayout)
                ->viewList($path, $page, $rowsPerPage, $orderBy, $cond)
                ->writeHTML()
            ;
        }

        /**
         * Mostra tela de listagem e atualização
         * 
         * @param type $listLayout
         */
        static function viewFormInTabs($listLayout, $path){
            $orderBy = self::prepareOrderBy();
            $paginator = self::preparePaginator();
            $cond = self::prepareCondition();
            $page = $paginator['page'];
            $rowsPerPage = $paginator['rowsPerPage']; 
            
            $form = new Form();
            $form
                ->setLayout($listLayout)
                ->viewForm($path)
                ->viewList($path, $page, $rowsPerPage, $orderBy, $cond)
                ->writeHTML()
            ;            
        }
        
        /**
         * Prepara o order by para telas de listagem
         * 
         * @return Array
         */
        static private function prepareOrderBy(){
             if( isset($_GET['orderBy']) && !empty($_GET['orderBy']) ){
                $orderBy = $_GET['orderBy'];

                // ORDER BY Valor!/ID
                $orderBy = explode('/', $orderBy);
                foreach ($orderBy as $key => $val) {
                    if( strpos($val, "!") === false ){
                        $orderBy[$key] = $val = "`$val`";
                    }else{
                        $orderBy[$key] = preg_replace("/(.*?)!/i", "`$1` DESC", $val);
                    }
                }
                
                return implode(", ", $orderBy);
            }else{
                return null;
            }           
        }

        /**
         * Prepara paginação de talas de listagem
         * 
         * @return array
         */
        static private function preparePaginator(){
            $page = (isset($_GET['page']))? $_GET['page'] : '1';
            $rowsPerPage = (isset($_GET['rowsPerPage']))?  $_GET['rowsPerPage'] : '';
            return Array("page" => $page, "rowsPerPage" => $rowsPerPage);
        }


        /**
         * Prepara condeições de listagem / search
         * 
         * @return string
         */
        static private function prepareCondition(){
            if( isset($_GET['cond']) ){
                $cond = json_decode($_GET['cond']);

                if(is_array($cond) ){
                    $andOr[" "] = "";
                    $andOr["!"] = "OR";
                    $andOr["-"] = "AND";

                    $condition[":"] = "=";
                    $condition[";"] = "<>";
                    $condition["*"] = "LIKE";
                    $condition["^"] = "RLIKE";
                    $condition["-"] = "BETWEEN";

                    $firstTime = true;
                    $condStr = '';
                    foreach($cond as $val){
                        if( !empty($val[3]) ){
                            if( $firstTime ) $val[0] = " ";
                            $condStr .= "{$andOr[ $val[0] ]} `{$val[1]}` {$condition[ $val[2] ]} '{$val[3]}' ";
                            $firstTime = false;
                        }
                    }

                    return "$condStr";
                }
            }
            
            return "";
        }
        
        /**
         * Mostra a tela de recuperação de senha
         * 
         * @global type $_M_CONFIG
         * @global type $_M_THIS_CONFIG
         * @param type $recovery_hash
         */
        static public function viewrecoverPassword($recovery_hash){
            global $_M_CONFIG;
            global $_M_THIS_CONFIG;

            // LAYOUT
            echo 
                new Frontend(
                    $_M_CONFIG->console['template'] . "recovery-password.html",
                    array_merge(
                        $_M_THIS_CONFIG,
                        Array( "recovery_hash" => $recovery_hash )
                    )
                )
            ;
        }
        
        
        static public function execAction($action="", $path=""){

            switch( $action ) {
                // MOSTRA A INTERFACE GRÁFICA DO
                // DASHBOARD ESPECIFÍCO
                case "view-dashboard":{
                    if( !User::check("backend/forms/view/dashboard", "bool") )
                        User::check("backend/modules/view/dashboard/{$path}", "html");
                    $dashboard = new DashboardComposer();
                    $dashboard->viewDashboard( $path, "dashboard.html" );
                    break;
                }

                // MOSTRA A INTERFACE GRÁFICA DA
                // TELA DE FORMULÁRIO DE INSERÇÃO
                case "view-insert-form":{
                    if( !User::check("backend/forms/view/insert", "bool") )
                        User::check("backend/modules/view/insert/{$path}", "html");
                    self::viewFormInsert("form.html", $path);
                    break;
                }

                // MOSTRA A INTERFACE GRÁFICA DA
                // TELA DE FORMULÁRIO DE ATUAIZAÇÃO
                case "view-update-form":{
                    if( !User::check("backend/forms/view/update", "bool") )
                        User::check("backend/modules/view/update/{$path}", "html");
                    self::viewFormUpdate("form.html", $path);
                    break;
                }

                // BUSCA LISTA DE DADOS REFERENTE 
                // AO FORMULÁRIO NO BANCO DE DADOS
                case "view-list-form":{
                    if( !User::check("backend/forms/view/list", "bool") )
                        User::check("backend/modules/view/list/{$path}", "html");
                    self::viewFormList("list.html", $path);
                    break;
                }

                // BUSCA LISTA DE DADOS REFERENTE 
                // AO FORMULÁRIO NO BANCO DE DADOS
                // E O FORMULÁRIO DE INSERÇÃO
                case "view-inTabs-form":{
                    if( !User::check("backend/forms/view/inTabs", "bool") )
                        User::check("backend/modules/view/inTabs/{$path}", "html");
                    self::viewFormInTabs("listAndInsert.html", $path);
                    break;
                }

                // MOSTRA A INTERFACE GRÁFICA DA
                // TELA DE FORMULÁRIO DE INSERÇÃO
                // EM JANELA
                case "view-insert-window-form":{
                    if( !User::check("backend/forms/view/insert", "bool") )
                        User::check("backend/modules/save/insert/{$path}", "html");
                    self::viewFormInsert("form-window.html", $path);
                    break;
                }

                // MOSTRA A INTERFACE GRÁFICA DA
                // TELA DE FORMULÁRIO DE ATUAIZAÇÃO
                // EM JANELA
                case "view-update-window-form":{
                    if( !User::check("backend/forms/view/update", "bool") )
                        User::check("backend/modules/save/update/{$path}", "html");
                    self::viewFormUpdate("form-window.html", $path);
                    break;
                }

                // BUSCA LISTA DE DADOS REFERENTE 
                // AO FORMULÁRIO NO BANCO DE DADOS
                // EM JANELA
                case "view-list-window-form":{
                    if( !User::check("backend/forms/view/list", "bool") )
                        User::check("backend/modules/save/list/{$path}", "html");
                    self::viewFormList("list-window.html", $path);
                    break;
                }

                // MOSTRA A INTERFACE GRÁFICA DA
                // TELA DE SOLICITAÇÃO DE RECUPERAÇÃO DE SENHA
                case "view-change-password":{
                    self::viewrecoverPassword($_GET['hash']);
                    break;
                }

                // DELETA UM FORMULÁRIO
                case "delete-form":{
                    if( !User::check("backend/forms/save/delete", "bool") )
                        User::check("backend/modules/save/delete/{$path}", "json");
                    $form = new Form();
                    echo json_encode( $form->deleteForm( $path, $_GET['id']) );
                    break;
                }

                // INSERE OU ATUALIZA DADOS 
                // NO BANCO DE DADOS
                case "save-form":{
                    if( preg_match("/update\:.*/i", $_POST['_M_ACTION']) ){
                        if( !User::check("backend/forms/save/update", "bool") )
                            User::check("backend/modules/save/update/{$path}", "json");
                    }else{
                        if( !User::check("backend/forms/save/insert", "bool") )
                            User::check("backend/modules/save/insert/{$path}", "json");
                    }

                    $form = new Form();
                    echo json_encode( $form->saveForm( $path, $_POST) );
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
            } # switch
        }
        
    } # class
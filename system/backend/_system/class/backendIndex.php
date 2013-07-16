<?php

    namespace backend;

    use backend\Form;
    use backend\DashboardComposer;
    use backend\Frontend;
    use backend\User;
    
    use vendor\Symfony\Component\Yaml\Yaml;
    
    /**
     * 
     */
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
        static public function viewFormUpdate($formLayout, $path, $id){
            $Form = new Form();
            $Form
                ->setLayout($formLayout)
                ->viewForm($path, $id)
                ->writeHTML()
            ; 
        }

        /**
         * Mostra tela de listagem de dados
         * 
         * @param type $listLayout
         */
        static public function viewFormList($listLayout, $path, $orderBy='', $page='1', $rowsPerPage='', $cond=""){
            $orderBy     = self::prepareOrderBy($orderBy);
            $paginator   = self::preparePaginator($page, $rowsPerPage);
            $cond        = self::prepareCondition($cond);
            $page        = $paginator['page'];
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
        static function viewFormInTabs($listLayout, $path, $orderBy="", $page='1', $rowsPerPage='', $cond=""){
            $orderBy     = self::prepareOrderBy($orderBy);
            $paginator   = self::preparePaginator($page, $rowsPerPage);
            $cond        = self::prepareCondition($cond);
            $page        = $paginator['page'];
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
        static private function prepareOrderBy($orderBy=""){
             if( !empty($orderBy) ){

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
        static private function preparePaginator($page='1', $rowsPerPage=''){
            return Array("page" => $page, "rowsPerPage" => $rowsPerPage);
        }


        /**
         * Prepara condeições de listagem / search
         * 
         * @return string
         */
        static private function prepareCondition($cond=""){
            if( !empty($cond) ){
                $cond = json_decode($cond);

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
                    $_M_THIS_CONFIG['template'] . "recovery-password.html",
                    array_merge(
                        $_M_THIS_CONFIG,
                        Array( "recovery_hash" => $recovery_hash )
                    )
                )
            ;
        }
        
        /**
         * 
         * @global \backend\type $_M_THIS_CONFIG
         * @param type $action
         * @param type $path
         * @param type $get
         * @param type $post
         */
        static public function execAction($action="", $path="", $get, $post){
            //
            global $_M_THIS_CONFIG;
            
            //
            $path = "modules/$path";
            
            // TRATA O GET
            $get = 
                array_replace(
                    array(
                        "id"=>"",
                        "page"=>"1",
                        "rowsPerPage"=>"",
                        "cond"=>"1",
                        "orderBy"=>"",
                        "hash"=>""
                    )
                    , $get
                )
            ;
            
            // ESCOLHE A AÇÃO A SER EXECUTADA
            switch( $action ) {
                // MOSTRA A INTERFACE GRÁFICA DO
                // DASHBOARD ESPECIFÍCO
                case "view-dashboard":{
                    User::check("backend/{$path}", $action, "html");
                    $dashboard = new DashboardComposer();
                    $dashboard->viewDashboard( $path, "dashboard.html" );
                    break;
                }

                // MOSTRA A INTERFACE GRÁFICA DA
                // TELA DE FORMULÁRIO DE INSERÇÃO
                case "view-insert-form":{
                    User::check("backend/{$path}", $action, "html");
                    self::viewFormInsert("form.html", $path);
                    break;
                }

                // MOSTRA A INTERFACE GRÁFICA DA
                // TELA DE FORMULÁRIO DE ATUAIZAÇÃO
                case "view-update-form":{
                    User::check("backend/{$path}", $action, "html");
                    self::viewFormUpdate("form.html", $path, $get['id']);
                    break;
                }
                
                // MOSTRA A INTERFACE GRÁFICA DA
                // TELA DE FORMULÁRIO FALSO
                case "view-dummy-form":{
                    User::check("backend/{$path}", $action, "html");
                    self::viewFormUpdate("dummy-form.html", $path, "dummy");
                    break;
                }

                // BUSCA LISTA DE DADOS REFERENTE 
                // AO FORMULÁRIO NO BANCO DE DADOS
                case "view-list-form":{
                    User::check("backend/{$path}", $action, "html");
                    self::viewFormList("list.html", $path, $get['orderBy'], $get['page'], $get['rowsPerPage'], $get['cond']);
                    break;
                }

                // BUSCA LISTA DE DADOS REFERENTE 
                // AO FORMULÁRIO NO BANCO DE DADOS
                // E O FORMULÁRIO DE INSERÇÃO
                case "view-inTabs-form":{
                    User::check("backend/{$path}", $action, "html");
                    self::viewFormInTabs("listAndInsert.html", $path, $get['orderBy'], $get['page'], $get['rowsPerPage'], $get['cond']);
                    break;
                }

                // MOSTRA A INTERFACE GRÁFICA DA
                // TELA DE FORMULÁRIO DE INSERÇÃO
                // EM JANELA
                case "view-insert-window-form":{
                    User::check("backend/{$path}", str_replace("-window", "", $action), "html");
                    self::viewFormInsert("form-window.html", $path);
                    break;
                }

                // MOSTRA A INTERFACE GRÁFICA DA
                // TELA DE FORMULÁRIO DE ATUAIZAÇÃO
                // EM JANELA
                case "view-update-window-form":{
                    User::check("backend/{$path}", str_replace("-window", "", $action), "html");
                    self::viewFormUpdate("form-window.html", $path, $get['id']);
                    break;
                }

                // MOSTRA A INTERFACE GRÁFICA DA
                // TELA DE FORMULÁRIO FALSO
                // EM JANELA
                case "view-dummy-window-form":{
                    User::check("backend/{$path}", str_replace("-window", "", $action), "html");
                    self::viewFormUpdate("dummy-form-window.html", $path, "dummy");
                    break;
                }

                // BUSCA LISTA DE DADOS REFERENTE 
                // AO FORMULÁRIO NO BANCO DE DADOS
                // EM JANELA
                case "view-list-window-form":{
                    User::check("backend/{$path}", str_replace("-window", "", $action), "html");
                    self::viewFormList("list-window.html", $path, $get['orderBy'], $get['page'], $get['rowsPerPage'], $get['cond']);
                    break;
                }

                // MOSTRA A INTERFACE GRÁFICA DA
                // TELA DE SOLICITAÇÃO DE RECUPERAÇÃO DE SENHA
                case "view-change-password":{
                    self::viewrecoverPassword($get['hash']);
                    break;
                }

                // DELETA UM FORMULÁRIO
                case "delete-form":{
                    User::check("backend/{$path}", "delete", "json");
                    $form = new Form();
                    echo json_encode( $form->deleteForm( $path, $get['id']) );
                    break;
                }

                // INSERE OU ATUALIZA DADOS 
                // NO BANCO DE DADOS
                case "save-form":{
                    if( preg_match("/update\:.*/i", $post['_M_ACTION']) ){
                        User::check("backend/{$path}", "update", "json");
                    }else{
                        User::check("backend/{$path}", "insert", "json");
                    }

                    $form = new Form();
                    echo json_encode( $form->saveForm( $path, $post) );
                    break;
                }

                // EXECUTA AJAX DE ALGUM TYPE ESPECIFICO
                case "type-ajax":{
                    $type = $get['type'];
                    if( file_exists($fileType = "types/$type/config-events.php") ){
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
                    echo json_encode( User::login( $post['login'], $post['password']) );
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
                    echo json_encode( User::recoverPassword($post['login']) );
                    break;
                }

                // ALTERA A SENHA COM BASE
                // NA SOLICITAÇÃO DE RECUPERAÇÃO DE SENHA
                case "change-password":{
                    echo json_encode( User::recoverPasswordChangePassword($post['recovery_hash'], $post['password'], $post['password-1']) );
                    break;
                }

                // AÇÃO PADRÃO
                default:{
                    header("Location: {$_M_THIS_CONFIG['start-place']}");
                    break;
                } 
            } # switch
        }
        
        /**
         * 
         * @global type $_M_MENU
         * @global type $_M_MENU_MODULE
         * @global type $_M_MENU_PARTS
         */
        static public function createMenu(){
            global $_M_MENU;
            global $_M_MENU_MODULE;
            global $_M_MENU_PARTS;
            
            $_M_MENU = self::createMenuRecursive($_M_MENU);
            $_M_MENU_MODULE = "<ul>$_M_MENU_MODULE</ul>";
            $_M_MENU_PARTS = $_M_MENU_PARTS;
        }
        
        /**
         * 
         * @global \backend\type $_M_CONFIG
         * @global \backend\type $_M_MENU_PARTS
         * @global \backend\type $_M_MENU_MODULE
         * @param type $array
         * @param type $deep
         * @param type $deepClass
         * @return string
         */
        static private function createMenuRecursive($array, $deep=0, $deepClass=1){
            global $_M_CONFIG;

            
            $indent = 4;
            $pad0 = str_pad("", $deep);
            $pad1 = str_pad("", $deep+($indent) );
            $pad2 = str_pad("", $deep+($indent*2) );

            $ret = $pad0 . "<ul class='deep_$deepClass'>\r\n";
            foreach ($array as $key => $val) {
                
                if(isset($val['link'])){
                    //
                    parse_str( preg_replace("/^\?/", "", $val['link']), $mLnk);
                    
                    // permissões por menus
                    if( User::check("backend/modules/{$mLnk['path']}", $mLnk['action'], "bool") ){
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

                        // array da pagina/url atual (pagina que esta sendo mostrada no browser)
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
                        // permissões por modulo
                        if( User::check("backend/modules/{$val['load-from-module']}", "all", "bool") ){
                            // verifica se o usuário pode ver este módulo
                            $ret .= $pad1 . "<li>\r\n";
                            $ret .= $pad2 . "<span>{$val['label']}</span>\r\n";

                            // variaveis comuns
                            global $_M_MENU_PARTS;
                            global $_M_MENU_MODULE;

                            // verifica se o menu do modulo é ativo
                            $cssClass = (isset($_GET['path']))? $_GET['path'] : "";
                            $cssClass = ( preg_replace("/\/.*$/", "", $cssClass) == $val['load-from-module'] )? "active" : "deactive";

                            // cria item do menu de modulo
                            $_M_MENU_MODULE .= "\r\n    <li class='{$val['load-from-module']}'><a class='$cssClass' href='{$val['module-start-url']}'>{$val['label']}</a></li>";

                            // cria o itens filhos a partir do menu de formulário no main-menu
                            $smenu = Yaml::parse( file_get_contents("modules/{$val['load-from-module']}/menu.yml") );
                            $_M_MENU_PARTS[ $val['load-from-module'] ] =  self::createMenuRecursive($smenu, $deep+($indent*2), $deepClass+1);

                            // retorna o menu
                            $ret .= $_M_MENU_PARTS[ $val['load-from-module'] ];
                            $ret .= $pad1 . "</li>\r\n";
                        }
                    }

                    // Recursividade de menu
                    elseif( isset($val['submenu']) && is_array($val['submenu']) ){
                        $ret .= $pad1 . "<li>\r\n";
                        $ret .= $pad2 . "<span>{$val['label']}</span>\r\n";
                        $ret .= self::createMenuRecursive($val['submenu'], $deep+($indent*2), $deepClass+1);    
                        $ret .= $pad1 . "</li>\r\n";
                    }
                }


            }
            $ret .= $pad0 . "</ul>\r\n";
            return $ret;
        }
        
    } # class
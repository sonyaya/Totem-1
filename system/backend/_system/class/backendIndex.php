<?php

    namespace backend;

    class backendIndex{

        /**
         * Mostra tela de Inserção de dados
         * 
         * @param type $formLayout
         */
        static public function viewFormInsert($formLayout){
            $Form = new Form();
            $Form
                ->setLayout($formLayout)
                ->viewForm($_GET['form'])
                ->writeHTML()
            ;
        }

        /**
         * Mostra tela de atualização de dados
         * 
         * @param type $formLayout
         */
        static public function viewFormUpdate($formLayout){
            $Form = new Form();
            $Form
                ->setLayout($formLayout)
                ->viewForm($_GET['form'], $_GET['id'])
                ->writeHTML()
            ; 
        }

        /**
         * Mostra tela de listagem de dados
         * 
         * @param type $listLayout
         */
        static public function viewFormList($listLayout){
            $orderBy = self::prepareOrderBy();
            $paginator = self::preparePaginator();
            $page = $paginator['page'];
            $rowsPerPage = $paginator['rowsPerPage'];  
            
            $form = new Form();
            $form
                ->setLayout($listLayout)
                ->viewList($_GET['form'], $page, $rowsPerPage, $orderBy /*, $cond */)
                ->writeHTML()
            ;
        }

        /**
         * Mostra tela de listagem e atualização
         * 
         * @param type $listLayout
         */
        static function viewFormListAndInsert($listLayout){
            $orderBy = self::prepareOrderBy();
            $paginator = self::preparePaginator();
            $page = $paginator['page'];
            $rowsPerPage = $paginator['rowsPerPage']; 
            
            $form = new Form();
            $form
                ->setLayout($listLayout)
                ->viewForm($_GET['form'])
                ->viewList($_GET['form'], $page, $rowsPerPage, $orderBy /*, $cond */)
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
    }
<?php

    namespace backend;

    class backendIndex{

        /**
         *
         */
        static public function viewFormInsert($formLayout){
            $Form = new Form();
            $Form->setFormLayout($formLayout);
            echo $Form->viewForm($_GET['form']);
        }

        /**
         *
         */
        static public function viewFormUpdate($formLayout){
            $Form = new Form();
            $Form->setFormLayout($formLayout);
            echo $Form->viewForm($_GET['form'], $_GET['id']);
        }

        /**
         * 
         */
        static public function viewFormList($listLayout){
            if( isset($_GET['orderBy']) ){
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
                $orderBy = implode(", ", $orderBy);
            }else{
                $orderBy = null;
            }

            // CONDITION
            $cond = (isset($_GET['cond']))? $_GET['cond'] : '';
            $condList = Array();
            preg_match_all("/\((?P<andOr>[-_!])(?P<column>\w+?)(?P<comparison>[:;^*-])(?P<value>.+?)(?P=andOr)\)/i", $cond, $condList, PREG_SET_ORDER);
            
            $andOr['-'] = " AND";
            $andOr['x'] = " OR";
            $andOr['_'] = '';
            
            $comparison[':'] = '=';
            $comparison[';'] = '<>';
            $comparison['^'] = 'RLIKE';
            $comparison['*'] = 'LIKE';
            $comparison['-'] = 'BETEWEEN';
            
            $cond = '';
            foreach($condList as $key=>$val){
                if( !is_numeric($val['value'])){
                    $value = "'{$val['value']}'";
                }else{
                     $value = $val['value'];
                }
                $cond .= "{$andOr[$val['andOr']]} `{$val['column']}` {$comparison[$val['comparison']]} $value";
            }

            // MOSTRA LISTAGEM
            $page = (isset($_GET['page']))? $_GET['page'] : '1';
            $rowsPerPage = (isset($_GET['rowsPerPage']))?  $_GET['rowsPerPage'] : '';
            
            $form = new Form();
            $form->setListLayout($listLayout);
            echo $result = $form->viewList($_GET['form'], $page, $rowsPerPage, $orderBy, $cond);
        }

        /**
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
<?php
    use backend\MySQL;

    class fk{

        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $thisLabel
         * @return type
         */
        public function validate($thisData, $thisColumn, $allData, $parameters, $thisLabel){
            if( empty($thisData) ){
                return Array( "error" => true, "message" => "O campo $thisLabel não pode ser nulo." );
            }else{
                return Array( "error" => false );
            }
        }

        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $toTypeLayout
         */
        public function beforeLoadDataToForm(&$thisData, $thisColumn, &$allData, $parameters, &$toTypeLayout){
            if( !empty($thisData) ){
                $db = new MySQL();
                $data = $db
                    ->setTable($parameters['table'])
                    ->setPage(1)
                    ->setRowsPerPage(1)
                    ->select(Array( 
                            "value"=>$parameters['column'], 
                            "label"=>$parameters['label']
                        ), 
                        "`{$parameters['column']}` = '$thisData'",
                        true
                    )
                ;

                $thisData = Array(
                    "id" => $data['value'],
                    "label" => $data['label']
                );
            }
        }

        /**
         * 
         */
        public function ajax(){
            $db = new MySQL();
            echo 
                json_encode(
                    $db
                        ->setTable($_POST['table'])
                        ->setPage(1)
                        ->setRowsPerPage(5)
                        ->select(
                            Array( 
                                "value"=>$_POST['column'], 
                                "label"=>$_POST['label']
                            ), 
                            "`{$_POST['label']}` like '{$_POST['value']}%' ORDER BY `{$_POST['label']}`"
                        )
                )
            ;
        }
    }
<?php
    use backend\MySQL;

    class manyToMany{
        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $thisLabel
         * @return type
         */
        public function validate($thisData, $thisColumn, &$allData, $parameters, $thisLabel){
            if( empty($thisData[1]) && !@$parameters['nullable'] ){
                return Array( "error" => true, "message" => "O campo $thisLabel, não pode ser vazio." );
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
         * @param type $pKey
         */
        public function beforeInsert(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            unset($allData[ $thisColumn ]);
        }

        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $pKey
         */
        public function beforeUpdate(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            $db = new MySQL();
            $db->setTable($parameters['middle-table']);
            $db->delete("`{$parameters['middle-fk']}` = '{$pKey['value']}'");
            array_shift($thisData);
            foreach ($thisData as $key => $val) {
                $db->save(Array(
                    $parameters['middle-fk'] => $pKey['value'],
                    $parameters['middle-pk'] => $val
                ));
            }
            
            unset($allData[ $thisColumn ]);
        }

        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $pKey
         */
        public function beforeDelete(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
          $db = new MySQL();
            $db->setTable($parameters['middle-table']);
            $db->delete("`{$parameters['middle-fk']}` = '{$pKey['value']}'");
            unset($allData[ $thisColumn ]);
        }
        
        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $toTypeLayout
         * @param type $pKey
         */
        public function beforeLoadDataToForm(&$thisData, $thisColumn, &$allData, $parameters, &$toTypeLayout, $pKey){
            $db = new MySQL();
            $db->setTable($parameters['middle-table']);
            $toTypeLayout = $db->select(
                Array(
                    "fk"=>$parameters['middle-fk'],
                    "pk"=>$parameters['middle-pk'],
                    "data" => Array(
                        "pk",
                        "{$parameters['right-table']}.{$parameters['right-fk']}",
                        Array(
                            "value"=>$parameters['right-fk'],
                            "label"=>$parameters['right-label'] 
                        )
                    )
                ),
                "`{$parameters['middle-fk']}`='{$pKey['value']}'",
                false
            );
        }

        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $pKey
         */
        public function afterInsert($thisData, $thisColumn, $allData, $parameters, $pKey){
            $db = new MySQL();
            $db->setTable($parameters['middle-table']);
            array_shift($thisData);
            foreach ($thisData as $key => $val) {
                $db->save(Array(
                    $parameters['middle-fk'] => $pKey['value'],
                    $parameters['middle-pk'] => $val
                ));
            }
        }

        /**
         * 
         */
        public function ajax(){
            $db = new MySQL();
            $resultDb = $db
                ->setTable($_POST['right-table'])
                ->setPage(1)
                ->setRowsPerPage(20)
                ->select(
                    Array(
                        "label"=>$_POST['right-label'],
                        "value"=>$_POST['right-fk']
                    ),
                    "`{$_POST['right-table']}`.`{$_POST['right-label']}` like '{$_POST['value']}%'",
                    false
                )
            ;
            echo json_encode( $resultDb );
        }
    }
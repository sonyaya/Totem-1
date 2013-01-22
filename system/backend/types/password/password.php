<?php
    class password{
        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $thisLabel
         * @return type
         */
        public function validate($thisData, $thisColumn, &$allData, $thisLabel){
            if($thisData !== $allData["$thisColumn-repeat"]){
                return Array( "error" => true, "message" => "Os campo $thisLabel não são iguais." );
            }else{
                return Array( "error" => false );
            }
        }

        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         */
        public function beforeInsert(&$thisData, $thisColumn, &$allData){
            $this->beforeUpdateAndInsert($thisData, $thisColumn, $allData);
        }


        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         */
        public function beforeUpdateAndInsert(&$thisData, $thisColumn, &$allData){
            if( empty($allData[$thisColumn]) ){
                unset($allData[$thisColumn]);
            }else{
                $thisData = backend\User::generatePasswordHash($thisData);
            }
            unset($allData["$thisColumn-repeat"]);
        }
    }
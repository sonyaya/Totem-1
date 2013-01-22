<?php
    class textarea{
        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $thisLabel
         */
        public function validate($thisData, $thisColumn, &$allData, $parameters, $thisLabel){
             if( empty($thisData) ){
                return Array( "error" => true, "message" => "O campo $thisLabel, não pode ser vazio." );
            }else{
                return Array( "error" => false );
            }
        }
    }
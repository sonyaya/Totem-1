<?php
    class number{
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
            if( !is_numeric($thisData) ){
                return Array( "error" => true, "message" => "O campo $thisLabel, deve ser numérico." );
            }elseif( $thisData > $parameters['max'] || $thisData < $parameters['min'] ){
                return Array( "error" => true, "message" => "O campo $thisLabel, deve ser maior que {$parameters['min']} e menor que {$parameters['max']}." );
            }elseif( $thisData % $parameters['step'] !== 0){
                return Array( "error" => true, "message" => "O campo $thisLabel, deve ser multiplo de {$parameters['step']}." );
            }else{
                return Array( "error" => false );
            }
        }
    }
<?php
    class jsonArray{
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
            $thisData = array(
                "val" => htmlentities($thisData),
                "lis" => json_decode($thisData)
            );
        }
    }
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
        
        /**
         * 
         * @param type $thisData
         * @param type $thisRow
         * @param type $thisColumn
         * @param type $allData
         */
        public function beforeList(&$thisData, $thisRow, $thisColumn, &$allData){
            $arr = json_decode($thisData);
            if(is_array($arr) ){
                $thisData = "<div class='jsonArray'><ul class='list'><li>".implode("</li><li>", $arr)."</li></ul></div>";
            }else{
                $thisData = "";
            }
        }
    }
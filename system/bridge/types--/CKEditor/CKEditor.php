<?php
    class CKEditor{
        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $thisLabel
         */
        #public function validate($thisData, $thisColumn, &$allData, $parameters, $thisLabel){}

        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $pKey
         */
        #public function beforeInsert(&$thisData, $thisColumn, &$allData, $parameters, $pKey){}

        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $pKey
         */
        #public function beforeUpdate(&$thisData, $thisColumn, &$allData, $parameters,  $pKey){}

        /**
         * 
         * @param type $thisData
         * @param type $thisRow
         * @param type $thisColumn
         * @param type $allData
         * /
        #public function beforeList(&$thisData, $thisRow, $thisColumn, &$allData){}
        
        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $pKey
         */
        #public function beforeDelete(&$thisData, $thisColumn, &$allData, $parameters, $pKey){}

        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $toTypeLayout
         * @param type $pKey
         */
        #public function beforeLoadDataToForm(&$thisData, $thisColumn, &$allData, $parameters, &$toTypeLayout, $pKey){}

        /**
         * 
         * @global type $_M_CONFIG
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $pKey
         */
        #public function afterInsert(&$thisData, $thisColumn, &$allData, $parameters, $pKey){}

        /**
         * 
         * @global type $_M_CONFIG
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $pKey
         * @param type $key
         */
        #public function afterUpdate(&$thisData, $thisColumn, &$allData, $parameters, $pKey){}

        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $pKey
         */
        #public function afterDelete(&$thisData, $thisColumn, &$allData, $parameters, $pKey){}

        /**
         * 
         * @global type $_M_CONFIG
         */
        #public function ajax(){}
    }
<?php
    class dateBr{


        /**
         * 
         * @param type $thisData
         * @param type $thisColumn
         * @param type $allData
         * @param type $parameters
         * @param type $pKey
         */
        public function beforeInsert(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            $this->beforeInsertAndUpdate($thisData, $thisColumn, $allData, $parameters, $pKey);
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
            $this->beforeInsertAndUpdate($thisData, $thisColumn, $allData, $parameters, $pKey);
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
            // busca start e stop
            $start = str_replace('now', 'date("Y")' , strtolower($parameters['year']['start']) ) ;
            $stop  = str_replace('now', 'date("Y")' , strtolower($parameters['year']['stop' ]) ) ;

            $start = (integer)eval("return $start;");
            $stop  = (integer)eval("return $stop ;");

            // date time do update e do insert
            if( !empty($thisData) ){
                $dateTime = explode(" ", $thisData);
                $date = explode("-", $dateTime[0]);
                $loaded = Array(
                    "date" => Array(
                        "year"  => $date[0] ,
                        "month" => $date[1] ,
                        "day"   => $date[2]
                    )
                );
            }else{
                $loaded = Array(
                    "date" => Array(
                        "year"  => date('Y') ,
                        "month" => date('m') ,
                        "day"   => date('d')
                    )
                );
            }

            // envia pro layout do type
            $toTypeLayout = Array(
                "loaded" => $loaded,
                "year" => Array( 
                    "start" => $start, 
                    "stop"  => $stop
                )
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
        public function beforeInsertAndUpdate(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            if( $thisData['year'] !== "--"){
                $thisData = "{$thisData['year']}-{$thisData['month']}-{$thisData['day']}";    
            }else{
                $thisData = null;
            }
        }
    }
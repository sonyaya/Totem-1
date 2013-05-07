<?php
    class meioMask{
        public function validate($thisData, $thisColumn, &$allData, $parameters, $thisLabel){
            if( empty($thisData) && !@$parameters['nullable'] ){
                return Array( "error" => true, "message" => "O campo $thisLabel, não pode ser vazio." );
            }else{
                // valida quantidade de digitos nas mascaras
                $count['phone']    = 14;
                $count['phone-us'] = 15;
                $count['cpf']      = 14;
                $count['cnpj']     = 18;
                $count['date']     = 10;
                $count['date-us']  = 10;
                $count['cep']      = 10;
                $count['time']     = 5;
                $count['cc']       = 19;

                if( isset($parameters['mask']) ){
                    if( in_array( $parameters['mask'] , array_keys($count)) ){
                        if( mb_strlen($thisData, 'utf8') < $count[ $parameters['mask'] ] ){
                            return Array( "error" => true, "message" => "O campo $thisLabel, não foi preenchido corretamente." );
                        }
                    }
                }

                // sem erros
                return Array( "error" => false );
            }
        }
        
        public function beforeInsert(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            $this->beforeInsertAndUpdate($thisData, $thisColumn, $allData, $parameters, $pKey);
        }
        
        public function afterInsert(&$thisData, $thisColumn, &$allData, $parameters, $pKey){

        }
        
        public function beforeUpdate(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            $this->beforeInsertAndUpdate($thisData, $thisColumn, $allData, $parameters, $pKey);
        }
        
        public function afterUpdate(&$thisData, $thisColumn, &$allData, $parameters, $pKey){

        }
        
        public function beforeLoadDataToForm(&$thisData, $thisColumn, &$allData, $parameters, &$toTypeLayout, $pKey){
            $thisData = htmlentities($thisData);
        }
        
        public function beforeList(&$thisData, $thisRow, $thisColumn, &$allData, $parameters){

        }
        
        public function ajax(){

        }

        public function beforeInsertAndUpdate(&$thisData, $thisColumn, &$allData, $parameters, $pKey){
            if( isset($parameters['mask']) ){
                switch ($parameters['mask']) {
                    case 'phone':
                    case 'cep':
                    case 'cc':
                    case 'cpf':
                    case 'phone-us':
                        $thisData = preg_replace("/\D/", "", $thisData);
                        break;

                    case 'decimal-us':
                        $thisData = str_replace(",", "", $thisData);
                        break;

                    case 'integer':
                    case 'decimal':
                    case 'signed-decimal':
                    case 'signed-decimal-us':
                        $thisData = str_replace(".", "", $thisData);
                        $thisData = str_replace(",", ".", $thisData);
                        break;
                }
            }
        }
    }
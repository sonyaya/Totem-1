<?php

use backend\MySQL;

class FormEvents {
    /**
     * Executa ao enviar o formulário para ser salvo depois de atualizar
     */
    function afterLoadData($data, $pkey, $config){
        $key = array_keys($pkey);
        $key = $key[0];
        
        $keyVal = $pkey[ $key ];
        
        $db = new MySQL();
        $db->setTable($config['table']);
        
        $result = $db->select(Array("read_at"), "$key = $keyVal", true, false);
        
        if( empty($result['read_at']) ){
            $db->save(
                Array(
                    "read_at" => date("Y-m-d H:i-s"),
                    "read" => "1"
                )
                ,"$key = $keyVal")
            ;
        }
    }
}
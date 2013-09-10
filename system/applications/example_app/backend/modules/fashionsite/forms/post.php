<?php
class FormEvents {
    function beforeInsert(&$data, $pkey, $config){
        $data['insert_at'] = date("Y-m-d H:i:s");
    }
    
    function beforeUpdate(&$data, $pkey, $config){
        $data['update_at'] = date("Y-m-d H:i:s");
    }
}
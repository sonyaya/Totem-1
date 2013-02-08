<?php

    use backend\MySQL;

    class dashboard{
        public function __construct() {
        }
        
        public function getData(){
            $db = new MySQL();
            $db->setTable("_m_user_message");
            
            $data = $db->select(array("message", "from_user", "to_user"), 1, true, false);
            //$data = $db->rowsCount(1);
            
            return Array($data);
        }
    }
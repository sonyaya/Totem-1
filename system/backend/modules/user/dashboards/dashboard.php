<?php

    use backend\MySQL;

    class dashboard{
        public function __construct() {
        }
        
        public function getData(){
            $db = new MySQL();
            $db->setTable("_m_user");
            
            $data = $db->select(array("first_name", "login"), 1, true, false);
            $data = $db->rowsCount(1);
            
            return Array($data);
        }
    }
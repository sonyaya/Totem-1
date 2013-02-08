<?php

    use backend\MySQL;

    class dashboard{
        public function __construct() {
        }
        
        public function getData(){
            $db = new MySQL();
            $db->setTable("_m_user_message");
            
            $data = $db->select(array("id", "message", "from_user", "send_at"), "to_user = {$_SESSION['user']['id']}", true, false);
            //$data = $db->rowsCount(1);
            
            return Array($data);
        }
    }
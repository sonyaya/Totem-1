<?php

    use backend\MySQL;

    class dashboard{
        public function __construct() {
        }
        
        public function getData(){
            $db = new MySQL();
            $db->setTable("_m_user_message");
            
            // Mensagens
            $messages = $db->select(
                array(
                    "id", 
                    "from_user", 
                    "send_at" => "(DATE_FORMAT(send_at, '%d/%m/%Y %H:%i:%s'))",
                    "from_user_name" => array(
                        "from_user",
                        "_m_user.id",
                        array("first_name", "middle_name", "last_name"),
                        "concat" => array(" ")
                    )
                ), 
                "to_user = {$_SESSION['user']['id']} ORDER BY send_at DESC", 
                true, 
                false
            );
               
            // Número de mensagens
            $messagesCount = $db->rowsCount(1);
            
            // Envia pra tela
            return Array(
                "messages" => $messages,
                "messagesCount" => $messagesCount
            );
        }
    }
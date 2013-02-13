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
                    "title", 
                    "send_at" => "(DATE_FORMAT(send_at, '%d/%m/%Y %H:%i:%s'))",
                    "from_user", 
                    "from_user_name" => array(
                        "from_user",
                        "_m_user.id",
                        array("first_name", "middle_name", "last_name"),
                        "concat" => array(" ")
                    )
                ), 
                "`read`=false AND `to_user`={$_SESSION['user']['id']} ORDER BY send_at DESC", 
                false, 
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
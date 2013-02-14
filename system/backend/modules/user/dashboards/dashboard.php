<?php

    use backend\MySQL;

    class dashboard{
        public function __construct() {
        }
        
        public function getData(){
            $db = new MySQL();
            $db->setTable("_m_user_message");
            
            // Novas Mensagens
            $messages = $db->select(
                array(
                    "id", 
                    "title", 
                    "send_at" => "(DATE_FORMAT(send_at, '%d/%m/%Y às %H:%i'))",
                    "from_user", 
                    "from_user_name" => array(
                        "from_user",
                        "_m_user.id",
                        array("first_name", "middle_name", "last_name"),
                        "concat" => array(" ")
                    )
                ), 
                $where = "
                    `read`=false 
                    AND (`to_user`={$_SESSION['user']['id']} or `to_user`=0) 
                    ORDER BY 
                        send_at 
                    DESC
                ", 
                false, 
                false
            );
            $messagesCountNew = $db->rowsCount($where);
            
            // Mensagens Antigas
            $oldMessages = $db->select(
                array(
                    "id", 
                    "title", 
                    "send_at" => "(DATE_FORMAT(send_at, '%d/%m/%Y às %H:%i'))",
                    "from_user", 
                    "from_user_name" => array(
                        "from_user",
                        "_m_user.id",
                        array("first_name", "middle_name", "last_name"),
                        "concat" => array(" ")
                    )
                ), 
                $where = "
                    `read`=true 
                    AND (`to_user`={$_SESSION['user']['id']} or `to_user`=0) 
                    ORDER BY 
                        send_at DESC
                    LiMIT
                        30
                ", 
                false, 
                false
            );
            $messagesCountOld = $db->rowsCount($where);
            
            // Mensagens Enviadas Lidas
            $readSendMessages = $db->select(
                array(
                    "id", 
                    "title", 
                    "send_at" => "(DATE_FORMAT(send_at, '%d/%m/%Y às %H:%i'))",
                    "from_user", 
                    "from_user_name" => array(
                        "from_user",
                        "_m_user.id",
                        array("first_name", "middle_name", "last_name"),
                        "concat" => array(" ")
                    )
                ), 
                $where = "
                    `read`=true 
                    AND (`from_user`={$_SESSION['user']['id']}) 
                    ORDER BY 
                        send_at DESC
                    LiMIT
                        30
                ", 
                false, 
                false
            );          
            $messagesCountSendRead = $db->rowsCount($where);
            
            // Mensagens Enviadas Não Lidas
            $unreadSendMessages = $db->select(
                array(
                    "id", 
                    "title", 
                    "send_at" => "(DATE_FORMAT(send_at, '%d/%m/%Y às %H:%i'))",
                    "from_user", 
                    "from_user_name" => array(
                        "from_user",
                        "_m_user.id",
                        array("first_name", "middle_name", "last_name"),
                        "concat" => array(" ")
                    )
                ), 
                $where = "
                    `read`=false 
                    AND (`from_user`={$_SESSION['user']['id']}) 
                    ORDER BY 
                        send_at DESC
                    LiMIT
                        30
                ", 
                false, 
                false
            );
            $messagesCountSendUnread = $db->rowsCount($where);
            
            // Envia pra tela
            return Array(
                "messages" => $messages,
                "old-messages" => $oldMessages,
                "read-send-messages" => $readSendMessages,
                "unread-send-messages" => $unreadSendMessages,
                "messagesCount" => Array(
                    "old"=>$messagesCountOld, 
                    "new"=>$messagesCountNew, 
                    "send"=>$messagesCountSendRead+$messagesCountSendUnread,
                    "sendRead"=>$messagesCountSendRead,
                    "sendUnread"=>$messagesCountSendUnread
                )
            );
        }
    }
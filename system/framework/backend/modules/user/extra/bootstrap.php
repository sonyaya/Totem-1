<?php

class userBootstrap{
    function __construct($_M_THIS_CONFIG, $_M_USER, &$menu){
        foreach ($menu as $key=>$val){
            $db = new \backend\MySQL();
            $db->setTable("_m_user_message");
            $messagesCount = $db->rowsCount("to_user={$_M_USER['id']} AND `read`=0");
            
            if($messagesCount > 0){
                if(isset($val['load-from-module']) && $val['load-from-module']=="user"){
                    $menu[ $key ]['label'] .= "<span class='count'>$messagesCount</span>";
                    break;
                }
            }
        }
    }
}

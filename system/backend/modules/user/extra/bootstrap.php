<?php

    foreach ($menu as $key=>$val){
        if(isset($val['load-from-module']) && $val['load-from-module']=="user"){
            $menu[ $key ]['label'] .= "<span>123</span>";
            break;
        }
    }

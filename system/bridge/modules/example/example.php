<?php

    /**
     * Representational State Transfer - REST
     *
     * @author danielvarela
     */
    class rest{
        /**
         * Select 
         * 
         * Select all          : http://[url]
         * Select one by id    : http://[url]/[id]
         * Select one by column: http://[url]/[column]/[value]
         * 
         * curl -d 'sendKey=Value' --request GET get http://127.0.0.1/totem/bridge/[module]/[class] 
         * curl -d 'sendKey=Value' --request GET get http://127.0.0.1/totem/bridge/[module]/[class]/id
         */
        public function get(){
            return array_merge($_GET);
        }
        
        /**
         * Insert e Update
         * 
         * insert: http://[url]
         * update: http://[url]/[id]
         * 
         * curl -d 'sendKey=Value' --request POST http://127.0.0.1/totem/bridge/[module]/[class] 
         * curl -d 'sendKey=Value' --request POST http://127.0.0.1/totem/bridge/[module]/[class]/id
         */
        public function post(){
            return array_merge($_POST);
        }
        
        
        /**
         * Delete http://[url]/[id]
         * 
         * curl -d 'sendKey=Value' --request DELETE http://127.0.0.1/totem/bridge/[module]/[class]/id
         */
        public function delete(){
            return array_merge($_REQUEST);
        }
    }
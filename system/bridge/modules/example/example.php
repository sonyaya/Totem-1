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
         * curl -H "Content-Type: application/json" http://127.0.0.1/totem/bridge/[module]/[class] 
         * curl -H "Content-Type: application/json" http://127.0.0.1/totem/bridge/[module]/[class]/id
         */
        public function get(){
            return array_merge($_GET, $_POST);
        }
        
        /**
         * Insert e Update
         * 
         * insert: http://[url]
         * update: http://[url]/[id]
         * 
         */
        public function post(){}
        
        
        /**
         * Delete http://[url]/[id]
         * 
         */
        public function delete(){}
    }
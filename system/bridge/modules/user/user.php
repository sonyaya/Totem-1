<?php

    /**
     * Representational State Transfer - REST
     *
     * @author danielvarela
     */
    class user {
        /**
         * Select 
         * 
         * Select all          : http://[url]
         * Select one by id    : http://[url]/[id]
         * Select one by column: http://[url]/[column]/[value]
         * 
         */
        public function get(){}
        
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
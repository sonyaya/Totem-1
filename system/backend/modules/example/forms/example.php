<?php
class FormEvents {
    
    /**
     * Execut em formulários de atualização antes de carregar os valores do banco de dados
     * /
    function beforeLoadData($pkey, $config){
     
    } */
    
    /**
     * Execut em formulários de atualização depois de carregar os valores do banco de dados
     * 
     * @param array $loadedData
     * /
    function afterLoadData(&$loadedData, $pkey, $config){
        
    } */
    
    /**
     * Executa ao enviar o formulário para ser salvo antes de inserir
     * /
    function beforeInsert(&$data, $pkey, $config){
        
    } */
    
    /**
     * Executa ao enviar o formulário para ser salvo depois de inserir
     * /
    function afterInsert($data, $pkey, $config){
        
    } */
    
    /**
     * Executa ao enviar o formulário para ser salvo antes de atualiza
     * /
    function beforeUpdate(&$data, $pkey, $config){
        
    } */
    
    /**
     * Executa ao enviar o formulário para ser salvo depois de atualizar
     * /
    function afterUpdate($data, $pkey, $config){
        
    } */
    
    /**
     * Executa antes de deletar formulário
     * /
    function beforeDelete(&$data, $pkey, $config){
        
    }*/
    
    /**
     * Executa depois de deletar formulário
     * /
    function afterDelete($data, $pkey){
        
    } */
}
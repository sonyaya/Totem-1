<?php
class FormEvents {
    
    /**
     * Execut em formulários de atualização antes de carregar os valores do banco de dados
     * /
    function beforeLoadData(){
     * 
    } */
    
    /**
     * Execut em formulários de atualização depois de carregar os valores do banco de dados
     * 
     * @param array $loadedData
     * /
    function afterLoadData(&$loadedData, $pkey){
        
    } */
    
    /**
     * Executa ao enviar o formulário para ser salvo antes de inserir
     * /
    function beforeInsert(&$data, $pkey){
        
    } */
    
    /**
     * Executa ao enviar o formulário para ser salvo depois de inserir
     * /
    function afterInsert($data, $pkey){
        
    } */
    
    /**
     * Executa ao enviar o formulário para ser salvo antes de atualiza
     * /
    function beforeUpdate(&$data, $pkey){
        
    } */
    
    /**
     * Executa ao enviar o formulário para ser salvo depois de atualizar
     * /
    function afterUpdate($data, $pkey){
        
    } */
    
    /**
     * Executa antes de deletar formulário
     * /
    function beforeDelete(&$data, $pkey){
        
    }*/
    
    /**
     * Executa depois de deletar formulário
     * /
    function afterDelete($data, $pkey){
        
    } */
}
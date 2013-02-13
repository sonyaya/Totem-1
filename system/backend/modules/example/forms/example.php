<?php
class FormEvents {
    
    /**
     * Execut em formulários de atualização antes de carregar os valores do banco de dados
     */
    function beforeLoadData(){
        echo "before load data";
    }
    
    /**
     * Execut em formulários de atualização depois de carregar os valores do banco de dados
     * 
     * @param array $loadedData
     */
    function afterLoadData(&$loadedData){
       echo "after load data";
    }
    
    /**
     * Executa ao enviar o formulário para ser salvo antes de inserir
     */
    function beforeInsert(&$data){
    }
    
    /**
     * Executa ao enviar o formulário para ser salvo depois de inserir
     */
    function afterInsert($data){
        
    }
    
    /**
     * Executa ao enviar o formulário para ser salvo antes de atualiza
     */
    function beforeUpdate(&$data){
        
    }
    
    /**
     * Executa ao enviar o formulário para ser salvo depois de atualizar
     */
    function afterUpdate($data){
        
    }
    
    /**
     * Executa antes de deletar formulário
     */
    function beforeDelete(&$data){
        
    }    
    /**
     * Executa depois de deletar formulário
     */
    function afterDelete($data){
        
    }
}
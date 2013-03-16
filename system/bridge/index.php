<?php

    #
    # Como utilizar
    #
    # LIST ONE
    # curl -X GET http://127.0.0.1/totem/bridge/common/forms/country/1
    #
    # LIST VARIUS
    # curl -d 'page=1' -X GET http://127.0.0.1/totem/bridge/common/forms/country
    #
    # INSERT
    # curl -d 'name=TESTE&iso-code-alfa3=XXX' -X POST http://127.0.0.1/totem/bridge/common/forms/country
    #
    # UPDATE
    # curl -d 'name=TESTE&iso-code-alfa3=XXX' -X POST http://127.0.0.1/totem/bridge/common/forms/country/1
    #
    # DELETE
    # curl -X DELETE http://127.0.0.1/totem/bridge/common/forms/country/1
    #



    # INICIALIAZAÇÃO 
    require_once "bootstrap.php";

    # USED CLASSES
    use backend\Frontend;
    use backend\Form;
    
    # SYS VAR
    $sys = Array(
       "config" => array_merge(
           $_M_THIS_CONFIG,
           Array(
               "upload-path" => $_M_CONFIG->system['upload-path']
           )
       )
    );
    
    
    # EXECUTA O REST
    if(file_exists($file = "modules/{$_GET['_m_action']}.php")){
        # RUN
        require_once($file);
        $rest = new rest();
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        echo json_encode( $rest->$method() ); 
    }else{
        # RODA AS FUNÇÕES PADRÕES DO BACKEND
        $path = "../backend/modules/{$_GET['_m_action']}";
        switch( strtolower($_SERVER['REQUEST_METHOD']) ){
            case "get":
                $page        = (empty($_GET['page'])       ) ? null : $_GET['page'];
                $rowsPerPage = (empty($_GET['rowsPerPage'])) ? null : $_GET['rowsPerPage'];
                $orderBy     = (empty($_GET['orderBy'])    ) ? null : $_GET['orderBy'];

                if( isset($_GET['_m_id']) ){
                    $condition   = "_M_PRIMARY_KEY_VALUE_ = '{$_GET['_m_id']}'";
                    $form = new Form();
                    $return = $form->getViewData($path, $page, $rowsPerPage, $orderBy, $condition, "bridge");
                    echo json_encode($return); 
                }else{
                    $condition   = (empty($_GET['condition'])  ) ? null : $_GET['condition'];
                    $form = new Form();
                    $return = $form->getViewData($path, $page, $rowsPerPage, $orderBy, $condition, "bridge");
                    echo json_encode($return); 
                }
                break;

            case "post":
                if( isset($_GET['_m_id']) ){
                    $form = new Form();
                    $return = $form->saveForm($path, array_merge(Array("_M_ACTION"=>"update:{$_GET['_m_id']}"), $_POST));
                    echo json_encode($return);        
                }else{
                    $form = new Form();
                    $return = $form->saveForm($path, array_merge(Array("_M_ACTION"=>"insert"), $_POST));
                    echo json_encode($return);     
                }
                break;

            case "delete":
                $form = new Form();
                $return = $form->deleteForm($path, $_GET['_m_id']);
                echo json_encode($return);
                break;
        }
    }
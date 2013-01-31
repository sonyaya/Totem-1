$(function(){
    $("#bt-save-form").click(function(){
        $(this).closest("form").submit();
        return false;
    });

    $("#form-insert-and-update").submit(function(e){

        if(e.cancelable === true) return false; // impede apertar enter e enviar o formulário

        $.post(
            "?action=save-form&form=&m.var:form;",
            $(this).closest("#form-insert-and-update").serialize(),
            function(data){
                if( data.error ){
                    mesageConcat = 'Os seguintes erros ocorreram: \r\n';
                    $.each(data.message, function(key, val){
                        mesageConcat += "- "+val+"\r\n";
                    });
                    alert(mesageConcat);
                }else{
                    if( window.opener ){
                        //window.close();
                    }else{
                        //window.location = "?action=view-update-form&form=&m.var:form;&id=" + data.result._M_PRIMARY_KEY_VALUE_;
                    }
                }
            },
            "json"
        );
        return false;
    });
});
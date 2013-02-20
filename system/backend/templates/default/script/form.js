$(function(){
    $("#bt-save-form").click(function(){
        $("#form-insert-and-update").submit();
        return false;
    });

    $("#form-insert-and-update").submit(function(e){

        if(e.cancelable === true) return false; // impede apertar enter e enviar o formulário

        $.post(
            "?action=save-form&form=" + layout.uri("form"),
            $(this).closest("#form-insert-and-update").serialize(),
            function(data){
                if( data.error ){
                    if( typeof data.message !== "string"){
                        mesageConcat = 'Os seguintes erros ocorreram: \r\n' ;
                        $.each(data.message, function(key, val){
                            mesageConcat += "- "+val+"\r\n";
                        });
                        alert(mesageConcat);
                    }else{
                        alert(data.message);
                    }
                }else{
                    if( window.opener ){
                        window.close();
                    }else{
                        window.location = "?action=view-update-form&form=&m.var:form;&id=" + data.result._M_PRIMARY_KEY_VALUE_;
                    }
                }
            },
            "json"
        );

        return false;
    });

    $("#bt-delete-form").click(function(){
        msg  = "ATENÇÃO!\r\n\r\n"
        msg += "- Você esta prestes a deletar este formulário, esta ação não pode ser desfeita, clique \r\n";
        msg += "em OK caso realmente deseja excluir estas informações ou clique em CANCELAR \r\n";
        msg += "para impedir a exclusão destes dados.";
        if( confirm(msg) ){
            $.post(
                "?action=delete-form&form=&m.var:form;&id=&m.var:_GET.id;",
                function(data){
                    if(data.error){
                        alert(data.message);
                    }else{
                        window.location = "?action=view-list-form&form=&m.var:form;";
                    }
                },
                "json"
            );
        }
        return false;
    });
});
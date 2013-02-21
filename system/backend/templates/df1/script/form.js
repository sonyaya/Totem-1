$(function(){
    //
    $("body").on("click", ".bt-save-form", function(){
        $form = $(this).closest("form");
        $form.submit();
        return false;
    });

    //
    $("body").on("submit", "form.form-insert-or-update", function(e){
        
        if(e.cancelable === true) return false; // impede apertar enter e enviar o formulário
        
        layout.ajax.showLoader();
        $.post(
            "?action=save-form&path=" + layout.uri("path"),
            $(this).serialize(),
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
                    layout.ajax.hideLoader();
                }else{
                    layout.ajax.hideLoader();
                }
            },
            "json"
        );

        return false;
    });
});

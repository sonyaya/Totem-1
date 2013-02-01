$(function(){
    $("body").on("click", ".bt-save-form", function(e){
        alert("bt save");
        $form = $(this).closest("form");
        $form.submit();
        return false;
    });
});

//
function saveForm(form){
    $.post(
        "?action=save-form&form=" + layout.uri("form"),
        $(form).serialize(),
        function(data){
            if( data.error ){
                mesageConcat = 'Os seguintes erros ocorreram: \r\n';
                $.each(data.message, function(key, val){
                    mesageConcat += "- "+val+"\r\n";
                });
                alert(mesageConcat);
            }else{
                alert("mensagem aqui");
            }
        },
        "json"
    );
    return false;
}
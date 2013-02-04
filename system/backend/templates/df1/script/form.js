$(function(){
    $("body").on("click", ".bt-save-form", function(e){
        $form = $(this).closest("form");
        $form.submit();
        return false;
    });
});

//
function saveForm(form){
    layout.ajax.showLoader();
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
                layout.ajax.hideLoader();
            }else{
                layout.ajax.hideLoader();
            }
        },
        "json"
    );
    return false;
}
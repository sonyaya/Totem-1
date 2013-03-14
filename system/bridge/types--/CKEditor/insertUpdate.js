$(function(){
    $("form").submit(function(){
        for ( instance in CKEDITOR.instances ){
            CKEDITOR.instances[instance].updateElement();
        }
    });
});
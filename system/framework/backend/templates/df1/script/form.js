$(function(){
    //
    $("body").on("click", ".bt-save-form", function(){
        $form = $(this).closest("form");
        $form.submit();
        return false;
    });

    //
    $("body").on("submit", "form.insert", layout.form.insert);
    $("body").on("submit", "form.update", layout.form.update);
    $("body").on("submit", "form.dummy" , layout.form.runDummyForm);
});

$(function(){
    $('body').on('keyup', '.number', function () { 
        this.value = this.value.replace(/[^0-9\.]/g,'');
    });
});
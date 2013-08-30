$(function(){
    $('input.varchar').setMask();
    
    // telefones para SP remova isso quando o brasil
    // mudar o padrão para todo território, e atualiza
    // o meio mask, até lgo! rs
    $("[alt=phone]").live('keypress', function (event) {  
        var target, phone, element;  
        target = (event.currentTarget) ? event.currentTarget : event.srcElement;  
        phone = target.value.replace(/\D/g, '');  
        element = $(target);  
        element.unsetMask();  
        if (phone.length > 5 && phone.substr(0,3) == "119") { //ele só vai colocar no formato de SP quando for ddd 11 e iniciar com 9.  
          element.setMask("(99) 99999-9999");  
        } else {  
          element.setMask("(99) 9999-9999");  
        }  
    });
    
});
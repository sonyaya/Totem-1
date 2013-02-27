$(function(){
    list = {};
    list.action  = layout.uri("action"); 
    list.form    = layout.uri("path");
    list.cond    = ($.trim(cond = layout.uri("cond")) == "") ? [] : $.parseJSON(cond);
    list.page    = layout.uri("page");
    list.orderBy = layout.uri("orderBy");
});


var layout = new Object();

/**
 * SE ENCARREGA DE ABRIR POPUPS
 */
layout.popup = function(url, title){
    if( layout.uri("path") == layout.uri("path", url) ){
        newWindow = window.open(url, title);        
    }else{
        var w = screen.width / 1.2;
        var w = 650;
        var barSize = 70;
        newWindow = window.open(
            url,
            title + Math.random(),
            'toolbar=no,'
            +'scrollbars=yes,'
            +'location=no,'
            +'resizable=yes,'
            +'width='+w+','
            +'height=0'
        );

        newWindow.onload = function(){
            $popBody = newWindow.$('body');
            var h = $popBody.outerHeight() + barSize;
            newWindow.resizeTo( w, h );
        }        
    }
}

/**
 * SE ENCARREGA DE FACILTAR A NAVEGAÇÃO PELA URI
 */
layout.uri = (function(key, uriArray){
    if(typeof uriArray == "undefined")
        uriArray = window.location.search
    uriArray = uriArray.split(/[?&](.*?)=.*?/im);
    pos = $.inArray(key, uriArray);
    if( pos % 2 ){
        return uriArray[ pos+1 ];
    }else{
        return null;
    }
})

/**
 * 
 */
layout.ajax = {};

/**
 * SE ENCARREGA DE MOSTRAR O LOADER QUANDO UM AJAX FOR EXECUTADO
 */
layout.ajax.showLoader = (function(){});


/**
 * SE ENCARREGA DE ESCONDER O LOADER DOS AJAX
 */
layout.ajax.hideLoader = (function(){});


/**
 *
 */
layout.list = {};

/**
 *
 */
layout.list.button = {};

/**
 * 
 */
layout.list.button.order = (function(){
    if( $(this).hasClass('CHANGE_TO_DESC') ){
        order = $(this).text() + "!";
    }else{
        order = $(this).text();
    }

    order = encodeURIComponent(order);

    window.location = 
          "?action=" + list.action
        + "&path=" + list.form
        + "&page=" + list.page
        + "&orderBy=" + list.order
        + "&cond=" + JSON.stringify(list.cond)
    ;
});

/**
 * 
 */
layout.list.button.reload = (function(){});

/**
 * 
 */
layout.list.button.page = (function(){
    window.location = 
        "?action=" + list.action
       + "&path=" + list.form
       + "&" + $(this).attr("rel")
       + "&orderBy=" + list.orderBy
       + "&cond=" + JSON.stringify(list.cond)
    ;
});

/**
 * 
 */
layout.list.button.nextPage = (function(){
     $(this).closest("div.action-bar").find(".active").next().click();
});

/**
 * 
 */
layout.list.button.prevPage = (function(){
    $(this).closest("div.action-bar").find(".active").prev().click();
});

/**
 * 
 */
layout.list.button.search = (function(){
    countColumns = $(this).find("div.search").length;

    search = [];
    for(i=1; i<=countColumns; i++){
        search.push([ 
            $("#cond-and-or-"+i).val() || " ", 
            $("#cond-column-"+i).val(),
            $("#cond-comparison-"+i).val(),
            $("#cond-value-"+i).val()
        ]);
    }

    window.location = 
        "?action=" + action
        + "&path=" + form
        + "&page=" + page
        + "&orderBy=" + orderBy
        + "&cond=" + JSON.stringify(search)
    ;

    return false;
});

/**
 * 
 */
layout.list.action = {};

/**
 * 
 */
layout.list.action.edit = (function(){
    layout.popup("?action=view-update-window-form&path=" + list.form + "&id=" + $(this).attr('href'), "Atualizando - cod. "+$(this).attr("href"));
    return false;
});

/**
 * 
 */
layout.list.action.delete = (function(){
    msg  = "ATENÇÃO!\r\n\r\n"
    msg += "- Você esta prestes a deletar um registro do banco de dados, \r\n"
    msg += "esta ação não poderá ser desfeita, clique em OK se realmente \r\n"
    msg += "deseja eliminar este registo.";
    if( confirm(msg) ){
        var delId = $(this).attr('href');
        $.post(
            "?action=delete-form&path="+ list.form +"&id=" + delId,
            function(data){
                if(data.error){
                    alert(data.message);
                }else{
                    if( $("table tbody tr").length == 1 ){
                        location.reload(true);
                    }else{
                        $("tr[rel="+ delId +"]").remove();
                    }
                }
            },
            "json"
        );
    }
    return false;
});

/**
 *
 */
layout.list.button.select = (function(){
    alert("do something");
    return false;
});

/**
 * 
 */
layout.list.tab = {};

/**
 * 
 */
layout.list.tab.close = (function(){});

/**
 * 
 */
layout.form = {};

/**
 * 
 */
layout.form.insert = (function(e){
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
            }else{
                // Fecha se for um popup
                window.close();
            }
            layout.ajax.hideLoader();
        },
        "json"
    );

    return false;
});

/**
 * 
 */
layout.form.update = (function(e){
    layout.form.insert(e);
});

/**
 * 
 */
layout.form.runDummyForm = (function(e){
    alert("fazer este negocio abrir em uma novajanela.");
    layout.form.insert(e);
});


/**
 * 
 */
layout.form.delete = (function(){
    msg  = "ATENÇÃO!\r\n\r\n"
    msg += "- Você esta prestes a deletar este formulário, esta ação não pode ser desfeita, clique \r\n";
    msg += "em OK caso realmente deseja excluir estas informações ou clique em CANCELAR \r\n";
    msg += "para impedir a exclusão destes dados.";
    if( confirm(msg) ){
        $.post(
            "?action=delete-form&path="+ layout.uri("path") +"&id="+ layout.uri("id"),
            function(data){
                if(data.error){
                    alert(data.message);
                }else{
                    window.opener.location.reload(true);
                    window.close();
                }
            },
            "json"
        );
    }
    return false;
});
//    
$(function(){
    // -- CRIA O LOADER --------------------------------------------------------
    
    var opts = {
        lines: 6,             // The number of lines to draw
        length: 0,            // The length of each line
        width: 10,            // The line thickness
        radius: 11,           // The radius of the inner circle
        corners: 1,           // Corner roundness (0..1)
        rotate: 0,            // The rotation offset
        color: '#fff',        // #rgb or #rrggbb
        speed: 0.5,           // Rounds per second
        trail: 10,            // Afterglow percentage
        shadow: true,         // Whether to render a shadow
        hwaccel: true,        // Whether to use hardware acceleration
        className: 'spinner', // The CSS class to assign to the spinner
        zIndex: 2e9,          // The z-index (defaults to 2000000000)
        top: 'auto',          // Top position relative to parent in px
        left: 'auto'          // Left position relative to parent in px
    };
    
    var target = document.getElementById('canvasloader-container');
    var spinner = new Spinner(opts).spin(target);
      
    // -- CLASSE ESPECIFICA DA TELA DE LISTAGEM --------------------------------
    
    list = {};

    // ATUALIZA ATRIBUTOS PASSADOS POR URL 
    list.updateAttributes = (function(){
        list.action  = layout.uri("action"); 
        list.form    = layout.uri("path");
        list.cond    = ($.trim(list.cond = layout.uri("cond")) == "") ? [] : $.parseJSON(list.cond);
        list.page    = layout.uri("page");
        list.orderBy = layout.uri("orderBy");
    });

    // ADICIONA CLASSE A COLUNA QUE ESTA 
    // SENDO UTILIZADA COMO ORDENADORA
    list.addOrderClass = (function(orderBy){
        orderBy = (typeof orderBy == "undefined") ? layout.uri("orderBy") : orderBy ;
        orderBy = orderBy.split('/');
        $.each(orderBy, function(key, val){
            val = decodeURIComponent(val);
            if( val.indexOf("!") > 0 ){
                DOM = val.replace("!", "");
                DOM = $("th[rel='"+DOM+"']").addClass('CHANGE_TO_ASC');
            }else{
                DOM = val.replace("!", "");
                DOM = $("th[rel='"+DOM+"']").addClass('CHANGE_TO_DESC');
            }
        });
    });
    
    // ADICIONA BEFORE EM TODOS OS TD DO 
    // BODY CONFORME OS VALORES DE THEAD
    list.addInlineTitles = (function(){
        $("thead tr th").each(function(){
            $this = $(this);
            index = $this.index();
            if( index > 0 ){
                title = $this.text();
                $td = $("tbody tr").find("td:eq("+index+") a").before("<span class='inline-title'>"+title+": </span>");
            }
        });
    });
    
});
   
   
   
// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------
// -----------------------------------------------------------------------------



// OBJETO DE LAYOUT
var layout = {};

/**
 * SE ENCARREGA DE ABRIR POPUPS
 * 
 */
layout.popup = (function(url, title){
    // abrir em uma nova aba
    if( layout.uri("path") == layout.uri("path", url) ){
        // Abre tela de load
        layout.ajax.showLoader();
        
        // Número de abas
        no = $("#popup-menu li.popup-tab").length + 1;

        // cria a tab
        $newTab = $("<li class='popup-tab' id='tab-popup-"+no+"'><span>x</span> "+title+"</li>");

        // cria a janela
        $newWindow = $("<div class='window for-tab-popup-"+no+"'><span class='load'>Aguarde...</span></div>");
        $newWindow.load(url);

        // adiciona na interface os novos elementos
        $newTab.prependTo("#popup-menu");
        $newWindow.prependTo("#popup-window");

        // clica na nova aba criada
        $newTab.click();
        
        // Fecha a tela de load
        layout.ajax.hideLoader();
    }else{
        var w = 600;
        var barSize = 70;
        var popup = 
            window.open(
                url + "&popup=1",
                title + Math.random(),
                'toolbar=no,'
                +'scrollbars=yes,'
                +'location=no,'
                +'resizable=yes,'
                +'width='+w+','
                +'height=0'
            )
        ;
            
        popup.onload = function(){
            $popBody = this.$('body');
            var h = $popBody.outerHeight() + barSize;
            this.resizeTo( w, h );
        };
    }
});

/**
 * SE ENCARREGA DE FACILTAR A NAVEGAÇÃO PELA URI
 * 
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
});

/**
 * 
 */
layout.ajax = {};

/**
 * 
 */
layout.ajax.showLoader = (function(){
    $('#loader').fadeIn();
})

/**
 * 
 */
layout.ajax.hideLoader = (function(){
    $('#loader').fadeOut();
})

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

    list.updateAttributes();
    url = "&path=" + list.form
        + "&page=" + list.page
        + "&orderBy=" + order
        + "&cond=" + JSON.stringify(list.cond)
    ;

    layout.ajax.showLoader();
    $(".list-content").load("?action=view-list-window-form" + url, function(){ 
        list.addOrderClass(order); 
    });
    layout.ajax.hideLoader();

    var stateObj = { foo: "bar" };
    history.pushState(stateObj, null, "?action=view-listAndInsert-form"+url);
});

/**
 * 
 */
layout.list.button.reload = (function(){
    list.updateAttributes();
    
    url = "&path="+ list.form
        + "&page=" + list.page
        + "&orderBy="+ list.orderBy
        + "&cond="+ JSON.stringify(list.cond)
    ;

    layout.ajax.showLoader();
    $(".list-content").load("?action=view-list-window-form" + url, function(){ 
        list.addOrderClass();
        list.addInlineTitles();
    });

    layout.ajax.hideLoader();
    });

    // PAGINAÇÃO
    $(".window.list").on("click", ".bt-page", function(){
    list.updateAttributes();
    url = "&path=" + list.form
        + "&" + $(this).attr("rel")
        + "&orderBy=" + list.orderBy
        + "&cond=" + JSON.stringify(list.cond)
    ;

    //
    layout.ajax.showLoader();
    
    $(".list-content").load("?action=view-list-window-form" + url, function(){ 
        list.addOrderClass(); 
    });
    
    layout.ajax.hideLoader();
});

/**
 * 
 */
layout.list.button.page = (function(){});

/**
 * 
 */
layout.list.button.nextPage = (function(){
    $next = $(this).closest(".action-bar").find(".active").next(".bt-page");
    if(typeof $next[0] == "object"){
        $next.click();
    }
});

/**
 * 
 */
layout.list.button.prevPage = (function(){
    $prev = $(this).closest(".action-bar").find(".active").prev(".bt-page");
    if(typeof $prev[0] == "object"){
        $prev.click();
    }
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
        "?action=" + list.action
        + "&path=" + list.form
        + "&page=" + list.page
        + "&orderBy=" + list.orderBy
        + "&cond=" + JSON.stringify(list.search)
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
    msg  = "iiiii ATENÇÃO!\r\n\r\n"
    msg += "- Você esta prestes a deletar um registro do banco de dados, \r\n"
    msg += "esta ação não poderá ser desfeita, clique em OK se realmente \r\n"
    msg += "deseja eliminar este registo.";
    if( confirm(msg) ){
        var delId = $(this).attr('href');
        $.post(
            "?action=delete-form&path="+ layout.uri("path") +"&id=" + delId,
            function(data){
                if(data.error){
                    alert(data.message);
                }else{
                    $("button.bt-reload-list").click();
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
layout.list.action.select = (function(){
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
layout.list.tab.close = (function(){
    $this = $(this);
    $li = $this.closest("li");

    // vai pra próxima tab caso seja a tab atual
    if( $li.hasClass("active") ){
        $li.next().click();            
    }

    // remove os elementos
    $li.remove();
    $(".for-"+$li.attr("id")).remove();

    // previne ação padrão
    return false;
});

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
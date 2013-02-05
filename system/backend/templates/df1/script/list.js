$(function(){
    // 
    action  = layout.uri("action"); 
    form    = layout.uri("form");
    cond    = layout.uri("cond");
    page    = layout.uri("page");
    orderBy = layout.uri("orderBy");

    // ADICIONA CLASSE A COLUNA QUE ESTA 
    // SENDO UTILIZADA COMO ORDENADORA
    orderBy = orderBy.split('/');
    $.each(orderBy, function(key, val){
        if( val.indexOf("!") > 0 ){
            DOM = val.replace("!", "");
            DOM = $("th[rel='"+DOM+"']").addClass('CHANGE_TO_ASC');
        }else{
            DOM = val.replace("!", "");
            DOM = $("th[rel='"+DOM+"']").addClass('CHANGE_TO_DESC');
        }
    });

    // ORDENAÇÃO
    $(".window.list").on("click", "th", function(){
        if( $(this).hasClass('CHANGE_TO_DESC') ){
            order = $(this).text() + "!";
        }else{
            order = $(this).text();
        }

        window.location = 
            "?action=" + action
            + "&form=" + form
            + "&page=" + page
            + "&orderBy=" + order
            + "&cond=" + JSON.stringify(cond)
        ;
    });
    
    // ATUALIZAR TELA DE LISTAGEM NO DF1
    $(".window.list").on("click", ".bt-reload-list", function(){
        layout.ajax.showLoader();
        url = 
            "?action=view-list-window-form"
            +"&form="+ form
            +"&" + $(this).attr("rel")
            +"&orderBy="+ orderBy
            +"&cond="+ JSON.stringify(cond)
        ;
        
        $(".list-content").load(url)
        layout.ajax.hideLoader();
    });

    // PAGINAÇÃO
    $(".window.list").on("click", ".bt-page", function(){
        url = 
            "?action=view-list-window-form"
            + "&form=" + form
            + "&" + $(this).attr("rel")
            + "&orderBy=" + orderBy
            + "&cond=" + JSON.stringify(cond)
        ;
        
        layout.ajax.showLoader();
        $(".list-content").load(url)
        layout.ajax.hideLoader();
        
    });

    $(".window.list").on("click", ".next-page", function(){
        $next = $(this).closest(".action-bar").find(".active").next(".bt-page");
        if(typeof $next[0] == "object"){
            $next.click();
        }
    });

    $(".window.list").on("click", ".prev-page", function(){
        $prev = $(this).closest(".action-bar").find(".active").prev(".bt-page");
        if(typeof $prev[0] == "object"){
            $prev.click();
        }
    });

    // ACTIONS
    $(".window.list").on("click", ".edit", function(){
        layout.popup("?action=view-update-window-form&form=" + form + "&id=" + $(this).attr('href'), "Atualizando - cod. "+$(this).attr("href"));
        return false;
    });

    $(".window.list").on("click", ".delete", function(){
        msg  = "ATENÇÃO!\r\n\r\n"
        msg += "- Você esta prestes a deletar um registro do banco de dados, \r\n"
        msg += "esta ação não poderá ser desfeita, clique em OK se realmente \r\n"
        msg += "deseja eliminar este registo.";
        if( confirm(msg) ){
            var delId = $(this).attr('href');
            $.post(
                "?action=delete-form&form=&m.var:form;&id=" + delId,
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

    $(".window.list").on("click", ".select", function(){
        alert("select");
    });

    // PESQUISA / SEARCH
    $("#bt-search").click(function(){
        $("div.for-tab-search form").submit();
    });

    $("div.for-tab-search form").submit(function(){
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
            + "&form=" + form
            + "&page=" + page
            + "&orderBy=" + orderBy
            + "&cond=" + JSON.stringify(search)
        ;

        return false;
    });

    // POPULAR FORMULÁRIO DE PESQUISA / SEARCH
    if($.trim(cond) !== ""){
        cond = $.parseJSON(cond);
        $.each(cond, function(key, val){
            i = key+1;
            $("#cond-and-or-"+i).val( val[0] ), 
            $("#cond-column-"+i).val( val[1] ),
            $("#cond-comparison-"+i).val( val[2] ),
            $("#cond-value-"+i).val( val[3] )
        });
    }
});
    
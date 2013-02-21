$(function(){
    list = {};
    
    // 
    list.updateAttributes = function(){
        list.action  = layout.uri("action"); 
        list.form    = layout.uri("path");
        list.cond    = ($.trim(list.cond = layout.uri("cond")) == "") ? [] : $.parseJSON(list.cond);
        list.page    = layout.uri("page");
        list.orderBy = layout.uri("orderBy");
    }
    list.updateAttributes();

    // ADICIONA CLASSE A COLUNA QUE ESTA 
    // SENDO UTILIZADA COMO ORDENADORA
    list.addOrderClass = function(orderBy){
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
    }
    list.addOrderClass(list.orderBy);

    // ORDENAÇÃO
    $(".window.list").on("click", "th", function(){
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
    
    // ATUALIZAR TELA DE LISTAGEM NO DF1
    $(".window.list").on("click", ".bt-reload-list", function(){
        list.updateAttributes();
        url = "&path="+ list.form
            + "&page=" + list.page
            + "&orderBy="+ list.orderBy
            + "&cond="+ JSON.stringify(list.cond)
        ;
        
        layout.ajax.showLoader();
        $(".list-content").load("?action=view-list-window-form" + url, function(){ 
            list.addOrderClass();
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
        
        layout.ajax.showLoader();
        $(".list-content").load("?action=view-list-window-form" + url, function(){ 
            list.addOrderClass(); 
        });
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
        layout.popup("?action=view-update-window-form&path=" + list.form + "&id=" + $(this).attr('href'), "Atualizando - cod. "+$(this).attr("href"));
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
                "?action=delete-form&path="+ layout.uri("form") +"&id=" + delId,
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
            "?action=" + list.action
            + "&path=" + list.form
            + "&page=" + list.page
            + "&orderBy=" + list.orderBy
            + "&cond=" + JSON.stringify(list.search)
        ;

        return false;
    });

    // POPULAR FORMULÁRIO DE PESQUISA / SEARCH
    $.each(list.cond, function(key, val){
        i = key+1;
        $("#cond-and-or-"+i).val( val[0] ), 
        $("#cond-column-"+i).val( val[1] ),
        $("#cond-comparison-"+i).val( val[2] ),
        $("#cond-value-"+i).val( val[3] )
    });

});
    
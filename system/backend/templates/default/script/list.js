$(function(){
    // 
    action  = layout.uri("action"); 
    form    = layout.uri("path");
    cond    = ($.trim(cond = layout.uri("cond")) == "") ? [] : $.parseJSON(cond);
    page    = layout.uri("page");
    orderBy = layout.uri("orderBy");

    // ADICIONA CLASSE A COLUNA QUE ESTA 
    // SENDO UTILIZADA COMO ORDENADORA
    orderBy = orderBy.split('/');
    $.each(orderBy, function(key, val){
        if( val.indexOf("!") > 0 ){
            val = decodeURIComponent(val);
            DOM = val.replace("!", "");
            DOM = $("th[rel='"+DOM+"']").addClass('CHANGE_TO_ASC');
        }else{
            DOM = val.replace("!", "");
            DOM = $("th[rel='"+DOM+"']").addClass('CHANGE_TO_DESC');
        }
    });

    // ORDENAÇÃO
    $("table").on("click", "th", function(){
        if( $(this).hasClass('CHANGE_TO_DESC') ){
            order = $(this).text() + "!";
        }else{
            order = $(this).text();
        }

        order = encodeURIComponent(order);

        window.location = 
              "?action=" + action
            + "&path=" + form
            + "&page=" + page
            + "&orderBy=" + order
            + "&cond=" + JSON.stringify(cond)
        ;
    });

    // PAGINAÇÃO
    $(".list").on("click", ".bt-page", function(){
        window.location = 
             "?action=" + action
            + "&path=" + form
            + "&" + $(this).attr("rel")
            + "&orderBy=" + orderBy
            + "&cond=" + JSON.stringify(cond)
        ;
    });

    $(".next-page").click(function(){
        $(this).closest("div.action-bar").find(".active").next().click();
    });

    $(".prev-page").click(function(){
        $(this).closest("div.action-bar").find(".active").prev().click();
    });

    // ACTIONS
    $("table").on("click", ".edit", function(){
        layout.popup("?action=view-update-window-form&path=" + form + "&id=" + $(this).attr('href'), "Atualizando - cod. "+$(this).attr("href"));
        return false;
    });

    $("table").on("click", ".delete", function(){
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

    $("table").on("click", ".select", function(){
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
            + "&path=" + form
            + "&page=" + page
            + "&orderBy=" + orderBy
            + "&cond=" + JSON.stringify(search)
        ;

        return false;
    });

    // POPULAR FORMULÁRIO DE PESQUISA / SEARCH
    $.each(cond, function(key, val){
        i = key+1;
        $("#cond-and-or-"+i).val( val[0] ), 
        $("#cond-column-"+i).val( val[1] ),
        $("#cond-comparison-"+i).val( val[2] ),
        $("#cond-value-"+i).val( val[3] )
    });
    
});
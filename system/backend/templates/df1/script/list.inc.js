action = "&m.var:_GET.action;";
form = "&m.var:form;";
cond = <m.if cond='&m.var:bool:_GET.cond;'>&m.var:_GET.cond;</m.if><m.if cond='&m.var:bool:_GET.cond; -eq- false'>[]</m.if>;
page = "<m.if cond='&m.var:bool:_GET.page;'>&m.var:_GET.page;</m.if>";
orderBy = "<m.if cond='&m.var:bool:_GET.orderBy;'>&m.var:_GET.orderBy;</m.if>";

<m.if cond='&m.var:bool:_GET.orderBy;'>
// ADICIONA CLASSE A COLUNA QUE ESTA 
// SENDO UTILIZADA COMO ORDENADORA
orderBy = '&m.var:_GET.orderBy;';
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
</m.if>

// ORDENAÇÃO
$("table").on("click", "th", function(){
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

// PAGINAÇÃO
$(".list").on("click", ".bt-page", function(){
    window.location = 
        "?action=" + action
        + "&form=" + form
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
    window.location = "?action=view-update-form&form=" + form + "&id=" + $(this).attr('href');
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
        + "&form=" + form
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
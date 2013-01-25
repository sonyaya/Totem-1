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
        "?action=&m.var:_GET.action;"
        + "&form=&m.var:form;"
        + "&cond=<m.if cond='&m.var:bool:_GET.cond;'>&m.var:_GET.cond;</m.if>"
        + "&page=<m.if cond='&m.var:bool:_GET.page;'>&m.var:_GET.page;</m.if>"
        + "&orderBy=" + order
    ;
});

// PAGINAÇÃO
$(".list").on("click", ".bt-page", function(){
    window.location = 
        "?action=&m.var:_GET.action;"
        + "&form=&m.var:form;"
        + "&cond=<m.if cond='&m.var:bool:_GET.cond;'>&m.var:_GET.cond;</m.if>"
        + "&orderBy=<m.if cond='&m.var:bool:_GET.orderBy;'>&m.var:_GET.orderBy;</m.if>"
        + "&" + $(this).attr("rel")
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
    window.location = "?action=view-update-form&form=&m.var:form;&id=" + $(this).attr('href');
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
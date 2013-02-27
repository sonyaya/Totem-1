$(function(){

    // ADICIONA BEFORE EM TODOS OS TD DO 
    // BODY CONFORME OS VALORES DE THEAD
    $("thead tr th").each(function(){
        $this = $(this);
        index = $this.index();
        if( index > 0 ){
            title = $this.text();
            $td = $("tbody tr").find("td:eq("+index+") a").before("<span class='inline-title'>"+title+": </span>");
        }
    });

    // ADICIONA CLASSE A COLUNA QUE ESTA 
    // SENDO UTILIZADA COMO ORDENADORA
    orderBy = list.orderBy.split('/');
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
    $("table").on("click", "th", function(){});

    // PAGINAÇÃO
    $(".list").on("click", ".bt-page", function(){});
    $(".next-page").click( layout.list.button.nextPage );
    $(".prev-page").click( layout.list.button.prevPage );

    // ACTIONS
    $("table").on("click", ".edit"  , layout.list.action.edit );
    $("table").on("click", ".delete", layout.list.action.delete );
    $("table").on("click", ".select", layout.list.action.select );

    // PESQUISA / SEARCH
    $("#bt-search").click(function(){
        $("div.for-tab-search form").submit();
    });

    $("div.for-tab-search form").submit( layout.list.button.search );

    // POPULAR FORMULÁRIO DE PESQUISA / SEARCH
    $.each(cond, function(key, val){
        i = key+1;
        $("#cond-and-or-"+i).val( val[0] ), 
        $("#cond-column-"+i).val( val[1] ),
        $("#cond-comparison-"+i).val( val[2] ),
        $("#cond-value-"+i).val( val[3] )
    });
    
});
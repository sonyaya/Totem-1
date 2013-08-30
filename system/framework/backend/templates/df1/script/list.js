$(function(){

    // ORDENAÇÃO
    $(".window.list").on("click", "th", layout.list.button.order );
    
    // ATUALIZAR TELA DE LISTAGEM NO DF1
    $(".window.list").on("click", ".bt-reload-list", layout.list.button.reload);
    $(".window.list").on("click", ".next-page"     , layout.list.button.nextPage);
    $(".window.list").on("click", ".prev-page"     , layout.list.button.prevPage);
    $(".window.list").on("click", ".bt-page"       , layout.list.button.page);
 
    // ACTIONS
    $(".window.list").on("click", ".edit"  , layout.list.action.edit);
    $(".window.list").on("click", ".delete", layout.list.action.delete);
    $(".window.list").on("click", ".select", layout.list.select);

    // PESQUISA / SEARCH
    $("#bt-search").click(function(){ $("div.for-tab-search form").submit(); });
    $("div.for-tab-search form").submit( layout.list.button.search );

    // X QUE FECHA AS ABAS
    // ADICIONA FUNÇÃO AO BOTÃO FECHAR DA TOP-TAB
    $("#popup-menu").on("click", "li span", layout.list.tab.close );

//    // POPULAR FORMULÁRIO DE PESQUISA / SEARCH
//    $.each(list.cond, function(key, val){
//        i = key+1;
//        $("#cond-and-or-"+i).val( val[0] ), 
//        $("#cond-column-"+i).val( val[1] ),
//        $("#cond-comparison-"+i).val( val[2] ),
//        $("#cond-value-"+i).val( val[3] )
//    });

    //
    list.addInlineTitles();    
    list.updateAttributes();
    list.addOrderClass(list.orderBy);

});
    
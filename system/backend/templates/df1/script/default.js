$(function(){
    // close de abas
    // adiciona função ao botão fechar da top-tab
    $("#popup-menu").on("click", "li span", function(){
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
});

// OBJETO DE LAYOUT
var layout = new Object();

/**
 * SE ENCARREGA DE ABRIR POPUPS
 */
layout.popup = function(url, title){
    no = $("#popup-menu li.popup-tab").length + 1;
    
    // cria a tab
    $newTab = $("<li class='popup-tab' id='tab-popup-"+no+"'><span>x</span> "+title+"</li>");
    
    // cria a janela
    $newWindow = $("<div class='window for-tab-popup-"+no+"'>Aguarde...</div>");
    $newWindow.load(url +" #popup-window");
    
    // adiciona na interface os novos elementos
    $newTab.prependTo("#popup-menu");
    $newWindow.prependTo("#popup-window");
    
    // clica na nova aba criada
    $newTab.click();
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

//    
$(function(){
    // CRIA O LOADER 
    var cl = new CanvasLoader('canvasloader-container');
    cl.setColor('#FFFFFF'); // default is '#000000'
    cl.setShape('spiral'); // default is 'oval'
    cl.setDiameter(122); // default is 40
    cl.setDensity(35); // default is 40
    cl.setRange(2); // default is 1.3
    cl.setSpeed(1); // default is 2
    cl.setFPS(20); // default is 24
    cl.show(); // Hidden by default
    
    // X QUE FECHA AS ABAS
    // ADICIONA FUNÇÃO AO BOTÃO FECHAR DA TOP-TAB
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
layout.popup = (function(url, title){
    
    // abrir em uma nova aba
    if( layout.uri("form") == layout.uri("form", url) ){
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
        newWindow = window.open(
            url + "&popup=1",
            title,
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
});

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
});

/**
 * 
 */
layout.ajax = {};

/**
 * 
 */
layout.ajax.showLoader = function(){
    $('#loader').fadeIn();
}


/**
 * 
 */
layout.ajax.hideLoader = function(){
    $('#loader').fadeOut();
}

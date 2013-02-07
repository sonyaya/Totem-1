//    
$(function(){
    // CRIA O LOADER 
var opts = {
  lines: 6, // The number of lines to draw
  length: 0, // The length of each line
  width: 10, // The line thickness
  radius: 11, // The radius of the inner circle
  corners: 1, // Corner roundness (0..1)
  rotate: 0, // The rotation offset
  color: '#fff', // #rgb or #rrggbb
  speed: 0.5, // Rounds per second
  trail: 10, // Afterglow percentage
  shadow: true, // Whether to render a shadow
  hwaccel: true, // Whether to use hardware acceleration
  className: 'spinner', // The CSS class to assign to the spinner
  zIndex: 2e9, // The z-index (defaults to 2000000000)
  top: 'auto', // Top position relative to parent in px
  left: 'auto' // Left position relative to parent in px
};
    var target = document.getElementById('canvasloader-container');
    var spinner = new Spinner(opts).spin(target);
    
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

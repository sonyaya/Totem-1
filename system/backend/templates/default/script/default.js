var layout = new Object();

/**
 * SE ENCARREGA DE ABRIR POPUPS
 */
layout.popup = function(url, title){
    if( layout.uri("path") == layout.uri("path", url) ){
        newWindow = window.open(url, title);        
    }else{
        var w = screen.width / 1.2;
        var w = 650;
        var barSize = 70;
        newWindow = window.open(
            url,
            title + Math.random(),
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

/**
 * 
 */
layout.ajax = {};

/**
 * SE ENCARREGA DE MOSTRAR O LOADER QUANDO UM AJAX FOR EXECUTADO
 */
layout.ajax.showLoader = function(){
    
}


/**
 * SE ENCARREGA DE ESCONDER O LOADER DOS AJAX
 */
layout.ajax.hideLoader = function(){
    
}

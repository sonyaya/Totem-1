var layout = new Object();

/**
 * SE ENCARREGA DE ABRIR POPUPS
 */
layout.popup = function(url, title){
    var w = 640;
    var barSize = 70;
    newWindow = window.open(
        url,
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

/**
 * URI
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

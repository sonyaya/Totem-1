var layout = new Object();

/**
 * SE ENCARREGA DE ABRIR POPUPS
 */
layout.popup = function(url, title){
    var w = 640;
    var barSize = 60;
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
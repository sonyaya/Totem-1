$(function(){
    // TOP-TABS
    $(".top-tabs ul").on("click", "li", function(){
        $this = $(this);
        $this.siblings().removeClass("active");
        $this.addClass("active");
        $this.closest(".window-holder").find(".content > .window").hide();
        $(".for-" + $this.attr("id")).show();
        window.location.hash = $this.attr("id");
        
        // atualiza se for a aba de listagem
        if($this.attr("id") == "tab-list")
            layout.list.button.reload();
    });

    if( (deepLink = window.location.hash) !== ''){
        if(deepLink !== "#tab-list")
            $(deepLink).click();
    }
    
    // ADICIONAR A CLASSE ACTIVE-PARENT NO SIDE-MENU
    $("nav.side").find(".active-by-module").parents("li").addClass('active-parent');
});
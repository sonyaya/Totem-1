$(function(){
    // TOP-TABS
    $(".top-tabs ul").on("click", "li", function(){
        $this = $(this);
        $this.siblings().removeClass("active");
        $this.addClass("active");
        $this.closest(".window-holder").find(".content > .window").hide();
        $(".for-" + $this.attr("id")).show();
        window.location.hash = $this.attr("id");
    });

    if( (deepLink = window.location.hash) !== ''){
        $(deepLink).click();
    }

    // BOTÕES DE NAVEGAÇÃO ENTRE ABAS
    $("body").on("click", ".next-tab", function(){
        $(".top-tab ul").find(".active").next().click();
    });

    $("body").on("click", ".preview-tab", function(){
        $(".top-tab ul").find(".active").prev().click();
    });
    
    // ADICIONAR A CLASSE ACTIVE-PARENT NO SIDE-MENU
    $("nav.side").find(".active-by-module").parents("li").addClass('active-parent');
});
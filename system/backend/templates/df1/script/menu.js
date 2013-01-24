$(function(){
    $(".top-tabs ul").on("click", "li", function(){
        $(this).siblings().removeClass("active");
        $(this).addClass("active");
        $(".window").hide();
        $(".for-" + $(this).attr("id")).show();
    });

    $(".next-tab").click(function(){
        $(".top-tab ul").find(".active").next().click();
    });

    $(".preview-tab").click(function(){
        $(".top-tab ul").find(".active").prev().click();
    });
});
function DateBr_loadDays($this){
    $inputHolder = $this.closest(".input-holder");
    year  = $inputHolder.find(".input-year").val();
    month = $inputHolder.find(".input-month").val();
    lastDay = (new Date(year, month, 0)).getDate();
    html = '';
    for(i=1; i<lastDay+1; i++){
        x = (i<10) ? "0"+i : i;
        html += '<option>'+x+'</option>';
    }
    $inputHolder.find(".input-day").html( html );
}

$(function(){
    $(".dateBr").on("change", "select", function(){
        if( $(this).val() == "--" ){
            $(this).closest(".inner-holder").find("select").val("--"); 
            $inputHolder.find(".input-day").html("<option>--</option>");
        }
    });
    
    $(".dateBr").on("focus", ".input-day", function(){
        DateBr_loadDays($(this));
    });

    $(".dateBr").on("change", ".input-month", function(){
        DateBr_loadDays($(this));
    });
});
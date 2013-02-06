function DateTimeBr_loadDays($this){
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
    $(".dateTimeBr").on("change", "select", function(){
        if( $(this).val() == "--" ){
            $(this).closest(".inner-holder").find("select").val("--"); 
            $inputHolder.find(".input-day").html("<option>--</option>");
        }
    });
    
    $(".dateTimeBr").on("focus", ".input-day", function(){
        DateTimeBr_loadDays($(this));
    });

    $(".dateTimeBr").on("change", ".input-month", function(){
        DateTimeBr_loadDays($(this));
    });
});
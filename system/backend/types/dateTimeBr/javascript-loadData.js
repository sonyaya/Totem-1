// TYPE: &m.var:type;
// NAME: &m.var:name;
if(&m.var:integer:toLayout.loaded.date.year; > 0){   
    $("#&m.var:id;_year").val("&m.var:toLayout.loaded.date.year;");
    $("#&m.var:id;_month").val("&m.var:toLayout.loaded.date.month;");
    DateTimeBr_loadDays( $("#&m.var:id;_day") );
    $("#&m.var:id;_day").val("&m.var:toLayout.loaded.date.day;");
    $("#&m.var:id;_hours").val("&m.var:toLayout.loaded.time.hours;");
    $("#&m.var:id;_minutes").val("&m.var:toLayout.loaded.time.minutes;");
    $("#&m.var:id;_seconds").val("&m.var:toLayout.loaded.time.seconds;");
}else{
    $("#&m.var:id;_year").val("--");
    $("#&m.var:id;_month").val("--");
    $("#&m.var:id;_day").html("<option>--</option>").val("--");
    $("#&m.var:id;_hours").val("--");
    $("#&m.var:id;_minutes").val("--");
    $("#&m.var:id;_seconds").val("--");     
}  
// TYPE: &m.var:type;
// NAME: &m.var:name;
if('-&m.var:integer:toLayout.loaded.date.year;-' > 0){   
    $("#&m.var:id;_year").val("&m.var:toLayout.loaded.date.year;");
    $("#&m.var:id;_month").val("&m.var:toLayout.loaded.date.month;");
    DateBr_loadDays( $("#&m.var:id;_day") );
    $("#&m.var:id;_day").val("&m.var:toLayout.loaded.date.day;");
}else{
    $("#&m.var:id;_year").val("--");
    $("#&m.var:id;_month").val("--");
    $("#&m.var:id;_day").html("<option>--</option>").val("--");   
}  
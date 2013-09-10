var g = $('.chart').find('g.highcharts-legend ~ rect, g.highcharts-legend ~ path');

g.hide();

$('.chart').mouseenter(function() {
    var g = $(this).find('g.highcharts-legend ~ rect, g.highcharts-legend ~ path');
    g.show();
});

$('.chart').mouseleave(function() {
    var g = $(this).find('g.highcharts-legend ~ rect, g.highcharts-legend ~ path');
    g.hide();
});
var areaBasic;
areaBasic = new Highcharts.Chart({
    chart: {
        renderTo: 'area-basic',
        type: 'area'
    },
    title: {
        text: 'Usuários Anuais'
    },
    subtitle: {
        text: 'usuários por países'
    },
    xAxis: {
        labels: {
            formatter: function() {
                return this.value; // clean, unformatted number for year
            }
        }
    },
    yAxis: {
        title: {
            text: 'Quantidade'
        },
        labels: {
            formatter: function() {
                return this.value / 1000 +'mil';
            }
        }
    },
    tooltip: {
        formatter: function() {
            return this.series.name +' acessos <b>'+ Highcharts.numberFormat(this.y, 0) +'</b><br/>em '+ this.x;
        }
    },
    plotOptions: {
        area: {
            pointStart: 2009,
            marker: {
                enabled: false,
                symbol: 'circle',
                radius: 2,
                states: {
                    hover: {
                        enabled: true
                    }
                }
            }
        }
    },
    series: [ 
        {
            name: 'Brasil',
            data: [1600, 1800, 1200, 1200, 2800]
        },
        {
            name: 'USA',
            data: [1000, 2000, 1000, 590, 2100]
        }, 
        {
            name: 'Russia',
            data: [950, 800, 1100, 930, 1500]
        }
    ]
});
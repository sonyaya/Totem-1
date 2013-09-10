var pieChart;

//
var paisData = [
        {
            name: 'Brasil',
            y: 80,
            color: Highcharts.getOptions().colors[0]
        }, 
        
        {
            name: 'USA',
            y: 20,
            color: Highcharts.getOptions().colors[1]
        }
    ]
;

//
var estadosData = [
        // brasil
        {
            name: 'Paraná',
            y: 50,
            color:  Highcharts.Color( Highcharts.getOptions().colors[0] ).brighten( 0.2 ).get()
        },
        
        {
            name: 'São Paulo',
            y: 30,
            color:  Highcharts.Color( Highcharts.getOptions().colors[0] ).brighten( 0.2 ).get()
        }, 
        
        // usa
        {
            name: 'Texas',
            y: 20,
            color: Highcharts.getOptions().colors[1]
        }
    ]
;

// Create the chart
pieChart = new Highcharts.Chart({
    credits:{
      enabled: false  
    },
    chart: {
        renderTo: 'pie-chart',
        type: 'pie'
    },
    
    title: {
        text: 'Acessos'
    },
    
    plotOptions: {
        pie: {
            shadow: false
        }
    },
    
    tooltip: {
            valueSuffix: '%'
    },
    
    series: [{
            name: 'Países',
            data: paisData,
            size: '60%',
            dataLabels: {
                formatter: function() {
                    return this.y > 5 ? this.point.name : null;
                },
                color: 'white',
                distance: -30
            }
        }, 
        {
            name: 'Estados',
            data: estadosData,
            innerSize: '60%',
            dataLabels: {
                formatter: function() {
                    // display only if larger than 1
                    return this.y > 1 ? '<b>'+ this.point.name +':</b> '+ this.y +'%'  : null;
                }
            }
        }
    ]
});
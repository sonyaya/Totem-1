var pieChart;

var paisEstadoCidade = '-&m.var:json:data.paisEstadoCidade;-';

//
var paisData = [];
var estadoData = [];
var cidadeData = [];
var colorCount = 0;

console.log(paisEstadoCidade);

for(key in paisEstadoCidade['pais']){
    paisData.push(
        { 
            name: key,
            y: parseInt(paisEstadoCidade['pais'][key]['pageViews'] ),
            color: Highcharts.getOptions().colors[ colorCount ]
        }
    );
        
    for(keyEst in paisEstadoCidade['pais'][key]['estado']){
        estadoData.push(
            { 
                name: keyEst,
                y: parseInt(paisEstadoCidade['pais'][key]['estado'][keyEst]['pageViews'] ),
                color:  Highcharts.Color( Highcharts.getOptions().colors[ colorCount ]).brighten( 0.1 ).get()
            }
        );
            
        for(keyCid in paisEstadoCidade['pais'][key]['estado'][keyEst]['cidade']){
            cidadeData.push(
                { 
                    name: keyCid,
                    y: parseInt(paisEstadoCidade['pais'][key]['estado'][keyEst]['cidade'][keyCid]['pageViews'] ),
                    color:  Highcharts.Color( Highcharts.getOptions().colors[ colorCount ]).brighten( 0.2 ).get()
                }
            );      
        }
    }
    
    colorCount++;
}


// Create the chart
pieChart = new Highcharts.Chart({
    credits:{
      enabled: false  
    },
    chart: {
        renderTo: 'pie-chart-pais-estado-cidade',
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
                    return this.y > 1 ? '<b>'+ this.point.name +'</b> ' : null;
                },
                color: 'white',
                distance: -35
            }
        }, 
        {
            name: 'Estados',
            data: estadoData,
            innerSize: '55%',
            dataLabels: {
                formatter: function() {
                    return this.y > 5 ? '<b>'+ this.point.name +'</b> ' : null;
                },
                color: 'white',
                distance: -20
            }
        }, 
        {
            name: 'Cidades',
            data: cidadeData,
            innerSize: '80%',
            dataLabels: {
                formatter: function() {
                    // display only if larger than 1
                    return this.y > 1 ? '<b>'+ this.point.name +':</b> '+ this.y +'%'  : null;
                }
            }
        }
    ]
});
Highcharts.theme = {
    colors: ['#BBBB88', '#EEAA88', '#EEDD99', '#EEC290', '#CCC68D', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
    chart: {
        backgroundColor: "transparent",
    },
    
    title: {
        style: {
            color: '#222222',
            font: '1em Verdana',
        }
    },
    
    subtitle: {
        style: {
            color: '#666666',
            font: '0.8em Verdana'
        }
    },

    legend: {
        itemStyle: {
            font: '1em Verdana',
            color: 'black'
        },
        
        itemHoverStyle:{
            color: 'gray'
        }   
    },
    xAxis: {
        //gridLineWidth: 1,
        //lineColor: '#ccc',
        //tickColor: '#ccc',
        labels: {
            style: {
                font: '0.8em Verdana',
                color: 'black'
            }
        },
        title: {
            style: {
            font: '1em Verdana',
            color: 'black'
            }
        }
    },
    yAxis: {
        //minorTickInterval: 'auto',
        //lineColor: '#ccc',
        //lineWidth: 1,
        //tickWidth: 1,
        //tickColor: '#ccc',
        labels: {
            style: {
                font: '0.8em Verdana',
                color: 'black'
            }
        },
        title: {
            style: {
                color: '#666',
                font: '0.8em Verdana',
            }
        }
    },
    legend: {
        itemStyle: {
                font: '0.8em Verdana',
                color: 'black'

        },
        itemHoverStyle: {
            color: '#039'
        },
        itemHiddenStyle: {
            color: 'gray'
        }
    },
    labels: {
        style: {
            color: '#99b'
        }
    }
};

// Apply the theme
var highchartsOptions = Highcharts.setOptions(Highcharts.theme);

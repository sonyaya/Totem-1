Highcharts.theme = {
    colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
    chart: {
        backgroundColor: "transparent",
    },
    title: {
        style: {
            color: '#000',
            font: '1.5em Verdana'
        }
    },
    subtitle: {
        style: {
            color: '#666666',
            font: '1em Verdana'
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
    }
};

// Apply the theme
var highchartsOptions = Highcharts.setOptions(Highcharts.theme);

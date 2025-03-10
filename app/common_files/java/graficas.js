
function grafica_uso(div_elemento,datos_grafica) {
    if (_(div_elemento) !== null) {
        $.getJSON(datos_grafica, function (json) {
            var options = {
                chart: {
                    type: 'area'
                },
                series: json,
                xaxis: {
                    categories: nombreCortoMes
                },
                stroke: {
                    show: true,
                    curve: 'smooth',
                    lineCap: 'butt',
                    colors: undefined,
                    width: 3,
                    dashArray: 0,
                },
                markers: {
                    size: 4,
                    colors: ["#765be6"],
                    strokeColors: "#fff",
                    strokeWidth: 1,
                    hover: {
                        size: 7,
                    }
                },
                dataLabels: {
                    enabled: true
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '25%',
                        endingShape: 'rounded'
                    },
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        legend: {
                            position: 'bottom',
                            offsetX: 0,
                            offsetY: 0
                        }
                    }
                }]
            }
            _(div_elemento).innerHTML = "";
            var chart = new ApexCharts(_(div_elemento), options);
            chart.render();
        });
    }
}

export const initChartTwo = () => {
    const chartElement = document.querySelector('#chartTwo');

    if (chartElement) {
        const isDark = document.documentElement.classList.contains('dark');

        const chartTwoOptions = {
            series: [75.55],
            colors: ["#465FFF"],
            chart: {
                fontFamily: "Barlow, sans-serif",
                type: "radialBar",
                height: 330,
                sparkline: {
                    enabled: true,
                },
            },
            plotOptions: {
                radialBar: {
                    startAngle: -90,
                    endAngle: 90,
                    hollow: {
                        size: "80%",
                    },
                    track: {
                        background: isDark ? "#1F2937" : "#E4E7EC",
                        strokeWidth: "100%",
                        margin: 5,
                    },
                    dataLabels: {
                        name: {
                            show: false,
                        },
                        value: {
                            fontSize: "36px",
                            fontWeight: "600",
                            offsetY: 60,
                            color: isDark ? "#F9FAFB" : "#1D2939",
                            formatter: function (val) {
                                return val + "%";
                            },
                        },
                    },
                },
            },
            fill: {
                type: "solid",
                colors: ["#465FFF"],
            },
            stroke: {
                lineCap: "round",
            },
            labels: ["Progress"],
        };

        const chart = new ApexCharts(chartElement, chartTwoOptions);
        chart.render();
        return chart;
    }
}

export default initChartTwo;

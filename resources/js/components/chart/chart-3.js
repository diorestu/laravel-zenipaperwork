export const initChartThree = () => {
    const chartElement = document.querySelector('#chartThree');

    if (chartElement) {
        const isDark = document.documentElement.classList.contains('dark');
        const labelColor = isDark ? "#9CA3AF" : "#6B7280";
        const borderColor = isDark ? "#1F2937" : "#E5E7EB";

        const chartThreeOptions = {
            series: [{
                name: "Sales",
                data: [180, 190, 170, 160, 175, 165, 170, 205, 230, 210, 240, 235],
            },
            {
                name: "Revenue",
                data: [40, 30, 50, 40, 55, 40, 70, 100, 110, 120, 150, 140],
            },
            ],
            legend: {
                show: false,
                position: "top",
                horizontalAlign: "left",
            },
            colors: ["#465FFF", "#9CB9FF"],
            chart: {
                fontFamily: "Barlow, sans-serif",
                height: 310,
                type: "area",
                toolbar: {
                    show: false,
                },
            },
            fill: {
                gradient: {
                    enabled: true,
                    opacityFrom: 0.55,
                    opacityTo: 0,
                },
            },
            stroke: {
                curve: "straight",
                width: ["2", "2"],
            },
            markers: {
                size: 0,
            },
            labels: {
                show: false,
                position: "top",
            },
            grid: {
                borderColor: borderColor,
                xaxis: {
                    lines: {
                        show: false,
                    },
                },
                yaxis: {
                    lines: {
                        show: true,
                    },
                },
            },
            dataLabels: {
                enabled: false,
            },
            tooltip: {
                theme: isDark ? "dark" : "light",
                x: {
                    format: "dd MMM yyyy",
                },
            },
            xaxis: {
                type: "category",
                categories: [
                    "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                    "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
                ],
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
                labels: {
                    style: {
                        colors: labelColor,
                    },
                },
                tooltip: false,
            },
            yaxis: {
                title: {
                    style: {
                        fontSize: "0px",
                    },
                },
                labels: {
                    style: {
                        colors: labelColor,
                    },
                },
            },
        };

        const chart = new ApexCharts(chartElement, chartThreeOptions);
        chart.render();
        return chart;
    }
}

export default initChartThree;

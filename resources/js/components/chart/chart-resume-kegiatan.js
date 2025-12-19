export const initChartResumeKegiatan = () => {
    const chartElement = document.querySelector("#chartResumeKegiatan")

    if (!chartElement) return

    const chart = new ApexCharts(chartElement, {
        series: [44, 55, 13],
        labels: ["Terlambat", "Tepat Waktu", "Belum Selesai"],

        chart: {
            type: "pie",
            height: "100%",
            width: "100%",
            fontFamily: "Outfit, sans-serif",
        },

        colors: [
            "#FF4560",
            "#00E396",
            "#FEB019",
        ],

        stroke: {
            width: 3,
            colors: ["#ffffff"],
        },

        dataLabels: {
            enabled: true,
            style: {
                fontSize: "13px",
                fontWeight: 600,
                colors: ["#ffffff"],
            },
            dropShadow: {
                enabled: false,
            },
            formatter: (val) => `${val.toFixed(1)}%`,
        },

        legend: {
            position: "right",
            fontSize: "14px",
            markers: {
                width: 10,
                height: 10,
                radius: 12,
            },
            itemMargin: {
                vertical: 6,
            },
        },

        responsive: [
            {
                breakpoint: 640,
                options: {
                    chart: {
                        height: 240,
                    },
                    legend: {
                        position: "bottom",
                    },
                },
            },
        ],
    })

    window.addEventListener("load", () => {
        setTimeout(() => {
            chart.render()
        }, 400)
    })

    return chart
}

export default initChartResumeKegiatan

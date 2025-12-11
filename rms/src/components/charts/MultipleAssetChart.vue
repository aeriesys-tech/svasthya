<template>
    <!-- <h1>Multiple</h1> -->
    <div id="trendChartContainer" style="height: 400px; margin-top: 30px;" v-if="hasChartData"></div>
</template>

<script>
    import Highcharts from "highcharts";
    import Exporting from "highcharts/modules/exporting";
    import ExportData from "highcharts/modules/export-data";
    import OfflineExporting from "highcharts/modules/offline-exporting";
    import FullScreen from "highcharts/modules/full-screen";
    import moment from "moment";

    // Initialize Highcharts modules
    Exporting(Highcharts);
    ExportData(Highcharts);
    OfflineExporting(Highcharts);
    FullScreen(Highcharts);

    export default {
        data() {
            return {
                selectedDates: new Set(), // Stores selected dates
            };
        },
        props: {
            seriesData: {
                type: Array,
                required: true,
            },
        },
        computed: {
            hasChartData() {
                // console.log("seriesData",this.seriesData.length )
                return this.seriesData.length > 0;
            },
        },
        watch: {
            seriesData: {
                handler(newData) {
                    if (newData.length) {
                        this.$nextTick(() => {
                            this.renderChart(newData);
                        });
                    }
                },
                deep: true,
                immediate: true,
            },
        },

        methods: {


            renderChart(seriesData) {
                if (this.chart) {
                    this.chart.destroy(); // Destroy old chart before re-rendering
                }

                // Extract all unique timestamps from all series
                const allJobDates = new Set();
                seriesData.forEach((series) => {
                    series.data.forEach((point) => {
                        allJobDates.add(moment.utc(point.x).format("YYYY-MM-DD HH:mm:ss"));
                    });
                });

                // Extract unique variable names for Y-axis mapping
                const uniqueVariables = [...new Set(seriesData.map((series) => series.name))];

                // Assign Highcharts default colors dynamically
                const defaultColors = Highcharts.getOptions().colors; // Get the default Highcharts colors
                const colorMap = {};
                uniqueVariables.forEach((variable, index) => {
                    colorMap[variable] = defaultColors[index % defaultColors.length]; // Ensure colors cycle if more than the default set
                });

                // Define multiple Y-axes with colors matching the series
                const yAxisConfig = uniqueVariables.map((variable, index) => ({
                    title: { text: variable, style: { color: colorMap[variable] } }, // Assign title color
                    labels: { style: { color: colorMap[variable] } }, // Assign label color
                    opposite: index % 2 === 1, // Alternate sides for readability
                }));


                this.chart = Highcharts.chart("trendChartContainer", {
                    chart: { type: "line" },
                    title: { text: "Job Date Vs Value" },
                    xAxis: {
                        type: "linear",
                        // categories: sortedJobDates.map((_, index) => index), // Use index numbers
                        title: { text: "Index" }, // Change title from Job Date to Index
                        labels: {
                            rotation: 0, // Keep labels horizontal for clarity
                            formatter: function () {
                                return `<b>${this.value}</b>`; // Show indices instead of dates
                            },
                        },
                    },
                    yAxis: yAxisConfig,
                    tooltip: {
                        useHTML: true,
                        formatter: function () {
                            const value = this.y;
                            const jobDate = this.point.job_date; // Access preserved job_date

                            return `
                                <b>${this.series.name}</b><br/>
                                <b>Index:</b> ${this.x}<br/>
                                <b>Date:</b> ${moment.utc(jobDate).format("YYYY-MM-DD HH:mm:ss")}<br/>
                                <b>Value:</b> ${value}
                            `;
                        },
                    },
                    series: seriesData.map((series) => (
                        {
                        date:series.x,
                        name: series.name,
                        yAxis: uniqueVariables.indexOf(series.name), // Assign correct Y-axis
                        color: colorMap[series.name], // Assign corresponding color
                        data: series.data.map((point, index) => ({
                            x: index, // Use index instead of timestamp
                            y: parseFloat(point.y),
                            job_date: point.x,
                        })),


                    })
                    ),


                    exporting: {
                        enabled: true,
                        buttons: {
                            contextButton: {
                                menuItems: ["viewFullscreen", "printChart", "separator", "downloadPNG", "downloadJPEG", "downloadPDF", "downloadSVG", "separator", "downloadCSV", "downloadXLS"],
                            },
                        },
                    },
                });
            },
        },
    };
</script>

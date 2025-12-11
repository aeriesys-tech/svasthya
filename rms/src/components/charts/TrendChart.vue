<template>
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
            renderChartOld(seriesData) {
                if (this.chart) {
                    this.chart.destroy();
                }

                this.chart = Highcharts.chart("trendChartContainer", {
                    chart: { type: "line" },
                    title: { text: "Job Date Vs Value" },
                    xAxis: {
                        type: "datetime",
                        title: { text: "Job Date" },
                        // tickInterval: 2 * 60 * 60 * 1000,
                        labels: {
                            rotation: 90,
                            formatter: function () {
                                // return Highcharts.dateFormat(
                                //   "%e %b %Y %H:%M",
                                //   moment(this.value).local().valueOf()
                                // );
                                return Highcharts.dateFormat("%e %b %Y %H:%M", this.value);
                            },
                        },
                    },
                    yAxis: { title: { text: "Value" } },
                    series: seriesData,
                    exporting: {
                        enabled: true, // Enable context menu
                        buttons: {
                            contextButton: {
                                menuItems: ["printChart", "separator", "downloadPNG", "downloadJPEG", "downloadPDF", "downloadSVG", "separator", "downloadCSV", "downloadXLS"],
                            },
                        },
                    },
                });
            },

            renderChartworking(seriesData) {
                console.log("SSSEEE", seriesData);
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

                // Convert set to sorted array
                const sortedJobDates = [...allJobDates].sort((a, b) => moment(a, "YYYY-MM-DD HH:mm:ss") - moment(b, "YYYY-MM-DD HH:mm:ss"));

                // Map timestamps to category indices
                const timestampIndexMap = {};
                sortedJobDates.forEach((date, index) => {
                    timestampIndexMap[date] = index; // Start from 1 instead of 0
                });

                //show number format index before date
                // const formattedCategories = sortedJobDates.map((date, index) => {
                //     return `${index + 1} - ${moment.utc(date, "YYYY-MM-DD HH:mm:ss").format("DD / M / YYYY HH:mm")}`;
                // });

                this.chart = Highcharts.chart("trendChartContainer", {
                    chart: { type: "line" },
                    title: { text: "Job Date Vs Value" },
                    xAxis: {
                        type: "category",
                        //show number format index before date
                        // categories: formattedCategories,
                        categories: sortedJobDates.map((date) => moment.utc(date, "YYYY-MM-DD HH:mm:ss").format("DD / M / YYYY HH:mm")), // Display formatted dates
                        title: { text: "" },
                        labels: {
                            rotation: 90,
                            useHTML: true,
                            formatter: function () {
                                return `<b>${this.value}</b>`; // Display job date
                            },
                        },
                    },
                    yAxis: { title: { text: "Value" } },
                    tooltip: {
                        useHTML: true,
                        formatter: function () {
                            return `
                                <b>${this.series.name}</b><br/>
                                <b>Date:</b> ${this.x}<br/>
                                <b>Value:</b> ${this.y}`;
                        },
                    },
                    series: seriesData.map((series) => ({
                        name: series.name,
                        data: series.data.map((point) => ({
                            x: timestampIndexMap[moment.utc(point.x).format("YYYY-MM-DD HH:mm:ss")], // Assign correct x index
                            y: parseFloat(point.y),
                        })),
                    })),
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

            renderChart(seriesData) {
                console.log("SSSEEE", seriesData);
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

                // Convert set to sorted array
                const sortedJobDates = [...allJobDates].sort((a, b) => moment(a, "YYYY-MM-DD HH:mm:ss") - moment(b, "YYYY-MM-DD HH:mm:ss"));

                // Map timestamps to category indices
                const timestampIndexMap = {};
                sortedJobDates.forEach((date, index) => {
                    timestampIndexMap[date] = index;
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
                        type: "category",
                        categories: sortedJobDates.map((date) => moment.utc(date, "YYYY-MM-DD HH:mm:ss").format("DD / M / YYYY HH:mm")),
                        title: { text: "" },
                        labels: {
                            rotation: 90,
                            useHTML: true,
                            formatter: function () {
                                return `<b>${this.value}</b>`;
                            },
                        },
                    },
                    yAxis: yAxisConfig,
                    tooltip: {
                        useHTML: true,
                        formatter: function () {
                            return `
                    <b>${this.series.name}</b><br/>
                    <b>Date:</b> ${this.x}<br/>
                    <b>Value:</b> ${this.y}`;
                        },
                    },
                    series: seriesData.map((series) => ({
                        name: series.name,
                        yAxis: uniqueVariables.indexOf(series.name), // Assign correct Y-axis
                        color: colorMap[series.name], // Assign corresponding color
                        data: series.data.map((point) => ({
                            x: timestampIndexMap[moment.utc(point.x).format("YYYY-MM-DD HH:mm:ss")],
                            y: parseFloat(point.y),
                        })),
                    })),
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

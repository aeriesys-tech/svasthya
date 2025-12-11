<template>
    <!-- <h1>Single</h1> -->
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
            lcl: {
                type: Number,
                required: false,
            },
            ucl: {
                type: Number,
                required: false,
            },
        },
        computed: {
            hasChartData() {
                console.log("seriesData", this.seriesData);
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
                    this.chart.destroy();
                }
                const lcl = this.lcl;
                const ucl = this.ucl;

                console.log("lcl----", lcl);
                console.log("ucl----", ucl);
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

                // Create LCL and UCL series only if they are defined
                const controlLimitsSeries = [];
                if (lcl !== undefined) {
                    controlLimitsSeries.push({
                        name: "LCL",
                        type: "line",
                        dashStyle: "Dot",
                        color: "red",
                        yAxis: 0, // Assign LCL and UCL to default Y-axis
                        data: sortedJobDates.map((_, index) => [index, lcl]),
                        marker: { enabled: false },
                    });
                }
                if (ucl !== undefined) {
                    controlLimitsSeries.push({
                        name: "UCL",
                        type: "line",
                        dashStyle: "Dot",
                        color: "red",
                        yAxis: 0, // Assign LCL and UCL to default Y-axis
                        data: sortedJobDates.map((_, index) => [index, ucl]),
                        marker: { enabled: false },
                    });
                }
                // Map job dates with spares for quick lookup
                const spareDateMap = new Set();
                seriesData.forEach((series) => {
                    series.data.forEach((point) => {
                        if (point && point.spares && point.spares.length > 0) {
                            const jobDateStr = moment.utc(point.x).format("YYYY-MM-DD HH:mm");
                            // star mark to show--match if the spares[] has service date n match with job_date
                            // const spareDates = point.spares.map((s) => moment.utc(s.service_date).format("YYYY-MM-DD HH:mm"));
                            spareDateMap.add(jobDateStr);

                            // Optional: exact match
                            // star mark to show--match if the spares[] has service date n match with job_date
                            // if (spareDates.includes(jobDateStr)) {
                            //     console.log("jobDateStr----", jobDateStr);
                            //     console.log("spareDates====", spareDates);
                            //     spareDateMap.add(jobDateStr);
                            // }

                            // Or, if you only want to compare dates (not times), use:
                            // const jobDateOnly = moment.utc(jobDateStr).format("YYYY-MM-DD");
                            // spareDates.forEach(sd => {
                            //     if (moment.utc(sd).format("YYYY-MM-DD") === jobDateOnly) {
                            //         spareDateMap.add(jobDateStr);
                            //     }
                            // });
                        }
                    });
                });

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
                            // formatter: function () {
                            //     const rawDate = sortedJobDates[this.pos]; // UTC formatted date
                            //     console.log("rawDate=====", rawDate)
                            //     const displayDate = moment.utc(rawDate, "YYYY-MM-DD HH:mm").format("DD / M / YYYY HH:mm");
                            //     const formatedDate = moment.utc(rawDate, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm");
                            //     console.log(formatedDate)
                            //     // Add * if this date has a matching spare
                            //     const hasSpare = spareDateMap.has(formatedDate);
                            //     return `<b>${hasSpare ? "* " : ""}${displayDate}</b>`;
                            // },
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
                    // series: seriesData.map((series) => ({
                    //     name: series.name,
                    //     yAxis: uniqueVariables.indexOf(series.name), // Assign correct Y-axis
                    //     color: colorMap[series.name], // Assign corresponding color
                    //     data: series.data.map((point) => ({
                    //         x: timestampIndexMap[moment.utc(point.x).format("YYYY-MM-DD HH:mm:ss")],
                    //         y: parseFloat(point.y),
                    //     })),
                    // })),

                    series: [
                        ...seriesData.map((series) => ({
                            name: series.name,
                            yAxis: uniqueVariables.indexOf(series.name), // Assign correct Y-axis
                            color: colorMap[series.name],
                            // before excedding the limit code
                            // data: series.data.map((point) => ({
                            //     x: timestampIndexMap[moment.utc(point.x).format("YYYY-MM-DD HH:mm:ss")],
                            //     y: isNaN(point.y) ? 0 : parseFloat(point.y),
                            // })),

                            // after excedding the limit code
                            data: series.data.map((point) => {
                                const value = isNaN(point.y) ? 0 : parseFloat(point.y);
                                const timestamp = timestampIndexMap[moment.utc(point.x).format("YYYY-MM-DD HH:mm:ss")];

                                const isOutOfControl = (this.lcl !== undefined && value < this.lcl) || (this.ucl !== undefined && value > this.ucl);

                                return {
                                    x: timestamp,
                                    y: value,
                                    marker: isOutOfControl
                                        ? {
                                              enabled: true,
                                              fillColor: "red",
                                              radius: 5,
                                              symbol: "circle",
                                          }
                                        : {},
                                    original: point.spares || {}, // Keep original for spare check
                                };
                            }),
                        })),
                        ...controlLimitsSeries, // Add LCL and UCL lines
                    ],

                    exporting: {
                        enabled: true,
                        buttons: {
                            contextButton: {
                                menuItems: ["viewFullscreen", "printChart", "separator", "downloadPNG", "downloadJPEG", "downloadPDF", "downloadSVG", "separator", "downloadCSV", "downloadXLS"],
                            },
                        },
                    },
                });

                // Add stars on x-axis line
                sortedJobDates.forEach((rawDate, index) => {
                    const formatedDate = moment.utc(rawDate, "YYYY-MM-DD HH:mm:ss").format("YYYY-MM-DD HH:mm");

                    if (spareDateMap.has(formatedDate)) {
                        const x = this.chart.xAxis[0].toPixels(index); // Convert category index to pixel
                        const y = this.chart.chartHeight - this.chart.marginBottom + 5; // Adjust below x-axis line

                        this.chart.renderer
                            .text("★", x - 8, y) // -4 to center star over tick
                            .attr({
                                zIndex: 5,
                                fill: "gold", // You can change the star color
                            })
                            .css({
                                fontSize: "25px",
                            })
                            .add();
                    }
                });

            },
        },
    };
</script>

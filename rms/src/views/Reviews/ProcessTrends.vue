<template>
    <div class="">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <div>
                <ol class="breadcrumb fs-sm mb-1">
                    <li class="breadcrumb-item" aria-current="page">
                        <router-link to="/dashboard">Dashboard</router-link>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="javascript:void(0)">Review</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Process Trends</li>
                </ol>
                <h4 class="main-title mb-0">Process Trends</h4>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-one">
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="card-title">Process Trends</h6>
                    </div>
                    <div class="card-body">
                           <div class="row mb-3">
                            <div class="col-md-3 mb-2">
                                <label>Assets</label>
                                <!-- <MultiSelect
                                    v-model="process_trend.asset_id"
                                    filter
                                    :options="assets"
                                    optionLabel="asset_name"
                                    optionValue="asset_id"
                                    placeholder="Select Assets"
                                    :maxSelectedLabels="3"
                                    :class="{ 'p-invalid': errors?.asset_id }"
                                    style="width: 100%; height: 37px;"
                                    @change="onAssetChange"
                                /> -->
                                <MultiSelect
                                    v-model="process_trend.asset_id"
                                    filter
                                    :options="assets"
                                    optionLabel="asset_name"
                                    optionValue="asset_id"
                                    placeholder="Select Assets"
                                    :maxSelectedLabels="3"
                                    :class="{ 'p-invalid': errors?.asset_id }"
                                    style="width: 100%; height: 37px;"
                                />

                                <span v-if="errors?.asset_id">
                                    <small class="text-danger">{{ errors.asset_id[0] }}</small>
                                </span>
                            </div>
                            <div class="col-md-3 mb-2">
                                <label>Asset Zones</label>
                                <!-- <select class="form-control" v-model="process_trend.asset_zone_id" @change="getVariables" :class="{'is-invalid': errors?.asset_zone_id}">
                                    <option value="">Select Zone</option>
                                    <option v-for="zone in asset_zones" :key="zone.asset_zone_id" :value="zone.asset_zone_id">
                                        {{ zone.zone_name }}
                                    </option>
                                </select> -->
                                <!-- <select class="form-control" v-model="process_trend.asset_zone_id" @change="getVariables" :class="{'is-invalid': errors?.asset_zone_id}">
                                    <option value="">Select Zone</option>
                                    <option v-if="asset_zones.length > 0" :value="asset_zones[0].asset_zone_id">
                                        {{ asset_zones[0].zone_name }}
                                    </option>
                                </select> -->
                                <input type="text" disabled class="form-control" v-model="process_trend.asset_zone_id"/>
                                <span v-if="errors?.asset_zone_id">
                                    <small class="text-danger">{{ errors.asset_zone_id[0] }}</small>
                                </span>
                            </div>
                            <div class="col-md-3 mb-2" v-if="isSingleAsset">
                                <label>Variables</label>
                                <MultiSelect
                                    v-model="process_trend.variable_id"
                                    filter
                                    :options="variables"
                                    optionLabel="variable_name"
                                    optionValue="variable_id"
                                    placeholder="Select Variables"
                                    :maxSelectedLabels="3"
                                    style="width: 100%; height: 37px;"
                                    :style="errors?.variable_id ? error_style : ''"
                                    :class="{'is-invalid': errors?.variable_id}"
                                />
                                <span v-if="errors?.variable_id">
                                    <small class="text-danger">{{ errors.variable_id[0] }}</small>
                                </span>
                            </div>

                            <div class="col-md-3 mb-2" v-else>
                                <label>Variables</label>
                                <!-- <select class="form-control" v-model="process_trend.variable_id" :disabled="!process_trend.asset_zone_id" :class="{'is-invalid': errors?.variable_id}">
                                    <option value="">Select Variable</option>
                                    <option v-for="v in variables" :key="v.variable_id" :value="v.variable_id">
                                        {{ v.variable_name }}
                                    </option>
                                </select> -->
                                 <search
                                    :class="{ 'is-invalid': errors?.variable_id}"
                                    :customClass="{ 'is-invalid': errors?.variable_id}"
                                    :initialize="process_trend.variable_id"
                                    id="variable_id"
                                    label="variable_name"
                                    placeholder="Select Variable"
                                    :data="variables"
                                    @input="variable_id => process_trend.variable_id = variable_id"
                                    :disabled="!process_trend.asset_zone_id"
                                >
                                </search>
                                <span v-if="errors?.variable_id">
                                    <small class="text-danger">{{ errors.variable_id[0] }}</small>
                                </span>
                            </div>

                            <div class="col-md-3 mb-2">
                                <label>Record</label>
                                <select class="form-control" v-model="process_trend.last_record" >
                                    <option value="">Select Record</option>
                                    <option value="10">Last 10 Records</option>
                                    <option value="50">Last 50 Records</option>
                                    <option value="100">Last 100 Records</option>
                                    <option value="last_compaign">Last Compaign</option>
                                    <option value="last_patching">Last Patching</option>
                                </select>
                            </div>

                            <template v-if="!process_trend.last_record">
                             <div class="col-md-3">
                                <label>Time Period</label>
                                <select class="form-control" v-model="selectedTimePeriod" @change="handleTimePeriodChange">
                                    <option value="8_hours">8 Hours</option>
                                    <option value="24_hours">24 Hours</option>
                                    <option value="7_days">7 Days</option>
                                    <option value="1_month">1 Month</option>
                                    <option value="1_year">1 Year</option>
                                    <option value="custom">Custom</option>
                                </select>
                            </div>


                            <div class="col-md-3">
                                <label>From</label>
                                <input type="datetime-local" class="form-control" :class="{'is-invalid': errors?.from_date}" v-model="process_trend.from_date" :disabled="selectedTimePeriod !== 'custom'"/>
                                 <span v-if="errors?.from_date">
                                    <small class="text-danger">{{ errors.from_date[0] }}</small>
                                </span>
                            </div>
                            <div class="col-md-3">
                                <label>To</label>
                                <input type="datetime-local" class="form-control" :class="{'is-invalid': errors?.to_date}" v-model="process_trend.to_date" :disabled="selectedTimePeriod !== 'custom'"/>
                                 <span v-if="errors?.to_date">
                                    <small class="text-danger">{{ errors.to_date[0] }}</small>
                                </span>
                            </div>
                            </template>
                            <div class="col-md-3 my-auto mb-0">
                                <button class="btn btn-primary" @click="search" v-if="isSingleAsset">View</button>
                                <button class="btn btn-primary" @click="searchMultiple" v-else>View</button>
                            </div>
                        </div>




                        <hr />
                        <div v-if="hasSearched">
                            <!-- <TrendChart :seriesData="chartData" v-if="hasChartData" /> -->
                            <SingleAssetChart v-if="isSingleAsset && hasChartData && process_trend.variable_id?.length === 1" :seriesData="chartData" :lcl="lcl" :ucl="ucl" />
                            <SingleAssetChart v-else-if="isSingleAsset && hasChartData" :seriesData="chartData" />
                            <MultipleAssetChart v-else-if="!isSingleAsset && hasChartData" :seriesData="chartData" />

                            <div v-else class="text-center text-muted py-4">
                                <i class="pi pi-info-circle" style="font-size: 2rem;"></i>
                                <p class="mb-0 mt-2 fw-bold text-danger">No records found</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import moment from "moment";
    import Highcharts from "highcharts";
import MultiSelect from "primevue/multiselect";
    import Search from "@/components/Search.vue";
    // import TrendChart from "@/components/charts/TrendChart.vue";

    import MultipleAssetChart from "@/components/charts/MultipleAssetChart.vue";
    import SingleAssetChart from "@/components/charts/SingleAssetChart.vue";
    export default {
        components: {
            MultiSelect,
            MultipleAssetChart,
            SingleAssetChart,
            Search
        },
        data() {
            return {
                assets: [],
                asset_zones: [],
                variables: [],
                process_trend: {
                    asset_id: [],
                    asset_zone_id: "Overall",
                    variable_id: [],
                    // from_date: moment().subtract(1, "days").format("YYYY-MM-DDTHH:mm"),
                    // to_date: moment().format("YYYY-MM-DDTHH:mm"),
                    from_date: null,
                    to_date: null,
                    last_record:"",
                    // variable_id: "",
                },
                selectedTimePeriod: "custom",
                hasChartData: false,
                chartData: [],
                errors: {},
                hasSearched: false,
                lcl: 0,
                ucl: 0,
            };
        },
        computed: {
            isSingleAsset() {
                return this.process_trend.asset_id.length === 1;
            },
        },
        watch: {
            // "process_trend.asset_id"(val) {
            //     if (val.length !== 1 && this.process_trend.variable_id.length > 1) {
            //         this.process_trend.variable_id = this.process_trend.variable_id.slice(0, 1);
            //     }
            // },

            "process_trend.asset_id": {
        handler(val) {
            setTimeout(() => {
                if (val.length === 1) {
                    // Ensure variable_id is always an array for MultiSelect
                    if (!Array.isArray(this.process_trend.variable_id)) {
                        this.process_trend.variable_id = this.process_trend.variable_id
                            ? [this.process_trend.variable_id]
                            : [];
                    }
                } else {
                    // Ensure variable_id is a string for <select>
                    if (Array.isArray(this.process_trend.variable_id)) {
                        this.process_trend.variable_id = this.process_trend.variable_id[0] || "";
                    }
                }
            }, 50);
        },
        immediate: true,
    },
        },
        mounted() {
            this.getAssets();
        },
        methods: {
            getAssets() {
                let vm = this;
                let loader = vm.$loading.show();

                vm.$store
                    .dispatch("post", { uri: "getAssets" })
                    .then((response) => {
                        loader.hide();
                        vm.assets = response.data.data;
                        vm.getVariables();
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },
            onAssetChange() {
                this.getAssetZones();
                this.process_trend.variable_id = [];
            },
            getAssetZones() {
                let vm = this;
                let loader = vm.$loading.show();
                vm.$store
                    .dispatch("post", {
                        uri: "getAssetZones",
                        data: vm.process_trend,
                    })
                    .then((response) => {
                        loader.hide();
                        vm.asset_zones = response.data.data;
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response?.data?.errors || [];
                        vm.$store.dispatch("error", error.response?.data?.message || "An error occurred");
                    });
            },
            getVariables() {
                let vm = this;
                let loader = vm.$loading.show();

                vm.$store
                    .dispatch("post", {
                        uri: "getZoneUserVariables",
                        data: vm.process_trend,
                    })
                    .then((response) => {
                        loader.hide();
                        vm.variables = response.data.data;
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response?.data?.errors || [];
                        vm.$store.dispatch("error", error.response?.data?.message || "An error occurred");
                    });
            },

            // single asset api
            search() {
                this.errors = {};
                const payload = {
                    ...this.process_trend,
                    variable_id: Array.isArray(this.process_trend.variable_id) ? this.process_trend.variable_id : [this.process_trend.variable_id],
                };
                let vm = this;
                let loader = vm.$loading.show();

                vm.$store
                    .dispatch("post", {
                        uri: "getProcessTrendValues",
                        data: payload,
                    })
                    .then((res) => {
                        loader.hide();
                        const trendData = res.data;
                        const grouped = {};
                        trendData.forEach((item) => {
                            vm.lcl = parseInt(item?.lcl, 10);
                            vm.ucl = parseInt(item?.ucl, 10);
                            item.trend_values.forEach((trend) => {
                                const key = `${trend.asset_name} - ${item.variable.variable_name}`;
                                if (!grouped[key]) grouped[key] = [];
                                grouped[key].push({
                                    x: moment.utc(trend.job_date, "YYYY-MM-DD HH:mm:ss").local().valueOf(),
                                    y: parseFloat(trend.value),
                                    variable_name: item.variable.variable_name,
                                    spares:trend.spares
                                });

                                // grouped[key].push({
                                //     x: moment(trend.job_date, "YYYY-MM-DD HH:mm:ss").valueOf(), // Ensure UTC timestamp
                                //     y: parseFloat(trend.value),
                                //     variable_name: item.variable.variable_name,
                                // });
                            });
                        });
                        const colors = Highcharts.getOptions().colors;
                        const series = Object.entries(grouped).map(([name, data], index) => ({
                            name,
                            data,
                            color: colors[index % colors.length],
                        }));
                        vm.hasChartData = series.length > 0;
                        vm.chartData = series;
                        this.hasSearched = true;
                    })
                    .catch((error) => {
                        loader.hide();
                        console.log("Error fetching data: ", error);
                        vm.errors = error.response?.data?.errors || [];
                        vm.$store.dispatch("error", error.response?.data?.message || "An error occurred");
                    });
            },

            searchMultiple() {
                this.errors = {};
                const payload = {
                    ...this.process_trend,
                    variable_id: Array.isArray(this.process_trend.variable_id) ? this.process_trend.variable_id : [this.process_trend.variable_id],
                };
                let vm = this;
                let loader = vm.$loading.show();

                vm.$store
                    .dispatch("post", {
                        uri: "getProcessTrendValueAssets",
                        data: payload,
                    })
                    .then((res) => {
                        loader.hide();
                        const trendData = res.data;
                        const grouped = {};
                        trendData.forEach((item) => {
                            item.trend_values.forEach((trend) => {
                                const key = `${trend.asset_name} - ${item.variable.variable_name}`;
                                if (!grouped[key]) grouped[key] = [];
                                grouped[key].push({
                                    x: trend.job_date,
                                    y: parseFloat(trend.value),
                                    variable_name: item.variable.variable_name,
                                });
                            });
                        });

                        const colors = Highcharts.getOptions().colors;
                        const series = Object.entries(grouped).map(([name, data], index) => ({
                            name,
                            data,
                            color: colors[index % colors.length],
                        }));
                        vm.hasChartData = series.length > 0;
                        vm.chartData = series;
                        this.hasSearched = true;
                    })
                    .catch((error) => {
                        loader.hide();
                        console.log("Error fetching data: ", error);
                        vm.errors = error.response?.data?.errors || [];
                        vm.$store.dispatch("error", error.response?.data?.message || "An error occurred");
                    });
            },

               handleTimePeriodChange() {
                   const now = moment();
                console.log("selectedTimePeriod",this.selectedTimePeriod)
                   if (this.selectedTimePeriod === "8_hours") {
                    const past8Hours = moment().subtract(8, "hours");
                    this.process_trend.from_date = past8Hours.format("YYYY-MM-DDTHH:mm");
                    this.process_trend.to_date = now.format("YYYY-MM-DDTHH:mm");
                }
                else if (this.selectedTimePeriod === "24_hours") {
                    const past24Hours = moment().subtract(24, "hours");
                    this.process_trend.from_date = past24Hours.format("YYYY-MM-DDTHH:mm");
                    this.process_trend.to_date = now.format("YYYY-MM-DDTHH:mm");
                }
                else if (this.selectedTimePeriod === "7_days") {
                    const past7Days = moment().subtract(7, "days");
                    this.process_trend.from_date = past7Days.format("YYYY-MM-DDTHH:mm");
                    this.process_trend.to_date = now.format("YYYY-MM-DDTHH:mm");
                }
                else if (this.selectedTimePeriod === "1_month") {
                    const past1Month = moment().subtract(1, "months");
                    this.process_trend.from_date = past1Month.format("YYYY-MM-DDTHH:mm");
                    this.process_trend.to_date = now.format("YYYY-MM-DDTHH:mm");
                   }
                else if (this.selectedTimePeriod === "1_year") {
                        const past1Year = moment().subtract(1, "years");
                        this.process_trend.from_date = past1Year.format("YYYY-MM-DDTHH:mm");
                        this.process_trend.to_date = moment().format("YYYY-MM-DDTHH:mm");
                    }
                else if (this.selectedTimePeriod === "custom") {
                    this.process_trend.from_date = null;
                    this.process_trend.to_date = null;
                }
            },
        },
    };
</script>

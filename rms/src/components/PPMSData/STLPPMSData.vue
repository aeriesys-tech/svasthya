<template>
    <div class="">
        <div class="row">
            <div class="col-12">
                <div class="card card-one">
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="card-title">STL PPMS Data</h6>
                        <button class="btn btn-success btn-sm" @click="downloadExcel">
                            <i class="ri-download-line"></i> Download Excel
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">
                                <select
                                    class="form-control form-control-sm mb-2"
                                    v-model="meta.ladle_number"
                                    @change="search()"
                                >
                                    <option value="">Select Ladle No</option>
                                    <option v-for="ladle in ladle_numbers" :key="ladle" :value="ladle">
                                        {{ ladle }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-3">
                                <input
                                    class="form-control form-control-sm mb-2"
                                    type="datetime-local"
                                    placeholder="From Date & Time"
                                    v-model="meta.from_date"
                                    @change="search()"
                                />
                            </div>
                            <div class="col-3">
                                <input
                                    class="form-control form-control-sm mb-2"
                                    type="datetime-local"
                                    placeholder="To Date & Time"
                                    v-model="meta.to_date"
                                    @change="search()"
                                />
                            </div>
                            <div class="col-3">
                                <input
                                    class="form-control form-control-sm mb-2"
                                    type="text"
                                    placeholder="Type keyword and press enter key"
                                    v-model="meta.search"
                                    @keypress.enter="search()"
                                />
                            </div>
                        </div>
                        <div class="table-responsive table-responsive-sm">
                            <table class="table table-sm text-nowrap table-striped table-bordered mb-0">
                                <thead>
                                    <tr style="background-color: #9b9b9b; color: white">
                                        <th class="text-center">#</th>
                                        <th @click="sort('ladle_number')">
                                            Ladle No.
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'ladle_number' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'ladle_number' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('insert_date')">
                                            Date
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'insert_date' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'insert_date' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('heat_no')">
                                            Heat No.
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'heat_no' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'heat_no' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('grade')">
                                            Grade
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'grade' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'grade' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('re_treat')">
                                            Re Treat
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 're_treat' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 're_treat' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('holding_time')">
                                            Holding Time
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'holding_time' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'holding_time' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('processing_time')">
                                            Processing Time
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'processing_time' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'processing_time' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('o2_ppm')">
                                            O2 PPM
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'o2_ppm' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'o2_ppm' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('oxygen_after_celoxa')">
                                            Oxygen After Celoxa
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'oxygen_after_celoxa' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'oxygen_after_celoxa' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('heat_size')">
                                            Heat Size
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'heat_size' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'heat_size' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('al2_addition_bar')">
                                            Al2 Addition Bar
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'al2_addition_bar' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'al2_addition_bar' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('al2_addition_coil')">
                                            Al2 Addition Coil
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'al2_addition_coil' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'al2_addition_coil' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('tapping_temperature')">
                                            Tapping Temperature
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'tapping_temperature' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'tapping_temperature' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('lf_in_sulphur')">
                                            Lf In Sulphur
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'lf_in_sulphur' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'lf_in_sulphur' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('lf_in_temperature')">
                                            Lf In Temperature
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'lf_in_temperature' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'lf_in_temperature' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('lime_consumption')">
                                            Lime Consumption
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'lime_consumption' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'lime_consumption' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('tundish_temperature')">
                                            Tundish Temperature
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'tundish_temperature' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'tundish_temperature' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('super_heat_avg')">
                                            Super Heat Avg
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'super_heat_avg' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'super_heat_avg' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('super_heat_max')">
                                            Super Heat Max
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'super_heat_max' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'super_heat_max' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('lifting_temperature')">
                                            Lifting Temperature
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'lifting_temperature' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'lifting_temperature' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('lf_slag_report')">
                                            Lf Slag Report
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'lf_slag_report' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'lf_slag_report' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('created_at')">
                                            Created At
                                            <span>
                                                <i
                                                    v-if="meta.keyword == 'created_at' && meta.order_by == 'asc'"
                                                    class="ri-arrow-up-line"
                                                ></i>
                                                <i
                                                    v-else-if="meta.keyword == 'created_at' && meta.order_by == 'desc'"
                                                    class="ri-arrow-down-line"
                                                ></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(ppms, key) in ppms_datas" :key="key">
                                        <td class="text-center">{{ meta.from + key }}</td>
                                        <td>{{ ppms?.ladle_number }}</td>
                                        <td>{{ ppms?.insert_date }}</td>
                                        <td>{{ ppms?.heat_no }}</td>
                                        <td>{{ ppms?.grade }}</td>
                                        <td>{{ ppms?.re_treat }}</td>
                                        <td>{{ ppms?.holding_time }}</td>
                                        <td>{{ ppms?.processing_time }}</td>
                                        <td>{{ ppms?.o2_ppm }}</td>
                                        <td>{{ ppms?.oxygen_after_celoxa }}</td>
                                        <td>{{ ppms?.heat_size }}</td>
                                        <td>{{ ppms?.al2_addition_bar }}</td>
                                        <td>{{ ppms?.al2_addition_coil }}</td>
                                        <td>{{ ppms?.tapping_temperature }}</td>
                                        <td>{{ ppms?.lf_in_sulphur }}</td>
                                        <td>{{ ppms?.lf_in_temperature }}</td>
                                        <td>{{ ppms?.lime_consumption }}</td>
                                        <td>{{ ppms?.tundish_temperature }}</td>
                                        <td>{{ ppms?.super_heat_avg }}</td>
                                        <td>{{ ppms?.super_heat_max }}</td>
                                        <td>{{ ppms?.lifting_temperature }}</td>
                                        <td>{{ ppms?.lf_slag_report }}</td>
                                        <td>{{ ppms?.created_at }}</td>
                                    </tr>
                                    <tr v-if="ppms_datas.length == 0">
                                        <td colspan="23" class="text-center">No records found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <select
                                class="form-select form-select-sm width-75"
                                v-model="meta.per_page"
                                @change="onPerPageChange"
                            >
                                <option>10</option>
                                <option>15</option>
                                <option>20</option>
                                <option>25</option>
                                <option>30</option>
                                <option>200</option>
                                <option>500</option>
                            </select>
                            <span>Showing {{ meta.from }} to {{ meta.to }} of {{ meta.totalRows }} entries</span>
                            <Pagination
                                :maxPage="meta.maxPage"
                                :totalPages="meta.lastPage"
                                :currentPage="parseInt(meta.page)"
                                @pagechanged="onPageChange"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import Pagination from "@/components/Pagination.vue";
    export default {
        components: {
            Pagination,
        },
        data() {
            return {
                meta: {
                    search: "",
                    order_by: "desc",
                    keyword: "ppms_data_id",
                    per_page: 25,
                    totalRows: 0,
                    page: 1,
                    lastPage: 1,
                    from: 1,
                    to: 1,
                    maxPage: 1,
                    trashed: false,
                    ladle_number: "",
                    from_date: "",
                    to_date: "",
                },
                ppms_datas: [],
                ladle_numbers: [],
                errors: [],
                status: true,
            };
        },
        mounted() {
            this.index();
            this.ppmsLadleNos();
        },
        methods: {
            index() {
                let vm = this;
                let loader = vm.$loading.show();
                vm.$store
                    .dispatch("post", { uri: "paginatePPMSDatas", data: vm.meta })
                    .then((response) => {
                        loader.hide();
                        vm.ppms_datas = response.data.data;
                        vm.meta.totalRows = response.data.meta.total;
                        vm.meta.from = response.data.meta.from;
                        vm.meta.to = response.data.meta.to;
                        vm.meta.lastPage = response.data.meta.last_page;
                        vm.meta.maxPage = vm.meta.lastPage >= 3 ? 3 : vm.meta.lastPage;
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response?.data?.errors;
                        vm.$store.dispatch("error", error?.response?.data?.message);
                    });
            },
            ppmsLadleNos() {
                let vm = this;
                let loader = vm.$loading.show();
                vm.$store
                    .dispatch("post", { uri: "ppmsLadleNos" })
                    .then((response) => {
                        loader.hide();
                        vm.ladle_numbers = response.data.data;
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response?.data?.errors;
                        vm.$store.dispatch("error", error?.response?.data?.message);
                    });
            },
            search() {
                this.meta.page = 1;
                this.index();
            },
            onPageChange(page) {
                this.meta.page = page;
                this.index();
            },
            sort(field) {
                this.meta.keyword = field;
                this.meta.order_by = this.meta.order_by == "asc" ? "desc" : "asc";
                this.index();
            },
            onPerPageChange() {
                this.meta.page = 1;
                this.index();
            },
            downloadExcel() {
                let vm = this;
                let loader = vm.$loading.show();

                // Get all data with current filters (without pagination)
                let downloadParams = {
                    ladle_number: vm.meta.ladle_number,
                    from_date: vm.meta.from_date,
                    to_date: vm.meta.to_date,
                    search: vm.meta.search,
                    order_by: vm.meta.order_by,
                    keyword: vm.meta.keyword,
                    per_page: 10000, // Get all records
                    page: 1,
                };

                vm.$store
                    .dispatch("post", {
                        uri: "paginatePPMSDatas",
                        data: downloadParams,
                    })
                    .then((response) => {
                        loader.hide();

                        // Create CSV content
                        const csvContent = vm.createCSVContent(response.data.data);

                        // Create and download file
                        const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
                        const link = document.createElement("a");
                        const url = URL.createObjectURL(blob);

                        link.setAttribute("href", url);
                        const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, "-");
                        link.setAttribute("download", `ppms_data_${timestamp}.csv`);
                        link.style.visibility = "hidden";

                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);

                        vm.$store.dispatch("success", "Excel file downloaded successfully!");
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.$store.dispatch("error", error?.response?.data?.message || "Failed to fetch data for Excel");
                    });
            },
            createCSVContent(data) {
                // CSV headers
                const headers = [
                    "Ladle No.",
                    "Date",
                    "Heat No.",
                    "Grade",
                    "Re Treat",
                    "Holding Time",
                    "Processing Time",
                    "O2 PPM",
                    "Oxygen After Celoxa",
                    "Heat Size",
                    "Al2 Addition Bar",
                    "Al2 Addition Coil",
                    "Tapping Temperature",
                    "Lf In Sulphur",
                    "Lf In Temperature",
                    "Lime Consumption",
                    "Tundish Temperature",
                    "Super Heat Avg",
                    "Super Heat Max",
                    "Lifting Temperature",
                    "Lf Slag Report",
                    "Created At",
                ];

                // Create CSV rows
                const rows = data.map((item) => [
                    item.ladle_number || "",
                    item.insert_date || "",
                    item.heat_no || "",
                    item.grade || "",
                    item.re_treat || "",
                    item.holding_time || "",
                    item.processing_time || "",
                    item.o2_ppm || "",
                    item.oxygen_after_celoxa || "",
                    item.heat_size || "",
                    item.al2_addition_bar || "",
                    item.al2_addition_coil || "",
                    item.tapping_temperature || "",
                    item.lf_in_sulphur || "",
                    item.lf_in_temperature || "",
                    item.lime_consumption || "",
                    item.tundish_temperature || "",
                    item.super_heat_avg || "",
                    item.super_heat_max || "",
                    item.lifting_temperature || "",
                    item.lf_slag_report || "",
                    item.created_at || "",
                ]);

                // Combine headers and rows
                const csvContent = [headers, ...rows]
                    .map((row) => row.map((field) => `"${field}"`).join(","))
                    .join("\n");

                return csvContent;
            },
        },
    };
</script>

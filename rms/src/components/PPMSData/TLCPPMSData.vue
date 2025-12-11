<template>
    <div>
        <div class="row">
            <div class="col-12">
                <div class="card card-one">
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="card-title">TLC PPMS Data</h6>
                        <button class="btn btn-success btn-sm" @click="downloadExcel">
                            <i class="ri-download-line"></i> Download Excel
                        </button>
                    </div>

                    <div class="card-body">
                       
                        <div class="row mb-3">
                            <div class="col-3">
                                <select class="form-control form-control-sm" v-model="meta.torpedo_no" @change="search">
                                    <option disabled value="">Select Torpedo No</option>
                                    <option value="">ALL</option>
                                    <option v-for="no in torpedo_numbers" :key="no" :value="no">{{ no }}</option>
                                </select>
                            </div>

                            <div class="col-3">
                                <input
                                    class="form-control form-control-sm"
                                    type="datetime-local"
                                    placeholder="From Date & Time"
                                    v-model="meta.from_date"
                                    @change="search"
                                />
                            </div>

                            <div class="col-3">
                                <input
                                    class="form-control form-control-sm"
                                    type="datetime-local"
                                    placeholder="To Date & Time"
                                    v-model="meta.to_date"
                                    @change="search"
                                />
                            </div>

                            <div class="col-3">
                                <input
                                    class="form-control form-control-sm"
                                    type="text"
                                    placeholder="Type keyword and press enter key"
                                    v-model="meta.search"
                                    @keypress.enter="search"
                                />
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-striped table-bordered text-nowrap">
                                <thead style="background-color: #9b9b9b; color: white">
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th @click="sort('torpedo_no')" class="pointer">
                                            Torpedo No
                                            <sort-icon field="torpedo_no" />
                                        </th>
                                        <th @click="sort('proddate')" class="pointer">
                                            Prod Date
                                            <sort-icon field="proddate" />
                                        </th>
                                        <th @click="sort('shift')" class="pointer">
                                            Shift
                                            <sort-icon field="shift" />
                                        </th>
                                        <th @click="sort('hot_metal_qty')" class="pointer">
                                            Hot Metal Qty
                                            <sort-icon field="hot_metal_qty" />
                                        </th>
                                        <th @click="sort('gross_weight')" class="pointer">
                                            Gross Weight
                                            <sort-icon field="gross_weight" />
                                        </th>
                                        <th @click="sort('tare_wt')" class="pointer">
                                            Tare Weight
                                            <sort-icon field="tare_wt" />
                                        </th>
                                        <th @click="sort('mat_type')" class="pointer">
                                            Mat Type
                                            <sort-icon field="mat_type" />
                                        </th>
                                        <th @click="sort('hm_source')" class="pointer">
                                            HM Source
                                            <sort-icon field="hm_source" />
                                        </th>
                                        <th @click="sort('supply_area')" class="pointer">
                                            Supply Area
                                            <sort-icon field="supply_area" />
                                        </th>
                                        <th @click="sort('bf_cast_no')" class="pointer">
                                            BF Cast No
                                            <sort-icon field="bf_cast_no" />
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(row, idx) in ppms_datas" :key="row.tlc_ppms_data_id">
                                        <td class="text-center">{{ meta.from + idx }}</td>
                                        <td>{{ row.torpedo_no }}</td>
                                        <td>{{ row.proddate }}</td>
                                        <td>{{ row.shift }}</td>
                                        <td>{{ row.hot_metal_qty }}</td>
                                        <td>{{ row.gross_weight }}</td>
                                        <td>{{ row.tare_wt }}</td>
                                        <td>{{ row.mat_type }}</td>
                                        <td>{{ row.hm_source }}</td>
                                        <td>{{ row.supply_area }}</td>
                                        <td>{{ row.bf_cast_no }}</td>
                                    </tr>
                                    <tr v-if="!ppms_datas.length">
                                        <td colspan="11" class="text-center">No records found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <select
                                class="form-select form-select-sm"
                                style="width: 75px"
                                v-model.number="meta.per_page"
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

                            <span> Showing {{ meta.from }} to {{ meta.to }} of {{ meta.totalRows }} entries </span>

                            <Pagination
                                :maxPage="meta.maxPage"
                                :totalPages="meta.lastPage"
                                :currentPage="meta.page"
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
        components: { Pagination },

        data() {
            return {
                meta: {
                    search: "",
                    order_by: "desc",
                    keyword: "tlc_ppms_data_id",
                    per_page: 25,
                    totalRows: 0,
                    page: 1,
                    lastPage: 1,
                    from: 1,
                    to: 1,
                    maxPage: 1,
                    torpedo_no: "",
                    from_date: "",
                    to_date: "",
                },

                ppms_datas: [],
                torpedo_numbers: [], 
            };
        },

        mounted() {
            this.loadTorpedoNumbers();
            this.index();
        },

        methods: 
        {
            index() {
                const loader = this.$loading.show();
                this.$store
                    .dispatch("post", { uri: "paginateTLCPPMSDatas", data: this.meta })
                    .then((r) => {
                        this.ppms_datas = r.data.data;
                        const m = r.data.meta;
                        this.meta.totalRows = m.total;
                        this.meta.from = m.from;
                        this.meta.to = m.to;
                        this.meta.lastPage = m.last_page;
                        this.meta.maxPage = m.last_page >= 3 ? 3 : m.last_page;
                    })
                    .catch(() => this.$store.dispatch("error", "Failed to load data"))
                    .finally(() => loader.hide());
            },

            loadTorpedoNumbers() {
                const loader = this.$loading.show();
                this.$store
                    .dispatch("post", { uri: "ppmsTLCLadleNos" })
                    .then((r) => (this.torpedo_numbers = r.data.data))
                    .catch(() => this.$store.dispatch("error", "Failed to load torpedo numbers"))
                    .finally(() => loader.hide());
            },

            sort(field) {
                if (this.meta.keyword === field) {
                    this.meta.order_by = this.meta.order_by === "asc" ? "desc" : "asc";
                } else {
                    this.meta.keyword = field;
                    this.meta.order_by = "asc";
                }
                this.index();
            },

            search() {
                this.meta.page = 1;
                this.index();
            },

            onPerPageChange() {
                this.meta.page = 1;
                this.index();
            },

            onPageChange(page) {
                this.meta.page = page;
                this.index();
            },

            downloadExcel() {
                if (!this.ppms_datas.length && this.meta.totalRows === 0) {
                    alert("No data to export");
                    return;
                }

                const loader = this.$loading.show();

                const params = {
                    ...this.meta,
                    per_page: 10000,
                    page: 1,
                };

                this.$store
                    .dispatch("post", { uri: "paginateTLCPPMSDatas", data: params })
                    .then((r) => {
                        const csv = this.buildCSV(r.data.data);
                        this.triggerDownload(csv);
                        this.$store.dispatch("success", "CSV downloaded");
                    })
                    .catch(() => this.$store.dispatch("error", "Export failed"))
                    .finally(() => loader.hide());
            },

            buildCSV(rows) {
                const headers = [
                    "#",
                    "Torpedo No",
                    "Prod Date",
                    "Shift",
                    "Hot Metal Qty",
                    "Gross Weight",
                    "Tare Weight",
                    "Mat Type",
                    "HM Source",
                    "Supply Area",
                    "BF Cast No",
                ];

                const dataRows = rows.map((r, idx) => [
                    idx + 1,
                    r.torpedo_no ?? "",
                    r.proddate ?? "",
                    r.shift ?? "",
                    r.hot_metal_qty ?? "",
                    r.gross_weight ?? "",
                    r.tare_wt ?? "",
                    r.mat_type ?? "",
                    r.hm_source ?? "",
                    r.supply_area ?? "",
                    r.bf_cast_no ?? "",
                ]);

                const csvLines = [headers, ...dataRows].map((line) => line.map((v) => `"${v}"`).join(",")).join("\n");

                return "\ufeff" + csvLines;
            },

            triggerDownload(csvContent) {
                const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
                const url = URL.createObjectURL(blob);
                const a = document.createElement("a");
                a.href = url;
                const ts = new Date().toISOString().slice(0, 19).replace(/:/g, "-");
                a.download = `tlc_ppms_data_${ts}.csv`;
                document.body.appendChild(a);
                a.click();
                setTimeout(() => {
                    URL.revokeObjectURL(url);
                    a.remove();
                }, 100);
            },
        },
    };

    const SortIcon = {
        functional: true,
        render(h, { parent }) {
            const { keyword, order_by } = parent.meta;
            const field = parent.$attrs.field;

            if (keyword !== field) return h("i", { class: "fas fa-sort ms-1" });

            return h("i", {
                class: order_by === "asc" ? "ri-arrow-up-line ms-1" : "ri-arrow-down-line ms-1",
            });
        },
    };
</script>

<style scoped>
    .pointer {
        cursor: pointer;
    }
</style>

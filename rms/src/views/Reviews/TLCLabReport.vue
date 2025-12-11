<template>
    <div class="">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <div>
                <ol class="breadcrumb fs-sm mb-1">
                    <li class="breadcrumb-item" aria-current="page">
                        <router-link to="/dashboard">Dashboard</router-link>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="javascript:void(0)">Reviews</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">Torpedo Lab Report</li>
                </ol>
                <h4 class="main-title mb-0">Torpedo Lab Report</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-one">
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="card-title">Torpedo Lab Report</h6>
                    </div>

                    <div class="card-body">
                        <input class="form-control form-control-sm mb-2" type="text" placeholder="Type keyword and press enter key" v-model="meta.search" @keypress.enter="search" />

                        <div class="table-responsive table-responsive-sm">
                            <table class="table table-sm text-nowrap table-striped table-bordered mb-0">
                                <thead>
                                    <tr style="background-color: #9b9b9b; color: white;">
                                        <th class="text-center">#</th>
                                        
                                        <th @click="sort('torpedo_ladle_no')">
                                            Torpedo Ladle No <span><i :class="sortIcon('torpedo_ladle_no')"></i></span>
                                        </th>
                                        <th @click="sort('bf_cast_no')">
                                            BF Cast No <span><i :class="sortIcon('bf_cast_no')"></i></span>
                                        </th>
                                        <th @click="sort('arrival')">
                                            Arrival <span><i :class="sortIcon('arrival')"></i></span>
                                        </th>
                                        <th @click="sort('hot_metal_report')">
                                            Hot Metal Report <span><i :class="sortIcon('hot_metal_report')"></i></span>
                                        </th>
                                        <th @click="sort('hot_metal_c')">
                                            C <span><i :class="sortIcon('hot_metal_c')"></i></span>
                                        </th>
                                        <th @click="sort('hot_metal_si')">
                                            Si <span><i :class="sortIcon('hot_metal_si')"></i></span>
                                        </th>
                                        <th @click="sort('hot_metal_mn')">
                                            Mn <span><i :class="sortIcon('hot_metal_mn')"></i></span>
                                        </th>
                                        <th @click="sort('hot_metal_s')">
                                            S <span><i :class="sortIcon('hot_metal_s')"></i></span>
                                        </th>
                                        <th @click="sort('hot_metal_p')">
                                            P <span><i :class="sortIcon('hot_metal_p')"></i></span>
                                        </th>
                                        <th @click="sort('hot_metal_ti')">
                                            Ti <span><i :class="sortIcon('hot_metal_ti')"></i></span>
                                        </th>
                                        <th @click="sort('hot_metal_zn')">
                                            Zn <span><i :class="sortIcon('hot_metal_zn')"></i></span>
                                        </th>
                                        <th @click="sort('temp')">
                                            Temp <span><i :class="sortIcon('temp')"></i></span>
                                        </th>
                                        <th @click="sort('bf_slag_report')">
                                            BF Slag Report <span><i :class="sortIcon('bf_slag_report')"></i></span>
                                        </th>
                                        <th @click="sort('bf_slag_cao')">
                                            CaO <span><i :class="sortIcon('bf_slag_cao')"></i></span>
                                        </th>
                                        <th @click="sort('bf_slag_mgo')">
                                            MgO <span><i :class="sortIcon('bf_slag_mgo')"></i></span>
                                        </th>
                                        <th @click="sort('bf_slag_sio2')">
                                            SiO2 <span><i :class="sortIcon('bf_slag_sio2')"></i></span>
                                        </th>
                                        <th @click="sort('bf_slag_al2o3')">
                                            Al2O3 <span><i :class="sortIcon('bf_slag_al2o3')"></i></span>
                                        </th>
                                        <th @click="sort('bf_slag_feo')">
                                            FeO <span><i :class="sortIcon('bf_slag_feo')"></i></span>
                                        </th>
                                        <th @click="sort('bf_slag_s')">
                                            S <span><i :class="sortIcon('bf_slag_s')"></i></span>
                                        </th>
                                        <th @click="sort('bf_slag_mno')">
                                            MnO <span><i :class="sortIcon('bf_slag_mno')"></i></span>
                                        </th>
                                        <th @click="sort('bf_slag_na2o')">
                                            Na2O <span><i :class="sortIcon('bf_slag_na2o')"></i></span>
                                        </th>
                                        <th @click="sort('bf_slag_k2o')">
                                            K2O <span><i :class="sortIcon('bf_slag_k2o')"></i></span>
                                        </th>
                                        <th @click="sort('bf_slag_tio2')">
                                            TiO2 <span><i :class="sortIcon('bf_slag_tio2')"></i></span>
                                        </th>
                                        <th @click="sort('basicity')">
                                            Basicity <span><i :class="sortIcon('basicity')"></i></span>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="(lab, key) in torpedoLabs" :key="lab.torpedo_lab_id">
                                        <td class="text-center">{{ meta.from + key }}</td>
                                       
                                        <td>{{ lab?.torpedo_ladle_no || '' }}</td>
                                        <td>{{ lab?.bf_cast_no || '' }}</td>
                                        <td>{{ lab?.arrival || '' }}</td>
                                        <td>{{ lab?.hot_metal_report || '' }}</td>
                                        <td>{{ lab?.hot_metal_c || '' }}</td>
                                        <td>{{ lab?.hot_metal_si || '' }}</td>
                                        <td>{{ lab?.hot_metal_mn || '' }}</td>
                                        <td>{{ lab?.hot_metal_s || '' }}</td>
                                        <td>{{ lab?.hot_metal_p || '' }}</td>
                                        <td>{{ lab?.hot_metal_ti || '' }}</td>
                                        <td>{{ lab?.hot_metal_zn || '' }}</td>
                                        <td>{{ lab?.temp || '' }}</td>
                                        <td>{{ lab?.bf_slag_report || '' }}</td>
                                        <td>{{ lab?.bf_slag_cao || '' }}</td>
                                        <td>{{ lab?.bf_slag_mgo || '' }}</td>
                                        <td>{{ lab?.bf_slag_sio2 || '' }}</td>
                                        <td>{{ lab?.bf_slag_al2o3 || '' }}</td>
                                        <td>{{ lab?.bf_slag_feo || '' }}</td>
                                        <td>{{ lab?.bf_slag_s || '' }}</td>
                                        <td>{{ lab?.bf_slag_mno || '' }}</td>
                                        <td>{{ lab?.bf_slag_na2o || '' }}</td>
                                        <td>{{ lab?.bf_slag_k2o || '' }}</td>
                                        <td>{{ lab?.bf_slag_tio2 || '' }}</td>
                                        <td>{{ lab?.basicity || '' }}</td>
                                    </tr>

                                    <tr v-if="torpedoLabs.length === 0">
                                        <td colspan="27" class="text-center">No records found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <select class="form-select form-select-sm width-75" v-model="meta.per_page" @change="onPerPageChange">
                                <option>10</option>
                                <option>15</option>
                                <option>20</option>
                                <option>25</option>
                                <option>30</option>
                                <option>200</option>
                                <option>500</option>
                            </select>
                            <span>Showing {{ meta.from }} to {{ meta.to }} of {{ meta.totalRows }} entries</span>
                            <Pagination :maxPage="meta.maxPage" :totalPages="meta.lastPage" :currentPage="parseInt(meta.page)" @pagechanged="onPageChange" />
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
                    keyword: "torpedo_lab_id",
                    per_page: 15,
                    totalRows: 0,
                    page: 1,
                    lastPage: 1,
                    from: 1,
                    to: 1,
                    maxPage: 1,
                    trashed: false,
                },
                torpedoLabs: [],
                errors: {},
            };
        },
        mounted() {
            this.index();
        },
        methods: {
            index() {
                let loader = this.$loading.show();
                this.$store
                    .dispatch("post", { uri: "paginateTLCLabReport", data: this.meta })
                    .then((response) => {
                        loader.hide();
                        this.torpedoLabs = response.data.data;
                        this.meta.totalRows = response.data.meta.total;
                        this.meta.from = response.data.meta.from;
                        this.meta.to = response.data.meta.to;
                        this.meta.lastPage = response.data.meta.last_page;
                        this.meta.maxPage = this.meta.lastPage >= 3 ? 3 : this.meta.lastPage;
                    })
                    .catch((error) => {
                        loader.hide();
                        this.errors = error.response?.data?.errors || {};
                        this.$store.dispatch("error", error?.response?.data?.message || "Failed to load data");
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
                this.meta.order_by = this.meta.order_by === "asc" ? "desc" : "asc";
                this.index();
            },
            onPerPageChange() {
                this.meta.page = 1;
                this.index();
            },
            sortIcon(field) {
                if (this.meta.keyword === field) {
                    return this.meta.order_by === "asc" ? "ri-arrow-up-line" : "ri-arrow-down-line";
                }
                return "fas fa-sort";
            },
        },
    };
</script>

<style scoped>
    .is-invalid {
        border-color: #dc3545;
    }
    .invalid-feedback {
        display: block;
        color: #dc3545;
        font-size: 0.875rem;
    }
    .width-75 {
        width: 75px;
    }
</style>

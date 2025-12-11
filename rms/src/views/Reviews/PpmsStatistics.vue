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
                    <li class="breadcrumb-item active" aria-current="page">Running Life Summary</li>
                </ol>
                <h4 class="main-title mb-0">Running Life Summary</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-one">
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="card-title">Running Life Summary</h6>
                    </div>

                    <div class="card-body">
                        <input class="form-control form-control-sm mb-2" type="text" placeholder="Type keyword and press enter key" v-model="meta.search" @keypress.enter="search" />

                        <div class="table-responsive table-responsive-sm">
                            <table class="table table-sm text-nowrap table-striped table-bordered mb-0">
                                <thead>
                                    <tr style="background-color: #9b9b9b; color: white;">
                                        <th class="text-center">#</th>
                                        <th @click="sort('asset_id')">
                                            Asset <span><i :class="sortIcon('asset_id')"></i></span>
                                        </th>
                                        <th @click="sort('date_time')">
                                            Date Time <span><i :class="sortIcon('date_time')"></i></span>
                                        </th>
                                        <th @click="sort('activity')">
                                            Activity <span><i :class="sortIcon('activity')"></i></span>
                                        </th>
                                        <th @click="sort('campaign_no')">
                                            Campaign No. <span><i :class="sortIcon('campaign_no')"></i></span>
                                        </th>
                                        <th @click="sort('running_life')">
                                            Full Ladle Life <span><i :class="sortIcon('running_life')"></i></span>
                                        </th>
                                        <th @click="sort('repair_life')">
                                            Repair Life <span><i :class="sortIcon('repair_life')"></i></span>
                                        </th>
                                        <th @click="sort('process_time')">
                                            Process Time <span><i :class="sortIcon('process_time')"></i></span>
                                        </th>
                                        <th @click="sort('total_billets')">
                                            Total Billets <span><i :class="sortIcon('total_billets')"></i></span>
                                        </th>
                                        <th @click="sort('billet_repair_heats')">
                                            Billet Repair Heats <span><i :class="sortIcon('billet_repair_heats')"></i></span>
                                        </th>
                                        <th @click="sort('continuous_billets')">
                                            Continuous Billets <span><i :class="sortIcon('continuous_billets')"></i></span>
                                        </th>
                                        <th>Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="(ppms, key) in ppms_statistics" :key="ppms.ppms_statistic_id">
                                        <td class="text-center">{{ meta.from + key }}</td>
                                        <td>{{ ppms?.asset?.asset_code || '' }}</td>
                                        <td>{{ ppms?.date_time || '' }}</td>
                                        <td>{{ ppms?.activity || '' }}</td>

                                        <!-- Campaign No -->
                                        <td>
                                            <input v-if="editRowIndex === key" v-model="editData.campaign_no" class="form-control form-control-sm" type="text" :class="{ 'is-invalid': errors.campaign_no }" />
                                            <span v-else>{{ ppms?.campaign_no || '' }}</span>
                                            <div v-if="errors.campaign_no" class="invalid-feedback">{{ errors.campaign_no }}</div>
                                        </td>

                                        <!-- Running Life -->
                                        <td>
                                            <input v-if="editRowIndex === key" v-model="editData.running_life" class="form-control form-control-sm" type="text" :class="{ 'is-invalid': errors.running_life }" />
                                            <span v-else>{{ ppms?.running_life || '' }}</span>
                                            <div v-if="errors.running_life" class="invalid-feedback">{{ errors.running_life }}</div>
                                        </td>

                                        <!-- Repair Life -->
                                        <td>
                                            <input v-if="editRowIndex === key" v-model="editData.repair_life" class="form-control form-control-sm" type="text" :class="{ 'is-invalid': errors.repair_life }" />
                                            <span v-else>{{ ppms?.repair_life || '' }}</span>
                                            <div v-if="errors.repair_life" class="invalid-feedback">{{ errors.repair_life }}</div>
                                        </td>

                                        <!-- Process Time -->
                                        <td>
                                            <input v-if="editRowIndex === key" v-model="editData.process_time" class="form-control form-control-sm" type="text" :class="{ 'is-invalid': errors.process_time }" />
                                            <span v-else>{{ ppms?.process_time || '' }}</span>
                                            <div v-if="errors.process_time" class="invalid-feedback">{{ errors.process_time }}</div>
                                        </td>

                                        <!-- Total Billets -->
                                        <td>
                                            <input v-if="editRowIndex === key" v-model="editData.total_billets" class="form-control form-control-sm" type="text" :class="{ 'is-invalid': errors.total_billets }" />
                                            <span v-else>{{ ppms?.total_billets || '' }}</span>
                                            <div v-if="errors.total_billets" class="invalid-feedback">{{ errors.total_billets }}</div>
                                        </td>

                                        <!-- Billet Repair Heats -->
                                        <td>
                                            <input v-if="editRowIndex === key" v-model="editData.billet_repair_heats" class="form-control form-control-sm" type="text" :class="{ 'is-invalid': errors.billet_repair_heats }" />
                                            <span v-else>{{ ppms?.billet_repair_heats || '' }}</span>
                                            <div v-if="errors.billet_repair_heats" class="invalid-feedback">{{ errors.billet_repair_heats }}</div>
                                        </td>

                                        <!-- Continuous Billets -->
                                        <td>
                                            <input v-if="editRowIndex === key" v-model="editData.continuous_billets" class="form-control form-control-sm" type="text" :class="{ 'is-invalid': errors.continuous_billets }" />
                                            <span v-else>{{ ppms?.continuous_billets || '' }}</span>
                                            <div v-if="errors.continuous_billets" class="invalid-feedback">{{ errors.continuous_billets }}</div>
                                        </td>

                                        <!-- Actions -->
                                        <td class="text-center">
                                            <a
                                                href="javascript:void(0)"
                                                :class="editRowIndex === key ? 'text-primary' : 'text-success'"
                                                style="margin-right: 8px;"
                                                @click="editRowIndex === key ? updateStatistic(key) : editStatistic(ppms, key)"
                                                :title="editRowIndex === key ? 'Update Statistics' : 'Edit Statistics'"
                                            >
                                                <i :class="editRowIndex === key ? 'ri-save-line fs-18 lh-1' : 'ri-pencil-line fs-18 lh-1'"></i>
                                            </a>
                                            <a v-if="editRowIndex === key" href="javascript:void(0)" class="text-danger" @click="cancelEdit" title="Cancel Edit">
                                                <i class="ri-close-line fs-18 lh-1"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <tr v-if="ppms_statistics.length === 0">
                                        <td colspan="12" class="text-center">No records found</td>
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
                    keyword: "ppms_statistic_id",
                    per_page: 15,
                    totalRows: 0,
                    page: 1,
                    lastPage: 1,
                    from: 1,
                    to: 1,
                    maxPage: 1,
                    trashed: false,
                },
                ppms_statistics: [],
                errors: {},
                status: true,
                editRowIndex: null,
                editData: {}, // Store data for the row being edited
            };
        },
        mounted() {
            this.index();
        },
        methods: {
            index() {
                let loader = this.$loading.show();
                this.$store
                    .dispatch("post", { uri: "getLatestPpmsStatistics", data: this.meta })
                    .then((response) => {
                        loader.hide();
                        this.ppms_statistics = response.data.data;
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
            editStatistic(ppms, key) {
                this.editRowIndex = key;
                this.errors = {};
                this.editData = {
                    ppms_statistic_id: ppms.ppms_statistic_id,
                    date_time: ppms.date_time,
                    activity: ppms.activity,
                    campaign_no: ppms.campaign_no,
                    running_life: ppms.running_life,
                    repair_life: ppms.repair_life,
                    total_billets: ppms.total_billets,
                    continuous_billets: ppms.continuous_billets,
                    asset: ppms.asset ? { ...ppms.asset } : null,
                    process_time: ppms.process_time,
                    billet_repair_heats: ppms.billet_repair_heats,
                };
            },
            cancelEdit() {
                this.editRowIndex = null;
                this.editData = {};
                this.errors = {};
            },
            updateStatistic(key) {
                let updatedData = {
                    ppms_statistic_id: this.editData.ppms_statistic_id,
                    date_time: this.editData.date_time,
                    activity: this.editData.activity,
                    campaign_no: this.editData.campaign_no,
                    running_life: this.editData.running_life,
                    repair_life: this.editData.repair_life,
                    total_billets: this.editData.total_billets,
                    continuous_billets: this.editData.continuous_billets,
                    asset_id: this.editData.asset?.asset_id || null,
                    process_time: this.editData.process_time,
                    billet_repair_heats: this.editData.billet_repair_heats,
                };

                let loader = this.$loading.show();
                this.$store
                    .dispatch("post", { uri: "updatePpmsStatistic", data: updatedData })
                    .then((response) => {
                        loader.hide();
                        this.editRowIndex = null;
                        this.editData = {};
                        this.errors = {};
                        this.$store.dispatch("success", response.data.message);

                        // Vue 3: no $set needed
                        this.ppms_statistics[key] = {
                            ...this.ppms_statistics[key],
                            ...updatedData,
                        };
                    })
                    .catch((error) => {
                        loader.hide();
                        this.errors = error.response?.data?.errors || {};
                        this.$store.dispatch("error", error?.response?.data?.message || "Failed to update record");
                    });
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

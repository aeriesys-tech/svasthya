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
                    <li class="breadcrumb-item active" aria-current="page">Alerts</li>
                </ol>
                <h4 class="main-title mb-0">Alerts</h4>
            </div>
            <div>
                <button v-can="'alerts.create'" type="button" class="btn btn-primary" @click="openCreateModal">
                    <i class="ri-add-line fs-18 lh-1"></i> Add Alert
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-one">
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="card-title">Alerts</h6>
                    </div>

                    <div class="card-body">
                        <input class="form-control form-control-sm mb-2" type="text" placeholder="Type keyword and press enter key" v-model="meta.search" @keypress.enter="search" />

                        <div class="table-responsive table-responsive-sm">
                            <table class="table table-sm text-nowrap table-striped table-bordered mb-0">
                                <thead>
                                    <tr style="background-color: #9b9b9b; color: white;">
                                        <th class="text-center">#</th>
                                        <th @click="sort('asset_type_id')">
                                            Asset Type <span><i :class="sortIcon('asset_type_id')"></i></span>
                                        </th>
                                        <th @click="sort('check')">
                                            Check <span><i :class="sortIcon('check')"></i></span>
                                        </th>
                                        <!-- <th>Stage 1 Emails</th> -->
                                        <!-- <th>Stage 2 Emails</th> -->
                                        <!-- <th>Stage 3 Emails</th> -->
                                        <th>Stage 1 Conditions</th>
                                        <th>Stage 2 Conditions</th>
                                        <th>Stage 3 Conditions</th>
                                        <!-- <th>Stage 1 Values</th> -->
                                        <!-- <th>Stage 2 Values</th> -->
                                        <!-- <th>Stage 3 Values</th> -->
                                        <th class="text-center" v-can="['alerts.update','alerts.delete', 'alerts.view']">Actions</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <tr v-for="(alert, key) in alerts" :key="alert.alert_id">
                                        <td class="text-center">{{ meta.from + key }}</td>
                                        <td>
                                            <a
                                                href="javascript:void(0)"
                                                class="text-primary"
                                                style="cursor: pointer; text-decoration: none;"
                                                @click="openEditModal(alert)"
                                                title="edit alert"
                                            >
                                                {{ alert?.asset_type?.asset_type_name || '' }}
                                            </a>
                                        </td>
                                        <td>{{ alert?.check || '' }}</td>
                                        <!-- <td>{{ alert?.stage_1_emails || '' }}</td> -->
                                        <!-- <td>{{ alert?.stage_2_emails || '' }}</td> -->
                                        <!-- <td>{{ alert?.stage_3_emails || '' }}</td> -->
                                        <td>{{ alert?.stage_1_conditions || '' }}</td>
                                        <td>{{ alert?.stage_2_conditions || '' }}</td>
                                        <td>{{ alert?.stage_3_conditions || '' }}</td>
                                        <!-- <td>{{ alert?.stage_1_values || '' }}</td> -->
                                        <!-- <td>{{ alert?.stage_2_values || '' }}</td> -->
                                        <!-- <td>{{ alert?.stage_3_values || '' }}</td> -->
                                        <td class="text-center" v-can="['alerts.update','alerts.delete', 'alerts.view']">
                                            <a
                                                href="javascript:void(0)"
                                                class="text-success"
                                                style="margin-right: 8px;"
                                                @click="openEditModal(alert)"
                                                title="Edit Alert"
                                                v-can="'alerts.update'"
                                            >
                                                <i class="ri-pencil-line fs-18 lh-1"></i>
                                            </a>
                                            <a
                                                href="javascript:void(0)"
                                                class="text-primary me-2"
                                                style="margin-right: 8px;"
                                                @click="openViewModal(alert)"
                                                title="View Alert" 
                                                v-can="'alerts.view'"
                                            >
                                                <i class="ri-eye-fill fs-18 lh-1"></i>
                                            </a>
                                            <a
                                                href="javascript:void(0)"
                                                class="text-danger"
                                                @click="deleteAlert(alert)"
                                                title="Delete Alert"
                                                v-can="'alerts.delete'"
                                            >
                                                <i class="ri-delete-bin-line fs-18 lh-1"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <tr v-if="alerts.length === 0">
                                        <td colspan="13" class="text-center">No records found</td>
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
                                
                            </select>
                            <span>Showing {{ meta.from }} to {{ meta.to }} of {{ meta.totalRows }} entries</span>
                            <Pagination :maxPage="meta.maxPage" :totalPages="meta.lastPage" :currentPage="parseInt(meta.page)" @pagechanged="onPageChange" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Create/Edit Modal -->
        <div class="modal fade" id="alertModal" tabindex="-1" aria-labelledby="alertModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl alert-modal-custom">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="alertModalLabel">{{ viewMode ? 'View Alert' : (isEditMode ? 'Edit Alert' : 'Add Alert') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form @submit.prevent="submitForm">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Asset Type <span class="text-danger">*</span></label>
                                    <search
                                        v-if="!viewMode && !isEditMode"
                                        :class="{ 'is-invalid': errors?.asset_type_id }"
                                        :customClass="{ 'is-invalid': errors?.asset_type_id }"
                                        :initialize="form.asset_type_id"
                                        id="asset_type_id"
                                        label="asset_type_name"
                                        placeholder="Select Asset Type"
                                        :data="asset_types"
                                        @input="asset_type_id => form.asset_type_id = asset_type_id"
                                    />
                                    <input
                                        v-else
                                        type="text"
                                        class="form-control"
                                        :value="getAssetTypeName(form.asset_type_id)"
                                        readonly
                                        disabled
                                    />
                                    <small v-if="errors?.asset_type_id" class="text-danger">{{ errors.asset_type_id[0] }}</small>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Check <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors?.check }"
                                        v-model="form.check"
                                        placeholder="Enter Check"
                                        :readonly="viewMode || isEditMode"
                                        :disabled="viewMode || isEditMode"
                                    />
                                    <small v-if="errors?.check" class="text-danger">{{ errors.check[0] }}</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Stage 1 Conditions</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors?.stage_1_conditions }"
                                        v-model="form.stage_1_conditions"
                                        placeholder="Enter Stage 1 Conditions"
                                        :readonly="viewMode || isEditMode"
                                        :disabled="viewMode || isEditMode"
                                    />
                                    <small v-if="errors?.stage_1_conditions" class="text-danger">{{ errors.stage_1_conditions[0] }}</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Stage 2 Conditions</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors?.stage_2_conditions }"
                                        v-model="form.stage_2_conditions"
                                        placeholder="Enter Stage 2 Conditions"
                                        :readonly="viewMode || isEditMode"
                                        :disabled="viewMode || isEditMode"
                                    />
                                    <small v-if="errors?.stage_2_conditions" class="text-danger">{{ errors.stage_2_conditions[0] }}</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Stage 3 Conditions</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors?.stage_3_conditions }"
                                        v-model="form.stage_3_conditions"
                                        placeholder="Enter Stage 3 Conditions"
                                        :readonly="viewMode || isEditMode"
                                        :disabled="viewMode || isEditMode"
                                    />
                                    <small v-if="errors?.stage_3_conditions" class="text-danger">{{ errors.stage_3_conditions[0] }}</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Stage 1 Values</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors?.stage_1_values }"
                                        v-model="form.stage_1_values"
                                        placeholder="Enter Stage 1 Values"
                                        :readonly="viewMode || isEditMode"
                                        :disabled="viewMode || isEditMode"
                                    />
                                    <small v-if="errors?.stage_1_values" class="text-danger">{{ errors.stage_1_values[0] }}</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Stage 2 Values</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors?.stage_2_values }"
                                        v-model="form.stage_2_values"
                                        placeholder="Enter Stage 2 Values"
                                        :readonly="viewMode || isEditMode"
                                        :disabled="viewMode || isEditMode"
                                    />
                                    <small v-if="errors?.stage_2_values" class="text-danger">{{ errors.stage_2_values[0] }}</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Stage 3 Values</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors?.stage_3_values }"
                                        v-model="form.stage_3_values"
                                        placeholder="Enter Stage 3 Values"
                                        :readonly="viewMode || isEditMode"
                                        :disabled="viewMode || isEditMode"
                                    />
                                    <small v-if="errors?.stage_3_values" class="text-danger">{{ errors.stage_3_values[0] }}</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Stage 1 Emails</label>
                                    <textarea
                                        class="form-control"
                                        :class="{ 'is-invalid': errors?.stage_1_emails }"
                                        v-model="form.stage_1_emails"
                                        placeholder="Enter Stage 1 Emails"
                                        rows="4"
                                        :readonly="viewMode"
                                        :disabled="viewMode"
                                    ></textarea>
                                    <small v-if="errors?.stage_1_emails" class="text-danger">{{ errors.stage_1_emails[0] }}</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Stage 2 Emails</label>
                                    <textarea
                                        class="form-control"
                                        :class="{ 'is-invalid': errors?.stage_2_emails }"
                                        v-model="form.stage_2_emails"
                                        placeholder="Enter Stage 2 Emails"
                                        rows="4"
                                        :readonly="viewMode"
                                        :disabled="viewMode"
                                    ></textarea>
                                    <small v-if="errors?.stage_2_emails" class="text-danger">{{ errors.stage_2_emails[0] }}</small>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Stage 3 Emails</label>
                                    <textarea
                                        class="form-control"
                                        :class="{ 'is-invalid': errors?.stage_3_emails }"
                                        v-model="form.stage_3_emails"
                                        placeholder="Enter Stage 3 Emails"
                                        rows="4"
                                        :readonly="viewMode"
                                        :disabled="viewMode"
                                    ></textarea>
                                    <small v-if="errors?.stage_3_emails" class="text-danger">{{ errors.stage_3_emails[0] }}</small>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button v-if="!viewMode" type="button" class="btn btn-primary" @click="submitForm">
                            <span v-if="isEditMode">Update</span>
                            <span v-else>Save</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Pagination from "@/components/Pagination.vue";
    import Search from "@/components/Search.vue";

    export default {
        components: { Pagination, Search },
        data() {
            return {
                meta: {
                    search: "",
                    order_by: "desc",
                    keyword: "alert_id",
                    per_page: 15,
                    totalRows: 0,
                    page: 1,
                    lastPage: 1,
                    from: 1,
                    to: 1,
                    maxPage: 1,
                    trashed: false,
                },
                alerts: [],
                asset_types: [],
                errors: {},
                isEditMode: false,
                viewMode: false,
                form: {
                    alert_id: null,
                    asset_type_id: "",
                    check: "",
                    stage_1_emails: "",
                    stage_2_emails: "",
                    stage_3_emails: "",
                    stage_1_conditions: "",
                    stage_2_conditions: "",
                    stage_3_conditions: "",
                    stage_1_values: "",
                    stage_2_values: "",
                    stage_3_values: "",
                },
            };
        },
        mounted() {
            this.index();
            this.loadAssetTypes();
        },
        methods: {
            index() {
                let loader = this.$loading.show();
                this.$store
                    .dispatch("post", { uri: "paginateAlerts", data: this.meta })
                    .then((response) => {
                        loader.hide();
                        this.alerts = response.data.data;
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
            loadAssetTypes() {
                this.$store
                    .dispatch("post", { uri: "getAssetTypes", data: {} })
                    .then((response) => {
                        this.asset_types = response.data.data || [];
                    })
                    .catch((error) => {
                        console.error("Failed to load asset types", error);
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
            openCreateModal() {
                this.isEditMode = false;
                this.viewMode = false;
                this.resetForm();
                this.errors = {};
                const modal = new bootstrap.Modal(document.getElementById('alertModal'));
                modal.show();
            },
            openViewModal(alert) {
                this.isEditMode = false;
                this.viewMode = true;
                this.errors = {};
                this.form = {
                    alert_id: alert.alert_id,
                    asset_type_id: alert.asset_type_id || "",
                    check: alert.check || "",
                    stage_1_emails: alert.stage_1_emails || "",
                    stage_2_emails: alert.stage_2_emails || "",
                    stage_3_emails: alert.stage_3_emails || "",
                    stage_1_conditions: alert.stage_1_conditions || "",
                    stage_2_conditions: alert.stage_2_conditions || "",
                    stage_3_conditions: alert.stage_3_conditions || "",
                    stage_1_values: alert.stage_1_values || "",
                    stage_2_values: alert.stage_2_values || "",
                    stage_3_values: alert.stage_3_values || "",
                };
                const modal = new bootstrap.Modal(document.getElementById('alertModal'));
                modal.show();
            },
            openEditModal(alert) {
                this.isEditMode = true;
                this.viewMode = false;
                this.errors = {};
                this.form = {
                    alert_id: alert.alert_id,
                    asset_type_id: alert.asset_type_id || "",
                    check: alert.check || "",
                    stage_1_emails: alert.stage_1_emails || "",
                    stage_2_emails: alert.stage_2_emails || "",
                    stage_3_emails: alert.stage_3_emails || "",
                    stage_1_conditions: alert.stage_1_conditions || "",
                    stage_2_conditions: alert.stage_2_conditions || "",
                    stage_3_conditions: alert.stage_3_conditions || "",
                    stage_1_values: alert.stage_1_values || "",
                    stage_2_values: alert.stage_2_values || "",
                    stage_3_values: alert.stage_3_values || "",
                };
                const modal = new bootstrap.Modal(document.getElementById('alertModal'));
                modal.show();
            },
            getAssetTypeName(asset_type_id) {
                if (!asset_type_id) return '';
                const assetType = this.asset_types.find(at => at.asset_type_id == asset_type_id);
                return assetType ? assetType.asset_type_name : '';
            },
            resetForm() {
                this.form = {
                    alert_id: null,
                    asset_type_id: "",
                    check: "",
                    stage_1_emails: "",
                    stage_2_emails: "",
                    stage_3_emails: "",
                    stage_1_conditions: "",
                    stage_2_conditions: "",
                    stage_3_conditions: "",
                    stage_1_values: "",
                    stage_2_values: "",
                    stage_3_values: "",
                };
            },
            submitForm() {
                this.errors = {};
                let loader = this.$loading.show();
                const uri = this.isEditMode ? "updateAlert" : "addAlert";
                
                this.$store
                    .dispatch("post", { uri: uri, data: this.form })
                    .then((response) => {
                        loader.hide();
                        this.$store.dispatch("success", response.data.message);
                        const modal = bootstrap.Modal.getInstance(document.getElementById('alertModal'));
                        if (modal) {
                            modal.hide();
                        }
                        this.resetForm();
                        this.index();
                    })
                    .catch((error) => {
                        loader.hide();
                        this.errors = error.response?.data?.errors || {};
                        this.$store.dispatch("error", error?.response?.data?.message || "Failed to save alert");
                    });
            },
            deleteAlert(alert) {
                if (confirm("Are you sure you want to delete this alert?")) {
                    let loader = this.$loading.show();
                    this.$store
                        .dispatch("post", { uri: "deleteAlert", data: { alert_id: alert.alert_id } })
                        .then((response) => {
                            loader.hide();
                            this.$store.dispatch("success", response.data.message || "Alert deleted successfully");
                            this.index();
                        })
                        .catch((error) => {
                            loader.hide();
                            this.$store.dispatch("error", error?.response?.data?.message || "Failed to delete alert");
                        });
                }
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

<style>
    .alert-modal-custom {
        max-width: 95% !important;
        width: 95% !important;
    }
    .alert-modal-custom .modal-content {
        max-height: 90vh;
        overflow-y: auto;
    }
</style>

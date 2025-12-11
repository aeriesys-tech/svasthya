<template>
    <div class="col-12 mb-2" v-can="'asset_template_Checks.view'">
        <div class="card card-one">
            <div class="card-header d-flex justify-content-between">
                <h6 class="card-title">Checks</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3 g-2" v-can="['asset_template_Checks.create','asset_template_Checks.update']">
                    <div class="col-md-4">
                        <label class="form-label">Template Zone Service <span class="text-danger"> *</span></label>
                        <MultiSelect
                            v-model="check.check_template_zones_obj"
                            filter
                            optionLabel="displayName"
                            :options="formattedTemplateServices"
                            placeholder="Select Service"
                            :maxSelectedLabels="3"
                            style="width: 100%; height: 37px;"
                            :class="{ 'is-invalid': errors?.asset_template_service_id }"
                            :customClass="{ 'is-invalid': errors?.asset_template_service_id }"
                            id="asset_template_service_id"
                            @input="templateservice1 => check.asset_template_service_id = templateservice1"
                            :disabled="check.check_zone_read_only"
                        />
                        <span v-if="errors?.asset_template_service_id" class="invalid-feedback" style="display: block !important;">
                            {{ errors?.asset_template_service_id[0] }}
                        </span>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Check <span class="text-danger"> *</span></label>
                        <search
                            :class="{ 'is-invalid': errors?.check_id }"
                            :customClass="{ 'is-invalid': errors?.check_id }"
                            aria-describedby="basic-addon2"
                            aria-label="Select Check"
                            class="my-auto"
                            :initialize="check.check_id"
                            id="check_id"
                            label="field_name"
                            placeholder="Select Check"
                            :data="checks"
                            @input=" check1 => check.check_id = check1"
                            @selectsearch="checkstatus()"
                        >
                        </search>
                        <span v-if="errors?.check_id" class="invalid-feedback">{{ errors?.check_id[0] }}</span>
                    </div>

                    <div class="col-md-12">
                        <div class="row align-items-center g-2">
                            <div class="col-md-3" v-if="check.field_type === 'Number'">
                                <label class="form-label">Lcl</label>
                                <input type="text" class="form-control" placeholder="Lcl" v-model="check.lcl" />
                            </div>
                            <div class="col-md-3" v-if="check.field_type === 'Number'">
                                <label class="form-label">Ucl</label>
                                <input type="text" class="form-control" placeholder="Ucl" v-model="check.ucl" />
                            </div>
                            <div class="col-md-3" v-if="check.field_type === 'Select'">
                                <label class="form-label">Field Value</label>
                                <input type="text" disabled="disabled" class="form-control" placeholder="Field Value" v-model="check.field_values" />
                            </div>
                            <div class="col-md-3" v-if="check.field_type === 'Select'">
                                <label class="form-label">Default Value</label>
                                <input :class="{ 'is-invalid': errors?.default_value }" type="text" class="form-control" placeholder="Default Value" v-model="check.default_value" />
                                <span v-if="errors?.default_value" class="invalid-feedback">{{ errors?.default_value[0] }}</span>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Order <span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" :class="{ 'is-invalid': errors?.order }" placeholder="Order" v-model="check.order" />
                                <span v-if="errors?.order" class="invalid-feedback" style="display: block !important;">
                                    {{ errors?.order[0] }}
                                </span>
                            </div>
                            <div class="col-md-3 pt-4 d-flex justify-content-end1" v-if="check.asset_template_check_id">
                                <button v-can="'asset_template_Checks.update'" class="btn btn-outline-success me-2" @click="updateCheck()"><i class="ri-add-circle-line icon-hgt"></i> Update</button>
                            </div>

                            <div class="col-md-3 pt-4 d-flex justify-content-end1" v-else>
                                <button v-can="'asset_template_Checks.create'" class="btn btn-outline-success me-2" @click="addCheck()"><i class="ri-add-circle-line icon-hgt"></i> Add</button>
                            </div>
                        </div>
                    </div>
                </div>
                <input class="form-control form-control-sm mb-2" type="text" placeholder="Type keyword and press enter key" v-model="check_meta.search" @keypress.enter="searchCheck()" />
                <div class="row g-2">
                    <div class="col-12">
                        <div class="table-responsive table-responsive-sm">
                            <table class="table table-sm text-nowrap table-striped table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" width="2%">#</th>
                                        <th>
                                            Template Zone
                                            <span>
                                                <i v-if="check_meta.keyword=='template_zone_id' && check_meta.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="check_meta.keyword=='template_zone_id' && check_meta.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Service
                                            <span>
                                                <i v-if="check_meta.keyword=='service_name' && check_meta.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="check_meta.keyword=='service_name' && check_meta.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th width="40%" style="white-space: normal;">
                                            Field Name
                                            <span>
                                                <i v-if="check_meta.keyword=='field_name' && check_meta.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="check_meta.keyword=='field_name' && check_meta.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>

                                        <th width="10%">
                                            Field Type
                                            <span>
                                                <i v-if="check_meta.keyword=='field_type' && check_meta.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="check_meta.keyword=='field_type' && check_meta.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>

                                        <th width="8%">
                                            Lcl
                                            <span>
                                                <i v-if="check_meta.keyword=='lcl' && check_meta.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="check_meta.keyword=='lcl' && check_meta.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th width="8%">
                                            Ucl
                                            <span>
                                                <i v-if="check_meta.keyword=='ucl' && check_meta.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="check_meta.keyword=='ucl' && check_meta.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th width="8%" style="white-space: normal;">
                                            Default Value
                                            <span>
                                                <i v-if="check_meta.keyword=='default_value' && check_meta.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="check_meta.keyword=='default_value' && check_meta.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>

                                        <th width="8%" style="white-space: normal;">
                                            Order
                                            <span>
                                                <i v-if="check_meta.keyword=='order' && check_meta.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="check_meta.keyword=='order' && check_meta.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>

                                        <th class="text-center" v-can="['asset_template_Checks.update','asset_template_Checks.delete']">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="asset_checks?.length==0">
                                        <td colspan="10" class="text-center">No records found</td>
                                    </tr>
                                    <tr v-for="check, key in asset_checks" :key="key">
                                        <td class="text-center">{{(check_meta.page-1)*check_meta.per_page + key+1 }}</td>
                                        <td>{{ check?.asset_zone?.zone_name }}</td>
                                        <td>{{check?.asset_template_service?.service_name}}</td>
                                        <td style="white-space: normal;">{{check?.check?.field_name}}</td>
                                        <td>{{ check?.check?.field_type }}</td>
                                        <td>{{ check.lcl }}</td>
                                        <td>{{ check.ucl }}</td>
                                        <td style="white-space: normal;">{{ check.default_value }}</td>
                                        <td>{{ check?.order }}</td>
                                        <td class="text-center" v-can="['asset_template_Checks.update','asset_template_Checks.delete']">
                                            <a href="javascript:void(0)" v-can="'asset_template_Checks.update'" class="text-success me-2" @click="editCheck(check)"><i class="ri-pencil-line fs-18 lh-1"></i></a>
                                            <a href="javascript:void(0)" v-can="'asset_template_Checks.delete'" class="text-danger me-2" @click="deleteCheck(check)"><i class="ri-delete-bin-6-line fs-18 lh-1"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <select class="form-select from-select-sm width-75" v-model="check_meta.per_page" @change="onPerPageChange('Checks')">
                        <option>5</option>
                        <option>10</option>
                        <option>15</option>
                        <option>20</option>
                        <option>25</option>
                        <option>30</option>
                    </select>
                    <span>Showing {{ check_meta.from }} to {{ check_meta.to }} of {{ check_meta.totalRows }} entries</span>
                    <Pagination :maxPage="check_meta.maxPage" :totalPages="check_meta.lastPage" :currentPage="check_meta.page" @pagechanged="onPageChange" :from="'Checks'" />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import axios from "axios";
    import Search from "@/components/Search.vue";
    import Pagination from "@/components/Pagination.vue";
    import MultiSelect from "primevue/multiselect";
    export default {
        name: "AssetTemplates.View",
        components: { Search, Pagination, MultiSelect },
        props: {
            asset_template_id: {
                type: Number,
                required: true,
            },
        },
        data() {
            return {
                check_meta: {
                    search: "",
                    order_by: "desc",
                    keyword: "check_id",
                    per_page: 10,
                    totalRows: 0,
                    page: 1,
                    lastPage: 1,
                    from: 1,
                    to: 1,
                    maxPage: 1,
                    trashed: false,
                    asset_template_id: "",
                    asset_type_id: "",
                    department_id: "",
                },

                asset: {
                    asset_template_id: "",
                    plant_id: "",
                    template_code: "",
                    template_name: "",
                    asset_type_id: "",
                    section_id: "",
                    serial_no: "",
                    status: "",
                    QR_Code: "",
                },
                check: {
                    check_id: "",
                    check_code: "",
                    check_name: "",
                    asset_template_id: "",
                    lcl: "",
                    ucl: "",
                    order: "",
                    default_value: "",
                    asset_template_check_id: "",
                    template_zone_id: "",
                    check_template_zones_obj: [],
                    check_template_zones: [],
                    check_zone_read_only: false,
                    template_check_status: false,
                    asset_template_service_id: "",
                },

                checks: [],
                asset_checks: [],

                template_services: [],
                template_spare_services: [],
                asset_zones: [],
                errors: [],
                status: true,
                asset_zone_status: false,
                asset_zone_status_checks: false,
                asset_type: "",
                // on add other api should not call

                isValid: true,
                assets1: [],
                error_style: {
                    "border-color": "#dc3545",
                    "padding-right": "calc(1.5em + 0.812rem)",
                    "background-image": `url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e")`,
                    "background-repeat": "no-repeat",
                    "background-position": "right calc(0.375em + 0.203rem) center",
                    "background-size": "calc(0.75em + 0.406rem) calc(0.75em + 0.406rem)",
                },
            };
        },
        watch: {
            assets1() {
                this.assets1 = this.$store.getters.asset_templates;
                // You can also add any additional logic here if needed
            },

            "check.check_id": function () {
                let vm = this;
                let check_temp = vm.checks.filter((element) => {
                    return element.check_id == vm.check.check_id;
                });
                console.log("vm.check.template_check_status", vm.check.template_check_status);
                if (check_temp.length && !vm.check.template_check_status) {
                    vm.check.lcl = check_temp[0].lcl;
                    vm.check.ucl = check_temp[0].ucl;
                    vm.check.order = check_temp[0].order;
                    vm.check.default_value = check_temp[0].default_value;
                    vm.check.field_type = check_temp[0]?.field_type;
                    vm.check.field_values = check_temp[0]?.field_values;
                }
                if (vm.check.template_check_status) {
                    vm.check.field_type = check_temp[0]?.field_type;
                    vm.check.field_values = check_temp[0]?.field_values;
                }
            },
        },
        computed: {
            formattedTemplateServices() {
                return this.template_services.map((service) => ({
                    ...service,
                    displayName: `${service.zone_name} - ${service.service_name}`,
                }));
            },
            formattedTemplateSpareServices() {
                return this.template_spare_services.map((service) => ({
                    ...service,
                    displayName: `${service.zone_name} - ${service.service_name}`,
                }));
            },
        },
        mounted() {
            document.body.classList.add("sidebar-hide");
            this.getAssetTemplate();
        },
        methods: {
            getAssetTemplate() {
                let vm = this;
                vm.$store
                    .dispatch("post", { uri: "getAssetTemplate", data: { asset_template_id: vm.asset_template_id } })
                    .then((response) => {
                        vm.asset = response.data.data;
                        vm.asset_zones = response?.data?.data?.zone_name;
                        vm.getAssetChecks();
                    })
                    .catch(function (error) {
                        vm.errors = error.response?.data?.errors;
                        vm.$store.dispatch("error", error.response?.data?.message);
                    });
            },
            getAssetChecks() {
                let vm = this;
                let loader = vm.$loading.show();
                vm.check_meta.asset_template_id = vm.asset.asset_template_id;
                vm.check_meta.asset_type_id = vm.asset.asset_type_id;
                vm.check_meta.department_id = vm.asset.department_id;
                vm.$store
                    .dispatch("post", { uri: "paginateAssetTemplateChecks", data: vm.check_meta })
                    .then((response) => {
                        loader.hide();
                        vm.checks = response.data.checks;
                        vm.asset_checks = response.data.paginate_checks;
                        vm.template_services = response.data.template_services;
                        vm.check_meta.totalRows = response.data.meta.total;
                        vm.check_meta.from = response.data.meta.from;
                        vm.check_meta.to = response.data.meta.to;
                        vm.check_meta.lastPage = response.data.meta.last_page;
                        vm.check_meta.maxPage = vm.check_meta.lastPage >= 3 ? 3 : vm.check_meta.lastPage;
                    })
                    .catch(function (error) {
                        loader.hide();
                        // console.log("Check Error--", error);
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },

            editCheck(check) {
                console.log("check---", check);
                this.check.template_check_status = true;
                this.check.asset_template_id = check.asset_template_id;
                this.check.asset_template_check_id = check.asset_template_check_id;
                this.check.check_id = check.check_id;
                this.check.lcl = check.lcl;
                this.check.ucl = check.ucl;
                this.check.order = check.order;
                this.check.default_value = check.default_value;

                this.check.check_template_zones = [check.template_zone_id];
                this.check.template_zone_id = check.template_zone_id;

                const selectedService = this.formattedTemplateServices.find((service) => service.asset_template_service_id === check.asset_template_service_id);

                if (selectedService) {
                    this.check.check_template_zones_obj = [selectedService];
                    this.check.asset_template_service_id = selectedService.asset_template_service_id;
                } else {
                    this.check.check_template_zones_obj = [];
                }

                this.check.check_zone_read_only = true;
            },

            checkstatus() {
                this.check.template_check_status = false;
            },
            addCheck() {
                let vm = this;
                vm.check.asset_template_id = vm.asset.asset_template_id;
                vm.check.check_template_zones = [];
                vm.check.check_template_zones_obj.map(function (ele) {
                    vm.check.check_template_zones.push(ele.asset_template_service_id);
                });
                let loader = vm.$loading.show();
                vm.$store
                    .dispatch("post", { uri: "addAssetTemplateCheck", data: vm.check })
                    .then((response) => {
                        loader.hide();
                        vm.$store.dispatch("success", response.data.message);
                        vm.check.check_id = "";
                        vm.check.lcl = "";
                        vm.check.ucl = "";
                        vm.check.order = "";
                        vm.check.default_value = "";
                        vm.check.field_type = "";
                        vm.errors = [];
                        vm.check.template_zone_id = [];
                        vm.check.check_template_zones_obj = [];
                        vm.check.check_template_zones = [];
                        vm.asset_zone_status_checks = false;
                        vm.check.check_zone_read_only = false;
                        vm.getAssetChecks();
                    })
                    .catch(function (error) {
                        console.log("Error", error);
                        loader.hide();
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },
            updateCheck() {
                let vm = this;
                let loader = vm.$loading.show();
                let uri = { uri: "updateAssetTemplateCheck", data: vm.check };
                vm.$store
                    .dispatch("post", uri)
                    .then(function (response) {
                        loader.hide();
                        vm.$store.dispatch("success", response.data.message);
                        vm.check.template_check_status = false;
                        vm.check.check_id = "";
                        vm.check.lcl = "";
                        vm.check.ucl = "";
                        vm.check.order = "";
                        vm.check.default_value = "";
                        vm.check.field_type = "";
                        vm.check.asset_template_check_id = "";
                        vm.check.asset_template_service_id = "";
                        vm.check.check_template_zones_obj = [];
                        vm.check.check_template_zones = [];
                        vm.asset_zone_status_checks = false;
                        vm.check.check_zone_read_only = false;
                        vm.errors = [];
                        vm.getAssetChecks();
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },

            deleteCheck(check) {
                const confirmDelete = confirm("Are you sure you want to delete it ?");
                if (confirmDelete) {
                    let vm = this;
                    let loader = vm.$loading.show();
                    vm.$store
                        .dispatch("post", { uri: "forceDeleteAssetTemplateCheck", data: check })
                        .then((response) => {
                            loader.hide();
                            vm.$store.dispatch("success", response.data.message);
                            vm.check.check_id = "";
                            vm.errors = [];
                            vm.getAssetChecks();
                        })
                        .catch(function (error) {
                            loader.hide();
                            vm.$store.dispatch("error", error.response.data.message);
                        });
                }
            },
            searchCheck() {
                let vm = this;
                vm.check_meta.page = 1;
                vm.getAssetChecks();
            },

            onPageChange(page, from) {
                if (from == "Checks") {
                    this.check_meta.page = page;
                    this.getAssetChecks();
                }
            },
            sort(field, from) {
                if (from == "Checks") {
                    this.check_meta.keyword = field;
                    this.check_meta.order_by = this.check_meta.order_by == "asc" ? "desc" : "asc";
                    this.getAssetChecks();
                }
            },
            onPerPageChange(from) {
                if (from == "Checks") {
                    this.check_meta.page = 1;
                    this.getAssetChecks();
                }
            },
        },
    };
</script>
<style scoped>
.card-one {
    border-top-right-radius: 1px;
    border-top-left-radius: 1px;
}
</style>
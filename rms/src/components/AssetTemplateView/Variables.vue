<template>
    <div class="col-12 mb-2" v-can="'asset_template_Variables.view'">
        <div class="card card-one">
            <div class="card-header d-flex justify-content-between">
                <h6 class="card-title">Variables</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3 g-2" v-can="['asset_template_Variables.create','asset_template_Variables.update']">
                    <div class="col-md-4">
                        <label class="form-label">Template Zone <span class="text-danger"> *</span></label>
                        <MultiSelect
                            v-model="variable.variable_template_zones_obj"
                            filter
                            optionLabel="zone_name"
                            :options="asset_zones"
                            placeholder="Select Template Zone"
                            :maxSelectedLabels="3"
                            style="width: 100%; height: 37px;"
                            :style="errors?.variable_template_zones ? error_style : ''"
                            :disabled="variable.variable_zone_read_only"
                        />
                        <span v-if="errors?.variable_template_zones" class="invalid-feedback" style="display: block !important;">{{ errors?.variable_template_zones[0] }}</span>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Variable <span class="text-danger"> *</span></label>
                        <search
                            :class="{ 'is-invalid': errors?.variable_id }"
                            :customClass="{ 'is-invalid': errors?.variable_id }"
                            aria-describedby="basic-addon2"
                            aria-label="Select Variable"
                            class="my-auto"
                            :initialize="variable.variable_id"
                            id="variable_id"
                            label="variable_name"
                            label2="variable_code"
                            placeholder="Select Variable"
                            :data="variables"
                            @input=" variable1 => variable.variable_id = variable1"
                            @selectsearch="getVariableAttribute(variable.variable_id)"
                        >
                        </search>
                        <span v-if="errors?.variable_id" class="invalid-feedback">{{ errors?.variable_id[0] }}</span>
                    </div>

                    <div class="col-md-3" v-for="field, key in variable.asset_variable_attributes" :key="key">
                        <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type=='Text'">
                            <label class="form-label">{{field.variable_attributes[0].display_name}}</label><span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                            <input type="text" class="form-control" :placeholder="'Enter '+ field.variable_attributes[0].display_name" :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}" v-model="field.field_value" />
                            <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.variable_attributes[0].display_name][0] }}</span>
                        </div>

                        <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type=='Number'">
                            <label class="form-label">{{field.variable_attributes[0].display_name}}</label><span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                            <input
                                type="number"
                                class="form-control"
                                min="0"
                                oninput="validity.valid||(value='');"
                                :placeholder="'Enter '+ field.variable_attributes[0].display_name"
                                :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}"
                                v-model="field.field_value"
                            />
                            <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.variable_attributes[0].display_name][0] }}</span>
                        </div>

                        <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type === 'Date'">
                            <label class="form-label">
                                {{ field.variable_attributes[0].display_name }}
                                <span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                            </label>
                            <input
                                type="date"
                                class="form-control"
                                :placeholder="'Enter ' + field.variable_attributes[0].display_name"
                                :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}"
                                v-model="field.field_value"
                            />
                            <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">
                                {{ errors[field.variable_attributes[0].display_name][0] }}
                            </span>
                        </div>

                        <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type === 'Date&Time'">
                            <label class="form-label">
                                {{ field.variable_attributes[0].display_name }}
                                <span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                            </label>

                            <input
                                type="datetime-local"
                                class="form-control"
                                :placeholder="'Enter ' + field.variable_attributes[0].display_name"
                                :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}"
                                v-model="field.field_value"
                                step="1"
                            />
                            <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">
                                {{ errors[field.variable_attributes[0].display_name][0] }}
                            </span>
                        </div>
                        <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type=='Dropdown'">
                            <label class="form-label">{{field.variable_attributes[0].display_name}}</label><span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                            <select class="form-control" :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}" v-model="field.field_value">
                                <option :value="field.field_value" v-if="field.field_value">{{field.field_value}}</option>
                                <option :value="field.field_value" v-else>Select {{field.variable_attributes[0].display_name}}</option>
                                <option v-for="value, key in field.variable_attributes[0].field_values.split(',')" :key="key" :value="value">{{value}}</option>
                            </select>
                            <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.variable_attributes[0].display_name][0] }}</span>
                        </div>
                        <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type=='Color'">
                            <label class="form-label">{{ field.variable_attributes[0].display_name }}<span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span></label>
                            <input type="color" class="form-control" v-model="field.field_value" style="height: 2.2rem;" />
                            <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.variable_attributes[0].display_name][0] }}</span>
                        </div>

                        <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type=='List'">
                            <label class="form-label">{{field.variable_attributes[0].display_name}}</label><span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                            <select class="form-control" :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}" v-model="field.field_value">
                                <option :value="field.field_value" v-if="field.field_value">{{field.field_value}}</option>
                                <option :value="field.variable_attributes[0].variable_attribute_value?.field_value" v-if="field.variable_attributes[0].variable_attribute_value?.field_value">
                                    {{field.variable_attributes[0].variable_attribute_value?.field_value}}
                                </option>
                                <option :value="field.variable_attributes[0].variable_attribute_value?.field_value" v-else>Select {{field.variable_attributes[0].display_name}}</option>
                                <option v-for="value, key in field.variable_attributes[0].list_parameter?.field_values.split(',')" :key="key" :value="value.trim()">{{value.trim()}}</option>
                            </select>
                            <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.variable_attributes[0].display_name][0] }}</span>
                        </div>
                    </div>

                    <div class="col-md-2 pt-4" v-if="variable.asset_template_variable_id">
                        <button v-can="'asset_template_Variables.update'" class="btn btn-outline-success me-2" @click="updateVariable()"><i class="ri-add-circle-line icon-hgt"></i> Update</button>
                    </div>
                    <div class="col-md-2 pt-4" v-else>
                        <button v-can="'asset_template_Variables.create'" class="btn btn-outline-success me-2" @click="addVariable()"><i class="ri-add-circle-line icon-hgt"></i> Add</button>
                    </div>
                </div>
                <input class="form-control form-control-sm mb-2" type="text" placeholder="Type keyword and press enter key" v-model="variable_meta_service.search" @keypress.enter="searchVariable()" />
                <div class="row g-2">
                    <div class="col-12">
                        <div class="table-responsive table-responsive-sm">
                            <table class="table table-sm text-nowrap table-striped table-bordered mb-0">
                                <thead>
                                    <tr class="">
                                        <th class="text-center">#</th>
                                        <th>
                                            Template Zone
                                            <span>
                                                <i v-if="variable_meta_service.keyword=='template_zone_id' && variable_meta_service.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="variable_meta_service.keyword=='template_zone_id' && variable_meta_service.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Variable Type
                                            <span>
                                                <i v-if="variable_meta_service.keyword=='variable_type_id' && variable_meta_service.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="variable_meta_service.keyword=='variable_type_id' && variable_meta_service.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Variable Code
                                            <span>
                                                <i v-if="variable_meta_service.keyword=='variable_code' && variable_meta_service.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="variable_meta_service.keyword=='variable_code' && variable_meta_service.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Variable Name
                                            <span>
                                                <i v-if="variable_meta_service.keyword=='variable_name' && variable_meta_service.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="variable_meta_service.keyword=='variable_name' && variable_meta_service.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="asset_variables?.length==0">
                                        <td colspan="6" class="text-center">No records found</td>
                                    </tr>
                                    <tr v-for="variable, key in asset_variables" :key="key">
                                        <td class="text-center">{{(variable_meta_service.page-1)*variable_meta_service.per_page + key+1 }}</td>
                                        <td>{{ variable.asset_zone?.zone_name }}</td>
                                        <td>{{variable.variable?.variable_type?.variable_type_name}}</td>
                                        <td>{{variable.variable?.variable_code}}</td>
                                        <td>{{variable.variable?.variable_name}}</td>
                                        <td class="text-center">
                                            <a v-can="'asset_template_Variables.update'" href="javascript:void(0)" class="text-success me-2" @click="editVariable(variable)"><i class="ri-pencil-line fs-18 lh-1"></i></a>
                                            <a href="javascript:void(0)" class="text-primary me-2" data-bs-toggle="modal" data-bs-target="#exampleModal" @click.prevent="viewAsset(variable, 'Variables')">
                                                <i class="ri-eye-fill fs-18 lh-1"></i>
                                            </a>
                                            <a v-can="'asset_template_Variables.delete'" href="javascript:void(0)" class="text-danger me-2" @click="deleteVariable(variable)">
                                                <i class="ri-delete-bin-6-line fs-18 lh-1"></i>
                                            </a>
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
                    <select class="form-select from-select-sm width-75" v-model="variable_meta_service.per_page" @change="onPerPageChange('Variables')">
                        <option>5</option>
                        <option>10</option>
                        <option>15</option>
                        <option>20</option>
                        <option>25</option>
                        <option>30</option>
                    </select>
                    <span>Showing {{ variable_meta_service.from }} to {{ variable_meta_service.to }} of {{ variable_meta_service.totalRows }} entries</span>
                    <Pagination :maxPage="variable_meta_service.maxPage" :totalPages="variable_meta_service.lastPage" :currentPage="variable_meta_service.page" @pagechanged="onPageChange" :from="'Variables'" />
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{asset_type}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2">
                        <div class="col-12 mb-2">
                            <div class="row g-2" v-if="asset_type=='Variables'">
                                <div class="col-md-4" v-for="field, key in assets1?.asset_variable_attributes" :key="key">
                                    <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type=='Text'">
                                        <label class="form-label">{{field.variable_attributes[0].display_name}}</label><span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            :placeholder="'Enter '+ field.variable_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.variable_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type=='Number'">
                                        <label class="form-label">{{field.variable_attributes[0].display_name}}</label><span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                                        <input
                                            type="number"
                                            class="form-control"
                                            min="0"
                                            oninput="validity.valid||(value='');"
                                            :placeholder="'Enter '+ field.variable_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.variable_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type === 'Date'">
                                        <label class="form-label">
                                            {{ field.variable_attributes[0].display_name }}
                                            <span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="date"
                                            class="form-control"
                                            :placeholder="'Enter ' + field.variable_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">
                                            {{ errors[field.variable_attributes[0].display_name][0] }}
                                        </span>
                                    </div>

                                    <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type === 'Date&Time'">
                                        <label class="form-label">
                                            {{ field.variable_attributes[0].display_name }}
                                            <span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                                        </label>

                                        <input
                                            type="datetime-local"
                                            class="form-control"
                                            :placeholder="'Enter ' + field.variable_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            step="1"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">
                                            {{ errors[field.variable_attributes[0].display_name][0] }}
                                        </span>
                                    </div>
                                    <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type=='Dropdown'">
                                        <label class="form-label">{{field.variable_attributes[0].display_name}}</label><span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                                        <select class="form-control" :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}" :value="field.field_value" disabled="true">
                                            <option :value="field.field_value" v-if="field.field_value">{{field.field_value}}</option>
                                            <option :value="field.field_value" v-else>Select {{field.variable_attributes[0].display_name}}</option>
                                            <option v-for="value, key in field.variable_attributes[0].field_values.split(',')" :key="key" :value="value">{{value}}</option>
                                        </select>
                                        <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.variable_attributes[0].display_name][0] }}</span>
                                    </div>
                                    <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type=='Color'">
                                        <label class="form-label">{{ field.variable_attributes[0].display_name }}<span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span></label>
                                        <input type="color" class="form-control" :value="field.field_value" style="height: 2.2rem;" disabled="true" />
                                        <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.variable_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.variable_attributes.length && field.variable_attributes[0].field_type=='List'">
                                        <label class="form-label">{{field.variable_attributes[0].display_name}}</label><span v-if="field.variable_attributes[0].is_required" class="text-danger">*</span>
                                        <select class="form-control" :class="{'is-invalid': errors[field.variable_attributes[0].display_name]}" :value="field.field_value" disabled="true">
                                            <option :value="field.variable_attributes[0].variable_attribute_value?.field_value" v-if="field.variable_attributes[0].variable_attribute_value?.field_value">
                                                {{field.variable_attributes[0].variable_attribute_value?.field_value}}
                                            </option>
                                            <option :value="field.variable_attributes[0].variable_attribute_value?.field_value" v-else>Select {{field.variable_attributes[0].display_name}}</option>
                                            <option v-for="value, key in field.variable_attributes[0].list_parameter?.field_values.split(',')" :key="key" :value="value.trim()">{{value.trim()}}</option>
                                        </select>
                                        <span v-if="errors[field.variable_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.variable_attributes[0].display_name][0] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
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
                variable_meta_service: {
                    search: "",
                    order_by: "desc",
                    keyword: "variable_id",
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
                variable: {
                    variable_id: "",
                    variable_code: "",
                    variable_name: "",
                    asset_template_id: "",
                    template_zone_id: "",
                    variable_template_zones_obj: [],
                    variable_template_zones: [],
                    asset_template_variable_id: "",
                    asset_variable_attributes: [],
                    initial_variable_id: "",
                    initial_asset_variable_attributes: [],
                    deleted_asset_variable_values: [],
                    variable_zone_read_only: false,
                },

                asset_spares: [],
                checks: [],
                asset_variables: [],
                template_services: [],
                template_spare_services: [],
                asset_zones: [],
                errors: [],
                status: true,
                asset_zone_status: false,

                asset_zone_status_variables: false,
                asset_type: "",
                // on add other api should not call
                initial_status: true,
                isValid: true,
                assets1: [],
                filter_check: {},
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
                        vm.getAssetVariables();
                    })
                    .catch(function (error) {
                        vm.errors = error.response?.data?.errors;
                        vm.$store.dispatch("error", error.response?.data?.message);
                    });
            },
            getAssetVariables() {
                let vm = this;
                let loader = vm.$loading.show();
                vm.variable_meta_service.asset_template_id = vm.asset.asset_template_id;
                vm.variable_meta_service.asset_type_id = vm.asset.asset_type_id;
                vm.$store
                    .dispatch("post", { uri: "paginateAssetTemplateVariables", data: vm.variable_meta_service })
                    .then((response) => {
                        loader.hide();
                        vm.variables = response.data.variables;
                        vm.asset_variables = response.data.paginate_variables;
                        vm.variable_meta_service.totalRows = response.data.meta.total;
                        vm.variable_meta_service.from = response.data.meta.from;
                        vm.variable_meta_service.to = response.data.meta.to;
                        vm.variable_meta_service.lastPage = response.data.meta.last_page;
                        vm.variable_meta_service.maxPage = vm.variable_meta_service.lastPage >= 3 ? 3 : vm.variable_meta_service.lastPage;
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },
            editVariable(variable) {
                this.variable.variable_id = variable.variable_id;
                this.variable.asset_template_id = variable.asset_template_id;
                this.variable.asset_template_variable_id = variable.asset_template_variable_id;
                this.variable.variable_code = variable.variable_code;
                this.variable.variable_name = variable.variable_name;
                this.variable.variable_template_zones = [];
                this.variable.variable_template_zones.push(variable.template_zone_id);
                this.variable.template_zone_id = variable.template_zone_id;
                this.variable.asset_variable_attributes = variable.asset_variable_attributes;

                this.variable.initial_variable_id = variable.variable_id;
                this.variable.initial_asset_variable_attributes = variable.asset_variable_attributes;

                // this.variable.variable_template_zones_obj = [];
                // this.variable.variable_template_zones_obj.push({
                //     asset_template_id: variable.asset_zone.asset_template_id,
                //     template_zone_id: variable.asset_zone.template_zone_id,
                //     zone_name: variable.asset_zone.zone_name,
                //     diameter: variable.asset_zone.diameter,
                //     height: variable.asset_zone.height,
                //     asset_spares: variable.asset_zone.asset_spares,
                // });
                const matchingZone = this.asset_zones.find(zone => zone.template_zone_id === variable.template_zone_id);
                this.variable.variable_template_zones_obj = matchingZone ? [matchingZone] : [{
                    asset_template_id: variable.asset_zone.asset_template_id,
                    template_zone_id: variable.asset_zone.template_zone_id,
                    zone_name: variable.asset_zone.zone_name,
                    diameter: variable.asset_zone.diameter,
                    height: variable.asset_zone.height,
                    asset_spares: variable.asset_zone.asset_spares,
                }];
                this.variable.variable_zone_read_only = true;
            },

            validateFields(validation_type) {
                this.isValid = true;
                this.errors = {};
                if (validation_type == "Variables") {
                    for (const field of Object.values(this.variable.asset_variable_attributes)) {
                        if (field.variable_attributes.length && field.variable_attributes[0].is_required && !field.field_value) {
                            if (field.variable_attributes[0].field_type === "Color") {
                                // Set default color if not provided
                                field.field_value = "#000000"; // Default to black
                            } else {
                                this.errors[field.variable_attributes[0].display_name] = [`${field.variable_attributes[0].display_name} is required`];
                                this.isValid = false;
                            }
                        }
                    }
                }
            },
            addVariable() {
                let vm = this;
                vm.variable.asset_template_id = vm.asset.asset_template_id;
                vm.variable.variable_template_zones = [];
                vm.variable.variable_template_zones_obj.map(function (ele) {
                    vm.variable.variable_template_zones.push(ele.template_zone_id);
                });
                let loader = vm.$loading.show();
                vm.validateFields("Variables");
                if (!vm.isValid) {
                    loader.hide();
                    return;
                }
                vm.$store
                    .dispatch("post", { uri: "addAssetTemplateVariable", data: vm.variable })
                    .then((response) => {
                        loader.hide();
                        vm.$store.dispatch("success", response.data.message);
                        vm.variable.variable_id = "";
                        vm.variable.template_zone_id = "";
                        vm.variable.variable_template_zones = [];
                        vm.variable.asset_variable_attributes = [];

                        vm.variable.initial_variable_id = "";
                        vm.variable.initial_asset_variable_attributes = [];
                        vm.variable.deleted_asset_variable_values = [];

                        vm.asset_zone_status_variables = false;

                        vm.variable.variable_template_zones_obj = [];
                        vm.variable.variable_zone_read_only = false;

                        vm.errors = [];
                        vm.getAssetVariables();
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },
            updateVariable() {
                let vm = this;
                let loader = vm.$loading.show();
                vm.validateFields("Variables");
                if (!vm.isValid) {
                    loader.hide();
                    return;
                }
                if (vm.variable.variable_id != vm.variable.initial_variable_id) {
                    vm.variable.initial_asset_variable_attributes.map(function (ele) {
                        vm.variable.deleted_asset_variable_values.push(ele.template_variable_value_id);
                    });
                }

                let uri = { uri: "updateAssetTemplateVariable", data: vm.variable };
                vm.$store
                    .dispatch("post", uri)
                    .then(function (response) {
                        loader.hide();
                        vm.$store.dispatch("success", response.data.message);
                        vm.variable.variable_id = "";
                        vm.variable.template_zone_id = "";
                        vm.variable.asset_template_variable_id = "";
                        vm.variable.variable_template_zones = [];
                        vm.variable.asset_variable_attributes = [];
                        vm.variable.initial_variable_id = "";
                        vm.variable.initial_asset_variable_attributes = [];
                        vm.variable.deleted_asset_variable_values = [];
                        vm.asset_zone_status_variables = false;

                        vm.variable.variable_template_zones_obj = [];
                        vm.variable.variable_zone_read_only = false;

                        vm.errors = [];
                        vm.getAssetVariables();
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response?.data?.errors;
                        vm.$store.dispatch("error", error?.response?.data?.message);
                    });
            },
            deleteVariable(variable) {
                const confirmDelete = confirm("Are you sure you want to delete it ?");
                if (confirmDelete) {
                    let vm = this;
                    let loader = vm.$loading.show();
                    vm.$store
                        .dispatch("post", { uri: "deleteAssetTemplateVariable", data: variable })
                        .then((response) => {
                            loader.hide();
                            vm.$store.dispatch("success", response.data.message);
                            vm.variable.variable_id = "";
                            vm.errors = [];
                            vm.getAssetVariables();
                        })
                        .catch(function (error) {
                            loader.hide();
                            vm.errors = error.response.data.errors;
                            vm.$store.dispatch("error", error.response.data.message);
                        });
                }
            },

            searchVariable() {
                let vm = this;
                vm.variable_meta_service.page = 1;
                vm.getAssetVariables();
            },
            onPageChange(page, from) {
                if (from == "Variables") {
                    this.variable_meta_service.page = page;
                    this.getAssetVariables();
                }
            },
            sort(field, from) {
                if (from == "Variables") {
                    this.variable_meta_service.keyword = field;
                    this.variable_meta_service.order_by = this.variable_meta_service.order_by == "asc" ? "desc" : "asc";
                    this.getAssetVariables();
                }
            },
            onPerPageChange(from) {
                if (from == "Variables") {
                    this.variable_meta_service.page = 1;
                    this.getAssetVariables();
                }
            },
            getVariableAttribute(variable_id) {
                let vm = this;
                let loader = vm.$loading.show();
                vm.variable.asset_variable_attributes = [];
                vm.$store
                    .dispatch("post", { uri: "assetVariableAttributeValues", data: { variable_id: variable_id } })
                    .then((response) => {
                        loader.hide();
                        vm.variable.asset_variable_attributes = response.data.data;
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },
            viewAsset(asset, from) {
                this.$store.commit("setAssetTemplates", asset);
                this.asset_type = from;
                this.assets1 = this.$store.getters.asset_templates;
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
<template>
    <div class="col-12 mb-2" v-can="'assetServices.view'">
        <div class="card card-one">
            <div class="card-header d-flex justify-content-between">
                <h6 class="card-title">Services</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3 g-2" v-can="['assetServices.create','assetServices.update']">
                    <div class="col-md-4">
                        <label class="form-label">Asset Zone <span class="text-danger"> *</span></label>
                        <MultiSelect
                            v-model="service.service_asset_zones_obj"
                            filter
                            optionLabel="zone_name"
                            :options="asset_zones"
                            placeholder="Select Asset Zone"
                            :maxSelectedLabels="3"
                            style="width: 100%; height: 37px;"
                            :style="errors?.service_asset_zones ? error_style : ''"
                            :disabled="service.service_zone_read_only"
                        />
                        <span v-if="errors?.service_asset_zones" class="invalid-feedback" style="display: block !important;">{{ errors?.service_asset_zones[0] }}</span>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Service <span class="text-danger"> *</span></label>
                        <search
                            :class="{ 'is-invalid': errors?.service_id }"
                            :customClass="{ 'is-invalid': errors?.service_id }"
                            aria-describedby="basic-addon2"
                            aria-label="Select Check"
                            class="my-auto"
                            :initialize="service.service_id"
                            id="service_id"
                            label="service_name"
                            placeholder="Select Service"
                            :data="services"
                            @input=" service1 => service.service_id = service1"
                            @selectsearch="getServiceAttribute(service.service_id)"
                        >
                        </search>
                        <span v-if="errors?.service_id" class="invalid-feedback">{{ errors?.service_id[0] }}</span>
                    </div>

                    <div class="col-md-3" v-for="field, key in service.asset_service_attributes" :key="key">
                        <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'Text'">
                            <label class="form-label">{{field?.service_attributes[0]?.display_name}}</label><span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                            <input type="text" class="form-control" :placeholder="'Enter '+ field?.service_attributes[0]?.display_name" :class="{'is-invalid': errors[field?.service_attributes[0]?.display_name]}" v-model="field.field_value" />
                            <span v-if="errors[field?.service_attributes[0]?.display_name]" class="invalid-feedback">{{ errors[field?.service_attributes[0]?.display_name][0] }}</span>
                        </div>

                        <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'Number'">
                            <label class="form-label">{{field?.service_attributes[0]?.display_name}}</label><span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                            <input
                                type="number"
                                class="form-control"
                                min="0"
                                oninput="validity.valid||(value='');"
                                :placeholder="'Enter '+ field?.service_attributes[0]?.display_name"
                                :class="{'is-invalid': errors[field?.service_attributes[0]?.display_name]}"
                                v-model="field.field_value"
                            />
                            <span v-if="errors[field?.service_attributes[0]?.display_name]" class="invalid-feedback">{{ errors[field?.service_attributes[0]?.display_name][0] }}</span>
                        </div>

                        <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'Date'">
                            <label class="form-label">
                                {{ field?.service_attributes[0]?.display_name }}
                                <span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" :placeholder="'Enter ' + field?.service_attributes[0]?.display_name" :class="{'is-invalid': errors[field?.service_attributes[0]?.display_name]}" v-model="field.field_value" />
                            <span v-if="errors[field?.service_attributes[0]?.display_name]" class="invalid-feedback">
                                {{ errors[field?.service_attributes[0]?.display_name][0] }}
                            </span>
                        </div>

                        <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'Date&Time'">
                            <label class="form-label">
                                {{ field?.service_attributes[0]?.display_name }}
                                <span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                            </label>

                            <input
                                type="datetime-local"
                                class="form-control"
                                :placeholder="'Enter ' + field?.service_attributes[0]?.display_name"
                                :class="{'is-invalid': errors[field?.service_attributes[0]?.display_name]}"
                                v-model="field.field_value"
                                step="1"
                            />
                            <span v-if="errors[field?.service_attributes[0]?.display_name]" class="invalid-feedback">
                                {{ errors[field?.service_attributes[0]?.display_name][0] }}
                            </span>
                        </div>
                        <div v-if="field.service_attributes?.length && field.service_attributes[0]?.field_type === 'Dropdown'">
                            <label class="form-label">{{field?.service_attributes[0]?.display_name}}</label><span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                            <select class="form-control" :class="{'is-invalid': errors[field?.service_attributes[0]?.display_name]}" v-model="field.field_value">
                                <option :value="field.field_value" v-if="field.field_value">{{field.field_value}}</option>
                                <option :value="field.field_value" v-else>Select {{field?.service_attributes[0]?.display_name}}</option>
                                <option v-for="value, key in field.service_attributes[0].field_values.split(',')" :key="key" :value="value">{{value}}</option>
                            </select>
                            <span v-if="errors[field?.service_attributes[0]?.display_name]" class="invalid-feedback">{{ errors[field?.service_attributes[0]?.display_name][0] }}</span>
                        </div>
                        <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'Color'">
                            <label class="form-label">{{ field?.service_attributes[0]?.display_name }}<span v-if="field.service_attributes[0].is_required" class="text-danger">*</span></label>
                            <input type="color" class="form-control" v-model="field.field_value" style="height: 2.2rem;" />
                            <span v-if="errors[field?.service_attributes[0]?.display_name]" class="invalid-feedback">{{ errors[field?.service_attributes[0]?.display_name][0] }}</span>
                        </div>

                        <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'List'">
                            <label class="form-label">{{field?.service_attributes[0]?.display_name}}</label><span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                            <select class="form-control" :class="{'is-invalid': errors[field?.service_attributes[0]?.display_name]}" v-model="field.field_value">
                                <option :value="field.service_attributes[0].service_attribute_value?.field_value" v-if="field.service_attributes[0]?.service_attribute_value?.field_value">
                                    {{field.service_attributes[0].service_attribute_value?.field_value}}
                                </option>
                                <option :value="field.service_attributes[0].service_attribute_value?.field_value" v-else>Select {{field.service_attributes[0].display_name}}</option>
                                <option v-for="value, key in field.service_attributes[0].list_parameter?.field_values.split(',')" :key="key" :value="value.trim()">{{value.trim()}}</option>
                            </select>
                            <span v-if="errors[field?.service_attributes[0]?.display_name]" class="invalid-feedback">{{ errors[field?.service_attributes[0]?.display_name][0] }}</span>
                        </div>
                    </div>

                    <div class="col-md-2 pt-4" v-if="service.asset_service_id">
                        <button v-can="'assetServices.update'" class="btn btn-outline-success me-2" @click="updateService()"><i class="ri-add-circle-line icon-hgt"></i> Update</button>
                    </div>
                    <div class="col-md-2 pt-4" v-else>
                        <button v-can="'assetServices.create'" class="btn btn-outline-success me-2" @click="addService()"><i class="ri-add-circle-line icon-hgt"></i> Add</button>
                    </div>
                </div>
                <input class="form-control form-control-sm mb-2" type="text" placeholder="Type keyword and press enter key" v-model="check_meta_service.search" @keypress.enter="searchService()" />
                <div class="row g-2">
                    <div class="col-12">
                        <div class="table-responsive table-responsive-sm">
                            <table class="table table-sm text-nowrap table-striped table-bordered mb-0">
                                <thead>
                                    <tr class="">
                                        <th class="text-center">#</th>
                                        <th>
                                            Asset Zone
                                            <span>
                                                <i v-if="check_meta_service.keyword=='asset_zone_id' && check_meta_service.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="check_meta_service.keyword=='asset_zone_id' && check_meta_service.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Service Type
                                            <span>
                                                <i v-if="check_meta_service.keyword=='service_type_id' && check_meta_service.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="check_meta_service.keyword=='service_type_id' && check_meta_service.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Service Name
                                            <span>
                                                <i v-if="check_meta_service.keyword=='service_name' && check_meta_service.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="check_meta_service.keyword=='service_name' && check_meta_service.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Service Code
                                            <span>
                                                <i v-if="check_meta_service.keyword=='service_code' && check_meta_service.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="check_meta_service.keyword=='service_code' && check_meta_service.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="asset_services?.length==0">
                                        <td colspan="6" class="text-center">No records found</td>
                                    </tr>
                                    <tr v-for="service, key in asset_services" :key="key">
                                        <td class="text-center">{{(check_meta_service.page-1)*check_meta_service.per_page + key+1 }}</td>
                                        <td>{{ service?.asset_zone?.zone_name }}</td>
                                        <td>{{ service?.service?.service_type?.service_type_name }}</td>
                                        <td>{{ service?.service?.service_name }}</td>
                                        <td>{{ service?.service?.service_code }}</td>
                                        <td class="text-center">
                                            <a v-can="'assetServices.update'" href="javascript:void(0)" class="text-success me-2" @click="editService(service)"><i class="ri-pencil-line fs-18 lh-1"></i></a>
                                            <a href="javascript:void(0)" class="text-primary me-2" data-bs-toggle="modal" data-bs-target="#exampleModal" @click.prevent="viewAsset(service, 'Services')">
                                                <i class="ri-eye-fill fs-18 lh-1"></i>
                                            </a>
                                            <a v-can="'assetServices.delete'" href="javascript:void(0)" class="text-danger me-2" @click="deleteService(service)"><i class="ri-delete-bin-6-line fs-18 lh-1"></i></a>
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
                    <select class="form-select from-select-sm width-75" v-model="check_meta_service.per_page" @change="onPerPageChange('Services')">
                        <option>5</option>
                        <option>10</option>
                        <option>15</option>
                        <option>20</option>
                        <option>25</option>
                        <option>30</option>
                    </select>
                    <span>Showing {{ check_meta_service.from }} to {{ check_meta_service.to }} of {{ check_meta_service.totalRows }} entries</span>
                    <Pagination :maxPage="check_meta_service.maxPage" :totalPages="check_meta_service.lastPage" :currentPage="check_meta_service.page" @pagechanged="onPageChange" :from="'Services'" />
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
                            <div class="row g-2" v-if="asset_type=='Services'">
                                <div class="col-md-4" v-for="field, key in assets1?.asset_service_attributes" :key="key">
                                    <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'Text'">
                                        <label class="form-label">{{field.service_attributes[0].display_name}}</label><span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            :placeholder="'Enter '+ field.service_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field?.service_attributes[0]?.display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field?.service_attributes[0]?.display_name]" class="invalid-feedback">{{ errors[field?.service_attributes[0]?.display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'Number'">
                                        <label class="form-label">{{field.service_attributes[0].display_name}}</label><span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                                        <input
                                            type="number"
                                            class="form-control"
                                            min="0"
                                            oninput="validity.valid||(value='');"
                                            :placeholder="'Enter '+ field.service_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field?.service_attributes[0]?.display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field?.service_attributes[0]?.display_name]" class="invalid-feedback">{{ errors[field?.service_attributes[0]?.display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'Date'">
                                        <label class="form-label">
                                            {{ field.service_attributes[0].display_name }}
                                            <span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="date"
                                            class="form-control"
                                            :placeholder="'Enter ' + field.service_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field?.service_attributes[0]?.display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field?.service_attributes[0]?.display_name]" class="invalid-feedback">
                                            {{ errors[field?.service_attributes[0]?.display_name][0] }}
                                        </span>
                                    </div>

                                    <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'Date&Time'">
                                        <label class="form-label">
                                            {{ field.service_attributes[0].display_name }}
                                            <span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                                        </label>

                                        <input
                                            type="datetime-local"
                                            class="form-control"
                                            :placeholder="'Enter ' + field.service_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field?.service_attributes[0]?.display_name]}"
                                            :value="field.field_value"
                                            step="1"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field?.service_attributes[0]?.display_name]" class="invalid-feedback">
                                            {{ errors[field?.service_attributes[0]?.display_name][0] }}
                                        </span>
                                    </div>
                                    <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'Dropdown'">
                                        <label class="form-label">{{field.service_attributes[0].display_name}}</label><span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                                        <select class="form-control" :class="{'is-invalid': errors[field?.service_attributes[0]?.display_name]}" :value="field.field_value" disabled="true">
                                            <option :value="field.field_value" v-if="field.field_value">{{field.field_value}}</option>
                                            <option :value="field.field_value" v-else>Select {{field?.service_attributes[0]?.display_name}}</option>
                                            <option v-for="value, key in field.service_attributes[0].field_values.split(',')" :key="key" :value="value">{{value}}</option>
                                        </select>
                                        <span v-if="errors[field?.service_attributes[0]?.display_name]" class="invalid-feedback">{{ errors[field?.service_attributes[0]?.display_name][0] }}</span>
                                    </div>
                                    <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'Color'">
                                        <label class="form-label">{{ field.service_attributes[0].display_name }}<span v-if="field.service_attributes[0].is_required" class="text-danger">*</span></label>
                                        <input type="color" class="form-control" :value="field.field_value" style="height: 2.2rem;" disabled="true" />
                                        <span v-if="errors[field?.service_attributes[0]?.display_name]" class="invalid-feedback">{{ errors[field?.service_attributes[0]?.display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.service_attributes?.length && field.service_attributes[0].field_type === 'List'">
                                        <label class="form-label">{{field.service_attributes[0].display_name}}</label><span v-if="field.service_attributes[0].is_required" class="text-danger">*</span>
                                        <select class="form-control" :class="{'is-invalid': errors[field?.service_attributes[0]?.display_name]}" :value="field.field_value" disabled="true">
                                            <option :value="field.service_attributes[0].service_attribute_value?.field_value" v-if="field.service_attributes[0].service_attribute_value?.field_value">
                                                {{field?.service_attributes[0]?.service_attribute_value?.field_value}}
                                            </option>
                                            <option :value="field.service_attributes[0].service_attribute_value?.field_value" v-else>Select {{field.service_attributes[0].display_name}}</option>
                                            <option v-for="value, key in field.service_attributes[0].list_parameter?.field_values.split(',')" :key="key" :value="value.trim()">{{value.trim()}}</option>
                                        </select>
                                        <span v-if="errors[field?.service_attributes[0]?.display_name]" class="invalid-feedback">{{ errors[field?.service_attributes[0]?.display_name][0] }}</span>
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
        name: "Assets.View",
        components: { Search, Pagination, MultiSelect },

        props: {
            asset_id: {
                type: Number,
                required: true,
            },
        },
        data() {
            return {
                asset_type: "",

                check_meta_service: {
                    search: "",
                    order_by: "desc",
                    keyword: "service_id",
                    per_page: 10,
                    totalRows: 0,
                    page: 1,
                    lastPage: 1,
                    from: 1,
                    to: 1,
                    maxPage: 1,
                    trashed: false,
                    asset_id: "",
                    asset_type_id: "",
                },

                asset: {
                    asset_id: "",
                    plant_id: "",
                    asset_code: "",
                    asset_name: "",
                    asset_type_id: "",
                    section_id: "",
                    serial_no: "",
                    status: "",
                    QR_Code: "",
                },
                service: {
                    service_id: "",
                    service_code: "",
                    service_name: "",
                    asset_id: "",
                    asset_zone_id: "",
                    asset_service_id: "",
                    service_asset_zones_obj: [],
                    service_asset_zones: [],
                    asset_service_attributes: [],
                    initial_service_id: "",
                    initial_asset_service_attributes: [],
                    deleted_asset_service_values: [],
                    service_zone_read_only: false,
                },

                asset_spares: [],
                asset_services: [],
                checks: [],
                asset_checks: [],
                template_services: [],

                asset_zones: [],
                asset_spare_services: [],
                errors: [],
                status: true,
                asset_zone_status: false,

                asset_zone_status_services: false,

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
                this.assets1 = this.$store.getters.assets;
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
                return this.asset_spare_services.map((service) => ({
                    ...service,
                    displayName: `${service.zone_name} - ${service.service_name}`,
                }));
            },
        },
        mounted() {
            document.body.classList.add("sidebar-hide");
            this.getAsset();
        },
        methods: {
            getAsset() {
                let vm = this;
                vm.$store
                    .dispatch("post", { uri: "getAsset", data: { asset_id: vm.asset_id } })
                    .then((response) => {
                        vm.asset = response.data.asset;
                        vm.asset_zones = response?.data?.asset?.zone_name;
                        vm.asset.QR_Code = response?.data?.QRCode;
                        vm.getAssetServices();
                    })
                    .catch(function (error) {
                        vm.errors = error.response?.data?.errors;
                        vm.$store.dispatch("error", error.response?.data?.message);
                    });
            },
            getAssetServices() {
                let vm = this;
                let loader = vm.$loading.show();
                vm.check_meta_service.asset_id = vm.asset.asset_id;
                vm.check_meta_service.asset_type_id = vm.asset.asset_type_id;
                vm.$store
                    .dispatch("post", { uri: "paginateAssetServices", data: vm.check_meta_service })
                    .then((response) => {
                        loader.hide();
                        console.log("service paginate", response.data);
                        vm.services = response.data.services;
                        vm.asset_services = response.data.paginate_services;
                        vm.check_meta_service.totalRows = response.data.meta.total;
                        vm.check_meta_service.from = response.data.meta.from;
                        vm.check_meta_service.to = response.data.meta.to;
                        vm.check_meta_service.lastPage = response.data.meta.last_page;
                        vm.check_meta_service.maxPage = vm.check_meta_service.lastPage >= 3 ? 3 : vm.check_meta_service.lastPage;
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },
            editService(service) {
                this.service.service_id = service.service_id;
                this.service.asset_id = service.asset_id;
                this.service.asset_service_id = service.asset_service_id;
                this.service.service_code = service.service_code;
                this.service.service_name = service.service_name;
                this.service.service_asset_zones = [];
                this.service.service_asset_zones.push(service.asset_zone_id);
                this.service.asset_zone_id = service.asset_zone_id;
                this.service.asset_service_attributes = service.asset_service_attributes;
                this.service.initial_service_id = service.service_id;
                this.service.initial_asset_service_attributes = service.asset_service_attributes;

                // this.service.service_asset_zones_obj = [];
                // this.service.service_asset_zones_obj.push({
                //     asset_id: service.asset_zone.asset_id,
                //     asset_zone_id: service.asset_zone.asset_zone_id,
                //     status: service.asset_zone.status,
                //     zone_name: service.asset_zone.zone_name,
                //     diameter: service.asset_zone.diameter,
                //     height: service.asset_zone.height,
                //     asset_spares: service.asset_zone.asset_spares,
                // });

                const matchingZone = this.asset_zones.find(zone => zone.asset_zone_id === service.asset_zone_id);
                this.service.service_asset_zones_obj = matchingZone ? [matchingZone] : [{
                    asset_id: service.asset_zone.asset_id,
                    asset_zone_id: service.asset_zone.asset_zone_id,
                    zone_name: service.asset_zone.zone_name,
                    diameter: service.asset_zone.diameter,
                    height: service.asset_zone.height,
                    asset_spares: service.asset_zone.asset_spares,
                }];

                this.service.service_zone_read_only = true;
            },
            validateFields(validation_type) {
                this.isValid = true;
                this.errors = {};
                if (validation_type == "Services") {
                    for (const field of Object.values(this.service.asset_service_attributes)) {
                        if (field.service_attributes.length && field.service_attributes[0].is_required && !field.field_value) {
                            if (field.service_attributes[0].field_type === "Color") {
                                // Set default color if not provided
                                field.field_value = "#000000"; // Default to black
                            } else {
                                this.errors[field?.service_attributes[0]?.display_name] = [`${field?.service_attributes[0]?.display_name} is required`];
                                this.isValid = false;
                            }
                        }
                    }
                }
            },
            addService() {
                let vm = this;
                vm.service.asset_id = vm.asset.asset_id;
                vm.service.service_asset_zones = [];
                vm.service.service_asset_zones_obj.map(function (ele) {
                    vm.service.service_asset_zones.push(ele.asset_zone_id);
                });
                let loader = vm.$loading.show();
                vm.validateFields("Services");
                if (!vm.isValid) {
                    loader.hide();
                    return;
                }
                vm.$store
                    .dispatch("post", { uri: "addAssetService", data: vm.service })
                    .then((response) => {
                        loader.hide();
                        vm.$store.dispatch("success", response.data.message);
                        vm.service.service_id = "";
                        vm.service.asset_zone_id = "";
                        vm.service.service_asset_zones = [];
                        vm.service.asset_service_attributes = [];

                        vm.service.service_asset_zones_obj = [];
                        vm.service.service_zone_read_only = false;

                        vm.service.initial_service_id = "";
                        vm.service.initial_asset_service_attributes = [];
                        vm.service.deleted_asset_service_values = [];

                        vm.asset_zone_status_services = false;
                        vm.errors = [];
                        vm.getAssetServices();
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },
            updateService() {
                let vm = this;
                let loader = vm.$loading.show();
                vm.validateFields("Services");
                if (!vm.isValid) {
                    loader.hide();
                    return;
                }
                if (vm.service.service_id != vm.service.initial_service_id) {
                    vm.service.initial_asset_service_attributes.map(function (ele) {
                        vm.service.deleted_asset_service_values.push(ele.asset_service_value_id);
                    });
                }
                let uri = { uri: "updateAssetService", data: vm.service };
                vm.$store
                    .dispatch("post", uri)
                    .then(function (response) {
                        loader.hide();
                        vm.$store.dispatch("success", response.data.message);
                        vm.service.service_id = "";
                        vm.service.asset_zone_id = "";
                        vm.service.asset_service_id = "";
                        vm.service.service_asset_zones = [];
                        vm.service.asset_service_attributes = [];

                        vm.service.service_asset_zones_obj = [];
                        vm.service.service_zone_read_only = false;

                        vm.service.initial_service_id = "";
                        vm.service.initial_asset_service_attributes = [];
                        vm.service.deleted_asset_service_values = [];

                        vm.asset_zone_status_services = false;
                        vm.errors = [];
                        vm.getAssetServices();
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },

            deleteService(service) {
                const confirmDelete = confirm("Are you sure you want to delete it ?");
                if (confirmDelete) {
                    let vm = this;
                    // check.asset_id = vm.asset.asset_id;
                    let loader = vm.$loading.show();
                    vm.$store
                        .dispatch("post", { uri: "forceDeleteAssetService", data: service })
                        .then((response) => {
                            loader.hide();
                            vm.$store.dispatch("success", response.data.message);
                            vm.service.service_id = "";
                            vm.errors = [];
                            vm.getAssetServices();
                        })
                        .catch(function (error) {
                            loader.hide();
                            vm.errors = error.response.data.errors;
                            vm.$store.dispatch("warning", error.response.data.message);
                        });
                }
            },
            onPageChange(page, from) {
                if (from == "Services") {
                    this.check_meta_service.page = page;
                    this.getAssetServices();
                }
            },
            sort(field, from) {
                if (from == "Services") {
                    this.check_meta_service.keyword = field;
                    this.check_meta_service.order_by = this.check_meta_service.order_by == "asc" ? "desc" : "asc";
                    this.getAssetServices();
                }
            },
            onPerPageChange(from) {
                if (from == "Services") {
                    this.check_meta_service.page = 1;
                    this.getAssetServices();
                }
            },
            getServiceAttribute(service_id) {
                let vm = this;
                let loader = vm.$loading.show();
                vm.service.asset_service_attributes = [];
                vm.$store
                    .dispatch("post", { uri: "assetServiceAttributeValues", data: { service_id: service_id } })
                    .then((response) => {
                        loader.hide();
                        vm.service.asset_service_attributes = response.data.data;
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },
            viewAsset(asset, from) {
                this.$store.commit("setAssets", asset);
                this.asset_type = from;
                this.assets1 = this.$store.getters.assets;

            },
            searchService() {
                let vm = this;
                vm.check_meta_service.page = 1;
                vm.getAssetServices();
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
<template>
    <div class="col-12 mb-2" v-can="'asset_template_Spares.view'">
        <div class="card card-one">
            <div class="card-header d-flex justify-content-between">
                <h6 class="card-title">Spares</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3 g-2" v-can="['asset_template_Spares.update', 'asset_template_Spares.create']">
                    <div class="col-md-4">
                        <label class="form-label">Template Zone Service <span class="text-danger"> *</span></label>
                        <MultiSelect
                            v-model="spare.spare_template_zones_obj"
                            filter
                            optionLabel="displayName"
                            :options="formattedTemplateSpareServices"
                            placeholder="Select Service"
                            :maxSelectedLabels="3"
                            style="width: 100%; height: 37px;"
                            :class="{ 'is-invalid': errors?.asset_template_service_id }"
                            :customClass="{ 'is-invalid': errors?.asset_template_service_id }"
                            id="asset_template_service_id"
                            @input="templateservice1 => spare.asset_template_service_id = templateservice1"
                            :disabled="spare.spare_zone_read_only"
                        />
                        <span v-if="errors?.asset_template_service_id" class="invalid-feedback" style="display: block !important;">
                            {{ errors?.asset_template_service_id[0] }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Spare <span class="text-danger"> *</span></label>
                        <search
                            :class="{ 'is-invalid': errors?.spare_id }"
                            :customClass="{ 'is-invalid': errors?.spare_id }"
                            aria-describedby="basic-addon2"
                            aria-label="Select Spare"
                            class="my-auto"
                            :initialize="spare.spare_id"
                            id="spare_id"
                            label="spare_name"
                            label2="spare_code"
                            placeholder="Select Spare"
                            :data="spares"
                            @input=" spare1 => spare.spare_id = spare1"
                            @selectsearch="getSpareAttribute(spare.spare_id)"
                        >
                        </search>
                        <span v-if="errors?.spare_id" class="invalid-feedback">{{ errors?.spare_id[0] }}</span>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Max Quantity <span class="text-danger"> *</span></label>
                        <input type="number" class="form-control" placeholder="Enter Max Quantity" :class="{ 'is-invalid': errors?.quantity }" v-model="spare.quantity" />
                        <span v-if="errors?.quantity" class="invalid-feedback">{{ errors?.quantity[0] }}</span>
                    </div>
                    <div class="col-md-3" v-for="field, key in spare.asset_spare_attributes" :key="key">
                        <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type=='Text'">
                            <label class="form-label">{{field.spare_attributes[0].display_name}}</label><span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                            <input type="text" class="form-control" :placeholder="'Enter '+ field.spare_attributes[0].display_name" :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}" v-model="field.field_value" />
                            <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.spare_attributes[0].display_name][0] }}</span>
                        </div>

                        <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type=='Number'">
                            <label class="form-label">{{field.spare_attributes[0].display_name}}</label><span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                            <input
                                type="number"
                                class="form-control"
                                min="0"
                                oninput="validity.valid||(value='');"
                                :placeholder="'Enter '+ field.spare_attributes[0].display_name"
                                :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}"
                                v-model="field.field_value"
                            />
                            <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.spare_attributes[0].display_name][0] }}</span>
                        </div>

                        <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type === 'Date'">
                            <label class="form-label">
                                {{ field.spare_attributes[0].display_name }}
                                <span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                            </label>
                            <input type="date" class="form-control" :placeholder="'Enter ' + field.spare_attributes[0].display_name" :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}" v-model="field.field_value" />
                            <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">
                                {{ errors[field.spare_attributes[0].display_name][0] }}
                            </span>
                        </div>

                        <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type === 'Date&Time'">
                            <label class="form-label">
                                {{ field.spare_attributes[0].display_name }}
                                <span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                            </label>

                            <input
                                type="datetime-local"
                                class="form-control"
                                :placeholder="'Enter ' + field.spare_attributes[0].display_name"
                                :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}"
                                v-model="field.field_value"
                                step="1"
                            />
                            <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">
                                {{ errors[field.spare_attributes[0].display_name][0] }}
                            </span>
                        </div>
                        <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type=='Dropdown'">
                            <label class="form-label">{{field.spare_attributes[0].display_name}}</label><span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                            <select class="form-control" :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}" v-model="field.field_value">
                                <option :value="field.field_value" v-if="field.field_value">{{field.field_value}}</option>
                                <option :value="field.field_value" v-else>Select {{field.spare_attributes[0].display_name}}</option>
                                <option v-for="value, key in field.spare_attributes[0].field_values.split(',')" :key="key" :value="value">{{value}}</option>
                            </select>
                            <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.spare_attributes[0].display_name][0] }}</span>
                        </div>
                        <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type=='Color'">
                            <label class="form-label">{{ field.spare_attributes[0].display_name }}<span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span></label>
                            <input type="color" class="form-control" v-model="field.field_value" style="height: 2.2rem;" />
                            <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.spare_attributes[0].display_name][0] }}</span>
                        </div>

                        <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type=='List'">
                            <label class="form-label">{{field.spare_attributes[0].display_name}}</label><span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                            <select class="form-control" :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}" v-model="field.field_value">
                                <option :value="field.field_value" v-if="field.field_value">{{field.field_value}}</option>
                                <option :value="field.spare_attributes[0].spare_attribute_value?.field_value" v-if="field.spare_attributes[0].spare_attribute_value?.field_value">
                                    {{field.spare_attributes[0].spare_attribute_value?.field_value}}
                                </option>
                                <option :value="field.spare_attributes[0].spare_attribute_value?.field_value" v-else>Select {{field.spare_attributes[0].display_name}}</option>
                                <option v-for="value, key in field.spare_attributes[0].list_parameter?.field_values.split(',')" :key="key" :value="value.trim()">{{value.trim()}}</option>
                            </select>
                            <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.spare_attributes[0].display_name][0] }}</span>
                        </div>
                    </div>
                    <div class="col-md-2 pt-4" v-if="spare.asset_template_spare_id">
                        <button v-can="'asset_template_Spares.update'" class="btn btn-outline-success me-2" @click="updateSpare()"><i class="ri-add-circle-line icon-hgt"></i> Update</button>
                    </div>
                    <div class="col-md-2 pt-4" v-else>
                        <button v-can="'asset_template_Spares.create'" class="btn btn-outline-success me-2" @click="addSpare()"><i class="ri-add-circle-line icon-hgt"></i> Add</button>
                    </div>
                </div>
                <input class="form-control form-control-sm mb-2" type="text" placeholder="Type keyword and press enter key" v-model="meta.search" @keypress.enter="searchSpare()" />
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
                                                <i v-if="meta.keyword=='template_zone_id' && meta.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword=='template_zone_id' && meta.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Service
                                            <span>
                                                <i v-if="meta.keyword=='template_zone_id' && meta.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword=='template_zone_id' && meta.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Spare Type
                                            <span>
                                                <i v-if="meta.keyword=='spare_type_id' && meta.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword=='spare_type_id' && meta.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Spare Code
                                            <span>
                                                <i v-if="meta.keyword=='spare_code' && meta.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword=='spare_code' && meta.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Spare Name

                                            <span>
                                                <i v-if="meta.keyword=='spare_name' && meta.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword=='spare_name' && meta.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Quantity

                                            <span>
                                                <i v-if="meta.keyword=='quantity' && meta.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword=='quantity' && meta.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="asset_spares?.length==0">
                                        <td colspan="7" class="text-center">No records found</td>
                                    </tr>
                                    <tr v-for="spare, key in asset_spares" :key="key">
                                        <td class="text-center">{{ (meta.page-1)*meta.per_page + key+1 }}</td>
                                        <td>{{ spare.asset_zone?.zone_name }}</td>
                                        <td>{{spare?.asset_template_service?.service?.service_name}}</td>
                                        <td>{{spare.spare?.spare_type?.spare_type_name}}</td>
                                        <td>{{spare.spare?.spare_code}}</td>
                                        <td>{{spare.spare?.spare_name}}</td>
                                        <td>{{ spare.quantity }}</td>
                                        <td class="text-center">
                                            <a v-can="'asset_template_Spares.update'" href="javascript:void(0)" class="text-success me-2" @click="editSpare(spare)"><i class="ri-pencil-line fs-18 lh-1"></i></a>
                                            <a href="javascript:void(0)" class="text-primary me-2" data-bs-toggle="modal" data-bs-target="#exampleModal" @click.prevent="viewAsset(spare, 'Spares')">
                                                <i class="ri-eye-fill fs-18 lh-1"></i>
                                            </a>
                                            <a v-can="'asset_template_Spares.delete'" href="javascript:void(0)" class="text-danger me-2" @click="deleteSpare(spare)"><i class="ri-delete-bin-6-line fs-18 lh-1"></i></a>
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
                    <select class="form-select from-select-sm width-75" v-model="meta.per_page" @change="onPerPageChange('Spares')">
                        <option>5</option>
                        <option>10</option>
                        <option>15</option>
                        <option>20</option>
                        <option>25</option>
                        <option>30</option>
                    </select>
                    <span>Showing {{ meta.from }} to {{ meta.to }} of {{ meta.totalRows }} entries</span>
                    <Pagination :maxPage="meta.maxPage" :totalPages="meta.lastPage" :currentPage="meta.page" @pagechanged="onPageChange" :from="'Spares'" />
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
                            <div class="row g-2" v-if="asset_type=='Spares'">
                                <div class="col-md-4" v-for="field, key in assets1?.asset_spare_attributes" :key="key">
                                    <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type=='Text'">
                                        <label class="form-label">{{field.spare_attributes[0].display_name}}</label><span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                                        <input
                                            type="text"
                                            class="form-control"
                                            :placeholder="'Enter '+ field.spare_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.spare_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type=='Number'">
                                        <label class="form-label">{{field.spare_attributes[0].display_name}}</label><span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                                        <input
                                            type="number"
                                            class="form-control"
                                            min="0"
                                            oninput="validity.valid||(value='');"
                                            :placeholder="'Enter '+ field.spare_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.spare_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type === 'Date'">
                                        <label class="form-label">
                                            {{ field.spare_attributes[0].display_name }}
                                            <span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                                        </label>
                                        <input
                                            type="date"
                                            class="form-control"
                                            :placeholder="'Enter ' + field.spare_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">
                                            {{ errors[field.spare_attributes[0].display_name][0] }}
                                        </span>
                                    </div>

                                    <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type === 'Date&Time'">
                                        <label class="form-label">
                                            {{ field.spare_attributes[0].display_name }}
                                            <span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                                        </label>

                                        <input
                                            type="datetime-local"
                                            class="form-control"
                                            :placeholder="'Enter ' + field.spare_attributes[0].display_name"
                                            :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}"
                                            :value="field.field_value"
                                            step="1"
                                            disabled="true"
                                        />
                                        <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">
                                            {{ errors[field.spare_attributes[0].display_name][0] }}
                                        </span>
                                    </div>
                                    <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type=='Dropdown'">
                                        <label class="form-label">{{field.spare_attributes[0].display_name}}</label><span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                                        <select class="form-control" :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}" :value="field.field_value" disabled="true">
                                            <option :value="field.field_value" v-if="field.field_value">{{field.field_value}}</option>
                                            <option :value="field.field_value" v-else>Select {{field.spare_attributes[0].display_name}}</option>
                                            <option v-for="value, key in field.spare_attributes[0].field_values.split(',')" :key="key" :value="value">{{value}}</option>
                                        </select>
                                        <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.spare_attributes[0].display_name][0] }}</span>
                                    </div>
                                    <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type=='Color'">
                                        <label class="form-label">{{ field.spare_attributes[0].display_name }}<span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span></label>
                                        <input type="color" class="form-control" :value="field.field_value" style="height: 2.2rem;" disabled="true" />
                                        <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.spare_attributes[0].display_name][0] }}</span>
                                    </div>

                                    <div v-if="field.spare_attributes.length && field.spare_attributes[0].field_type=='List'">
                                        <label class="form-label">{{field.spare_attributes[0].display_name}}</label><span v-if="field.spare_attributes[0].is_required" class="text-danger">*</span>
                                        <select class="form-control" :class="{'is-invalid': errors[field.spare_attributes[0].display_name]}" :value="field.field_value" disabled="true">
                                            <option :value="field.spare_attributes[0].spare_attribute_value.field_value" v-if="field.spare_attributes[0].spare_attribute_value.field_value">
                                                {{field.spare_attributes[0].spare_attribute_value.field_value}}
                                            </option>
                                            <option :value="field.spare_attributes[0].spare_attribute_value.field_value" v-else>Select {{field.spare_attributes[0].display_name}}</option>
                                            <option v-for="value, key in field.spare_attributes[0].list_parameter?.field_values.split(',')" :key="key" :value="value">{{value}}</option>
                                        </select>
                                        <span v-if="errors[field.spare_attributes[0].display_name]" class="invalid-feedback">{{ errors[field.spare_attributes[0].display_name][0] }}</span>
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
                meta: {
                    search: "",
                    order_by: "desc",
                    keyword: "spare_id",
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
                spare: {
                    spare_id: "",
                    asset_template_spare_id: "",
                    spare_code: "",
                    spare_name: "",
                    asset_template_id: "",
                    template_zone_id: "",
                    spare_template_zones_obj: [],
                    spare_template_zones: [],
                    quantity: "",
                    asset_spare_attributes: [],
                    initial_spare_id: "",
                    initial_asset_spare_attributes: [],
                    deleted_asset_spare_values: [],
                    spare_zone_read_only: false,
                    asset_template_service_id: "",
                },


                device_code: "",
                spares: [],
                asset_spares: [],
                checks: [],
                asset_checks: [],
                asset_services: [],
                asset_variables: [],
                asset_data_sources: [],
                accessory_types: [],
                asset_accessories: [],
                template_services: [],
                template_spare_services: [],
                asset_zones: [],
                errors: [],
                status: true,
                asset_zone_status: false,

                asset_zone_status_variables: false,
                asset_zone_status_datasources: false,
                asset_zone_status_spares: false,
                asset_zone_status_checks: false,
                asset_zone_status_services: false,
                asset_zone_status_accessories: false,
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
                        vm.getAssetSpares();
                    })
                    .catch(function (error) {
                        vm.errors = error.response?.data?.errors;
                        vm.$store.dispatch("error", error.response?.data?.message);
                    });
            },
            getAssetSpares() {
                let vm = this;
                let loader = vm.$loading.show();
                vm.meta.asset_template_id = vm.asset.asset_template_id;
                vm.meta.asset_type_id = vm.asset.asset_type_id;
                vm.$store
                    .dispatch("post", { uri: "paginateAssetTemplateSpares", data: vm.meta })
                    .then((response) => {
                        loader.hide();
                        vm.asset_spares = response.data.paginate_spares;
                        vm.spares = response?.data?.spares;
                        vm.template_spare_services = response.data.template_spare_services;
                        vm.meta.totalRows = response.data.meta.total;
                        vm.meta.from = response.data.meta.from;
                        vm.meta.to = response.data.meta.to;
                        vm.meta.lastPage = response.data.meta.last_page;
                        vm.meta.maxPage = vm.meta.lastPage >= 3 ? 3 : vm.meta.lastPage;
                    })
                    .catch(function (error) {
                        loader.hide();
                        console.log("paginate spare errors-----------", error);
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },

            editSpare(spare) {
                this.spare.spare_id = spare.spare_id;
                this.spare.asset_template_id = spare.asset_template_id;
                this.spare.asset_template_spare_id = spare.asset_template_spare_id;
                this.spare.spare_code = spare.spare_code;
                this.spare.spare_name = spare.spare_name;
                this.spare.spare_template_zones = [];
                this.spare.spare_template_zones.push(spare.template_zone_id);
                this.spare.spare_template_zones_obj = [];
                this.spare.spare_template_zones_obj.push({
                    asset_template_id: spare.asset_zone.asset_template_id,
                    template_zone_id: spare.asset_zone.template_zone_id,
                    zone_name: spare.asset_zone.zone_name,
                    diameter: spare.asset_zone.diameter,
                    height: spare.asset_zone.height,
                    asset_spares: spare.asset_zone.asset_spares,
                });
                this.spare.spare_zone_read_only = true;
                this.spare.template_zone_id = spare.template_zone_id;
                this.spare.quantity = spare.quantity;
                this.spare.asset_spare_attributes = spare.asset_spare_attributes;
                this.spare.initial_spare_id = spare.spare_id;
                this.spare.initial_asset_spare_attributes = spare.asset_spare_attributes;

                const selectedSpare = this.formattedTemplateSpareServices.find((service) => service.asset_template_service_id === spare.asset_template_service_id);

                if (selectedSpare) {
                    this.spare.spare_template_zones_obj = [selectedSpare];
                    this.spare.asset_template_service_id = selectedSpare.asset_template_service_id;
                }
            },

            validateFields(validation_type) {
                this.isValid = true;
                this.errors = {};
                if (validation_type == "Spares") {
                    for (const field of Object.values(this.spare.asset_spare_attributes)) {
                        if (field.spare_attributes.length && field.spare_attributes[0].is_required && !field.field_value) {
                            if (field.spare_attributes[0].field_type === "Color") {
                                // Set default color if not provided
                                field.field_value = "#000000"; // Default to black
                            } else {
                                this.errors[field.spare_attributes[0].display_name] = [`${field.spare_attributes[0].display_name} is required`];
                                this.isValid = false;
                            }
                        }
                    }
                }
            },
            addSpare() {
                let vm = this;
                vm.spare.asset_template_id = vm.asset.asset_template_id;
                vm.spare.spare_template_zones = [];
                vm.spare.spare_template_zones_obj.map(function (ele) {
                    vm.spare.spare_template_zones.push(ele.asset_template_service_id);
                });
                let loader = vm.$loading.show();
                vm.validateFields("Spares");
                // If validation fails, hide loader and stop execution
                if (!vm.isValid) {
                    loader.hide();
                    return;
                }
                vm.$store
                    .dispatch("post", { uri: "addAssetTemplateSpare", data: vm.spare })
                    .then((response) => {
                        loader.hide();
                        vm.$store.dispatch("success", response.data.message);
                        vm.spare.spare_id = "";
                        vm.spare.template_zone_id = [];
                        vm.spare.spare_template_zones = [];
                        vm.spare.spare_template_zones_obj = [];
                        vm.spare.quantity = "";
                        vm.spare.asset_spare_attributes = [];
                        vm.spare.initial_spare_id = "";
                        vm.spare.asset_template_service_id = "";
                        vm.spare.initial_asset_spare_attributes = [];
                        vm.spare.deleted_asset_spare_values = [];
                        vm.asset_zone_status_spares = false;
                        vm.errors = [];
                        vm.spare.spare_zone_read_only = false;
                        vm.getAssetSpares();
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },
            updateSpare() {
                let vm = this;
                let loader = vm.$loading.show();
                vm.validateFields("Spares");
                // Prevent form submission if validation fails
                if (!vm.isValid) {
                    loader.hide();
                    return;
                }

                if (vm.spare.spare_id != vm.spare.initial_spare_id) {
                    vm.spare.initial_asset_spare_attributes.map(function (ele) {
                        vm.spare.deleted_asset_spare_values.push(ele.template_spare_value_id);
                    });
                }
                let uri = { uri: "updateAssetTemplateSpare", data: vm.spare };
                vm.$store
                    .dispatch("post", uri)
                    .then(function (response) {
                        loader.hide();
                        vm.$store.dispatch("success", response.data.message);
                        vm.spare.spare_id = "";
                        vm.spare.template_zone_id = "";
                        vm.spare.asset_template_spare_id = "";
                        vm.spare.spare_template_zones_obj = [];
                        vm.spare.spare_template_zones = [];
                        vm.spare.quantity = "";
                        vm.spare.asset_template_service_id = "";
                        vm.spare.asset_spare_attributes = [];
                        vm.spare.initial_spare_id = "";
                        vm.spare.initial_asset_spare_attributes = [];
                        vm.spare.deleted_asset_spare_values = [];
                        vm.asset_zone_status_spares = false;
                        vm.errors = [];
                        vm.spare.spare_zone_read_only = false;
                        vm.getAssetSpares();
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response?.data?.errors;
                        vm.$store.dispatch("error", error?.response?.data?.message);
                    });
            },

            deleteSpare(spare) {
                const confirmDelete = confirm("Are you sure you want to delete it ?");

                if (confirmDelete) {
                    let vm = this;
                    // spare.asset_id = vm.asset.asset_id;
                    let loader = vm.$loading.show();
                    vm.$store
                        .dispatch("post", { uri: "forceDeleteAssetTemplateSpare", data: spare })
                        .then((response) => {
                            loader.hide();
                            vm.$store.dispatch("success", response.data.message);
                            vm.spare.spare_id = "";
                            vm.errors = [];
                            vm.getAssetSpares();
                        })
                        .catch(function (error) {
                            loader.hide();
                            vm.errors = error.response.data.errors;
                            vm.$store.dispatch("error", error.response.data.message);
                        });
                }
            },
            searchSpare() {
                let vm = this;
                vm.meta.page = 1;
                vm.getAssetSpares();
            },

            onPageChange(page, from) {
                if (from == "Spares") {
                    this.meta.page = page;
                    this.getAssetSpares();
                }
            },
            sort(field, from) {
                if (from == "Spares") {
                    this.meta.keyword = field;
                    this.meta.order_by = this.meta.order_by == "asc" ? "desc" : "asc";
                    this.getAssetSpares();
                }
            },

            onPerPageChange(from) {
                if (from == "Spares") {
                    this.meta.page = 1;
                    this.getAssetSpares();
                }
            },

            getSpareAttribute(spare_id) {
                let vm = this;
                let loader = vm.$loading.show();
                vm.spare.asset_spare_attributes = [];
                vm.$store
                    .dispatch("post", { uri: "assetSpareAttributeValues", data: { spare_id: spare_id } })
                    .then((response) => {
                        loader.hide();
                        vm.spare.asset_spare_attributes = response.data.data;
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
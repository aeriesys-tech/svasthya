<template>
    <div class="col-12 mb-2" v-can="'asset_template_accessories.view'">
        <div class="card card-one">
            <div class="card-header d-flex justify-content-between">
                <h6 class="card-title">Accessories</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3 g-2" v-can="'asset_template_accessories.create'">
                    <div class="col-md-4">
                        <label class="form-label">Template Zone <span class="text-danger"> *</span></label>
                        <MultiSelect
                            v-model="accessory.accessory_template_zones_obj"
                            filter
                            optionLabel="zone_name"
                            :options="asset_zones"
                            placeholder="Select Template Zone"
                            :maxSelectedLabels="3"
                            style="width: 100%; height: 37px;"
                            :style="errors?.accessory_template_zones ? error_style : ''"
                            :disabled="accessory.accessory_zone_read_only"
                        />
                        <span v-if="errors?.accessory_template_zones" class="invalid-feedback" style="display: block !important;">{{ errors?.accessory_template_zones[0] }}</span>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Accessory <span class="text-danger"> *</span></label>
                        <search
                            :class="{ 'is-invalid': errors?.accessory_type_id }"
                            :customClass="{ 'is-invalid': errors?.accessory_type_id }"
                            aria-describedby="basic-addon2"
                            aria-label="Select Accessory"
                            class="my-auto"
                            :initialize="accessory.accessory_type_id"
                            id="accessory_type_id"
                            label="accessory_type_name"
                            placeholder="Select Accessory"
                            :data="accessory_types"
                            @input=" accessory1 => accessory.accessory_type_id = accessory1"
                        >
                        </search>
                        <span v-if="errors?.accessory_type_id" class="invalid-feedback">{{ errors?.accessory_type_id[0] }}</span>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Accessory Name <span class="text-danger"> *</span></label>
                        <input type="text" placeholder="Enter accessory name" class="form-control" :class="{'is-invalid':errors?.accessory_name}" v-model="accessory.accessory_name" />
                        <span v-if="errors?.accessory_name" class="invalid-feedback">{{ errors.accessory_name[0] }}</span>
                    </div>
                    <div class="col-md-10">
                        <label class="form-label">Attachment <span class="text-danger"> *</span></label>
                        <input type="file" class="form-control" id="attachment" ref="attachment" name="attachment" :class="{ 'is-invalid': errors?.attachment }" />
                        <span v-if="errors?.attachment" class="invalid-feedback">{{ errors?.attachment[0] }}</span>
                    </div>
                    <div class="col-md-2 pt-4">
                        <div style="float: left;">
                            <button class="btn btn-outline-success me-2" @click="addAccessory()"><i class="ri-add-circle-line icon-hgt"></i> Add</button>
                        </div>
                    </div>
                </div>
                <input class="form-control form-control-sm mb-2" type="text" placeholder="Type keyword and press enter key" v-model="accessory_meta_service.search" @keypress.enter="searchAccessory()" />
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
                                                <i v-if="accessory_meta_service.keyword=='template_zone_id' && accessory_meta_service.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="accessory_meta_service.keyword=='template_zone_id' && accessory_meta_service.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Accessory Type
                                            <span>
                                                <i v-if="accessory_meta_service.keyword=='accessory_type_id' && accessory_meta_service.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="accessory_meta_service.keyword=='accessory_type_id' && accessory_meta_service.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Accessory Name
                                            <span>
                                                <i v-if="accessory_meta_service.keyword=='accessory_name' && accessory_meta_service.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="accessory_meta_service.keyword=='accessory_name' && accessory_meta_service.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Attachment
                                            <span>
                                                <i v-if="accessory_meta_service.keyword=='attachment' && accessory_meta_service.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="accessory_meta_service.keyword=='attachment' && accessory_meta_service.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th class="text-center" v-can="'asset_template_accessories.delete'">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="asset_accessories?.length==0">
                                        <td colspan="6" class="text-center">No records found</td>
                                    </tr>
                                    <tr v-for="accessory, key in asset_accessories" :key="key">
                                        <td class="text-center">{{(accessory_meta_service.page-1)*accessory_meta_service.per_page + key+1 }}</td>
                                        <td>{{ accessory.asset_zone?.zone_name }}</td>
                                        <td>{{ accessory.accessory_type.accessory_type_name }}</td>
                                        <td>{{ accessory.accessory_name }}</td>
                                        <td>
                                            <a v-if="accessory.attachment" :href="getAttachmentUrl(accessory.attachment)" target="_blank" rel="noopener noreferrer">
                                                {{ getAttachmentName(accessory.attachment) }}
                                            </a>
                                            <span v-else>No attachment</span>
                                        </td>
                                        <td class="text-center" v-can="'asset_template_accessories.delete'">
                                            <a href="javascript:void(0)" class="text-danger me-2" @click="deleteAccessory(accessory)"><i class="ri-delete-bin-6-line fs-18 lh-1"></i></a>
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
                    <select class="form-select from-select-sm width-75" v-model="accessory_meta_service.per_page" @change="onPerPageChange('Accessories')">
                        <option>5</option>
                        <option>10</option>
                        <option>15</option>
                        <option>20</option>
                        <option>25</option>
                        <option>30</option>
                    </select>
                    <span>Showing {{ accessory_meta_service.from }} to {{ accessory_meta_service.to }} of {{ accessory_meta_service.totalRows }} entries</span>
                    <Pagination :maxPage="accessory_meta_service.maxPage" :totalPages="accessory_meta_service.lastPage" :currentPage="accessory_meta_service.page" @pagechanged="onPageChange" :from="'Accessories'" />
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
                accessory_meta_service: {
                    search: "",
                    order_by: "desc",
                    keyword: "asset_template_accessory_id",
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
                accessory: {
                    accessory_id: "",
                    asset_template_id: "",
                    accessory_template_zones: [],
                    accessory_name: "",
                    accessory_type_id: "",
                    attachment: "",
                    accessory_template_zones_obj: [],
                    accessory_zone_read_only: false,
                },
                checks: [],
                accessory_types: [],
                asset_accessories: [],
                template_services: [],
                template_spare_services: [],
                asset_zones: [],
                errors: [],
                status: true,
                asset_zone_status: false,
                asset_zone_status_accessories: false,
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
                        vm.getAssetAccessories();
                    })
                    .catch(function (error) {
                        vm.errors = error.response?.data?.errors;
                        vm.$store.dispatch("error", error.response?.data?.message);
                    });
            },
            getAssetAccessories() {
                let vm = this;
                vm.accessory_meta_service.asset_template_id = vm.asset.asset_template_id;
                vm.accessory_meta_service.asset_type_id = vm.asset.asset_type_id;
                let loader = vm.$loading.show();
                vm.$store
                    .dispatch("post", { uri: "paginateAssetTemplateAccessories", data: vm.accessory_meta_service })
                    .then((response) => {
                        loader.hide();
                        vm.accessory_types = response.data.accessory_types;
                        vm.asset_accessories = response.data.paginate_accessories;
                        vm.accessory_meta_service.totalRows = response.data.meta.total;
                        vm.accessory_meta_service.from = response.data.meta.from;
                        vm.accessory_meta_service.to = response.data.meta.to;
                        vm.accessory_meta_service.lastPage = response.data.meta.last_page;
                        vm.accessory_meta_service.maxPage = vm.accessory_meta_service.lastPage >= 3 ? 3 : vm.accessory_meta_service.lastPage;
                    })
                    .catch(function (error) {
                        loader.hide();
                        console.log("errors accessories--", error);
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },

            getAttachmentUrl(attachment) {
                return `${attachment}`;
            },
            getAttachmentName(attachment) {
                return attachment.split("/").pop();
            },

            deleteAccessory(accessory) {
                const confirmDelete = confirm("Are you sure you want to delete it ?");
                if (confirmDelete) {
                    let vm = this;
                    let loader = vm.$loading.show();
                    vm.$store
                        .dispatch("post", { uri: "deleteAssetTemplateAccessory", data: accessory })
                        .then((response) => {
                            loader.hide();
                            vm.$store.dispatch("success", response.data.message);
                            vm.accessory.accessory_id = "";
                            vm.errors = [];
                            vm.getAssetAccessories();
                        })
                        .catch(function (error) {
                            loader.hide();
                            vm.errors = error.response.data.errors;
                            vm.$store.dispatch("error", error.response.data.message);
                        });
                }
            },

            addAccessory() {
                let vm = this;
                vm.accessory.asset_template_id = vm.asset.asset_template_id;
                let loader = vm.$loading.show();
                vm.accessory.accessory_template_zones = [];
                vm.accessory.accessory_template_zones_obj.map(function (ele) {
                    vm.accessory.accessory_template_zones.push(ele.template_zone_id);
                });
                const data = new FormData();
                data.append("asset_template_id", vm.accessory.asset_template_id);
                data.append("accessory_type_id", vm.accessory.accessory_type_id);
                data.append("accessory_name", vm.accessory.accessory_name);
                vm.accessory.accessory_template_zones.forEach((zone) => {
                    data.append("accessory_template_zones[]", zone);
                });
                data.append("attachment", vm.$refs.attachment.files[0]);

                axios
                    .post(vm.$store.state.apiUrl + "addAssetTemplateAccessory", data, {
                        headers: {
                            "Content-Type": "multipart/form-data",
                            Authorization: "Bearer" + " " + vm.$store.getters.token,
                        },
                    })
                    .then((response) => {
                        loader.hide();
                        vm.$store.dispatch("success", response.data.message);
                        vm.accessory.accessory_id = "";
                        vm.accessory.accessory_name = "";
                        vm.accessory.accessory_template_zones = [];
                        (vm.accessory.accessory_type_id = ""), (vm.$refs.attachment.value = "");
                        vm.errors = [];
                        vm.asset_zone_status_accessories = false;

                        vm.accessory.accessory_template_zones_obj = [];
                        vm.accessory.accessory_zone_read_only = false;

                        vm.getAssetAccessories();
                    })

                    // vm.$store
                    //     .dispatch("post", { uri: "addAssetAccessory", data: vm.accessory })
                    //     .then((response) => {
                    //         loader.hide();
                    //         vm.$store.dispatch("success", response.data.message);
                    //         vm.accessory.accessory_id = "";
                    //         vm.errors = [];
                    //         vm.accessory.accessory_asset_zone_id=[];
                    //         vm.getAssetAccessories();
                    //     })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },

            searchAccessory() {
                let vm = this;
                vm.accessory_meta_service.page = 1;
                vm.getAssetAccessories();
            },
            onPageChange(page, from) {
                if (from == "Accessories") {
                    this.accessory_meta_service.page = page;
                    this.getAssetAccessories();
                }
            },
            sort(field, from) {
                if (from == "Accessories") {
                    this.accessory_meta_service.keyword = field;
                    this.accessory_meta_service.order_by = this.accessory_meta_service.order_by == "asc" ? "desc" : "asc";
                    this.getAssetAccessories();
                }
            },

            onPerPageChange(from) {
                if (from == "Accessories") {
                    this.accessory_meta_service.page = 1;
                    this.getAssetAccessories();
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
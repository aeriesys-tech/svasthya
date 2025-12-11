<template>
    <div class="d-sm-flex align-items-center justify-content-between mb-2">
        <div>
            <ol class="breadcrumb fs-sm mb-1">
                <li class="breadcrumb-item">
                    <router-link to="/dashboard">Dashboard</router-link>
                </li>
                <li class="breadcrumb-item" aria-current="page">
                    <a href="javascript:void(0)">Vendor</a>
                </li>
            </ol>
            <h4 class="main-title mb-0">Vendor</h4>
        </div>
        <router-link to="/dashboard" class="btn btn-danger"><i class="ri-list-check"></i> BACK</router-link>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card card-one">
                <!-- Services Section -->
                <div class="card-header d-flex justify-content-between">
                    <h6 class="card-title mb-0">
                        Vendor - Inspections
                    </h6>
                    <span v-if="vendor_services.length" class="fw-bold">
                        {{ vendor_services[0]?.asset_service?.asset?.asset_name }} :: {{ vendor_services[0]?.asset_service?.asset?.asset_code }}
                    </span>

                    <div class="d-flex mb-2 align-items-center">
                        <select class="form-select me-2" style="width:180px" v-model="selectedVendorService" :disabled="!selectedServiceIds.length">
                            <option disabled value="">Select Vendor</option>
                            <option v-for="vendor in vendors" :key="vendor" :value="vendor">{{ vendor }}</option>
                        </select>

                        <button class="btn btn-primary" @click="updateSelectedServices" :disabled="!selectedServiceIds.length || !selectedVendorService">Update</button>
                    </div>

                </div>
                <div class="card-body">
                    <div class="table-responsive table-responsive-sm">
                        <table class="table table-sm text-nowrap table-striped table-bordered mb-0">
                            <thead>
                                <tr style="background-color: #9b9b9b; color: white;">
                                    <th></th>
                                    <th class="text-center">#</th>
                                    <!-- <th>Asset</th> -->
                                    <th>Asset Zone</th>
                                    <th>Service</th>
                                    <th>Vendor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="vendor_services.length == 0">
                                    <td colspan="5" class="text-center">No records found</td>
                                </tr>
                                <tr v-for="(service, key) in vendor_services" :key="key">
                                    <td><input type="checkbox" :value="service.asset_service_value_id" v-model="selectedServiceIds" /></td>
                                    <td class="text-center">{{ services_meta.from + key }}</td>
                                    <!-- <td>{{ service?.asset_service?.asset?.asset_name }}</td> -->
                                    <td>{{ service?.asset_service?.asset_zone?.zone_name }}</td>
                                    <td>{{ service?.asset_service?.service?.service_name }}</td>
                                    <td>{{ service?.field_value }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <select class="form-select form-select-sm width-75" v-model="services_meta.per_page" @change="onPerPageChange('services')">
                            <option>10</option>
                            <option>15</option>
                            <option>20</option>
                            <option>50</option>
                            <option>100</option>
                        </select>
                        <span>Showing {{ services_meta.from }} to {{ services_meta.to }} of {{ services_meta.totalRows }} entries</span>
                        <Pagination :maxPage="services_meta.maxPage" :totalPages="services_meta.lastPage" :currentPage="parseInt(services_meta.page)" @pagechanged="onPageChange($event, 'services')" />
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 mt-3">
            <div class="card card-one">
                <!-- Spares Section -->
                <div class="card-header d-flex justify-content-between">
                    <h6 class="card-title mb-0">
                        Vendor - Spares
                    </h6>
                    <span v-if="vendor_spares.length" class="fw-bold">
                        {{ vendor_spares[0]?.asset_spare?.asset?.asset_name }} :: {{ vendor_spares[0]?.asset_spare?.asset?.asset_code }}
                    </span>

                    <div class="d-flex mb-2 align-items-center">
                        <select class="form-select me-2" v-model="selectedVendorSpare" :disabled="!selectedSpareIds.length">
                            <option disabled value="">Select Vendor</option>
                            <option v-for="vendor in vendors" :key="vendor" :value="vendor">{{ vendor }}</option>
                        </select>
                        <button class="btn btn-primary" @click="updateSelectedSpares" :disabled="!selectedSpareIds.length || !selectedVendorSpare">Update</button>
                    </div>

                </div>
                <div class="card-body">
                    <div class="table-responsive table-responsive-sm">
                        <table class="table table-sm text-nowrap table-striped table-bordered mb-0">
                            <thead>
                                <tr style="background-color: #9b9b9b; color: white;">
                                    <th></th>
                                    <th class="text-center">#</th>
                                    <!-- <th>Asset</th> -->
                                    <th>Asset Zone</th>
                                    <th>Service</th>
                                    <th>Spare</th>
                                    <th>Vendor</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="vendor_spares.length == 0">
                                    <td colspan="5" class="text-center">No records found</td>
                                </tr>
                                <tr v-for="(spare, key) in vendor_spares" :key="key">
                                    <td><input type="checkbox" :value="spare.asset_spare_value_id" v-model="selectedSpareIds" /></td>
                                    <td class="text-center">{{ spares_meta.from + key }}</td>
                                    <!-- <td>{{ spare?.asset_spare?.asset?.asset_name }}</td> -->
                                    <td>{{ spare?.asset_spare?.asset_zone?.zone_name }}</td>
                                    <td>{{ spare?.asset_spare?.asset_service?.service?.service_name }}</td>
                                    <td>{{ spare?.asset_spare?.spare?.spare_name }}</td>
                                    <td>{{ spare?.field_value }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between align-items-center">
                        <select class="form-select form-select-sm width-75" v-model="spares_meta.per_page" @change="onPerPageChange('spares')">
                            <option>10</option>
                            <option>15</option>
                            <option>20</option>
                            <option>50</option>
                            <option>100</option>
                        </select>
                        <span>Showing {{ spares_meta.from }} to {{ spares_meta.to }} of {{ spares_meta.totalRows }} entries</span>
                        <Pagination :maxPage="spares_meta.maxPage" :totalPages="spares_meta.lastPage" :currentPage="parseInt(spares_meta.page)" @pagechanged="onPageChange($event, 'spares')" />
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
                services_meta: { 
                    order_by: "desc", 
                    keyword: "asset_service_id", 
                    per_page: 10, totalRows: 0, 
                    page: 1, 
                    lastPage: 1, 
                    from: 1, 
                    to: 1, 
                    maxPage: 1, 
                    asset_id: "",
                },
                spares_meta: { 
                    order_by: "desc", 
                    keyword: "asset_spare_id", 
                    per_page: 10, 
                    totalRows: 0, 
                    page: 1, 
                    lastPage: 1, 
                    from: 1, 
                    to: 1, 
                    maxPage: 1, 
                    asset_id: "",
                },
                vendor_services: [],
                vendor_spares: [],
                selectedServiceIds: [],
                selectedSpareIds: [],
                selectedVendorService: "",
                selectedVendorSpare: "",
                vendors: [],
                services:[],
                spares:[],
            };
        },
        mounted() {
            if (this.$route.params.asset_id) {
                this.services_meta.asset_id = this.$route.params.asset_id;
                this.spares_meta.asset_id = this.$route.params.asset_id;
            }
            this.index();
            this.userVendors();
        },
        methods: {
            index() {
                let vm = this;
                let loader = vm.$loading.show();
                const payload = {
                    asset_id: vm.services_meta.asset_id,
                    services_per_page: vm.services_meta.per_page,
                    services_page: vm.services_meta.page,
                    spares_per_page: vm.spares_meta.per_page,
                    spares_page: vm.spares_meta.page,
                };

                if (vm.services_meta.service_id) {
                    payload.service_id = vm.services_meta.service_id;
                }
                if (vm.spares_meta.spare_id) {
                    payload.spare_id = vm.spares_meta.spare_id;
                }
                vm.$store.dispatch("post", { uri: "vendors", data: payload,})
                .then((response) => {
                    loader.hide();
                    vm.vendor_services = response.data.asset_services.data || [];
                    vm.vendor_spares = response.data.asset_spares.data || [];

                    vm.services_meta.totalRows = response.data.asset_services.total;
                    vm.services_meta.from = response.data.asset_services.from;
                    vm.services_meta.to = response.data.asset_services.to;
                    vm.services_meta.lastPage = response.data.asset_services.last_page;
                    vm.services_meta.maxPage = Math.min(vm.services_meta.lastPage, 3);

                    vm.spares_meta.totalRows = response.data.asset_spares.total;
                    vm.spares_meta.from = response.data.asset_spares.from;
                    vm.spares_meta.to = response.data.asset_spares.to;
                    vm.spares_meta.lastPage = response.data.asset_spares.last_page;
                    vm.spares_meta.maxPage = Math.min(vm.spares_meta.lastPage, 3);
                }).catch((error) => {
                    loader.hide();
                    vm.$store.dispatch("error", error?.response?.data?.message);
                });
            },
            onServiceSelect(value, type) {
                if (type === 'service') {
                    this.services_meta.service_id = value?.service_id || "";
                }
                if (type === 'spare') {
                    this.spares_meta.spare_id = value?.spare_id || "";
                }
                this.index();
            },
            onPageChange(page, type) {
                if (type === "services") this.services_meta.page = page;
                if (type === "spares") this.spares_meta.page = page;
                this.index();
            },
            onPerPageChange(type) {
                if (type === "services") this.services_meta.page = 1;
                if (type === "spares") this.spares_meta.page = 1;
                this.index();
            },
            userVendors() {
                let vm = this;
                let loader = vm.$loading.show();
                vm.$store.dispatch("post", { uri: "userVendors" })
                .then((response) => {
                    loader.hide();
                    vm.vendors = response.data.vendors;
                })
                .catch(function (error) {
                    loader.hide();
                    vm.errors = error.response.data.errors;
                    vm.$store.dispatch("error", error.response.data.message);
                });
            },
            updateSelectedServices() {
                let vm = this;
                const selectedServices = this.vendor_services
                    .filter(service => this.selectedServiceIds.includes(service.asset_service_value_id))
                    .map(service => ({
                        asset_id: service.asset_id,
                        asset_service_value_id: service.asset_service_value_id
                    }));

                let loader = vm.$loading.show();
                this.$store.dispatch("post", {
                    uri: "updateVendorServices",
                    data: {
                        vendor: this.selectedVendorService,
                        services: selectedServices
                    },
                }).then((response) => {
                    loader.hide();
                    vm.$store.dispatch("success", response.data.message);
                    this.selectedServiceIds = [];
                    this.selectedVendorService = "";
                    this.index();
                }).catch(function (error) {
                    loader.hide();
                    vm.errors = error.response?.data?.errors;
                    vm.$store.dispatch("error", error?.response?.data?.message);
                });
            },

            updateSelectedSpares() {
                let vm = this;
                const selectedSpares = this.vendor_spares
                    .filter(spare => this.selectedSpareIds.includes(spare.asset_spare_value_id))
                    .map(spare => ({
                        asset_spare_value_id: spare.asset_spare_value_id,
                        asset_id: spare.asset_id
                    }));
                let loader = vm.$loading.show();
                this.$store.dispatch("post", {
                    uri: "updateVendorSpares",
                    data: {
                        vendor: this.selectedVendorSpare,
                        spares: selectedSpares
                    },
                }).then((response) => {
                    loader.hide();
                    vm.$store.dispatch("success", response.data.message);
                    vm.selectedSpareIds = [];
                    vm.selectedVendorSpare = "";
                    vm.index();
                }).catch(function (error) {
                    loader.hide();
                    vm.errors = error.response?.data?.errors;
                    vm.$store.dispatch("error", error?.response?.data?.message);
                });
            },
        },
    };
</script>

<style scoped>
    .width-75 {
        width: 75px;
    }
</style>

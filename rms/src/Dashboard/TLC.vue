<template>
    <div v-if="asset_type_code === 'TLC' && asset_id">
        <div class="col" v-if="asset_id">
            <label class="form-label fw-bold"></label>
            <div class="d-flex justify-content-end">
                <a title="Asset Accessories" href="javascript:void(0)" class="text-primary me-2" @click.prevent="viewRegister(null, '/asset/accessories/'+asset_id)">
                    <i class="ri-survey-line fs-24 lh-1"></i>
                </a>
                <a v-if="selected_asset?.geometry_type === 'V-Cylindrical'" title="Vertical Geometric Type" href="javascript:void(0)" class="text-primary me-2" @click.prevent="viewVGeometric(selected_asset)">
                    <img src="/assets/images/icosahedron.png" style="width: 24px; height: 24px;" />
                </a>
                <a v-else-if="selected_asset?.geometry_type === 'H-Cylindrical'" title="Horizontal Geometric Type" href="javascript:void(0)" class="text-primary me-2" @click.prevent="viewHGeometric(selected_asset)">
                    <img src="/assets/images/icosahedron.png" style="width: 24px; height: 24px;" />
                </a>
            </div>
        </div>

        <div class="col d-flex justify-content-end mt-0" v-if="asset_id">
            <div class="fs-sm me-3">
                <small><strong>Last Updated On: </strong>{{ last_update_date }}</small>
            </div>
        </div>

        <div class="row g-2 mt-0" v-if="asset_id">
            <!-- Row 1: 6 cards -->
            <div class="col-6 col-xl-2">
                <div class="card card-one" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1">TLC Life</label>
                        <h3 class="card-value mb-1">{{ tlc_life }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1">TLC repair Life</label>
                        <h3 class="card-value mb-1">{{ tlc_repair_life }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" style="min-height: 100px !important;">
                    <div class="card-body">
                    <label class="card-title fs-sm fw-medium mb-1">Type of Repair</label>
                    <div class="card-value mb-1"
                        style="white-space: normal; word-break: break-word; line-height: 1.4;">
                        {{ type_of_repair ? type_of_repair.replace(/_/g, ' ') : '-' }}
                    </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1">Total hot metal carried till date</label>
                        <h3 class="card-value mb-1">{{ total_hot_metal_carried }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1">Date of service</label>
                        <h3 class="card-value mb-1" style="font-size: 0.9rem;">{{ date_of_service || '-' }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1">Total no service in days</label>
                        <h3 class="card-value mb-1">{{ total_no_service_in_days }}</h3>
                    </div>
                </div>
            </div>

            <!-- Row 2: 5 cards (one spans 2 columns) -->
            <div class="col-6 col-xl-2">
                <div class="card card-one" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1">Last hot inspection date</label>
                        <h3 class="card-value mb-1" style="font-size: 1rem;">{{ last_hot_inspection_date || '-' }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1">Last hot inspection life</label>
                        <h3 class="card-value mb-1" style="font-size: 0.9rem;">{{ last_hot_inspection_life || '-' }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-4">
                <div class="card card-one" style="min-height: 100px !important; cursor: pointer;" @click="openHotInspectionModal">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1">Last Hot inspection details</label>
                        <h6 class="card-value mb-1" style="font-size: 0.85rem;">{{ last_hot_inspection_details || '-' }}</h6>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" :class="{ 'bg-danger text-white': hot_inspection_deviation > 0 }" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1" :class="{ 'text-white': hot_inspection_deviation > 0 }">
                            Hot Inspection deviation
                        </label>
                        <h3 class="card-value mb-1" :class="{ 'text-white': hot_inspection_deviation > 0 }">
                            {{ hot_inspection_deviation }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" :class="{ 'bg-danger text-white': process_deviation > 0 }" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1" :class="{ 'text-white': process_deviation > 0 }">
                            Process deviation
                        </label>
                        <h3 class="card-value mb-1" :class="{ 'text-white': process_deviation > 0 }">
                            {{ process_deviation }}
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Row 3: 6 cards (one spans 2 columns) -->
            <div class="col-6 col-xl-2">
                <div class="card card-one" :class="{ 'bg-danger text-white': expected_remaining_life < 0 }" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1" :class="{ 'text-white': expected_remaining_life < 0 }">
                            Expected remaining life
                        </label>
                        <h3 class="card-value mb-1" :class="{ 'text-white': expected_remaining_life < 0 }">
                            {{ expected_remaining_life }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1">TLC current status</label>
                        <h3 class="card-value mb-1" style="font-size: 0.9rem;">{{ tlc_current_status || '-' }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1">Initial tare weight</label>
                        <h3 class="card-value mb-1">{{ initial_tare_weight || '-' }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1">Recent tare weight</label>
                        <h3 class="card-value mb-1">{{ recent_tare_weight || '-' }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" :class="{ 'bg-danger text-white': recent_holding_time > 0 }" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1" :class="{ 'text-white': recent_holding_time > 0 }">
                            Recent holiding time
                        </label>
                        <h3 class="card-value mb-1" :class="{ 'text-white': recent_holding_time > 0 }">
                            {{ recent_holding_time || '-' }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <router-link :to="`/vendor_list/${asset_id}`" class="text-decoration-none h-100">
                    <div class="card card-one h-100" style="min-height: 100px !important;">
                        <div class="card-body">
                            <label class="card-title fs-sm fw-medium mb-1">Vendor details</label>
                            <h6 class="card-value mb-1">(Inspection & Spare)</h6>
                        </div>
                    </div>
                </router-link>
            </div>
        </div>

        <!-- Last Hot Inspection Details Modal -->
        <div v-if="showHotInspectionModal" class="custom-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; z-index: 1050;">
            <div style="background: white; padding: 20px; border-radius: 5px; max-width: 900px; width: 90%; max-height: 95vh; display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #dee2e6; padding-bottom: 10px; margin-bottom: 20px;">
                    <h5 style="margin: 0;"><strong>Last Hot Inspection Details</strong></h5>
                    <button @click="showHotInspectionModal = false" style="background: #fb3310; color: white; border: none; font-size: 1.2rem; cursor: pointer; width: 30px; height: 30px; border-radius: 3px;">&times;</button>
                </div>

                <!-- Scrollable Content -->
                <div style="overflow-y: auto; overflow-x: hidden; flex: 1;">
                    <div v-if="user_check && Object.keys(user_check).length > 0">
                        <!-- User Check Basic Info -->
                        <div class="mb-4">
                            <h6 class="mb-3"><strong>Last Hot Inspection Information</strong></h6>
                            <table class="table table-bordered table-striped">
                                <tbody>
                                    <tr v-if="user_check.plant_id">
                                        <th>Reference Date</th>
                                        <td>{{ user_check.reference_date }}</td>
                                    </tr>
                                    <tr v-if="user_check.asset_id">
                                        <th>Asset Zone Service	</th>
                                        <td>{{ user_check.asset_service.asset_zone.zone_name }} :: {{ user_check.asset_service.service.service_name }}</td>
                                    </tr>
                                    <tr v-if="user_check.reference_no">
                                        <th>Reference No</th>
                                        <td>{{ user_check.reference_no }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- User Asset Check Details -->
                        <div v-if="user_asset_checks && user_asset_checks.length > 0">
                            <h6 class="mb-3"><strong>Last Hot Inspection Details</strong></h6>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 50%;">Field Name</th>
                                        <th style="width: 50%;">Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(assetCheck, index) in user_asset_checks" :key="index">
                                        <td><strong>{{ assetCheck.field_name || '-' }}</strong></td>
                                        <td>{{ assetCheck.value !== null && assetCheck.value !== undefined ? assetCheck.value : '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else-if="user_check.user_asset_check && user_check.user_asset_check.length > 0">
                            <h6 class="mb-3"><strong>User Asset Check Details</strong></h6>
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 50%;">Field Name</th>
                                        <th style="width: 50%;">Value</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(assetCheck, index) in user_check.user_asset_check" :key="index">
                                        <td><strong>{{ assetCheck.field_name || '-' }}</strong></td>
                                        <td>{{ assetCheck.value !== null && assetCheck.value !== undefined ? assetCheck.value : '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center text-muted py-3">
                            <p>No user asset check data available</p>
                        </div>
                    </div>
                    <div v-else class="text-center text-muted py-4">
                        <p>Data Not Found</p>
                    </div>
                </div>

                <div style="border-top: 1px solid #dee2e6; padding-top: 15px; margin-top: 20px; text-align: right;">
                    <button @click="showHotInspectionModal = false" class="btn btn-danger">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "TLC",
    props: {
        asset_id: {
            type: [String, Number],
            default: null
        },
        asset_type_code: {
            type: String,
            default: ""
        },
        selected_asset: {
            type: Object,
            default: null
        }
    },
    data() {
        return {
            // Dashboard counts data
            tlc_life: 0,
            tlc_repair_life: "",
            type_of_repair: "",
            total_hot_metal_carried: 0,
            date_of_service: "",
            total_no_service_in_days: 0,
            last_hot_inspection_date: "",
            last_hot_inspection_life: 0,
            last_hot_inspection_details: "",
            hot_inspection_deviation: 0,
            process_deviation: 0,
            expected_remaining_life: 0,
            tlc_current_status: "",
            initial_tare_weight: "",
            recent_tare_weight: "",
            recent_holding_time: "",
            last_update_date: "",
            user_check: null,
            user_asset_checks: [],
            showHotInspectionModal: false,
        };
    },
    watch: {
        asset_id(newAssetId, oldAssetId) {
            if (newAssetId !== oldAssetId && newAssetId) {
                this.getDashboardCounts();
            }
        }
    },
    mounted() {
        if (this.asset_id) {
            this.getDashboardCounts();
        }
    },
    methods: {
        getDashboardCounts() {
            if (!this.asset_id) return;
            let vm = this;
            let loader = vm.$loading.show();
            vm.$store.dispatch("post", { uri: "getTLCDashboard", data: { asset_id: vm.asset_id } }).then((response) => {
                loader.hide();
                // TLC specific data
                vm.tlc_life = response.data.tlc_life || 0;
                vm.tlc_repair_life = response.data.tlc_repair_life || "";
                vm.type_of_repair = response.data.type_of_repair || "";
                vm.total_hot_metal_carried = response.data.total_hot_metal_carried || 0;
                vm.date_of_service = response.data.date_of_service || "";
                vm.total_no_service_in_days = response.data.total_no_service_in_days || 0;
                vm.last_hot_inspection_date = response.data.last_hot_inspection_date || "";
                vm.last_hot_inspection_life = response.data.last_hot_inspection_life || 0;
                vm.last_hot_inspection_details = response.data.last_hot_inspection_details || "";
                vm.hot_inspection_deviation = response.data.hot_inspection_deviation || 0;
                vm.process_deviation = response.data.process_deviations || 0;
                vm.expected_remaining_life = response.data.expected_remaining_lifes || 0;
                vm.tlc_current_status = response.data.tlc_current_status || "";
                vm.initial_tare_weight = response.data.initial_tare_weight || "";
                vm.recent_tare_weight = response.data.recent_tare_weight || "";
                vm.recent_holding_time = response.data.recent_holding_time || "";
                vm.last_update_date = response.data.prod_date || "";
                vm.user_check = response.data.user_check || null;
                // Extract user_asset_check array from user_check
                if (vm.user_check) {
                    vm.user_asset_checks = vm.user_check.user_asset_check || 
                                          vm.user_check.UserAssetCheck || 
                                          (Array.isArray(vm.user_check) ? vm.user_check : []);
                } else {
                    vm.user_asset_checks = [];
                }
            }).catch((error) => {
                loader.hide();
                vm.errors = error.response?.data?.message;
                vm.$store.dispatch("error", error?.response?.data?.message);
            });
        },
        viewRegister(asset, path) {
            this.$store.commit("setCurrentPage", 1);
            this.$store.commit("setAssetId", this.asset_id);
            this.$router.push(path);
        },
        viewVGeometric(asset) {
            this.$store.commit("setCurrentPage", 1);
            this.$store.commit("setAssetId", this.asset_id);
            this.$router.push("/geometric/" + this.asset_id);
        },
        viewHGeometric(asset) {
            this.$store.commit("setCurrentPage", 1);
            this.$store.commit("setAssetId", this.asset_id);
            this.$router.push("/horizontal_geometric/" + this.asset_id);
        },
        openHotInspectionModal() {
            this.showHotInspectionModal = true;
        },
        formatKey(key) {
            // Convert snake_case to Title Case
            return key
                .split('_')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        },
    },
};
</script>


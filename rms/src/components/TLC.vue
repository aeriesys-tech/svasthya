<template>
    <div v-if="asset_type_code === 'TLC'">
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
                        <label class="card-title fs-sm fw-medium mb-1">Type of repair</label>
                        <h3 class="card-value mb-1">{{ type_of_repair || '-' }}</h3>
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
                        <h3 class="card-value mb-1" style="font-size: 0.9rem;">{{ last_hot_inspection_date || '-' }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1">Last hot inspection life</label>
                        <h3 class="card-value mb-1">{{ last_hot_inspection_life }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-4">
                <div class="card card-one" style="min-height: 100px !important;">
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
            tlc_repair_life: 0,
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
            vm.$store.dispatch("post", { uri: "getDashboardCounts", data: { asset_id: vm.asset_id } }).then((response) => {
                loader.hide();
                // TLC specific data
                vm.tlc_life = response.data.tlc_life || 0;
                vm.tlc_repair_life = response.data.tlc_repair_life || 0;
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
                vm.last_update_date = response.data.last_update_date || "";
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
    },
};
</script>


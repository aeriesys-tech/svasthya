<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between">
            <div>
                <ol class="breadcrumb fs-sm mb-1"></ol>
                <h4 class="main-title mb-0">Dashboard</h4>
            </div>
        </div>
        <div class="row g-2 mt-2">
            <div class="col">
                <router-link to="/assets" class="text-decoration-none">
                    <div class="task-category p-3 bg-warning h-100 text-center rounded shadow-sm">
                        <h2 class="category-percent mb-2 text-white">{{ total_asset }}</h2>
                        <h6 class="text-white mb-0">ASSETS</h6>
                    </div>
                </router-link>
            </div>

            <div class="col">
                <router-link to="/Deviations" class="text-decoration-none">
                    <div class="task-category p-3 h-100 text-center rounded shadow-sm" :class="{ 'bg-danger': total_deviations > 0, 'bg-secondary': total_deviations === 0 }">
                        <h2 class="category-percent mb-2" :class="{ 'text-white': total_deviations === 0, 'text-white': total_deviations > 0 }">
                            {{ total_deviations }}
                        </h2>
                        <div>
                            <h6 class="mb-0" :class="{ 'text-white': total_deviations === 0, 'text-white': total_deviations > 0 }">
                                INSPECTION DEVIATIONS
                            </h6>
                        </div>
                    </div>
                </router-link>
            </div>

            <div class="col">
                <router-link to="/process_deviations" class="text-decoration-none">
                    <div class="task-category p-3 h-100 text-center rounded shadow-sm" :class="{ 'bg-danger': total_process_deviations > 0, 'bg-secondary': total_process_deviations === 0 }">
                        <h2 class="category-percent mb-2" :class="{ 'text-white': total_process_deviations === 0, 'text-white': total_process_deviations > 0 }">
                            {{ total_process_deviations }}
                        </h2>
                        <div>
                            <h6 class="mb-0" :class="{ 'text-white': total_process_deviations === 0, 'text-white': total_process_deviations > 0 }">
                                PROCESS DEVIATIONS
                            </h6>
                        </div>
                    </div>
                </router-link>
            </div>

            <div class="col">
                <router-link to="/Pendings" class="text-decoration-none">
                    <div class="task-category p-3 h-100 text-center rounded shadow-sm" style="background-color: #9aa2a5;">
                        <h2 class="category-percent mb-2" :class="{ 'text-white': total_pending_services === 0, 'text-danger': total_pending_services > 0 }">
                            {{ total_pending_services }}
                        </h2>
                        <div>
                            <h6 class="mb-0" :class="{ 'text-white': total_pending_services === 0, 'text-danger': total_pending_services > 0 }">
                                PENDINGS / OVERDUES
                            </h6>
                        </div>
                    </div>
                </router-link>
            </div>

            <div class="col">
                <router-link to="/UpcomingJobs" class="text-decoration-none">
                    <div class="task-category p-3 h-100 text-center rounded shadow-sm" style="background-color: #9aa2a5;">
                        <h2 class="category-percent mb-2" :class="{ 'text-white': total_upcoming_jobs === 0, 'text-danger': total_upcoming_jobs > 0 }">
                            {{ total_upcoming_jobs }}
                        </h2>
                        <div>
                            <h6 class="mb-0" :class="{ 'text-white': total_upcoming_jobs === 0, 'text-danger': total_upcoming_jobs > 0 }">
                                UPCOMING JOBS
                            </h6>
                        </div>
                    </div>
                </router-link>
            </div>

            <div class="mt-5">
                <div class="section-container" v-if="phases.length">
                    <h6><strong>Phases</strong></h6> 
                    <div class="row g-2 d-flex justify-content-start">
                        <div class="col-6 col-xl-2" v-for="(phase, index) in phases" :key="index">
                            <div 
                                class="card card-one mb-2" style="min-height: 60px !important;"
                                :class="{ 'border-active': meta.phase_id === phase.phase_id }"
                                @click="meta.phase_id = phase.phase_id; getAssetAreas(phase)"
                            >
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="small-blue me-2"></span>
                                        <h6 class="mb-0 text-secondary" style="font-size: 0.8rem;">{{ phase.phase_code || phase.phase_name }}:</h6>
                                    </div>
                                    <div>
                                        <b class="fs-16 mb-0 text-gray">{{ phase.phase_name }}</b>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-container" v-if="areas.length">
                    <h6><strong>Zones</strong></h6>
                    <div class="row g-2 d-flex justify-content-start">
                        <div class="col-6 col-xl-2" v-for="(area, index) in areas" :key="index">
                            <div 
                                class="card card-one mb-2" style="min-height: 60px !important;"
                                :class="{ 'border-active': meta.area_id === area.area_id }"
                                @click="meta.area_id = area.area_id; getAssetShops(area)"
                            >
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="small-blue me-2"></span>
                                        <h6 class="mb-0 text-secondary" style="font-size: 0.8rem;">{{ area.area_code || area.area_name }}:</h6>
                                    </div>
                                    <div>
                                        <b class="fs-16 mb-0 text-gray">{{ area.area_name }}</b>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-container" v-if="shops.length">
                    <h6><strong>Shops</strong></h6>
                    <div class="row g-2 d-flex justify-content-start">
                        <div class="col-6 col-xl-2" v-for="(shop, index) in shops" :key="index">
                            <div 
                                class="card card-one mb-2" style="min-height: 60px !important;"
                                :class="{ 'border-active': meta.plant_id === shop.plant_id }"
                                @click="meta.plant_id = shop.plant_id; getAssetsAssetTypes(shop)"
                            >
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="small-blue me-2"></span>
                                        <h6 class="mb-0 text-secondary" style="font-size: 0.8rem;">{{ shop.plant_code || shop.plant_name }}:</h6>
                                    </div>
                                    <div>
                                        <b class="fs-16 mb-0 text-gray">{{ shop.plant_name }}</b>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-container" v-if="asset_types_data.length">
                    <h6><strong>Asset Types</strong></h6>
                    <div class="row g-2 d-flex justify-content-start">
                        <div class="col-6 col-xl-2" v-for="(asset_tdata, index) in asset_types_data" :key="index">
                            <div 
                                class="card card-one mb-2" style="min-height: 40px !important;"
                                :class="{ 'border-active': meta.asset_type_id === asset_tdata.asset_type_id }"
                                @click="meta.asset_type_id = asset_tdata.asset_type_id; getLinkedAssets(asset_tdata)"
                            >
                                <div class="card-body d-flex align-items-center">
                                   <div>
                                        <img 
                                            :src="asset_tdata.asset_type_code === 'STL' 
                                                ? '/assets/images/steel.png' 
                                                : asset_tdata.asset_type_code === 'TLC' 
                                                ? '/assets/images/torpedo.svg' 
                                                : '/assets/images/default.png'" 

                                            :style="asset_tdata.asset_type_code === 'STL' 
                                                ? { height: '30px', width: '30px' } 
                                                : asset_tdata.asset_type_code === 'TLC' 
                                                ? { height: '30px', width: '60px' } 
                                                : { height: '30px', width: '30px' }"

                                            class="img me-2"
                                        />
                                        </div>

                                    <div>
                                        <h6 class="mb-0"><b>{{ 
                                            asset_tdata.asset_type_code === 'STL' ? 'Steel Ladle' : 
                                            asset_tdata.asset_type_code === 'TLC' ? 'Torpedo Ladle Car' : 
                                            asset_tdata.asset_type_name 
                                        }}</b></h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="section-container" v-if="asset_lists.length">
                    <h6><strong>Assets</strong></h6>
                    <div class="row g-2 d-flex justify-content-start">
                        <div class="col-6 col-xl-2" v-for="(asset_list, index) in asset_lists" :key="index">
                            <div
                                class="card card-one mb-2 " style="min-height: 110px !important;"
                                :class="{
                                    'border-active': meta.asset_id === asset_list.asset_id,
                                    'border-alert': isLotBelowThreshold(asset_list) && meta.asset_id !== asset_list.asset_id
                                }"
                                @click="selectAsset(asset_list)"
                            >
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-1">
                                        <span
                                            :class="{
                                                'small-blue': !isLotBelowThreshold(asset_list) || meta.asset_id !== asset_list.asset_id,
                                                'small-red': isLotBelowThreshold(asset_list)
                                            }"
                                            class="me-2"
                                        ></span>
                                        <h6 class="mb-0 text-secondary" style="font-size: 0.8rem;">{{ asset_list.asset_code }}:</h6>
                                    </div>
                                    <div>
                                        <b class="fs-16 mb-0 text-gray">{{ asset_list.asset_name }}</b>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               
            </div>

            <!-- PPMS Components -->
            <STL v-if="getSelectedAssetTypeCode() === 'STL' && meta.asset_id" 
                :asset_id="meta.asset_id" 
                :asset_type_code="getSelectedAssetTypeCode()"
                :selected_asset="getSelectedAsset()"
            />
            
            <TLC v-if="getSelectedAssetTypeCode() === 'TLC' && meta.asset_id && getSelectedAsset()" 
                :asset_id="meta.asset_id" 
                :asset_type_code="getSelectedAssetTypeCode()"
                :selected_asset="getSelectedAsset()"
            />
        </div>
        
    </div>
</template>

<script>
    import Search from "@/components/Search.vue";
    import STL from "@/Dashboard/STL.vue";
    import TLC from "@/Dashboard/TLC.vue";

    export default {
        name: "Dashboard",
        components: { Search, STL, TLC },
        data() {
            return {
                total_users: 0,
                total_asset: 0,
                total_equipment: 0,
                total_service_types: 0,
                total_deviations: 0,
                total_process_deviations: 0,
                total_pending_services: 0,
                total_upcoming_jobs: 0,
                asset_types: [],
                assets: [],
                meta: { asset_type_id: "", asset_id: "", plant_id: "", phase_id: "", area_id: "" },
                total_asset_pending_services: 0,
                total_asset_upcoming_jobs: 0,
                phases: [],
                areas: [],
                shops: [],
                asset_types_data: [],
                asset_lists: [],
            };
        },
        mounted() {
            this.getAssetPhases();
            this.getAssetTypes();

            // Restore meta values from store
            this.meta.asset_type_id = this.$store.state.dashboard_asset_type_id || "";
            this.meta.asset_id = this.$store.state.dashboard_asset_id || "";
            this.meta.plant_id = this.$store.state.dashboard_plant_id || "";
            this.meta.area_id = this.$store.state.dashboard_area_id || "";
            this.meta.phase_id = this.$store.state.dashboard_phase_id || "";

            // Load cascade levels based on existing meta
            if (this.meta.phase_id) this.getAssetAreas({ phase_id: this.meta.phase_id }, false);
            if (this.meta.area_id) this.getAssetShops({ area_id: this.meta.area_id }, false);
            if (this.meta.plant_id) this.getAssetsAssetTypes({ plant_id: this.meta.plant_id }, false);
            if (this.meta.asset_type_id) this.getLinkedAssets({ asset_type_id: this.meta.asset_type_id }, false);
        },
        watch: {
            'meta.asset_id'(newAssetId, oldAssetId) {
            if (newAssetId !== oldAssetId) {
                // Components will handle their own data fetching
            }
            },
        },
        beforeRouteEnter(to, from, next) {
            next((vm) => {
                if (from.name === "login") {
                    vm.$router.go();
                }
                if (vm.$store.getters.user) {
                    vm.getDashboardContent();
                    if (vm.$store.state.dashboard_asset_id) {
                        vm.meta.asset_id = vm.$store.state.dashboard_asset_id;
                        // Components will handle their own data fetching
                    }
                }
            });
        },
     
        methods: {
            isLotBelowThreshold(asset) {
                if (!asset.latest_campaign) return false;

                const lots = [
                    asset.latest_campaign.metal_pp1_lot,
                    asset.latest_campaign.metal_pp2_lot,
                    asset.latest_campaign.slag_pp1_lot,
                    asset.latest_campaign.slag_pp2_lot
                ].filter(lot => lot !== null && lot !== undefined);
                return lots.some(lot => Number(lot) < 40);
            },

            getSelectedAsset() {
                return this.asset_lists.find((a) => a.asset_id === this.meta.asset_id);
            },
            getSelectedAssetTypeCode() {
                const selectedAssetType = this.asset_types_data.find(
                    (at) => at.asset_type_id === this.meta.asset_type_id
                );
                return selectedAssetType ? selectedAssetType.asset_type_code : "";
            },
            getDashboardContent() {
                let vm = this;
                let loader = vm.$loading.show();
                vm.$store.dispatch("post", { uri: "getDashboardContent" }).then(function (response) {
                    loader.hide();
                    vm.total_asset = response.data.asset || 0;
                    vm.total_equipment = response.data.equipment || 0;
                    vm.total_users = response.data.user || 0;
                    vm.total_service_types = response.data.service_type || 0;
                    vm.total_deviations = response.data.deviations || 0;
                    vm.total_pending_services = response.data.pending_services || 0;
                    vm.total_upcoming_jobs = response.data.upcoming_jobs || 0;
                    vm.total_process_deviations = response.data.process_deviations || 0;
                }).catch((error) => {
                    loader.hide();
                    vm.errors = error.response?.data?.message;
                    vm.$store.dispatch("error", error?.response?.data?.message);
                });
            },
            selectAsset(asset_list) {
                this.meta.asset_id = asset_list.asset_id;
                this.$store.commit("setDashboardAssetId", this.meta.asset_id);
                // Components will handle their own data fetching
            },
            getAssetTypes() {
                let vm = this;
                vm.$store.dispatch("post", { uri: "getAssetTypes" }).then((response) => {
                    vm.asset_types = response.data.data || [];
                });
            },
            getAssetPhases() {
                let vm = this;
                vm.$store.dispatch("post", { uri: "getAssetPhases" }).then((response) => {
                    vm.phases = response.data.phases || [];
                });
            },
            getAssetAreas(phase, resetAsset = true) {
                let vm = this;
                vm.$store.dispatch("post", { uri: "getAssetAreas", data: phase }).then((response) => {
                    vm.areas = response.data.areas || [];
                    vm.meta.phase_id = phase.phase_id;
                    vm.$store.commit("setDashboardPhaseId", vm.meta.phase_id);

                    vm.meta.area_id = "";
                    vm.$store.commit("setDashboardAreaId", "");

                    vm.shops = [];
                    vm.asset_types_data = [];
                    vm.asset_lists = [];
                    if (resetAsset) {
                        vm.meta.asset_id = "";
                        vm.meta.asset_type_id = "";
                        vm.$store.commit("setDashboardAssetId", "");
                        vm.$store.commit("setDashboardAssetTypeId", "");
                    }
                });
            },
            getAssetShops(area, resetAsset = true) {
                let vm = this;
                vm.$store.dispatch("post", { uri: "getAssetShops", data: area }).then((response) => {
                    vm.shops = response.data.shops || [];
                    vm.meta.area_id = area.area_id;
                    vm.$store.commit("setDashboardAreaId", vm.meta.area_id);

                    vm.asset_types_data = [];
                    vm.asset_lists = [];
                    if (resetAsset) {
                        vm.meta.asset_id = "";
                        vm.meta.asset_type_id = "";
                        vm.$store.commit("setDashboardAssetId", "");
                        vm.$store.commit("setDashboardAssetTypeId", "");
                    }
                }).catch((error) => {
                    loader.hide();
                    vm.errors = error.response?.data?.message;
                    vm.$store.dispatch("error", error?.response?.data?.message);
                });
            },
            getAssetsAssetTypes(shop, resetAsset = true) {
                let vm = this;
                vm.$store.dispatch("post", {
                        uri: "getAssetsAssetTypes",
                        data: { plant_id: shop.plant_id, phase_id: vm.meta.phase_id },
                    }).then((response) => {
                        vm.asset_types_data = response.data.asset_types || [];
                        vm.meta.plant_id = shop.plant_id;
                        vm.$store.commit("setDashboardPlantId", vm.meta.plant_id);

                        vm.asset_lists = [];
                        if (resetAsset) {
                            vm.meta.asset_id = "";
                            vm.meta.asset_type_id = "";
                            vm.$store.commit("setDashboardAssetId", "");
                            vm.$store.commit("setDashboardAssetTypeId", "");
                        }
                    }).catch((error) => {
                    loader.hide();
                    vm.errors = error.response?.data?.message;
                    vm.$store.dispatch("error", error?.response?.data?.message);
                });
            },

            getLinkedAssets(asset_tdata, resetAsset = true) {
                let vm = this;
                vm.$store.dispatch("post", {
                    uri: "getLinkedAssets",
                    data: {
                        asset_type_id: asset_tdata.asset_type_id,
                        plant_id: vm.meta.plant_id,
                        phase_id: vm.meta.phase_id
                    },
                }).then((response) => {
                    if (response.data && response.data.message) {
                        vm.$store.dispatch("error", response.data.message);
                        vm.asset_lists = [];
                        if (resetAsset) {
                            vm.meta.asset_id = "";
                            vm.$store.commit("setDashboardAssetId", "");
                        }
                        return;
                    }
                    vm.asset_lists = response.data.assets || [];
                    vm.meta.asset_type_id = asset_tdata.asset_type_id;
                    vm.$store.commit("setDashboardAssetTypeId", vm.meta.asset_type_id);
                    if (resetAsset) {
                        vm.meta.asset_id = "";
                        vm.$store.commit("setDashboardAssetId", "");
                    }

                    // Check if any asset in asset_lists has LOT below threshold
                    vm.asset_lists.forEach((asset) => {
                        if (vm.isLotBelowThreshold(asset)) {
                            // Optionally, you can set a flag or directly handle styling in the template
                            console.log(`Asset ${asset.asset_name} has LOT below threshold`);
                        }
                    });
                }).catch((error) => {
                    vm.errors = error.response?.data?.message;
                    vm.$store.dispatch("error", error?.response?.data?.message);
                });
            },

            getAsset() {
                let vm = this;
                vm.$store.dispatch("post", { uri: "getAssetTypeAssets", data: vm.meta }).then((response) => {
                    vm.assets = response.data || [];
                }).catch((error) => {
                    loader.hide();
                    vm.errors = error.response?.data?.message;
                    vm.$store.dispatch("error", error?.response?.data?.message);
                });
            },
        },
    };
</script>

<style scoped>
    .asset-search-width {
        width: 300px;
        max-width: 100%;
    }
    .section-container {
        margin-bottom: 0.5rem;
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #333;
    }
    .cement-bg {
        background-color: #9aa2a5;
    }

    .ladle-bg {
        background-color: white;
    }
    
    .small-blue {
        display: inline-block;
        width: 8px;
        height: 20px;
        background: blue;
        border-radius: 4px;
    }
    .border-active{
        border-color:blue;
        border:2px solid #0064fa;
        background:#e6f0ff
    }
    .small-red {
        display: inline-block;
        width: 8px;
        height: 20px;
        background: red;
        border-radius: 4px;
    }
    .border-alert{
        border-color:blue;
        border:2px solid #fa0000;
        background:#ffe6e6
    }
</style>

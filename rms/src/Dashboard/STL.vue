<template>
    <div v-if="asset_type_code === 'STL'">
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
            <div class="col-6 col-xl-2">
                <div class="card card-one" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1">Campaign No</label>
                        <h3 class="card-value mb-1">{{ total_asset_campaign_no }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" :class="{ 'bg-danger text-white': total_asset_running_lifes > 128 }" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1" :class="{ 'text-white': total_asset_running_lifes > 128 }">
                            Full Ladle Life (Heats)
                        </label>
                        <h3 class="card-value mb-1" :class="{ 'text-white': total_asset_running_lifes > 128 }">
                            {{ total_asset_running_lifes }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" :class="{ 'bg-danger text-white': total_asset_patching_lifes > 63 }" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1" :class="{ 'text-white': total_asset_patching_lifes > 63 }">
                            Repair Life (Heats)
                        </label>
                        <h3 class="card-value mb-1" :class="{ 'text-white': total_asset_patching_lifes > 63 }">
                            {{ total_asset_patching_lifes }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1">Sum of Process Time</label>
                        <h3 class="card-value mb-1">{{ total_asset_process_times }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" :class="{ 'bg-danger text-white': asset_total_billets > total_life_perc }" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1" :class="{ 'text-white': asset_total_billets > total_life_perc }">
                            Total Billet Heats
                        </label>
                        <h3 class="card-value mb-1" :class="{ 'text-white': asset_total_billets > total_life_perc }">
                            {{ asset_total_billets }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1">Billet Repair Heats</label>
                        <h3 class="card-value mb-1">{{ billet_repair_heats }}</h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" :class="{ 'bg-danger text-white': total_asset_continuous_billets > 4 }" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1" :class="{ 'text-white': total_asset_continuous_billets > 4 }">
                            Cont Billet Heats
                        </label>
                        <h3 class="card-value mb-1" :class="{ 'text-white': total_asset_continuous_billets > 4 }">
                            {{ total_asset_continuous_billets }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-6">
            <div class="card card-one" style="cursor: pointer; font-size: 0.7rem; min-height: 100px !important;" @click="openScannerModal">
                <div class="card-body d-flex flex-column justify-content-between py-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="card-title fs-sm fw-medium mb-1">LOT Report</label>
                        <div style="font-size: 0.65rem; margin-bottom: 4px;">
                            {{ image_data?.job_date_time }}
                        </div>
                    </div>
                    <div class="mt-auto">
                        <div v-if="!image_data || !image_data.slag_pp1_lot || !image_data.slag_pp2_lot || !image_data.metal_pp1_lot  || !image_data.metal_pp2_lot" 
                            class="text-center text-danger fw-bold py-3" 
                            style="font-size: 0.7rem;">
                            LOT Report Not Found
                        </div>

                        <div v-else>
                            <div style="font-size: 0.88rem; margin-bottom: 4px; text-align: center;">
                                Ladle Life : <b>{{ image_data?.ladle_life }}</b>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-bordered text-center mb-0" style="font-size: 0.90rem; line-height: 1.0;">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="padding: 1px 2px; background-color: burlywood;" :class="{ 'bg-danger text-white': image_data?.slag_pp1_lot < 40 }">
                                                SZ PP1
                                            </th>
                                            <th style="padding: 1px 2px; background-color: burlywood;" :class="{ 'bg-danger text-white': image_data?.slag_pp2_lot < 40 }">
                                                SZ PP2
                                            </th>
                                            <th style="padding: 1px 2px; background-color: lightgoldenrodyellow;" :class="{ 'bg-danger text-white': image_data?.metal_pp1_lot < 40 }">
                                                MZ PP1
                                            </th>
                                            <th style="padding: 1px 2px; background-color: lightgoldenrodyellow;" :class="{ 'bg-danger text-white': image_data?.metal_pp2_lot < 40 }">
                                                MZ PP2
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td style="padding: 1px 2px; background-color: burlywood;" :class="{ 'bg-danger text-white': image_data?.slag_pp1_lot < 40 }">
                                                <strong>{{ image_data?.slag_pp1_lot ?? " " }}</strong>
                                            </td>
                                            <td style="padding: 1px 2px; background-color: burlywood;" :class="{ 'bg-danger text-white': image_data?.slag_pp2_lot < 40 }">
                                                <strong>{{ image_data?.slag_pp2_lot ?? " " }}</strong>
                                            </td>
                                            <td style="padding: 1px 2px; background-color: lightgoldenrodyellow;" :class="{ 'bg-danger text-white': image_data?.metal_pp1_lot < 40 }">
                                                <strong>{{ image_data?.metal_pp1_lot ?? " " }}</strong>
                                            </td>
                                            <td style="padding: 1px 2px; background-color: lightgoldenrodyellow;" :class="{ 'bg-danger text-white': image_data?.metal_pp2_lot < 40 }">
                                                <strong>{{ image_data?.metal_pp2_lot ?? " " }}</strong>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" :class="{ 'bg-danger text-white': total_asset_deviations > 0 }" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1" :class="{ 'text-white': total_asset_deviations > 0 }">
                            Inspection Deviation
                        </label>
                        <h3 class="card-value mb-1" :class="{ 'text-white': total_asset_deviations > 0 }">
                            {{ total_asset_deviations }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" :class="{ 'bg-danger text-white': total_asset_process_deviations > 0 }" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1" :class="{ 'text-white': total_asset_process_deviations > 0 }">
                            Process Deviation
                        </label>
                        <h3 class="card-value mb-1" :class="{ 'text-white': total_asset_process_deviations > 0 }">
                            {{ total_asset_process_deviations }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-6 col-xl-2">
                <div class="card card-one" :class="{ 'bg-danger text-white': total_asset_expected_remaining_lifes < 0 }" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1" :class="{ 'text-white': total_asset_expected_remaining_lifes < 0 }">
                            Exp Remaining Life
                        </label>
                        <h3 class="card-value mb-1" :class="{ 'text-white': total_asset_expected_remaining_lifes < 0 }">
                            {{ total_asset_expected_remaining_lifes }}
                        </h3>
                    </div>
                </div>
            </div>

            <router-link :to="`/vendor_list/${asset_id}`" class="col-6 col-xl-2 text-decoration-none">
                <div class="card card-one h-100" style="min-height: 100px !important;">
                    <div class="card-body">
                        <label class="card-title fs-sm fw-medium mb-1">Vendor</label>
                        <h6 class="card-value mb-1">(Inspection & Spare)</h6>
                    </div>
                </div>
            </router-link>
        </div>

        <div v-if="showScannerModal" class="custom-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; z-index: 1050;">
            <div style="background: white; padding: 20px; border-radius: 5px; max-width: 1400px; width: 100%; max-height: 95vh; display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #dee2e6; padding-bottom: 10px; margin-bottom: 20px;">
                    <h5 style="margin: 0;"><strong>LOT & Scanner Report</strong></h5>
                    <button @click="showScannerModal = false" style="background: #fb3310; color: white; border: none; font-size: 1.2rem; cursor: pointer;">&times;</button>
                </div>

                <!-- Scrollable -->
                <div style="overflow-y: auto; overflow-x: hidden; flex: 1;">
                    <div class="mb-5">
                        <h6 style="margin-bottom: 15px;"><strong>LOT Report :</strong></h6>
                        <div class="d-flex gap-4 align-items-center mb-4">
                            <h6 class="mb-0"><strong>Job No:</strong> {{ image_data.job_no }}</h6>
                            <h6 class="mb-0"><strong>Script:</strong> {{ image_data.script }}</h6>
                            <h6 class="mb-0"><strong>Job Date&Time:</strong> {{ image_data.job_date_time }}</h6>
                        </div>

                        <div v-if="!image_data.date || !image_data.ladle_life">
                            <span colspan="12" class="text-center text-danger fw-bold py-3">
                                LOT Record Not Found
                            </span>
                        </div>
                        <div v-else >
                            <table class="custom-table" style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
                                <thead>
                                    <tr class="header-row-1" style="background-color: #003087; color: white; border-bottom: 2px solid #6c757d;">
                                        <th colspan="3" style="padding: 10px; border: 1px solid #6c757d; text-align: left;">Ladle No: {{ image_data?.asset?.ppms_code }}</th>
                                        <th colspan="8" style="padding: 10px; border: 1px solid #6c757d; text-align: center;"></th>
                                        <th style="padding: 10px; border: 1px solid #6c757d;"></th>
                                    </tr>
                                    <tr class="header-row-2" style="background-color: #f8f9fa; border-bottom: 2px solid #6c757d;">
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: lightgreen; text-align: left;"></th>
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: lightgreen; text-align: left;"></th>
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: lightgreen; text-align: left;"></th>
                                        <th colspan="4" style="padding: 8px; border: 1px solid #6c757d; background-color: burlywood; text-align: center;">Minimum LOT Slag Zone</th>
                                        <th colspan="4" style="padding: 8px; border: 1px solid #6c757d; background-color: lightgoldenrodyellow; text-align: center;">Maximum LOT Metal Zone</th>
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: lightgreen; text-align: left;"></th>
                                    </tr>
                                    <tr class="header-row-2" style="background-color: #f8f9fa; border-bottom: 2px solid #6c757d;">
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: lightgreen; text-align: left;">Sl No</th>
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: lightgreen; text-align: left;">Date</th>
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: lightgreen; text-align: left;">Ladle Life</th>
                                        <th colspan="2" style="padding: 8px; border: 1px solid #6c757d; background-color: burlywood; text-align: center;">PP1 Side</th>
                                        <th colspan="2" style="padding: 8px; border: 1px solid #6c757d; background-color: burlywood; text-align: center;">PP2 Side</th>
                                        <th colspan="2" style="padding: 8px; border: 1px solid #6c757d; background-color: lightgoldenrodyellow; text-align: center;">PP1 Side</th>
                                        <th colspan="2" style="padding: 8px; border: 1px solid #6c757d; background-color: lightgoldenrodyellow; text-align: center;">PP2 Side</th>
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: lightgreen; text-align: left;">Remarks</th>
                                    </tr>
                                    <tr class="header-row-2" style="background-color: #f8f9fa; border-bottom: 2px solid #6c757d;">
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: lightgreen; text-align: left;"></th>
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: lightgreen; text-align: left;"></th>
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: lightgreen; text-align: left;"></th>
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: burlywood; text-align: center;">Height (m)</th>
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: burlywood; text-align: center;">LOT (mm)</th>
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: burlywood; text-align: center;">Height (m)</th>
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: burlywood; text-align: center;">LOT (mm)</th>
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: lightgoldenrodyellow; text-align: center;">Height (m)</th>
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: lightgoldenrodyellow; text-align: center;">LOT (mm)</th>
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: lightgoldenrodyellow; text-align: center;">Height (m)</th>
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: lightgoldenrodyellow; text-align: center;">LOT (mm)</th>
                                        <th style="padding: 8px; border: 1px solid #6c757d; background-color: lightgreen; text-align: left;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="image_data.date || image_data.ladle_life" class="body-row" style="border-bottom: 1px solid #6c757d; background-color: #f8f9fa;">
                                        <td style="padding: 8px; border: 1px solid #6c757d;">1</td>
                                        <td style="padding: 8px; border: 1px solid #6c757d;">{{ image_data?.date }}</td>
                                        <td style="padding: 8px; border: 1px solid #6c757d;">{{ image_data?.ladle_life }}</td>
                                        <td class="center" style="padding: 8px; border: 1px solid #6c757d; text-align: center;">{{ image_data?.slag_pp1_height }}</td>
                                        <td class="center" style="padding: 8px; border: 1px solid #6c757d; text-align: center;">{{ image_data?.slag_pp1_lot }}</td>
                                        <td class="center" style="padding: 8px; border: 1px solid #6c757d; text-align: center;">{{ image_data?.slag_pp2_height }}</td>
                                        <td class="center" style="padding: 8px; border: 1px solid #6c757d; text-align: center;">{{ image_data?.slag_pp2_lot }}</td>
                                        <td class="center" style="padding: 8px; border: 1px solid #6c757d; text-align: center;">{{ image_data?.metal_pp1_height }}</td>
                                        <td class="center" style="padding: 8px; border: 1px solid #6c757d; text-align: center;">{{ image_data?.metal_pp1_lot }}</td>
                                        <td class="center" style="padding: 8px; border: 1px solid #6c757d; text-align: center;">{{ image_data?.metal_pp2_height }}</td>
                                        <td class="center" style="padding: 8px; border: 1px solid #6c757d; text-align: center;">{{ image_data?.metal_pp2_lot }}</td>
                                        <td style="padding: 8px; border: 1px solid #6c757d;">{{ image_data?.remarks }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Scanner Results -->
                    <div>
                        <h6 style="margin-bottom: 15px;"><strong>Scanner Results :</strong></h6>
                        <div class="d-flex justify-content-between align-items-center mb-3" style="border-bottom: 1px solid #dee2e6; padding-bottom: 10px;">
                            <div class="d-flex gap-4 align-items-center">
                                <h6 class="mb-0"><strong>Job No:</strong> {{ scannerData[0]?.campaign?.job_no }}</h6>
                                <h6 class="mb-0"><strong>Script:</strong> {{ scannerData[0]?.campaign?.script }}</h6>
                                <h6 class="mb-0"><strong>Job Date&Time:</strong> {{ scannerData[0]?.campaign?.job_date_time }}</h6>
                            </div>
                            <div class="d-flex gap-4 align-items-center" v-if="scannerData[0]?.campaign?.file || scannerData[0]?.campaign?.image">
                                <template v-if="scannerData[0]?.campaign?.file">
                                    <h6 class="mb-0">
                                        <strong>PDF File:</strong>
                                        <a :href="scannerData[0].campaign.file" target="_blank" rel="noopener noreferrer">
                                            <i class="ri-eye-fill fs-18 lh-1"></i>
                                        </a>
                                    </h6>
                                </template>
                                <template v-if="scannerData[0]?.campaign?.image">
                                    <h6 class="mb-0">
                                        <strong>LOT Report:</strong>
                                        <a :href="scannerData[0].campaign.image" target="_blank" rel="noopener noreferrer">
                                            <i class="ri-eye-fill fs-18 lh-1"></i>
                                        </a>
                                    </h6>
                                </template>
                            </div>
                        </div>

                        <div v-if="scannerData && scannerData.length">
                            <div class="row">
                                <div class="col-md-6 mb-3" v-for="(item, index) in scannerData" :key="index">
                                    <div class="border p-2 rounded text-center">
                                        <img :src="item.file" alt="Scanner Image" style="max-width: 100%; max-height: 250px; object-fit: contain;" />
                                        <p>{{ item.location }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else style="text-align: center;">
                            <p style="color: red;">No scanner results found.</p>
                        </div>
                    </div>
                </div>
                <div class="text-end mt-2">
                    <button @click="showScannerModal = false" class="btn btn-danger">Close</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "STL",
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
            showScannerModal: false,
            scannerData: null,
            image_data: null,
            // Dashboard counts data
            total_asset_campaign_no: 0,
            total_asset_running_lifes: 0,
            total_asset_patching_lifes: 0,
            total_asset_process_times: 0,
            asset_total_billets: 0,
            total_life_perc: 0,
            billet_repair_heats: 0,
            total_asset_continuous_billets: 0,
            total_asset_deviations: 0,
            total_asset_process_deviations: 0,
            total_asset_expected_remaining_lifes: 0,
            last_update_date: "",
        };
    },
    watch: {
        asset_id(newAssetId, oldAssetId) {
            if (newAssetId !== oldAssetId && newAssetId) {
                this.image_data = null;
                this.scannerData = null;
                this.showCampaignImages();
                this.getDashboardCounts();
            }
        }
    },
    mounted() {
        if (this.asset_id) {
            this.showCampaignImages();
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
                vm.total_asset_deviations = response.data.deviations || 0;
                vm.total_asset_running_lifes = response.data.running_lifes || 0;
                vm.total_asset_patching_lifes = response.data.patching_lifes || 0;
                vm.total_asset_process_times = response.data.process_times || 0;
                vm.total_asset_process_deviations = response.data.process_deviations || 0;
                vm.total_asset_expected_remaining_lifes = response.data.expected_remaining_lifes || 0;
                vm.total_asset_campaign_no = response.data.campaign_no || 0;
                vm.asset_total_billets = response.data.total_billets || 0;
                vm.total_asset_continuous_billets = response.data.continuous_billets || 0;
                vm.total_life_perc = response.data.total_life_perc || 0;
                vm.last_update_date = response.data.last_update_date || "";
                vm.billet_repair_heats = response.data.billet_repair_heats || 0;
            }).catch((error) => {
                loader.hide();
                vm.errors = error.response?.data?.message;
                vm.$store.dispatch("error", error?.response?.data?.message);
            });
        },
        showCampaignImages() {
            if (!this.asset_id) return;
            let loader = this.$loading.show();
            this.image_data = null;
            this.$store.dispatch("post", { uri: "showCampaignImages", data: { asset_id: this.asset_id } })
            .then((response) => {
                loader.hide();
                if (response.data && response.data.message) {
                    this.$store.dispatch("error", response.data.message);
                    this.image_data = null;
                    return;
                }
                this.image_data = response.data.data || {};
            }).catch((error) => {
                loader.hide();
                this.image_data = null;
            });
        },
        openScannerModal() {
            if (!this.asset_id) return;
            let loader = this.$loading.show();
            this.$store.dispatch("post", {
                uri: "getScannerResults",
                data: { asset_id: this.asset_id }
            })
            .then((response) => {
                loader.hide();
                this.scannerData = response.data.data || [];
                this.showScannerModal = true;
            })
            .catch((error) => {
                loader.hide();
                this.errors = error?.response?.data?.message;
                this.$store.dispatch("error", error?.response?.data?.message);
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


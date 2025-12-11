<template>
    <div class="">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <div>
                <ol class="breadcrumb fs-sm mb-1">
                    <li class="breadcrumb-item" aria-current="page">
                        <router-link to="/dashboard">Dashboard</router-link>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="javascript:void(0)">Registers</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <router-link to="/user_checks">Scanner Register</router-link>
                    </li>
                    <li class="breadcrumb-item" aria-current="page">View</li>
                </ol>
                <h4 class="main-title mb-0">View Scanner Register</h4>
            </div>
            <router-link to="/health_checks" type="submit" class="btn btn-primary" style="float: right;"><i class="ri-list-check"></i> SCANNER REGISTERS</router-link>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card card-one mb-3">
                    <div class="card-header">
                        <h5 class="card-title">Scanner Register</h5>
                        <br />
                    </div>

                    <div class="card-body p-0">
                        <div class="setting-item">
                            <div class="row g-2 mb-0">
                                <div class="col-md-4">
                                    <h6>Job Number</h6>
                                    <p class="font">{{ campaigns[0]?.campaign?.job_no }}</p>
                                </div>
                                <div class="col-md-4">
                                    <h6>Asset</h6>
                                    <p class="font">{{ campaigns[0]?.campaign?.asset?.asset_name }}</p>
                                </div>
                                <div class="col-md-4">
                                    <h6>Job Date & Time</h6>
                                    <p class="font">{{ convertDateFormat(campaigns[0]?.campaign?.job_date_time) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0 mt-0">
                        <div class="setting-item d-flex align-items-center gap-2">
                            <h6 class="mb-0">PDF File : </h6>
                            <a :href="campaigns[0]?.campaign?.file" target="_blank" rel="noopener noreferrer">
                                <i class="ri-eye-line fs-18 lh-1"></i>
                            </a>
                        </div>
                    </div>

                    <div class="setting-item px-3 pb-3">
                        <h6 class="mb-2"><strong>LOT Report :</strong></h6>
                        <div v-if="!campaigns[0]?.campaign?.date || !campaigns[0]?.campaign?.slag_pp1_height">
                            <span colspan="12" style="text-align: center; padding: 20px; color: #ff0000;">
                               LOT Record Not Found
                            </span>
                        </div>
                        <div v-else>
                            <table class="custom-table" style="width: 100%; border-collapse: collapse; font-family: Arial, sans-serif;">
                                <thead>
                                    <tr class="header-row-1" style="background-color: #003087; color: white; border-bottom: 2px solid #6c757d;">
                                        <th colspan="3" style="padding: 10px; border: 1px solid #6c757d; text-align: left;">Ladle No: {{ campaigns[0]?.campaign?.asset?.ppms_code }}</th>
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
                                    <tr v-if="campaigns[0]?.campaign.date || campaigns[0]?.campaign.ladle_life" class="body-row" style="border-bottom: 1px solid #6c757d; background-color: #f8f9fa;">
                                        <td style="padding: 8px; border: 1px solid #6c757d;">1</td>
                                        <td style="padding: 8px; border: 1px solid #6c757d;">{{ campaigns[0]?.campaign?.date }}</td>
                                        <td style="padding: 8px; border: 1px solid #6c757d;">{{ campaigns[0]?.campaign?.ladle_life }}</td>
                                        <td class="center" style="padding: 8px; border: 1px solid #6c757d; text-align: center;">{{ campaigns[0]?.campaign?.slag_pp1_height }}</td>
                                        <td class="center" style="padding: 8px; border: 1px solid #6c757d; text-align: center;">{{ campaigns[0]?.campaign?.slag_pp1_lot }}</td>
                                        <td class="center" style="padding: 8px; border: 1px solid #6c757d; text-align: center;">{{ campaigns[0]?.campaign?.slag_pp2_height }}</td>
                                        <td class="center" style="padding: 8px; border: 1px solid #6c757d; text-align: center;">{{ campaigns[0]?.campaign?.slag_pp2_lot }}</td>

                                        <td class="center" style="padding: 8px; border: 1px solid #6c757d; text-align: center;">{{ campaigns[0]?.campaign?.metal_pp1_height }}</td>
                                        <td class="center" style="padding: 8px; border: 1px solid #6c757d; text-align: center;">{{ campaigns[0]?.campaign?.metal_pp1_lot }}</td>
                                        <td class="center" style="padding: 8px; border: 1px solid #6c757d; text-align: center;">{{ campaigns[0]?.campaign?.metal_pp2_height }}</td>
                                        <td class="center" style="padding: 8px; border: 1px solid #6c757d; text-align: center;">{{ campaigns[0]?.campaign?.metal_pp2_lot }}</td>
                                        <td style="padding: 8px; border: 1px solid #6c757d;">{{ campaigns[0]?.campaign?.remarks }}</td>
                                    </tr>
                                    <tr v-else>
                                        <td colspan="12" style="text-align: center; padding: 20px; color: #ff0000;">
                                            <span>No records found</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="setting-item px-3 pb-3">
                        <h6 class="mb-2"><strong>Scanner Results :</strong></h6>
                        <table class="table table-bordered mb-0">
                            <tbody>
                                <tr v-for="(row, rowIndex) in chunkedCampaigns" :key="rowIndex">
                                    <td class="text-center" v-for="(item, index) in row" :key="index" style="width: 50%;">
                                        <img class="mb-2" :src="item?.file" height="180" />
                                        <h6>{{ item.location }}</h6>
                                    </td>
                                    <td v-if="row.length < 2"></td>
                                </tr>
                                <tr v-if="campaigns.length === 0">
                                    <td colspan="2" class="text-center text-danger">No records found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import moment from "moment";
    export default {
        name: "ViewAsset",
        data() {
            return {
                health_check: {
                    campaign_id: "",
                },
                campaigns: [],
            };
        },
        beforeRouteEnter(to, from, next) {
            next((vm) => {
                vm.health_check.campaign_id = to.params.campaign_id;
                vm.getCampaignImages();
            });
        },
        computed: {
            chunkedCampaigns() {
                const chunks = [];
                for (let i = 0; i < this.campaigns.length; i += 2) {
                    chunks.push(this.campaigns.slice(i, i + 2));
                }
                return chunks;
            }
        },
        methods: {
            getCampaignImages() {
                let vm = this;
                let loader = vm.$loading.show();
                let uri = { uri: "getCampaignImages", data: { campaign_id: vm.health_check.campaign_id } };
                vm.$store
                    .dispatch("post", uri)
                    .then(function (response) {
                        loader.hide();
                        vm.campaigns = response.data.data;
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },
            showImage(image) {
                document.getElementById("image").style.display = "block";
                document.getElementById("btnID").style.display = "none";
            },
            convertDateFormat(date) {
                let vm = this;
                return moment(date).format("YYYY-MM-DD HH:mm:ss");
            },
        },
    };
</script>
<style scoped>
    .font {
        font-size: 14px;
    }
    .setting-item .table th:not(:first-child),
    .setting-item .table td:not(:first-child) {
        text-align: left;
    }
    .img {
        transition: transform 0.3s ease-in-out;
    }

    .imageContainer > img:hover {
        -webkit-transform: scale(1.3);
        transform: scale(1.3);
    }

    .imageContainer:hover,
    .imageContainer img:hover {
        transform: none !important;
        box-shadow: none !important;
        cursor: default !important;
        filter: none !important;
    }

</style>

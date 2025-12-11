<template>
    <div class="card card-one" v-if="assets">
        <div class="card-header text-center">
            <h6 class="card-title">{{ assets.asset_name }}</h6>
        </div>
        <div class="card-body">
            <h6 class="mb-3"><span class="text-primary">Note:</span> 1 meter={{meter}}px</h6>
            <div class="col-md-3 mb-4">
                <input class="form-control" type="number" v-model="meter" />
            </div>
            <div class="row">
                <div class="col-md-12 mb-4 ss align-items-center justify-content-center">
                    <h6 class="text-center" :style="`width:${Number(assets.diameter*meter)}px;margin-left:35px;`">Dia={{Number(assets.diameter)}} (m)</h6>
                    <div class="dimensions-x mb-3" :style="`width:${Number(assets.diameter*meter)}px; margin-left:35px;` "><span class="arrow-left"></span><span class="arrow-right"></span></div>
                    <div class="row">
                        <div class="col-2 d-flex align-items-center justify-content-center">
                            <div class="dimensions-y" :style="`height:${Number(assets.height*meter)}px;`"><span class="arrow-top"></span><span class="arrow-bottom"></span></div>
                            <h6 class="vertical-text mr-0">Heigth={{Number(assets.height)}} (m)</h6>
                        </div>
                        <div class="col-10">
                            <div :title="assets.asset_name" class="" :style="`height:${Number(assets.height*meter)}px; width:${Number(assets.diameter*meter)}px; border:2px solid gray`" @click="showAssetZones()"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 ss" v-if="display_asset_zone">
                    <h6 class="text-center" :style="`width:${Number(assets.diameter * meter)}px;`">
                        Dia={{ Number(assets.diameter) }} (m)
                    </h6>
                    <div class="dimensions-x mb-3" :style="`width:${Number(assets.diameter * meter)}px;`">
                        <span class="arrow-left"></span><span class="arrow-right"></span>
                    </div>

                    <div v-for="(zone, key) in assets.zone_name" :key="key" class="zone-container"
                        :style="`height:${Number(zone.height * meter)}px; width:${Number(assets.diameter * meter)}px; border-left:1px solid gray; border-right:1px solid gray;`">
                        
                        <div class="zone-name-container" style="text-align: center; align-items: center;">
                            <span style="writing-mode: vertical-lr;">{{ Number(zone.height) }} (m)</span>
                            <span class="vertical-text1">&nbsp;&nbsp;{{ zone.zone_name }}</span>
                        </div>

                        <div v-for="(spare, key1) in zone.asset_spares" :key="key1"
                            :title="`${spare?.spare?.spare_name} :: (${spare.quantity}) :: (${spare.asset_service.service.service_name})`"
                            :style="{
                                height: `${parseFloat(((spare.quantity / totalQuantity(zone.asset_spares)) * Number(zone.height)) * meter)}px`,
                                width: `${assets.diameter * meter}px`,
                                cursor: 'pointer',
                                textAlign: 'center',
                                alignContent: 'center',
                                // borderBottom: key1 % 2 === 0 ? '1px solid gray' : '',
                                // borderTop: key1 % 2 !== 0 ? '1px solid gray' : '',
                                display: 'flex',
                                position: 'relative'
                            }">
                            
                            <!-- Percentage Outside Left -->
                            <div :style="{
                                position: 'absolute',
                                left: '-10px',
                                transform: 'translateX(-100%)',
                                color: '#41505f',
                                fontWeight: 'bold',
                                fontSize: '12px',
                                whiteSpace: 'nowrap'
                            }">
                                {{ (parseFloat((spare.quantity / totalQuantity(zone.asset_spares))) * 100).toFixed(2) }} %
                            </div>

                            <!-- Left Section -->
                            <div :style="{
                                width: '5%',
                                height: '100%',
                                backgroundColor: spareColorCode(spare)
                            }"></div>

                            <!-- Middle  Section -->
                            <div :style="{
                                width: '90%',
                                height: '100%',
                                backgroundColor: 'white',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center'
                            }">
                            </div>

                            <!-- Right Section -->
                            <div :style="{
                                width: '5%',
                                height: '100%',
                                backgroundColor: spareColorCode(spare)
                            }"></div>

                        </div>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <div class="card card-one">
                        <div class="card-header d-flex justify-content-between">
                            <h6 class="card-title">Spares</h6>
                        </div>
                        <div class="card-body">
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
                                                            <i v-if="meta.keyword=='asset_zone_id' && meta.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                            <i v-else-if="meta.keyword=='asset_zone_id' && meta.order_by=='desc'" class="ri-arrow-down-line"></i>
                                                            <i v-else class="fas fa-sort"></i>
                                                        </span>
                                                    </th>
                                                    <th>
                                                        Service
                                                        <span>
                                                            <i v-if="meta.keyword=='asset_zone_id' && meta.order_by=='asc'" class="ri-arrow-up-line"></i>
                                                            <i v-else-if="meta.keyword=='asset_zone_id' && meta.order_by=='desc'" class="ri-arrow-down-line"></i>
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
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-if="asset_spares?.length==0">
                                                    <td colspan="7" class="text-center">No records found</td>
                                                </tr>
                                                <tr v-for="spare, key in asset_spares" :key="key">
                                                    <td class="text-center">{{ (meta.page-1)*meta.per_page + key+1 }}</td>
                                                    <td>{{ spare.asset_zone?.zone_name }}</td>
                                                    <td>{{ spare?.asset_service?.service?.service_name }}</td>
                                                    <td>{{spare.spare?.spare_type?.spare_type_name}}</td>
                                                    <td>{{spare.spare?.spare_code}}</td>
                                                    <td>{{spare.spare?.spare_name}}</td>
                                                    <td>{{ spare.quantity }}</td>
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
            </div>
        </div>
    </div>
</template>

<script>
    import Pagination from "@/components/Pagination.vue";
    export default {
        name: "Geometric",
        components: { Pagination },
        data() {
            return {
                meta: {
                    search: "",
                    order_by: "asc",
                    keyword: "spare_id",
                    per_page: 5,
                    totalRows: 0,
                    page: 1,
                    lastPage: 1,
                    from: 1,
                    to: 1,
                    maxPage: 1,
                    trashed: false,
                    asset_id: "",
                },
                asset: {
                    asset_id: "",
                },
                assets: "",
                meter: null,
                display_asset_zone: false,
                asset_spares: [],
            };
        },
        mounted() {
            this.meter = this.$store.state.meter;
            if (this.meta.asset_id) {
                this.getAssetSpares();
            }
        },

        beforeRouteEnter(to, from, next) {
            next((vm) => {
                let uri = { uri: "getAssetReview", data: { asset_id: to.params.asset_id } };
                vm.$store
                    .dispatch("post", uri)
                    .then(function (response) {
                        vm.assets = response.data.asset;
                        vm.meta.asset_id = to.params.asset_id;
                        console.log("assets:----", vm.assets);
                        vm.getAssetSpares();
                    })
                    .catch(function (error) {
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            });
        },
        methods: {
            showAssetZones() {
                let vm = this;
                vm.display_asset_zone = !vm.display_asset_zone;
            },
            // totalQuantity(spare) {
            //     return spare.reduce((sum, item) => sum + item.quantity, 0);
            // },
            totalQuantity(spares) {
                return spares.reduce((sum, item) => sum + parseFloat(item.quantity), 0);
            },
            spareColorCode(spare) {
                console.log("spare:----", spare.asset_spare_value);
                for (let i = 0; i < spare.asset_spare_value.length; i++) {
                    if (spare.asset_spare_value[i].field_value.includes("#")) {
                        return spare.asset_spare_value[i].field_value;
                    }
                }
            },
            getAssetSpares() {
                let vm = this;
                vm.$store
                    .dispatch("post", { uri: "paginateAssetSparesReview", data: this.meta })
                    .then((response) => {
                        vm.asset_spares = response.data.paginate_spares;
                        vm.spares = response?.data?.spares;
                        vm.meta.totalRows = response.data.meta.total;
                        vm.meta.from = response.data.meta.from;
                        vm.meta.to = response.data.meta.to;
                        vm.meta.lastPage = response.data.meta.last_page;
                        vm.meta.maxPage = vm.meta.lastPage >= 3 ? 3 : vm.meta.lastPage;
                        // vm.getChecks();
                        if (vm.initial_status) {
                            vm.getAssetChecks();
                        }
                    })
                    .catch(function (error) {
                        console.log("errors", error);
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            },
            onPageChange(page) {
                this.meta.page = page;
                this.getAssetSpares();
            },

            onPerPageChange() {
                let vm = this;
                vm.meta.page = 1;
                vm.getAssetSpares();
            },
        },
    };
</script>
<style scoped>
    .rotate {
        display: inline-block; /* Necessary for transform to work */
        transform: rotate(-90deg) translateX(-100%);
        /* Adjust positioning */
        position: relative;
        left: 100%; /* Move it into view */
        top: 50%; /* Center vertically */
    }

    .dimensions-x {
        height: 1px;
        background: #000;
    }
    .arrow-left {
        width: 0;
        height: 0;
        border-top: 3px solid transparent;
        border-bottom: 3px solid transparent;
        float: left;
        border-right: 3px solid #000;
        margin-top: -2px;
        margin-left: -3px;
    }
    .arrow-right {
        width: 0;
        height: 0;
        border-top: 3px solid transparent;
        border-bottom: 3px solid transparent;
        border-left: 3px solid #000;
        float: right;
        margin-top: -2px;
        margin-right: -3px;
    }
    .dimensions-y {
        width: 1px;
        background: #000;
        margin-left: 5px;
        display: inline-block;
        /* position: relative; */
        position: absolute;
    }
    .arrow-top {
        width: 0;
        height: 0;
        border-left: 3px solid transparent;
        border-right: 3px solid transparent;
        border-bottom: 3px solid #000;
        margin-left: -2px;
        position: absolute;
    }
    .arrow-bottom {
        width: 0;
        height: 0;
        border-left: 3px solid transparent;
        border-right: 3px solid transparent;
        border-top: 3px solid #000;
        margin-left: -2px;
        position: absolute;
        bottom: 0px;
    }
    .vertical-text {
        writing-mode: vertical-lr;
        text-orientation: mixed;
    }
    .mr-0 {
        margin-right: 0px;
    }
    .zone-container {
        position: relative;
    }

    .zone-name-container {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 105%;
        /* transform: translateX(-50%); */
        z-index: 1;
        display: flex;
        /* align-items: center;
  justify-content: center; */
        border-right: 1px solid gray;
        /* padding: 10px;
  padding-right: 0px; */
    }

    /* Arrow at the top */
    .zone-name-container::before {
        content: "";
        position: absolute;
        right: -6px; /* Slightly outside the border */
        top: 0; /* Positioned at the top of the container */
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-bottom: 6px solid gray; /* Downward-facing triangle */
    }

    /* Arrow at the bottom */
    .zone-name-container::after {
        content: "";
        position: absolute;
        right: -6px; /* Slightly outside the border */
        bottom: 0; /* Positioned at the bottom of the container */
        width: 0;
        height: 0;
        border-left: 6px solid transparent;
        border-right: 6px solid transparent;
        border-top: 6px solid gray; /* Upward-facing triangle */
    }

    .vertical-text1 {
        /* transform: rotate(90deg); */
        white-space: nowrap;
        /* font-weight: bold; */
        color: gray;
        text-align: center;
        position: absolute;
        left: inherit;
    }

    .zone-name-container2 {
        position: absolute;
        /* top: 0;
        bottom: 0; */
        right: 100%;
        transform: translateX(-50%);
        z-index: 1;
        /* display: flex; */
    }
    .vertical-text2 {
        /* transform: rotate(90deg); */
        white-space: nowrap;
        color: orange;
        text-align: center;
    }

    .ss {
        box-sizing: border-box;
        margin: 0 auto; /* Center horizontally */
        padding: 0;
        width: 100%; /* Ensure full-width */
        display: flex;
        flex-direction: column; /* Stack child elements */
        align-items: center;
        justify-content: center;
    }
</style>

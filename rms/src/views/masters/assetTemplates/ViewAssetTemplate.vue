<template>
    <div class="">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <div>
                <ol class="breadcrumb fs-sm mb-1">
                    <li class="breadcrumb-item" aria-current="page">
                        <router-link to="/dashboard">Dashboard</router-link>
                    </li>
                    <li class="breadcrumb-item"><router-link to="/asset_templates">Asset Templates</router-link></li>
                    <li class="breadcrumb-item active" aria-current="page">View</li>
                </ol>
                <h4 class="main-title mb-0">Asset Templates</h4>
            </div>
            <router-link to="/asset_templates" type="submit" class="btn btn-primary" style="float: right;"><i class="ri-list-check"></i> ASSET TEMPLATES</router-link>
        </div>
        <div class="row g-2">
            <div class="col-xl-3 mb-1">
                <div class="row">
                    <div class="col-12 mb-2">
                        <div class="card card-one">
                            <div class="card-header d-flex justify-content-between">
                                <h6 class="card-title">{{asset?.template_code}}</h6>
                            </div>
                            <div class="card-body">
                                <nav class="nav nav-classic">
                                    <span class="nav-link text-dark"><span>Template Code</span> <span class="badge text-dark" style="text-wrap: balance;">{{asset.template_code}}</span></span>
                                    <span class="nav-link text-dark"><span>Template Name</span> <span class="badge text-dark">{{asset.template_name}}</span></span>
                                    <span class="nav-link text-dark"><span>Scanner Code</span> <span class="badge text-dark">{{asset?.scanner_code}}</span></span>
                                    <span class="nav-link text-dark"><span>PPMS Code</span> <span class="badge text-dark">{{asset?.ppms_code}}</span></span>
                                    <span class="nav-link text-dark"><span>Asset Type</span> <span class="badge text-dark">{{asset.asset_type?.asset_type_name}}</span></span>
                                    <span class="nav-link text-dark"><span>Latitude</span> <span class="badge text-dark">{{asset.latitude}}</span></span>
                                    <span class="nav-link text-dark"><span>Longitude</span> <span class="badge text-dark">{{asset.longitude}}</span></span>
                                    <span class="nav-link text-dark"><span>Radius</span> <span class="badge text-dark">{{asset.radius}}</span></span>
                                    <span class="nav-link text-dark"><span>Phase</span> <span class="badge text-dark">{{asset?.phase?.phase_name}}</span></span>
                                    <span class="nav-link text-dark">
                                        <span>Department</span>
                                        <span class="" style="margin-left: auto; padding-right: 5px;">
                                            <ul class="list_style_none mb-0 text-end">
                                                <li v-if="asset?.asset_department_ids?.length === 0">No departments</li>
                                                <li v-for="department in asset.asset_department_ids" :key="department.id">
                                                    {{ department.department?.department_name }}
                                                </li>
                                            </ul>
                                        </span>
                                    </span>
                                    <span class="nav-link text-dark"><span>Functional</span> <span class="badge text-dark">{{asset.functional?.functional_name}}</span></span>
                                    <span class="nav-link text-dark" v-for="(zone, index) in asset.zone_name" :key="zone.template_zone_id">
                                        <span>Zone {{ index + 1 }}</span>
                                        <span class="badge text-dark">{{ zone.zone_name }}</span>
                                    </span>
                                    <h6 class="nav-link text-dark d-flex justify-content-between" v-for="asset_attribute, key in asset.asset_attributes" :key="key">
                                        {{ asset_attribute.display_name}}
                                        <span v-if="asset_attribute.field_name == 'Color'" :style="getColor(asset_attribute)">{{ asset_attribute?.asset_attribute_value?.field_value }}</span>
                                        <span v-else>{{ asset_attribute?.asset_attribute_value?.field_value }}</span>
                                    </h6>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-9 mb-2">
                <div class="card1 card-one1">
                    <div class="card-content1">
                        <ul class="nav nav-tabs nav-tabs-mb-icon nav-tabs-card">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" ref="services" href="#services" @click="openTab('services')">
                                    <em class="icon ni ni-web-development"></em>
                                    <span>Services</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" ref="checks" @click="openTab('checks')" href="#checks">
                                    <em class="icon ni ni-web-development"></em>
                                    <span>Checks</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" ref="spares" href="#spares" @click="openTab('spares')">
                                    <em class="icon ni ni-web-development"></em>
                                    <span>Spares</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" ref="variables" @click="openTab('variables')" href="#variables">
                                    <em class="icon ni ni-web-development"></em>
                                    <span>Variables</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" ref="data_sources" @click="openTab('data_sources')" href="#data_sources">
                                    <em class="icon ni ni-web-development"></em>
                                    <span>Data Sources</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" ref="accessories" @click="openTab('accessories')" href="#accessories">
                                    <em class="icon ni ni-web-development"></em>
                                    <span>Accessories</span>
                                </a>
                            </li>
                        </ul>
                        <div class="card-inner">
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="services">
                                    <Services ref="services_tab" v-if="asset.asset_template_id && tab==='services'" :asset_template_id="asset.asset_template_id" ></Services>
                                </div>
                                <div class="tab-pane fade" id="checks">
                                    <Checks ref="checks_tab" v-if="asset.asset_template_id && tab==='checks'" :asset_template_id="asset.asset_template_id"></Checks>
                                </div>
                                <div class="tab-pane fade" id="spares">
                                    <Spares ref="spares_tab" v-if="asset.asset_template_id && tab==='spares'" :asset_template_id="asset.asset_template_id"></Spares>
                                </div>
                                <div class="tab-pane fade" id="variables">
                                    <Variables ref="variables_tab" v-if="asset.asset_template_id && tab==='variables'" :asset_template_id="asset.asset_template_id"></Variables>
                                </div>
                                <div class="tab-pane fade" id="data_sources">
                                    <DataSources ref="data_sources_tab" v-if="asset.asset_template_id && tab==='data_sources'" :asset_template_id="asset.asset_template_id"></DataSources>
                                </div>
                                <div class="tab-pane fade" id="accessories">
                                    <Accessories ref="accessories_tab" v-if="asset.asset_template_id && tab==='accessories'" :asset_template_id="asset.asset_template_id"></Accessories>
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
    import Services from "@/components/AssetTemplateView/Services.vue";
    import Accessories from "@/components/AssetTemplateView/Accessories.vue";
    import DataSources from "@/components/AssetTemplateView/DataSources.vue";
    import Variables from "@/components/AssetTemplateView/Variables.vue";
    import Checks from "@/components/AssetTemplateView/Checks.vue";
    import Spares from "@/components/AssetTemplateView/Spares.vue";
    import axios from "axios";
    import Search from "@/components/Search.vue";
    import Pagination from "@/components/Pagination.vue";
    import MultiSelect from "primevue/multiselect";
    export default {
        name: "AssetTemplates.View",
        components: { Accessories, DataSources, Services, Variables, Checks, Spares, Search, Pagination, MultiSelect },
        data() {
            return {
                tab: "services",
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
                asset_zones: [],
            };
        },

        beforeRouteEnter(to, from, next) {
            next((vm) => {
                document.body.classList.add("sidebar-hide");
                vm.asset.asset_template_id = to.params.asset_template_id;
                let uri = { uri: "getAssetTemplate", data: { asset_template_id: to.params.asset_template_id } };
                vm.$store
                    .dispatch("post", uri)
                    .then(function (response) {
                        vm.asset = response.data.data;
                        console.log("ASSSS=", vm.asset);
                        // vm.asset_zones = response.data.zones;
                        vm.asset_zones = response?.data?.data?.zone_name;
                        // vm.getAssetSpares();
                    })
                    .catch(function (error) {
                        console.log("Error", error);
                        // vm.errors = error.response.data.errors;
                        // vm.$store.dispatch("error", error.response.data.message);
                    });
            });
        },
        mounted() {
            document.body.classList.add("sidebar-hide");
            this.getSettings();
        },
        methods: {
            getSettings() {
                let vm = this;
                vm.openTab(vm.tab);
            },
            openTab(tab) {
                let vm = this;
                vm.tab = tab;
                {
                    if (vm.tab == "services") {
                        vm.$refs.services_tab;
                    }
                    if (vm.tab == "checks") {
                        vm.$refs.checks_tab;
                    }
                    if (vm.tab == "spares") {
                        vm.$refs.spares_tab;
                    }
                    if (vm.tab == "variables") {
                        vm.$refs.variables_tab;
                    }
                    if (vm.tab == "data_sources") {
                        vm.$refs.data_sources_tab;
                    }
                    if (vm.tab == "accessories") {
                        vm.$refs.accessories_tab;
                    }
                }
            },
        },
    };
</script>
<style scoped>
    .list_style_none {
        list-style: none;
    }
    .department-badge {
        display: inline-block;
        max-width: 200px;
        overflow: hidden;
        white-space: wrap;
    }
    .badge {
        /* color: #6e7985; */
        font-size: 14px;
        opacity: inherit;
        margin-left: auto;
    }

    .border-left {
        border-top-left-radius: 0px;
        border-bottom-left-radius: 0px;
    }

    /* multi seleect css */

    .dropdown {
        position: relative;
        cursor: pointer;
    }
    .multiselect {
        position: relative;
    }
    .multiselect ul {
        border: 1px solid #ddd;
        border-top: 0;
        border-radius: 0 0 3px 3px;
        left: 0px;
        padding: 8px 8px;
        top: -0.1rem;
        width: 100%;
        list-style: none;
        max-height: 150px;
        overflow: auto;
        background: white;
    }
    .overselect {
        position: absolute;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
    }



.nav-tabs.nav-tabs-card {
        display: flex;
        width: 100%;
    }

    .nav-tabs.nav-tabs-card .nav-item {
        flex: 1;
        text-align: center;
        background-color:white;
    }

    .nav-tabs.nav-tabs-card .nav-link {
        width: 100%;
         padding: 10px;
        /* background-color: yellow; */
    }
    .nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
    color: blue;
    font-weight: 500;
    background-color: #ebf0f7;
}
.nav-link {
    color: black;
    box-shadow: 0 1px 2px rgba(204, 210, 218, 0.07), 0 2px 4px rgba(204, 210, 218, 0.07), 0 4px 8px rgba(204, 210, 218, 0.07), 0 8px 16px rgba(204, 210, 218, 0.07), 0 16px 32px rgba(204, 210, 218, 0.07), 0 32px 64px rgba(204, 210, 218, 0.07);

}
</style>

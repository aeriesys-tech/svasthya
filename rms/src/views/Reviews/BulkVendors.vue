<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <div>
                <ol class="breadcrumb fs-sm mb-1">
                    <li class="breadcrumb-item"><router-link to="/dashboard">Dashboard</router-link></li>
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Registers</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Update Vendors</li>
                </ol>
                <h4 class="main-title mb-0">Update Vendors</h4>
            </div>
        </div>

        <div class="card card-one">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="vendorTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link"
                            :class="{ active: activeTab === 'inspection' }"
                            @click="activeTab = 'inspection'"
                            type="button"
                            role="tab"
                        >
                            Inspection Vendors
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link"
                            :class="{ active: activeTab === 'spare' }"
                            @click="activeTab = 'spare'"
                            type="button"
                            role="tab"
                        >
                            Spare Vendors
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content" id="vendorTabsContent">
                    <!-- ====================== INSPECTION TAB ====================== -->
                    <div class="tab-pane fade" :class="{ 'show active': activeTab === 'inspection' }" role="tabpanel">
                        <form @submit.prevent="submitInspectionForm">
                            <div class="row g-3">
                                <!-- Vendor -->
                                <div class="col-md-6">
                                    <label class="form-label">Vendor <span class="text-danger">*</span></label>
                                    <select
                                        class="form-select align-control"
                                        :class="{ 'is-invalid': inspectionErrors.vendor }"
                                        v-model="inspection.vendor"
                                        id="vendor_inspection"
                                    >
                                        <option value="" disabled>Select Vendor</option>
                                        <option v-for="v in vendors" :key="v" :value="v">{{ v }}</option>
                                    </select>
                                    <small v-if="inspectionErrors.vendor" class="text-danger"
                                        >{{ inspectionErrors.vendor[0] }}</small
                                    >
                                </div>

                                <!-- Service -->
                                <div class="col-md-6">
                                    <label class="form-label">Service <span class="text-danger">*</span></label>
                                    <search
                                        :class="{ 'is-invalid': inspectionErrors.service_id }"
                                        :customClass="{ 'is-invalid': inspectionErrors.service_id }"
                                        :initialize="inspection.service_id"
                                        id="service_id"
                                        label="service_name"
                                        placeholder="Select Service"
                                        :data="services"
                                        @input="onServiceSelect"
                                    />
                                    <small v-if="inspectionErrors.service_id" class="text-danger"
                                        >{{ inspectionErrors.service_id[0] }}</small
                                    >
                                </div>

                                <!-- Assets -->
                                <div class="col-12">
                                    <label class="form-label">Assets <span class="text-danger">*</span></label>
                                    <div v-if="inspection_assets.length" class="row g-2">
                                        <div v-for="a in inspection_assets" :key="a.asset_id" class="col-md-4 col-sm-6">
                                            <label class="d-flex align-items-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="inspection.assets_obj"
                                                    :value="a"
                                                    class="me-2 custom-checkbox"
                                                />
                                                {{ a.asset_name }}
                                            </label>
                                        </div>
                                    </div>
                                    <div v-else class="text-muted">No assets available. Please select a service.</div>
                                    <small v-if="inspectionErrors.assets" class="text-danger"
                                        >{{ inspectionErrors.assets[0] }}</small
                                    >
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="button" @click="resetInspection" class="btn btn-secondary me-2">Reset</button>
                                <button type="submit" class="btn btn-primary">Update Inspection Vendors</button>
                            </div>
                        </form>
                    </div>

                    <!-- ====================== SPARE TAB ====================== -->
                    <div class="tab-pane fade" :class="{ 'show active': activeTab === 'spare' }" role="tabpanel">
                        <form @submit.prevent="submitSpareForm">
                            <div class="row g-3">
                                <!-- Vendor -->
                                <div class="col-md-4">
                                    <label class="form-label">Vendor <span class="text-danger">*</span></label>
                                    <select
                                        class="form-select align-control"
                                        :class="{ 'is-invalid': spareErrors.vendor }"
                                        v-model="spare_list.vendor"
                                        id="vendor_spare"
                                    >
                                        <option value="" disabled>Select Vendor</option>
                                        <option v-for="v in vendors" :key="v" :value="v">{{ v }}</option>
                                    </select>
                                    <small v-if="spareErrors.vendor" class="text-danger"
                                        >{{ spareErrors.vendor[0] }}</small
                                    >
                                </div>

                                <!-- Service - ORIGINAL STYLE (NO WRAPPER) -->
                                <div class="col-md-4">
                                    <label class="form-label">Service <span class="text-danger">*</span></label>
                                    <search
                                        :class="{ 'is-invalid': spareErrors.service_id }"
                                        :customClass="{ 'is-invalid': spareErrors.service_id }"
                                        :initialize="spare_list.service_id"
                                        id="service_id"
                                        label="service_name"
                                        placeholder="Select Service"
                                        :data="spare_services"
                                        @input="onSpareSelect"
                                    />
                                    <small v-if="spareErrors.service_id" class="text-danger"
                                        >{{ spareErrors.service_id[0] }}</small
                                    >
                                </div>

                                <!-- Assets (MultiSelect) - ALIGNED -->
                                <div class="col-md-4">
                                    <label class="form-label">Assets <span class="text-danger">*</span></label>
                                    <MultiSelect
                                        v-model="spare_list.selected_assets"
                                        :options="spare_assets"
                                        optionLabel="asset_name"
                                        optionValue="asset_id"
                                        :class="{ 'is-invalid': spareErrors.selected_assets }"
                                        class="multiselect-fix"
                                        id="assets_spare"
                                        placeholder="Select Assets"
                                        :dataKey="'asset_id'"
                                        display="chip"
                                        :filter="true"
                                        :showClear="true"
                                        :maxSelectedLabels="2"
                                        selectedItemsLabel="{0} assets selected"
                                        appendTo="body"
                                        @change="onMultiAssetsChange"
                                    />
                                    <small v-if="spareErrors.selected_assets" class="text-danger"
                                        >{{ spareErrors.selected_assets[0] }}</small
                                    >
                                </div>

                                <!-- Spares + Select All -->
                                <div v-if="asset_spares.length" class="col-12">
                                    <label class="form-label">Select Spares <span class="text-danger">*</span></label>

                                    <!-- SELECT ALL -->
                                    <div class="mb-3">
                                        <label class="d-flex align-items-center fw-bold text-primary">
                                            <input
                                                type="checkbox"
                                                :checked="allSparesSelected"
                                                :indeterminate="someSparesSelected && !allSparesSelected"
                                                @change="toggleSelectAll"
                                                class="me-2 custom-checkbox"
                                            />
                                            <span>Select All Spares</span>
                                        </label>
                                    </div>

                                    <div class="row g-2">
                                        <div
                                            v-for="s in asset_spares"
                                            :key="s.asset_spare_id"
                                            class="col-md-4 col-sm-6"
                                        >
                                            <label class="d-flex align-items-center">
                                                <input
                                                    type="checkbox"
                                                    v-model="spare_list.selected_spares"
                                                    :value="s"
                                                    class="me-2 custom-checkbox"
                                                />
                                                <div class="fw-medium">{{ s.spare?.spare_name || 'N/A' }}</div>
                                            </label>
                                        </div>
                                    </div>

                                    <small v-if="spareErrors.selected_spares" class="text-danger"
                                        >{{ spareErrors.selected_spares[0] }}</small
                                    >
                                </div>

                                <div
                                    v-else-if="spare_list.selected_assets && spare_list.selected_assets.length"
                                    class="col-12 text-muted"
                                >
                                    No spares available for the selected assets.
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="button" @click="resetSpare" class="btn btn-secondary me-2">Reset</button>
                                <button type="submit" class="btn btn-primary">Update Spare Vendors</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Search from "@/components/Search.vue";
    import MultiSelect from "primevue/multiselect";

    export default {
        components: { Search, MultiSelect },
        data() {
            return {
                activeTab: "inspection",
                vendors: [],
                services: [],
                spare_services: [],
                inspection: { vendor: "", service_id: "", assets_obj: [] },
                inspection_assets: [],
                inspectionErrors: {},
                spare_list: { vendor: "", service_id: "", selected_assets: [], selected_spares: [] },
                spare_assets: [],
                asset_spares: [],
                spareErrors: {},
                errors: "",
            };
        },

        computed: {
            allSparesSelected() {
                return (
                    this.asset_spares.length > 0 &&
                    this.asset_spares.every((s) =>
                        this.spare_list.selected_spares.some((sel) => sel.asset_spare_id === s.asset_spare_id)
                    )
                );
            },
            someSparesSelected() {
                return this.spare_list.selected_spares.length > 0;
            },
        },

        beforeRouteEnter(to, from, next) {
            next((vm) => vm.userVendors());
        },

        methods: {
            userVendors() {
                const l = this.$loading.show();
                this.$store
                    .dispatch("post", { uri: "userVendors" })
                    .then((r) => {
                        l.hide();
                        this.vendors = r.data.vendors || [];
                        this.getServices();
                        this.getSpares();
                    })
                    .catch((e) => {
                        l.hide();
                        this.$store.dispatch("error", e?.response?.data?.message || "Failed");
                    });
            },

            getServices() {
                const l = this.$loading.show();
                this.$store
                    .dispatch("post", { uri: "getAssetVendorServices" })
                    .then((r) => {
                        l.hide();
                        this.services = r.data.data || [];
                    })
                    .catch((e) => {
                        l.hide();
                        this.$store.dispatch("error", e?.response?.data?.message || "Failed");
                    });
            },

            getSpares() {
                const l = this.$loading.show();
                this.$store
                    .dispatch("post", { uri: "getAssetVendorSpares" })
                    .then((r) => {
                        l.hide();
                        this.spare_services = r.data.data || [];
                    })
                    .catch((e) => {
                        l.hide();
                        this.$store.dispatch("error", e?.response?.data?.message || "Failed");
                    });
            },

            onServiceSelect(id) {
                this.inspection.service_id = id;
                if (!id) {
                    this.inspection_assets = [];
                    this.inspection.assets_obj = [];
                    return;
                }
                const l = this.$loading.show();
                this.$store
                    .dispatch("post", { uri: "getAssetVendorServiceAssets", data: { service_id: id } })
                    .then((r) => {
                        l.hide();
                        this.inspection_assets = r.data.data || [];
                        this.inspection.assets_obj = [];
                    })
                    .catch((e) => {
                        l.hide();
                        this.$store.dispatch("error", e?.response?.data?.message || "Failed");
                    });
            },

            onSpareSelect(id) {
                this.spare_list.service_id = id;
                if (!id) {
                    this.spare_assets = [];
                    this.spare_list.selected_assets = [];
                    this.spare_list.selected_spares = [];
                    this.asset_spares = [];
                    return;
                }
                const l = this.$loading.show();
                this.$store
                    .dispatch("post", { uri: "getAssetVendorSpareAssets", data: { service_id: id } })
                    .then((r) => {
                        l.hide();
                        this.spare_assets = r.data.data || [];
                        this.spare_list.selected_assets = [];
                        this.spare_list.selected_spares = [];
                        this.asset_spares = [];
                    })
                    .catch((e) => {
                        l.hide();
                        this.$store.dispatch("error", e?.response?.data?.message || "Failed");
                    });
            },

            onMultiAssetsChange() {
                if (
                    !this.spare_list.selected_assets ||
                    !this.spare_list.selected_assets.length ||
                    !this.spare_list.service_id
                ) {
                    this.asset_spares = [];
                    this.spare_list.selected_spares = [];
                    return;
                }

                const assetIds = this.spare_list.selected_assets;
                const l = this.$loading.show();
                this.$store
                    .dispatch("post", {
                        uri: "getAssetVendorAssetspares",
                        data: { asset_ids: assetIds, service_id: this.spare_list.service_id },
                    })
                    .then((r) => {
                        l.hide();
                        if (r.data && r.data.asset_spares) {
                            const uniqueSpares = r.data.asset_spares.filter(
                                (spare, index, self) =>
                                    index === self.findIndex((s) => s.asset_spare_id === spare.asset_spare_id)
                            );
                            this.asset_spares = uniqueSpares;
                            this.spare_list.selected_spares = this.spare_list.selected_spares.filter((sel) =>
                                uniqueSpares.some((s) => s.asset_spare_id === sel.asset_spare_id)
                            );
                        } else {
                            this.asset_spares = [];
                            this.spare_list.selected_spares = [];
                        }
                    })
                    .catch((e) => {
                        l.hide();
                        this.$store.dispatch("error", e?.response?.data?.message || "Failed");
                        this.asset_spares = [];
                        this.spare_list.selected_spares = [];
                    });
            },

            toggleSelectAll(e) {
                const checked = e.target.checked;
                if (checked) {
                    const missing = this.asset_spares.filter(
                        (s) => !this.spare_list.selected_spares.some((sel) => sel.asset_spare_id === s.asset_spare_id)
                    );
                    this.spare_list.selected_spares.push(...missing);
                } else {
                    this.spare_list.selected_spares = this.spare_list.selected_spares.filter(
                        (sel) => !this.asset_spares.some((s) => s.asset_spare_id === sel.asset_spare_id)
                    );
                }
            },

            submitInspectionForm() {
                this.inspectionErrors = {};
                if (!this.inspection.vendor || !this.inspection.service_id || !this.inspection.assets_obj.length) {
                    this.inspectionErrors = {
                        vendor: !this.inspection.vendor ? ["Vendor is required."] : undefined,
                        service_id: !this.inspection.service_id ? ["Service is required."] : undefined,
                        assets: !this.inspection.assets_obj.length ? ["At least one asset is required."] : undefined,
                    };
                    this.$store.dispatch("error", "Please fill all required fields.");
                    return;
                }

                const payload = {
                    vendor: this.inspection.vendor,
                    service_id: this.inspection.service_id,
                    assets: this.inspection.assets_obj.map((a) => a.asset_id),
                };
                const l = this.$loading.show();
                this.$store
                    .dispatch("post", { uri: "bulkUpdateInspectionVendors", data: payload })
                    .then((r) => {
                        l.hide();
                        this.$store.dispatch("success", r.data.message || "Updated");
                        this.resetInspection();
                    })
                    .catch((e) => {
                        l.hide();
                        this.$store.dispatch("error", e?.response?.data?.message || "Failed");
                    });
            },

            submitSpareForm() {
                this.spareErrors = {};
                if (
                    !this.spare_list.vendor ||
                    !this.spare_list.service_id ||
                    !this.spare_list.selected_assets ||
                    !this.spare_list.selected_assets.length ||
                    !this.spare_list.selected_spares.length
                ) {
                    this.spareErrors = {
                        vendor: !this.spare_list.vendor ? ["Vendor is required."] : undefined,
                        service_id: !this.spare_list.service_id ? ["Service is required."] : undefined,
                        selected_assets:
                            !this.spare_list.selected_assets || !this.spare_list.selected_assets.length
                                ? ["At least one asset is required."]
                                : undefined,
                        selected_spares: !this.spare_list.selected_spares.length
                            ? ["At least one spare must be selected."]
                            : undefined,
                    };
                    this.$store.dispatch("error", "Please fill all required fields.");
                    return;
                }

                const payload = {
                    vendor: this.spare_list.vendor,
                    service_id: this.spare_list.service_id,
                    asset_ids: this.spare_list.selected_assets,
                    asset_spare_ids: this.spare_list.selected_spares.map((s) => s.asset_spare_id),
                    spare_ids: this.spare_list.selected_spares.map((s) => s.spare_id),
                };
                const l = this.$loading.show();
                this.$store
                    .dispatch("post", { uri: "bulkUpdateSpareVendors", data: payload })
                    .then((r) => {
                        l.hide();
                        this.$store.dispatch("success", r.data.message || "Updated");
                        this.resetSpare();
                    })
                    .catch((e) => {
                        l.hide();
                        this.$store.dispatch("error", e?.response?.data?.message || "Failed");
                    });
            },

            resetInspection() {
                this.inspection = { vendor: "", service_id: "", assets_obj: [] };
                this.inspection_assets = [];
                this.inspectionErrors = {};
            },
            resetSpare() {
                this.spare_list = { vendor: "", service_id: "", selected_assets: [], selected_spares: [] };
                this.spare_assets = [];
                this.asset_spares = [];
                this.spareErrors = {};
            },
        },
    };
</script>

<style scoped>
    .nav-tabs .nav-link.active {
        background-color: #0d6efd !important;
        color: #fff !important;
        border-color: #0d6efd #0d6efd #0d6efd !important;
    }
    .nav-tabs .nav-link {
        color: #6c757d;
        border: 1px solid transparent;
        border-radius: 0.375rem 0.375rem 0 0;
        transition: 0.3s;
    }
    .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
        color: #495057;
    }
    .nav-tabs .nav-link.active:hover {
        background-color: #0b5ed7 !important;
    }

    .tab-content {
        position: relative;
        overflow: visible;
    }
    .tab-pane {
        position: relative;
        overflow: visible;
    }
    .card-body {
        position: relative;
        overflow: visible;
    }

    .tab-pane :deep(.search) {
        position: relative;
        z-index: 10;
    }
    .tab-pane :deep(.search-dropdown) {
        z-index: 9999 !important;
    }

    .align-control {
        height: 38px !important;
        min-height: 38px;
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        background-color: #fff;
    }

    .multiselect-fix {
        width: 100%;
    }
    .multiselect-fix :deep(.p-multiselect) {
        min-height: 38px;
        font-size: 0.875rem;
        padding: 0;
        border-radius: 0.375rem;
        border: 1px solid #ced4da;
        background-color: #fff;
        transition:
            border-color 0.15s ease-in-out,
            box-shadow 0.15s ease-in-out;
        position: relative;
    }
    .multiselect-fix :deep(.p-multiselect:hover) {
        border-color: #86b7fe;
    }
    .multiselect-fix :deep(.p-multiselect:not(.p-disabled).p-focus) {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        outline: 0;
    }
    .multiselect-fix :deep(.p-multiselect-label) {
        padding: 0.375rem 0.75rem;
        line-height: 1.5;
        min-height: 38px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.25rem;
        cursor: pointer;
        width: 100%;
    }
    .multiselect-fix :deep(.p-multiselect-label-container) {
        flex: 1;
        overflow: visible;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0;
    }
    .multiselect-fix :deep(.p-multiselect-token) {
        padding: 0.125rem 0.5rem;
        margin: 0.125rem 0.25rem 0.125rem 0;
        background: #f8f9fa;
        color: #495057;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        font-size: 0.8125rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .multiselect-fix :deep(.p-multiselect-token-label) {
        line-height: 1.2;
        color: #495057;
    }
    .multiselect-fix :deep(.p-multiselect-token-icon) {
        margin-left: 0.25rem;
        cursor: pointer;
        color: #6c757d;
    }
    .multiselect-fix :deep(.p-multiselect-token-icon:hover) {
        color: #495057;
    }
    .multiselect-fix :deep(.p-multiselect-label-container > span) {
        margin-left: 0.25rem;
        color: #6c757d;
        font-size: 0.8125rem;
        font-weight: 500;
        white-space: nowrap;
    }
    .multiselect-fix :deep(.p-multiselect-trigger) {
        width: 2.5rem;
        border-left: 1px solid #ced4da;
        cursor: pointer;
        flex-shrink: 0;
    }
    .multiselect-fix :deep(.p-multiselect-trigger:hover) {
        background-color: #f8f9fa;
    }
    .multiselect-fix :deep(.p-multiselect-panel) {
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        margin-top: 0.25rem;
        z-index: 9999 !important;
    }
    .multiselect-fix :deep(.p-multiselect-header) {
        padding: 0.5rem;
        border-bottom: 1px solid #dee2e6;
        background: #f8f9fa;
    }
    .multiselect-fix :deep(.p-multiselect-filter-container) {
        margin: 0;
    }
    .multiselect-fix :deep(.p-multiselect-filter-container .p-inputtext) {
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
    }
    .multiselect-fix :deep(.p-multiselect-items-wrapper) {
        max-height: 200px;
        overflow-y: auto;
    }
    .multiselect-fix :deep(.p-multiselect-item) {
        padding: 0.5rem 0.75rem;
        margin: 0;
        border-radius: 0;
        transition: background-color 0.15s ease-in-out;
    }
    .multiselect-fix :deep(.p-multiselect-item:hover) {
        background-color: #f8f9fa;
    }
    .multiselect-fix :deep(.p-multiselect-item.p-highlight) {
        background-color: #0d6efd;
        color: #fff;
    }
    .multiselect-fix :deep(.p-multiselect-item.p-highlight:hover) {
        background-color: #0b5ed7;
    }
    .multiselect-fix :deep(.p-checkbox) {
        margin-right: 0.5rem;
    }

    .multiselect-fix.is-invalid :deep(.p-multiselect) {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    .multiselect-fix.is-invalid :deep(.p-multiselect:not(.p-disabled).p-focus) {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .custom-checkbox {
        position: relative;
        width: 1.1rem;
        height: 1.1rem;
        border: 2px solid #6c757d;
        border-radius: 0.25rem;
        background: #fff;
        cursor: pointer;
        appearance: none;
        transition:
            border-color 0.2s,
            background 0.2s;
    }
    .custom-checkbox:checked {
        background: #0d6efd;
        border-color: #0d6efd;
    }
    .custom-checkbox:checked::after {
        content: "";
        position: absolute;
        left: 0.15rem;
        top: 0.05rem;
        width: 0.35rem;
        height: 0.65rem;
        border: solid #fff;
        border-width: 0 0.15rem 0.15rem 0;
        transform: rotate(45deg);
    }
    .custom-checkbox:indeterminate {
        background: #fff;
        border-color: #6c757d;
    }
    .custom-checkbox:indeterminate::after {
        content: "";
        position: absolute;
        inset: 4px;
        background: #6c757d;
    }
    .custom-checkbox:hover {
        border-color: #0d6efd;
    }
</style>

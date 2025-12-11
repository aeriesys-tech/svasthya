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
                    <li class="breadcrumb-item active" aria-current="page">Inspection Registers</li>
                </ol>
                <h4 class="main-title mb-0">Inspection Registers</h4>
            </div>
            <div class="d-flex">
                <!-- <a :href="$store.state.apiUrl+'downloadUserCheckTemplate'" type="button" class="btn btn-success me-2"> <i class="ri-download-line"></i> DOWNLOAD TEMPLATE </a> -->
                <!-- <div class="input-group w-auto me-2">
                    <input type="file" class="form-control w-auto" aria-describedby="basic-addon2" @change="onFileChange" ref="fileInput" />
                    <span class="input-group-text">
                        <a href="javascript:void(0)" class="input-group-text1 text-white" :class="{ 'disabled': !selectedFile }" @click="handleFileUpload"><i class="ri-upload-line"></i> UPLOAD TEMPLATE</a>
                    </span>
                </div> -->
                <router-link v-can="'check_registers.create'" to="/user_check/create" class="btn btn-primary" style="float: right;"><i class="ri-list-check"></i> ADD INSPECTION REGISTER</router-link>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-one">
                    <div class="card-header d-flex justify-content-between">
                        <h6 class="card-title">Inspection Registers</h6>
                    </div>
                    <div class="card-body">
                        <input class="form-control form-control-sm mb-2" type="text" placeholder="Type keyword and press enter key" v-model="meta.search" @keypress.enter="search()" />
                        <div class="table-responsive table-responsive-sm">
                            <table class="table table-sm text-nowrap table-striped table-bordered mb-0">
                                <thead>
                                    <tr class="" style="background-color: #9b9b9b; color: white;">
                                        <th class="text-center">#</th>
                                        <th @click="sort('asset_code')">
                                            Asset
                                            <span>
                                                <i v-if="meta.keyword == 'asset_code' && meta.order_by == 'asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword == 'asset_code' && meta.order_by == 'desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('reference_no')">
                                            Reference No.
                                            <span>
                                                <i v-if="meta.keyword == 'reference_no' && meta.order_by == 'asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword == 'reference_no' && meta.order_by == 'desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('reference_date')">
                                            Reference Date & Time
                                            <span>
                                                <i v-if="meta.keyword == 'reference_date' && meta.order_by == 'asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword == 'reference_date' && meta.order_by == 'desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Asset Zone Service
                                            <span>
                                                <i v-if="meta.keyword == 'asset_zone_id' && meta.order_by == 'asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword == 'asset_zone_id' && meta.order_by == 'desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Created At
                                            <span>
                                                <i v-if="meta.keyword == 'asset_zone_id' && meta.order_by == 'asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword == 'asset_zone_id' && meta.order_by == 'desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="user_check, key in user_checks" :key="key">
                                        <td class="text-center">{{ meta.from + key }}</td>
                                        <td>{{user_check.asset?.asset_code}}</td>
                                        <td>{{user_check.reference_no}}</td>
                                        <td>{{convertDateFormat(user_check.reference_date)}}</td>
                                        <td>{{user_check?.asset_service?.asset_zone?.zone_name}} :: {{ user_check?.asset_service?.service?.service_name}}</td>
                                       <td>{{ user_check?.user?.name }} <br> {{ user_check.created_at }}</td>
                                        <td class="text-center">
                                            <a title="Edit" v-can="'check_registers.update'" href="javascript:void(0)" class="text-success me-2" @click="editUserCheck(user_check)">
                                                <i class="ri-pencil-line fs-18 lh-1"></i>
                                            </a>
                                            <a title="View" href="javascript:void(0)" @click="viewUserCheck(user_check)" class="text-primary me-2" ><i class="ri-eye-fill fs-18 lh-1"></i></a>
                                            <a title="Delete" v-can="'check_registers.delete'" href="javascript:void(0)" class="text-danger me-2" @click.prevent="deleteUserCheck(user_check)"><i class="ri-delete-bin-6-line fs-18 lh-1"></i></a>
                                        </td>
                                    </tr>
                                    <tr v-if="user_checks.length==0">
                                        <td colspan="6" class="text-center">No records found</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <select class="form-select from-select-sm width-75" v-model="meta.per_page" @change="onPerPageChange">
                                <option>10</option>
                                <option>15</option>
                                <option>20</option>
                                <option>25</option>
                                <option>30</option>
                            </select>
                            <span>Showing {{ meta.from }} to {{ meta.to }} of {{ meta.totalRows }} entries</span>
                            <Pagination :maxPage="meta.maxPage" :totalPages="meta.lastPage" :currentPage="parseInt(meta.page)" @pagechanged="onPageChange" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Pagination from "@/components/Pagination.vue";
import moment from "moment";
export default {
    name: "UserChecks.Index",
    components: {
        Pagination,
    },
    data(){
        return {
            status:true,
            meta: {
                search: "",
                order_by: "desc",
                keyword: "user_check_id",
                per_page: 10,
                totalRows: 0,
                page: 1,
                lastPage: 1,
                from: 1,
                to: 1,
                maxPage: 1,
                trashed: false,
            },
            user_checks:[],
            errors: [],
            status: true,
            selectedFile: null,
        }
    },
    beforeRouteEnter(to, from, next) {
        next((vm) => {
            if(from.name != 'UserChecks.Edit' && from.name != 'UserChecks.View'){
                vm.$store.commit("setCurrentPage", vm.meta.page)
            }else{
                vm.meta.page = vm.$store.getters.current_page
            }
        });
    },
    mounted() {
        // this.get_activity = this.$store.getters.permissions.filter(function (element) {
        //     return element.ability.ability.includes("userActivities.update") || element.ability.ability.includes("userActivities.delete");
        // });
        this.$store.commit("setAssetId", '');
        this.index();
    },
    methods: {
         convertDateFormat(date) {
                let vm = this;
            // return moment(date).format("yyyy-MM-DD");
                return moment(date).format("DD-MM-YYYY HH:mm");
            },
        index() {
            let vm = this;
            let loader = vm.$loading.show();
            vm.$store
            .dispatch("post", { uri: "paginateUserChecks", data: vm.meta })
            .then((response) => {
                loader.hide();
                vm.user_checks = response.data.data;
                vm.meta.totalRows = response.data.meta.total;
                vm.meta.from = response.data.meta.from;
                vm.meta.to = response.data.meta.to;
                vm.meta.lastPage = response.data.meta.last_page;
                vm.meta.maxPage = vm.meta.lastPage >= 3 ? 3 : vm.meta.lastPage;
            })
            .catch(function (error) {
                loader.hide();
                vm.errors = error.response.data.errors;
                vm.$store.dispatch("error", error.response.data.message);
            });
        },
        editUserCheck(user_check) {
            this.$store.commit("setCurrentPage", parseInt(this.meta.page))
            this.$router.push("/user_check/" + user_check.user_check_id + "/edit");
        },
        viewUserCheck(user_check){
            this.$store.commit("setCurrentPage", this.meta.page)
            this.$router.push("/user_check/" + user_check.user_check_id + "/view");
        },
        onFileChange(event) {
            this.selectedFile = event.target.files[0];
        },
        handleFileUpload() {
            console.log("dcdcdc");
            if (!this.selectedFile) {
                this.$store.dispatch("error", "Please select a valid file before uploading.");
                return;
            }

            const formData = new FormData();
            formData.append("file", this.selectedFile);

            this.$store
                .dispatch("post", { uri: "uploadUserCheckTemplate", data: formData })
                .then((response) => {
                    this.$store.dispatch("success", response.data.success);
                    this.selectedFile = null;
                    this.$refs.fileInput.value = "";
                    this.index();
                })
                .catch(function (error) {
                    this.$store.dispatch("error", error?.response?.data?.message || "File upload failed.");
                    console.error("Upload Error:", error);
                });
        },
        deleteUserCheck(user_check) {
            const confirmDelete = confirm("Are you sure you want to delete it ?");
            if (confirmDelete) {
                let vm = this;
                let loader = vm.$loading.show();
                vm.$store
                    .dispatch("post", { uri: "deleteUserCheck", data: user_check, })
                    .then((response) => {
                        loader.hide();
                        vm.$store.dispatch("success", response.data.message);
                        vm.index();
                    })
                    .catch(function (error) {
                        loader.hide();
                        vm.errors = error.response.data.errors;
                        vm.$store.dispatch("error", error.response.data.message);
                    });
            }
        },
        search() {
            let vm = this;
            vm.meta.page = 1;
            vm.index();
        },

        onPageChange(page) {
            this.meta.page = page;
            this.index();
        },
        sort(field) {
            this.meta.keyword = field;
            this.meta.order_by = this.meta.order_by == "asc" ? "desc" : "asc";
            this.index();
        },
        onPerPageChange() {
            let vm = this;
            vm.meta.page = 1;
            vm.index();
        },
    }
}
</script>

<style scoped>
    .input-group > :not(:first-child):not(.dropdown-menu):not(.valid-tooltip):not(.valid-feedback):not(.invalid-tooltip):not(.invalid-feedback) {
        margin-left: 0px;
        border-top-left-radius: 0;
        border-bottom-left-radius: 0;
        background-color: #6e7985;  
        color: white !important;
        font-size: 0.75rem;
        line-height: 1.4;
    }

    .d-flex,
    .d-flex * {
        font-size: 0.75rem;
    }
</style>

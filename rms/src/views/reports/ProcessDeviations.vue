<template>
    <div class="">
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <div>
                <ol class="breadcrumb fs-sm mb-1">
                    <li class="breadcrumb-item" aria-current="page">
                        <router-link to="/dashboard">Dashboard</router-link></li>
                        <li class="breadcrumb-item" aria-current="page">
                            <a href="javascript:void(0)">Process Deviations</a></li>
                    <!-- <li class="breadcrumb-item active" aria-current="page">Deviations</li> -->
                </ol>
                <h4 class="main-title mb-0">Process Deviations</h4>
            </div>
            <router-link to="/completed_process_deviations" class="btn btn-primary"> Completed Process Deviations</router-link>
            <!-- <router-link v-can="'users.create'" to="/user/create" class="btn btn-primary" style="float: right;"><i
                    class="ri-list-check"></i> ADD USER</router-link> -->
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card card-one">
                   <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="card-title">Process Deviations</h6>
                        <div v-if="selectedDeviations.length > 0 && showBulkUpdateButton">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkUpdateModal" @click="prepareBulkUpdate">Update {{ selectedDeviations.length }} Deviation(s)</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-3">
                                <select class="form-control form-control-sm mb-2" v-model="meta.asset_id" @change="search()">
                                    <option value="">Select Asset</option>
                                    <option v-for="asset, key in assets" :key="key" :value="asset.asset_id">{{ asset.asset_name }}</option>
                                </select>
                            </div>
                            <div class="col-9">
                                <input class="form-control form-control-sm mb-2" type="text"
                                placeholder="Type keyword and press enter key" v-model="meta.search" @keypress.enter="search()" />
                            </div>
                        </div>

                        <div class="table-responsive table-responsive-sm">
                            <table class="table table-sm text-nowrap table-striped table-bordered mb-0">
                                <thead>
                                    <tr style="background-color:#9b9b9b;color:white;">
                                        <th class="text-center">
                                            <input type="checkbox" v-model="selectAll" @change="toggleSelectAll" />
                                        </th>
                                        <th class="text-center">#</th>
                                        <th>
                                            Asset
                                            <span>
                                                <i v-if="meta.keyword == 'asset_name' && meta.order_by == 'asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword == 'asset_name' && meta.order_by == 'desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Asset Type
                                            <span>
                                                <i v-if="meta.keyword == 'asset_type_name' && meta.order_by == 'asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword == 'asset_type_name' && meta.order_by == 'desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                           Job No.
                                            <span>
                                                <i v-if="meta.keyword == 'reference_no' && meta.order_by == 'asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword == 'reference_no' && meta.order_by == 'desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th>
                                            Job Date
                                            <span>
                                                <i v-if="meta.keyword == 'reference_date' && meta.order_by == 'asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword == 'reference_date' && meta.order_by == 'desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('field_name')">
                                            Variable
                                            <span>
                                                <i v-if="meta.keyword == 'field_name' && meta.order_by == 'asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword == 'field_name' && meta.order_by == 'desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                     
                                        <th @click="sort('lcl')">
                                            Lcl
                                            <span>
                                                <i v-if="meta.keyword == 'lcl' && meta.order_by == 'asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword == 'lcl' && meta.order_by == 'desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th @click="sort('ucl')">
                                            Ucl
                                            <span>
                                                <i v-if="meta.keyword == 'ucl' && meta.order_by == 'asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword == 'ucl' && meta.order_by == 'desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                      

                                        <th @click="sort('value')">
                                             Value
                                            <span>
                                                <i v-if="meta.keyword == 'value' && meta.order_by == 'asc'" class="ri-arrow-up-line"></i>
                                                <i v-else-if="meta.keyword == 'value' && meta.order_by == 'desc'" class="ri-arrow-down-line"></i>
                                                <i v-else class="fas fa-sort"></i>
                                            </span>
                                        </th>
                                        <th class="text-center">Action</th>
                                        <!-- <th class="text-center" v-can="'users.delete'">Status</th>
                                        <th class="text-center" v-can="'users.update'">Actions</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-if="user_variables.length==0">
                                          <td colspan="12" class="text-center">No records found</td>
                                    </tr>
                                    <tr v-for="deviation, key in user_variables" :key="key">
                                        <td class="text-center">
                                            <input type="checkbox" :value="deviation.user_asset_variable_id" v-model="selectedDeviations" @change="updateSelectAll" />
                                        </td>
                                        <td class="text-center">{{ meta.from + key }}</td>
                                        <!-- <td>
                                            {{ deviation?.asset?.asset_department_ids.map(department => department.department?.department_name).join(', ') }}
                                        </td> -->
                                        <td>{{ deviation?.asset?.asset_name}}</td>
                                        <td>{{ deviation.asset_type?.asset_type_name}}</td>
                                        <td>{{ deviation?.job_no}}</td>
                                        <td>{{ deviation?.job_date}}</td>
                                        <td>{{ deviation.variable?.variable_name}}</td>
                                        <td>{{ deviation?.lcl }}</td>
                                        <td>{{ deviation?.ucl }}</td>
                                        <td>{{ deviation?.value }}</td>
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" @click="update(deviation)"><i class="ri-add-line fs-18 lh-1"></i></button>
                                        </td>
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
                                <option>500</option>
                                <option>3000</option>
                            </select>
                            <span>Showing {{ meta.from }} to {{ meta.to }} of {{ meta.totalRows }} entries</span>
                            <Pagination :maxPage="meta.maxPage" :totalPages="meta.lastPage" :currentPage="parseInt(meta.page)" @pagechanged="onPageChange" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
     <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" ref="deviationModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Update Process Deviation</h5>
                    <button type="button" id="close" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <label class="form-label">Remarks</label><span class="text-danger"> *</span>
                        <textarea rows="2" class="form-control" type="text" placeholder="Remarks for Deviations" :class="{'is-invalid':errors.remarks}" v-model="user_variableId.remarks"></textarea>
                        <span v-if="errors.remarks" class="invalid-feedback">{{ errors.remarks[0] }}</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="updateDeviation">Update changes</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- bulk Modal -->
    <div class="modal fade" id="bulkUpdateModal" tabindex="-1" aria-labelledby="bulkUpdateModalLabel" aria-hidden="true" ref="bulkDeviationModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkUpdateModalLabel">Update Process Deviations</h5>
                    <button type="button" id="bulkClose" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div>
                        <label class="form-label">Remarks</label><span class="text-danger"> *</span>
                        <textarea rows="2" class="form-control" type="text" placeholder="Remarks for deviations" :class="{'is-invalid': bulkErrors?.remarks}" v-model="bulkRemarks"></textarea>
                        <span v-if="bulkErrors?.remarks" class="invalid-feedback">{{ bulkErrors?.remarks[0] }}</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" @click="bulkUpdateDeviation">Update Deviations</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Pagination from "@/components/Pagination.vue";
export default {
    components: {
        Pagination
    },
    data() {
        return {
            meta: {
                search: '',
                order_by: "desc",
                keyword: "user_variable_id",
                per_page: 10,
                totalRows: 0,
                page: 1,
                lastPage: 1,
                from: 1,
                to: 1,
                maxPage: 1,
                trashed: false,
                department_id:'',
                asset_id:'',
            },
            user_variableId: {
                user_asset_variable_id: "",
                remarks: "",
            },
            user_variables: [],
            errors: [],
            departments:[],
            assets:[],
            status: true,
            selectedDeviations: [],
            selectAll: false,
            bulkRemarks: "",
            showBulkUpdateButton: true,
            bulkErrors: [],
        }
    },
    // beforeRouteEnter(to, from, next) {
    //     next((vm) => {
    //         if(from.name != 'Users.Edit'){
    //             vm.$store.commit("setCurrentPage", vm.meta.page)
    //         }else{
    //             vm.meta.page = vm.$store.getters.current_page
    //         }
    //     });
    // },
    mounted() {
        this.index();
        this.getDepartments();
        this.getAssets();
    },

    methods: {
        index() {
            let vm = this;
            let loader = vm.$loading.show();
            vm.$store.dispatch('post', { uri: 'processDeviations', data: vm.meta })
                .then(response => {
                    loader.hide();
                    vm.user_variables = response.data.data;
                    vm.meta.totalRows = response.data.meta.total;
                    vm.meta.from = response.data.meta.from;
                    vm.meta.to = response.data.meta.to;
                    vm.meta.lastPage = response.data.meta.last_page;
                    vm.meta.maxPage = vm.meta.lastPage >= 3 ? 3 : vm.meta.lastPage;
                    vm.showBulkUpdateButton = true;
                    
                    vm.updateSelectAll();
                })
                .catch(function (error) {
                    loader.hide();
                    vm.errors = error.response.data.errors;
                    vm.$store.dispatch("error", error.response.data.message);
                });
        },
          update(deviation) {
            let vm = this;
            vm.user_variableId.user_asset_variable_id = deviation.user_asset_variable_id;
        },
        updateDeviation() {
            let vm = this;
            let loader = vm.$loading.show();
            vm.$store
                .dispatch("post", { uri: "updateProcessDeviation", data: vm.user_variableId })
                .then((response) => {
                    loader.hide();
                    vm.$store.dispatch("success", response.data.message);
                    let modal = vm.$refs.deviationModal;
                    modal.style.display = "none";
                    document.getElementById("close").click();
                    vm.index();
                })
                .catch(function (error) {
                    loader.hide();
                    vm.errors = error.response.data.errors;
                    vm.$store.dispatch("error", error.response.data.message);
                });
        },

         bulkUpdateDeviation() {
            let vm = this;
            let loader = vm.$loading.show();
            const deviations = vm.user_variables
                .filter((deviation) => vm.selectedDeviations.includes(deviation.user_asset_variable_id))
                .map((deviation) => ({
                    ...deviation,
                    remarks: vm.bulkRemarks,
                }));
            const data = {
                deviations: deviations,
            };
            vm.$store
                .dispatch("post", { uri: "bulkUpdateProcessDeviation", data: data })
                .then((response) => {
                    loader.hide();
                    vm.$store.dispatch("success", response.data.message);
                    let modal = vm.$refs.bulkDeviationModal;
                    modal.style.display = "none";
                    document.getElementById("bulkClose").click();
                    vm.showBulkUpdateButton = false;
                    vm.selectedDeviations = [];
                    vm.selectAll = false;
                    vm.index();
                })
                .catch(function (error) {
                    loader.hide();
                    vm.bulkErrors = error.response?.data?.errors || [];
                    vm.$store.dispatch("error", error.response?.data?.message || "An error occurred");
                });
        },

        getDepartments() {
            let vm = this;
            let loader = vm.$loading.show();
            vm.$store.dispatch('post', { uri: 'getDepartments' })
                .then(response => {
                    loader.hide();
                    vm.departments = response.data.data;
                })
                .catch(function (error) {
                    loader.hide();
                    vm.errors = error.response.data.errors;
                    vm.$store.dispatch("error", error.response.data.message);
                });
        },
        getAssets() {
            let vm = this;
            let loader = vm.$loading.show();
            vm.$store.dispatch('post', { uri: 'getAssets' })
                .then(response => {
                    loader.hide();
                    vm.assets = response.data.data;
                })
                .catch(function (error) {
                    loader.hide();
                    vm.errors = error.response.data.errors;
                    vm.$store.dispatch("error", error.response.data.message);
                });
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

        toggleSelectAll() 
        {
            if (this.selectAll) {
                this.user_variables.forEach((deviation) => {
                    if (!this.selectedDeviations.includes(deviation.user_asset_variable_id)) {
                        this.selectedDeviations.push(deviation.user_asset_variable_id);
                    }
                });
            } else {
                this.user_variables.forEach((deviation) => {
                    const index = this.selectedDeviations.indexOf(deviation.user_asset_variable_id);
                    if (index !== -1) {
                        this.selectedDeviations.splice(index, 1);
                    }
                });
            }
        },
        updateSelectAll() {
            this.selectAll =
                this.user_variables.length > 0 &&
                this.user_variables.every((deviation) =>
                    this.selectedDeviations.includes(deviation.user_asset_variable_id)
                );
        },
    }
}
</script>

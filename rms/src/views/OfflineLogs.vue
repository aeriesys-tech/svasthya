<template>
    <div>
        <div class="d-sm-flex align-items-center justify-content-between mb-2">
            <div>
                <ol class="breadcrumb fs-sm mb-1">
                    <li class="breadcrumb-item"><router-link to="/dashboard">Dashboard</router-link></li>
                    <li class="breadcrumb-item active">Uploaded Logs</li>
                </ol>
                <h4 class="main-title mb-0">Uploaded Logs</h4>
            </div>
        </div>


        <div class="card card-one">
            <div class="card-header d-flex justify-content-between">
                <h6 class="card-title">Uploaded Logs</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-2">
                        <label class="form-label">User</label>
                        <!-- <select class="form-control mb-3" v-model="meta.user_id">
                            <option value="">Select User</option>
                            <option v-for="user, key in users" :key="key" :value="user.user_id">{{ user.name }}</option>
                        </select> -->
                        <search
                            :class="{ 'is-invalid': errors?.user_id}"
                            :customClass="{ 'is-invalid': errors?.user_id}"
                            :initialize="meta.user_id"
                            id="user_id"
                            label="name"
                            placeholder="Select User"
                            :data="users"
                            @input="user_id => meta.user_id = user_id"
                        >
                        </search>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Asset</label>
                        <!-- <select class="form-control mb-3" v-model="meta.asset_id">
                            <option value="">Select Asset</option>
                            <option v-for="asset, key in assets" :key="key" :value="asset.asset_id">{{ asset.asset_name }}</option>
                        </select> -->
                         <search
                            :class="{ 'is-invalid': errors?.asset_id}"
                            :customClass="{ 'is-invalid': errors?.asset_id}"
                            :initialize="meta.asset_id"
                            id="asset_id"
                            label="asset_name"
                            placeholder="Select Asset"
                            :data="assets"
                            @input="asset_id => meta.asset_id = asset_id"
                        >
                        </search>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Register</label>
                        <select class="form-control mb-3" v-model="meta.register" >
                            <option value="">Select Register</option>
                            <option value="Activity Register">Activity Register</option>
                            <option value="Inspection Register">Inspection Register</option>
                            <option value="Spare Register">Spare Register</option>
                        </select>
                    </div>
                    <div class="col-2">
                        <label class="form-label">Status</label>
                        <select class="form-control mb-3" v-model="meta.status">
                            <option value="">Select Status</option>
                            <option :value="true">Success</option>
                            <option :value="false">Failed</option>
                        </select>
                    </div>
                    <div class="col-2">
                        <label class="form-label">From Date</label>
                        <input class="form-control mb-3" type="date" v-model="meta.from_date">
                    </div>
                    <div class="col-2">
                        <label class="form-label">To Date</label>
                        <input class="form-control mb-3" type="date" v-model="meta.to_date">
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <div>
                            <button class="btn btn-primary me-2" @click="search">Search</button>
                            <button class="btn btn-danger" @click="reset">Reset</button>
                        </div>
                    </div>
                </div>
                <div>
                    <input class="form-control form-control-sm mb-2 mt-2" type="text" placeholder="Type keyword and press enter key" v-model="meta.search" @keypress.enter="search" />
                </div>

                <div class="table-responsive">
                    <table class="table table-sm table-striped table-bordered text-nowrap mb-0">
                        <thead>
                            <tr style="background-color:#9b9b9b;color:white;">
                                <th class="text-center">#</th>
                                <th @click="sort('user_id')">User <SortIcon :field="'user_id'" :meta="meta" /></th>
                                <th @click="sort('asset_name')">Asset <SortIcon :field="'asset_name'" :meta="meta" /></th>
                                <th @click="sort('date_time')">Date & Time <SortIcon :field="'date_time'" :meta="meta" /></th>
                                <th @click="sort('register')">Register <SortIcon :field="'register'" :meta="meta" /></th>
                                <th @click="sort('log_type')">Log Type <SortIcon :field="'log_type'" :meta="meta" /></th>
                                <th @click="sort('status')">Status <SortIcon :field="'status'" :meta="meta" /></th>
                                <th class="text-center">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- No Data Found -->
                            <tr v-if="offline_logs.length === 0">
                                <td colspan="7" class="text-center">No records found</td>
                            </tr>
                            <!-- Logs Data -->
                            <tr v-for="(log, index) in offline_logs" :key="index">
                                <td class="text-center">{{ meta.from + index }}</td>
                                <td>{{ log.user?.name }}</td>
                                <td>{{ log.asset?.asset_name }}</td>
                                <td>{{ log?.date_time }}</td>
                                <td>{{ log?.register }}</td>
                                <td>{{ log?.log_type }}</td>
                                <td :class="log?.status ? 'text-success' : 'text-danger'" class="text-center">
                                    {{ log?.status ? 'Success' : 'Failed' }}
                                </td>
                                <td class="text-center">
                                    <i class="ri-eye-line cursor-pointer text-primary" @click="openModal(log)" data-bs-toggle="modal" data-bs-target="#logModal"></i>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            <div class="card-footer d-flex justify-content-between align-items-center">
                <select class="form-select form-select-sm w-auto" v-model="meta.per_page" @change="onPerPageChange">
                    <option>10</option>
                    <option>15</option>
                    <option>20</option>
                </select>
                <span>Showing {{ meta.from }} to {{ meta.to }} of {{ meta.totalRows }} entries</span>
                <Pagination :maxPage="meta.maxPage" :totalPages="meta.lastPage" :currentPage="meta.page" @pagechanged="onPageChange" />
            </div>
        </div>

        <!-- Modal for Request / Response Details -->
        <div class="modal fade" id="logModal" tabindex="-1" aria-labelledby="logModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="background-color: red;"></button>
                    </div>
                    <div class="modal-body row">
                        <div class="col-md-6">
                            <div class="card" style="max-height: 70vh; overflow-y: auto;">
                                <div class="card-header"><strong>Request Body</strong></div>
                                <div class="card-body"><pre>{{ formattedRequest }}</pre></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card" style="max-height: 70vh; overflow-y: auto;">
                                <div class="card-header"><strong>Response Body</strong></div>
                                <div class="card-body"><pre>{{ formattedResponse }}</pre></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import Pagination from "@/components/Pagination.vue";
 import Search from "@/components/Search.vue";

const SortIcon = {
    props: ["field", "meta"],
};

export default {
    components: { Pagination, SortIcon,Search },

    data() {
        return {
            meta: {
                search: "",
                order_by: "desc",
                keyword: "offline_log_id",
                per_page: 10,
                totalRows: 0,
                page: 1,
                lastPage: 1,
                from: 1,
                to: 1,
                maxPage: 1,
                user_id : '',
                asset_id : '',
                register : '',
                from_date : '',
                to_date : '',
                status : ''
            },
            offline_logs: [],
            request_body_items: null,
            response_body_items: null,
            users : [],
            assets: [],
        };
    },
    computed: {
        formattedRequest() {
            return JSON.stringify(this.request_body_items, null, 2);
        },
        formattedResponse() {
            return JSON.stringify(this.response_body_items, null, 2);
        },
    },
    mounted() {
        this.index();
        this.getUsers();
    },
    methods: {
        index() {
            const loader = this.$loading.show();
            this.$store
                .dispatch("post", {
                    uri: "offlineUploadedLogs",
                    data: this.meta,
                })
                .then((res) => {
                    loader.hide();
                    this.offline_logs = res.data.data;
                    const meta = res.data.meta;
                    this.meta.totalRows = meta.total;
                    this.meta.from = meta.from;
                    this.meta.to = meta.to;
                    this.meta.lastPage = meta.last_page;
                    this.meta.maxPage = this.meta.lastPage >= 3 ? 3 : this.meta.lastPage;
                })
                .catch((error) => {
                    loader.hide();
                    this.$store.dispatch("error", error.response.data.message);
                });
        },
        getUsers() {
        let vm = this;
        let loader = vm.$loading.show();
        vm.$store.dispatch('post', { uri: 'getUsers' })
            .then(response => {
                loader.hide();
                vm.users = response.data.data;
                vm.getAssets();
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
            this.meta.page = 1;
            this.index();
        },
        reset() {
            let vm = this;
            vm.meta.user_id = "";
            vm.meta.asset_id = "";
            vm.meta.status = "";
            vm.meta.register = "";
            vm.meta.from_date = "";
            vm.meta.to_date = "";
            vm.errors = [];
            vm.index();
        },
        onPageChange(page) {
            this.meta.page = page;
            this.index();
        },
        onPerPageChange() {
            this.meta.page = 1;
            this.index();
        },
        sort(field) {
            this.meta.keyword = field;
            this.meta.order_by = this.meta.order_by === "asc" ? "desc" : "asc";
            this.index();
        },
        openModal(log) {
            this.request_body_items = log.request;
            this.response_body_items = log.response;
        },
    },
};
</script>

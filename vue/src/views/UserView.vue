<template>
    <div class="container mt-4">
        <div class="card bg-gradient-8 shadow-sm mb-4">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col">
                        <p class="h6">Users</p>
                    </div>
                    <div class="col-auto">
                        <button type="button" class="btn btn-theme btn-square" @click="create()">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body pb-0">
                <div class="col-12 mb-3">
                    <DataTable 
                        :columns="usersDataTable.columns" 
                        :data="usersDataTable.data" 
                        :options="usersDataTable.options" 
                        class="table table-bordered">
                    </DataTable>
                </div>
            </div>
        </div>
    </div>

    <!-- User modal starts -->
    <div class="modal fade" id="userModal" tabindex="-1"  data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <p class="modal-title h5" id="userModalLabel">{{ isUserEditMode ? 'Edit User' : 'Add User' }}</p>
                    <button type="button" class="btn-close" @click="reset()" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row gx-3 gx-lg-4">  
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input class="form-control" :class="{'is-invalid': errors.name}" v-model="user.name" @blur="validateName()" placeholder="Name" type="text">
                                <label>Name *</label>
                                <span class="error-wrapper invalid-feedback">{{ errors.name }}</span>
                            </div>
                        </div>    
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input class="form-control" :class="{'is-invalid': errors.username}" v-model="user.username" @blur="validateUsername()" placeholder="Username" type="text">
                                <label>Username *</label>
                                <span class="error-wrapper invalid-feedback">{{ errors.username }}</span>
                            </div>
                        </div>  
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input class="form-control" :class="{'is-invalid': errors.email}" v-model="user.email" @blur="validateEmail()" placeholder="Email" type="email">
                                <label>Email *</label>
                                <span class="error-wrapper invalid-feedback">{{ errors.email }}</span>
                            </div>
                        </div>  
                        <div class="col-12">
                            <div class="position-relative">
                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control" id="checkstrength" placeholder="Enter your password" onkeyup="checkstrength()" :class="{'is-invalid': errors.password}" v-model="user.password" @blur="validatePassword()" >
                                    <label for="checkstrength">Password</label>
                                    <span class="error-wrapper invalid-feedback">{{ errors.password }}</span>
                                </div>
                            </div>
                            <div class="feedback mb-3">
                                <div class="row">
                                    <div class="col">
                                        <div class="check-strength weak" id="checksterngthdisplay">
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                            <div></div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <span class="text-danger small" id="textpassword">This is a weak</span>
                                        <i class="bi bi-info-circle text-theme ms-1" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Password should contain atleast 1 capital, 1 alphanumeric &amp; min. 8 characters" data-bs-original-title="Password should contain atleast 1 capital, 1 alphanumeric &amp; min. 8 characters"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input class="form-control" :class="{'is-invalid': errors.phone_number}" v-model="user.phone_number" @blur="validatePhoneNumber()" placeholder="PhoneNumber" type="text">
                                <label>Phone Number *</label>
                                <span class="error-wrapper invalid-feedback">{{ errors.phone_number }}</span>
                            </div>
                        </div>            
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <select class="form-select" v-model="user.user_role" :class="{'is-invalid': errors.user_role}" @blur="validateUserRole()">
                                    <option value="">Select</option>                                  
                                    <option value="Admin">Admin</option>                                  
                                    <option value="User">User</option>                                  
                                </select>
                                <label>User Role*</label>
                                <span class="error-wrapper invalid-feedback">{{ errors.user_role }}</span>
                            </div>
                        </div>
                        <div class="col-12" v-if="isUserEditMode">
                            <div class="form-floating mb-3">
                                <select class="form-select" v-model="user.status" :class="{'is-invalid': errors.status}">
                                    <option value="1">Active</option>                                  
                                    <option value="0">Inactive</option>                                  
                                </select>
                                <label>Status*</label>
                                <span class="error-wrapper invalid-feedback">{{ errors.status }}</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input type="file" class="form-control" :class="{'is-invalid': errors.photo}" placeholder="Select Photo" @change="handlePhotoChange" >
                                <label>Photo</label>
                                <span class="error-wrapper invalid-feedback">{{ errors.photo }}</span>
                            </div>
                            <small class="text-muted">Upload only .jpeg, .jpg, .png file formats. Maximum file size allowed is 4MB.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" @click="reset()" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" v-if="!isUserFormLoading" @click="isUserEditMode ? update() : store()">Submit</button>
                    <button class="btn btn-success" v-else>
                        <div class="spinner-border spinner-border-sm" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- User modal ends -->
    
    <DeleteModal ref="deleteModalRef" :name="'user'" :isLoading="isUserFormLoading" :destroy="destroy" />
</template>
<script setup>
    import { onMounted, onBeforeUnmount, ref, nextTick, inject } from 'vue';
    import { TOAST_SUCCESS, TOAST_ERROR } from '@/utils/config.js';
    import { dataTableActionsClickHandler, objectArrayTo2D } from '@/utils/dataTable';
    import { handleValidateName, handleValidateUsername, handleValidateEmail, handleValidatePassword, handleValidatePhoneNumber, handleValidateUserRole, handleValidateImage } from '@/utils/validator';
    import axios from 'axios';
    import DataTable from 'datatables.net-vue3';
    import DataTablesCore from 'datatables.net';
    import Responsive from 'datatables.net-responsive';
    import DeleteModal from '@/components/DeleteModal.vue';

    DataTable.use(DataTablesCore);
    DataTable.use(Responsive);

    const API_URL = import.meta.env.VITE_API_URL;
    const PUBLIC_URL = import.meta.env.VITE_PUBLIC_URL;
    const users = ref([]);
    const user = ref({
        id: null,
        name: '',
        username: '',
        email: '',
        phone_number: '',
        password: '',
        user_role: '',
        photo: '',
        status: 1,
    });
    const usersDataTable = ref({
        columns: [
            { title: 'ID', visible: false },
            { title: '#'},
            { 
                title: 'Photo',
                render: function(data,type,row){
                    const imageUrl = getImageUrl(row[0]);
                    return `<img src="${imageUrl}" alt="Photo" class="avatar avatar-50 rounded">`
                },
            },
            { title: 'Name' },
            { title: 'Role' },
            { title: 'Username' },
            { title: 'Email' },
            { title: 'Phone' },
            { 
                title: 'Status',
                render: function (data, type, row, meta) {
                    const id = row[0];
                    let userRes = users.value.find(item => item.id === id) || null;
                    return userRes.status === 1 ? '<span class="text-success">Active</span>' : '<span class="text-danger">Inactive</span>';
                }
            },
            {
                title: 'Actions',
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    const id = row[0];
                    return `
                        <button class="btn btn-square btn-link theme-blue edit-btn" data-id="${id}"><i class="bi bi-pencil"></i></button>
                        <button class="btn btn-square btn-link theme-red delete-btn" data-id="${id}"><i class="bi bi-trash"></i></button>
                    `
                },
            }
        ],
        data: [],
        options: {
            responsive: true,
            lengthChange: false,
            info: false,
            destroy: true,
        }
    });
    const isUserFormValid = ref(false);
    const isUserFormLoading = ref(false);
    const isUserEditMode = ref(false);
    const deleteModalRef = ref(null);
    const errors = ref({});
    const showToast = inject('showToast')
    let usersDataTableActionsClickHandler;

    /**
     * Loaders
     */

    onMounted(() => {
        usersDataTableActionsClickHandler = dataTableActionsClickHandler(edit, confirmDelete);
        document.addEventListener('click', usersDataTableActionsClickHandler);
        loadUsers();  
    });

    onBeforeUnmount(() => {
        document.removeEventListener('click', usersDataTableActionsClickHandler);
    });

    const loadUsers = ()=> {
        axios.get(`${API_URL}/user`,{
            headers: {                
                'Authorization': `Bearer ${sessionStorage.getItem('event-catering-token')}`,
            },
        })
        .then(async (response) => {
            users.value = response.data.users;  
            usersDataTable.value.data = objectArrayTo2D(users.value, {
                columns: ['path', 'name', 'user_role', 'username', 'email', 'phone_number', 'status']
            });
            await nextTick();
        })
        .catch((error) => {
            handleApiError(error);
        });
    }

    /**
     * Operations
     */
    const create = () => {
        reset();
        isUserEditMode.value = false;
        showModalByElementId('userModal');
    }

    const store = () => {
        isUserFormValid.value = true;
        validateName();
        validateUsername();
        validateEmail();
        validatePassword();
        validatePhoneNumber();
        validateUserRole();
        validatePhoto();
        if(isUserFormValid.value) {
            const formData = new FormData();
            formData.append('name', user.value.name);
            formData.append('username', user.value.username);
            formData.append('email', user.value.email);
            formData.append('phone_number', user.value.phone_number);
            formData.append('password', user.value.password);
            formData.append('user_role', user.value.user_role);
            if (user.value.photo) {
                formData.append('photo', user.value.photo);
            }

            axios.post(`${API_URL}/user`, formData, {
                headers: {
                    'Authorization': 'Bearer ' + sessionStorage.getItem('event-catering-token'),
                    'Content-Type': 'multipart/form-data'
                },
                meta: { loadingRef: isUserFormLoading }
            }).then((response) => {
                showToast?.(response.data.message, TOAST_SUCCESS);
                hideModalByElementId('userModal');
                loadUsers();
            }).catch((error) => {
                handleApiError(error);
            })
        }
    }

    const edit = (id) => {
        reset();
        isUserEditMode.value = true;
        let userRes= users.value.find(item => item.id === id) || null;
        user.value.id = userRes.id;
        user.value.name = userRes.name;
        user.value.username = userRes.username;
        user.value.email = userRes.email;
        user.value.phone_number = userRes.phone_number;
        user.value.password = '';
        user.value.user_role = userRes.user_role;
        user.value.photo = '';
        user.value.status = userRes.status;
        showModalByElementId('userModal');
    }

    const update = () => {
        isUserFormValid.value = true;
        validateName();
        validateUsername();
        validateEmail();
        validatePhoneNumber();
        validateUserRole();
        validatePhoto();
        if(isUserFormValid.value) {
            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('name', user.value.name);
            formData.append('username', user.value.username);
            formData.append('email', user.value.email);
            formData.append('phone_number', user.value.phone_number);
            formData.append('password', user.value.password);
            formData.append('user_role', user.value.user_role);
            formData.append('status', user.value.status);
            if (user.value.photo) {
                formData.append('photo', user.value.photo);
            }

            axios.post(`${API_URL}/user/${user.value.id}`, formData, {
                headers: {
                    'Authorization': 'Bearer ' + sessionStorage.getItem('event-catering-token'),
                    'Content-Type': 'multipart/form-data'
                },
                meta: { loadingRef: isUserFormLoading }
            }).then((response) => {
                showToast?.(response.data.message, TOAST_SUCCESS);
                hideModalByElementId('userModal');
                loadUsers();
            }).catch((error) => {
                handleApiError(error);
            })
        }
    }

    const confirmDelete = (id) => {
        let userRes= users.value.find(item => item.id === id) || null;
        user.value.id = userRes.id;
        user.value.name = userRes.name;
        user.value.username = userRes.username;
        user.value.email = userRes.email;
        user.value.phone_number = userRes.phone_number;
        user.value.password = '';
        user.value.user_role = userRes.user_role;
        user.value.photo = '';
        user.value.status = userRes.status;
        deleteModalRef.value.show();
    }

    const destroy = () => {
        axios.delete(`${API_URL}/user/${user.value.id}`, {
            headers: {
                'Authorization': `Bearer ${sessionStorage.getItem('event-catering-token')}`,
            },
            meta: { loadingRef: isUserFormLoading }
        })
        .then((response) => {
            showToast?.(response.data.message, TOAST_SUCCESS);
            deleteModalRef.value.hide();
            loadUsers();  
        })
        .catch((error) => {
           handleApiError(error);
        })
    }

    /**
     * Validators
     */
    const validateName = () => {
        errors.value.name = handleValidateName(user.value.name);
        if(errors.value.name) {
            isUserFormValid.value = false;
        }
    }

    const validateUsername = () => {
        errors.value.username = handleValidateUsername(user.value.username);
        if(errors.value.username) {
            isUserFormValid.value = false;
        }
    }

    const validateEmail = () => {
        errors.value.email = handleValidateEmail(user.value.email);
        if(errors.value.email) {
            isUserFormValid.value = false;
        }
    }

    const validatePassword = () => {
        errors.value.password = handleValidatePassword(user.value.password);
        if(errors.value.password) {
            isUserFormValid.value = false;
        }
    }

    const validatePhoneNumber = () => {
        errors.value.phone_number = handleValidatePhoneNumber(user.value.phone_number);
        if(errors.value.phone_number) {
            isUserFormValid.value = false;
        }
    }

    const validateUserRole = () => {
        errors.value.user_role = handleValidateUserRole(user.value.user_role);
        if(errors.value.user_role) {
            isUserFormValid.value = false;
        }
    }

    const validatePhoto = () => {
        errors.value.photo = handleValidateImage(user.value.photo);
        if(errors.value.photo) {
            isUserFormValid.value = false;
        }
    }

    /**
     * Helpers
     */

    const showModalByElementId = (elementId) => {
        const modal = new window.bootstrap.Modal(document.getElementById(elementId));
        modal.show();
    }

    const hideModalByElementId = (elementId) => {
        const modal = window.bootstrap.Modal.getInstance(document.getElementById(elementId));
        modal.hide();
    }

    const handlePhotoChange = (event) => {
        const file = event.target.files[0];
        user.value.photo = file;
        validatePhoto();
    };

    const handleApiError = (error) => {
        const response = error?.response;
        if (response?.status === 422 && response.data?.errors) {
            Object.entries(response.data.errors).forEach(([key, messages]) => {
                errors.value[key] = messages[0];
            });
        }
        const message = response?.data?.message || "Something went wrong.";
        showToast?.(message, TOAST_ERROR);
    };

    const getImageUrl = (id) => {
        let userRes = users.value.find(item => item.id === id) || null;
        if(userRes.url) {
            return userRes.url;
        } else if(userRes.path) {
            return PUBLIC_URL + '/storage/' +  userRes.path;
        } else {
            return '/images/user.jpg';
        }
    }

    /**
     * Resets
     */
    const reset = () => {
        user.value.id = null;
        user.value.name = '';
        user.value.username = '';
        user.value.email = '';
        user.value.phone_number = '';
        user.value.password = '';
        user.value.user_role = '';
        user.value.photo = '';
        user.value.status = 1;
        errors.value = {};
    }
</script>
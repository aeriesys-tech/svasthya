<template>
    <!-- standard header -->
    <header class="adminuiux-header">
        <!-- Fixed navbar -->
        <nav class="navbar">
            <div class="container-fluid">
                <!-- logo -->
                <!-- <a class="navbar-brand" href="ecommerce-dashboard.html">
                    <img data-bs-img="light" src="/images/logo.png" alt="">
                    <img data-bs-img="dark" src="/images/logo.png" alt="">
                    <div class="d-block ps-2">
                        <h6 class="fs-6 mb-0">Union Catering Company</h6>
                        <p class="company-tagline">Exquisite Event Services</p>
                    </div>
                </a> -->
                <!-- <div class=" ms-auto ">
                    <br>
                    <img data-bs-img="light" src="/images/logo.png" alt="" width="100px">
                    <img data-bs-img="dark" src="/images/logo.png" alt="" width="100px">
                </div> -->
                <!-- right icons button -->
                <div class="ms-auto">
                </div>
            </div>
        </nav>
    </header>
    <div class="adminuiux-wrap z-index-0 position-relative">
        <main class="adminuiux-content">
            <!--Page body-->
            <div class="container-fluid">
                <div class="row gx-3 align-items-center justify-content-center py-3 mt-auto z-index-1 height-dynamic" style="--h-dynamic: calc(100vh - 120px)">
                    <div class="col login-box maxwidth-400">
                        <div class="mb-4">
                            <h3 class="text-theme-1 fw-normal mb-0">Welcome to,</h3>
                            <h1 class="fw-bold text-theme-accent-1 mb-0">Svasthya</h1>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" :class="{'border-danger': errors.username}" id="username" placeholder="Enter username" autocomplete="off" tabindex="1" autofocus v-model="username">
                            <label for="username">Email</label>
                        </div>
                        <div class="position-relative">
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" :class="{'border-danger': errors.password}" id="passwd" placeholder="Enter your password" tabindex="2" v-model="password">
                                <label for="passwd">Password</label>
                            </div>
                            <button class="btn btn-square btn-link text-theme-1 position-absolute end-0 top-0 mt-2 me-2 ">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="row gx-3 align-items-center mb-3">
                            <div class="col">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="rememberme" id="rememberme">
                                    <label class="form-check-label" for="rememberme">Remember me</label>
                                </div>
                            </div>
                            <div class="col-auto">
                                <router-link to="#" class=" ">Forgot Password?</router-link>
                            </div>
                        </div>
                        <button class="btn btn-lg btn-theme w-100 mb-3" tabindex="3" @click="login()" v-if="!isLoginFormLoading">Login</button>
                        <button class="btn btn-lg btn-theme w-100 mb-3" v-else>
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </button>
                        <div class="text-center mt-3">
                            <router-link to="/register" class="text-decoration-none">
                                Don’t have an account? <span class="fw-bold text-secondary">Register</span>
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <!-- standard index footer -->
    <footer class="adminuiux-footer mt-auto">
        <div class="container-fluid text-center">
            <!-- <span class="small">Copyright @2025, <a href="https://adminuiux.com" target="_blank">eCommuiux</a> on Earth ❤️ </span> -->
        </div>
    </footer>
    <Toast ref="toastRef" />
</template>

<script setup>
    import { ref } from 'vue';
    import { TOAST_SUCCESS, TOAST_ERROR } from '@/utils/config';
    import { handleValidateUsername, handleValidatePassword } from '@/utils/validator.js';
    import { useRouter } from 'vue-router';
    import Toast from '@/components/Toast.vue';
    import axios from 'axios';
    
    const API_URL = import.meta.env.VITE_API_URL;
    const router = useRouter();
    const isLoginFormLoading = ref(false);
    const isLoginFormValid = ref(false);

    const username = ref('');
    const password = ref('');
    const errors = ref({});
    const toastRef = ref(null);

    const validateUsername = () => {
        errors.value.username = handleValidateUsername(username.value);
        if(errors.value.username) {
            isLoginFormValid.value = false;
        }
    };

    const validatePassword = () => {
        errors.value.password = handleValidatePassword(password.value);
        if(errors.value.password) {
            isLoginFormValid.value = false;
        }
    };

    const login = () => {
        router.push({ path: `/dashboard` });
        // isLoginFormValid.value = true;
        // validateUsername();
        // validatePassword();
        // if(isLoginFormValid.value) {
        //     axios.post(`${API_URL}/login`, {
        //         username: username.value,
        //         password: password.value,
        //     }, {
        //         meta: { loadingRef: isLoginFormLoading }
        //     })
        //     .then((response) => {
        //         toastRef.value.show("Login successful", TOAST_SUCCESS);
        //         sessionStorage.setItem('event-catering-token', response.data.token);
        //         sessionStorage.setItem('event-catering-id', response.data.user.id);
        //         sessionStorage.setItem('event-catering-username', response.data.user.username);
        //         sessionStorage.setItem('event-catering-email', response.data.user.email);
        //         setTimeout(() => {
        //             router.push({ path: `/dashboard` });
        //         }, 1500);
        //     })
        //     .catch((error) => {
        //         if(error.response){
        //             if(error.response.status === 422){
        //                 if (error.response.data.errors){
        //                     Object.keys(error.response.data.errors).forEach((key, index) => {
        //                         errors.value[key] = error.response.data.errors[key][0];
        //                         setTimeout(() => {
        //                             toastRef.value.show(error.response.data.errors[key][0], TOAST_ERROR);
        //                         }, index * 500);
        //                     });  
        //                 }
        //             } else if(error.response.status === 403) {
        //                 toastRef.value.show(error.response.data.message, TOAST_ERROR);
        //             } else {
        //                 toastRef.value.show('Server Error', TOAST_ERROR);
        //             }
        //         } else {
        //             toastRef.value.show('Server Error', TOAST_ERROR);
        //         }
        //     })
        // }
    }
</script>
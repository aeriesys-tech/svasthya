<template>
    <!-- Standard sidebar -->
    <!-- Standard sidebar -->
    <div class="adminuiux-sidebar">
        <div class="adminuiux-sidebar-inner">
            <ul class="nav flex-column menu-active-line mt-3">
                <li class="nav-item" onclick="contentClick()">
                    <router-link to="/dashboard" class="nav-link">
                        <i class="menu-icon bi bi-columns-gap"></i>
                        <span class="menu-name">Dashboard</span>
                    </router-link>
                </li>
                <li class="nav-item" onclick="contentClick()">
                    <router-link to="/assessment" class="nav-link">
                        <i class="menu-icon bi bi-file-check"></i>
                        <span class="menu-name">Know & Check Yourself</span>
                    </router-link>
                </li>
                <li class="nav-item" onclick="contentClick()">
                    <router-link to="/activity" class="nav-link">
                        <i class="menu-icon bi bi-list-task"></i>
                        <span class="menu-name">Activities</span>
                    </router-link>
                </li>
                <li class="nav-item" onclick="contentClick()">
                    <router-link to="/self-enhancement" class="nav-link">
                        <i class="menu-icon bi bi-check2-square"></i>
                        <span class="menu-name">Self Enhancement</span>
                    </router-link>
                </li>
                <li class="nav-item" onclick="contentClick()">
                    <router-link to="/your-progress" class="nav-link">
                        <i class="menu-icon bi-graph-up-arrow"></i>
                        <span class="menu-name">Your Progress</span>
                    </router-link>
                </li>
                <li class="nav-item" onclick="contentClick()">
                    <router-link to="/learn" class="nav-link">
                        <i class="menu-icon bi bi-boxes"></i>
                        <span class="menu-name">Education</span>
                    </router-link>
                </li>
                <li class="nav-item" onclick="contentClick()">
                    <router-link to="/calendar" class="nav-link">
                        <i class="menu-icon bi bi-calendar"></i>
                        <span class="menu-name">Events</span>
                    </router-link>
                </li>
                <li class="nav-item" onclick="contentClick()">
                    <router-link to="/appointment" class="nav-link">
                        <i class="menu-icon bi bi-clock"></i>
                        <span class="menu-name">Appointment</span>
                    </router-link>
                </li>
                <li class="nav-item" onclick="contentClick()">
                    <router-link to="/feedback" class="nav-link">
                        <i class="menu-icon bi bi-chat-square-dots"></i>
                        <span class="menu-name">Feedback</span>
                    </router-link>
                </li>
                <li class="nav-item" onclick="contentClick()">
                    <router-link to="/help" class="nav-link">
                        <i class="nav-icon bi bi-lightbulb"></i>
                        <span class="menu-name">Help</span> 
                    </router-link>
                </li> 
            </ul>
            <div class=" mt-auto "></div>
            <div class="px-3 not-iconic">
                <div class="card adminuiux-card shadow-sm border-0 bg-secondary mb-3" role="button" @click="logout()">
                    <div class="card-body">
                        <div class="row gx-3">
                            <div class="col">
                                <p class="mb-0 text-light" v-if="!isLogoutLoading"><i class="bi bi-box-arrow-right me-1 "></i> Logout</p>
                                <span class="mb-0 text-light" v-if="isLogoutLoading">
                                    <div class="spinner-border spinner-border-sm" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div> Logging out...
                                </span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { ref } from 'vue';
    import { useRouter } from 'vue-router';
    import { disconnectEcho } from '@/utils/echoManager';
    import axios from 'axios';

    const API_URL = import.meta.env.VITE_API_URL;
    const router = useRouter();
    const isLogoutLoading = ref(false);

    const logout = () => {
        disconnectEcho();
        
        axios.get(`${API_URL}/logout`, {
            headers: {
                Authorization: 'Bearer ' + sessionStorage.getItem('event-catering-token')
            },
            meta: { loadingRef: isLogoutLoading }
        }).then(() => {

        })
        .catch((error) => {
            
        }).finally(() => {
            contentClick()
            sessionStorage.removeItem('event-catering-token');
            sessionStorage.removeItem('event-catering-id');
            sessionStorage.removeItem('event-catering-username');
            sessionStorage.removeItem('event-catering-email');
            sessionStorage.removeItem('reloaded');
            router.push({ path: `/` });
        });
    }
</script>
<template>
    <header class="adminuiux-header">
        <nav class="navbar navbar-expand-lg fixed-top">
            <div class="container-fluid d-flex align-items-center justify-content-between">

                <!-- LEFT SECTION -->
                <div class="d-flex align-items-center">
                    <!-- Sidebar toggle -->
                    <button class="btn btn-link btn-square sidebar-toggler me-2" type="button" onclick="initSidebar()">
                        <i class="sidebar-svg" data-feather="menu"></i>
                    </button>

                    <!-- Logo / Brand -->
                    <a class="navbar-brand mb-0 h6 fw-bold text-dark" href="javascript:void(0)">
                        Svasthya
                    </a>
                </div>

                <!-- RIGHT SECTION -->
                <div class="d-flex align-items-center ms-auto">
                    <!-- ⏱ Timer -->
                    <!-- <span class="me-3 fw-bold">⏱ {{ runtime }}</span> -->

                    <!-- Theming Settings -->
                    <button class="btn btn-link btn-square btn-icon btn-link-header position-relative" type="button" data-bs-toggle="offcanvas" data-bs-target="#theming">
                        <i class="bi bi-palette"></i>
                    </button>

                    <!-- Dark Mode -->
                    <button class="btn btn-link btn-square btnsunmoon btn-link-header" id="btn-layout-modes-dark-page">
                        <i class="sun mx-auto" data-feather="sun"></i>
                        <i class="moon mx-auto" data-feather="moon"></i>
                    </button>

                    <!-- Language Dropdown -->
                    <div class="dropdown ">
                        <button class="btn btn-link btn-square btn-icon btn-link-header dropdown-toggle no-caret" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-translate"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item active" data-value="EN">EN - English</a></li>
                            <li><a class="dropdown-item" data-value="FR">KAN - Kannada</a></li>
                        </ul>
                    </div>

                    <!-- Notifications -->
                    <button class="btn btn-link btn-square btn-icon btn-link-header position-relative" data-bs-toggle="offcanvas" data-bs-target="#view-notification">
                        <i data-feather="bell"></i>
                        <span class="position-absolute top-0 end-0 badge rounded-pill bg-danger p-1">
                            <small>{{ notifications.length > 10 ? '10+' : notifications.length }}</small>
                            <span class="visually-hidden">unread messages</span>
                        </span>
                    </button>
                </div>

            </div>
            <br>
            <!-- TIMER BELOW RIGHT ICONS - Hidden for admin routes -->
            <div v-if="!isAdminRoute" class="timer-bar py-1 d-flex align-items-center justify-content-center w-100" >
                <span class="fw-bold">⏱ {{ runtime }}</span>
            </div>
        </nav>
    </header>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import { notifications } from '@/stores/notificationStore';

const route = useRoute();

// Check if current route is an admin route
const isAdminRoute = computed(() => {
	return route.path.startsWith('/admin') || route.meta?.isAdmin === true;
});

const runtime = ref('00:00:00'); // HH:mm:ss
let secondsElapsed = 0;
let timerInterval = null;

function formatTime(sec) {
    const hrs = String(Math.floor(sec / 3600)).padStart(2, '0');
    const mins = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
    const secs = String(sec % 60).padStart(2, '0');
    return `${hrs}:${mins}:${secs}`;
}

onMounted(() => {
    timerInterval = setInterval(() => {
        secondsElapsed++;
        runtime.value = formatTime(secondsElapsed);
    }, 1000);
});

onUnmounted(() => {
    clearInterval(timerInterval);
});
</script>

<!-- <style scoped>
    .adminuiux-header .timer-bar {
        background: var(--color-background);
        /* border-top: 1px solid #ddd; */
    }
</style> -->


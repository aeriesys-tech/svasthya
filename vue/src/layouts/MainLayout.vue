<template>
	<Header></Header>
	<div class="adminuiux-wrap">
		<AdminSidebar v-if="isAdminRoute"></AdminSidebar>
		<Sidebar v-else></Sidebar>
		<main class="adminuiux-content has-sidebar" onclick="contentClick()">
			<slot></slot>
			<br><br>
		</main>
	</div>
	<Notification></Notification>
	<BottomNavigationBar v-if="!isAdminRoute"></BottomNavigationBar>
	<Toast ref="toastRef" />
</template>

<script setup>
import { ref, provide, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import Header from '@/components/Header.vue';
import Sidebar from '@/components/Sidebar.vue';
import AdminSidebar from '@/components/AdminSidebar.vue';
import Notification from '@/components/Notification.vue';
import BottomNavigationBar from '@/components/BottomNavigationBar.vue';
import Toast from '@/components/Toast.vue';
import { initEcho } from '@/utils/echoManager';

const route = useRoute();
const toastRef = ref(null);

// Check if current route is an admin route
const isAdminRoute = computed(() => {
	return route.path.startsWith('/admin') || route.meta?.isAdmin === true;
});

/**
 * Loaders
 */
onMounted(() => {
	const waitForToken = setInterval(() => {
		const token = sessionStorage.getItem('event-catering-token');
		if (token) {
			clearInterval(waitForToken);
			initEcho(token);
		}
	}, 100);
});

/**
 * Operations
 */

const showToast = (message, config) => {
	toastRef.value.show(message, config);
}

provide('showToast', showToast)
</script>
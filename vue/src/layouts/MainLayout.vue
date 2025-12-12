<template>
	<Header></Header>
	<div class="adminuiux-wrap">
		<Sidebar></Sidebar>
		<main class="adminuiux-content has-sidebar" onclick="contentClick()">
			<slot></slot>
			<br><br>
		</main>
	</div>
	<Notification></Notification>
	<BottomNavigationBar></BottomNavigationBar>
	<Toast ref="toastRef" />
</template>

<script setup>
import { ref, provide, onMounted } from 'vue';
import Header from '@/components/Header.vue';
import Sidebar from '@/components/Sidebar.vue';
import Notification from '@/components/Notification.vue';
import BottomNavigationBar from '@/components/BottomNavigationBar.vue';
import Toast from '@/components/Toast.vue';
import { initEcho } from '@/utils/echoManager';

const toastRef = ref(null);

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
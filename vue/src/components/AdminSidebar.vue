<template>
	<!-- Admin sidebar -->
	<div class="adminuiux-sidebar">
		<div class="adminuiux-sidebar-inner">
			<ul class="nav flex-column menu-active-line mt-3">
				<li class="nav-item" onclick="contentClick()">
					<router-link to="/admin/dashboard" class="nav-link">
						<i class="menu-icon bi bi-columns-gap"></i>
						<span class="menu-name">Dashboard</span>
					</router-link>
				</li>
				<li class="nav-item" onclick="contentClick()">
					<router-link to="/admin/users" class="nav-link">
						<i class="menu-icon bi bi-people"></i>
						<span class="menu-name">Users</span>
					</router-link>
				</li>
				<li class="nav-item" onclick="contentClick()">
					<a href="#" class="nav-link" @click.prevent="scrollToMoods">
						<i class="menu-icon bi bi-emoji-smile"></i>
						<span class="menu-name">Moods</span>
					</a>
				</li>
				<li class="nav-item" onclick="contentClick()">
					<a href="#" class="nav-link" @click.prevent="scrollToReflections">
						<i class="menu-icon bi bi-journal-text"></i>
						<span class="menu-name">Daily Reflections</span>
					</a>
				</li>
			</ul>
			<div class=" mt-auto "></div>
			<div class="px-3 not-iconic">
				<div class="card adminuiux-card shadow-sm border-0 bg-secondary mb-3" role="button" @click="logout()">
					<div class="card-body">
						<div class="row gx-3">
							<div class="col">
								<p class="mb-0 text-light" v-if="!isLogoutLoading"><i
										class="bi bi-box-arrow-right me-1 "></i> Logout</p>
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
import { useAuthStore } from '@/stores/authStore';

const router = useRouter();
const authStore = useAuthStore();
const isLogoutLoading = ref(false);

const scrollToMoods = () => {
	const moodsTab = document.getElementById('moods-tab');
	if (moodsTab) {
		moodsTab.click();
	}
};

const scrollToReflections = () => {
	const reflectionsTab = document.getElementById('reflections-tab');
	if (reflectionsTab) {
		reflectionsTab.click();
	}
};

const logout = () => {
	isLogoutLoading.value = true;

	// Call admin logout API endpoint
	authStore.post({
		uri: 'admin/logout',
		data: {},
		meta: { loadingRef: isLogoutLoading }
	})
		.then(() => {
			// Clear admin session
			sessionStorage.removeItem('admin-token');
			sessionStorage.removeItem('admin-user');
			sessionStorage.removeItem('is-admin');
			// Redirect to admin login
			router.push({ name: 'AdminLoginView' });
		})
		.catch((error) => {
			// Even if API call fails, clear local state and redirect
			sessionStorage.removeItem('admin-token');
			sessionStorage.removeItem('admin-user');
			sessionStorage.removeItem('is-admin');
			router.push({ name: 'AdminLoginView' });
		})
		.finally(() => {
			isLogoutLoading.value = false;
			if (typeof contentClick === 'function') {
				contentClick();
			}
		});
}
</script>


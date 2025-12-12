<template>
	<div class="container py-3">
		<!-- Header -->
		<div class="d-flex justify-content-between align-items-center mb-3">
			<div>
				<h4 class="mb-1">Learning</h4>
				<p class="text-muted mb-0">
					Explore helpful blogs and videos to support your wellbeing.
				</p>
			</div>
		</div>

		<!-- Toggle Pills -->
		<div class="d-flex gap-2 mb-4">
			<button
				class="toggle-pill "
                :class="{ active: selectedTab === 'blogs' }"				
				@click="selectedTab = 'blogs'"
			>
				Blogs
			</button>
			<button
				class="toggle-pill "
				:class="{ active: selectedTab === 'videos' }"
				@click="selectedTab = 'videos'"
			>
				Videos
			</button>
		</div>

		<!-- Dynamic Component -->
		<component :is="currentComponent" />
	</div>
</template>

<script setup>
    import { ref, computed } from 'vue'
    import BlogList from '@/components/BlogList.vue'
    import VideoList from '@/components/VideoList.vue' // add later

    const selectedTab = ref('blogs')

    const currentComponent = computed(() => {
        return selectedTab.value === 'blogs' ? BlogList : VideoList // replace null with VideoList later
    });
</script>
<style scoped>
    .toggle-pill {
        border: 1px solid #dc3545;
        color: #6c757d;
        background-color: transparent;
        border-radius: 50rem;
        padding: 6px 16px;
        font-weight: 500;
        transition: all 0.2s ease-in-out;
    }

    .toggle-pill:hover {
        background-color: #dc3545;
        color: #fff;
    }

    .toggle-pill.active {
        background-color: #dc3545 !important;
        color: #fff !important;
        border-color: #dc3545 !important;
    }
</style>
<template>
	<nav v-if="pagination && pagination.last_page > 1" aria-label="Page navigation">
		<ul class="pagination justify-content-center">
			<li class="page-item" :class="{ disabled: pagination.current_page === 1 }">
				<a class="page-link" href="#" @click.prevent="goToPage(pagination.current_page - 1)">
					Previous
				</a>
			</li>

			<li v-for="page in visiblePages" :key="page" class="page-item" :class="{ active: page === pagination.current_page }">
				<a class="page-link" href="#" @click.prevent="goToPage(page)">
					{{ page }}
				</a>
			</li>

			<li class="page-item" :class="{ disabled: pagination.current_page === pagination.last_page }">
				<a class="page-link" href="#" @click.prevent="goToPage(pagination.current_page + 1)">
					Next
				</a>
			</li>
		</ul>
		<div class="text-center mt-2 text-muted">
			<small>
				Showing {{ pagination.from }} to {{ pagination.to }} of {{ pagination.total }} entries
			</small>
		</div>
	</nav>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
	pagination: {
		type: Object,
		required: true
	}
});

const emit = defineEmits(['page-changed']);

const visiblePages = computed(() => {
	if (!props.pagination) return [];

	const current = props.pagination.current_page;
	const last = props.pagination.last_page;
	const pages = [];

	// Show max 5 pages around current page
	let start = Math.max(1, current - 2);
	let end = Math.min(last, current + 2);

	// Adjust if we're near the start or end
	if (end - start < 4) {
		if (start === 1) {
			end = Math.min(last, start + 4);
		} else if (end === last) {
			start = Math.max(1, end - 4);
		}
	}

	for (let i = start; i <= end; i++) {
		pages.push(i);
	}

	return pages;
});

const goToPage = (page) => {
	if (page < 1 || page > props.pagination.last_page) return;
	if (page === props.pagination.current_page) return;

	emit('page-changed', page);
};
</script>


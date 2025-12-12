<template>
	<div class="container py-3">

		<!-- Header -->
		<div class="mb-3">
			<h4 class="mb-1">Your Progress</h4>
			<p class="text-muted mb-0">
				Check the completion status of all wellbeing activities.
			</p>
		</div>

		<!-- Activity List -->
		<div class="list-group shadow-sm rounded-3">
			<div
				v-for="item in activities"
				:key="item.id"
				class="list-group-item d-flex justify-content-between align-items-center py-3"
			>
				<div class="fw-semibold">{{ item.name }}</div>

				<!-- Static status -->
				<span
					class="status-pill"
					:class="{
						completed: item.completed,
						pending: !item.completed
					}"
				>
					{{ item.completed ? "Completed" : "Pending" }}
				</span>
			</div>
		</div>

	</div>
</template>

<script setup>
const activities = [
    { id: 1, name: "Resilience - Five good things", completed: true },
    { id: 2, name: "Resilience - Resilience mapping", completed: false },
    { id: 3, name: "Decision Making", completed: true },
    { id: 4, name: "Goal Setting", completed: false },
    { id: 5, name: "Planning / Organising", completed: true },
    { id: 6, name: "Critical Thinking", completed: false },
    { id: 7, name: "Communication - Language Matters", completed: true },
    { id: 8, name: "Communication - Silence Signals", completed: false },
    { id: 9, name: "Empathy", completed: true },
    { id: 10, name: "Conflict Resolution", completed: false },
    { id: 11, name: "Social Skills", completed: false },
    { id: 12, name: "Sharing - Gratitude Gallery", completed: true },
    { id: 13, name: "Sharing - Gratitude Wall", completed: true },
    { id: 14, name: "Nurturing Relationship", completed: false },
    { id: 15, name: "Leadership", completed: false },
    { id: 16, name: "Teamwork - Mission Planning", completed: true },
    { id: 17, name: "Teamwork - My Role in Team", completed: false },
    { id: 18, name: "Responsible Citizenship", completed: false },
    { id: 19, name: "Self Motivation - Why I Serve", completed: true },
    { id: 20, name: "Self Motivation - Motivation Meter", completed: true },
    { id: 21, name: "Competitiveness", completed: false },
    { id: 22, name: "Stress Management - Riding The Wave", completed: true },
    { id: 23, name: "Stress Management - Mind Full Breathing", completed: false },
    { id: 24, name: "Self-Discipline - Discipline Tracker", completed: false },
    { id: 25, name: "Self-Discipline - Willpower Wallet", completed: false },
    { id: 26, name: "Personal Safety - Safety Check", completed: true },
    { id: 27, name: "Personal Safety - Calm Response", completed: true },
    { id: 28, name: "Self Esteem - Mirror Movements", completed: false },
    { id: 29, name: "Self Esteem - Reframe Thought", completed: true },
    { id: 30, name: "Moral Reasoning", completed: false },
    { id: 31, name: "Healthy Lifestyle - Quick Exercise", completed: false },
    { id: 32, name: "Healthy Lifestyle - Nutrition Logging", completed: true },
    { id: 33, name: "Resilience", completed: true },
    { id: 34, name: "Decision Making (Advanced)", completed: false },
    { id: 35, name: "Critical Thinking (Advanced)", completed: false },
    { id: 36, name: "Creative Thinking", completed: false },
    { id: 37, name: "Empathy (Advanced)", completed: true },
    { id: 38, name: "Problem Solving", completed: false },
    { id: 39, name: "Self Awareness", completed: false },
    { id: 40, name: "Sleep Hygiene", completed: true },
    { id: 41, name: "Anger Management", completed: false },
    { id: 42, name: "Self Insight", completed: false },
    { id: 43, name: "Effective Communication", completed: true },
    { id: 44, name: "Coping With Emotion", completed: false },
    { id: 45, name: "Coping With Stress", completed: true },
    { id: 46, name: "Digital De-addiction", completed: false },
];

</script>

<style scoped>
.status-pill {
	border: 1px solid #dc3545;
	background: transparent;
	color: #6c757d;
	border-radius: 50rem;
	padding: 6px 16px;
	font-size: 0.8rem;
	white-space: nowrap;
}

.status-pill.completed {
	background-color: #6bf453 !important;
	color: #111 !important;
	border-color: #52f14c !important;
}

.status-pill.pending {
	color: #6c757d;
}
</style>

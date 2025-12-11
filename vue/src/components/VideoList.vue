<template>
	<div>
		<!-- Section Header -->
		<h4 class="mb-1">Videos</h4>
		<p class="text-muted mb-3">
			Watch expert sessions and wellbeing tips for better physical and mental health.
		</p>

		<!-- Toggle Pills -->
		<div class="d-flex flex-wrap gap-2 mb-4">
			<button
				class="toggle-pill"
				:class="{ active: selectedCategory === 'physical' }"
				@click="selectedCategory = 'physical'"
			>
				Physical Health
			</button>
			<button
				class="toggle-pill"
				:class="{ active: selectedCategory === 'mental' }"
				@click="selectedCategory = 'mental'"
			>
				Mental Health
			</button>
            <button
				class="toggle-pill"
				:class="{ active: selectedCategory === 'diet' }"
				@click="selectedCategory = 'diet'"
			>
				Diet & Nutrition
			</button>
		</div>

		 <!-- Accordion for Videos -->
        <div v-if="selectedCategory === 'physical'">
            <div class="accordion" id="physicalAccordion">
                <div
                    class="accordion-item mb-2"
                    v-for="(section, index) in physicalSections"
                    :key="index"
                >
                    <h2 class="accordion-header" :id="`heading-${index}`">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            :data-bs-target="`#collapse-physical-${index}`"
                            aria-expanded="false"
                            :aria-controls="`collapse-physical-${index}`"
                        >
                            {{ section.title }}
                        </button>
                    </h2>
                    <div
                        :id="`collapse-physical-${index}`"
                        class="accordion-collapse collapse"
                        :aria-labelledby="`heading-${index}`"
                        data-bs-parent="#physicalAccordion"
                    >
                        <div class="accordion-body">
                            <div
                                v-for="(video, vIndex) in section.videos"
                                :key="vIndex"
                                class="mb-4"
                            >
                                <h6>{{ vIndex + 1}}. {{ video.title }}</h6>
                                <div class="ratio ratio-16x9 rounded shadow-sm">
                                    <iframe
                                        :src="video.url"
                                        title="YouTube video player"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                    ></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Metal helath accordian -->
        <div v-else-if="selectedCategory === 'mental'">
            <div class="accordion" id="mentalAccordion">
                <div
                    class="accordion-item mb-2"
                    v-for="(section, index) in mentalSections"
                    :key="index"
                >
                    <h2 class="accordion-header" :id="`heading-mental-${index}`">
                        <button
                            class="accordion-button collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            :data-bs-target="`#collapse-mental-${index}`"
                            aria-expanded="false"
                            :aria-controls="`collapse-mental-${index}`"
                        >
                            {{ section.title }}
                        </button>
                    </h2>
                    <div
                        :id="`collapse-mental-${index}`"
                        class="accordion-collapse collapse"
                        :aria-labelledby="`heading-mental-${index}`"
                        data-bs-parent="#mentalAccordion"
                    >
                        <div class="accordion-body">
                            <div
                                v-for="(video, vIndex) in section.videos"
                                :key="vIndex"
                                class="mb-4"
                            >
                                <h6>{{ video.title }}</h6>
                                <div class="ratio ratio-16x9 rounded shadow-sm">
                                    <iframe
                                        :src="video.url"
                                        title="YouTube video player"
                                        frameborder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen
                                    ></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Accordion for Diet -->
		<div v-else>
			<div class="accordion" id="dietAccordion">
				<div
					class="accordion-item mb-2"
					v-for="(section, index) in dietSections"
					:key="index"
				>
					<h2 class="accordion-header" :id="`heading-diet-${index}`">
						<button
							class="accordion-button collapsed"
							type="button"
							data-bs-toggle="collapse"
							:data-bs-target="`#collapse-diet-${index}`"
							aria-expanded="false"
							:aria-controls="`collapse-diet-${index}`"
						>
							{{ section.title }}
						</button>
					</h2>
					<div
						:id="`collapse-diet-${index}`"
						class="accordion-collapse collapse"
						:aria-labelledby="`heading-diet-${index}`"
						data-bs-parent="#dietAccordion"
					>
						<div class="accordion-body">
							<div
								v-for="(video, vIndex) in section.videos"
								:key="vIndex"
								class="mb-4"
							>
								<h6>{{ video.title }}</h6>
								<div class="ratio ratio-16x9 rounded shadow-sm">
									<iframe
										:src="video.url"
										title="YouTube video player"
										frameborder="0"
										allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
										allowfullscreen
									></iframe>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
    </div>
</template>

<script setup>
    import { ref } from 'vue'
    import { physicalSections } from '@/utils/physicalVideos';
    import { mentalSections } from '@/utils/mentalVideos';
    import { dietSections } from '@/utils/dietVideos';
    const selectedCategory = ref('physical');

</script>

<style scoped>
    .ratio {
        border-radius: 0.75rem;
        overflow: hidden;
    }
    .card-title {
        font-weight: 600;
    }

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

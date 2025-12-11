<template>
	<!-- bottom navigation bar starts -->
	<footer ref="bottomNavigationBarRef" class="adminuiux-mobile-footer hide-on-scrolldown style-1">
		<div class="container">
			<ul class="nav nav-pills nav-justified">
				<li class="nav-item">
					<router-link to="/dashboard" class="nav-link">
						<span>
							<i class="nav-icon text-warning bi bi-shop"></i>
							<span class="nav-text text-warning">Home</span>
						</span>
					</router-link>
				</li>
				<li class="nav-item">
					<router-link to="/radio" class="nav-link">
						<span>
							<i class="nav-icon text-warning bi bi-mic"></i>
							<span class="nav-text text-warning">Radio</span>
						</span>
					</router-link>
				</li>
				<li class="nav-item">
					<router-link to="/live" class="nav-link">
						<span>
							<i class="nav-icon text-warning bi bi-tv"></i>
							<span class="nav-text text-warning">Live</span>
						</span>
					</router-link>
				</li>
				<li class="nav-item">
					<router-link to="/profile" class="nav-link">
						<span>
							<i class="nav-icon text-warning bi bi-person"></i>
							<span class="nav-text text-warning">Profile</span>
						</span>
					</router-link>
				</li>
			</ul>
		</div>
	</footer>
	<!-- bottom navigation bar ends -->

	<!-- floating chatbot button  <i class="bi bi-robot"></i>-->
	<div class="position-fixed bottom-0 end-0 m-1 z-index-5" id="fixedbuttons">
		<button class="btn btn-square rounded-circle" type="button" data-bs-toggle="offcanvas" data-bs-target="#chatbotOffcanvas">
			<img src="/images/chatbot.jpeg" alt="chatbot image" style="width: 50px; border-radius: 50%;">
		</button>
	</div>

	<!-- Chatbot Offcanvas -->
	<div class="offcanvas offcanvas-end" tabindex="-1" id="chatbotOffcanvas">
		<div class="offcanvas-header border-bottom">
			<h5 class="offcanvas-title">Virtual Assistant</h5>
			<button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
		</div>

		<div class="offcanvas-body d-flex flex-column">
			<!-- Chat History -->
			<div class="chat-messages flex-grow-1 mb-3">
				<div class="alert alert-primary">👋 Hi Namaste!</div>
				<div class="alert alert-secondary">I'm here to guide you through Physical or Mental wellness resources.</div>

				<div v-for="(chat, index) in chatHistory" :key="index" class="mb-2">
					<div v-if="chat.sender === 'bot'" class="alert alert-primary">{{ chat.text }}</div>
					<div v-else class="alert alert-secondary">{{ chat.text }}</div>
				</div>

				<!-- Questionnaire Flow -->
				<div v-if="showQuestions && currentIndex < questions.length">
					<div class="alert alert-primary">{{ questions[currentIndex].text }}</div>
					<div>
						<button
							v-for="(opt, idx) in questions[currentIndex].options"
							:key="idx"
							class="btn btn-outline-primary btn-sm me-2 mb-2"
							@click="selectOption(opt)">
							{{ opt }}
						</button>
					</div>
				</div>

				<!-- Appointment Booking -->
				<div v-if="showAppointment" class="mt-3">
					<div class="alert alert-primary">Would you like to book an appointment with a wellness officer? Please select a date and time.</div>
					<input type="date" v-model="appointmentDate" class="form-control mb-2" />
					<input type="time" v-model="appointmentTime" class="form-control mb-2" />
					<button class="btn btn-success w-100" @click="confirmAppointment">Confirm Appointment</button>
				</div>

				<!-- End Chat -->
				<div v-if="chatEnded" class="mt-3 text-center">
					<button class="btn btn-danger" @click="restartChat">End Chat</button>
				</div>
			</div>

			<!-- Free Text Input -->
			<div v-if="!chatEnded" class="input-group">
				<input type="text" class="form-control" v-model="userMessage" placeholder="Type your message..." />
				<button class="btn btn-theme" type="button" @click="sendMessage">Send</button>
			</div>
		</div>
	</div>
</template>

<script setup>
import { ref } from 'vue';

const bottomNavigationBarRef = ref(null);
const chatHistory = ref([]);
const currentIndex = ref(0);

const showQuestions = ref(true);
const userMessage = ref('');
const showAppointment = ref(false);
const appointmentDate = ref('');
const appointmentTime = ref('');
const chatEnded = ref(false);

// questions
const questions = [
	{ id: 1, text: "🥤 How many glasses of water did you drink today?", options: ["0–2", "3–5", "6–8", "More than 8"] },
	{ id: 2, text: "🚶 Did you walk or exercise for 30 mins today?", options: ["✅ Yes", "❌ No"] },
	{ id: 3, text: "🍱 What kind of food did you mostly eat today?", options: ["🍛 Home-cooked", "🍟 Fast food", "🧁 Sugary snacks", "🥗 Fruits/Salads"] },
	{ id: 4, text: "🕒 What time did you have dinner?", options: ["Before 8 PM", "8-10 PM", "After 10 PM", "Skipped dinner"] },
	{ id: 5, text: "😤 Did you feel angry or irritated today?", options: ["No", "Mild", "Moderate", "High"] },
	{ id: 6, text: "🎯 What triggered your anger?", options: ["Traffic/commute", "Argument at work", "Public misbehavior", "Family stress", "Fatigue/stress", "I don’t know"] },
	{ id: 7, text: "🧘 Did you use any calming strategy today?", options: ["Deep breathing", "Walking away", "Talking it out", "Humour/sarcasm", "No, I forgot"] },
	{ id: 8, text: "⭐ Did it help you feel better?", options: ["Yes, a lot", "A bit", "Not really", "No effect"] }
];

// questionnaire selection
function selectOption(option) {
	const currentQ = questions[currentIndex.value];
	chatHistory.value.push({ sender: 'bot', text: currentQ.text });
	chatHistory.value.push({ sender: 'user', text: option });

	if (currentIndex.value === questions.length - 1) {
		chatHistory.value.push({ sender: 'bot', text: "✅ Thank you for completing the questionnaire! Please type your reflections below." });
		showQuestions.value = false;
	} else {
		currentIndex.value++;
	}
}

// free text message
function sendMessage() {
	if (!userMessage.value.trim()) return;

	chatHistory.value.push({ sender: 'user', text: userMessage.value });

	// case 1: user skipped questions
	if (showQuestions.value) {
		showQuestions.value = false;
		chatHistory.value.push({ sender: 'bot', text: "✅ Thank you for your response. Our wellness officer will contact you shortly." });
		showAppointment.value = true;
	} else {
		// after completing questionnaire
		chatHistory.value.push({ sender: 'bot', text: "✅ Thank you for your reflections. Our wellness officer will contact you shortly." });
		showAppointment.value = true;
	}

	userMessage.value = '';
}

// appointment confirmation
function confirmAppointment() {
	if (!appointmentDate.value || !appointmentTime.value) {
		alert("Please select both date and time.");
		return;
	}
	const appointmentMsg = `📅 Your appointment is booked for ${appointmentDate.value} at ${appointmentTime.value}. Thank you — our wellness officer will contact you then.`;
	chatHistory.value.push({ sender: 'bot', text: appointmentMsg });
	showAppointment.value = false;
	chatEnded.value = true;
}

// restart chat
function restartChat() {
	currentIndex.value = 0;
	chatHistory.value = [];
	showQuestions.value = true;
	showAppointment.value = false;
	appointmentDate.value = '';
	appointmentTime.value = '';
	chatEnded.value = false;
	userMessage.value = '';
}
</script>

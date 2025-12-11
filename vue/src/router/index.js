import { createRouter, createWebHistory, createWebHashHistory } from 'vue-router'
import { useAuthStore } from '@/stores/authStore'
// layouts
import BlankLayout from '@/layouts/BlankLayout.vue'
import MainLayout from '@/layouts/MainLayout.vue'
// views
import LoginView from '@/views/LoginView.vue'
import ForgotPasswordView from '@/views/ForgotPasswordView.vue'
import DashboardView from '@/views/DashboardView.vue'
import NotFoundView from '@/views/NotFoundView.vue'
import CalendarView from '@/views/CalendarView.vue'
import LearnView from '@/views/LearnView.vue'
import PhysicalQnAView from '@/views/PhysicalQnAView.vue'
import MentalQnAView from '@/views/MentalQnAView.vue'
import ObesityInfoView from '@/views/ObesityInfoView.vue'
import ObesityQuestionnaire from '@/components/ObesityQuestionnaire.vue'
import AngerInfoView from '@/views/AngerInfoView.vue'
import AngerQuestionnaire from '@/components/AngerQuestionnaire.vue'
import AngerThermometerActivity from '@/views/AngerThermometerActivity.vue'
import ProfilePage from '@/views/ProfilePage.vue'
import RadioView from '@/views/RadioView.vue'
import LiveView from '@/views/LiveView.vue'
import AppointmentView from '@/views/AppointmentView.vue'
// import DecisionMakingActivityView from '../views/life-skill-activities/head/DecisionMakingActivityView.vue'
import EmpathyActivityView from '../views/self-enhancement-activities/EmpathyActivityView.vue'
import SelfAwarenessActivityView from '../views/self-enhancement-activities/SelfAwarenessActivityView.vue'
import AssessmentView from '../views/AssessmentView.vue'
import ResilienceActivityView from '../views/self-enhancement-activities/ResilienceActivityView.vue'
import SleepHygieneActivityView from '../views/self-enhancement-activities/SleepHygieneActivityView.vue'
import AngerManagementActivityView from '../views/self-enhancement-activities/AngerManagementActivityView.vue'
import SelfInsightActivityView from '../views/self-enhancement-activities/SelfInsightActivityView.vue'
import RegisterView from '../views/RegisterView.vue'
import FeedbackView from '../views/FeedbackView.vue'
import LifeskillsActivityView from '../views/LifeskillsActivityView.vue'
import SelfEnhancementView from '../views/SelfEnhancementView.vue'
import ActivityView from '../views/ActivityView.vue'
import HeadActivityView from '../views/life-skill-activities/head/HeadActivityView.vue'
import HandActivityView from '../views/life-skill-activities/hand/HandActivityView.vue'
import HealthActivityView from '../views/life-skill-activities/health/HealthActivityView.vue'
import FiveGoodThingsActivityView from '../views/life-skill-activities/head/FiveGoodThingsActivityView.vue'
import HeartActivityView from '../views/life-skill-activities/heart/HeartActivityView.vue'
import StressManagementActivityView from '../views/life-skill-activities/health/RidingTheWaveActivityView.vue'
import EmpathyHeartActivityView from '../views/life-skill-activities/heart/EmpathyHeartActivityView.vue'
import DecisionMakingActivityView from '../views/life-skill-activities/head/DecisionMakingActivityView.vue'
import GoalSettingDailyTargetActivityView from '../views/life-skill-activities/head/GoalSettingDailyTargetActivityView.vue'
import LeadershipActivityView from '../views/life-skill-activities/hand/LeadershipActivityView.vue'
import MissionPlanningActivityView from '../views/life-skill-activities/hand/MissionPlanningActivityView.vue'
import MyRoleActivityView from '../views/life-skill-activities/hand/MyRoleActivityView.vue'
import LanguageMattersActivityView from '../views/life-skill-activities/heart/LanguageMattersActivityView.vue'
import SilenceSignalsActivityView from '../views/life-skill-activities/heart/SilenceSignalsActivityView.vue'
import ConflictResolutionActivityView from '../views/life-skill-activities/heart/ConflictResolutionActivityView.vue'
import QuickExerciseBreakActivityView from '../views/life-skill-activities/health/QuickExerciseBreakActivityView.vue'
import DisciplineTrackerActivityView from '../views/life-skill-activities/health/DisciplineTrackerActivityView.vue'
import WillpowerWalletActivityView from '../views/life-skill-activities/health/WillpowerWalletActivityView.vue'
import ResilienceMapActivityView from '../views/life-skill-activities/head/ResilienceMapActivityView.vue'
import PlanningActivityView from '../views/life-skill-activities/head/PlanningActivityView.vue'
import CreativeThinkingActivityView from '../views/self-enhancement-activities/CreativeThinkingActivityView.vue'
import CriticalThinkingActivityView from '../views/self-enhancement-activities/CriticalThinkingActivityView.vue'
import DecisionMakingView from '../views/self-enhancement-activities/DecisionMakingView.vue'
import ProblemSolvingActivityView from '../views/self-enhancement-activities/ProblemSolvingActivityView.vue'
import ProgressView from '../views/ProgressView.vue'
import EffectiveCommunicationActivityView from '../views/self-enhancement-activities/EffectiveCommunicationActivityView.vue'
import CopingWithEmotionActivityView from '../views/self-enhancement-activities/CopingWithEmotionActivityView.vue'
import CopingWithStressActivityView from '../views/self-enhancement-activities/CopingWithStressActivityView.vue'
import DigitalDeaddictionActivityView from '../views/self-enhancement-activities/DigitalDeaddictionActivityView.vue'
import BreakItDownActivitView from '../views/life-skill-activities/head/BreakItDownActivitView.vue'
import PausePlanProceedActivityView from '../views/life-skill-activities/head/PausePlanProceedActivityView.vue'
import SocialSkillActivityView from '../views/life-skill-activities/heart/SocialSkillActivityView.vue'
import GratitudeGalleryAvtivityView from '../views/life-skill-activities/heart/GratitudeGalleryAvtivityView.vue'
import GratitudeWallActivityView from '../views/life-skill-activities/heart/GratitudeWallActivityView.vue'
import NurturingRelationshipActivityView from '../views/life-skill-activities/heart/NurturingRelationshipActivityView.vue'
import ResponsibleCitizenshipActivityView from '../views/life-skill-activities/hand/ResponsibleCitizenshipActivityView.vue'
import WhyIServeActivityView from '../views/life-skill-activities/hand/WhyIServeActivityView.vue'
import MotivationMeterActivityView from '../views/life-skill-activities/hand/MotivationMeterActivityView.vue'
import CompetitivenessActivityView from '../views/life-skill-activities/hand/CompetitivenessActivityView.vue'
import RidingTheWaveActivityView from '../views/life-skill-activities/health/RidingTheWaveActivityView.vue'
import MindfulBreathingExerciseActivityView from '../views/life-skill-activities/health/MindfulBreathingExerciseActivityView.vue'
import NutritionLoggingActivityView from '../views/life-skill-activities/health/NutritionLoggingActivityView.vue'
import SafetyCheckActivityView from '../views/life-skill-activities/health/SafetyCheckActivityView.vue'
import CalmResponseActivityView from '../views/life-skill-activities/health/CalmResponseActivityView.vue'
import MirrorMomentsActivityView from '../views/life-skill-activities/health/MirrorMomentsActivityView.vue'
import ReframeThoughtActivityView from '../views/life-skill-activities/health/ReframeThoughtActivityView.vue'
import MoralReasoningActivityView from '../views/life-skill-activities/health/MoralReasoningActivityView.vue'
import HelpView from '../views/HelpView.vue'



// routes
const router = createRouter({
	history: createWebHashHistory(import.meta.env.BASE_URL),
	routes: [
		{
			path: '/',
			name: 'LoginView',
			component: LoginView,
			meta: {
				layout: BlankLayout,
				auth: false
			},
		},
		{
			path: '/register',
			name: 'RegisterView',
			component: RegisterView,
			meta: {
				layout: BlankLayout,
				auth: false
			},
		},
		{
			path: '/forgot-password',
			name: 'ForgotPasswordView',
			component: ForgotPasswordView,
			meta: {
				layout: BlankLayout,
				auth: false
			},
		},
		{
			path: '/dashboard',
			name: 'DashboardView',
			component: DashboardView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/assessment',
			name: 'AssessmentView',
			component: AssessmentView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity',
			name: 'ActivityView',
			component: ActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills',
			name: 'LifeskillsActivityView',
			component: LifeskillsActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/head',
			name: 'HeadActivityView',
			component: HeadActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/head/resilience/five-good-things',
			name: 'FiveGoodThingsActivityView',
			component: FiveGoodThingsActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/head/resilience/resilience-mapping',
			name: 'ResilienceMapActivityView',
			component: ResilienceMapActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/head/decision-making',
			name: 'DecisionMakingActivityView',
			component: DecisionMakingActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/head/goal-setting',
			name: 'GoalSettingDailyTargetActivityView',
			component: GoalSettingDailyTargetActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/head/planning',
			name: 'PlanningActivityView',
			component: PlanningActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/head/problem-solving/break-it-down',
			name: 'BreakItDownActivitView',
			component: BreakItDownActivitView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/head/problem-solving/pause-plan-proceed',
			name: 'PausePlanProceedActivityView',
			component: PausePlanProceedActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/heart',
			name: 'HeartActivityView',
			component: HeartActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/heart/empathy',
			name: 'EmpathyHeartActivityView',
			component: EmpathyHeartActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/heart/communication/language-matters',
			name: 'LanguageMattersActivityView',
			component: LanguageMattersActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/heart/communication/silence-signals',
			name: 'SilenceSignalsActivityView',
			component: SilenceSignalsActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/heart/conflict-resolution',
			name: 'ConflictResolutionActivityView',
			component: ConflictResolutionActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/heart/social-skills',
			name: 'SocialSkillActivityView',
			component: SocialSkillActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/heart/sharing/gratitude-gallery',
			name: 'GratitudeGalleryAvtivityView',
			component: GratitudeGalleryAvtivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/heart/sharing/gratitude-wall',
			name: 'GratitudeWallActivityView',
			component: GratitudeWallActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/heart/nurturing-relationship',
			name: 'NurturingRelationshipActivityView',
			component: NurturingRelationshipActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/hand',
			name: 'HandActivityView',
			component: HandActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/hand/leadership',
			name: 'LeadershipActivityView',
			component: LeadershipActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/hand/teamwork/mission-planning',
			name: 'MissionPlanningActivityView',
			component: MissionPlanningActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/hand/teamwork/my-role',
			name: 'MyRoleActivityView',
			component: MyRoleActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/hand/responsible-citizenship',
			name: 'ResponsibleCitizenshipActivityView',
			component: ResponsibleCitizenshipActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/hand/self-motivation/why-i-serve',
			name: 'WhyIServeActivityView',
			component: WhyIServeActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/hand/self-motivation/motivation-meter',
			name: 'MotivationMeterActivityView',
			component: MotivationMeterActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/hand/competitiveness',
			name: 'CompetitivenessActivityView',
			component: CompetitivenessActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/health',
			name: 'HealthActivityView',
			component: HealthActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/health/stress-management/riding-the-wave',
			name: 'RidingTheWaveActivityView',
			component: RidingTheWaveActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/health/stress-management/mind-ful-breathing',
			name: 'MindfulBreathingExerciseActivityView',
			component: MindfulBreathingExerciseActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/health/discipline/discipline-tracker',
			name: 'DisciplineTrackerActivityView',
			component: DisciplineTrackerActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/health/discipline/willpower-wallet',
			name: 'WillpowerWalletActivityView',
			component: WillpowerWalletActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/health/lifestyle/quick-exercise',
			name: 'QuickExerciseBreakActivityView',
			component: QuickExerciseBreakActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/health/lifestyle/nutrition-logging',
			name: 'NutritionLoggingActivityView',
			component: NutritionLoggingActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/health/personal-safety/safety-check',
			name: 'SafetyCheckActivityView',
			component: SafetyCheckActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/health/personal-safety/calm-response',
			name: 'CalmResponseActivityView',
			component: CalmResponseActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/health/self-esteem/mirror-movements',
			name: 'MirrorMomentsActivityView',
			component: MirrorMomentsActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/health/self-esteem/reframe-thought',
			name: 'ReframeThoughtActivityView',
			component: ReframeThoughtActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/life-skills/health/moral-reasoning',
			name: 'MoralReasoningActivityView',
			component: MoralReasoningActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/self-enhancement',
			name: 'SelfEnhancementView',
			component: SelfEnhancementView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/self-enhancement/decision-making',
			name: 'DecisionMakingView',
			component: DecisionMakingView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/self-enhancement/problem-solving',
			name: 'ProblemSolvingActivityView',
			component: ProblemSolvingActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/self-enhancement/empathy',
			name: 'EmpathyActivityView',
			component: EmpathyActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/self-enhancement/resilience',
			name: 'ResilienceActivityView',
			component: ResilienceActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/self-enhancement/critical-thinking',
			name: 'CriticalThinkingActivityView',
			component: CriticalThinkingActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/self-enhancement/creative-thinking',
			name: 'CreativeThinkingActivityView',
			component: CreativeThinkingActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/self-enhancement/self-awareness',
			name: 'SelfAwarenessActivityView',
			component: SelfAwarenessActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/self-enhancement/sleep-hygiene',
			name: 'SleepHygieneActivityView',
			component: SleepHygieneActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/self-enhancement/anger-management',
			name: 'AngerManagementActivityView',
			component: AngerManagementActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/self-enhancement/self-insight',
			name: 'SelfInsightActivityView',
			component: SelfInsightActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/self-enhancement/effective-communication',
			name: 'EffectiveCommunicationActivityView',
			component: EffectiveCommunicationActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/self-enhancement/coping-with-emotions',
			name: 'CopingWithEmotionActivityView',
			component: CopingWithEmotionActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/self-enhancement/coping-with-stress',
			name: 'CopingWithStressActivityView',
			component: CopingWithStressActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/self-enhancement/digital-de-addiction',
			name: 'DigitalDeaddictionActivityView',
			component: DigitalDeaddictionActivityView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/learn',
			name: 'LearnView',
			component: LearnView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/physical/qna',
			name: 'PhysicalQnAView',
			component: PhysicalQnAView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/mental/qna',
			name: 'MentalQnAView',
			component: MentalQnAView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/obesity/info',
			name: 'ObesityInfoView',
			component: ObesityInfoView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/obesity/questionnaire',
			name: 'ObesityQuestionnaire',
			component: ObesityQuestionnaire,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/anger/info',
			name: 'AngerInfoView',
			component: AngerInfoView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/anger/questionnaire',
			name: 'AngerQuestionnaire',
			component: AngerQuestionnaire,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/activity/anger-thermometer',
			name: 'AngerThermometerActivity',
			component: AngerThermometerActivity,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/your-progress',
			name: 'ProgressView',
			component: ProgressView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/profile',
			name: 'ProfilePage',
			component: ProfilePage,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/feedback',
			name: 'FeedbackView',
			component: FeedbackView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/calendar',
			name: 'CalendarView',
			component: CalendarView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/radio',
			name: 'RadioView',
			component: RadioView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/live',
			name: 'LiveView',
			component: LiveView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/appointment',
			name: 'AppointmentView',
			component: AppointmentView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/help',
			name: 'HelpView',
			component: HelpView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		},
		{
			path: '/:pathMatch(.*)*',
			name: 'NotFoundView',
			component: NotFoundView,
			meta: {
				layout: MainLayout,
				auth: true
			},
		}
	],

})

router.beforeEach((to, from, next) => {
	const authStore = useAuthStore();

	// Check if route requires authentication
	const requiresAuth = to.meta.auth === true;
	const isAuthenticated = authStore.isAuthenticated;

	// If route requires auth and user is not authenticated, redirect to login
	if (requiresAuth && !isAuthenticated) {
		next({ name: 'LoginView' });
		return;
	}

	// If user is authenticated and trying to access login/register, redirect to dashboard
	if (isAuthenticated && (to.name === 'LoginView' || to.path === '/login' || to.path === '/register')) {
		next({ name: 'DashboardView' });
		return;
	}

	// Otherwise, allow navigation
	next();
});

router.afterEach(() => {
	// need this to keep the theme working in all views. settimeout is also necessary maybe it queues them or i dont know maybe its witchcraft
	setTimeout(() => {
		if (typeof setActivelink === 'function') setActivelink();
		if (typeof fixedHeaderSpace === 'function') fixedHeaderSpace();
		// if (typeof autoThemeMode === 'function') autoThemeMode();
		if (typeof featherjs === 'function') featherjs();
		if (typeof coverimg === 'function') coverimg();
		if (typeof dontclosedd === 'function') dontclosedd();
		if (typeof checkstrength === 'function') checkstrength();
		if (typeof bstooltip === 'function') bstooltip();
		if (typeof swipernavpagination === 'function') swipernavpagination();
		if (typeof PageLoaderHide === 'function') PageLoaderHide();
	}, 0);
});

export default router

<template>
    <div class="container">
        <!-- Heading -->
        <h4 class="mb-1">Blogs</h4>
        <p class="text-muted mb-3">Explore resources and techniques to improve your mental wellbeing</p>

        <!-- Search -->
        <div class="mb-3">
            <input
                type="text"
                class="form-control rounded-pill px-4"
                placeholder="Search topics, keywords..."
                v-model="search"
            />
        </div>

        <!-- Tags Filter -->
        <div class="d-flex flex-wrap gap-2 mb-4">
            <button
                v-for="(tag, index) in tags"
                :key="index"
                @click="selectedTag = tag"
                :class="['btn btn-sm rounded-pill', selectedTag === tag ? 'btn-danger' : 'btn-outline-secondary']"
            >
                {{ tag }}
            </button>
        </div>

        <!-- Learn Cards -->
        <div
            v-for="(item, index) in filteredLearnItems"
            :key="index"
            class="card p-3 mb-3 border rounded"
        >
            <div class="d-flex justify-content-between align-items-start">
                <div class="pe-2">
                    <h6 class="mb-1">{{ item.title }}</h6>
                    <p class=" small mb-2" v-html="item.description"></p>
                    <div class="d-flex gap-3 small ">
                        <span><i class="bi bi-tag"></i> {{ item.tag }}</span>
                        <span><i class="bi bi-clock"></i> {{ item.duration }}</span>
                    </div>
                </div>
                <!-- Bookmark or toggle placeholder -->
                <button class="btn btn-sm btn-outline-danger rounded-circle">
                    <i class="bi bi-bookmark-fill"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const search = ref('')
const selectedTag = ref('All')

const tags = ['All', 'Self Awareness', 'Stress', 'Sleep','Well Being','Mental Health','Nutrition']

const learnItems = ref([
    // {
    //     title: 'Understanding Stress Response',
    //     description: 'Learn about the physiological and psychological effects of stress on first responders',
    //     tag: 'Stress',
    //     duration: '5 min'
    // },
    // {
    //     title: 'Coping with Traumatic Events',
    //     description: 'Evidence-based techniques for processing traumatic experiences in the field',
    //     tag: 'Trauma',
    //     duration: '8 min'
    // },
    // {
    //     title: 'Parenting Challenges and Mental Health in Police Personnel',
    //     description: 'Being a police officer is a demanding profession. Being a parent is a full-time responsibility. When both roles are played by the same person, the emotional and psychological pressure can be overwhelming. Police personnel often face unique challenges in managing their roles as protectors of society and as caregivers at home. This dual responsibility, if not balanced, can take a toll on mental health, family relationships, and overall well-being.',
    //     tag: 'Self Awareness',
    //     duration: '7 min'
    // },
    // {
    //     title: 'Importance of Sleep for Police Personnel',
    //     description: 'For police personnel, long shifts, night duties, and unpredictable work hours are often seen as part of the job. But what’s less visible is the price they pay - sleep deprivation. Sleep is not just rest; it is the body’s natural healing process. Lack of proper sleep contributes to cognitive impairment, emotional instability, poor physical health, and compromised judgment, all of which are detrimental in a law enforcement setting.',
    //     tag: 'Sleep',
    //     duration: '7 min'
    // },
    // {
    //     title: 'Sleep Hygiene for Shift Workers',
    //     description: 'Improve sleep quality despite irregular work schedules',
    //     tag: 'Sleep',
    //     duration: '6 min'
    // },
    // {
    //     title: 'Mindfulness Basics for First Responders',
    //     description: 'Practical mindfulness techniques to use before, during, and after high-stress calls',
    //     tag: 'Stress',
    //     duration: '7 min'
    // },
    {
		title: 'Why Well-Being Matters',
		description: `
            <b>Why Well-Being Matters</b><br>
            In fast-paced, high-pressure jobs, it’s easy to get caught up in the demands of the role and forget to take care of the most important part of the job—you. Every long shift, tough decision, and stressful moment shows your strength, but it also takes a toll on your mental and physical health. Looking after your well-being isn’t just about making it through each day—it’s about building the strength, balance, and focus to do your job well and live a fulfilling life. In this article, we’ll explore how stress affects your mind and body, and share simple, practical tips to help you stay strong, calm, and prepared.<br><br>

            <b>Mental Health: The Foundation of Strength</b><br>
            Mental health affects how you feel, think, and respond to the world. It shapes your emotions, relationships, and how you handle stress. When your mental well-being is taken care of, it’s easier to stay calm in difficult moments, make good decisions, and stay connected with the people around you.<br><br>
            High-stress roles often involve difficult situations, emotional strain, and split-second decisions. This makes mental strength just as important as physical fitness. A healthy mind helps you stay focused, confident, and balanced, no matter what comes your way. Building a supportive environment, whether at work or home, helps too. When people feel understood and valued, it boosts emotional strength and creates space for personal growth and professional success.<br><br>

            <b>Physical Health: Your Body Keeps You Going</b><br>
            In demanding jobs, your body needs to be ready for anything—long hours, sudden changes, and physically intense tasks. Good physical health helps you stay alert, strong, and responsive. But with irregular schedules and constant responsibilities, it’s easy to let physical health slide. This can lead to fatigue, low energy, and long-term problems like high blood pressure or heart disease. Regular exercise, a healthy diet, and proper rest are essential. Staying active boosts your energy, clears your mind, and reduces stress. Even small steps, like stretching during breaks or taking short walks, can make a big difference.<br><br>

            <b>Common Mental Health Challenges in High-Stress Roles</b><br>
            <ul>
            <li>Stress: Not all stress is bad. Some of it helps you stay motivated. But ongoing, high stress (distress) can lead to anxiety, exhaustion, and health problems.</li>
            <li>Depression: Feeling tired, disconnected, or low for a long time can be a sign of depression. It affects sleep, appetite, mood, and concentration.</li>
            <li>Anxiety: Constant worry, restlessness, or nervousness—even during downtime—can make it hard to relax and enjoy life.</li>
            <li>Burnout: Long periods of stress without rest can cause burnout. Emotional drain, fatigue, and loss of interest in work may occur.</li>
            <li>Substance Use: Some turn to alcohol or drugs to cope. Short-term relief often worsens long-term effects.</li>
            <li>Sleep Problems: Stress and irregular hours can affect sleep, leaving you tired, unfocused, and more vulnerable to illness.</li>
            </ul><br>

            <b>Key Life Skills and What You Can Do</b><br>
            <ul>
            <li>Self-awareness: Understand your feelings, strengths, and limits.</li>
            <li>Empathy: Listen and understand others’ emotions.</li>
            <li>Critical thinking: Question, gather facts, and consider options before acting.</li>
            <li>Creative thinking: Look for new ways to solve problems.</li>
            <li>Decision making: Stay calm and choose the best option.</li>
            <li>Problem solving: Break problems down and find patterns.</li>
            <li>Communication: Speak clearly and listen carefully.</li>
            <li>Interpersonal relationships: Respect others and work peacefully.</li>
            <li>Coping with stress: Exercise, eat well, breathe deeply, or try relaxation techniques.</li>
            <li>Coping with emotions: Talk to someone, journal, and practice self-care.</li>
            </ul><br>

            <b>Work-Life Balance: Staying Connected</b><br>
            Balancing work with personal life is difficult in demanding jobs. Long hours, unpredictable schedules, and constant pressure can leave little time for family, hobbies, or rest. Tips include:<br>
            <ul>
            <li>Time Management: Plan your day for family and personal activities.</li>
            <li>Set Boundaries: Leave work stress at work when possible.</li>
            <li>Take Care of Yourself: Eat well, sleep enough, and exercise regularly.</li>
            <li>Talk to Your Family: Be open about challenges for support.</li>
            <li>Stay Connected: Spend time with supportive friends and peers.</li>
            <li>Be Present: Even limited time can be fully experienced with loved ones.</li>
            </ul><br>

            <b>Final Thoughts</b><br>
            In high-pressure roles, strength means more than pushing through. It means knowing when to pause, care for yourself, and grow. Your mental health, physical fitness, and life skills are not extras—they’re essentials. Small steps like rest, movement, or asking for help can lead to big changes. With the right support, you can stay focused, clear-headed, and fulfilled. Remember, you’re not just here to survive—you’re here to thrive.
                    `,
        tag: 'Well Being',
        duration: '12 min'
    },
    // Add this object to your learnItems array
    {
        title: 'Stigma of Mental Health',
        description: `
            <b>Stigma of Mental Health</b><br>
            Understanding the Stigma of Mental Health in the Workplace and How to Combat It.<br><br>

            Mental health is essential in all aspects of our lives. Despite increasing awareness, discussing mental health remains uncomfortable for many people. What causes this discomfort? It stems from the stigma surrounding mental health—a collection of negative beliefs, attitudes, and opinions. In today’s fast-paced world, productivity and performance often take priority. However, we must also prioritize our psychological well-being. Unfortunately, the stigma associated with mental health in the workplace continues to exist, leaving many individuals to struggle in silence. Countless people face challenges such as anxiety, depression, and burnout, yet these issues often go unaddressed.<br><br>

            It is vital to create an open environment where individuals feel safe discussing their mental health, ensuring that everyone receives the support and understanding they need. In this blog, we will explore what workplace stigma looks like, why it matters, and how both organizations and individuals can take concrete steps to break the silence around mental health.<br><br>

            <b>What does mental health at work look like</b><br>
            The stigma surrounding mental health in the workplace can take on many forms, often appearing as dismissive comments, avoidance from colleagues, or a pervasive silence on the topic. For example, when an employee bravely opens up about their struggles with anxiety, they may unfortunately be labeled as "too sensitive," which undermines their serious concerns.<br><br>

            Consider the idea of taking leave for mental health reasons; how often do we hear of employers doing so? Rarely, if ever. This lack of visibility can discourage employees from seeking the time off they need to recharge and address their mental health needs. Many individuals may feel compelled to avoid requesting mental health leave due to the fear of being perceived as lazy or uncommitted to their jobs. This creates an environment where mental health challenges are overlooked, and individuals suffer in silence.<br><br>

            <b>Why is the stigma in the workplace problematic?</b><br>
            The stigma surrounding mental health can have a particularly profound impact in the workplace, often causing individuals to feel both ashamed and apprehensive about seeking help. This environment can lead to a host of negative outcomes, including:<br>
            <ul>
            <li><b>Burnout</b> — Employees may experience overwhelming feelings of physical, mental, and emotional exhaustion, leaving them drained and incapable of performing at their best.</li>
            <li><b>Social Withdrawal</b> — Individuals may begin to isolate themselves from their colleagues, withdrawing from social interactions and collaborative efforts, which can hinder teamwork and create a sense of disconnection.</li>
            <li><b>Struggling in Silence</b> — The fear of being judged or stigmatized can trap individuals in a cycle of loneliness. They may choose to suffer in silence, masking their struggles, which only exacerbates feelings of isolation and despair.</li>
            </ul><br>

            Neglecting mental health in the workplace can have significant consequences that affect both individuals and the organization as a whole:<br>
            <ul>
            <li><b>Productivity declines</b> — When employees are struggling with their mental well-being, their ability to concentrate, complete tasks, and stay motivated diminishes. This can lead to missed deadlines and a decrease in overall output.</li>
            <li><b>Sick leaves rise</b> — A lack of attention to mental health can lead to increased absenteeism as employees may take more time off to cope with stress, anxiety, or depression. This not only impacts their well-being but also disrupts team dynamics and workflows.</li>
            <li><b>Talent attrition</b> — Employees who feel unsupported in their mental health needs are more likely to seek opportunities elsewhere. The loss of skilled individuals can be detrimental to a team’s cohesion and success, ultimately affecting the organization's long-term growth and stability.</li>
            </ul><br>

            By recognizing these issues, we can work towards fostering a more supportive and understanding workplace culture.
                `,
        tag: 'Mental Health',
        duration: '10 min'
    },
    {
    title: 'The Role of Nutrition in Mental Health',
    description: `
        <b>The Role of Nutrition in Mental Health: Eating Right for a Better Mood at Work</b><br><br>

        In today’s fast-paced work culture, mental well-being is often overshadowed by deadlines, meetings, and the ever-growing to-do list. But what if the key to a better mood and sharper focus at work lies not in your calendar—but on your plate?<br><br>

        <b>Food and Mood: The Science Behind the Connection</b><br>
        Your brain is a high-performance organ that requires premium fuel. Nutrients such as omega-3 fatty acids, B vitamins, magnesium, and antioxidants help regulate mood, reduce inflammation, and support brain function. Diets high in processed foods, sugar, and unhealthy fats are linked to increased risk of depression and anxiety.<br><br>

        <b>Nutritional Psychiatry</b><br>
        Traditional diets like Mediterranean or Japanese diets, rich in unprocessed foods, show a 25–35% lower risk of depression compared to Western diets high in processed foods.<br><br>

        <b>The Power of a Balanced Diet</b>
        <ul>
        <li>Omega-3 fatty acids (fatty fish) reduce symptoms of depression.</li>
        <li>B vitamins (B6, B12, folate) support mood-regulating chemicals.</li>
        <li>Magnesium (nuts, seeds, greens) helps manage stress and anxiety.</li>
        <li>Antioxidants (fruits, vegetables) protect the brain from stress damage.</li>
        </ul><br>

        <b>Eating Right at Work</b>
        <ul>
        <li>Plan meals: balanced lunches with lean proteins, complex carbs, healthy fats.</li>
        <li>Snack smart: nuts, yogurt, fruit.</li>
        <li>Stay hydrated.</li>
        <li>Avoid sugar crashes.</li>
        <li>Create a mindful eating space.</li>
        </ul><br>

        <b>The Gut-Brain Axis</b><br>
        A diverse gut microbiome supports neurotransmitter production and reduces inflammation. Fermented foods like yogurt, kimchi, and kefir act as natural probiotics.<br><br>

        Nutrition is a modifiable factor in mental health. Making intentional food choices supports emotional resilience, focus, and well-being.
            `,
            tag: 'Nutrition',
    },
    {
        title: 'Creating Boundaries',
        description: `
        <b>Creating Boundaries: How to Say No Without Guilt for Your Well-Being</b><br><br>

        In a world that glorifies busyness and self-sacrifice, saying “no” can feel radical. Setting boundaries protects mental health, energy, and self-respect.<br><br>

        <b>Why Saying No Matters</b><br>
        Boundaries act as invisible fences, helping you maintain balance in relationships and responsibilities.<br><br>

        <b>The Psychology Behind the Guilt</b><br>
        Guilt often comes from fear of rejection or disappointing others. Reframe “no” as self-care.<br><br>

        <b>How to Say No Guilt-Free</b>
        <ul>
        <li>Be direct but kind.</li>
        <li>Avoid over-explaining.</li>
        <li>Use “compliment + no + redirect”.</li>
        <li>Practice assertiveness.</li>
        </ul><br>

        Boundaries empower you to prioritize your well-being and foster healthier relationships.
            `,
        tag: 'Mental Health',
        duration: '5 min'
    },
    {
        title: 'The Science of Sleep',
        description: `
        <b>The Science of Sleep: Why Rest is Vital for High-Performers</b><br><br>

        Sleep fuels focus, creativity, decision-making, and emotional resilience. Without it, even talented individuals fall short.<br><br>

        <b>What Happens When You Sleep?</b><br>
        Sleep consolidates memories, clears toxins, and repairs the body. Cycles of non-REM and REM support recovery and learning.<br><br>

        <b>Sleep and Cognitive Performance</b><br>
        Lack of sleep reduces attention, memory, problem-solving, and emotional control.<br><br>

        <b>Sleep and Physical Performance</b><br>
        Sleep improves reaction time, coordination, endurance, and muscle recovery.<br><br>

        <b>Tips for Better Sleep</b>
        <ul>
        <li>Stick to a schedule.</li>
        <li>Create a wind-down routine.</li>
        <li>Limit caffeine and alcohol.</li>
        <li>Keep sleep environment cool, dark, quiet.</li>
        <li>Take short naps when needed.</li>
        </ul><br>

        Sleep is essential for peak performance, decision-making, mental well-being, and overall health.
            `,
        tag: 'Sleep',
        duration: '7 min'
    },
    {
        title: 'Developing Resilience',
        description: `
        <b>Developing Resilience: Tools for Bouncing Back from Professional Setbacks</b><br><br>

        Setbacks are inevitable, but resilience—the ability to adapt and recover—is learnable.<br><br>

        <b>Tools to Build Resilience</b>
        <ul>
        <li>Growth Mindset: See setbacks as opportunities to learn.</li>
        <li>Self-Reflection: Learn from failures and improve.</li>
        <li>Social Support: Lean on colleagues, mentors, and friends.</li>
        <li>Self-Compassion: Be kind to yourself during challenges.</li>
        <li>Goal Setting: Small, achievable goals rebuild confidence.</li>
        </ul><br>

        Resilience is dynamic; it can be cultivated through mindset, habits, and relationships.
            `,
        tag: 'Mental Health',
        duration: '6 min'
    },
    {
        title: 'Overcoming Burnout',
        description: `
        <b>Overcoming Burnout: Signs, Symptoms, and Solutions</b><br><br>

        Burnout is emotional, physical, and mental exhaustion from prolonged stress.<br><br>

        <b>Signs and Symptoms</b>
        <ul>
        <li>Physical: fatigue, headaches, sleep problems.</li>
        <li>Emotional: irritability, hopelessness, detachment.</li>
        <li>Cognitive: difficulty concentrating, negative thinking.</li>
        <li>Behavioral: procrastination, withdrawal, substance use.</li>
        </ul><br>

        <b>Causes of Burnout</b>
        <ul>
        <li>High workload</li>
        <li>Lack of control</li>
        <li>Poor work-life balance</li>
        <li>Inadequate support</li>
        <li>Value mismatch</li>
        </ul><br>

        <b>Solutions</b>
        <ul>
        <li>Set boundaries</li>
        <li>Prioritize self-care</li>
        <li>Seek social support</li>
        <li>Reframe mindset</li>
        <li>Make structural changes</li>
        </ul><br>

        Burnout is reversible with early recognition and proactive strategies.
            `,
        tag: 'Stress',
        duration: '7 min'
    },
    {
        title: 'Coping with High-Pressure Situations',
        description: `
        <b>Coping with High-Pressure Situations: Easy Stress Management Tips</b><br><br>

        High-pressure jobs create stress that affects health, mood, and performance.<br><br>

        <b>Simple Ways to Manage Stress</b>
        <ul>
        <li>Breathe Deep and Stay Grounded</li>
        <li>Take Care of Your Body</li>
        <li>Don’t Be Afraid to Say No</li>
        <li>Talk to People You Trust</li>
        <li>Take Short Breaks</li>
        <li>Think About the Big Picture</li>
        <li>Reach Out to a Professional</li>
        </ul><br>

        Managing stress protects mental health and enhances performance.
            `,
        tag: 'Stress',
        duration: '6 min'
    },
    {
        title: 'Physical Activity and Mental Well-Being',
        description: `
        <b>Physical Activity and Mental Well-Being: How Exercise Boosts Your Productivity</b><br><br>

        Regular movement improves mood, focus, and energy.<br><br>

        <b>Mental Benefits</b>
        <ul>
        <li>Better focus and attention</li>
        <li>Improved mood and less irritability</li>
        <li>Increased energy levels</li>
        <li>Better sleep</li>
        <li>Sharper decision-making</li>
        </ul><br>

        <b>Simple Ways to Stay Active Daily</b>
        <ul>
        <li>Take short walks</li>
        <li>Stretch during work</li>
        <li>Breathing and movement exercises</li>
        <li>Choose stairs over lifts</li>
        <li>Make it social</li>
        </ul><br>

        Physical activity is an easy, effective way to improve mental health and productivity.
            `,
        tag: 'Self Awareness',
        duration: '6 min'
    },
    {
        title: 'Crisis Management: Keeping Calm Under Pressure',
        description: `
        <b>Crisis Management: Keeping Calm Under Pressure</b><br><br>

        High-stress jobs require staying calm in emergencies. This skill can be learned.<br><br>

        <b>Simple Steps to Stay Calm</b>
        <ul>
        <li>Focus on your breathing</li>
        <li>Pause and assess</li>
        <li>Use clear and simple language</li>
        <li>Stick to what you can control</li>
        <li>Practice mental rehearsal</li>
        <li>Talk it out afterwards</li>
        </ul><br>

        <b>Building Long-Term Resilience</b><br>
        Sleep well, exercise regularly, eat balanced meals, and stay connected with supportive people to strengthen emotional resilience.
            `,
        tag: 'Self Awareness',
        duration: '7 min'
    }

            
   
    
])

const filteredLearnItems = computed(() => {
    return learnItems.value.filter((item) => {
        const matchesTag = selectedTag.value === 'All' || item.tag === selectedTag.value
        const matchesSearch = item.title.toLowerCase().includes(search.value.toLowerCase()) ||
            item.description.toLowerCase().includes(search.value.toLowerCase())
        return matchesTag && matchesSearch
    })
});
</script>

<style scoped>

</style>

<template>
  <div class="pha-container mt-3 mb-3">
    <div class="card form-card">
      <!-- Card Header -->
      <div class="card-header">
        <h3 class="">Physical Health Assessment</h3>
      </div>

      <!-- Card Body -->
      <div class="card-body">

        <!-- Form -->
        <div class="step-header">
          <div class="step-title fs-4">{{ steps[currentStep].title }}</div>
          <!-- <div class="step-count">Step {{ currentStep + 1 }} / {{ steps.length }}</div> -->
        </div>

        <form ref="formRef" @submit.prevent="onSubmit" class="form-body">

          <!-- BASIC DETAILS -->
          <section v-if="currentStepKey === 'basic'">
            <div class="field mt-3">
              <label class="form-label">Age: </label>
              <input type="number" class="input" v-model.number="form.age" min="0" />
              <div v-if="errors.age" class="error">{{ errors.age }}</div>
            </div>

            <div class="field">
              <label class="form-label">Gender</label>
              <div class="options-row">
                <button type="button" class="option" :class="{ selected: form.gender === 'Male' }" @click="set('gender','Male')">Male</button>
                <button type="button" class="option" :class="{ selected: form.gender === 'Female' }" @click="set('gender','Female')">Female</button>
                <button type="button" class="option" :class="{ selected: form.gender === 'Other' }" @click="set('gender','Other')">Other</button>
              </div>
              <div v-if="errors.gender" class="error">{{ errors.gender }}</div>
            </div>

            <div class="field">
              <label class="form-label">Shift Pattern</label>
              <div class="options-row">
                <button type="button" class="option" :class="{ selected: form.shiftPattern === 'Regular' }" @click="set('shiftPattern','Regular')">Regular</button>
                <button type="button" class="option" :class="{ selected: form.shiftPattern === 'Rotational' }" @click="set('shiftPattern','Rotational')">Rotational</button>
                <button type="button" class="option" :class="{ selected: form.shiftPattern === 'Night' }" @click="set('shiftPattern','Night')">Night</button>
                <button type="button" class="option" :class="{ selected: form.shiftPattern === 'Mixed' }" @click="set('shiftPattern','Mixed')">Mixed</button>
              </div>
              <div v-if="errors.shiftPattern" class="error">{{ errors.shiftPattern }}</div>
            </div>

            <div class="field">
              <label class="form-label">Diagnosed Conditions</label>
              <div class="options-row wrap">
                <button type="button" class="option" :class="{ selected: hasDiagnosed('Hypertension') }" @click="toggleDiagnosed('Hypertension')">Hypertension</button>
                <button type="button" class="option" :class="{ selected: hasDiagnosed('Diabetes') }" @click="toggleDiagnosed('Diabetes')">Diabetes</button>
                <button type="button" class="option" :class="{ selected: hasDiagnosed('Obesity') }" @click="toggleDiagnosed('Obesity')">Obesity</button>
                <button type="button" class="option" :class="{ selected: hasDiagnosed('None') }" @click="toggleDiagnosed('None')">None</button>
              </div>
              <div v-if="errors.diagnosed" class="error">{{ errors.diagnosed }}</div>
            </div>

            <div class="field">
              <label class="form-label">Current Medications (optional)</label>
              <textarea  class="input w-100 rounded" v-model="form.currentMedications" placeholder="Medications (if any)"></textarea>
            </div>
          </section>

          <!-- GENERAL HEALTH -->
          <section v-if="currentStepKey === 'general'">
            <p class="muted">“In the last 7 days, how would you rate…”</p>
            <div class="field" v-for="(q,index) in generalQuestions" :key="q.key">
              <label class="q-label mt-2">{{ (index + 1) }}. {{ q.label }}</label>
              <div class="scale-note text-center muted">1 = {{ q.scaleLow }} · 5 = {{ q.scaleHigh }}</div>
              <div class="scale-row">
                <button v-for="n in 5" :key="n" type="button" class="scale-btn" :class="{ selected: form.general[q.key] === n }" @click="setNested('general', q.key, n)">{{ n }}</button>
              </div>
              <div v-if="errors['general.' + q.key]" class="error">{{ errors['general.' + q.key] }}</div>
            </div>
          </section>

          <!-- LIFESTYLE HABITS -->
          <section v-if="currentStepKey === 'lifestyle'">
            <p class="muted">In the last 7 days, how often did you…</p>
            <div class="field" v-for="(q,index) in lifestyleQuestions" :key="q.key">
              <label class="q-label mt-2">{{ (index + 1) }}. {{ q.label }}</label>
              <div class="options-row wrap">
                <button v-for="opt in q.options" :key="opt" type="button" class="option" :class="{ selected: form.lifestyle[q.key] === opt }" @click="setNested('lifestyle', q.key, opt)">{{ opt }}</button>
              </div>
              <div v-if="errors['lifestyle.' + q.key]" class="error">{{ errors['lifestyle.' + q.key] }}</div>
            </div>
          </section>

          <!-- CONDITIONAL SECTIONS -->
          <template v-for="section in conditionSections">
            <section v-if="currentStepKey === section.key" :key="section.key">
              <div class="field">
                <label>Do you have {{ section.title }}?</label>
                <div class="options-row">
                  <button type="button" class="option" :class="{ selected: form[section.key].has===true }" @click="setNested(section.key,'has',true)">Yes</button>
                  <button type="button" class="option" :class="{ selected: form[section.key].has===false }" @click="setNested(section.key,'has',false)">No</button>
                </div>
                <div v-if="errors[section.key+'.has']" class="error">{{ errors[section.key+'.has'] }}</div>
              </div>

              <div v-if="form[section.key].has===true">
                <div class="field" v-for="(q,index) in section.questions" :key="q.key">
                  <label class="q-label mt-2">{{ (index + 1) }}. {{ q.label }}</label>
                  <div v-if="q.type==='scale'">
                    <div class="scale-note text-center muted">1 = {{ q.scaleLow }} · 5 = {{ q.scaleHigh }}</div>
                    <div class="scale-row">
                        <button v-for="n in 5" :key="n" type="button" class="scale-btn" :class="{ selected: form[section.key][q.key]===n }" @click="setNested(section.key,q.key,n)">{{ n }}</button>
                    </div>
                  </div>
                  <div v-if="q.options" class="options-row wrap">
                    <button v-for="opt in q.options" :key="opt" type="button" class="option" :class="{ selected: form[section.key][q.key]===opt }" @click="setNested(section.key,q.key,opt)">{{ opt }}</button>
                  </div>
                  <div v-if="errors[section.key+'.'+q.key]" class="error">{{ errors[section.key+'.'+q.key] }}</div>
                </div>
              </div>
            </section>
          </template>

          <!-- YOGA / PRANAYAMA / DIET -->
          <section v-if="currentStepKey === 'yoga'">
            <div class="field" v-for="(q,index) in yogaQuestions" :key="q.key">
              <label class="q-label mt-2">{{ (index + 1) }}. {{ q.label }}</label>
              <div v-if="q.type==='scale'">
                <div class="scale-note text-center muted">1 = {{ q.scaleLow }} · 5 = {{ q.scaleHigh }}</div>
                <div class="scale-row">
                    <button v-for="n in 5" :key="n" type="button" class="scale-btn" :class="{ selected: form.yoga[q.key]===n }" @click="setNested('yoga',q.key,n)">{{ n }}</button>
                </div>
              </div>
              <div v-if="q.options" class="options-row wrap">
                <button v-for="opt in q.options" :key="opt" type="button" class="option" :class="{ selected: form.yoga[q.key]===opt }" @click="setNested('yoga',q.key,opt)">{{ opt }}</button>
              </div>
              <div v-if="errors['yoga.' + q.key]" class="error">{{ errors['yoga.' + q.key] }}</div>
            </div>
          </section>

          <!-- MENTAL -->
          <section v-if="currentStepKey === 'mental'">
            <div class="field" v-for="(q,index) in mentalQuestions" :key="q.key">
              <label class="q-label mt-2">{{ (index + 1) }}. {{ q.label }}</label>
              <div class="options-row wrap" v-if="q.options">
                <button v-for="opt in q.options" :key="opt" type="button" class="option" :class="{ selected: form.mental[q.key]===opt }" @click="setNested('mental',q.key,opt)">{{ opt }}</button>
              </div>
              <div v-if="q.type==='scale'">
                <div class="scale-note text-center muted">1 = {{ q.scaleLow }} · 5 = {{ q.scaleHigh }}</div>
                <div class="scale-row">
                    <button v-for="n in 5" :key="n" type="button" class="scale-btn" :class="{ selected: form.mental[q.key]===n }" @click="setNested('mental',q.key,n)">{{ n }}</button>
                </div>
              </div>
              <div v-if="errors['mental.' + q.key]" class="error">{{ errors['mental.' + q.key] }}</div>
            </div>
          </section>

          <!-- NAV -->
          <div class="nav-row">
            <button type="button" class="btn secondary" :disabled="currentStep===0" @click="prevStep">Previous</button>
            <div>
              <button v-if="!isLastStep" type="button" class="btn primary" @click="nextStep">Next</button>
              <button v-else type="submit" class="btn submit">Submit</button>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</template>


<script setup>
import { reactive, ref, computed, nextTick } from 'vue';

const emit = defineEmits(['close']);
const formRef = ref(null);

const form = reactive({
  age:null, gender:'', shiftPattern:'', diagnosed:[], currentMedications:'',
  general:{ energy:null, sleep:null, pain:null, digestive:null, wellbeing:null },
  lifestyle:{ mealsOnTime:'', eatHealthy:'', consumeUnhealthy:'', physicalActivity:'', water:'', stressed:'' },
  hypertension:{ has:null, headaches:null, heaviness:null, saltAwareness:null, anger:null, bpMonitoring:null },
  diabetes:{ has:null, fastingControl:null, hungerThirst:null, mealTiming:null, sugaryConsumption:null, footCare:null },
  obesity:{ has:null, heaviness:null, portionControl:null, activityLevel:null, cravings:null, stairs:null },
  yoga:{ yogaDays:null, pranayamaDays:null, dietCompliance:null, foodAwareness:null, motivation:null },
  mental:{ calm:null, handleStress:null, negativeThoughts:null, relax:null, overall:null }
});

const steps = [
  { key:'basic', title:'Basic Details' },
  { key:'general', title:'General Health' },
  { key:'lifestyle', title:'Lifestyle Habits' },
  { key:'hypertension', title:'Hypertension' },
  { key:'diabetes', title:'Diabetes' },
  { key:'obesity', title:'Obesity' },
  { key:'yoga', title:'Yoga, Pranayama & Diet Compliance' },
  { key:'mental', title:'Mental & Emotional Wellbeing' }
];

const conditionSections = [
  { key:'hypertension', title:'Hypertension', questions:[
    { key:'headaches', label:'Frequency of headaches/dizziness', type:'scale',scaleLow:'Always', scaleHigh:'Never' },
    { key:'heaviness', label:'Sensation of heaviness or tightness', type:'scale',scaleLow:'Always', scaleHigh:'Never' },
    { key:'saltAwareness', label:'Salt intake awareness', type:'scale',scaleLow:' Not aware', scaleHigh:'Fully aware' },
    { key:'anger', label:'Ability to manage anger/irritation', type:'scale',scaleLow:'Very poo', scaleHigh:'Excellent' },
    { key:'bpMonitoring', label:'BP monitoring consistency', options:['Never','Rarely','Sometimes','Often','Regularly'] }
  ]},
  { key:'diabetes', title:'Diabetes', questions:[
    { key:'fastingControl', label:'Fasting sugar control (self-perception)', options:['Very poor','Poor','Moderate','Good','Excellent'] },
    { key:'hungerThirst', label:'Frequency of excessive hunger/thirst', options:['Always','Often','Sometimes','Rarely','Never'] },
    { key:'mealTiming', label:'Meal timing regularity', options:['Very irregular','Irregular','Moderate','Regular','Very regular'] },
    { key:'sugaryConsumption', label:'Consumption of sugary foods', options:['Always','Often','Sometimes','Rarely','Never'] },
    { key:'footCare', label:'Foot care awareness', type:'scale',scaleLow:'Very low ', scaleHigh:'Very high' }
  ]},
  { key:'obesity', title:'Obesity', questions:[
    { key:'heaviness', label:'Feeling of heaviness in the body', options:['Always','Often','Sometimes','Rarely','Never'] },
    { key:'portionControl', label:'Portion control during meals', options:['Very poor','Poor','Moderate','Good','Excellent'] },
    { key:'activityLevel', label:'Level of physical activity', options:['None','Very low','Moderate','Good','High'] },
    { key:'cravings', label:'Craving for junk food', options:['Always','Often','Sometimes','Rarely','Never'] },
    { key:'stairs', label:'Ability to climb stairs without fatigue', options:['Very difficult','Difficult','Moderate','Easy','Very easy'] }
  ]}
];

const generalQuestions=[
  { key:'energy', label:'Your energy levels', scaleLow:'Very low', scaleHigh:'Very high' },
  { key:'sleep', label:'Quality of sleep', scaleLow:'Very poor', scaleHigh:'Very good' },
  { key:'pain', label:'Frequency of body pain or stiffness', scaleLow:'Always', scaleHigh:'Never' },
  { key:'digestive', label:'Digestive health', scaleLow:'Very poor', scaleHigh:'Very good' },
  { key:'wellbeing', label:'Overall physical wellbeing', scaleLow:'Very low', scaleHigh:'Excellent' }
];

const lifestyleQuestions=[
  { key:'mealsOnTime', label:'Eat meals on time?', options:['Never','Rarely','Sometimes','Often','Always'] },
  { key:'eatHealthy', label:'Eat healthy food as recommended?', options:['Never','Rarely','Sometimes','Often','Always'] },
  { key:'consumeUnhealthy', label:'Consume sugary, oily, or salty foods?', options:['Always','Often','Sometimes','Rarely','Never'] },
  { key:'physicalActivity', label:'Perform any physical activity (walking/yoga/exercise)?', options:['0 days','1–2','3–4','5–6','Daily'] },
  { key:'water', label:'Drink enough water?', options:['Never','Rarely','Sometimes','Often','Always'] },
  { key:'stressed', label:'Feel stressed during the day?', options:['Always','Often','Sometimes','Rarely','Never'] }
];

const yogaQuestions=[
  { key:'yogaDays', label:'How many days did you practice yoga?', options:['0','1–2','3–4','5–6','Daily'] },
  { key:'pranayamaDays', label:'How many days did you practice pranayama?', options:['0','1–2','3–4','5–6','Daily'] },
  { key:'dietCompliance', label:'How often did you follow the recommended diet plan?', options:['Never','Rarely','Sometimes','Often','Always'] },
  { key:'foodAwareness', label:'How aware were you of foods to avoid for your condition?', type:'scale', scaleLow:'Not aware', scaleHigh:'Fully aware' },
  { key:'motivation', label:'How motivated did you feel to follow healthy habits?', type:'scale', scaleLow:'Not at all', scaleHigh:'Very motivated' }
];

const mentalQuestions=[
  { key:'calm', label:'How calm did you feel throughout the day?', options:['Very low','Low','Moderate','Good','Excellent'] },
  { key:'handleStress', label:'How well did you handle stress?', options:['Very poor','Poor','Moderate','Good','Excellent'] },
  { key:'negativeThoughts', label:'Frequency of negative thoughts', options:['Always','Often','Sometimes','Rarely','Never'] },
  { key:'relax', label:'Ability to relax after work', options:['Very poor','Poor','Moderate','Good','Excellent'] },
  { key:'overall', label:'Overall mental wellbeing', type:'scale', scaleLow:'Very low', scaleHigh:'Excellent' }
];

const currentStep = ref(0);
const errors = reactive({});
const currentStepKey = computed(()=>steps[currentStep.value].key);
const isLastStep = computed(()=>currentStep.value===steps.length-1);

const set = (field,value)=>form[field]=value;
const toggleDiagnosed = cond=>{
  if(cond==='None'){ form.diagnosed=['None']; return; }
  const idx=form.diagnosed.indexOf(cond);
  if(idx>=0) form.diagnosed.splice(idx,1);
  else form.diagnosed.push(cond);
  if(form.diagnosed.includes('None')) form.diagnosed=form.diagnosed.filter(d=>'None'!==d);
};
const hasDiagnosed = cond=>form.diagnosed.includes(cond);
const setNested = (section,key,value)=>form[section][key]=value;

const nextStep = () => {
    if(validateStep(currentStepKey.value)) {
        currentStep.value++;
        Object.keys(errors).forEach(k=>delete errors[k]);

        nextTick(() => {
            const card = document.querySelector('.form-card');
            if(card) card.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }
};

const prevStep = () => {
    currentStep.value--;
    Object.keys(errors).forEach(k=>delete errors[k]);

    nextTick(() => {
        const card = document.querySelector('.form-card');
        if(card) card.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
};

const onSubmit = ()=>{
  if(validateStep(currentStepKey.value)){
    alert('Assessment submitted successfully.');
    emit('close');
  }
};

const validateStep = stepKey=>{
  let valid=true; Object.keys(errors).forEach(k=>delete errors[k]);
  switch(stepKey){
    case 'basic':
      if(!form.age) { errors.age='Required'; valid=false; }
      if(!form.gender) { errors.gender='Required'; valid=false; }
      if(!form.shiftPattern){ errors.shiftPattern='Required'; valid=false; }
      if(form.diagnosed.length===0){ errors.diagnosed='Select at least one'; valid=false; }
      break;
    case 'general':
      generalQuestions.forEach(q=>{ if(!form.general[q.key]) { errors['general.'+q.key]='Required'; valid=false; } });
      break;
    case 'lifestyle':
      lifestyleQuestions.forEach(q=>{ if(!form.lifestyle[q.key]){ errors['lifestyle.'+q.key]='Required'; valid=false; } });
      break;
    case 'hypertension':
      if(form.hypertension.has===null){ errors['hypertension.has']='Required'; valid=false; }
      if(form.hypertension.has===true){
        conditionSections.find(s=>s.key==='hypertension').questions.forEach(q=>{
          if(q.type==='scale' && !form.hypertension[q.key]){ errors['hypertension.'+q.key]='Required'; valid=false; }
          if(q.options && !form.hypertension[q.key]){ errors['hypertension.'+q.key]='Required'; valid=false; }
        });
      }
      break;
    case 'diabetes':
      if(form.diabetes.has===null){ errors['diabetes.has']='Required'; valid=false; }
      if(form.diabetes.has===true){
        conditionSections.find(s=>s.key==='diabetes').questions.forEach(q=>{
          if(q.type==='scale' && !form.diabetes[q.key]){ errors['diabetes.'+q.key]='Required'; valid=false; }
          if(q.options && !form.diabetes[q.key]){ errors['diabetes.'+q.key]='Required'; valid=false; }
        });
      }
      break;
    case 'obesity':
      if(form.obesity.has===null){ errors['obesity.has']='Required'; valid=false; }
      if(form.obesity.has===true){
        conditionSections.find(s=>s.key==='obesity').questions.forEach(q=>{
          if(q.type==='scale' && !form.obesity[q.key]){ errors['obesity.'+q.key]='Required'; valid=false; }
          if(q.options && !form.obesity[q.key]){ errors['obesity.'+q.key]='Required'; valid=false; }
        });
      }
      break;
    case 'yoga':
      yogaQuestions.forEach(q=>{ if(!form.yoga[q.key]){ errors['yoga.'+q.key]='Required'; valid=false; } });
      break;
    case 'mental':
      mentalQuestions.forEach(q=>{ if(!form.mental[q.key]){ errors['mental.'+q.key]='Required'; valid=false; } });
      break;
  }
  return valid;
};
</script>

<style scoped>
.pha-container{max-width:700px;margin:0 auto;font-family:Arial,sans-serif;}
.title{text-align:center;margin-bottom:20px;}
.card{background:#fff;border-radius:8px;box-shadow:0 2px 10px rgba(0,0,0,0.05);}
.field{margin-bottom:15px;}
.options-row{display:flex;gap:10px;margin-top:5px;flex-wrap:wrap;}
.option{padding:6px 12px;border:1px solid #ccc;border-radius:4px;background:#f9f9f9;cursor:pointer;}
.option.selected{background:#6c757d;color:#fff;border-color:#6c757d;}
.scale-row{display:flex;gap:10px;margin-top:5px;justify-content:center;}
.scale-btn{width:40px;height:40px;border:1px solid #ccc;border-radius:50%;background:#f9f9f9;cursor:pointer;}
.scale-btn.selected{background:#6c757d;color:#fff;border-color:#6c757d;}
.error{color:red;font-size:12px;margin-top:3px;}
.nav-row{display:flex;justify-content:space-between;margin-top:20px;}
.btn{padding:6px 14px;border:none;border-radius:4px;cursor:pointer;}
.btn.primary{background:#D80621;color:#fff;}
.btn.secondary{background:#ccc;color:#000;}
.btn.submit{background:#28a745;color:#fff;}
.muted{color:#666;font-size:14px;margin-bottom:10px;}
.step-title{color: #080606;}
.q-label{ color: #080606;}
</style>

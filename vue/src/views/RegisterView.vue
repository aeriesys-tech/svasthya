<template>
    <div class="container py-5 d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card shadow-sm p-4" style="max-width: 500px; width: 100%;">
            <h3 class="text-center mb-4">User Registration</h3>

            <!-- Step 1: Basic Info -->
            <div v-if="step === 1">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="kgid" placeholder="KGID Number" v-model.trim="form.kgid" />
                    <label for="kgid">KGID Number*</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="tel" class="form-control" id="mobile" placeholder="Mobile Number" v-model.trim="form.mobile" />
                    <label for="mobile">Mobile Number*</label>
                </div>

                <div class="form-floating mb-3">
                    <input type="password" class="form-control" id="password" placeholder="Password" v-model.trim="form.password" />
                    <label for="password">Password*</label>
                </div>

                <button class="btn btn-primary w-100" @click="requestOtp" :disabled="loading">
                    <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                    Request OTP
                </button>
            </div>

            <!-- Step 2: OTP Verification -->
            <div v-else-if="step === 2">
                <div class="form-floating mb-3">
                    <input type="text" class="form-control" id="otp" placeholder="Enter OTP" v-model.trim="form.otp" />
                    <label for="otp">Enter OTP*</label>
                </div>

                <button class="btn btn-primary w-100 mb-3" @click="verifyOtp" :disabled="loading">
                    <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                    Verify OTP
                </button>

                <!-- Resend OTP Section -->
                <div class="text-center small mb-3">
                    <span v-if="countdown > 0">
                        Resend OTP in <strong>{{ countdown }}</strong> seconds
                    </span>
                    <button
                        v-else
                        class="btn btn-link p-0 text-decoration-none"
                        @click="resendOtp"
                        :disabled="loading"
                    >
                        Resend OTP
                    </button>
                </div>

                <button class="btn btn-outline-secondary w-100" @click="goBackToStep(1)">← Back</button>
            </div>

            <!-- Step 3: Profile Details -->
            <div v-else-if="step === 3">
                <form @submit.prevent="submitProfile">
                    <div class="row">
                        <!-- Name -->
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="name" v-model.trim="profile.name" />
                                <label for="name">Name*</label>
                            </div>
                        </div>

                        <!-- Date of Birth -->
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input type="date" class="form-control" id="dob" v-model="profile.dob" />
                                <label for="dob">Date of Birth*</label>
                            </div>
                        </div>

                        <!-- Phone Number -->
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <input type="tel" class="form-control" id="phone" v-model.trim="profile.phone" />
                                <label for="phone">Phone Number*</label>
                            </div>
                        </div>

                        <!-- Gender -->
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <select class="form-select" id="gender" v-model="profile.gender">
                                    <option value="" disabled>Select Gender</option>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                    <option value="Others">Others</option>
                                </select>
                                <label for="gender">Gender*</label>
                            </div>
                        </div>

                        <!-- Designation -->
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <select class="form-select" id="designation" v-model="profile.designation">
                                    <option value="" disabled>Select Designation</option>
                                    <option v-for="d in designations" :key="d" :value="d">{{ d }}</option>
                                </select>
                                <label for="designation">Designation*</label>
                            </div>
                        </div>

                        <!-- Division -->
            <div class="col-12">
                <div class="form-floating mb-3">
                    <select class="form-select" id="division" v-model="profile.division" @change="updatePlaces">
                        <option value="" disabled>Select Division</option>
                        <option v-for="(division, index) in divisions" :key="index" :value="division">
                            {{ division }}
                        </option>
                    </select>
                    <label for="division">Division*</label>
                </div>
            </div>

            <!-- Current place of working -->
            <div class="col-12">
                <div class="form-floating mb-3">
                    <select class="form-select" v-model="profile.currentWorking">
                        <option value="" disabled>Select Current Place of Working</option>
                        <option v-for="(place, index) in currentPlaces" :key="index" :value="place">
                            {{ place }}
                        </option>
                    </select>
                    <label for="current-working">Current Place of Working*</label>
                </div>
            </div>                          

                        <!-- Qualification -->
                        <div class="col-12">
                            <div class="form-floating mb-3">
                                <select class="form-select" id="qualification" v-model="profile.qualification">
                                    <option value="" disabled>Select Qualification</option>
                                    <option v-for="q in qualifications" :key="q" :value="q">{{ q }}</option>
                                </select>
                                <label for="qualification">Qualification*</label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100" :disabled="loading">
                        <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                        Submit & Continue
                    </button>

                    <button class="btn btn-outline-secondary w-100 mt-3" type="button" @click="goBackToStep(2)">← Back</button>
                </form>
            </div>
        </div>
    </div>
    <Toast ref="toastRef" />
</template>

<script setup>
    import { ref, onUnmounted } from 'vue'
    import { useRouter } from 'vue-router'
    import { TOAST_SUCCESS, TOAST_ERROR } from '@/utils/config';
    import Toast from '@/components/Toast.vue';
    

    const router = useRouter()
    const toastRef = ref(null);
    const step = ref(1)
    const loading = ref(false)
    const countdown = ref(0)
    let timer = null

    const form = ref({
        kgid: '',
        mobile: '',
        password: '',
        otp: ''
    })

    const profile = ref({
        name: '',
        dob: '',
        phone: '',
        gender: '',
        designation: '',
        division: '',
        currentWorking: '',
        physicalHealth: '',
        qualification: ''
    })

    const designations = [
        "DGP", "ADGP", "IGP", "DIGP", "SGSP", "SP", "Addl SP",
        "DSP", "PI", "SI", "ASI", "HC", "PC"
    ]

   // Updated divisions
    const divisions = [
        "Central Division",
        "East Division",
        "West Division",
        "North Division",
        "South Division",
        "South East Division",
        "North East Division",
        "Whitefield Division",
        "Soth West Division",
        "North West Division",
        "Electronic City Division",
        "Traffic East Division",
        "Traffic West Division",
        "Traffic North Division",
        "Traffic South Division",
        "CAR South Division",
        "CAR North Division",
        "CAR Head Quarters",
        "CAR West"
    ]

    // Dummy places associated with divisions
  const divisionPlaces = {
    "Central Division": [
        "DCP CENTRAL",
        "ACP SESHADRIPURAM",
        "PI SESHADRIPURAM PS",
        "PI HIGH GROUND PS",
        "PI VYALIKAVAL PS",
        "PI SADASHIVA NAGAR PS",
        "ACP CUBBON PARK",
        "PI CUBBON PARK PS",
        "PI ASHOKNAGAR",
        "PI VIVEKNAGAR",
        "PI VIDHANA SOUDHA PS",
        "ACP HALASURU GATE",
        "PI HALASURU GATE PS",
        "PI WILSON GARDEN PS",
        "PI S.R NAGAR PS",
        "PI S.J PARK PS",
        "CEN PS (CYBER ECONOMIC NARCOTICS)"
    ],
    "East Division": [
        "DCP EAST",
        "ACP HALASURU",
        "ACP PULAKESHINAGAR",
        "ACP BANASWADI",
        "ACP K G HALLI",
        "PI HALASURU PS",
        "PI INDIRANAGAR PS",
        "PI BYAPPANAHALLI PS",
        "PI J.B.NAGAR PS",
        "PI BHARATHINAGAR PS",
        "PI PULAKESHINAGAR PS",
        "PI SHIVAJINAGAR PS",
        "PI COMMERCIAL STREET PS",
        "PI SHIVAJINAGAR WOMEM PS",
        "PI K.G.HALLI PS",
        "PI RAMAMURTHY NAGAR PS",
        "PI HENNURU PS",
        "PI BANASWADI PS",
        "PI D.J.HALLI PS",
        "PI GOVINDRAJPURA PS",
        "CEN PS (CYBER ECONOMIC NARCOTIC)"
    ],
    "West Division": [
        "DCP West",
        "ACP Chickpete",
        "PI Upparpete",
        "PI Kalasipalya",
        "PI City Market",
        "PI Chamarajpete",
        "PI Cottonpete",
        "ACP Byatarayanapura",
        "PI Byatarayanapura",
        "PI Annapurneshwari Nagar",
        "PI Chandra Layout",
        "PI J.J.Nagar",
        "PI R R NAGAR",
        "ACP Vijayanagar",
        "PI Vijayanagar",
        "PI Kamakshipalya",
        "PI BasaveshwaraNagar",
        "PI Govindarajanagar",
        "PI Magadi Road",
        "PI K.P.Agrahara",
        "PI Women PS",
        "ACP CEN WEST",
        "PI CEN Police Station-"
    ],
    "North Division": [
        "DCP North",
        "ACP Malleshwaram",
        "PI Malleshwaram",
        "PI Srirampura",
        "PI Rajajinagar",
        "PI SubramanyaNagar",
        "PI Mahalakshmi Layout",
        "PI Nandini Layout",
        "PI Women PS",
        "ACP Yeshwanthapura",
        "PI Yeshwanthapura",
        "PI R.M.C Yard",
        "PI Jalahalli",
        "ACP J.C.Nagar",
        "PI J.C.Nagar",
        "PI Sanjayanagar",
        "PI Hebbala",
        "PI R.T.Nagar",
        "ACP CEN NORTH",
        "PI CEN Police Station- DCP"
    ],
    "South Division": [
        "DCP South",
        "ACP Jayanagar",
        "PI Jayanagar",
        "PI Basavanagudi",
        "PI J.P.Nagar",
        "PI Siddapura",
        "PI Banashankari",
        "PI PuttenaHalli",
        "ACP V.V.Puram",
        "PI V V Puram",
        "PI Hanumantha Nagara",
        "PI K G Nagar",
        "PI ShankaraPuram",
        "PI Giri Nagar",
        "PI C K Acchukattu",
        "PI WOMEN B.Gudi",
        "ACP CEN SOUTH",
        "PI CEN Police Station-"
    ],
    "South East Division": [
        "DCP South-East",
        "ACP Mico Layout",
        "PI Thilakanagar",
        "PI Mico Layout",
        "PI Bommanahalli",
        "PI Suddaguntepalya",
        "ACP Madivala",
        "PI Koramangala",
        "PI Madivala",
        "PI H.S.R Layout",
        "PI Adugodi",
        "PI Women",
        "ACP CEN South East",
        "PI CEN Police Station- HSR"
    ],
    "North East Division": [
        "DCP NORTH-EAST",
        "ACP YELAHANKA",
        "PI YELAHANKA PS",
        "PI YELAHANKA NEW TOWN PS",
        "PI KODIGEHALLI PS",
        "PI VIDYARANYAPURA PS",
        "ACP SAMPIGEHALLI",
        "PI SAMPIGEHALLI PS",
        "PI KOTHANURU PS",
        "PI BHAGALURU PS",
        "PI AMRUTHAHALLI PS",
        "ACP DEVANAHALLI",
        "PI INTERNATIONAL AIRPORT PS",
        "PI DEVANAHALLI PS",
        "PI CHIKKAJALA PS",
        "ACP CEN PS",
        "CEN PS",
        "PI WOMEN PS NORTHEAST"
    ],
    "Whitefield Division": [
        "DCP WhiteField",
        "ACP WhiteField",
        "PI White Field",
        "PI K.R.Puram",
        "PI Mahadevapura",
        "PI Kadugodi",
        "PI Avalahalli",
        "PI Women PS",
        "ACP Marathahalli",
        "PI Marathahalli",
        "PI H.A.L",
        "PI Bellanduru",
        "PI Varthur",
        "ACP CEN Whitefield",
        "PI CEN Police Station-Whitefield PS"
    ],
    "Soth West Division": [
        "DCP South West",
        "ACP Kengeri",
        "PI Kengeri",
        "PI Jnanabharathi",
        "PI Kumbalagodu",
        "ACP SubramanyaPura",
        "PI SubramanyaPura",
        "PI KS Layout",
        "PI TalaghattaPura",
        "PI Konanakunte"
    ],
    "North West Division": [
        "DCP North West",
        "ACP Peenya",
        "PI Peenya",
        "PI Rajagopal Nagar",
        "PI Bagalgunte",
        "PI Byadralli",
        "PI Soladevanahalli",
        "PI Gangammanagudi"
    ],
    "Electronic City Division": [
        "DCP Electronic City",
        "ACP Electronic City",
        "PI Electronic City",
        "PI Bandepalya",
        "PI Hulimavu",
        "PI Beguru",
        "PI ParappanaAgrahara",
        "PI Hebbagodi"
    ],
    "Traffic East Division": [
        "DCP TRAFFIC EAST OFFICE",
        "ACP EAST TRAFFIC OFFICE",
        "ACP WHITEFIELD TRAFFIC",
        "ACP TRAFFIC PLANNING",
        "BANASAWADI TR. PS",
        "FRAZER TOWN TR. PS",
        "SHIVAJINAGAR TR.PS",
        "INDIRANAGAR TR.PS",
        "HALASOOR TR.PS",
        "KGHALLI TR. PS",
        "AIRPORT TR. PS",
        "K.R.PURAM TR.PS",
        "WHITEFIELD TR. PS",
        "MAHADEVAPURA TR. PS"
    ],
    "Traffic West Division": [
        "DCP Traffic West Division",
        "ACP Traffic Central Sub Div",
        "ACP Traffic West Sub Div",
        "ACP Traffic Vijayanagara Sub Div",
        "Ashoknagara Traffic PS",
        "Cubbon park Traffic PS",
        "Halsur gate Traffic PS",
        "High Grounds Traffic PS",
        "Sadashivanagara Traffic PS",
        "Wilson Garden traffic PS",
        "Upparpete Traffic PS",
        "Chamarajapete Traffic PS",
        "City Market Traffic PS",
        "Byatarayanapura Traffic PS",
        "Kengeri Traffic PS",
        "Kamakshipalya Traffic PS",
        "Vijaynagara Traffic PS",
        "Magadi Road Traffic Ps",
        "Jnanabharathi Traffic Ps",
        "Sheshadripuram Traffic PS"
    ],
    "Traffic North Division": [
        "DCP Traffic North",
        "ACP Traffic North",
        "PI Tr. Rajajinagar",
        "PI Tr. Yeshwanthapura",
        "PI TR. Jalahalli",
        "PI Tr. Peenya",
        "PI Tr. R T Nagar",
        "PI Tr. Hebbal",
        "PI Tr. Devanahalli",
        "PI Tr. Yelahanka",
        "PI Tr. Chikkajala",
        "PI Tr. Hennuru"
    ],
    "Traffic South Division": [
        "DCP Traffic South",
        "ACP Tr. Southeast",
        "PI Tr. MicoLayout",
        "PI Tr. Madivala",
        "PI Tr. Hulimavu",
        "PI Tr. Adugodi",
        "ACP Tr. HSR Layout",
        "PI Tr. Electronic City",
        "PI Tr. Bellanduru",
        "ACP Tr. South",
        "PI Tr. Jayanagara",
        "PI Tr. VV Puram",
        "PI Tr. Basavanagudi",
        "PI Tr. KS Layout",
        "PI Tr. Banashankari",
        "PI Tr. TalaghattaPur"
    ],
    "CAR South Division": [
        "DCP CAR (South)",
        "CAR South Control Room",
        "ACP CAR South-I",
        "ACP CAR South-II",
        "ACP CAR South-III",
        "ACP CAR South-IV"
    ],
    "CAR North Division": [
        "DCP CAR North",
        "CAR North Control Room",
        "ACP CAR North-I",
        "ACP CAR North-II",
        "ACP CAR North-III"
    ],
    "CAR Head Quarters": [
        "CAR HQRS.Control Room",
        "DCP CAR (HQ)",
        "ACP CAR HQRS-I",
        "ACP CAR HQRS-II",
        "ACP CAR HQRS-III",
        "ACP CAR HQRS-IV",
        "ACP CAR HQRS-V"
    ],
    "CAR West": [
        "DCP CAR (WEST)",
        "CAR WEST CONTROL ROOM",
        "ACP CAR WEST"
    ]
}

    const currentPlaces = ref([])

    const updatePlaces = () => {
        currentPlaces.value = divisionPlaces[profile.value.division] || []
        profile.value.currentWorking = '' // reset current working place when division changes
    }

    const qualifications = [
        "10th", "PUC", "Degree", "Post Graduation", "MPhil", "PhD", "Others"
    ]

    function startCountdown() {
        countdown.value = 30
        timer = setInterval(() => {
            if (countdown.value > 0) {
                countdown.value--
            } else {
                clearInterval(timer)
            }
        }, 1000)
    }

    const requestOtp = () => {
        if (!form.value.kgid || !form.value.mobile || !form.value.password) {
            toastRef.value.show("Please fill all required fields.", TOAST_ERROR);
            return
        }

        loading.value = true
        setTimeout(() => {
            loading.value = false
            toastRef.value.show("OTP sent successfully.", TOAST_SUCCESS);
            step.value = 2
            startCountdown()
        }, 1000)
    }

    const resendOtp = () => {
        loading.value = true
        setTimeout(() => {
            loading.value = false
            toastRef.value.show("OTP resent successfully.", TOAST_SUCCESS);
            startCountdown()
        }, 1000)
    }

    const verifyOtp = () => {
        if (!form.value.otp) {
            toastRef.value.show("Please enter the OTP.", TOAST_ERROR);
            return
        }

        loading.value = true
        setTimeout(() => {
            loading.value = false
            toastRef.value.show("OTP verified successfully.", TOAST_SUCCESS);
            step.value = 3
            clearInterval(timer)
        }, 1000)
    }

    const submitProfile = () => {
        if (!profile.value.name || !profile.value.dob || !profile.value.designation) {
            toastRef.value.show("Please complete all required fields.", TOAST_ERROR);
            return
        }

        loading.value = true
        setTimeout(() => {
            loading.value = false
            toastRef.value.show("Registration complete! Redirecting...", TOAST_SUCCESS);
            router.push('/dashboard')
        }, 1000)
    }

    const goBackToStep = (n) => {
        step.value = n
        window.scrollTo({ top: 0, behavior: 'smooth' })
    }

    onUnmounted(() => {
        if (timer) clearInterval(timer)
    });
</script>

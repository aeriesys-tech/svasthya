<template>
    <div class="container py-4">
        <h4 class="text-center mb-4">Financial Management</h4>

        <div class="row g-3">
            <div
                v-for="(section, index) in sections"
                :key="index"
                class="col-12 col-lg-6"
            >
                <div class="text-center mb-3">
                    <button
                        class="btn btn-accent w-100"
                        @click="toggleSection(index)"
                    >
                        {{ section.name }}
                    </button>
                </div>

                <!-- Questions -->
                <div v-if="section.show" class="card shadow-sm mb-4">
                    <div class="card-header bg-accent">
                        <h5 class="mb-0">{{ section.name }}</h5>
                    </div>

                    <div class="card-body">
                        <div
                            v-for="(question, qIndex) in section.questions"
                            :key="qIndex"
                            class="mb-3"
                        >
                            <p class="fw-semibold">{{ question.text }}</p>
                            <div class="d-flex flex-column ms-3">
                                <label
                                    v-for="(label, n) in ratingOptions"
                                    :key="n"
                                    class="form-check mb-1"
                                >
                                    <input
                                        type="radio"
                                        class="form-check-input me-2"
                                        :name="`q-${index}-${qIndex}`"
                                        :value="n + 1"
                                        v-model.number="question.answer"
                                    />
                                    {{ label }}
                                </label>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <button
                                class="btn btn-success"
                                @click="calculateScore(section)"
                            >
                                Submit Assessment
                            </button>
                        </div>

                        <div
                            v-if="section.result"
                            class="alert alert-info mt-3"
                        >
                            <strong>Total Score:</strong> {{ section.result.total }} <br />
                            <strong>Interpretation:</strong> {{ section.result.interpretation }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { reactive } from 'vue'

const ratingOptions = [
    'Strongly Disagree',
    'Disagree',
    'Neutral',
    'Agree',
    'Strongly Agree',
]

const sections = reactive([
    {
        name: 'Financial Knowledge',
        show: false,
        questions: [
            { text: 'I understand how bank interest rates affect savings and loans.', answer: null },
            { text: 'I am aware of the benefits and risks of investing in mutual funds.', answer: null },
            { text: 'I can identify the difference between LIC policies, chit funds, and SIPs.', answer: null },
            { text: 'I understand terms like inflation, GST, and income tax.', answer: null },
            { text: 'I am aware of how UPI, net banking, and mobile wallets work.', answer: null },
            { text: 'I know the basics of credit cards, EMIs, and CIBIL scores.', answer: null },
            { text: 'I am aware of government schemes like Sukanya Samriddhi Yojana or PPF.', answer: null },
            { text: 'I follow financial news or advice through newspapers, TV, or YouTube channels.', answer: null },
            { text: 'I understand how gold prices and fuel rates affect the economy.', answer: null },
            { text: 'I can plan my finances considering both income and liabilities.', answer: null },
        ],
        result: null,
    },
    {
        name: 'Savings Behaviour',
        show: false,
        questions: [
            { text: 'I keep a part of my monthly income aside for savings.', answer: null },
            { text: 'I have a recurring deposit or fixed deposit in a bank or post office.', answer: null },
            { text: 'I avoid withdrawing money from my savings for unnecessary expenses.', answer: null },
            { text: 'I have separate savings for emergencies like hospital bills.', answer: null },
            { text: 'I believe in saving small amounts regularly (e.g., piggy bank, bhisi/chit).', answer: null },
            { text: 'I save money during festivals or harvest seasons for future use.', answer: null },
            { text: 'I teach my children and family members the importance of saving.', answer: null },
            { text: 'I try to reduce expenses to increase my savings.', answer: null },
            { text: 'I deposit gifts or bonuses received during weddings/festivals into savings.', answer: null },
            { text: 'I believe saving is more important than showing off wealth.', answer: null },
        ],
        result: null,
    },
    {
        name: 'Expenditure Pattern',
        show: false,
        questions: [
            { text: 'I maintain a monthly budget for household expenses.', answer: null },
            { text: 'I avoid spending beyond my means, even with credit options.', answer: null },
            { text: 'I prioritise essential needs over luxuries.', answer: null },
            { text: 'I compare prices before buying clothes, groceries, or electronics.', answer: null },
            { text: 'I plan and save before making major purchases like a mobile phone or vehicle.', answer: null },
            { text: 'I avoid borrowing for non-essential expenses like weddings or vacations.', answer: null },
            { text: 'I track my spending using a diary, notebook, or mobile app.', answer: null },
            { text: 'I avoid unnecessary online shopping or quick loan offers.', answer: null },
            { text: 'I buy groceries or household items in bulk to save money.', answer: null },
            { text: 'I spend cautiously during festivals or sales, without crossing my budget.', answer: null },
        ],
        result: null,
    },
    {
        name: 'Investment Behaviour',
        show: false,
        questions: [
            { text: 'I invest regularly in options like PPF, mutual funds, gold, or real estate.', answer: null },
            { text: 'I prefer long-term investments over quick gains.', answer: null },
            { text: 'I invest after discussing with trusted people or financial advisors.', answer: null },
            { text: 'I understand the risks in the share market and invest accordingly.', answer: null },
            { text: 'I keep a record of my investments (passbook, demat, app, etc.).', answer: null },
            { text: 'I review my investments at least once a year.', answer: null },
            { text: 'I invest in government bonds, tax-saving instruments, or LIC policies.', answer: null },
            { text: 'I plan investments based on goals like children’s education or retirement.', answer: null },
            { text: 'I avoid investing in schemes that promise unusually high returns.', answer: null },
            { text: 'I believe investments are necessary for financial security and independence.', answer: null },
        ],
        result: null,
    },
])

function toggleSection(index) {
    sections[index].show = !sections[index].show
}

function calculateScore(section) {
    const total = section.questions.reduce((sum, q) => sum + (q.answer || 0), 0)
    let interpretation = ''

    if (total >= 41) interpretation = 'Excellent awareness and behaviour'
    else if (total >= 31) interpretation = 'Good, but needs some improvement'
    else if (total >= 21) interpretation = 'Basic level, should enhance financial discipline'
    else interpretation = 'Poor awareness, high risk of financial mismanagement'

    section.result = { total, interpretation }
}
</script>

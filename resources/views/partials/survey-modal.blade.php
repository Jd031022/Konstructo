<!-- Client Satisfaction Survey Modal -->
<div id="survey-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 px-4" x-data="surveyModal()" x-show="showModal" x-transition>
    <div class="relative top-4 mx-auto p-4 w-full max-w-2xl max-h-[85vh] overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-xl">
            <!-- Header -->
            <div class="bg-[#155386] text-white px-6 py-4 rounded-t-2xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold">Client Satisfaction Measurement</h2>
                        <p class="text-sm opacity-90">Help us serve you better!</p>
                    </div>
                    <button @click="closeModal()" class="text-white hover:text-gray-200 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Progress Indicator -->
            <div class="px-6 py-4 bg-gray-50 border-b">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Page <span x-text="currentPage"></span> of 2</span>
                    <span class="text-sm text-gray-500" x-text="currentPage === 1 ? 'Citizen\'s Charter' : 'Service Quality'"></span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-[#155386] h-2 rounded-full transition-all duration-300" :style="`width: ${(currentPage / 2) * 100}%`"></div>
                </div>
            </div>

            <!-- Form Content -->
            <form @submit.prevent="submitSurvey()" class="p-6">
                <!-- Page 1: Citizen's Charter Questions -->
                <div x-show="currentPage === 1" x-transition>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Citizen's Charter (CC) Questions</h3>
                        <p class="text-sm text-gray-600 mb-6">
                            The Citizen's Charter is an official document that reflects the services of a government agency/office including its requirements, fees, and processing times among others.
                        </p>

                        <!-- Client Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Client type *</label>
                                <select x-model="form.client_type" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                                    <option value="">Select client type</option>
                                    <option value="citizen">Citizen</option>
                                    <option value="business">Business</option>
                                    <option value="government">Government (Employee or another agency)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date *</label>
                                <input type="date" x-model="form.survey_date" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sex *</label>
                                <select x-model="form.sex" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                                    <option value="">Select sex</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Age *</label>
                                <input type="number" x-model="form.age" required min="1" max="120" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Region of residence</label>
                            <input type="text" x-model="form.region_of_residence" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent">
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service Availed *</label>
                            <input type="text" x-model="form.service_availed" required readonly class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 cursor-not-allowed">
                        </div>

                        <!-- CC1 -->
                        <div class="mb-6">
                            <p class="font-medium text-gray-900 mb-3">CC1. Which of the following best describes your awareness of a CC?</p>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" x-model="form.cc1_awareness" value="1" class="mr-2">
                                    <span class="text-sm">1. I know what a CC is and I saw this office's CC.</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" x-model="form.cc1_awareness" value="2" class="mr-2">
                                    <span class="text-sm">2. I know what a CC is but I did NOT see this office's CC.</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" x-model="form.cc1_awareness" value="3" class="mr-2">
                                    <span class="text-sm">3. I learned of the CC only when I saw this office's CC.</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" x-model="form.cc1_awareness" value="4" class="mr-2">
                                    <span class="text-sm">4. I do not know what a CC is and I did not see one in this office.</span>
                                </label>
                            </div>
                        </div>

                        <!-- CC2 -->
                        <div class="mb-6" x-show="form.cc1_awareness && form.cc1_awareness !== '4'">
                            <p class="font-medium text-gray-900 mb-3">CC2. If aware of CC (answered 1-3 in CC1), would you say that the CC of this office was?</p>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" x-model="form.cc2_helpfulness" value="1" class="mr-2">
                                    <span class="text-sm">1. Easy to see</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" x-model="form.cc2_helpfulness" value="2" class="mr-2">
                                    <span class="text-sm">2. Somewhat easy to see</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" x-model="form.cc2_helpfulness" value="3" class="mr-2">
                                    <span class="text-sm">3. Difficult to see</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" x-model="form.cc2_helpfulness" value="4" class="mr-2">
                                    <span class="text-sm">4. Not visible at all</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" x-model="form.cc2_helpfulness" value="5" class="mr-2">
                                    <span class="text-sm">5. N/A</span>
                                </label>
                            </div>
                        </div>

                        <!-- CC3 -->
                        <div class="mb-6" x-show="form.cc1_awareness && form.cc1_awareness !== '4'">
                            <p class="font-medium text-gray-900 mb-3">CC3. If aware of CC (answered codes 1-3 in CC1), how much did the CC help you in your transaction?</p>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" x-model="form.cc3_help_level" value="1" class="mr-2">
                                    <span class="text-sm">1. Helped very much</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" x-model="form.cc3_help_level" value="2" class="mr-2">
                                    <span class="text-sm">2. Somewhat helped</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" x-model="form.cc3_help_level" value="3" class="mr-2">
                                    <span class="text-sm">3. Did not help</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" x-model="form.cc3_help_level" value="4" class="mr-2">
                                    <span class="text-sm">4. N/A</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Page 2: Service Quality Dimensions -->
                <div x-show="currentPage === 2" x-transition>
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Service Quality Dimensions (SQD)</h3>
                        <p class="text-sm text-gray-600 mb-6">
                            Please check mark (✔) your answer to the following questions.
                        </p>

                        <!-- SQD Questions -->
                        <div class="space-y-6">
                            <div>
                                <p class="font-medium text-gray-900 mb-3">SQD0. I am satisfied with the service that I availed.</p>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd0_satisfied" value="1" class="mr-2">
                                        <span class="text-sm">1. Strongly Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd0_satisfied" value="2" class="mr-2">
                                        <span class="text-sm">2. Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd0_satisfied" value="3" class="mr-2">
                                        <span class="text-sm">3. Neither Agree nor Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd0_satisfied" value="4" class="mr-2">
                                        <span class="text-sm">4. Agree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd0_satisfied" value="5" class="mr-2">
                                        <span class="text-sm">5. Strongly Agree</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <p class="font-medium text-gray-900 mb-3">SQD1. I spent a reasonable amount of time for my transaction.</p>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd1_reasonable_time" value="1" class="mr-2">
                                        <span class="text-sm">1. Strongly Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd1_reasonable_time" value="2" class="mr-2">
                                        <span class="text-sm">2. Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd1_reasonable_time" value="3" class="mr-2">
                                        <span class="text-sm">3. Neither Agree nor Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd1_reasonable_time" value="4" class="mr-2">
                                        <span class="text-sm">4. Agree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd1_reasonable_time" value="5" class="mr-2">
                                        <span class="text-sm">5. Strongly Agree</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <p class="font-medium text-gray-900 mb-3">SQD2. The office followed the transaction's requirements and steps based on the information provided.</p>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd2_requirements_followed" value="1" class="mr-2">
                                        <span class="text-sm">1. Strongly Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd2_requirements_followed" value="2" class="mr-2">
                                        <span class="text-sm">2. Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd2_requirements_followed" value="3" class="mr-2">
                                        <span class="text-sm">3. Neither Agree nor Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd2_requirements_followed" value="4" class="mr-2">
                                        <span class="text-sm">4. Agree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd2_requirements_followed" value="5" class="mr-2">
                                        <span class="text-sm">5. Strongly Agree</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <p class="font-medium text-gray-900 mb-3">SQD3. The steps (including payment) I needed to do for my transaction were easy and simple.</p>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd3_steps_easy" value="1" class="mr-2">
                                        <span class="text-sm">1. Strongly Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd3_steps_easy" value="2" class="mr-2">
                                        <span class="text-sm">2. Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd3_steps_easy" value="3" class="mr-2">
                                        <span class="text-sm">3. Neither Agree nor Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd3_steps_easy" value="4" class="mr-2">
                                        <span class="text-sm">4. Agree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd3_steps_easy" value="5" class="mr-2">
                                        <span class="text-sm">5. Strongly Agree</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <p class="font-medium text-gray-900 mb-3">SQD4. I easily found information about my transaction from the office or its website.</p>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd4_info_easy_find" value="1" class="mr-2">
                                        <span class="text-sm">1. Strongly Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd4_info_easy_find" value="2" class="mr-2">
                                        <span class="text-sm">2. Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd4_info_easy_find" value="3" class="mr-2">
                                        <span class="text-sm">3. Neither Agree nor Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd4_info_easy_find" value="4" class="mr-2">
                                        <span class="text-sm">4. Agree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd4_info_easy_find" value="5" class="mr-2">
                                        <span class="text-sm">5. Strongly Agree</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <p class="font-medium text-gray-900 mb-3">SQD5. I paid a reasonable amount of fees for my transaction.</p>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd5_reasonable_fees" value="1" class="mr-2">
                                        <span class="text-sm">1. Strongly Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd5_reasonable_fees" value="2" class="mr-2">
                                        <span class="text-sm">2. Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd5_reasonable_fees" value="3" class="mr-2">
                                        <span class="text-sm">3. Neither Agree nor Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd5_reasonable_fees" value="4" class="mr-2">
                                        <span class="text-sm">4. Agree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd5_reasonable_fees" value="5" class="mr-2">
                                        <span class="text-sm">5. Strongly Agree</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <p class="font-medium text-gray-900 mb-3">SQD6. I feel the office was fair to everyone, or "walang palakasan", during my transaction.</p>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd6_fair_treatment" value="1" class="mr-2">
                                        <span class="text-sm">1. Strongly Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd6_fair_treatment" value="2" class="mr-2">
                                        <span class="text-sm">2. Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd6_fair_treatment" value="3" class="mr-2">
                                        <span class="text-sm">3. Neither Agree nor Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd6_fair_treatment" value="4" class="mr-2">
                                        <span class="text-sm">4. Agree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd6_fair_treatment" value="5" class="mr-2">
                                        <span class="text-sm">5. Strongly Agree</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <p class="font-medium text-gray-900 mb-3">SQD7. I was treated courteously by the staff, and (if asked for help) the staff was helpful.</p>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd7_courteous_staff" value="1" class="mr-2">
                                        <span class="text-sm">1. Strongly Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd7_courteous_staff" value="2" class="mr-2">
                                        <span class="text-sm">2. Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd7_courteous_staff" value="3" class="mr-2">
                                        <span class="text-sm">3. Neither Agree nor Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd7_courteous_staff" value="4" class="mr-2">
                                        <span class="text-sm">4. Agree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd7_courteous_staff" value="5" class="mr-2">
                                        <span class="text-sm">5. Strongly Agree</span>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <p class="font-medium text-gray-900 mb-3">SQD8. I got what I needed from the government office, or (if denied) denial of request was sufficiently explained to me.</p>
                                <div class="space-y-2">
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd8_got_what_needed" value="1" class="mr-2">
                                        <span class="text-sm">1. Strongly Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd8_got_what_needed" value="2" class="mr-2">
                                        <span class="text-sm">2. Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd8_got_what_needed" value="3" class="mr-2">
                                        <span class="text-sm">3. Neither Agree nor Disagree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd8_got_what_needed" value="4" class="mr-2">
                                        <span class="text-sm">4. Agree</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" x-model="form.sqd8_got_what_needed" value="5" class="mr-2">
                                        <span class="text-sm">5. Strongly Agree</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Optional Fields -->
                        <div class="mt-8 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Suggestions on how we can further improve our services (optional)</label>
                                <textarea x-model="form.suggestions" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent" placeholder="Your suggestions..."></textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email address (optional)</label>
                                <input type="email" x-model="form.email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#155386] focus:border-transparent" placeholder="your.email@example.com">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex justify-between items-center pt-6 border-t">
                    <button type="button" @click="previousPage()" x-show="currentPage > 1" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                        Previous
                    </button>
                    <div class="flex gap-3">
                        <button type="button" @click="nextPage()" x-show="currentPage < 2" class="px-6 py-2 bg-[#155386] text-white rounded-lg hover:bg-[#1F363D] transition">
                            Next
                        </button>
                        <button type="submit" x-show="currentPage === 2" :disabled="isSubmitting" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition disabled:opacity-50">
                            <span x-show="!isSubmitting">Submit Survey</span>
                            <span x-show="isSubmitting">Submitting...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function surveyModal() {
    return {
        showModal: false,
        currentPage: 1,
        isSubmitting: false,
        applicationId: null,
        form: {
            client_type: '',
            survey_date: new Date().toISOString().split('T')[0],
            sex: '',
            age: '',
            region_of_residence: '',
            service_availed: '',
            cc1_awareness: '',
            cc2_helpfulness: '',
            cc3_help_level: '',
            sqd0_satisfied: '',
            sqd1_reasonable_time: '',
            sqd2_requirements_followed: '',
            sqd3_steps_easy: '',
            sqd4_info_easy_find: '',
            sqd5_reasonable_fees: '',
            sqd6_fair_treatment: '',
            sqd7_courteous_staff: '',
            sqd8_got_what_needed: '',
            suggestions: '',
            email: ''
        },

        openModal(applicationId, serviceAvailed = '') {
            this.applicationId = applicationId;
            this.form.service_availed = serviceAvailed || 'Building Permit Application';
            this.showModal = true;
            this.currentPage = 1;
            this.resetForm();
        },

        closeModal() {
            this.showModal = false;
            this.currentPage = 1;
            this.resetForm();
        },

        resetForm() {
            this.form = {
                client_type: '',
                survey_date: new Date().toISOString().split('T')[0],
                sex: '',
                age: '',
                region_of_residence: '',
                service_availed: '',
                cc1_awareness: '',
                cc2_helpfulness: '',
                cc3_help_level: '',
                sqd0_satisfied: '',
                sqd1_reasonable_time: '',
                sqd2_requirements_followed: '',
                sqd3_steps_easy: '',
                sqd4_info_easy_find: '',
                sqd5_reasonable_fees: '',
                sqd6_fair_treatment: '',
                sqd7_courteous_staff: '',
                sqd8_got_what_needed: '',
                suggestions: '',
                email: ''
            };
        },

        nextPage() {
            if (this.currentPage < 2) {
                this.currentPage++;
            }
        },

        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
            }
        },

        async submitSurvey() {
            if (this.isSubmitting) return;

            this.isSubmitting = true;

            try {
                const formData = {
                    application_id: this.applicationId,
                    ...this.form
                };

                const response = await fetch('/applicant/survey/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    // Show success message
                    this.showSuccessMessage(data.message);
                    this.closeModal();
                } else {
                    // Show error
                    this.showErrorMessage(data.message || 'Failed to submit survey');
                }
            } catch (error) {
                console.error('Survey submission error:', error);
                this.showErrorMessage('An error occurred while submitting the survey');
            } finally {
                this.isSubmitting = false;
            }
        },

        showSuccessMessage(message) {
            // You can implement a toast notification here
            alert(message);
        },

        showErrorMessage(message) {
            // You can implement a toast notification here
            alert(message);
        }
    }
}

// Global function to trigger survey modal
window.showSurveyModal = function(applicationId, serviceAvailed = '') {
    const modal = document.querySelector('#survey-modal');
    if (modal && modal._x_dataStack) {
        modal._x_dataStack[0].openModal(applicationId, serviceAvailed);
    }
};
</script>
<script>
// Define the Alpine component first (before DOMContentLoaded)
document.addEventListener('alpine:init', () => {
    console.log('Alpine initializing survey modal component');
    
    Alpine.data('surveyModal', () => ({
        showModal: false,
        currentPage: 1,
        isSubmitting: false,
        applicationId: null,
        serviceAvailed: 'Building Permit Application',
        form: {
            client_type: '',
            survey_date: new Date().toISOString().split('T')[0],
            sex: '',
            age: '',
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

        init() {
            console.log('Survey modal component initialized');
            // Make this instance globally available
            window.surveyModal = this;
        },

        resetForm() {
            this.form = {
                client_type: '',
                survey_date: new Date().toISOString().split('T')[0],
                sex: '',
                age: '',
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
            this.currentPage = 1;
            this.isSubmitting = false;
        },

        openModal(applicationId, serviceAvailed = '') {
            console.log('openModal called with:', applicationId, serviceAvailed);
            this.resetForm();
            this.applicationId = applicationId;
            this.serviceAvailed = serviceAvailed || 'Building Permit Application';
            this.showModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeModal() {
            this.showModal = false;
            this.resetForm();
            document.body.style.overflow = 'auto';
        },

        nextPage() {
            // Validate page 1 fields before proceeding
            if (this.currentPage === 1) {
                if (!this.form.client_type) {
                    alert('Please select client type');
                    return;
                }
                if (!this.form.sex) {
                    alert('Please select sex');
                    return;
                }
                if (!this.form.age || this.form.age < 1 || this.form.age > 120) {
                    alert('Please enter a valid age (1-120)');
                    return;
                }
                if (!this.form.cc1_awareness) {
                    alert('Please answer the Citizen Charter awareness question');
                    return;
                }
            }
            
            if (this.currentPage < 2) {
                this.currentPage++;
                // Scroll to top of modal
                setTimeout(() => {
                    const modalContent = document.querySelector('#survey-modal .overflow-y-auto');
                    if (modalContent) modalContent.scrollTop = 0;
                }, 50);
            }
        },

        previousPage() {
            if (this.currentPage > 1) {
                this.currentPage--;
                setTimeout(() => {
                    const modalContent = document.querySelector('#survey-modal .overflow-y-auto');
                    if (modalContent) modalContent.scrollTop = 0;
                }, 50);
            }
        },

        async submitSurvey() {
            if (this.isSubmitting) return;

            // Validate all required fields on page 2
            if (!this.form.sqd0_satisfied) {
                alert('Please answer question SQD0 about your satisfaction');
                return;
            }
            if (!this.form.sqd1_reasonable_time) {
                alert('Please answer question SQD1 about reasonable time');
                return;
            }
            if (!this.form.sqd2_requirements_followed) {
                alert('Please answer question SQD2 about requirements');
                return;
            }
            if (!this.form.sqd3_steps_easy) {
                alert('Please answer question SQD3 about steps');
                return;
            }
            if (!this.form.sqd4_info_easy_find) {
                alert('Please answer question SQD4 about finding information');
                return;
            }
            if (!this.form.sqd5_reasonable_fees) {
                alert('Please answer question SQD5 about fees');
                return;
            }
            if (!this.form.sqd6_fair_treatment) {
                alert('Please answer question SQD6 about fair treatment');
                return;
            }
            if (!this.form.sqd7_courteous_staff) {
                alert('Please answer question SQD7 about staff courtesy');
                return;
            }
            if (!this.form.sqd8_got_what_needed) {
                alert('Please answer question SQD8 about getting what you needed');
                return;
            }

            this.isSubmitting = true;

            try {
                // Prepare form data with service_availed included
                const formData = {
                    application_id: this.applicationId,
                    service_availed: this.serviceAvailed,
                    client_type: this.form.client_type,
                    survey_date: this.form.survey_date,
                    sex: this.form.sex,
                    age: this.form.age,
                    cc1_awareness: this.form.cc1_awareness,
                    cc2_helpfulness: this.form.cc2_helpfulness,
                    cc3_help_level: this.form.cc3_help_level,
                    sqd0_satisfied: this.form.sqd0_satisfied,
                    sqd1_reasonable_time: this.form.sqd1_reasonable_time,
                    sqd2_requirements_followed: this.form.sqd2_requirements_followed,
                    sqd3_steps_easy: this.form.sqd3_steps_easy,
                    sqd4_info_easy_find: this.form.sqd4_info_easy_find,
                    sqd5_reasonable_fees: this.form.sqd5_reasonable_fees,
                    sqd6_fair_treatment: this.form.sqd6_fair_treatment,
                    sqd7_courteous_staff: this.form.sqd7_courteous_staff,
                    sqd8_got_what_needed: this.form.sqd8_got_what_needed,
                    suggestions: this.form.suggestions,
                    email: this.form.email
                };

                console.log('Submitting survey data:', formData);

                const response = await fetch('/applicant/survey/submit', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    alert('Thank you for your feedback! Your survey has been submitted successfully.');
                    this.closeModal();
                    // Refresh page after survey submission
                    setTimeout(() => window.location.reload(), 500);
                } else {
                    alert(data.message || 'Failed to submit survey');
                }
            } catch (error) {
                console.error('Survey submission error:', error);
                alert('An error occurred while submitting the survey. Please try again.');
            } finally {
                this.isSubmitting = false;
            }
        }
    }));
});

// Global function to trigger survey modal
window.showSurveyModal = function(applicationId, serviceAvailed = '') {
    console.log('showSurveyModal called with:', applicationId, serviceAvailed);
    
    // Check if the modal component is available
    if (window.surveyModal) {
        console.log('Modal component found, opening...');
        window.surveyModal.openModal(applicationId, serviceAvailed);
    } else {
        console.log('Modal component not ready yet, waiting...');
        // Wait for component to be available
        const checkInterval = setInterval(() => {
            if (window.surveyModal) {
                clearInterval(checkInterval);
                console.log('Modal component now available, opening...');
                window.surveyModal.openModal(applicationId, serviceAvailed);
            }
        }, 100);
        
        // Timeout after 5 seconds
        setTimeout(() => {
            clearInterval(checkInterval);
            console.error('Modal component failed to initialize');
            alert('Survey form is loading. Please refresh the page and try again.');
        }, 5000);
    }
};

// Function to check for pending surveys
async function checkPendingSurveys() {
    try {
        const response = await fetch('/applicant/survey/pending', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            console.log('Pending surveys response:', data);
            if (data.success && data.pending_surveys && data.pending_surveys.length > 0) {
                const firstPending = data.pending_surveys[0];
                console.log('Found pending survey for application:', firstPending.id);
                if (window.showSurveyModal) {
                    window.showSurveyModal(firstPending.id, firstPending.service_availed || 'Building Permit Application');
                }
            }
        }
    } catch (error) {
        console.error('Error checking for pending surveys:', error);
    }
}

// Debug function to check if modal is ready
window.checkSurveyModalReady = function() {
    console.log('Survey modal ready:', !!window.surveyModal);
    console.log('Modal component:', window.surveyModal);
    return !!window.surveyModal;
};

// Auto-check for pending surveys when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking for pending surveys...');
    // Wait for Alpine to initialize
    setTimeout(() => {
        checkPendingSurveys();
    }, 2000);
});
</script>

<style>
[x-cloak] { display: none !important; }
</style>

<!-- Client Satisfaction Survey Modal -->
<div 
    id="survey-modal" 
    x-data="surveyModal" 
    x-cloak
    x-show="showModal"
    class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full px-4 z-50"
    style="display: none;"
    @click.away="closeModal()">
    <div class="relative top-4 mx-auto p-4 w-full max-w-6xl max-h-[85vh] overflow-y-auto" @click.stop>
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
                            Please rate your agreement with the following statements.
                        </p>

                        <!-- SQD Questions -->
                        <div class="overflow-x-auto">
                            <table class="w-full border-collapse border border-gray-300">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th class="border border-gray-300 px-4 py-3 text-left font-medium text-gray-900">Service Quality Dimensions</th>
                                        <th class="border border-gray-300 px-2 py-3 text-center font-medium text-gray-700 text-xs">1<br>Strongly<br>Disagree</th>
                                        <th class="border border-gray-300 px-2 py-3 text-center font-medium text-gray-700 text-xs">2<br>Disagree</th>
                                        <th class="border border-gray-300 px-2 py-3 text-center font-medium text-gray-700 text-xs">3<br>Neither</th>
                                        <th class="border border-gray-300 px-2 py-3 text-center font-medium text-gray-700 text-xs">4<br>Agree</th>
                                        <th class="border border-gray-300 px-2 py-3 text-center font-medium text-gray-700 text-xs">5<br>Strongly<br>Agree</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-3">SQD0. I am satisfied with the service that I availed.</td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd0_satisfied" value="1" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd0_satisfied" value="2" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd0_satisfied" value="3" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd0_satisfied" value="4" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd0_satisfied" value="5" class="w-4 h-4"></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-3">SQD1. I spent a reasonable amount of time for my transaction.</td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd1_reasonable_time" value="1" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd1_reasonable_time" value="2" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd1_reasonable_time" value="3" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd1_reasonable_time" value="4" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd1_reasonable_time" value="5" class="w-4 h-4"></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-3">SQD2. The office followed the transaction's requirements and steps based on the information provided.</td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd2_requirements_followed" value="1" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd2_requirements_followed" value="2" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd2_requirements_followed" value="3" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd2_requirements_followed" value="4" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd2_requirements_followed" value="5" class="w-4 h-4"></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-3">SQD3. The steps (including payment) I needed to do for my transaction were easy and simple.</td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd3_steps_easy" value="1" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd3_steps_easy" value="2" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd3_steps_easy" value="3" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd3_steps_easy" value="4" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd3_steps_easy" value="5" class="w-4 h-4"></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-3">SQD4. I easily found information about my transaction from the office or its website.</td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd4_info_easy_find" value="1" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd4_info_easy_find" value="2" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd4_info_easy_find" value="3" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd4_info_easy_find" value="4" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd4_info_easy_find" value="5" class="w-4 h-4"></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-3">SQD5. I paid a reasonable amount of fees for my transaction.</td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd5_reasonable_fees" value="1" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd5_reasonable_fees" value="2" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd5_reasonable_fees" value="3" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd5_reasonable_fees" value="4" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd5_reasonable_fees" value="5" class="w-4 h-4"></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-3">SQD6. I feel the office was fair to everyone, or "walang palakasan", during my transaction.</td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd6_fair_treatment" value="1" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd6_fair_treatment" value="2" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd6_fair_treatment" value="3" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd6_fair_treatment" value="4" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd6_fair_treatment" value="5" class="w-4 h-4"></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-3">SQD7. I was treated courteously by the staff, and (if asked for help) the staff was helpful.</td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd7_courteous_staff" value="1" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd7_courteous_staff" value="2" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd7_courteous_staff" value="3" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd7_courteous_staff" value="4" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd7_courteous_staff" value="5" class="w-4 h-4"></td>
                                    </tr>
                                    <tr class="hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-3">SQD8. I got what I needed from the government office, or (if denied) denial of request was sufficiently explained to me.</td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd8_got_what_needed" value="1" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd8_got_what_needed" value="2" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd8_got_what_needed" value="3" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd8_got_what_needed" value="4" class="w-4 h-4"></td>
                                        <td class="border border-gray-300 px-2 py-3 text-center"><input type="radio" x-model="form.sqd8_got_what_needed" value="5" class="w-4 h-4"></td>
                                    </tr>
                                </tbody>
                            </table>
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
{{-- resources/views/applicant/basic-requirements/partials/submitted-review.blade.php --}}

<div class="bg-white rounded-2xl shadow-xl overflow-hidden mb-6">
    <div class="bg-gradient-to-r from-[#155386] to-[#1F363D] px-6 py-4">
        <div class="flex items-center gap-3">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h2 class="text-white font-bold text-lg">Submitted Requirements Review</h2>
        </div>
    </div>
    <div class="p-6">
        <div class="space-y-6">
            <!-- Property Documents -->
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <h3 class="font-semibold text-gray-800">Property Documents</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.102m1.102-4.768a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.102" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Transfer Certificate of Title (TCT) / Deed of Sale</span>
                        </div>
                        <a href="{{ $basicRequirement->tct_link }}" target="_blank" class="text-sm text-[#155386] hover:underline flex items-center gap-1">
                            View Document
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Tax Declaration</span>
                        </div>
                        <a href="{{ $basicRequirement->tax_declaration_link }}" target="_blank" class="text-sm text-[#155386] hover:underline flex items-center gap-1">
                            View Document
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Current Tax Receipt</span>
                        </div>
                        <a href="{{ $basicRequirement->current_tax_receipt_link }}" target="_blank" class="text-sm text-[#155386] hover:underline flex items-center gap-1">
                            View Document
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                    @if($basicRequirement->spa_link)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-700">Special Power of Attorney (SPA)</span>
                        </div>
                        <a href="{{ $basicRequirement->spa_link }}" target="_blank" class="text-sm text-[#155386] hover:underline flex items-center gap-1">
                            View Document
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Submission Info -->
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-semibold text-blue-800">Submission Information</span>
                </div>
                <p class="text-sm text-blue-700">Submitted on: {{ $basicRequirement->submitted_at->format('F d, Y h:i A') }}</p>
                <p class="text-xs text-blue-600 mt-1">Status: {{ ucfirst($basicRequirement->status) }}</p>
                @if($basicRequirement->status === 'approved' && $basicRequirement->approved_at)
                    <p class="text-xs text-green-600 mt-1">Approved on: {{ $basicRequirement->approved_at->format('F d, Y h:i A') }}</p>
                @endif
                @if($basicRequirement->status === 'rejected' && $basicRequirement->rejection_reason)
                    <div class="mt-3 pt-3 border-t border-blue-200">
                        <p class="text-sm font-semibold text-red-700">Rejection Reason:</p>
                        <p class="text-sm text-red-600 mt-1">{{ $basicRequirement->rejection_reason }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
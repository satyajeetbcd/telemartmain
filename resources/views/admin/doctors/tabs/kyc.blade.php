<!-- KYC Status Tab -->
<div class="space-y-6">
    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">KYC Documents Status</h3>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Aadhar Front -->
            <div class="border rounded-lg p-4 {{ $kycStatus['aadhar_front']['status'] === 'approved' ? 'border-green-500 bg-green-50' : ($kycStatus['aadhar_front']['status'] === 'rejected' ? 'border-red-500 bg-red-50' : 'border-gray-300') }}">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900">Aadhar Front</h4>
                    @if($kycStatus['aadhar_front']['status'] === 'approved')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                    @elseif($kycStatus['aadhar_front']['status'] === 'rejected')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                    @elseif($kycStatus['aadhar_front']['status'] === 'pending')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Not Uploaded</span>
                    @endif
                </div>
            </div>

            <!-- Aadhar Back -->
            <div class="border rounded-lg p-4 {{ $kycStatus['aadhar_back']['status'] === 'approved' ? 'border-green-500 bg-green-50' : ($kycStatus['aadhar_back']['status'] === 'rejected' ? 'border-red-500 bg-red-50' : 'border-gray-300') }}">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900">Aadhar Back</h4>
                    @if($kycStatus['aadhar_back']['status'] === 'approved')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                    @elseif($kycStatus['aadhar_back']['status'] === 'rejected')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                    @elseif($kycStatus['aadhar_back']['status'] === 'pending')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Not Uploaded</span>
                    @endif
                </div>
            </div>

            <!-- PAN Card -->
            <div class="border rounded-lg p-4 {{ $kycStatus['pan']['status'] === 'approved' ? 'border-green-500 bg-green-50' : ($kycStatus['pan']['status'] === 'rejected' ? 'border-red-500 bg-red-50' : 'border-gray-300') }}">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900">PAN Card</h4>
                    @if($kycStatus['pan']['status'] === 'approved')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                    @elseif($kycStatus['pan']['status'] === 'rejected')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                    @elseif($kycStatus['pan']['status'] === 'pending')
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                    @else
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">Not Uploaded</span>
                    @endif
                </div>
            </div>

            <!-- Degree Certificates -->
            <div class="border rounded-lg p-4 {{ $kycStatus['degrees']['approved_count'] > 0 ? 'border-green-500 bg-green-50' : 'border-gray-300' }}">
                <div class="flex items-center justify-between mb-2">
                    <h4 class="font-medium text-gray-900">Degrees</h4>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full 
                        {{ $kycStatus['degrees']['approved_count'] > 0 ? 'bg-green-100 text-green-800' : ($kycStatus['degrees']['pending_count'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ $kycStatus['degrees']['approved_count'] }} Approved
                    </span>
                </div>
                <p class="text-sm text-gray-600">
                    Total: {{ $kycStatus['degrees']['count'] }} | 
                    Pending: {{ $kycStatus['degrees']['pending_count'] }}
                </p>
            </div>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Overall KYC Status</h3>
        <div class="bg-gray-50 rounded-lg p-4">
            <span class="px-4 py-2 inline-flex text-base font-semibold rounded-full 
                {{ $kycStatus['overall_status'] === 'approved' ? 'bg-green-100 text-green-800' : ($kycStatus['overall_status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                {{ ucfirst($kycStatus['overall_status']) }}
            </span>
        </div>
    </div>
</div>


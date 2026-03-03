<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Doctor Reviews</h3>
            <p class="text-sm text-gray-600 mt-1">Patient reviews and ratings for Dr. {{ $doctor->name }}</p>
        </div>
    </div>

    <!-- Rating Summary -->
    <div class="bg-white border rounded-lg p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="text-4xl font-bold text-green-600">{{ number_format($averageRating, 1) }}</div>
                <div class="flex justify-center mt-2">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-6 h-6 {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                    @endfor
                </div>
                <p class="text-sm text-gray-600 mt-2">Average Rating</p>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-gray-900">{{ $reviewCount }}</div>
                <p class="text-sm text-gray-600 mt-2">Total Reviews</p>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-green-600">{{ $ratingDistribution[5] ?? 0 }}</div>
                <p class="text-sm text-gray-600 mt-2">5 Star Reviews</p>
            </div>
        </div>

        <!-- Rating Distribution -->
        <div class="mt-6 pt-6 border-t border-gray-200">
            <h4 class="text-sm font-medium text-gray-900 mb-4">Rating Distribution</h4>
            <div class="space-y-2">
                @for($i = 5; $i >= 1; $i--)
                    @php
                        $count = $ratingDistribution[$i] ?? 0;
                        $percentage = $reviewCount > 0 ? ($count / $reviewCount) * 100 : 0;
                    @endphp
                    <div class="flex items-center">
                        <div class="w-16 text-sm text-gray-600">{{ $i }} Star</div>
                        <div class="flex-1 mx-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        <div class="w-12 text-sm text-gray-600 text-right">{{ $count }}</div>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <!-- Reviews List -->
    <div class="bg-white border rounded-lg p-6">
        <h4 class="text-md font-semibold text-gray-900 mb-4">All Reviews</h4>
        
        @if($reviews->isEmpty())
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                </svg>
                <p>No reviews yet.</p>
            </div>
        @else
            <div class="space-y-6">
                @foreach($reviews as $review)
                    <div class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <div class="font-medium text-gray-900">{{ $review->patient->full_name ?? 'Anonymous' }}</div>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                                @if($review->appointment)
                                    <p class="text-xs text-gray-500 mb-2">Appointment: {{ $review->appointment->appointment_date->format('M d, Y') }}</p>
                                @endif
                                @if($review->comment)
                                    <p class="text-gray-700 mb-3">{{ $review->comment }}</p>
                                @endif
                                @if($review->doctor_reply)
                                    <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded mt-3">
                                        <div class="flex items-center mb-2">
                                            <span class="font-medium text-green-800">Dr. {{ $doctor->name }}</span>
                                            <span class="text-xs text-green-600 ml-2">Replied {{ $review->replied_at->format('M d, Y') }}</span>
                                        </div>
                                        <p class="text-green-700">{{ $review->doctor_reply }}</p>
                                    </div>
                                @endif
                                <p class="text-xs text-gray-500 mt-2">{{ $review->created_at->format('M d, Y') }}</p>
                            </div>
                            <div class="ml-4">
                                <form action="{{ route('admin.reviews.toggle-visibility', $review) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-sm {{ $review->is_visible ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}">
                                        {{ $review->is_visible ? 'Hide' : 'Show' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>


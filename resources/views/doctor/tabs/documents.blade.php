<!-- Document Upload Tab -->
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h3 class="text-lg font-semibold text-gray-900">Upload Documents</h3>
        <a href="{{ route('doctor.kyc.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
            Upload New Document
        </a>
    </div>

    @if($kycDocuments->count() > 0)
        <div class="divide-y divide-gray-200">
            @foreach($kycDocuments->flatten() as $document)
                <div class="p-4 hover:bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h4 class="text-sm font-medium text-gray-900">{{ $document->document_name }}</h4>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $document->document_type_label }} • 
                                Uploaded: {{ $document->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $document->status === 'approved' ? 'bg-green-100 text-green-800' : ($document->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ ucfirst($document->status) }}
                            </span>
                            <a href="{{ route('doctor.kyc.download', $document) }}" class="text-green-600 hover:text-green-900 text-sm font-medium">
                                View
                            </a>
                            @if($document->status === 'pending')
                                <form action="{{ route('doctor.kyc.destroy', $document) }}" method="POST" class="inline" 
                                    onsubmit="return confirm('Are you sure you want to delete this document?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Delete
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-12 bg-gray-50 rounded-lg">
            <p class="text-gray-500">No documents uploaded yet.</p>
            <a href="{{ route('doctor.kyc.create') }}" class="mt-4 inline-block text-green-600 hover:text-green-700">
                Upload your first document
            </a>
        </div>
    @endif
</div>


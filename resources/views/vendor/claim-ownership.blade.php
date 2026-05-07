<x-app-layout>
    <div class="max-w-4xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <a href="{{ route('place.show', $vendor->slug) }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-6">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Establishment
        </a>

        <!-- Card -->
        <div class="bg-white rounded-lg shadow-md p-6 sm:p-8">
            
            <!-- Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Claim Ownership</h1>
                <p class="text-gray-600">Request ownership of <strong class="text-gray-900">{{ $vendor->business_name }}</strong></p>
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zm-11-1a1 1 0 11-2 0 1 1 0 012 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-blue-800">
                            To claim ownership, you'll need to provide documentation proving your rights to this establishment (e.g., business license, registration certificate, ownership documents). Our admin team will review your application.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <form method="POST" action="{{ route('place.claim.submit', $vendor->slug) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Reason -->
                <div>
                    <label for="reason" class="block text-sm font-semibold text-gray-700 mb-2">Why should you own this establishment? *</label>
                    <textarea id="reason" name="reason" rows="4" required class="block w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-orange-500 focus:ring-orange-500 shadow-sm transition" placeholder="Explain your relationship to this establishment and why you should be the owner...">{{ old('reason') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Minimum 10 characters</p>
                    <x-input-error :messages="$errors->get('reason')" class="mt-2" />
                </div>

                <!-- Documents -->
                <div>
                    <label for="documents" class="block text-sm font-semibold text-gray-700 mb-2">Supporting Documents *</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-orange-500 transition cursor-pointer" onclick="document.getElementById('file-input').click()">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-8l-3.172-3.172a4 4 0 00-5.656 0L28 20M9 20l3.172-3.172a4 4 0 015.656 0L20 20" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">
                            <span class="font-semibold text-orange-600">Click to upload</span> or drag and drop
                        </p>
                        <p class="text-xs text-gray-500 mt-1">PDF, JPG, PNG (Max 5MB per file)</p>
                    </div>
                    <input type="file" id="file-input" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="hidden" required onchange="updateFileList()">
                    <div id="file-list" class="mt-4 space-y-2"></div>
                    <x-input-error :messages="$errors->get('documents')" class="mt-2" />
                    <x-input-error :messages="$errors->get('documents.*')" class="mt-2" />
                </div>

                <!-- Buttons -->
                <div class="flex gap-4 pt-6">
                    <a href="{{ route('place.show', $vendor->slug) }}" class="flex-1 px-6 py-3 border border-gray-300 rounded-lg text-gray-700 font-semibold hover:bg-gray-50 transition">
                        Cancel
                    </a>
                    <button type="submit" class="flex-1 px-6 py-3 bg-orange-500 text-white font-semibold rounded-lg hover:bg-orange-600 transition">
                        Submit Application
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateFileList() {
            const fileInput = document.getElementById('file-input');
            const fileList = document.getElementById('file-list');
            fileList.innerHTML = '';

            if (fileInput.files.length > 0) {
                const ul = document.createElement('ul');
                ul.className = 'space-y-2';
                
                for (let file of fileInput.files) {
                    const li = document.createElement('li');
                    li.className = 'flex items-center p-2 bg-gray-50 rounded border border-gray-200';
                    li.innerHTML = `
                        <svg class="w-5 h-5 text-gray-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1V5a1 1 0 00-1-1H3zM14 7a1 1 0 00-1 1v6.05A2.5 2.5 0 0015.95 16H17a1 1 0 001-1v-5a1 1 0 00-.293-.707l-2-2A1 1 0 0015 7h-1z" />
                        </svg>
                        <span class="text-sm text-gray-700 flex-1 truncate">${file.name}</span>
                        <span class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</span>
                    `;
                    ul.appendChild(li);
                }
                
                fileList.appendChild(ul);
            }
        }
    </script>
</x-app-layout>

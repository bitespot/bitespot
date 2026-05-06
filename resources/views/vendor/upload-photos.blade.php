@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
        <h1 class="text-2xl font-bold mb-6 text-gray-900">Upload Photos</h1>

        @if($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-700 font-semibold mb-2">Upload failed:</p>
                <ul class="list-disc list-inside text-red-600 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-green-700 font-semibold">{{ session('success') }}</p>
            </div>
        @endif

        <!-- Cover Photo Upload -->
        <div class="mb-8">
            <h2 class="text-lg font-semibold mb-4 text-gray-800">Cover Photo</h2>

            <div class="mb-4 rounded-lg overflow-hidden shadow-sm bg-gray-100 h-48 flex items-center justify-center">
                <img id="cover-preview"
                     src="{{ $vendor->cover_photo ? Storage::disk('s3')->url($vendor->cover_photo) : '' }}"
                     alt="Cover photo"
                     class="{{ $vendor->cover_photo ? '' : 'hidden' }} w-full h-full object-cover">
                <span id="cover-placeholder" class="{{ $vendor->cover_photo ? 'hidden' : '' }} text-gray-400 text-sm">No cover photo</span>
            </div>

            @if($vendor->cover_photo)
                <p class="text-xs text-gray-500 mb-3 break-all">
                    Stored key: <code>{{ $vendor->cover_photo }}</code>
                </p>
            @endif

            <form action="{{ route('vendor.photos.cover') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-orange-400 transition">
                    <input type="file" name="cover_photo" id="cover_photo" accept="image/*"
                           class="hidden" onchange="previewImage(event, 'cover-preview', 'cover-placeholder')">
                    <label for="cover_photo" class="cursor-pointer">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20a4 4 0 004 4h24a4 4 0 004-4V20m-10-6l-3-3m0 0l-3 3m3-3v10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <p class="text-sm text-gray-600">Click to upload or drag and drop<br><span class="text-xs text-gray-500">PNG, JPG, WEBP (max 5MB)</span></p>
                    </label>
                </div>
                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg transition">
                    Upload Cover Photo
                </button>
            </form>
        </div>

        <!-- Profile Photo Upload -->
        <div>
            <h2 class="text-lg font-semibold mb-4 text-gray-800">Profile Photo</h2>

            <div class="mb-4 w-32 h-32 mx-auto rounded-full overflow-hidden shadow-sm bg-gray-100 flex items-center justify-center">
                <img id="profile-preview"
                     src="{{ $vendor->profile_photo ? Storage::disk('s3')->url($vendor->profile_photo) : '' }}"
                     alt="Profile photo"
                     class="{{ $vendor->profile_photo ? '' : 'hidden' }} w-full h-full object-cover">
                <span id="profile-placeholder" class="{{ $vendor->profile_photo ? 'hidden' : '' }} text-gray-400 text-sm">No photo</span>
            </div>

            @if($vendor->profile_photo)
                <p class="text-xs text-gray-500 mb-3 text-center break-all">
                    Stored key: <code>{{ $vendor->profile_photo }}</code>
                </p>
            @endif

            <form action="{{ route('vendor.photos.profile') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-orange-400 transition">
                    <input type="file" name="profile_photo" id="profile_photo" accept="image/*"
                           class="hidden" onchange="previewImage(event, 'profile-preview', 'profile-placeholder')">
                    <label for="profile_photo" class="cursor-pointer">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-2" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20a4 4 0 004 4h24a4 4 0 004-4V20m-10-6l-3-3m0 0l-3 3m3-3v10" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <p class="text-sm text-gray-600">Click to upload or drag and drop<br><span class="text-xs text-gray-500">PNG, JPG, WEBP (max 5MB)</span></p>
                    </label>
                </div>
                <button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-2 px-4 rounded-lg transition">
                    Upload Profile Photo
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function previewImage(event, previewId, placeholderId) {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        const img = document.getElementById(previewId);
        const placeholder = document.getElementById(placeholderId);
        img.src = e.target.result;
        img.classList.remove('hidden');
        placeholder.classList.add('hidden');
    };
    reader.readAsDataURL(file);
}
</script>
@endsection

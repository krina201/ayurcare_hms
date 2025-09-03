@extends('backend.layouts.master')

@section('content')
    <!-- MAIN CONTENT -->
    <main class="p-6">
        <div class="max-w-4xl mx-auto">
            <!-- PAGE HEADER -->
            <div id="pageHeader" class="mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-ayur-brown-800 mb-2">Edit Medicine</h1>
                        <p class="text-ayur-brown-600">Update medicine information and details.</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('pharmacy.inventory') }}"
                            class="btn inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500">
                            <i class="fa-solid fa-arrow-left mr-2"></i>
                            Back to Inventory
                        </a>
                    </div>
                </div>
            </div>

            <!-- EDIT MEDICINE FORM -->
            <div class="bg-white rounded-xl shadow-lg p-6">
                <form method="POST" action="{{ route('pharmacy.update', $medicine->id) }}" novalidate>
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="save_type" id="save_type" value="1">

                    <!-- Basic Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-ayur-brown-800 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Medicine Name *</label>
                                <input type="text" name="name" value="{{ old('name', $medicine->name) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('name') border-red-500 @enderror"
                                    required>
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-ayur-brown-700 mb-2">Medicine Code *</label>
                                <input type="text" name="code" value="{{ old('code', $medicine->code) }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-ayur-green-500 focus:border-transparent @error('code') border-red-500 @enderror"
                                    required>
                                @error('code')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-ayur-brown-800 mb-4">Status Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium text-ayur-brown-800">Active Status</h4>
                                    <p class="text-xs text-ayur-brown-600">Medicine is available for dispensing</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="status" value="1" class="sr-only peer"
                                        {{ old('status', $medicine->status) ? 'checked' : '' }}>
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-ayur-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-ayur-green-600">
                                    </div>
                                </label>
                            </div>
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                <div>
                                    <h4 class="text-sm font-medium text-ayur-brown-800">Track Expiry</h4>
                                    <p class="text-xs text-ayur-brown-600">Send alerts before expiry date</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="track_expiry" value="1" class="sr-only peer"
                                        {{ old('track_expiry', $medicine->track_expiry) ? 'checked' : '' }}>
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-ayur-green-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-ayur-green-600">
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- ACTION BUTTONS -->
                    <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                        <button type="submit" id="submitBtn"
                            class="flex-1 inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-ayur-green-600 hover:bg-ayur-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 transition-colors duration-200">
                            <i class="fa-solid fa-save mr-2"></i>
                            Update Medicine
                        </button>

                        <button type="button" id="draftBtn"
                            class="flex-1 inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 transition-colors duration-200">
                            <i class="fa-solid fa-floppy-disk mr-2"></i>
                            Save as Draft
                        </button>

                        <button type="button" id="cancelBtn"
                            class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-ayur-brown-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-ayur-green-500 transition-colors duration-200">
                            <i class="fa-solid fa-xmark mr-2"></i>
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const draftBtn = document.getElementById('draftBtn');
            const submitBtn = document.getElementById('submitBtn');
            const cancelBtn = document.getElementById('cancelBtn');

            // Handle Save as Draft button click
            if (draftBtn) {
                draftBtn.addEventListener('click', function() {
                    document.getElementById('save_type').value = '0';
                    form.submit();
                });
            }

            // Handle Submit button click
            if (submitBtn) {
                submitBtn.addEventListener('click', function(e) {
                    document.getElementById('save_type').value = '1';
                });
            }

            // Handle Cancel button click
            if (cancelBtn) {
                cancelBtn.addEventListener('click', function() {
                    if (confirm('Are you sure you want to cancel? All unsaved changes will be lost.')) {
                        window.location.href = '{{ route('pharmacy.inventory') }}';
                    }
                });
            }
        });
    </script>
@endsection

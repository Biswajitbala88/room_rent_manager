<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ isset($tenant) ? __('Edit Tenant') : __('Add Tenant') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                <form 
                    action="{{ isset($tenant) ? route('tenants.update', $tenant->id) : route('tenants.store') }}" 
                    method="POST" 
                    enctype="multipart/form-data"
                >
                    @csrf
                    @if(isset($tenant))
                        @method('PUT')
                    @endif

                    <!-- Name -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Name</label>
                        <input type="text" name="name" value="{{ old('name', $tenant->name ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3">
                    </div>

                    <!-- Phone -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $tenant->phone ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3">
                    </div>

                    <!-- Room Number -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Room Number</label>
                        <input type="text" name="room_no" value="{{ old('room_no', $tenant->room_no ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3">
                    </div>

                    <!-- Rent Start Date -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Rent Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $tenant->start_date ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3">
                    </div>

                    <!-- Monthly Rent -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">Monthly Rent</label>
                        <input type="number" name="rent_amount" value="{{ old('rent_amount', $tenant->rent_amount ?? '') }}" class="shadow appearance-none border rounded w-full py-2 px-3">
                    </div>

                    <!-- Aadhaar Image -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Aadhaar Image</label>
                        <input type="file" name="aadhaar_image" accept="image/*" class="shadow appearance-none border rounded w-full py-2 px-3">
                        @if(isset($tenant) && $tenant->aadhaar_image)
                            <div class="mt-2">
                                <img src="{{ asset('storage/' . $tenant->aadhaar_image) }}" width="120" alt="Aadhaar Image">
                            </div>
                        @endif
                    </div>

                    <!-- Status -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Status</label>
                        <select name="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="active" {{ $tenant->status === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="close" {{ $tenant->status === 'close' ? 'selected' : '' }}>Close</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-between">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">
                            {{ isset($tenant) ? 'Update' : 'Save' }}
                        </button>
                        <a href="{{ route('tenants.index') }}" class="text-gray-600 hover:text-gray-900">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

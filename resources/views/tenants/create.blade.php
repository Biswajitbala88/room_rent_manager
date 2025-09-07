<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Tenant') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                @if (session('error'))
                    <div class="bg-red-100 text-red-800 px-4 py-2 rounded mb-4">
                        {{ session('error') }}
                    </div>
                @endif
                <form action="{{ route('tenants.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- Name -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Name</label>
                        <input type="text" name="name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <!-- Phone -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Phone</label>
                        <input type="text" name="phone" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <!-- Room Number -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Room Number</label>
                        <input type="text" name="room_no" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <!-- Rent Start Date -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Rent Start Date</label>
                        <input type="date" name="start_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <!-- Monthly Rent -->
                    <div class="mb-6">
                        <label class="block text-gray-700 font-bold mb-2">Monthly Rent</label>
                        <input type="number" name="rent_amount" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <!-- Aadhaar Image -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-bold mb-2">Aadhaar Image</label>
                        <input type="file" name="aadhaar_image[]" multiple accept="image/*"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <!-- Is Water Charge Checkbox -->
                    <div class="mb-6 flex items-center">
                        <input type="checkbox" name="is_water_charge" id="is_water_charge" class="mr-2 leading-tight">
                        <label for="is_water_charge" class="text-gray-700 font-bold">Include Water Charge</label>
                    </div>

                    <!-- Water Charge -->
                    <div class="mb-6" id="water_charge_wrapper">
                        <label class="block text-gray-700 font-bold mb-2">Water Charge</label>
                        <input type="number" name="water_charge" class="shadow appearance-none border rounded w-full py-2 px-3">
                    </div>

                    <!-- Advanced Paid -->
                    <div class="mb-6 flex items-center">
                        <input type="checkbox" name="is_advanced" id="is_advanced" class="mr-2 leading-tight">
                        <label for="is_advanced" class="text-gray-700 font-bold">Is Advanced Paid</label>
                    </div>

                    <!-- Buttons -->
                    <div class="flex items-center justify-between">
                        <input type="hidden" name="parent_id" value="{{ auth()->user()->id }}">
                        <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" type="submit">
                            Save
                        </button>
                        <a href="{{ route('tenants.index') }}" class="text-gray-600 hover:text-gray-900">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkbox = document.getElementById('is_water_charge');
        const chargeWrapper = document.getElementById('water_charge_wrapper');

        function toggleWaterCharge() {
            if (checkbox.checked) {
                chargeWrapper.style.display = 'block';
            } else {
                chargeWrapper.style.display = 'none';
            }
        }

        // Initial check
        toggleWaterCharge();

        // On checkbox change
        checkbox.addEventListener('change', toggleWaterCharge);
    });
</script>
</x-app-layout>

<h2>Tenant Details</h2>
<p><strong>Room:</strong> {{ $tenant->room_no }}</p>
<p><strong>Name:</strong> {{ $tenant->name }}</p>
<p><strong>Rent:</strong> {{ $tenant->rent_amount }}</p>
<p><strong>Start Date:</strong> {{ $tenant->start_date }}</p>
@if($tenant->aadhaar_image)
    <p><strong>Aadhaar:</strong> <img src="{{ asset('storage/'.$tenant->aadhaar_image) }}" width="100"></p>
@endif
<a href="{{ route('tenants.index') }}">Back to List</a>

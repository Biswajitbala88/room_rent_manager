<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::ofUser()
        ->with('parentUser')
        ->orderBy('id', 'desc')   // <-- correct place
        ->paginate(10);
        return view('tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('tenants.create');
    }

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
    ]);

    $data = $request->only([
        'name',
        'phone',
        'room_no',
        'start_date',
        'rent_amount',
        'is_water_charge',
        'parent_id',
        'water_charge',
        'is_advanced',
    ]);

    // Defaults
    $data['status'] = 'active';
    $data['is_water_charge'] = $request->has('is_water_charge') ? 1 : 0;
    $data['is_advanced'] = $request->has('is_advanced') ? 1 : 0;

    // Force water_charge = 0 if not applicable
    if ($data['is_water_charge'] == 0) {
        $data['water_charge'] = 0;
    }

    // Check room availability
    if (Tenant::room_availability($data['room_no'])) {
        return redirect()->back()->with('error', 'Room number is already occupied.');
    }

    // Aadhaar image(s) upload
    $photoPaths = [];

    if ($request->hasFile('aadhaar_image')) {
        $files = $request->file('aadhaar_image');

        // Normalize: handle both single & multiple uploads
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            $nameSlug = strtolower(str_replace(' ', '_', $data['name']));
            $timestamp = time() . rand(100, 999);
            $extension = $file->getClientOriginalExtension();
            $filename = "{$nameSlug}_{$timestamp}.{$extension}";

            $path = $file->storeAs('aadhaar_image', $filename, 'public');
            $photoPaths[] = $path;
        }

        $data['aadhaar_image'] = json_encode($photoPaths);
    }

    // Save tenant
    Tenant::create($data);

    return redirect()->route('tenants.index')->with('success', 'Tenant created successfully.');
}



    public function show(Tenant $tenant)
    {
        return view('tenants.show', compact('tenant'));
    }

    public function edit($id)
    {
        $tenant = Tenant::findOrFail($id);
        return view('tenants.edit', compact('tenant'));
    }

    public function update(Request $request, $id)
    {
        $tenant = Tenant::findOrFail($id);

        $data = $request->only([
            'name',
            'phone',
            'room_no',
            'start_date',
            'rent_amount',
            'status',
            'is_water_charge',
            'parent_id',
            'water_charge',
            'is_advanced',
        ]);

        // Handle water charge toggle
        $data['is_water_charge'] = $request->has('is_water_charge') ? 1 : 0;
        if ($data['is_water_charge'] == 0) {
            $data['water_charge'] = 0;
        }

        // Handle advance toggle
        $data['is_advanced'] = $request->has('is_advanced') ? 1 : 0;

        // Room availability check (skip current tenant id)
        if (Tenant::room_availability($data['room_no'], $id)) {
            return redirect()->back()->with('error', 'Room number is already occupied.');
        }

        // Handle Aadhaar image(s)
        if ($request->hasFile('aadhaar_image')) {
            $photoPaths = [];
            $files = $request->file('aadhaar_image');

            // Normalize to array (supports single & multiple uploads)
            if (!is_array($files)) {
                $files = [$files];
            }

            foreach ($files as $file) {
                $nameSlug = strtolower(str_replace(' ', '_', $data['name']));
                $timestamp = time() . rand(100, 999);
                $extension = $file->getClientOriginalExtension();
                $filename = "{$nameSlug}_{$timestamp}.{$extension}";

                $path = $file->storeAs('aadhaar_image', $filename, 'public');
                $photoPaths[] = $path;
            }

            $data['aadhaar_image'] = json_encode($photoPaths);
        }

        // Update tenant record
        $tenant->update($data);

        return redirect()->back()->with('success', 'Tenant updated successfully.');
    }




    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('tenants.index')->with('success', 'Tenant deleted successfully.');
    }
}

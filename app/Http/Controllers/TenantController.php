<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::paginate(10);
        // echo '<pre>'; print_r($tenants); exit;
        return view('tenants.index', compact('tenants'));
    }

    public function create()
    {
        return view('tenants.create');
    }

    public function store(Request $request)
    {
        // Debug: View submitted form data
        // echo '<pre>'; print_r($request->all()); exit;

        $data = $request->only([
            'name',
            'phone',
            'room_no',
            'start_date',
            'rent_amount',
            'is_water_charge',
        ]);
        $data['status'] = 'active'; 
        $data['is_water_charge'] = $request->has('is_water_charge') ? 1 : 0; 

        // Handle Aadhaar image file upload
        // if ($request->hasFile('aadhaar_image')) {
        //     $data['aadhaar_image'] = $request->file('aadhaar_image')->store('aadhaar', 'public');
        // }

        $photoPaths = [];

        foreach ($request->file('aadhaar_image') as $file) {
            $nameSlug = strtolower(str_replace(' ', '_', $data['name']));
            $timestamp = time() . rand(100, 999);
            $extension = $file->getClientOriginalExtension();
            $filename = "{$nameSlug}_{$timestamp}.{$extension}";

            $path = $file->storeAs('aadhaar_image', $filename, 'public');
            $photoPaths[] = $path;
        }

        $data['aadhaar_image'] = json_encode($photoPaths);

        // echo '<pre>'; print_r($data); exit;

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
        ]);

        $data['is_water_charge'] = $request->has('is_water_charge') ? 1 : 0;

        // Handle multiple Aadhaar images if uploaded
        if ($request->hasFile('aadhaar_image')) {
            $photoPaths = [];

            foreach ($request->file('aadhaar_image') as $file) {
                $nameSlug = strtolower(str_replace(' ', '_', $data['name']));
                $timestamp = time() . rand(100, 999);
                $extension = $file->getClientOriginalExtension();
                $filename = "{$nameSlug}_{$timestamp}.{$extension}";

                $path = $file->storeAs('aadhaar_image', $filename, 'public');
                $photoPaths[] = $path;
            }

            $data['aadhaar_image'] = json_encode($photoPaths);
        }

        $tenant->update($data);

        return redirect()->route('tenants.index')->with('success', 'Tenant updated successfully.');
    }



    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('tenants.index')->with('success', 'Tenant deleted successfully.');
    }
}

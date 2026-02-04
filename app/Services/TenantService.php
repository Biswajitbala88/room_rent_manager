<?php

namespace App\Services;

use App\Models\Tenant;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TenantService
{
    /**
     * Create a new tenant.
     *
     * @param array $data
     * @param array|null $files Files for aadhaar_image
     * @return Tenant
     * @throws Exception
     */
    public function createTenant(array $data, $files = null): Tenant
    {
        // Check room availability
        if (Tenant::room_availability($data['room_no'])) {
            throw new Exception('Room number is already occupied.');
        }

        // Handle defaults
        $data['status'] = 'active';
        $data['is_water_charge'] = $data['is_water_charge'] ?? 0;
        $data['is_advanced'] = $data['is_advanced'] ?? 0;

        if ($data['is_water_charge'] == 0) {
            $data['water_charge'] = 0;
        }

        // Handle Image Upload
        if ($files) {
            $data['aadhaar_image'] = $this->handleImageUpload($files, $data['name']);
        }

        return Tenant::create($data);
    }

    /**
     * Update an existing tenant.
     *
     * @param Tenant $tenant
     * @param array $data
     * @param array|null $files
     * @return bool
     * @throws Exception
     */
    public function updateTenant(Tenant $tenant, array $data, $files = null): bool
    {
        // Check room availability (excluding current tenant)
        if (isset($data['room_no']) && Tenant::room_availability($data['room_no'], $tenant->id)) {
            throw new Exception('Room number is already occupied.');
        }

        // Handle flags
        $data['is_water_charge'] = $data['is_water_charge'] ?? 0;
        $data['is_advanced'] = $data['is_advanced'] ?? 0;

        if ($data['is_water_charge'] == 0) {
            $data['water_charge'] = 0;
        }

        // Handle Image Upload
        if ($files) {
            $data['aadhaar_image'] = $this->handleImageUpload($files, $data['name']);
        }

        return $tenant->update($data);
    }

    /**
     * Handle Aadhaar image uploads.
     *
     * @param mixed $files
     * @param string $name
     * @return string JSON encoded array of paths
     */
    protected function handleImageUpload($files, string $name): string
    {
        $photoPaths = [];

        // Normalize to array
        if (!is_array($files)) {
            $files = [$files];
        }

        foreach ($files as $file) {
            $nameSlug = strtolower(str_replace(' ', '_', $name));
            $timestamp = time() . rand(100, 999);
            $extension = $file->getClientOriginalExtension();
            $filename = "{$nameSlug}_{$timestamp}.{$extension}";

            $path = $file->storeAs('aadhaar_image', $filename, 'public');
            $photoPaths[] = $path;
        }

        return json_encode($photoPaths);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'room_no',
        'start_date',
        'rent_amount',
        'aadhaar_image',
        'status',
        'is_water_charge',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
    public static function room_availability($room_no, $excludeTenantId = null)
    {
        return Tenant::where('room_no', $room_no)
            ->where('status', 'active')
            ->when($excludeTenantId, function ($query) use ($excludeTenantId) {
                $query->where('id', '!=', $excludeTenantId);
            })
            ->exists();
    }

}
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
        'parent_id',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

}
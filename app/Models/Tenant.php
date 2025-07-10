<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;


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

    public function parentUser()
{
    return $this->belongsTo(User::class, 'parent_id'); // not Tenant
}

    /**
     * Scope to filter tenants based on logged-in user's type.
     */
    public function scopeOfUser($query, $userId = null)
{
    $user = auth()->user();

    // SA (Super Admin) should see all tenants
    if ($user->user_type === 'SA') {
        return $query;
    }

    $userId = $userId ?? $user->id;

    // A (Admin) users see only their own tenants
    return $query->where('parent_id', $userId);
}


}
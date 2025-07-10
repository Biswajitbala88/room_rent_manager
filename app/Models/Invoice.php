<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    
    protected $fillable = [
        'tenant_id',
        'month',
        'electricity_units',
        'electricity_charge',
        'water_charge',
        'total_amount',
        'status',
        'received_amount',
    ];


    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }
    
    public function scopeOfUser($query, $userId = null)
    {
        $user = auth()->user();

        if ($user->user_type === 'SA') {
            return $query;
        }

        $userId = $userId ?? $user->id;

        return $query->whereHas('tenant', function ($q) use ($userId) {
            $q->where('parent_id', $userId);
        });
    }

}

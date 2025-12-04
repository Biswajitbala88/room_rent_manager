<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ElectricityUnit extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'unit',
    ];

    // Relation to Invoice
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'order_number',
        'payment_date',
        'amount_paid',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount_paid' => 'decimal:2'
    ];

    public function application()
    {
        return $this->belongsTo(ApplicationDocument::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
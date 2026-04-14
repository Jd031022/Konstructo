<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnershipVerification extends Model
{
    use HasFactory;

    protected $table = 'ownership_verifications';

    protected $fillable = [
        'application_id',
        'is_owner',
        'tct_link',
        'tax_declaration_link',
        'current_tax_receipt_link',
        'spa_link',
        'assessor_status',
        'treasurer_status',
        'assessor_remarks',
        'treasurer_remarks',
        'assessor_verified_at',
        'treasurer_verified_at',
    ];

    protected $casts = [
        'is_owner' => 'boolean',
        'assessor_verified_at' => 'datetime',
        'treasurer_verified_at' => 'datetime',
    ];

    public function application()
    {
        return $this->belongsTo(ApplicationDocument::class, 'application_id');
    }
}
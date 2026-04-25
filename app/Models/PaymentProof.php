<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentProof extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'user_id',
        'or_link',
        'status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'zoning_cert_link',
        'locational_clearance_link',
        'zoning_cert_uploaded_at',
        'locational_clearance_uploaded_at',
        'zoning_cert_uploaded_by',
        'locational_clearance_uploaded_by'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
        'zoning_cert_uploaded_at' => 'datetime',
        'locational_clearance_uploaded_at' => 'datetime'
    ];

    public function application()
    {
        return $this->belongsTo(ApplicationDocument::class, 'application_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function zoningCertUploader()
    {
        return $this->belongsTo(User::class, 'zoning_cert_uploaded_by');
    }

    public function locationalClearanceUploader()
    {
        return $this->belongsTo(User::class, 'locational_clearance_uploaded_by');
    }

    public function markAsVerified($verifiedBy)
    {
        $this->update([
            'status' => 'verified',
            'verified_by' => $verifiedBy,
            'verified_at' => now()
        ]);
    }

    public function markAsRejected($verifiedBy, $reason)
    {
        $this->update([
            'status' => 'rejected',
            'verified_by' => $verifiedBy,
            'verified_at' => now(),
            'rejection_reason' => $reason
        ]);
    }
}
<?php
// app/Models/AssessmentFee.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssessmentFee extends Model
{
    protected $table = 'assessment_fees';
    
    protected $fillable = [
        'application_id',
        'line_grade',
        'building_fee',
        'sanitary_fee',
        'mechanical_fee',
        'electrical_fee',
        'others_amount',
        'others_description',
        'penalties_fines',
        'total_amount',
        'assessed_by',
        'assessed_at',
        'assessment_notes'
    ];
    
    protected $casts = [
        'line_grade' => 'decimal:2',
        'building_fee' => 'decimal:2',
        'sanitary_fee' => 'decimal:2',
        'mechanical_fee' => 'decimal:2',
        'electrical_fee' => 'decimal:2',
        'others_amount' => 'decimal:2',
        'penalties_fines' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'assessed_at' => 'datetime'
    ];
    
    public function application(): BelongsTo
    {
        return $this->belongsTo(ApplicationDocument::class, 'application_id');
    }
    
    public function assessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }
    
    public function calculateTotal(): float
    {
        $total = 0;
        $total += $this->line_grade ?? 0;
        $total += $this->building_fee ?? 0;
        $total += $this->sanitary_fee ?? 0;
        $total += $this->mechanical_fee ?? 0;
        $total += $this->electrical_fee ?? 0;
        $total += $this->others_amount ?? 0;
        $total += $this->penalties_fines ?? 0;
        return $total;
    }
}
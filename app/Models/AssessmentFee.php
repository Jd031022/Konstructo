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
        'penalties_fines',
        'total_amount',
        'assessed_by',
        'assessed_at',
        'assessment_notes',
        'additional_fees'
    ];
    
    protected $casts = [
        'line_grade' => 'decimal:2',
        'building_fee' => 'decimal:2',
        'sanitary_fee' => 'decimal:2',
        'mechanical_fee' => 'decimal:2',
        'electrical_fee' => 'decimal:2',
        'penalties_fines' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'assessed_at' => 'datetime',
        'additional_fees' => 'array'
    ];
    
    public function application(): BelongsTo
    {
        return $this->belongsTo(ApplicationDocument::class, 'application_id');
    }
    
    public function assessedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }
    
    /**
     * Calculate total from standard fees
     */
    public function calculateStandardTotal(): float
    {
        $total = 0;
        $total += $this->line_grade ?? 0;
        $total += $this->building_fee ?? 0;
        $total += $this->sanitary_fee ?? 0;
        $total += $this->mechanical_fee ?? 0;
        $total += $this->electrical_fee ?? 0;
        $total += $this->penalties_fines ?? 0;
        return $total;
    }
    
    /**
     * Calculate total from additional fees
     */
    public function calculateAdditionalTotal(): float
    {
        $total = 0;
        $additionalFees = $this->additional_fees ?? [];
        
        if (is_array($additionalFees)) {
            foreach ($additionalFees as $fee) {
                $total += $fee['amount'] ?? 0;
            }
        }
        
        return $total;
    }
    
    /**
     * Calculate grand total (standard + additional)
     */
    public function calculateTotal(): float
    {
        return $this->calculateStandardTotal() + $this->calculateAdditionalTotal();
    }
    
    /**
     * Get all fees including breakdown
     */
    public function getFeeBreakdown(): array
    {
        $breakdown = [];
        
        // Standard fees
        if ($this->line_grade > 0) $breakdown[] = ['name' => 'Line Grade', 'amount' => (float) $this->line_grade];
        if ($this->building_fee > 0) $breakdown[] = ['name' => 'Building Fee', 'amount' => (float) $this->building_fee];
        if ($this->sanitary_fee > 0) $breakdown[] = ['name' => 'Sanitary/Plumbing Fee', 'amount' => (float) $this->sanitary_fee];
        if ($this->mechanical_fee > 0) $breakdown[] = ['name' => 'Mechanical Fee', 'amount' => (float) $this->mechanical_fee];
        if ($this->electrical_fee > 0) $breakdown[] = ['name' => 'Electrical Fee', 'amount' => (float) $this->electrical_fee];
        if ($this->penalties_fines > 0) $breakdown[] = ['name' => 'Penalties/Fines', 'amount' => (float) $this->penalties_fines];
        
        // Additional fees
        $additionalFees = $this->additional_fees ?? [];
        if (is_array($additionalFees)) {
            foreach ($additionalFees as $fee) {
                if (!empty($fee['description']) && ($fee['amount'] ?? 0) > 0) {
                    $breakdown[] = [
                        'name' => $fee['description'],
                        'amount' => (float) ($fee['amount'] ?? 0)
                    ];
                }
            }
        }
        
        return $breakdown;
    }
    
    /**
     * Format total amount for display
     */
    public function getFormattedTotalAttribute(): string
    {
        return '₱ ' . number_format($this->calculateTotal(), 2);
    }
    
    /**
     * Scope for assessments created today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('assessed_at', today());
    }
    
    /**
     * Scope for assessments created this week
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('assessed_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }
    
    /**
     * Scope for assessments created this month
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('assessed_at', now()->month)
                     ->whereYear('assessed_at', now()->year);
    }
}
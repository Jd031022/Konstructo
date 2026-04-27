<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CPDORating extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'application_id',
        'user_id',
        'rating',
        'processing_time',
        'responsiveness',
        'clarity',
        'fairness',
        'overall_satisfaction',
        'comments'
    ];
    
    public function application()
    {
        return $this->belongsTo(ApplicationDocument::class, 'application_id');
    }
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientSatisfactionSurvey extends Model
{
    use HasFactory;

    protected $fillable = [
        'application_id',
        'user_id',
        'client_type',
        'survey_date',
        'sex',
        'age',
        'region_of_residence',
        'service_availed',
        'cc1_awareness',
        'cc2_helpfulness',
        'cc3_help_level',
        'sqd0_satisfied',
        'sqd1_reasonable_time',
        'sqd2_requirements_followed',
        'sqd3_steps_easy',
        'sqd4_info_easy_find',
        'sqd5_reasonable_fees',
        'sqd6_fair_treatment',
        'sqd7_courteous_staff',
        'sqd8_got_what_needed',
        'suggestions',
        'email'
    ];

    protected $casts = [
        'survey_date' => 'date',
        'age' => 'integer'
    ];

    /**
     * Get the application that this survey belongs to
     */
    public function application()
    {
        return $this->belongsTo(ApplicationDocument::class, 'application_id');
    }

    /**
     * Get the user who completed this survey
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

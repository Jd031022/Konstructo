<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BfpApplicationData extends Model
{
    use HasFactory;

    protected $table = 'bfp_application_data';

    protected $fillable = [
        'application_id',
        'bfp_user_id',
        'fsec_link',
        'fsec_filename',
        'bfp_comments',
        'fsec_uploaded_at',
        'bfp_comments_updated_at'
    ];

    protected $casts = [
        'fsec_uploaded_at' => 'datetime',
        'bfp_comments_updated_at' => 'datetime'
    ];

    public function application()
    {
        return $this->belongsTo(ApplicationDocument::class, 'application_id');
    }

    public function bfpUser()
    {
        return $this->belongsTo(User::class, 'bfp_user_id');
    }
}
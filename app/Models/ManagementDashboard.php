<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagementDashboard extends Model
{
    use HasFactory;
    protected $table = 'management_dashboard';
    protected $fillable = [
        'thumbnail',
        'title',
        'alternative_text',
        'description',
        'link'
    ];
}

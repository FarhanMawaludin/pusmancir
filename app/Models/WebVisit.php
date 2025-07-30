<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WebVisit extends Model
{
    use HasFactory;
    protected $table = 'web_visits';
    protected $primaryKey = 'id';
    protected $fillable = [
        'ip',
        'user_agent',
        'url',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrapedNews extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'scrapedId',
        'title',
        'link',
        'points',
        'created_date'
    ];
}

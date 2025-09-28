<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScrapingResult extends Model
{
    protected $fillable = [
        'portal', 'title', 'content', 'tanggal', 'hash', 'url', 'batch_id'
    ];
}

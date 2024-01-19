<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaStorage extends Model
{
    use HasFactory;
    protected $fillable = [
        'model_name',
        'full_name',
        'extension',
        'type',
        'size',
        'public_path',
        'storage_path',
    ];
}

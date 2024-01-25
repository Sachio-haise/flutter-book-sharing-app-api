<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory;
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'review',
        'photo_id',
        'book_id',
        'status'
    ];

    public function photo()
    {
        return $this->belongsTo(MediaStorage::class, 'photo_id');
    }
    public function book()
    {
        return $this->belongsTo(MediaStorage::class, 'book_id');
    }

    public function reactions()
    {
        return $this->hasMany(Reaction::class, 'id');
    }
}

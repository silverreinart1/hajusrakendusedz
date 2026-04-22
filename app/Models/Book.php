<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'my_favorite_subject';

    protected $fillable = [
        'title', 'image', 'description',
        'author', 'publication_year', 'genre', 'isbn'
    ];
}

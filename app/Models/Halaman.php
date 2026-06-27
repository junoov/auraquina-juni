<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Halaman extends Model
{
    protected $table = 'halamans';

    protected $fillable = [
        'slug',
        'title',
        'eyebrow',
        'description',
        'sections',
        'aktif',
        'urutan',
    ];

    protected $casts = [
        'sections' => 'array',
        'aktif' => 'boolean',
    ];
}

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
        'aktif' => 'boolean',
    ];

    public function getSectionsAttribute($value)
    {
        $sections = json_decode($value, true) ?: [];

        $isAdmin = (class_exists(\Filament\Facades\Filament::class) && \Filament\Facades\Filament::getCurrentPanel()?->getId() === 'admin')
            || request()->is('admin*')
            || (request()->headers->get('referer') && str_contains(request()->headers->get('referer'), '/admin'))
            || app()->runningInConsole();

        if ($isAdmin) {
            return collect($sections)->map(function ($section) {
                if (isset($section['body']) && is_array($section['body'])) {
                    $section['body'] = collect($section['body'])->map(function ($bodyItem) {
                        if (is_array($bodyItem)) {
                            return ['isi' => ($bodyItem['isi'] ?? $bodyItem['state'] ?? '')];
                        }
                        return ['isi' => $bodyItem];
                    })->all();
                }
                return $section;
            })->all();
        }

        return collect($sections)->map(function ($section) {
            if (isset($section['body']) && is_array($section['body'])) {
                $section['body'] = collect($section['body'])->map(function ($bodyItem) {
                    return is_array($bodyItem) ? ($bodyItem['isi'] ?? $bodyItem['state'] ?? '') : $bodyItem;
                })->all();
            }
            return $section;
        })->all();
    }

    public function setSectionsAttribute($value)
    {
        $sections = collect($value)->map(function ($section) {
            if (isset($section['body']) && is_array($section['body'])) {
                $section['body'] = collect($section['body'])->map(function ($bodyItem) {
                    return is_array($bodyItem) ? ($bodyItem['isi'] ?? $bodyItem['state'] ?? '') : $bodyItem;
                })->filter(fn ($v) => $v !== null && $v !== '')->values()->all();
            }
            return $section;
        })->all();

        $this->attributes['sections'] = json_encode($sections);
    }
}

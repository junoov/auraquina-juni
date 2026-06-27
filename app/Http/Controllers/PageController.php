<?php

namespace App\Http\Controllers;

use App\Models\Halaman;
use App\Models\Kategori;
use App\Support\HalamanDefaults;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $defaultPages = collect(HalamanDefaults::items())->keyBy('slug');
        $record = Halaman::query()->where('slug', $slug)->where('aktif', true)->first();

        $page = $record
            ? [
                'title' => $record->title,
                'eyebrow' => $record->eyebrow,
                'description' => $record->description,
                'sections' => $record->sections,
            ]
            : $defaultPages->get($slug);

        abort_unless($page, 404);

        $kategoris = Kategori::where('aktif', true)->orderBy('urutan')->get();

        return view('pages.show', compact('kategoris', 'page', 'slug'));
    }
}

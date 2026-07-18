<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class RegisterController extends Controller
{
    /**
     * Tampilkan halaman registrasi.
     */
    public function show(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect('/');
        }

        $kategoris = Kategori::where('aktif', true)->orderBy('urutan')->get();

        return view('auth.register', compact('kategoris'));
    }

    /**
     * Proses registrasi user baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['required', 'accepted'],
        ], [
            'first_name.required' => 'Nama depan wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email ini sudah terdaftar.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'terms.required' => 'Anda harus menyetujui Syarat & Ketentuan.',
            'terms.accepted' => 'Anda harus menyetujui Syarat & Ketentuan.',
        ]);

        $name = trim($validated['first_name'] . ' ' . ($validated['last_name'] ?? ''));

        $user = User::create([
            'name' => $name,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('pelanggan');

        Auth::login($user);

        $request->session()->regenerate();

        return redirect('/')->with('status', 'Selamat datang di Auraquina!');
    }
}

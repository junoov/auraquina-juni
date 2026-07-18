<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ItemKeranjang;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function show(): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect('/');
        }

        $kategoris = Kategori::where('aktif', true)->orderBy('urutan')->get();

        return view('auth.login', compact('kategoris'));
    }

    /**
     * Proses upaya login.
     */
    public function attempt(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password yang Anda masukkan salah.',
            ]);
        }

        // Simpan old_session_id sebelum regenerate
        $oldSessionId = $request->session()->getId();

        $request->session()->regenerate();

        // Pindahkan cart guest ke user yang baru login
        ItemKeranjang::where('session_id', $oldSessionId)
            ->whereNull('user_id')
            ->update([
                'user_id' => Auth::id(),
                'session_id' => $request->session()->getId(),
            ]);

        return redirect($this->destination());
    }

    private function destination(): string
    {
        return Auth::user()?->hasAnyRole([
            'owner',
            'admin',
            'operator_pesanan',
            'operator_produk',
            'operator_konten',
            'viewer',
        ]) ? '/admin' : '/';
    }

    /**
     * Logout.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

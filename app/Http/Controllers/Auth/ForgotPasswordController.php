<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm(): View
    {
        $kategoris = Kategori::where('aktif', true)->orderBy('urutan')->get();

        return view('auth.forgot-password', compact('kategoris'));
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $status = Password::sendResetLink(['email' => $validated['email']]);

        return back()->with(
            $status === Password::RESET_LINK_SENT ? 'status' : 'error',
            __($status === Password::RESET_LINK_SENT
                ? 'Tautan reset password sudah dikirim ke email Anda.'
                : 'Kami belum bisa memproses permintaan reset password untuk email tersebut.')
        );
    }
}

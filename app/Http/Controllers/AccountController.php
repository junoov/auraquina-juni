<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Pesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function show(Request $request): View
    {
        return $this->renderAccount($request, 'profile');
    }

    public function delivery(Request $request): View
    {
        return $this->renderAccount($request, 'delivery');
    }

    public function information(Request $request): View
    {
        return $this->renderAccount($request, 'information');
    }

    public function orders(Request $request): View
    {
        return $this->renderAccount($request, 'orders');
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->fill($validated)->save();

        return redirect()->route('account.show')->with('status', 'Profil berhasil diperbarui.');
    }

    public function updateDelivery(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
        ]);

        $user->fill($validated)->save();

        return redirect()->route('account.delivery')->with('status', 'Alamat pengiriman berhasil diperbarui.');
    }

    private function renderAccount(Request $request, string $section): View
    {
        $user = $request->user();
        $kategoris = Kategori::where('aktif', true)->orderBy('urutan')->get();
        $pesanans = Pesanan::with('items')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(8)
            ->withQueryString();

        $stats = [
            'total' => Pesanan::where('user_id', $user->id)->count(),
            'pending' => Pesanan::where('user_id', $user->id)->where('status', Pesanan::STATUS_PENDING_PAYMENT)->count(),
            'active' => Pesanan::where('user_id', $user->id)->whereIn('status', [
                Pesanan::STATUS_PAID,
                Pesanan::STATUS_PROCESSING,
                Pesanan::STATUS_PACKED,
                Pesanan::STATUS_SHIPPED,
                Pesanan::STATUS_DELIVERED,
            ])->count(),
            'after_sales' => Pesanan::where('user_id', $user->id)
                ->whereNotNull('after_sales_status')
                ->count(),
        ];

        return view('account.show', compact('user', 'kategoris', 'pesanans', 'stats', 'section'));
    }
}

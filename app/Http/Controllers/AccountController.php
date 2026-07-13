<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Pesanan;
use App\Models\UserAddress;
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

    public function storeAddress(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        // Prevent duplicate addresses from double-submit
        $duplicate = $user->addresses()
            ->where('label', $validated['label'])
            ->where('recipient_name', $validated['recipient_name'])
            ->where('city', $validated['city'])
            ->where('address', $validated['address'])
            ->exists();

        if ($duplicate) {
            return redirect()->route('account.delivery')->with('status', 'Alamat serupa sudah tersimpan.');
        }

        $isDefault = (bool) ($validated['is_default'] ?? false) || ! $user->addresses()->exists();

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $address = $user->addresses()->create([
            ...$validated,
            'phone' => $user->phone,
            'is_default' => $isDefault,
        ]);

        if ($address->is_default) {
            $user->fill([
                'recipient_name' => $address->recipient_name,
                'phone' => $address->phone,
                'city' => $address->city,
                'address' => $address->address,
            ])->save();
        }

        return redirect()->route('account.delivery')->with('status', 'Alamat tersimpan berhasil ditambahkan.');
    }

    public function updateAddress(Request $request, string $address): RedirectResponse
    {
        $user = $request->user();
        $addr = $user->addresses()->findOrFail($address);

        $validated = $request->validate([
            'label' => ['required', 'string', 'max:80'],
            'recipient_name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:1000'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $isDefault = (bool) ($validated['is_default'] ?? false);

        if ($isDefault) {
            $user->addresses()->update(['is_default' => false]);
        }

        $addr->update([
            ...$validated,
            'phone' => $user->phone,
            'is_default' => $isDefault,
        ]);

        if ($addr->is_default) {
            $user->fill([
                'recipient_name' => $addr->recipient_name,
                'phone' => $addr->phone,
                'city' => $addr->city,
                'address' => $addr->address,
            ])->save();
        }

        return redirect()->route('account.delivery')->with('status', 'Alamat berhasil diperbarui.');
    }

    public function destroyAddress(string $address): RedirectResponse
    {
        $user = request()->user();
        $addr = $user->addresses()->findOrFail($address);
        $wasDefault = $addr->is_default;
        $addr->delete();

        if ($wasDefault) {
            $newDefault = $user->addresses()->latest()->first();
            if ($newDefault) {
                $newDefault->update(['is_default' => true]);
                $user->fill([
                    'recipient_name' => $newDefault->recipient_name,
                    'phone' => $newDefault->phone,
                    'city' => $newDefault->city,
                    'address' => $newDefault->address,
                ])->save();
            }
        }

        return redirect()->route('account.delivery')->with('status', 'Alamat berhasil dihapus.');
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

        $addresses = UserAddress::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return view('account.show', compact('user', 'kategoris', 'pesanans', 'stats', 'section', 'addresses'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = $request->user();

        auth()->logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('status', 'Akun Anda berhasil dihapus.');
    }
}

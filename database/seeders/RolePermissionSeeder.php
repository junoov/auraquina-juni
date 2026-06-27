<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $resources = [
            'kategori', 'produk', 'varian', 'gambar', 'pesanan',
            'stok', 'voucher', 'customer', 'user', 'role', 'review', 'halaman',
        ];
        $actions = ['view', 'create', 'update', 'delete'];

        $permissions = [];
        foreach ($resources as $resource) {
            foreach ($actions as $action) {
                $permissions[] = "{$action}_{$resource}";
            }
        }

        $permissions = array_merge($permissions, [
            'process_pesanan',
            'cancel_pesanan',
            'refund_pesanan',
            'adjust_stok',
            'view_report',
            'manage_setting',
        ]);

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $owner->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(
            Permission::where('name', 'not like', '%_user')
                ->where('name', 'not like', '%_role')
                ->where('name', '!=', 'manage_setting')
                ->get()
        );

        $opPesanan = Role::firstOrCreate(['name' => 'operator_pesanan', 'guard_name' => 'web']);
        $opPesanan->syncPermissions([
            'view_pesanan', 'update_pesanan',
            'process_pesanan', 'cancel_pesanan', 'refund_pesanan',
            'view_customer',
        ]);

        $opProduk = Role::firstOrCreate(['name' => 'operator_produk', 'guard_name' => 'web']);
        $opProduk->syncPermissions([
            'view_kategori', 'create_kategori', 'update_kategori', 'delete_kategori',
            'view_produk', 'create_produk', 'update_produk', 'delete_produk',
            'view_varian', 'create_varian', 'update_varian', 'delete_varian',
            'view_gambar', 'create_gambar', 'update_gambar', 'delete_gambar',
            'view_stok', 'update_stok', 'adjust_stok',
        ]);

        $opKonten = Role::firstOrCreate(['name' => 'operator_konten', 'guard_name' => 'web']);
        $opKonten->syncPermissions([
            'view_kategori', 'create_kategori', 'update_kategori',
            'view_produk', 'update_produk',
            'view_gambar', 'create_gambar', 'update_gambar', 'delete_gambar',
            'view_review', 'update_review',
            'view_halaman', 'create_halaman', 'update_halaman', 'delete_halaman',
        ]);

        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions(
            Permission::where('name', 'like', 'view_%')->get()
        );

        $ownerUser = User::firstOrCreate(
            ['email' => 'owner@auraquina.id'],
            ['name' => 'Owner Auraquina', 'password' => Hash::make('password')]
        );
        if (! $ownerUser->hasRole('owner')) {
            $ownerUser->assignRole('owner');
        }
    }
}

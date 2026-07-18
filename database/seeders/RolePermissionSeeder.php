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

        // Delete old roles that are no longer needed
        Role::whereIn('name', ['operator_pesanan', 'operator_produk', 'operator_konten', 'viewer'])->delete();

        $owner = Role::firstOrCreate(['name' => 'owner', 'guard_name' => 'web']);
        $owner->syncPermissions(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(
            Permission::where('name', 'not like', '%_user')
                ->where('name', 'not like', '%_role')
                ->where('name', '!=', 'manage_setting')
                ->get()
        );

        $pelanggan = Role::firstOrCreate(['name' => 'pelanggan', 'guard_name' => 'web']);

        // Assign 'pelanggan' role to all existing users who do not have owner or admin roles
        $nonAdminUsers = User::whereDoesntHave('roles', function ($query) {
            $query->whereIn('name', ['owner', 'admin']);
        })->get();

        foreach ($nonAdminUsers as $user) {
            $user->assignRole('pelanggan');
        }

        $ownerUser = User::firstOrCreate(
            ['email' => 'owner@auraquina.id'],
            ['name' => 'Owner Auraquina', 'password' => Hash::make('password')]
        );
        if (! $ownerUser->hasRole('owner')) {
            $ownerUser->assignRole('owner');
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $adminRole = Role::create(['name' => 'admin']);
        $dokterRole = Role::create(['name' => 'dokter']);
        $pasienRole = Role::create(['name' => 'pasien']);

        // ========== PERMISSIONS UNTUK ADMIN ==========
        $adminPermissions = [
            'view_patients',
            'create_patients',
            'edit_patients',
            'delete_patients',

            'view_doctors',
            'create_doctors',
            'edit_doctors',
            'delete_doctors',
            'view_schedules',
            'create_schedules',
            'edit_schedules',
            'delete_schedules',

            'view_appointments',
            'create_appointments',
            'edit_appointments',
            'delete_appointments',
            'confirm_appointments',
            'cancel_appointments',


            'view_medical_records',


            'view_treatments',
            'create_treatments',
            'edit_treatments',
            'delete_treatments',

            'view_online_consultations',
            'manage_online_consultations',
        ];

        foreach ($adminPermissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // ========== PERMISSIONS UNTUK DOKTER ==========
        $dokterPermissions = [
            'view_own_schedules',

            'view_own_appointments',
            'view_appointment_details',

            'view_patients',
            'view_patient_details',
            'view_medical_records',
            'create_medical_records',
            'edit_own_medical_records',

            'view_online_consultations',
            'handle_online_consultations',
            'chat_with_patients',

            'view_treatments',
        ];

        foreach ($dokterPermissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        // ========== PERMISSIONS UNTUK PASIEN (Mobile App) ==========
        $pasienPermissions = [
            'view_own_appointments',
            'create_own_appointments',
            'view_own_medical_records',
            'chat_with_doctors',
            'create_online_consultations',
        ];

        foreach ($pasienPermissions as $permission) {
            if (!Permission::where('name', $permission)->exists()) {
                Permission::create(['name' => $permission]);
            }
        }

        // ========== ASSIGN PERMISSIONS ==========
        $adminRole->givePermissionTo(Permission::all());
        $dokterRole->givePermissionTo($dokterPermissions);
        $pasienRole->givePermissionTo($pasienPermissions);

        // ========== BUAT USER ADMIN ==========
        $admin = User::create([
            'name' => 'Admin Klinik',
            'email' => 'admin@klinik.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
        ]);
        $admin->assignRole('admin');

        // ========== BUAT USER DOKTER (Contoh) ==========
        $dokter1 = User::create([
            'name' => 'Dr. Budi Santoso',
            'email' => 'budi@klinik.com',
            'password' => Hash::make('password'),
            'phone' => '081234567890',
            'email_verified_at' => now(),
            'specialization' => 'Dokter Gigi Umum',
            'license_number' => 'STR-12345678',
            'is_active' => true,
        ]);
        $dokter1->assignRole('dokter');

        $dokter2 = User::create([
            'name' => 'Dr. Siti Nurhaliza',
            'email' => 'siti@klinik.com',
            'password' => Hash::make('password'),
            'phone' => '081234567891',
            'email_verified_at' => now(),
            'specialization' => 'Ortodonti',
            'license_number' => 'STR-87654321',
            'is_active' => true,
        ]);
        $dokter2->assignRole('dokter');
    }
}

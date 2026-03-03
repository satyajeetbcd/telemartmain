<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // User Management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            
            // Role Management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            
            // Invitation Management
            'invitations.view',
            'invitations.create',
            'invitations.delete',
            
            // Patient Management
            'patients.view',
            'patients.create',
            'patients.edit',
            'patients.delete',
            
            // Appointment Management
            'appointments.view',
            'appointments.create',
            'appointments.edit',
            'appointments.delete',
            
            // Medical Records
            'records.view',
            'records.create',
            'records.edit',
            'records.delete',
            
            // Dashboard Access
            'dashboard.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Hospital Roles
        $roles = [
            [
                'name' => 'Super Admin',
                'description' => 'Full system access with all permissions',
                'permissions' => $permissions,
            ],
            [
                'name' => 'Administrator',
                'description' => 'Hospital administrator with management access',
                'permissions' => [
                    'users.view', 'users.create', 'users.edit',
                    'roles.view', 'roles.create', 'roles.edit',
                    'invitations.view', 'invitations.create', 'invitations.delete',
                    'patients.view', 'patients.create', 'patients.edit', 'patients.delete',
                    'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.delete',
                    'records.view', 'records.create', 'records.edit', 'records.delete',
                    'dashboard.view',
                ],
            ],
            [
                'name' => 'Doctor',
                'description' => 'Medical doctor with patient care access',
                'permissions' => [
                    'patients.view', 'patients.create', 'patients.edit',
                    'appointments.view', 'appointments.create', 'appointments.edit',
                    'records.view', 'records.create', 'records.edit',
                    'dashboard.view',
                ],
            ],
            [
                'name' => 'Nurse',
                'description' => 'Nursing staff with patient care access',
                'permissions' => [
                    'patients.view', 'patients.edit',
                    'appointments.view', 'appointments.create', 'appointments.edit',
                    'records.view', 'records.create', 'records.edit',
                    'dashboard.view',
                ],
            ],
            [
                'name' => 'Receptionist',
                'description' => 'Front desk staff with appointment management',
                'permissions' => [
                    'patients.view', 'patients.create',
                    'appointments.view', 'appointments.create', 'appointments.edit',
                    'dashboard.view',
                ],
            ],
            [
                'name' => 'Pharmacist',
                'description' => 'Pharmacy staff with medication management',
                'permissions' => [
                    'patients.view',
                    'records.view', 'records.edit',
                    'dashboard.view',
                ],
            ],
            [
                'name' => 'Lab Technician',
                'description' => 'Laboratory staff with test management',
                'permissions' => [
                    'patients.view',
                    'records.view', 'records.create', 'records.edit',
                    'dashboard.view',
                ],
            ],
            [
                'name' => 'Accountant',
                'description' => 'Financial staff with billing access',
                'permissions' => [
                    'patients.view',
                    'appointments.view',
                    'dashboard.view',
                ],
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleData['name'], 'guard_name' => 'web'],
                ['description' => $roleData['description']]
            );

            // Find permissions by name and sync
            $permissionModels = Permission::whereIn('name', $roleData['permissions'])->get();
            $role->syncPermissions($permissionModels);
        }
    }
}


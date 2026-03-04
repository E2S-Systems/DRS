<?php

namespace Database\Seeders;

use App\Enums\RoleUser;
use App\Support\PermissionsHelper;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesSeeder extends Seeder
{
    public function run()
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->removeDeprecatedPermissions();

        foreach (RoleUser::cases() as $roleEnum) {
            Role::firstOrCreate(['name' => $roleEnum->value]);
        }

        $profiles = config('profile-permissions');
        foreach ($profiles as $profileName => $permissions) {
            $role = Role::where('name', $profileName)->first();

            if ($role) {
                $rolePermissions = PermissionsHelper::getFlattenPermissions($permissions);
                $role->syncPermissions($rolePermissions);
            }
        }
    }

    private function getConfigPermissions(): Collection
    {
        return collect(PermissionsHelper::getFlattenPermissions(config('permissions')));
    }

    private function removeDeprecatedPermissions()
    {
        $configPermissions = $this->getConfigPermissions();
        $databasePermissions = Permission::all(['name'])->pluck('name');

        $deprecatedPermissions = $databasePermissions->diff($configPermissions);
        Permission::whereIn('name', $deprecatedPermissions)->delete();
    }
}

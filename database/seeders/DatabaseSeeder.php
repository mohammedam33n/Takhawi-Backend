<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Http\Controllers\Backend\SubCategoryController;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\BadWords;
use App\Models\Category;
use App\Models\Coins;
use App\Models\Coins_values;
use App\Models\Post;
use App\Models\Badge;
use App\Models\Level;
use App\Models\Tribe;
use App\Models\Sub_category;
use Spatie\Permission\Middlewares\RoleOrPermissionMiddleware;
// use Database\Factories\catFactory;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = Admin::create([
            'name' => 'appAdmin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin123')
        ]);

        # ##########################################################

        \App\Models\User::factory()->create([
            'name' => 'appUser',
            'email' => 'user@user.com',
            'password' => bcrypt('user123')
        ]);

        # ##########################################################

        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'admin-list',
            'admin-create',
            'admin-edit',
            'admin-delete',
            'dashboard-list',
            'dashboard-create',
            'dashboard-edit',
            'dashboard-delete',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'admin']);
        }

        # ##########################################################

        $role = Role::create(['name' => 'Admin', 'guard_name' => 'admin']);
        $permissions = Permission::pluck('id','id')->all();
        $role->syncPermissions($permissions);
        $admin->assignRole($role->id);

        ##################################################################
    }
}

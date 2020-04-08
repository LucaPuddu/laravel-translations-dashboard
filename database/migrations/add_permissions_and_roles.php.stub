<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddPermissionsAndRoles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create permissions if they don't exist already
        Permission::findOrCreate('manage-languages');
        Permission::findOrCreate('manage-pages');
        Permission::findOrCreate('manage-settings');
        Permission::findOrCreate('translate');

        // Create roles if they don't exist already
        $admin = Role::findOrCreate('admin');
        $admin->givePermissionTo(Permission::all());

        $translator = Role::findOrCreate('translator');
        $translator->givePermissionTo('translate');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Role::where('name', 'admin')->delete();
        Role::where('name', 'translator')->delete();

        Permission::where('name', 'manage-languages')->delete();
        Permission::where('name', 'manage-pages')->delete();
        Permission::where('name', 'manage-settings')->delete();
        Permission::where('name', 'translate')->delete();
    }
}

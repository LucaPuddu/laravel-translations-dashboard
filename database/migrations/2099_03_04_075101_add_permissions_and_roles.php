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
        Permission::create(['name' => 'manage-languages']);
        Permission::create(['name' => 'manage-pages']);
        Permission::create(['name' => 'manage-settings']);
        Permission::create(['name' => 'translate']);

        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo(Permission::all());

        $translator = Role::create(['name' => 'translator']);
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

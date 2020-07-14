<?php

use App\Role;
use App\Client;
use App\Permission;
use Illuminate\Database\Seeder;

class ClientRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client = Client::create([
            'id'             => 1,
            'first_name'     => 'Nunolawyer',
            'last_name'      => 'Admin',
            'email'          => 'nrcadvogados.pt@gmail.com',
            'password'       => Hash::make('Shiv@Nrcadvogados'),
            'is_superadmin'  => true,
        ]);

        $adminRole = Role::create([
            'id' => 1,
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        $permissions = array(
            ['name' => 'clients_access', 'display_name' => 'Access', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'clients_create', 'display_name' => 'Create', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'clients_show', 'display_name' => 'Show', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'clients_edit', 'display_name' => 'Edit', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'clients_delete', 'display_name' => 'Delete', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'clients_ban', 'display_name' => 'Ban/Activate client', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'clients_activity', 'display_name' => 'Activity Log', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'clients_print', 'display_name' => 'Client Print', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'editors_show', 'display_name' => 'Editor Show', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'editors_access', 'display_name' => 'Editor Access', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],

            ['name' => 'roles_access', 'display_name' => 'Access', 'group_name' => 'Roles', 'group_slug' => 'roles', 'guard_name' => 'web'],
            ['name' => 'roles_create', 'display_name' => 'Create', 'group_name' => 'Roles', 'group_slug' => 'roles', 'guard_name' => 'web'],
            ['name' => 'roles_show', 'display_name' => 'Show', 'group_name' => 'Roles', 'group_slug' => 'roles', 'guard_name' => 'web'],
            ['name' => 'roles_edit', 'display_name' => 'Edit', 'group_name' => 'Roles', 'group_slug' => 'roles', 'guard_name' => 'web'],
            ['name' => 'roles_delete', 'display_name' => 'Delete', 'group_name' => 'Roles', 'group_slug' => 'roles', 'guard_name' => 'web'],

            ['name' => 'permissions_access', 'display_name' => 'Access', 'group_name' => 'Permissions', 'group_slug' => 'permissions', 'guard_name' => 'web'],
            ['name' => 'permissions_create', 'display_name' => 'Create', 'group_name' => 'Permissions', 'group_slug' => 'permissions', 'guard_name' => 'web'],
            ['name' => 'permissions_show', 'display_name' => 'Show', 'group_name' => 'Permissions', 'group_slug' => 'permissions', 'guard_name' => 'web'],
            ['name' => 'permissions_edit', 'display_name' => 'Edit', 'group_name' => 'Permissions', 'group_slug' => 'permissions', 'guard_name' => 'web'],
            ['name' => 'permissions_delete', 'display_name' => 'Delete', 'group_name' => 'Permissions', 'group_slug' => 'permissions', 'guard_name' => 'web'],

            ['name' => 'activitylog_access', 'display_name' => 'Access', 'group_name' => 'Activity Log', 'group_slug' => 'activitylog', 'guard_name' => 'web'],
            ['name' => 'activitylog_show', 'display_name' => 'Show', 'group_name' => 'Activity Log', 'group_slug' => 'activitylog', 'guard_name' => 'web'],
            ['name' => 'activitylog_delete', 'display_name' => 'Delete', 'group_name' => 'Activity Log', 'group_slug' => 'activitylog', 'guard_name' => 'web'],
        );

        Permission::insert($permissions);

        $getPermissions = Permission::get();

        $assignPermissions = $getPermissions->map(function($item){
            return [$item->name];
        });

        $client->assignRole($adminRole);
        $adminRole->givePermissionTo($assignPermissions);

        $clientRole = Role::create([
            'id' => 2,
            'name' => 'client',
            'guard_name' => 'web'
        ]);

        $assignClientPermissions = $getPermissions->map(function($item){
            $restrictedPerms = ['clients_delete', 'clients_ban', 'clients_activity', 'roles_delete', 'permissions_delete', 'activitylog_delete'];
            if(!in_array($item->name, $restrictedPerms)){
                return [$item->name];
            }
        });
        $clientRole->givePermissionTo($assignClientPermissions);

        $editorRole = Role::create([
            'id' => 3,
            'name' => 'editor',
            'guard_name' => 'web'
        ]);
        /*$assignEditorPermissions = $getPermissions->map(function($item){
            $restrictedPerms = ['clients_delete', 'clients_ban', 'clients_activity', 'roles_delete', 'permissions_delete', 'activitylog_delete'];
            if (!in_array($item->name, $restrictedPerms)) {
                return [$item->name];
            }
        });
        $editorRole->givePermissionTo($assignEditorPermissions);*/
    }
}

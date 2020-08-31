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
            'password'       => (env('APP_ENV') == 'local') ? Hash::make('Shiv@Nrcadvogados') : Hash::make('Portugal@123'),
            'password_text'  => (env('APP_ENV') == 'local') ? 'Shiv@Nrcadvogados' : 'Portugal@123',
            'is_superadmin'  => true,
        ]);

        $adminRole = Role::create([
            'id' => 1,
            'name' => 'admin',
            'guard_name' => 'web'
        ]);

        $permissions = array(
            ['name' => 'clients_access', 'display_name' => 'Client Access', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'clients_create', 'display_name' => 'Client Create', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'clients_show', 'display_name' => 'Client Show', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'clients_edit', 'display_name' => 'Client Edit', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'clients_delete', 'display_name' => 'Client Delete', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'clients_ban', 'display_name' => 'Ban/Activate client', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'clients_activity', 'display_name' => 'Client activity Log', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'clients_activity_own', 'display_name' => 'Client activity Log Own', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'clients_print', 'display_name' => 'Client Print', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],
            ['name' => 'clients_email', 'display_name' => 'Client Email', 'group_name' => 'Clients', 'group_slug' => 'clients', 'guard_name' => 'web'],

            ['name' => 'editors_create', 'display_name' => 'Editor Create', 'group_name' => 'Editors', 'group_slug' => 'editors', 'guard_name' => 'web'],
            ['name' => 'editors_edit', 'display_name' => 'Editor Edit', 'group_name' => 'Editors', 'group_slug' => 'editors', 'guard_name' => 'web'],
            ['name' => 'editors_delete', 'display_name' => 'Editor Delete', 'group_name' => 'Editors', 'group_slug' => 'editors', 'guard_name' => 'web'],
            ['name' => 'editors_ban', 'display_name' => 'Editor Ban/Activate editor', 'group_name' => 'Editors', 'group_slug' => 'editors', 'guard_name' => 'web'],
            ['name' => 'editors_activity', 'display_name' => 'Editor activity Log', 'group_name' => 'Editors', 'group_slug' => 'editors', 'guard_name' => 'web'],
            ['name' => 'editors_print', 'display_name' => 'Editor Print', 'group_name' => 'Editors', 'group_slug' => 'editors', 'guard_name' => 'web'],
            ['name' => 'editors_show', 'display_name' => 'Editor Show', 'group_name' => 'Editors', 'group_slug' => 'editors', 'guard_name' => 'web'],
            ['name' => 'editors_access', 'display_name' => 'Editor Access', 'group_name' => 'Editors', 'group_slug' => 'editors', 'guard_name' => 'web'],
            ['name' => 'editors_email', 'display_name' => 'Editor Email', 'group_name' => 'Editors', 'group_slug' => 'editors', 'guard_name' => 'web'],

            ['name' => 'article_purpose_access', 'display_name' => 'Article purpose access', 'group_name' => 'ArticlePurpose', 'group_slug' => 'article_purpose', 'guard_name' => 'web'],
            // ['name' => 'article_purpose_show', 'display_name' => 'Article purpose show', 'group_name' => 'ArticlePurpose', 'group_slug' => 'article_purpose', 'guard_name' => 'web'],
            ['name' => 'article_purpose_create', 'display_name' => 'Article purpose Create', 'group_name' => 'ArticlePurpose', 'group_slug' => 'article_purpose', 'guard_name' => 'web'],
            ['name' => 'article_purpose_edit', 'display_name' => 'Article purpose Edit', 'group_name' => 'ArticlePurpose', 'group_slug' => 'article_purpose', 'guard_name' => 'web'],
            ['name' => 'article_purpose_delete', 'display_name' => 'Article purpose Delete', 'group_name' => 'ArticlePurpose', 'group_slug' => 'article_purpose', 'guard_name' => 'web'],
            ['name' => 'article_purpose_show_client', 'display_name' => 'Article purpose show client', 'group_name' => 'ArticlePurpose', 'group_slug' => 'article_purpose', 'guard_name' => 'web'],
            ['name' => 'article_purpose_show_editor', 'display_name' => 'Article purpose show editor', 'group_name' => 'ArticlePurpose', 'group_slug' => 'article_purpose', 'guard_name' => 'web'],

            ['name' => 'poa_access', 'display_name' => 'POA access', 'group_name' => 'POA', 'group_slug' => 'poa', 'guard_name' => 'web'],
            // ['name' => 'poa_show', 'display_name' => 'POA show', 'group_name' => 'POA', 'group_slug' => 'poa', 'guard_name' => 'web'],
            ['name' => 'poa_create', 'display_name' => 'POA Create', 'group_name' => 'POA', 'group_slug' => 'poa', 'guard_name' => 'web'],
            ['name' => 'poa_edit', 'display_name' => 'POA Edit', 'group_name' => 'POA', 'group_slug' => 'poa', 'guard_name' => 'web'],
            ['name' => 'poa_delete', 'display_name' => 'POA Delete', 'group_name' => 'POA', 'group_slug' => 'poa', 'guard_name' => 'web'],
            ['name' => 'poa_view', 'display_name' => 'POA view', 'group_name' => 'POA', 'group_slug' => 'poa', 'guard_name' => 'web'],
            ['name' => 'poa_download', 'display_name' => 'POA download', 'group_name' => 'POA', 'group_slug' => 'poa', 'guard_name' => 'web'],

            ['name' => 'account_access', 'display_name' => 'Account access', 'group_name' => 'Account', 'group_slug' => 'account', 'guard_name' => 'web'],

            ['name' => 'follow_up_access', 'display_name' => 'Follow up access', 'group_name' => 'FollowUp', 'group_slug' => 'FollowUp', 'guard_name' => 'web'],
            ['name' => 'follow_up_show', 'display_name' => 'Follow up show', 'group_name' => 'FollowUp', 'group_slug' => 'FollowUp', 'guard_name' => 'web'],

            ['name' => 'translate_model_document_access', 'display_name' => 'Translate Model Document access', 'group_name' => 'TMD', 'group_slug' => 'tmd', 'guard_name' => 'web'],
            ['name' => 'translate_model_document_create', 'display_name' => 'Translate Model Document Create', 'group_name' => 'TMD', 'group_slug' => 'tmd', 'guard_name' => 'web'],
            ['name' => 'translate_model_document_edit', 'display_name' => 'Translate Model Document Edit', 'group_name' => 'TMD', 'group_slug' => 'tmd', 'guard_name' => 'web'],
            ['name' => 'translate_model_document_delete', 'display_name' => 'Translate Model Document Delete', 'group_name' => 'TMD', 'group_slug' => 'tmd', 'guard_name' => 'web'],
            ['name' => 'translate_model_document_show_client', 'display_name' => 'Translate Model Document show client', 'group_name' => 'TMD', 'group_slug' => 'tmd', 'guard_name' => 'web'],
            ['name' => 'translate_model_document_show_file', 'display_name' => 'Translate Model Document view file', 'group_name' => 'TMD', 'group_slug' => 'tmd', 'guard_name' => 'web'],
            ['name' => 'translate_model_document_download', 'display_name' => 'Translate Model Document Download', 'group_name' => 'TMD', 'group_slug' => 'tmd', 'guard_name' => 'web'],

            ['name' => 'poa_access', 'display_name' => 'POA access', 'group_name' => 'POA', 'group_slug' => 'poa', 'guard_name' => 'web'],
            // ['name' => 'poa_show', 'display_name' => 'POA show', 'group_name' => 'POA', 'group_slug' => 'poa', 'guard_name' => 'web'],
            ['name' => 'poa_create', 'display_name' => 'POA Create', 'group_name' => 'POA', 'group_slug' => 'poa', 'guard_name' => 'web'],
            ['name' => 'poa_edit', 'display_name' => 'POA Edit', 'group_name' => 'POA', 'group_slug' => 'poa', 'guard_name' => 'web'],
            ['name' => 'poa_delete', 'display_name' => 'POA Delete', 'group_name' => 'POA', 'group_slug' => 'poa', 'guard_name' => 'web'],

            ['name' => 'our_fee_policy_document_access', 'display_name' => 'Our Fee Policy Document access', 'group_name' => 'OurFeePolicy', 'group_slug' => 'our_fee_policy_document', 'guard_name' => 'web'],
            ['name' => 'our_fee_policy_document_create', 'display_name' => 'Our Fee Policy Document Create', 'group_name' => 'OurFeePolicy', 'group_slug' => 'our_fee_policy_document', 'guard_name' => 'web'],
            ['name' => 'our_fee_policy_document_edit', 'display_name' => 'Our Fee Policy Document Edit', 'group_name' => 'OurFeePolicy', 'group_slug' => 'our_fee_policy_document', 'guard_name' => 'web'],
            ['name' => 'our_fee_policy_document_delete', 'display_name' => 'Our Fee Policy Document Delete', 'group_name' => 'OurFeePolicy', 'group_slug' => 'our_fee_policy_document', 'guard_name' => 'web'],

            ['name' => 'terms_and_conditions_access', 'display_name' => 'Terms and conditions access', 'group_name' => 'TermsAndConditions', 'group_slug' => 'terms_and_conditions', 'guard_name' => 'web'],
            ['name' => 'terms_and_conditions_create', 'display_name' => 'Terms and conditions Create', 'group_name' => 'TermsAndConditions', 'group_slug' => 'terms_and_conditions', 'guard_name' => 'web'],
            ['name' => 'terms_and_conditions_edit', 'display_name' => 'Terms and conditions Edit', 'group_name' => 'TermsAndConditions', 'group_slug' => 'terms_and_conditions', 'guard_name' => 'web'],
            ['name' => 'terms_and_conditions_delete', 'display_name' => 'Terms and conditions Delete', 'group_name' => 'TermsAndConditions', 'group_slug' => 'terms_and_conditions', 'guard_name' => 'web'],

            /*['name' => 'editors_profile_access', 'display_name' => 'Editor profile', 'group_name' => 'EditorsProfile', 'group_slug' => 'editors_profile', 'guard_name' => 'web'],
            ['name' => 'clients_profile_access', 'display_name' => 'Client profile', 'group_name' => 'ClientsProfile', 'group_slug' => 'clients_profile', 'guard_name' => 'web'],
            ['name' => 'admin_profile_access', 'display_name' => 'Admin profile', 'group_name' => 'AdminProfile', 'group_slug' => 'admin_profile', 'guard_name' => 'web'],*/

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
            $restrictedPerms = ['clients_delete', 'clients_ban', 'clients_activity', 'clients_activity_own', 'editors_delete', 'editors_ban', 'editors_activity', 'roles_delete', 'permissions_delete', 'activitylog_delete'];
            if (!in_array($item->name, $restrictedPerms) && $item->group_slug == 'clients') {
                return [$item->name];
            }
        });
        $clientRole->givePermissionTo($assignClientPermissions);

        $editorRole = Role::create([
            'id' => 3,
            'name' => 'editor',
            'guard_name' => 'web'
        ]);
        $assignEditorPermissions = $getPermissions->map(function($item){
            $restrictedPerms = ['editors_delete', 'editors_ban', 'editors_activity', 'roles_delete', 'permissions_delete', 'activitylog_delete'];
            if (!in_array($item->name, $restrictedPerms) && $item->group_slug == 'editors') {
                return [$item->name];
            }
        });
        $editorRole->givePermissionTo($assignEditorPermissions);
    }
}

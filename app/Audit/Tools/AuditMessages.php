<?php

namespace App\Audit\Tools;

use App\Role;
use App\Client;
use App\ClientPurposeArticle;
use App\Permission;
use App\ClientFee;
use App\DeletedRecord;
use App\Account;
use App\ClientCondition;
use App\ClientPrivateInformation;
use App\ClientDocument;
use App\ClientEmailProgressReport;
use App\ClientTermsAndCondition;
use App\FollowUp;

class AuditMessages 
{
    
    /**
     * Get readable messages for audits
     *
     * @param object $audits
     * @return object $audits
     */
    static public function get($audits){
        foreach ($audits as $key => $value) {
            $audits[$key]['event_message'] = self::getMessage($value);
        }

        return $audits;
    }

    /**
     * Get specified messages for an audit
     *
     * @param object $audit
     * @return string $message
     */
    static public function getMessage($audit){
        $message = "";
        if(isset($audit->new_values['last_login_at'])){
            $message = __("Logged in.");
        } elseif(isset($audit->new_values['last_logout_at'])){
            $message = __("Logged out.");
        } elseif($audit->event == "created"){
            $message = self::getMessageForCreated($audit);
        } elseif($audit->event == "updated"){
            $message = self::getMessageForUpdated($audit);
        } elseif($audit->event == "deleted"){
            $message = self::getMessageForDeleted($audit);
        }

        return $message;
    }

    /**
     * Get message for created event
     *
     * @param object $audit
     * @return string $message
     */
    static private function getMessageForCreated($audit){
        $message     = "";
        $createdUser = Client::find($audit->user_id);

        if($audit->auditable_type == "App\Client"){
            $auditale_user = Client::find($audit->auditable_id);

            if($auditale_user){
                $message = __("Created a new {$auditale_user->getCurentRoleName()} by ({$createdUser->first_name} {$createdUser->last_name}) named")." : ".$auditale_user->first_name . ' ' . $auditale_user->last_name;
            } else {
                $message = __("Created a new client/editor that no longer exists.");
            }
        } elseif($audit->auditable_type == "App\ClientPurposeArticle") {
            $auditale     = ClientPurposeArticle::with('purposeArticle')->find($audit->auditable_id);
            if ($auditale && !empty($auditale->purposeArticle)) {
                $message = __("Added a new client purpose article by ({$createdUser->first_name} {$createdUser->last_name}) to ({$createdUser->getClientNames($auditale->client_id)}) named")." : ".$auditale->purposeArticle->title;
            } else {
                $message = __("Created a new purpose article that no longer exists.");
            }
        } elseif($audit->auditable_type == "App\Role"){
            $auditale_role = Role::find($audit->auditable_id);
            if($auditale_role){
                $message = __("Created a new role named")." : ".$auditale_role->name;
            } else {
                $message = __("Created a new role that no longer exists.");
            }
        } elseif($audit->auditable_type == "App\Permission"){
            $auditale_permission = Permission::find($audit->auditable_id);
            if($auditale_permission){
                $message = __("Created a new permission named")." : ".$auditale_permission->name;
            } else {
                $message = __("Created a new permission that no longer exists.");
            }
        } elseif ($audit->auditable_type == "App\ClientFee") {
            $auditable_fee = ClientFee::find($audit->auditable_id);
            $auditaleUser  = false;

            if ($auditable_fee) {
                $auditaleUser = Client::find($auditable_fee->client_id);

                $message = __("Created a new fees by ({$createdUser->first_name} {$createdUser->last_name}) for")." : (".$auditaleUser->first_name . ' ' . $auditaleUser->last_name . ")";
            } else {
                $message = __("Created a new fee that no longer exists.");
            }
        } elseif ($audit->auditable_type == "App\Account") {
            $find = Account::find($audit->auditable_id);

            if ($find) {
                $message = __("Created a new account by ({$createdUser->first_name} {$createdUser->last_name}) for ({$createdUser->getClientNames($find->getAttributes()['client_id'])}) received amount : {$find->received_amount}");
            } else {
                $message = __("Created a new account that no longer exists.");
            }
        } elseif ($audit->auditable_type == "App\ClientCondition") {
            $find = ClientCondition::find($audit->auditable_id);

            if ($find) {
                $message = __("Created a new client condition by ({$createdUser->first_name} {$createdUser->last_name}) for : ({$createdUser->getClientNames($find->getAttributes()['client_id'])})");
            } else {
                $message = __("Created a new client condition that no longer exists.");
            }
        } elseif ($audit->auditable_type == "App\ClientPrivateInformation") {
            $find = ClientPrivateInformation::find($audit->auditable_id);

            if ($find) {
                $message = __("Created a new client private informations by ({$createdUser->first_name} {$createdUser->last_name}) for : ({$createdUser->getClientNames($find->getAttributes()['client_id'])})");
            } else {
                $message = __("Created a new client private informations that no longer exists.");
            }
        } elseif ($audit->auditable_type == "App\ClientDocument") {
            $find = ClientDocument::find($audit->auditable_id);

            if ($find) {
                $message = __("Created a new client documents by ({$createdUser->first_name} {$createdUser->last_name}) for : ({$createdUser->getClientNames($find->getAttributes()['client_id'])})");
            } else {
                $message = __("Created a new client documents that no longer exists.");
            }
        } elseif ($audit->auditable_type == "App\ClientEmailProgressReport") {
            $find = ClientEmailProgressReport::find($audit->auditable_id);

            if ($find) {
                $message = __("Created a new client email progress reports by ({$createdUser->first_name} {$createdUser->last_name}) for : ({$createdUser->getClientNames($find->getAttributes()['client_id'])})");
            } else {
                $message = __("Created a new client email progress reports that no longer exists.");
            }
        } elseif ($audit->auditable_type == "App\ClientTermsAndCondition") {
            $find = ClientTermsAndCondition::find($audit->auditable_id);

            if ($find) {
                $message = __("Created a new client terms and conditions by ({$createdUser->first_name} {$createdUser->last_name}) for : ({$createdUser->getClientNames($find->getAttributes()['client_id'])})");
            } else {
                $message = __("Created a new client terms and conditions that no longer exists.");
            }
        } elseif ($audit->auditable_type == "App\FollowUp") {
            $find = FollowUp::find($audit->auditable_id);

            if ($find) {
                $message = __("Created a new follow ups by ({$createdUser->first_name} {$createdUser->last_name}) for : ({$createdUser->getClientNames($find->getAttributes()['client_id'])})");
            } else {
                $message = __("Created a new follow ups that no longer exists.");
            }
        }

        return $message;
    }

    /**
     * Get message for updated event
     *
     * @param object $audit
     * @return string $message
     */
    static private function getMessageForUpdated($audit){
        $message     = "";
        $createdUser = Client::find($audit->user_id);

        if($audit->auditable_type == "App\Client"){
            if(isset($audit->old_values['banned'])){
                $auditale_user = Client::find($audit->auditable_id);
                if($auditale_user){
                    if($audit->new_values['banned']){
                        $message = __("Banned a client account. Client :")." : ".$auditale_user->first_name . ' ' . $auditale_user->last_name;
                    } else {
                        $message = __("Activated a client account. Client :")." : ".$auditale_user->first_name . ' ' . $auditale_user->last_name;
                    }
                } else {
                    if($audit->new_values['banned']){
                        $message = __("Banned a client account that no longer exists.");
                    } else {
                        $message = __("Activated a client account that no longer exists.");
                    }
                }
            } else {
                $auditale_user = Client::find($audit->auditable_id);
                if($auditale_user){
                    $message = __("Changed client information by ({$createdUser->first_name} {$createdUser->last_name}) for")." : (".$auditale_user->first_name . ' ' . $auditale_user->last_name . ")";
                } else {
                    $message = __("Changed client information for a client/editor that no longer exists.");
                }
            }
        } elseif($audit->auditable_type == "App\Role"){
            $auditale_role = Role::find($audit->auditable_id);
            if($auditale_role){
                $message = __("Changed information for a role named")." : ".$auditale_role->name;
            } else {
                $message = __("Changed information for a role that no longer exists.");
            }
        } elseif($audit->auditable_type == "App\Permission"){
            $auditale_permission = Permission::find($audit->auditable_id);
            if($auditale_permission){
                $message = __("Changed information for a permission named")." : ".$auditale_permission->name;
            } else {
                $message = __("Changed information for a permission that no longer exists.");
            }
        } elseif ($audit->auditable_type == "App\ClientFee") {
            $find         = ClientFee::find($audit->auditable_id);
            $auditaleUser = false;

            if ($find) {
                $auditaleUser = Client::find($find->client_id);

                if ($auditaleUser) {
                    $message = __("Changed client fees info by ({$createdUser->first_name} {$createdUser->last_name}) for")." : (".$auditaleUser->first_name . ' ' . $auditaleUser->last_name . ")";
                }
            }
            if (!$find || !$auditaleUser) {
                $message = __("Changed information for a permission that no longer exists.");
            }
        } elseif ($audit->auditable_type == "App\ClientCondition") {
            $find         = ClientCondition::find($audit->auditable_id);
            $auditaleUser = false;

            if ($find) {
                $auditaleUser = Client::find($find->client_id);

                if ($auditaleUser) {
                    $message = __("Changed client condition info by ({$createdUser->first_name} {$createdUser->last_name}) for")." : (".$auditaleUser->first_name . ' ' . $auditaleUser->last_name . ")";
                }
            }
            if (!$find || !$auditaleUser) {
                $message = __("Changed information for a condition that no longer exists.");
            }
        } elseif ($audit->auditable_type == "App\ClientPrivateInformation") {
            $find         = ClientPrivateInformation::find($audit->auditable_id);
            $auditaleUser = false;

            if ($find) {
                $auditaleUser = Client::find($find->client_id);

                if ($auditaleUser) {
                    $message = __("Changed client personal informations info by ({$createdUser->first_name} {$createdUser->last_name}) for")." : (".$auditaleUser->first_name . ' ' . $auditaleUser->last_name . ")";
                }
            }
            if (!$find || !$auditaleUser) {
                $message = __("Changed information for a personal informations that no longer exists.");
            }
        } elseif ($audit->auditable_type == "App\ClientEmailProgressReport") {
            $find         = ClientEmailProgressReport::find($audit->auditable_id);
            $auditaleUser = false;

            if ($find) {
                $auditaleUser = Client::find($find->client_id);

                if ($auditaleUser) {
                    $message = __("Changed client email progress reports info by ({$createdUser->first_name} {$createdUser->last_name}) for")." : (".$auditaleUser->first_name . ' ' . $auditaleUser->last_name . ")";
                }
            }
            if (!$find || !$auditaleUser) {
                $message = __("Changed information for a email progress reports that no longer exists.");
            }
        } elseif ($audit->auditable_type == "App\ClientTermsAndCondition") {
            $find         = ClientTermsAndCondition::find($audit->auditable_id);
            $auditaleUser = false;

            if ($find) {
                $auditaleUser = Client::find($find->client_id);

                if ($auditaleUser) {
                    $message = __("Changed client terms and conditions info by ({$createdUser->first_name} {$createdUser->last_name}) for")." : (".$auditaleUser->first_name . ' ' . $auditaleUser->last_name . ")";
                }
            }
            if (!$find || !$auditaleUser) {
                $message = __("Changed information for a terms and conditions that no longer exists.");
            }
        } elseif ($audit->auditable_type == "App\FollowUp") {
            $find         = FollowUp::find($audit->auditable_id);
            $auditaleUser = false;

            if ($find) {
                $auditaleUser = Client::find($find->client_id);

                if ($auditaleUser) {
                    $message = __("Changed client follow ups info by ({$createdUser->first_name} {$createdUser->last_name}) for")." : (".$auditaleUser->first_name . ' ' . $auditaleUser->last_name . ")";
                }
            }
            if (!$find || !$auditaleUser) {
                $message = __("Changed information for a follow ups that no longer exists.");
            }
        } elseif ($audit->auditable_type == "App\Account") {
            $find         = Account::find($audit->auditable_id);
            $auditaleUser = false;

            if ($find) {
                $auditaleUser = Client::find($find->client_id);

                if ($auditaleUser) {
                    $message = __("Changed client accounts info by ({$createdUser->first_name} {$createdUser->last_name}) for")." : (".$auditaleUser->first_name . ' ' . $auditaleUser->last_name . ")";
                }
            }
            if (!$find || !$auditaleUser) {
                $message = __("Changed information for a accounts that no longer exists.");
            }
        }

        return $message;
    }

    /**
     * Get message for deleted event
     *
     * @param object $audit
     * @return string $message
     */
    static private function getMessageForDeleted($audit){
        $message     = "";
        $createdUser = Client::find($audit->user_id);

        if($audit->auditable_type == "App\Client"){
            $message = __("Deleted a client named")." : ".$audit->old_values['name'];
        } elseif($audit->auditable_type == "App\Role"){
            $message = __("Deleted a role named")." ".$audit->old_values['name'];
        } elseif($audit->auditable_type == "App\Permission"){
            $message = __("Deleted a permission named")." ".$audit->old_values['name'];
        } elseif($audit->auditable_type == "App\ClientPurposeArticle") {
            $message = __("Deleted client purpose article by ({$createdUser->first_name} {$createdUser->last_name}) Deleted client purpose article name")." : ({$createdUser->getClientNames($audit->old_values['client_id'])})";
        } elseif($audit->auditable_type == "App\ClientCondition") {
            $message = __("Deleted client condition by ({$createdUser->first_name} {$createdUser->last_name}) for")." : ({$createdUser->getClientNames($audit->old_values['client_id'])})";
        } elseif($audit->auditable_type == "App\ClientPrivateInformation") {
            $message = __("Deleted client private informations by ({$createdUser->first_name} {$createdUser->last_name}) for")." : ({$createdUser->getClientNames($audit->old_values['client_id'])})";
        } elseif($audit->auditable_type == "App\ClientDocument") {
            $message = __("Deleted client documents by ({$createdUser->first_name} {$createdUser->last_name}) for")." : ({$createdUser->getClientNames($audit->old_values['client_id'])})";
        } elseif($audit->auditable_type == "App\ClientEmailProgressReport") {
            $message = __("Deleted client email progress reports by ({$createdUser->first_name} {$createdUser->last_name}) for")." : ({$createdUser->getClientNames($audit->old_values['client_id'])})";
        } elseif($audit->auditable_type == "App\ClientTermsAndCondition") {
            $message = __("Deleted client terms and conditions by ({$createdUser->first_name} {$createdUser->last_name}) for")." : ({$createdUser->getClientNames($audit->old_values['client_id'])})";
        } elseif($audit->auditable_type == "App\FollowUp") {
            $message = __("Deleted client follow ups by ({$createdUser->first_name} {$createdUser->last_name}) for")." : ({$createdUser->getClientNames($audit->old_values['client_id'])})";
        } elseif($audit->auditable_type == "App\Account") {
            $message = __("Deleted client accounts by ({$createdUser->first_name} {$createdUser->last_name}) for")." : ({$createdUser->getClientNames($audit->old_values['client_id'])})";
        }

        return $message;
    }
}
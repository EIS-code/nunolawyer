<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        
        'Illuminate\Auth\Events\Login' => [
            'App\Audit\Listeners\AuditLogin',
        ],

        'Illuminate\Auth\Events\Logout' => [
            'App\Audit\Listeners\AuditLogout',
        ],

        'item.updated' => [
            'App\Events\ItemEvent@itemUpdated',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}

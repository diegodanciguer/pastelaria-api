<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * As classes de eventos mapeadas para seus respectivos listeners.
     *
     * @var array
     */
    protected $listen = [
        // 'App\Events\SomeEvent' => [
        //     'App\Listeners\SomeEventListener',
        // ],
    ];

    /**
     * Registra quaisquer eventos para a aplicação.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Broadcast;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Registra quaisquer serviços na aplicação.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Define os canais de broadcast para a aplicação.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

        require base_path('routes/channels.php');
    }
}

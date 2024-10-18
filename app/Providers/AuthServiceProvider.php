<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * As políticas de autorização mapeadas para o aplicativo.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Registra quaisquer serviços de autenticação / autorização.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}

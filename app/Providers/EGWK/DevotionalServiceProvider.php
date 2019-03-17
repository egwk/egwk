<?php

namespace App\Providers\EGWK;

use Illuminate\Support\ServiceProvider;

class DevotionalServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->mergeConfigFrom(
            config_path('EGWK/devotional_api_help.php'), 'egwk.api_help.entries.devotional'
        );
        $this->mergeConfigFrom(
            config_path('EGWK/devotional_api_help.php'), 'egwk.api_help.entries.devotional'
        );
    }
}

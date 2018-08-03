<?php
/**
 * Created by PhpStorm.
 * User: Peter
 * Date: 30/03/2018
 * Time: 11:48
 */

namespace App\Providers\EGWK;

use Illuminate\Support\ServiceProvider;

class ReaderServiceProvider extends ServiceProvider
    {

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
        {
        }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
        {
        $this->mergeConfigFrom(
            config_path('EGWK/reader_api_help.php'), 'egwk.api_help.entries.reader'
        );
        }

    }

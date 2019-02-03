<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Config::set('install.stopwords', config('stopwords')); // Stopwords merged into the installer config.

        Schema::defaultStringLength(191);

        Blade::directive('echoRefcodeShort', function ($refcodeShort)
        {
            return "<?php echo !empty(trim($refcodeShort)) ? '<small class=\"refcode_short\">{' . $refcodeShort . '}</small>' : ''; ?>";
        });

        Blade::directive('datetimeID', function ($prefix)
        {
            return "<?php echo $prefix . date('-Ymd-Hi'); ?>";
        });

        /**
         * Paginate a standard Laravel Collection.
         *
         * From: https://gist.github.com/simonhamp/549e8821946e2c40a617c85d2cf5af5e
         *
         * @param int $perPage
         * @param int $total
         * @param int $page
         * @param string $pageName
         * @return array
         */
        Collection::macro('paginate', function ($perPage, $total = null, $page = null, $pageName = 'page')
        {

            $page = $page ?: LengthAwarePaginator::resolveCurrentPage($pageName);
            return new LengthAwarePaginator(
                $this->forPage($page, $perPage),
                $total ?: $this->count(),
                $perPage,
                $page,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => $pageName,
                ]
            );
        });

        //
        // Debug queries
        //
        /*
        \DB::listen(function ($sql)
            {
            echo($sql->sql . "<br/>\n");
            print_r($sql->bindings);
            dd($sql);
            });
        */
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            config_path('EGWK/_api_help.php'), 'egwk.api_help'
        );
    }
}

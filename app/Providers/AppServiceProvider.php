<?php



namespace App\Providers;



use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;



class AppServiceProvider extends ServiceProvider

{

    /**

     * Register any application services.

     */

    public function register(): void

    {

        //

    }



    /**

     * Bootstrap any application services.

     */

    public function boot(): void

    {

        Paginator::defaultView('vendor.pagination.mayari');

        try {
            Storage::disk('public')->makeDirectory('images/products');
        } catch (\Throwable $e) {
            //
        }

        if (env('APP_ENV') === 'production') {

            URL::forceScheme('https');

        }

    }

}
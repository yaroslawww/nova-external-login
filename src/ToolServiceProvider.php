<?php

namespace NovaExternalLogin;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Nova;

class ToolServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

        Nova::serving(function (ServingNova $event) {
            Nova::script('nova-external-login', __DIR__ . '/../dist/js/tool.js');
            Nova::style('nova-external-login', __DIR__ . '/../dist/css/tool.css');
        });
    }

    /**
     * Register the tool's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware([ 'nova' ])
             ->prefix('nova-vendor/nova-external-login')
             ->group(function () {
                 Route::post('/{resource}/{resourceId}/create-token', function (NovaRequest $request) {
                     $resource = $request->findResourceOrFail();

                     $tools = array_filter($resource->fields($request), function ($field) use ($request) {
                         return ($field instanceof ExternalLogin) && ($field->uniqueKey() === $request->input('unique_key'));
                     });

                     if (empty($tools)) {
                         throw new \Exception('tool not found');
                     }

                     return array_shift($tools)->loginUser($request);
                 })->middleware('can:viewAny,' . \App\Models\Advertising\AdvStaff::class);
             });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

<?php

namespace Encore\Admin\RBAC;

use Encore\Admin\Form;
use Encore\Admin\RBAC\Http\Controllers\RoleController;
use Encore\Admin\RBAC\Http\Controllers\UserController;
use Encore\Admin\RBAC\Http\Middleware\RouteRBAC;
use Encore\Admin\RBAC\Models\Administrator;
use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Routing\Route;
use Illuminate\Support\ServiceProvider;

class RBACServiceProvider extends ServiceProvider
{
    public function register()
    {
        Route::macro('rbac', function () {

            if (func_num_args() == 1) {
                $module = func_get_args()[0];
                RBAC::$modules[$module] = $this;
            } elseif (func_num_args() == 2) {
                list($module, $sub) = func_get_args();
                RBAC::$modules[$module][] = [
                    'name' => $sub,
                    'route' => $this,
                ];
            }

            return $this;
        });

        PendingResourceRegistration::macro('rbac', function ($name) {
            RBAC::$modules[$name] = $this->register();
            return $this;
        });

        // 注册中间件
        app('router')->aliasMiddleware('admin.rbac', RouteRBAC::class);

        // 替换认证模型
        config([
            'auth.providers.admin.model' => Administrator::class,
            'admin.database.users_model' => Administrator::class,
        ]);

        Form::extend('roleRoutes', Fields\Routes::class);
        Form::extend('roleActions', Fields\Actions::class);
    }

    public function boot(RBAC $extension)
    {
        if (!RBAC::boot()) {
            return ;
        }

        $this->registerRoutes();

        if ($this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database');
            $this->publishes([__DIR__.'/../config/' => config_path('admin/')], 'laravel-admin-rbac-config');
        }

        $this->loadViewsFrom($extension->views(), 'laravel-admin-rbac');
    }

    protected function registerRoutes()
    {
        RBAC::routes(function ($router) {
            // override `users` routes.
            $router->resource('auth/users', UserController::class)->names('admin.auth.users');
            $router->resource('auth/roles', RoleController::class)->names('admin.auth.roles');
        });
    }
}

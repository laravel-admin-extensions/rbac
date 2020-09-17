<?php

namespace Encore\Admin\RBAC\Http\Middleware;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Http\Middleware\Pjax;
use Encore\Admin\Layout\Content;
use Illuminate\Http\Request;

class RouteRBAC
{
    /**
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, \Closure $next)
    {
        if ((!$user = Admin::user()) || $this->shouldPassThrough($request)) {
            return $next($request);
        }

        if ($user->canAccessRoute($request->route())) {
            return $next($request);
        }

        if (!$request->pjax() && $request->ajax()) {
            abort(403, trans('admin.deny'));
            exit;
        }

        Pjax::respond(response(new Content(function (Content $content) {
            $content->title(trans('admin.deny'))->view('admin::pages.deny');
        })));
    }

    /**
     * Determine if the request has a URI that should pass through verification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function shouldPassThrough($request)
    {
        $excepts = array_merge(config('admin.auth.excepts', []), [
            'auth/login',
            'auth/logout',
            'auth/setting',
//            '_handle_action_',
//            '_handle_form_',
            '_handle_selectable_',
            '_handle_renderable_',
            '_require_config.js',
        ]);

        return collect($excepts)
            ->map('admin_base_path')
            ->contains(function ($except) use ($request) {
                if ($except !== '/') {
                    $except = trim($except, '/');
                }

                return $request->is($except);
            });
    }
}

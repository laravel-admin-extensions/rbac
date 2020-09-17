<?php

namespace Encore\Admin\RBAC\Fields;

use Encore\Admin\Form\Field;
use Encore\Admin\RBAC\RBAC;
use Illuminate\Routing\Route;
use Illuminate\Routing\RouteCollection;
use Illuminate\Support\Str;

class Routes extends Field
{
    protected $view = 'laravel-admin-rbac::routes';

    public function prepare($value)
    {
        return array_filter($value);
    }

    public function render()
    {
        $resourceMap = [
            'index'   => __('admin.index'),
            'create'  => __('admin.create'),
            'store'   => __('admin.store'),
            'show'    => __('admin.show'),
            'edit'    => __('admin.edit'),
            'update'  => __('admin.update'),
            'destroy' => __('admin.delete'),
        ];

        $modules = [];

        foreach (RBAC::$modules as $name => $module) {
            if (is_array($module)) {
                $tmp = [];

                foreach ($module as $route) {
                    $tmp[] = [
                        'label' => $route['name'],
                        'value' => sprintf('%s:%s', implode('|', $route['route']->methods()), $route['route']->uri())
                    ];
                }

                $modules[$name] = $tmp;
            } elseif ($module instanceof RouteCollection) {

                $tmp = [];
                foreach ($module as $route) {
                    foreach ($resourceMap as $action => $sub) {
                        if (Str::endsWith($route->getName(), ".$action")) {
                            $label = "{$sub}";
                            break;
                        }
                    }

                    $tmp[] = [
                        'label' => $label,
                        'value' => sprintf('%s:%s', implode('|', $route->methods()), $route->uri())
                    ];
                }

                $modules[$name] = $tmp;
            } elseif ($module instanceof Route) {
                $modules[$name] = sprintf('%s:%s', implode('|', $module->methods()), $module->uri());
            }
        }

        $this->addVariables(compact('modules'));

        return parent::render();
    }
}

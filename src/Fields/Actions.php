<?php

namespace Encore\Admin\RBAC\Fields;

use Encore\Admin\Form\Field;

class Actions extends Field
{
    protected $view = 'laravel-admin-rbac::actions';

    public function prepare($value)
    {
        return array_filter($value);
    }

    public function render()
    {
        $config = config('admin.rbac.actions', []);

        $actions = [];

        foreach ($config as $module => $item) {
            if (is_string($item)) {
                $actions[$module] = $item;
            } elseif (is_array($item)) {
                foreach ($item as $name => $sub) {
                    $actions[$module][] = [
                        'label' => $name,
                        'value' => $sub,
                    ];
                }
            }
        }

        $this->addVariables(compact('actions'));

        return parent::render();
    }
}

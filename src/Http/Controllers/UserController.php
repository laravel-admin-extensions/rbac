<?php

namespace Encore\Admin\RBAC\Http\Controllers;

use Encore\Admin\Http\Controllers\UserController as Controller;
use Encore\Admin\RBAC\Models\Role;

class UserController extends Controller
{
    public function table()
    {
        $table = parent::table();

        $table->column('roles', __('admin.roles'))->pluck('name')->label()->insertAfter('name');

        return $table;
    }

    public function form()
    {
        $form = parent::form();

        $form->multipleSelect('roles', trans('admin.roles'))->options(Role::all()->pluck('name', 'id'));

        return $form;
    }
}

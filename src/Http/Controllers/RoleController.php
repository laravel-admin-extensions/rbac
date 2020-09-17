<?php

namespace Encore\Admin\RBAC\Http\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Http\Controllers\AdminController;
use Encore\Admin\Models\Menu;
use Encore\Admin\RBAC\Models\Role;
use Encore\Admin\Table\Displayers\Actions;
use Encore\Admin\TreeTable;
use Illuminate\Support\Arr;

class RoleController extends AdminController
{
    public function title()
    {
        return admin_trans('admin.roles');
    }

    public function table()
    {
        $table = new TreeTable(new Role());

        $table->column('id', 'ID')->sortable();
        $table->column('name', trans('admin.name'));
        $table->column('slug', trans('admin.slug'));

        $table->column('created_at', trans('admin.created_at'));
        $table->column('updated_at', trans('admin.updated_at'));

//        $table->modalForm();

        $table->actions(function (Actions $actions) {
            if ($actions->row->slug == 'administrator') {
                $actions->disableDelete();
            }

            $actions->disableView();
        });

        $table->disableBatchActions();

        return $table;
    }

    public function form()
    {
        $form = new Form(new Role());

        $form->text('name', trans('admin.name'));

        $form->text('slug', trans('admin.slug'))->with(function ($value, $field) {
            if ($value == 'administrator') {
                $field->readonly();
            }
        });

        // In edit page
        $form->editing(function (Form $form) {
            if ($form->model()->slug != 'administrator') {
                $form->select('parent_id', trans('admin.parent_id'))->options(Role::selectOptions());

                $model = new Menu();
                $tree = $model->toTree();
                $this->formatRecursive($tree);

                $form->roleRoutes('routes', admin_trans('admin.accessible_routes'));
                $form->roleActions('actions', admin_trans('admin.accessible_actions'));
                $form->checktree('menus', admin_trans('admin.visible_menu'))->closeDepth(2)->options($tree);
            }

        });

        // In create page
        $form->creating(function (Form $form) {
            $form->select('parent_id', trans('admin.parent_id'))->options(Role::selectOptions());

            $model = new Menu();
            $tree = $model->toTree();
            $this->formatRecursive($tree);

            $form->roleRoutes('routes', admin_trans('admin.accessible_routes'));
            $form->roleActions('actions', admin_trans('admin.accessible_actions'));
            $form->checktree('menus', admin_trans('admin.visible_menu'))->closeDepth(2)->options($tree);
        });

        return $form;
    }

    protected function formatRecursive(&$tree)
    {
        foreach ($tree as &$item) {
            if (is_array($item) && isset($item['title'])) {
                $item['text'] = $item['title'];
                Arr::forget($item, [
                    'parent_id', 'order', 'title', 'icon', 'uri', 'permission', 'created_at', 'updated_at', 'ROOT'
                ]);
                if (isset($item['children'])) {
                    $this->formatRecursive($item['children']);
                }
            }
        }
    }
}

<?php

namespace Encore\Admin\RBAC;

use Encore\Admin\Extension;
use Encore\Admin\Models\Menu;
use Encore\Admin\RBAC\Models\Administrator;
use Encore\Admin\RBAC\Models\Role;

class RBAC extends Extension
{
    /**
     * @var string
     */
    public $name = 'rbac';

    /**
     * @var string
     */
    public $views = __DIR__ . '/../views';

    /**
     * @var string
     */
    public $assets = __DIR__ . '/../resources/assets';

    /**
     * @var array
     */
    public static $modules = [];

    /**
     * {@inheritdoc}
     */
    public static function import()
    {
        // 创建菜单项
        $lastOrder = Menu::query()->max('order');

        $root = [
            'parent_id' => 0,
            'order'     => $lastOrder++,
            'title'     => 'Roles',
            'icon'      => 'fas fa-user',
            'uri'       => 'auth/roles',
        ];

        Menu::query()->create($root);

        // 如果不存在`超管角色`，创建一个
        if (!Role::query()->where('slug', 'administrator')->exists()) {
            Role::unguard();
            $role = Role::query()->create([
                'name'      => 'Administrator',
                'slug'      => 'administrator',
                'parent_id' => 0,
                'routes'    => '',
                'actions'   => '',
                'menus'     => '',
            ]);

            // 给所有用户设置`超管`角色
            Administrator::all()->each(function($user) use ($role) {
                $user->roles()->save($role);
            });
        }
    }
}

<?php

namespace Encore\Admin\RBAC\Models;

use Encore\Admin\Models\Administrator as BaseModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Routing\Route;

class Administrator extends BaseModel
{
    /**
     * @var Collection
     */
    protected $allRoles;

    /**
     * @var array
     */
    protected $visibleMenu = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles()
    {
        $table = config('admin.rbac.role_users_table') ?: 'admin_role_users';

        return $this->belongsToMany(Role::class, $table, 'user_id', 'role_id');
    }

    /**
     * @return bool
     */
    public function isAdministrator()
    {
        return $this->allRoles()->where('slug', 'administrator')->isNotEmpty();
    }

    /**
     * @return Collection|\Illuminate\Support\Collection
     */
    protected function allRoles()
    {
        if ($this->allRoles) {
            return $this->allRoles;
        }

        $this->allRoles = collect();

        /** @var Role $role */
        foreach ($this->roles as $role) {
            $this->allRoles = $this->allRoles->merge($role->parents())->push($role);
        }

        return $this->allRoles;
    }

    /**
     * @return array|\Illuminate\Support\Collection
     */
    protected function getVisibleMenu()
    {
        if (!empty($this->visibleMenu)) {
            return $this->visibleMenu;
        }

        return $this->visibleMenu = $this->allRoles()->pluck('menus')->flatten();
    }

    /**
     * @param integer $menu menu id
     * @return bool
     */
    public function canSeeMenu($menu)
    {
        if ($this->isAdministrator()) {
            return true;
        }

        return $this->getVisibleMenu()->contains($menu);
    }

    /**
     * @param Route $route
     *
     * @return bool
     */
    public function canAccessRoute(Route $route)
    {
        if ($this->isAdministrator()) {
            return true;
        }

        if (in_array($route->getName(), ['admin.handle-form', 'admin.handle-action'])) {
            if ($action = request('_action')) {
                $action = str_replace('_', '\\', $action);
                return $this->allRoles()->pluck('actions')->flatten()->contains($action);
            }

            if ($form = request('_form_')) {
                return $this->allRoles()->pluck('actions')->flatten()->contains($form);
            }
        }

        return $this->allRoles()->pluck('routes')->flatten()->contains(
            sprintf('%s:%s', implode('|', $route->methods()), $route->uri())
        );
    }
}

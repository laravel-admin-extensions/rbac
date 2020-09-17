<?php

namespace Encore\Admin\RBAC\Models;

use Encore\Admin\Traits\DefaultDatetimeFormat;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use DefaultDatetimeFormat, ModelTree;

    protected $casts = [
        'routes'  => 'array',
        'actions' => 'array',
        'menus'   => 'array',
    ];

    /**
     * @var string
     */
    protected $titleColumn = 'name';

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        $this->setConnection($connection);

        $this->setTable(config('admin.rbac.roles_table') ?: 'admin_roles');

        parent::__construct($attributes);
    }
}

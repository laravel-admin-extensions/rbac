RBAC extension for laravel-admin 2.x
=

## Installation

```shell script
composer require laravel-admin-ext/rbac -vvv
```

发布资源：

```shell script
php artisan vendor:publish --provider="Encore\Admin\RBAC\RBACServiceProvider"
```

运行迁移

```php
php artisan migrate
```

通过下面的命令，创建一个菜单项，新建一个默认的`超级管理员`角色(administrator), 最后把所有的用户设置为`超级管理员`

```php
php artisan admin:import rbac
```

到这里结束安装，打开`http://localhost/admin/auth/roles`管理角色，在用户模块`http://localhost/admin/auth/users`可以给用户添加角色。

## Usage

> `超级管理员`角色拥有所有路由和action的访问权限，并且所有的菜单对其可见。

用户需先关联`角色`，然后给角色设置`可访问路由`、`可访问操作`以及`可见菜单`来实现给予角色访问控制。

角色之间可以继承，继承之后将自动拥有父级角色的访问权限。

![QQ20200917-160148](https://user-images.githubusercontent.com/1479100/93437902-1e7bcc00-f8ff-11ea-9df4-1073b4713ceb.png)

如上图所示，在设置角色权限的时候，会自动加载所有注册的`后台路由`，`action`以及`菜单项`，在这之前需要先进行下面的设置：

### 设置路由名称

在`app/Admin/routes.php`中，给路由设置名称：

```php

// resource资源路由，将自动生成`列表`、`创建`、`编辑`、`更新`等6个路由权限
$router->resource('posts', PostController::class)->rbac('文章管理');

// 将会生成`仪表盘`路由权限
$router->get('dashboard', 'DashboardController@index')->rbac('仪表盘');

// 如果希望多个路由在一个分组下面，可以使用下面的方法
$router->get('system/setting', 'SystemController@index')->rbac('系统', '设置');
$router->post('system/email', 'SystemController@index')->rbac('系统', '发送邮件');
```

上面的配置将会生成下面的`路由访问`配置

![QQ20200917-154747](https://user-images.githubusercontent.com/1479100/93436524-7c0f1900-f8fd-11ea-9170-0d2b02f88ae0.png)

### Action访问控制

如果你使用了laravel-admin的action，并希望进行访问控制，需要先在`config/admin/rbac.php`中进行配置：


```php
<?php

return [
    'actions' => [
        '上传文件' => \App\Admin\Actions\UploadFile::class,
        '全局' => [
            '批量复制' => \App\Admin\Actions\BatchReplicate::class,
            '清理缓存' => \App\Admin\Actions\ClearCache::class,
            '复制'   => \App\Admin\Actions\Replicate::class,
        ],
        '文档'   => [
            '克隆'   => \App\Admin\Actions\Document\CloneDocument::class,
            '批量复制' => \App\Admin\Actions\Document\CopyDocuments::class,
            '导入'   => \App\Admin\Actions\Document\ImportDocument::class,
            '修改权限' => \App\Admin\Actions\Document\ModifyPrivilege::class,
            '分享'   => \App\Admin\Actions\Document\ShareDocument::class,
            '批量分享' => \App\Admin\Actions\Document\ShareDocuments::class,
        ]
    ]
];
```

将会生成如下截图的Action控制项： 

![QQ20200917-155809](https://user-images.githubusercontent.com/1479100/93437697-dbb9f400-f8fe-11ea-882c-133471de5010.png)

## Donate

> Help keeping the project development going, by donating a little. Thanks in advance.

[![PayPal Me](https://img.shields.io/badge/Donate-PayPal-green.svg)](https://www.paypal.me/zousong)

![-1](https://cloud.githubusercontent.com/assets/1479100/23287423/45c68202-fa78-11e6-8125-3e365101a313.jpg)

License
------------
Licensed under [The MIT License (MIT)](LICENSE).



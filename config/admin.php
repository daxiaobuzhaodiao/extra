<?php

return [

    // 站点标题
    'name' => 'Laravel-admin',
    // 站点logo
    'logo' => '<b>Laravel</b> admin',
    // 站点标题缩写
    'logo-mini' => '<b>lu</b>',

    // 路由配置
    'route' => [
        'prefix' => 'admin',    // 路由前缀
        'namespace' => 'App\\Admin\\Controllers',   // 控制器命名空间
        'middleware' => ['web', 'admin'],   // 默认中间件
    ],
    // 安装目录
    'directory' => app_path('Admin'),
    // 页面标题
    'title' => 'Admin',
    // 是否使用https
    'https' => env('ADMIN_HTTPS', false),

    // 用户认证设置
    'auth' => [

        'controller' => App\Admin\Controllers\AuthController::class,

        'guards' => [
            'admin' => [
                'driver'   => 'session',
                'provider' => 'admin',
            ],
        ],

        'providers' => [
            'admin' => [
                'driver' => 'eloquent',
                'model'  => Encore\Admin\Auth\Database\Administrator::class,
            ],
        ],
    ],
    // 文件上传设置
    'upload' => [

        // 对应 filesystem.php 中的 disks
        'disk' => 'public',

        // Image and file upload path under the disk above.
        'directory' => [
            'image' => 'images',
            'file'  => 'files',
        ],
    ],

    // 数据库设置
    'database' => [

        // 数据库连接名称，留空即可
        'connection' => '',

        // 管理员用户表及模型
        'users_table' => 'admin_users',
        'users_model' => Encore\Admin\Auth\Database\Administrator::class,

        // 角色表及模型
        'roles_table' => 'admin_roles',
        'roles_model' => Encore\Admin\Auth\Database\Role::class,

        // 权限表及模型
        'permissions_table' => 'admin_permissions',
        'permissions_model' => Encore\Admin\Auth\Database\Permission::class,

        // 菜单表及模型
        'menu_table' => 'admin_menu',
        'menu_model' => Encore\Admin\Auth\Database\Menu::class,

        // 多对多关联中间表
        'operation_log_table'    => 'admin_operation_log',
        'user_permissions_table' => 'admin_user_permissions',
        'role_users_table'       => 'admin_role_users',
        'role_permissions_table' => 'admin_role_permissions',
        'role_menu_table'        => 'admin_role_menu',
    ],

    // 操作日志设置
    'operation_log' => [

        'enable' => true,

        /*
         * Only logging allowed methods in the list
         */
        'allowed_methods' => ['GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'CONNECT', 'OPTIONS', 'TRACE', 'PATCH'],

        // 不计入操作日志的路由
        'except' => [
            'admin/auth/logs*',
        ],
    ],

    // 地图组件供应商支持 google 和 tencent
    'map_provider' => 'google',

    /*
    | 页面风格
    | @see https://adminlte.io/docs/2.4/layout
    |
    | Supported:
    |    "skin-blue", "skin-blue-light", "skin-yellow", "skin-yellow-light",
    |    "skin-green", "skin-green-light", "skin-purple", "skin-purple-light",
    |    "skin-red", "skin-red-light", "skin-black", "skin-black-light".
    |
    */
    'skin' => 'skin-blue-light',

    // This value is the layout of admin pages.
    // @see https://adminlte.io/docs/2.4/layout
 
    'layout' => ['sidebar-mini', 'sidebar-collapse'],

    // 登陆页面背景图
    'login_background_image' => '',

    // 显示版本
    'show_version' => true,
    // 显示环境
    'show_environment' => true,
    // 菜单绑定权限
    'menu_bind_permission' => true,
    // 默认启用面包屑
    'enable_default_breadcrumb' => true,
    // 扩展安装目录
    'extension_dir' => app_path('Admin/Extensions'),

    // 扩展设置
    'extensions' => [

    ],
];

<?php

/**
 * Laravel Custom View Generator Configuration
 *
 * @author infinitietech05 <your.email@infinitietechnologies.com>
 * @package infinitietechnologies/custom-view-laravel
 * @created 2025-04-09 06:25:58
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Default View Type
    |--------------------------------------------------------------------------
    |
    | This option controls the default view type that will be used when creating
    | new views. Available options are: 'simple', 'blank', and 'resource'.
    |
    */
    'default_type' => 'simple',

    /*
    |--------------------------------------------------------------------------
    | Default File Permissions
    |--------------------------------------------------------------------------
    |
    | Define the default file permissions for newly created view files.
    | These settings will be used when the --permission flag is used
    | without specific values.
    |
    */
    'default_permissions' => [
        'chmod' => '644',
        'owner' => 'www-data',
        'group' => 'www-data',
    ],

    /*
    |--------------------------------------------------------------------------
    | Template Paths
    |--------------------------------------------------------------------------
    |
    | Define the paths where your custom templates are stored. You can add
    | multiple paths and they will be checked in the order listed here.
    |
    */
    'template_paths' => [
        'custom' => function_exists('resource_path')
            ? resource_path('views/templates')
            : base_path('resources/views/templates'),
        'package' => __DIR__ . '/../resources/templates',
    ],

    /*
    |--------------------------------------------------------------------------
    | View Extensions
    |--------------------------------------------------------------------------
    |
    | The file extensions that will be used for the view files. By default,
    | .blade.php is used, but you can add other extensions if needed.
    |
    */
    'extensions' => [
        '.blade.php',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Sections
    |--------------------------------------------------------------------------
    |
    | Define the default sections that will be included in views when using
    | the --template flag without specifying sections.
    |
    */
    'default_sections' => [
        'content',
        'scripts',
        'styles',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Components
    |--------------------------------------------------------------------------
    |
    | Define the default components that will be included in views when using
    | the --template flag without specifying components.
    |
    */
    'default_components' => [
        'navbar',
        'footer',
    ],

    /*
    |--------------------------------------------------------------------------
    | Template Presets
    |--------------------------------------------------------------------------
    |
    | Define template presets that can be used with the --template flag.
    | Each preset can define its own type, sections, components, and layout.
    |
    */
    'presets' => [
        'admin' => [
            'type' => 'resource',
            'extend' => 'layouts.admin',
            'sections' => ['content', 'sidebar', 'scripts'],
            'components' => ['admin-nav', 'admin-footer'],
        ],
        'blog' => [
            'type' => 'resource',
            'extend' => 'layouts.blog',
            'sections' => ['content', 'meta', 'sidebar'],
            'components' => ['blog-header', 'blog-footer', 'share-buttons'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | View Root Path
    |--------------------------------------------------------------------------
    |
    | This value determines the base path where views will be created.
    | It will use Laravel's resource_path helper if available, otherwise
    | it will fall back to the base_path with resources/views appended.
    |
    */
    'view_path' => function_exists('resource_path')
        ? resource_path('views')
        : base_path('resources/views'),
];
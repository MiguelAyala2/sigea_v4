<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Title
    |--------------------------------------------------------------------------
    |
    | Here you can change the default title of your admin panel.
    |
    | For detailed instructions you can look the title section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'title' => 'SIGEA',
    'title_prefix' => '',
    'title_postfix' => '',

    /*
    |--------------------------------------------------------------------------
    | Favicon
    |--------------------------------------------------------------------------
    |
    | Here you can activate the favicon.
    |
    | For detailed instructions you can look the favicon section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_ico_only' => false,
    'use_full_favicon' => false,

    /*
    |--------------------------------------------------------------------------
    | Google Fonts
    |--------------------------------------------------------------------------
    |
    | Here you can allow or not the use of external google fonts. Disabling the
    | google fonts may be useful if your admin panel internet access is
    | restricted somehow.
    |
    | For detailed instructions you can look the google fonts section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'google_fonts' => [
        'allowed' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Logo
    |--------------------------------------------------------------------------
    |
    | Here you can change the logo of your admin panel.
    |
    | For detailed instructions you can look the logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'logo' => '<b>Sigea</b>S.A.',
    'logo_img' => 'vendor/adminlte/dist/img/logo_redondo.png',
    'logo_img_class' => 'brand-image img-circle elevation-3',
    'logo_img_xl' => null,
    'logo_img_xl_class' => 'brand-image-xs',
    'logo_img_alt' => 'Sistema POS Logo',

    /*
    |--------------------------------------------------------------------------
    | Authentication Logo
    |--------------------------------------------------------------------------
    |
    | Here you can setup an alternative logo to use on your login and register
    | screens. When disabled, the admin panel logo will be used instead.
    |
    | For detailed instructions you can look the auth logo section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'auth_logo' => [
        'enabled' => false,
        'img' => [
            'path' => 'vendor/adminlte/dist/img/logo_redondo.png',
            'alt' => 'Auth Logo',
            'class' => '',
            'width' => 50,
            'height' => 50,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Preloader Animation
    |--------------------------------------------------------------------------
    |
    | Here you can change the preloader animation configuration. Currently, two
    | modes are supported: 'fullscreen' for a fullscreen preloader animation
    | and 'cwrapper' to attach the preloader animation into the content-wrapper
    | element and avoid overlapping it with the sidebars and the top navbar.
    |
    | For detailed instructions you can look the preloader section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

         'preloader' => [
         'enabled' => false,
         'mode' => 'fullscreen',
         'img' => [
             'path' => 'vendor/adminlte/dist/img/logo_redondo.png',
             'alt' => 'AdminLTE Preloader Image',
             'effect' => 'animation__shake',
             'width' => 60,
             'height' => 60,
         ],
     ],

    /*
    |--------------------------------------------------------------------------
    | User Menu
    |--------------------------------------------------------------------------
    |
    | Here you can activate and change the user menu.
    |
    | For detailed instructions you can look the user menu section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'usermenu_enabled' => true,
    'usermenu_header' => false,
    'usermenu_header_class' => 'bg-primary',
    'usermenu_image' => false,
    'usermenu_desc' => false,
    'usermenu_profile_url' => false,

    /*
    |--------------------------------------------------------------------------
    | Layout
    |--------------------------------------------------------------------------
    |
    | Here we change the layout of your admin panel.
    |
    | For detailed instructions you can look the layout section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'layout_topnav' => null,
    'layout_boxed' => null,
    'layout_fixed_sidebar' => null,
    'layout_fixed_navbar' => null,
    'layout_fixed_footer' => null,
    'layout_dark_mode' => null,

    /*
    |--------------------------------------------------------------------------
    | Authentication Views Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the authentication views.
    |
    | For detailed instructions you can look the auth classes section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_auth_card' => 'card-outline card-success',
    'classes_auth_header' => '',
    'classes_auth_body' => '',
    'classes_auth_footer' => '',
    'classes_auth_icon' => '',
    'classes_auth_btn' => 'btn-block btn-outline-success btn-sm',
    //'classes_auth_btn' => 'btn-flat btn-success'

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Classes
    |--------------------------------------------------------------------------
    |
    | Here you can change the look and behavior of the admin panel.
    |
    | For detailed instructions you can look the admin panel classes here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'classes_body' => '',
    'classes_brand' => '',
    'classes_brand_text' => '',
    'classes_content_wrapper' => '',
    'classes_content_header' => '',
    'classes_content' => '',
    'classes_sidebar' => 'sidebar-dark-primary elevation-4',
    'classes_sidebar_nav' => '',
    'classes_topnav' => 'navbar-white navbar-light',
    'classes_topnav_nav' => 'navbar-expand',
    'classes_topnav_container' => 'container',

    /*
    |--------------------------------------------------------------------------
    | Sidebar
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar of the admin panel.
    |
    | For detailed instructions you can look the sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'sidebar_mini' => 'lg',
    'sidebar_collapse' => false,
    'sidebar_collapse_auto_size' => false,
    'sidebar_collapse_remember' => false,
    'sidebar_collapse_remember_no_transition' => true,
    'sidebar_scrollbar_theme' => 'os-theme-light',
    'sidebar_scrollbar_auto_hide' => 'l',
    'sidebar_nav_accordion' => true,
    'sidebar_nav_animation_speed' => 300,

    /*
    |--------------------------------------------------------------------------
    | Control Sidebar (Right Sidebar)
    |--------------------------------------------------------------------------
    |
    | Here we can modify the right sidebar aka control sidebar of the admin panel.
    |
    | For detailed instructions you can look the right sidebar section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Layout-and-Styling-Configuration
    |
    */

    'right_sidebar' => false,
    'right_sidebar_icon' => 'fas fa-cogs',
    'right_sidebar_theme' => 'dark',
    'right_sidebar_slide' => true,
    'right_sidebar_push' => true,
    'right_sidebar_scrollbar_theme' => 'os-theme-light',
    'right_sidebar_scrollbar_auto_hide' => 'l',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    |
    | Here we can modify the url settings of the admin panel.
    |
    | For detailed instructions you can look the urls section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Basic-Configuration
    |
    */

    'use_route_url' => false,
    'dashboard_url' => 'home',
    'logout_url' => 'logout',
    'login_url' => 'login',
    'register_url' => 'register',
    'password_reset_url' => 'password/reset',
    'password_email_url' => 'password/email',
    'profile_url' => false,
    'disable_darkmode_routes' => false,

    /*
    |--------------------------------------------------------------------------
    | Laravel Asset Bundling
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Laravel Asset Bundling option for the admin panel.
    | Currently, the next modes are supported: 'mix', 'vite' and 'vite_js_only'.
    | When using 'vite_js_only', it's expected that your CSS is imported using
    | JavaScript. Typically, in your application's 'resources/js/app.js' file.
    | If you are not using any of these, leave it as 'false'.
    |
    | For detailed instructions you can look the asset bundling section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'laravel_asset_bundling' => false,
    'laravel_css_path' => 'css/app.css',
    'laravel_js_path' => 'js/app.js',

    /*
    |--------------------------------------------------------------------------
    | Menu Items
    |--------------------------------------------------------------------------
    |
    | Here we can modify the sidebar/top navigation of the admin panel.
    |
    | For detailed instructions you can look here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'menu' => [
        // Navbar items:
        [
            'type' => 'navbar-search',
            'text' => 'search',
            'topnav_right' => true,
        ],
        [
            'type' => 'fullscreen-widget',
            'topnav_right' => true,
        ],

        // Sidebar items:
        [
            'type' => 'sidebar-menu-search',
            'text' => 'search',
        ],
        [
            'text' => 'Admin',
            'icon' => 'fas fa-terminal',
            'submenu' => [
                [
                    'text' => 'Usuarios',
                    'route' => 'admin.usuarios.index',
                    'can' => 'SuperAdmin',
                ],
                [
                    'text' => 'Roles',
                    'route' => 'admin.roles.index',
                    'can' => 'SuperAdmin',
                ],
            ],
        ],
        [
            'text' => 'Empresa',
            'icon' => 'fas fa-building',
            'submenu' => [
                ['text' => 'Datos Empresa', 'route' => 'empresa.empresa.index', 'icon' => 'fas fa-info-circle'],
                ['text' => 'Sucursales', 'route' => 'empresa.sucursales.index', 'icon' => 'fas fa-store'],
                ['text' => 'Depósitos', 'route' => 'empresa.depositos.index', 'icon' => 'fas fa-warehouse'],
                ['text' => 'Puntos Expedición', 'route' => 'empresa.puntos-expedicion.index', 'icon' => 'fas fa-cash-register'],
                ['text' => 'Timbrados', 'route' => 'empresa.timbrados.index', 'icon' => 'fas fa-stamp'],
            ],
        ],
        [
            'text' => 'Stocks',
            'icon' => 'fas fa-boxes',
            'can' => 'Stocks Ver',
            'submenu' => [
                [
                    'text' => 'Productos',
                    'route' => 'stock.productos.index',
                    'icon' => 'fas fa-box',
                    'can' => 'Productos Ver',
                ],
                [
                    'text' => 'Categorías',
                    'route' => 'stock.categorias.index',
                    'icon' => 'fas fa-sitemap',
                    'can' => 'Categorias Ver',
                ],
                [
                    'text' => 'Marcas',
                    'route' => 'stock.marcas.index',
                    'icon' => 'fas fa-tag',
                    'can' => 'Marcas Ver',
                ],
                [
                    'text' => 'Unidades de Medida',
                    'route' => 'stock.unidades-medida.index',
                    'icon' => 'fas fa-ruler',
                    'can' => 'Unidades Medida Ver',
                ],
                [
                    'text' => 'Stock General',
                    'route' => 'stock.stock.index',
                    'icon' => 'fas fa-warehouse',
                    'can' => 'Stocks Ver',
                ],
                ['text' => 'Reportes', 'icon' => 'fas fa-chart-bar', 'submenu' => [
                    ['text' => 'Stock Bajo', 'route' => 'stock.reportes.stock-bajo'],
                    ['text' => 'Rotación', 'route' => 'stock.reportes.rotacion'],
                    ['text' => 'Valorizado', 'route' => 'stock.reportes.valorizado'],
                ]],
            ],
        ],
        [
            'text' => 'COMPRAS',
            'icon' => 'fas fa-shopping-cart',
            'can'  => 'compras.ver',
            'submenu' => [
                [
                    'text' => 'Dashboard',
                    'route' => 'compras.dashboard',
                    'icon' => 'fas fa-tachometer-alt',
                    'can' => 'compras.dashboard',
                ],
                [
                    'text' => 'Proveedores',
                    'route' => 'compras.proveedores.index',
                    'icon' => 'fas fa-truck',
                    'can' => 'Proveedores Ver',
                ],
                [
                    'text' => 'Pedidos de Compra',
                    'route' => 'compras.pedidos.index',
                    'icon' => 'fas fa-clipboard-list',
                    'can' => 'Proveedores Ver',
                ],
                [
                    'text' => 'Presupuestos',
                    'route' => 'compras.presupuestos.index',
                    'icon' => 'fas fa-file-invoice-dollar',
                    'can' => 'Proveedores Ver',
                ],
                [
                    'text' => 'Órdenes de Compra',
                    'route' => 'compras.ordenes.index',
                    'icon' => 'fas fa-file-signature',
                    'can' => 'Proveedores Ver',
                ],
                [
                    'text' => 'Compras/Facturas',
                    'route' => 'compras.compras.index',
                    'icon' => 'fas fa-file-invoice',
                    'can' => 'compras.compras.ver',
                ],
                [
                    'text' => 'Notas de Crédito',
                    'route' => 'compras.notas-credito.index',
                    'icon' => 'fas fa-file-circle-minus',
                    'can' => 'Proveedores Ver',
                ],
                [
                    'text' => 'Notas de Débito',
                    'route' => 'compras.notas-debito.index',
                    'icon' => 'fas fa-file-circle-plus',
                    'can' => 'Proveedores Ver',
                ],
                [
                    'text' => 'Remisiones',
                    'route' => 'compras.remisiones.index',
                    'icon' => 'fas fa-truck-loading',
                    'can' => 'Proveedores Ver',
                ],
                [
                    'text' => 'Recepción Mercadería',
                    'route' => 'compras.recepciones.index',
                    'icon' => 'fas fa-box-open',
                    'can' => 'compras.recepciones.ver',
                ],
                [
                    'text' => 'Aprobaciones',
                    'route' => 'compras.aprobaciones.pendientes',
                    'icon' => 'fas fa-check-double',
                    'can' => 'compras.aprobaciones.ver',
                ],
                [
                    'text' => 'Cuentas por Pagar',
                    'route' => 'compras.pagos.index',
                    'icon' => 'fas fa-money-bill-wave',
                    'can' => 'Proveedores Ver',
                ],
                [
                    'text' => 'Reportes',
                    'icon' => 'fas fa-chart-bar',
                    'can' => 'compras.reportes.ver',
                    'submenu' => [
                        [
                            'text' => 'Libro de Compras',
                            'route' => 'compras.reportes.libro-compras',
                            'icon' => 'fas fa-book',
                            'can' => 'compras.reportes.libro',
                        ],
                        [
                            'text' => 'Análisis Proveedores',
                            'route' => 'compras.reportes.analisis-proveedores',
                            'icon' => 'fas fa-chart-line',
                            'can' => 'compras.reportes.analisis',
                        ],
                        [
                            'text' => 'Flujo de Aprobaciones',
                            'route' => 'compras.reportes.flujo-aprobaciones',
                            'icon' => 'fas fa-project-diagram',
                            'can' => 'compras.reportes.flujo',
                        ],
                    ],
                ],
            ],
        ],
        [
            'text' => 'SERVICIOS',
            'icon' => 'fas fa-tools',
            'submenu' => [
                [
                    'text' => 'Clientes',
                    'icon' => 'fas fa-users',
                    'submenu' => [
                        [
                            'text' => 'Registrar Cliente',
                            'route' => 'servicios.clientes.registrar',
                            'icon' => 'fas fa-user-plus',
                        ],
                        [
                            'text' => 'Historial de Servicios',
                            'route' => 'servicios.clientes.historial',
                            'icon' => 'fas fa-history',
                        ],
                    ],
                ],
                [
                    'text' => 'Gestión de Servicios Técnicos',
                    'icon' => 'fas fa-wrench',
                    'submenu' => [
                        [
                            'text' => 'Solicitud de Servicio',
                            'route' => 'servicios.solicitudes.index',
                            'icon' => 'fas fa-clipboard-list',
                        ],
                        [
                            'text' => 'Recepción de Equipo',
                            'route' => 'servicios.recepcion.index',
                            'icon' => 'fas fa-box-open',
                        ],
                        [
                            'text' => 'Diagnóstico Técnico',
                            'route' => 'servicios.diagnostico.index',
                            'icon' => 'fas fa-stethoscope',
                        ],
                        [
                            'text' => 'Presupuesto',
                            'route' => 'servicios.presupuestos.index',
                            'icon' => 'fas fa-file-invoice-dollar',
                        ],
                        [
                            'text' => 'Orden de Servicio',
                            'route' => 'servicios.ordenes.index',
                            'icon' => 'fas fa-file-signature',
                        ],
                        [
                            'text' => 'Entrega / Cierre',
                            'route' => 'servicios.entrega.index',
                            'icon' => 'fas fa-check-circle',
                        ],
                    ],
                ],
                [
                    'text' => 'Promociones y Descuentos',
                    'icon' => 'fas fa-percentage',
                    'submenu' => [
                        [
                            'text' => 'Promociones',
                            'route' => 'servicios.promociones.index',
                            'icon' => 'fas fa-tags',
                        ],
                        [
                            'text' => 'Descuentos',
                            'route' => 'servicios.descuentos.index',
                            'icon' => 'fas fa-percent',
                        ],
                    ],
                ],
                [
                    'text' => 'Reclamos de Clientes',
                    'icon' => 'fas fa-exclamation-triangle',
                    'submenu' => [
                        [
                            'text' => 'Registrar Reclamo',
                            'route' => 'servicios.reclamos.registrar',
                            'icon' => 'fas fa-plus-circle',
                        ],
                        [
                            'text' => 'Seguimiento',
                            'route' => 'servicios.reclamos.seguimiento',
                            'icon' => 'fas fa-search',
                        ],
                    ],
                ],
                [
                    'text' => 'Informes Web',
                    'route' => 'servicios.informes.index',
                    'icon' => 'fas fa-chart-line',
                ],
            ],
        ],
        [
            'text' => 'VENTAS Y COBROS',
            'icon' => 'fas fa-cash-register',
            'submenu' => [
                [
                    'text' => 'Caja',
                    'icon' => 'fas fa-money-bill-wave',
                    'submenu' => [
                        [
                            'text' => 'Apertura de Caja',
                            'route' => 'ventas.caja.apertura',
                            'icon' => 'fas fa-unlock',
                        ],
                        [
                            'text' => 'Movimientos de Caja',
                            'route' => 'ventas.caja.movimientos',
                            'icon' => 'fas fa-exchange-alt',
                        ],
                        [
                            'text' => 'Cierre de Caja',
                            'route' => 'ventas.caja.cierre',
                            'icon' => 'fas fa-lock',
                        ],
                        [
                            'text' => 'Arqueo de Caja',
                            'route' => 'ventas.caja.arqueo',
                            'icon' => 'fas fa-calculator',
                        ],
                        [
                            'text' => 'Recaudaciones a Depositar',
                            'route' => 'ventas.caja.recaudaciones',
                            'icon' => 'fas fa-university',
                        ],
                    ],
                ],
                [
                    'text' => 'Pedidos de Clientes',
                    'icon' => 'fas fa-shopping-cart',
                    'submenu' => [
                        [
                            'text' => 'Registrar Pedido',
                            'route' => 'ventas.pedidos.registrar',
                            'icon' => 'fas fa-plus-circle',
                        ],
                        [
                            'text' => 'Historial de Pedidos',
                            'route' => 'ventas.pedidos.historial',
                            'icon' => 'fas fa-list',
                        ],
                    ],
                ],
                [
                    'text' => 'Ventas y Facturación',
                    'icon' => 'fas fa-file-invoice-dollar',
                    'submenu' => [
                        [
                            'text' => 'Generar Venta / Factura',
                            'route' => 'ventas.facturacion.index',
                            'icon' => 'fas fa-receipt',
                        ],
                        [
                            'text' => 'Cuentas a Cobrar',
                            'route' => 'ventas.cuentas-cobrar.index',
                            'icon' => 'fas fa-hand-holding-usd',
                        ],
                        [
                            'text' => 'Notas de Remisión',
                            'route' => 'ventas.remisiones.index',
                            'icon' => 'fas fa-truck',
                        ],
                        [
                            'text' => 'Notas de Crédito',
                            'route' => 'ventas.notas-credito.index',
                            'icon' => 'fas fa-undo',
                        ],
                        [
                            'text' => 'Notas de Débito',
                            'route' => 'ventas.notas-debito.index',
                            'icon' => 'fas fa-plus-square',
                        ],
                    ],
                ],
                [
                    'text' => 'Cobranzas',
                    'icon' => 'fas fa-coins',
                    'submenu' => [
                        [
                            'text' => 'Registrar Cobranza',
                            'route' => 'ventas.cobranzas.registrar',
                            'icon' => 'fas fa-dollar-sign',
                        ],
                        [
                            'text' => 'Cobranzas por Forma de Pago',
                            'route' => 'ventas.cobranzas.forma-pago',
                            'icon' => 'fas fa-credit-card',
                        ],
                        [
                            'text' => 'Historial de Cobranzas',
                            'route' => 'ventas.cobranzas.historial',
                            'icon' => 'fas fa-history',
                        ],
                    ],
                ],
                [
                    'text' => 'Libro de Ventas',
                    'route' => 'ventas.libro-ventas.index',
                    'icon' => 'fas fa-book',
                ],
                [
                    'text' => 'Informes Web',
                    'route' => 'ventas.informes.index',
                    'icon' => 'fas fa-chart-pie',
                ],
            ],
        ],
        // [
        //     'text' => 'blog',
        //     'url' => 'admin/blog',
        //     'can' => 'manage-blog',
        // ],
        // [
        //     'text' => 'pages',
        //     'url' => 'admin/pages',
        //     'icon' => 'far fa-fw fa-file',
        //     'label' => 4,
        //     'label_color' => 'success',
        // ],
        // ['header' => 'account_settings'],
        // [
        //     'text' => 'profile',
        //     'url' => 'admin/settings',
        //     'icon' => 'fas fa-fw fa-user',
        // ],
        // [
        //     'text' => 'change_password',
        //     'url' => 'admin/settings',
        //     'icon' => 'fas fa-fw fa-lock',
        // ],
        // [
        //     'text' => 'multilevel',
        //     'icon' => 'fas fa-fw fa-share',
        //     'submenu' => [
        //         [
        //             'text' => 'level_one',
        //             'url' => '#',
        //         ],
        //         [
        //             'text' => 'level_one',
        //             'url' => '#',
        //             'submenu' => [
        //                 [
        //                     'text' => 'level_two',
        //                     'url' => '#',
        //                 ],
        //                 [
        //                     'text' => 'level_two',
        //                     'url' => '#',
        //                     'submenu' => [
        //                         [
        //                             'text' => 'level_three',
        //                             'url' => '#',
        //                         ],
        //                         [
        //                             'text' => 'level_three',
        //                             'url' => '#',
        //                         ],
        //                     ],
        //                 ],
        //             ],
        //         ],
        //         [
        //             'text' => 'level_one',
        //             'url' => '#',
        //         ],
        //     ],
        // ],
        ['header' => 'INFO'],
        [
            'text' => 'SOPORTE',
            'icon_color' => 'yellow',
            'url' => '#',
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Filters
    |--------------------------------------------------------------------------
    |
    | Here we can modify the menu filters of the admin panel.
    |
    | For detailed instructions you can look the menu filters section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Menu-Configuration
    |
    */

    'filters' => [
        JeroenNoten\LaravelAdminLte\Menu\Filters\GateFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\HrefFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\SearchFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ActiveFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\ClassesFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\LangFilter::class,
        JeroenNoten\LaravelAdminLte\Menu\Filters\DataFilter::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Plugins Initialization
    |--------------------------------------------------------------------------
    |
    | Here we can modify the plugins used inside the admin panel.
    |
    | For detailed instructions you can look the plugins section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Plugins-Configuration
    |
    */

    'plugins' => [
        'Datatables' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css',
                ],
            ],
        ],
        'Select2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js',
                ],
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.css',
                ],
            ],
        ],
        'Chartjs' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.0/Chart.bundle.min.js',
                ],
            ],
        ],
        'Sweetalert2' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdn.jsdelivr.net/npm/sweetalert2@8',
                ],
            ],
        ],
        'Pace' => [
            'active' => false,
            'files' => [
                [
                    'type' => 'css',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/themes/blue/pace-theme-center-radar.min.css',
                ],
                [
                    'type' => 'js',
                    'asset' => false,
                    'location' => '//cdnjs.cloudflare.com/ajax/libs/pace/1.0.2/pace.min.js',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | IFrame
    |--------------------------------------------------------------------------
    |
    | Here we change the IFrame mode configuration. Note these changes will
    | only apply to the view that extends and enable the IFrame mode.
    |
    | For detailed instructions you can look the iframe mode section here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/IFrame-Mode-Configuration
    |
    */

    'iframe' => [
        'default_tab' => [
            'url' => null,
            'title' => null,
        ],
        'buttons' => [
            'close' => true,
            'close_all' => true,
            'close_all_other' => true,
            'scroll_left' => true,
            'scroll_right' => true,
            'fullscreen' => true,
        ],
        'options' => [
            'loading_screen' => 1000,
            'auto_show_new_tab' => true,
            'use_navbar_items' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    |
    | Here we can enable the Livewire support.
    |
    | For detailed instructions you can look the livewire here:
    | https://github.com/jeroennoten/Laravel-AdminLTE/wiki/Other-Configuration
    |
    */

    'livewire' => false,
];

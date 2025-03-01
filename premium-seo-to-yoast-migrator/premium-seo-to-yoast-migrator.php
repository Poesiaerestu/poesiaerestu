<?php
/**
 * Plugin Name: Premium SEO to Yoast Migrator
 * Description: Migrates SEO data from Premium SEO Pack to Yoast SEO
 * Version: 2.0.1
 * Author: Tu Nombre
 * License: GPLv3
 * Text Domain: premium-seo-migrator
 * Requires PHP: 7.4
 * Requires at least: 6.0
 */

defined('ABSPATH') || exit;

// ==================================================================
// 1. SEGURIDAD Y COMPATIBILIDAD - Corrige errores críticos del log
// ==================================================================

// Verificar versión PHP antes de cualquier operación
if (version_compare(PHP_VERSION, '7.4.0', '<')) {
    add_action('admin_notices', function() {
        echo '<div class="error notice"><p>';
        _e('Este plugin requiere PHP 7.4 o superior. Actualiza tu servidor.', 'premium-seo-migrator');
        echo '</p></div>';
    });
    return;
}

// Definir constantes con validación
if (!defined('PSP2YOAST_VERSION')) {
    define('PSP2YOAST_VERSION', '2.0.1');
    define('PSP2YOAST_PLUGIN_DIR', plugin_dir_path(__FILE__));
    define('PSP2YOAST_PLUGIN_URL', plugin_dir_url(__FILE__));
}

// ==================================================================
// 2. CARGA DE ARCHIVOS CON VALIDACIÓN - Soluciona error fatal
// ==================================================================

$required_files = [
    PSP2YOAST_PLUGIN_DIR . 'includes/class-migration-core.php',
    PSP2YOAST_PLUGIN_DIR . 'includes/class-data-processor.php',
    PSP2YOAST_PLUGIN_DIR . 'includes/class-database-manager.php',
    PSP2YOAST_PLUGIN_DIR . 'includes/class-ajax-handler.php'
];

foreach ($required_files as $file) {
    if (!file_exists($file)) {
        add_action('admin_notices', function() use ($file) {
            echo '<div class="error notice"><p>';
            printf(
                __('Archivo esencial faltante: %s - El plugin no puede funcionar.', 'premium-seo-migrator'),
                basename($file)
            );
            echo '</p></div>';
        });
        return;
    }
    require_once $file;
}

// ==================================================================
// 3. CLASE PRINCIPAL - Corrige errores de traducción temprana
// ==================================================================

class Premium_SEO_Migrator {

    public function __construct() {
        // Registrar hooks después de verificar capacidades
        add_action('init', [$this, 'init_plugin']);
    }

    public function init_plugin() {
        // 3.1 Carga de traducciones en hook init - Soluciona warnings 6.7.0
        load_plugin_textdomain(
            'premium-seo-migrator',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );

        // 3.2 Registrar funcionalidad solo para administradores
        if (current_user_can('manage_options')) {
            add_action('admin_menu', [$this, 'create_admin_menu']);
            add_action('admin_enqueue_scripts', [$this, 'load_admin_assets']);
        }

        // 3.3 Inicializar AJAX
        new PSP2Yoast_Ajax_Handler();
    }

    public function create_admin_menu() {
        add_menu_page(
            __('Migración SEO Premium', 'premium-seo-migrator'),
            __('Migrador SEO', 'premium-seo-migrator'),
            'manage_options',
            'seo-migrator',
            [$this, 'show_admin_interface'],
            'dashicons-database',
            80
        );
    }

    public function show_admin_interface() {
        include PSP2YOAST_PLUGIN_DIR . 'templates/admin-page.php';
    }

    // ==================================================================
    // 4. MANEJO DE ASSETS - Mejora seguridad y rendimiento
    // ==================================================================

    public function load_admin_assets($hook) {
        if ('toplevel_page_seo-migrator' !== $hook) {
            return;
        }

        // 4.1 Estilos CSS
        wp_enqueue_style(
            'psp2yoast-admin-css',
            PSP2YOAST_PLUGIN_URL . 'assets/css/admin.css',
            [],
            PSP2YOAST_VERSION
        );

        // 4.2 Scripts JS con localización segura
        wp_enqueue_script(
            'psp2yoast-admin-js',
            PSP2YOAST_PLUGIN_URL . 'assets/js/admin.js',
            ['jquery', 'wp-util'],
            PSP2YOAST_VERSION,
            true
        );

        wp_localize_script('psp2yoast-admin-js', 'psp2yoast_data', [
            'ajax_url'   => admin_url('admin-ajax.php'),
            'nonce'      => wp_create_nonce('psp2yoast_migration_nonce'),
            'i18n'       => [
                'confirm_start' => __('¿Estás seguro de iniciar la migración?', 'premium-seo-migrator'),
                'processing'    => __('Migrando datos...', 'premium-seo-migrator'),
                'completed'     => __('Migración completada con éxito', 'premium-seo-migrator'),
                'error'         => __('Error durante la migración:', 'premium-seo-migrator')
            ]
        ]);
    }
}

// ==================================================================
// 5. INICIALIZACIÓN SEGURA - Previene conflictos
// ==================================================================

add_action('plugins_loaded', function() {
    if (class_exists('Premium_SEO_Migrator') && !isset($GLOBALS['premium_seo_migrator'])) {
        $GLOBALS['premium_seo_migrator'] = new Premium_SEO_Migrator();
    }
});

// ==================================================================
// 6. ACTIVACIÓN SEGURA - Verifica dependencias
// ==================================================================

register_activation_hook(__FILE__, function() {
    if (!is_plugin_active('wordpress-seo/wp-seo.php')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(
            __('Este plugin requiere Yoast SEO instalado y activado.', 'premium-seo-migrator'),
            __('Dependencia faltante', 'premium-seo-migrator'),
            ['back_link' => true]
        );
    }
});

<?php
/**
 * Plugin Name: Premium SEO to Yoast Migrator
 * Description: Migrates SEO data from Premium SEO Pack to Yoast SEO
 * Version: 1.0.2
 * Author: Tu Nombre
 * License: GPLv3
 * Text Domain: premium-seo-migrator
 */

defined('ABSPATH') || exit;

// Definir constantes
define('PSP2YOAST_VERSION', '1.0.2');
define('PSP2YOAST_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PSP2YOAST_PLUGIN_URL', plugin_dir_url(__FILE__));

// Cargar dependencias
require_once PSP2YOAST_PLUGIN_DIR . 'includes/class-migration-core.php';
require_once PSP2YOAST_PLUGIN_DIR . 'includes/class-data-processor.php';
require_once PSP2YOAST_PLUGIN_DIR . 'includes/class-database-manager.php';
require_once PSP2YOAST_PLUGIN_DIR . 'includes/class-ajax-handler.php';

class Premium_SEO_Migrator {

    public function __construct() {
        register_activation_hook(__FILE__, [$this, 'activate']);
        add_action('admin_menu', [$this, 'add_admin_page']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }

    public function activate() {
        if (version_compare(PHP_VERSION, '7.4.0', '<')) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('Este plugin requiere PHP 7.4 o superior.', 'premium-seo-migrator'));
        }
    }

    public function add_admin_page() {
        add_menu_page(
            __('Migrador SEO', 'premium-seo-migrator'),
            __('SEO Migrator', 'premium-seo-migrator'),
            'manage_options',
            'seo-migrator',
            [$this, 'render_admin_page'],
            'dashicons-database'
        );
    }

    public function render_admin_page() {
        include PSP2YOAST_PLUGIN_DIR . 'templates/admin-page.php';
    }

    public function enqueue_assets($hook) {
        if ('toplevel_page_seo-migrator' === $hook) {
            wp_enqueue_style(
                'psp2yoast-admin-css',
                PSP2YOAST_PLUGIN_URL . 'assets/css/admin.css'
            );
            
            wp_enqueue_script(
                'psp2yoast-admin-js',
                PSP2YOAST_PLUGIN_URL . 'assets/js/admin.js',
                ['jquery'],
                PSP2YOAST_VERSION,
                true
            );
            
            wp_localize_script('psp2yoast-admin-js', 'psp2yoast_vars', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce'    => wp_create_nonce('psp2yoast_nonce')
            ]);
        }
    }
}

new Premium_SEO_Migrator();
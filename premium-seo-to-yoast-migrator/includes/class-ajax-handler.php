<?php
/**
 * Maneja las solicitudes AJAX para la migración
 */
class PSP2Yoast_Ajax_Handler {

    public function __construct() {
        add_action('wp_ajax_psp2yoast_start_migration', [$this, 'handle_migration']);
        add_action('wp_ajax_psp2yoast_get_progress', [$this, 'get_progress']);
    }

    /**
     * Inicia el proceso de migración
     */
    public function handle_migration() {
        check_ajax_referer('psp2yoast_nonce', 'security');

        try {
            $db_manager = new PSP2Yoast_Database_Manager();
            $result = $db_manager->full_migration();

            wp_send_json_success([
                'message' => __('Migración completada con éxito', 'premium-seo-migrator'),
                'stats' => $result
            ]);

        } catch (Exception $e) {
            wp_send_json_error([
                'message' => __('Error durante la migración: ', 'premium-seo-migrator') . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtiene el progreso actual
     */
    public function get_progress() {
        check_ajax_referer('psp2yoast_nonce', 'security');

        global $wpdb;
        $total = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key LIKE '_psp_%'"
        );

        $processed = $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->prefix}psp_migration_backup WHERE migrated = 1"
        );

        wp_send_json_success([
            'total' => $total,
            'processed' => $processed,
            'percentage' => $total > 0 ? round(($processed / $total) * 100) : 0
        ]);
    }
}

new PSP2Yoast_Ajax_Handler();

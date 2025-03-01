<?php
/**
 * Manejador AJAX para el proceso de migración
 * Versión: 2.1.0
 */

class PSP2Yoast_Ajax_Handler {

    private $nonce_action = 'psp2yoast_migration_nonce';
    private $capability = 'manage_options';

    public function __construct() {
        add_action('wp_ajax_psp2yoast_start_migration', [$this, 'handle_migration_request']);
        add_action('wp_ajax_psp2yoast_get_progress', [$this, 'get_migration_progress']);
    }

    /**
     * Maneja la solicitud de inicio de migración
     */
    public function handle_migration_request() {
        try {
            $this->validate_request();
            
            $db_manager = new PSP2Yoast_Database_Manager();
            $result = $db_manager->full_migration();

            wp_send_json_success([
                'message' => $this->get_success_message($result),
                'stats' => $result,
                'next_step' => $this->get_next_steps()
            ]);

        } catch (Exception $e) {
            $this->log_error($e);
            wp_send_json_error([
                'message' => $this->get_error_message($e),
                'error_code' => $e->getCode()
            ]);
        }
    }

    /**
     * Obtiene el progreso actual de la migración
     */
    public function get_migration_progress() {
        try {
            $this->validate_request();
            
            global $wpdb;
            $progress = [
                'total' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key LIKE '_psp_%'"),
                'processed' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}psp_migration_backup WHERE migrated = 1"),
                'errors' => $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}psp_migration_errors")
            ];

            wp_send_json_success($progress);

        } catch (Exception $e) {
            $this->log_error($e);
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    /**
     * Valida la solicitud AJAX
     */
    private function validate_request() {
        // Verificar nonce
        if (!isset($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], $this->nonce_action)) {
            throw new Exception(__('Nonce de seguridad inválido', 'premium-seo-migrator'), 401);
        }

        // Verificar capacidades del usuario
        if (!current_user_can($this->capability)) {
            throw new Exception(__('Acceso no autorizado', 'premium-seo-migrator'), 403);
        }
    }

    /**
     * Genera mensajes de éxito contextuales
     */
    private function get_success_message($result) {
        $messages = [
            'complete' => __('Migración completada exitosamente', 'premium-seo-migrator'),
            'partial' => __('Migración parcialmente completada', 'premium-seo-migrator'),
            'warning' => __('Migración completada con advertencias', 'premium-seo-migrator')
        ];

        $status = ($result['errors'] > 0) 
            ? (($result['processed'] > 0) ? 'warning' : 'partial') 
            : 'complete';

        return $messages[$status] . " - " . sprintf(
            __('Elementos procesados: %d, Errores: %d', 'premium-seo-migrator'),
            $result['processed'],
            $result['errors']
        );
    }

    /**
     * Genera mensajes de error detallados
     */
    private function get_error_message($e) {
        $messages = [
            401 => __('Autenticación fallida', 'premium-seo-migrator'),
            403 => __('Permisos insuficientes', 'premium-seo-migrator'),
            500 => __('Error interno del servidor', 'premium-seo-migrator'),
            'default' => __('Error durante la migración: ', 'premium-seo-migrator') . $e->getMessage()
        ];

        return $messages[$e->getCode()] ?? $messages['default'];
    }

    /**
     * Registra errores en el log
     */
    private function log_error($e) {
        error_log(sprintf(
            '[PSP2Yoast Error] %s - Código: %d - Trace: %s',
            $e->getMessage(),
            $e->getCode(),
            $e->getTraceAsString()
        ));
    }

    /**
     * Sugiere próximos pasos después de la migración
     */
    private function get_next_steps() {
        return [
            [
                'title' => __('Verificar resultados', 'premium-seo-migrator'),
                'url' => admin_url('admin.php?page=seo-migrator'),
                'type' => 'primary'
            ],
            [
                'title' => __('Generar informe', 'premium-seo-migrator'),
                'url' => '#',
                'type' => 'secondary'
            ],
            [
                'title' => __('Limpiar datos antiguos', 'premium-seo-migrator'),
                'url' => wp_nonce_url(admin_url('admin-post.php?action=psp2yoast_cleanup'), 'cleanup_nonce'),
                'type' => 'warning'
            ]
        ];
    }
}

// Inicialización segura
if (defined('DOING_AJAX') && DOING_AJAX) {
    new PSP2Yoast_Ajax_Handler();
}

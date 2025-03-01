<?php
class PSP2Yoast_Database_Manager {

    private $wpdb;
    private $batch_size = 50;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function full_migration() {
        $this->create_backup_table();
        $results = [];

        try {
            $this->start_transaction();
            $results['posts'] = $this->migrate_post_meta();
            $this->commit_transaction();
            return $results;
        } catch (Exception $e) {
            $this->rollback_transaction();
            error_log("[DB Error] " . $e->getMessage());
            return false;
        }
    }

    private function migrate_post_meta() {
        $total = $this->get_total_posts();
        $migrated = 0;

        for ($page = 0; $page <= ceil($total / $this->batch_size); $page++) {
            $posts = get_posts([
                'post_type' => 'any',
                'posts_per_page' => $this->batch_size,
                'offset' => $page * $this->batch_size
            ]);

            foreach ($posts as $post) {
                $this->migrate_single_post($post->ID);
                $migrated++;
            }
        }

        return $migrated;
    }

    private function migrate_single_post($post_id) {
        $migration_core = new PSP2Yoast_Migration_Core();
        return $migration_core->migrate_post_meta($post_id);
    }

    private function create_backup_table() {
        $charset = $this->wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}psp_backups (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            post_id bigint(20) NOT NULL,
            meta_key varchar(255) NOT NULL,
            meta_value longtext NOT NULL,
            migration_date datetime NOT NULL,
            PRIMARY KEY (id)
        ) $charset;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }

    private function start_transaction() {
        $this->wpdb->query('START TRANSACTION');
    }

    private function commit_transaction() {
        $this->wpdb->query('COMMIT');
    }

    private function rollback_transaction() {
        $this->wpdb->query('ROLLBACK');
    }

    private function get_total_posts() {
        return $this->wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->wpdb->posts} WHERE post_status = 'publish'"
        );
    }
}

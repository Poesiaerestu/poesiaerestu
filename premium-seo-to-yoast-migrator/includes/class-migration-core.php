<?php
class PSP2Yoast_Migration_Core {

    private $field_map = [
        '_psp_title' => '_yoast_wpseo_title',
        '_psp_meta_description' => '_yoast_wpseo_metadesc',
        '_psp_focus_keyword' => '_yoast_wpseo_focuskw'
    ];

    public function migrate_post($post_id) {
        if (!$this->is_valid_post($post_id)) return false;
        
        foreach ($this->field_map as $source => $target) {
            $value = get_post_meta($post_id, $source, true);
            if (!empty($value)) {
                $this->update_meta_safe($post_id, $target, $value);
                delete_post_meta($post_id, $source);
            }
        }
        return true;
    }

    private function is_valid_post($post_id) {
        return is_numeric($post_id) && get_post_status($post_id);
    }

    private function update_meta_safe($post_id, $key, $value) {
        if (!update_post_meta($post_id, $key, sanitize_text_field($value))) {
            error_log("Error actualizando $key para post $post_id");
        }
    }
}

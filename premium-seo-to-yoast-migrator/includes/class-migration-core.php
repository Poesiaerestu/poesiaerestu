<?php
class PSP2Yoast_Migration_Core {
    private $field_map = [
        '_psp_title' => '_yoast_wpseo_title',
        '_psp_meta_description' => '_yoast_wpseo_metadesc'
    ];

    public function migrate_post_meta($post_id) {
        foreach ($this->field_map as $source => $target) {
            $value = get_post_meta($post_id, $source, true);
            if (!empty($value)) {
                update_post_meta($post_id, $target, $value);
                delete_post_meta($post_id, $source);
            }
        }
        return true;
    }
}

<?php
if (!defined('WP_UNINSTALL_PLUGIN')) exit;

// Eliminar opciones
delete_option('psp2yoast_migration_status');

// Limpiar metadatos
$posts = get_posts([
    'numberposts' => -1,
    'post_type'   => 'any',
    'post_status' => 'any'
]);

foreach ($posts as $post) {
    delete_post_meta($post->ID, '_yoast_wpseo_title');
    delete_post_meta($post->ID, '_yoast_wpseo_metadesc');
    delete_post_meta($post->ID, '_yoast_wpseo_focuskw');
}
<?php
class PSP2Yoast_Data_Processor {

    public function process_psp_meta($psp_data) {
        $valid_data = $this->validate_input($psp_data);
        return $this->transform_to_yoast_format($valid_data);
    }

    private function validate_input($data) {
        if (empty($data['description'])) {
            throw new InvalidArgumentException("Falta descripción SEO");
        }
        return $data;
    }

    private function transform_to_yoast_format($data) {
        return [
            '_yoast_wpseo_title' => substr($data['title'] ?? '', 0, 60),
            '_yoast_wpseo_metadesc' => substr($data['description'], 0, 160),
            '_yoast_wpseo_focuskw' => $this->process_keywords($data['focus_keyword'] ?? ''),
            '_yoast_wpseo_opengraph-title' => $data['og_title'] ?? '',
            '_yoast_wpseo_opengraph-description' => $data['og_description'] ?? ''
        ];
    }

    private function process_keywords($keywords) {
        return implode(', ', array_map('sanitize_key', explode(',', $keywords)));
    }
}

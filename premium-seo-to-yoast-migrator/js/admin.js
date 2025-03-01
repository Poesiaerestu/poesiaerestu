jQuery(document).ready(function($) {
    $('#start-migration').on('click', function(e) {
        e.preventDefault();
        
        let $button = $(this);
        $button.prop('disabled', true).text('Migrando...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'psp2yoast_start_migration',
                nonce: psp2yoast_vars.nonce
            },
            success: function(response) {
                if (response.success) {
                    $('#migration-results').html(response.data.message);
                } else {
                    alert('Error: ' + response.data);
                }
                $button.prop('disabled', false).text('Iniciar Migración');
            }
        });
    });
});
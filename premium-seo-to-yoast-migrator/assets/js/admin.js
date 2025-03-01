jQuery(document).ready(function($) {
    const updateProgress = (percentage) => {
        $('#migration-progress-bar').css('width', percentage + '%');
    };

    $('#start-migration').on('click', function(e) {
        e.preventDefault();
        const $button = $(this).prop('disabled', true);
        
        $.ajax({
            url: psp2yoast_vars.ajax_url,
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
                $button.prop('disabled', false);
            }
        });
    });
});
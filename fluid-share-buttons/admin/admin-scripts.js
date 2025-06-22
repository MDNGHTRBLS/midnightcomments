jQuery(document).ready(function($) {
    // Color Picker
    $('.fsb-color-picker').wpColorPicker();

    // Visibility Rules
    function toggleVisibilitySettings() {
        const rule = $('#fsb_visibility_rule').val();
        $('.fsb-rule-setting').hide();
        if (rule === 'scroll') {
            $('#fsb-scroll-setting').show();
        } else if (rule === 'word_count') {
            $('#fsb-word-count-setting').show();
        }
    }
    toggleVisibilitySettings();
    $('#fsb_visibility_rule').on('change', toggleVisibilitySettings);

    // Reset Analytics
    $('#fsb-reset-analytics').on('click', function(e) {
        e.preventDefault();
        if (!confirm('Are you sure you want to reset all analytics data?')) return;
        const $button = $(this);
        $button.text('Resetting...').prop('disabled', true);
        $.ajax({
            url: fsb_admin_ajax.ajax_url,
            type: 'POST',
            data: { action: 'fsb_reset_analytics', nonce: fsb_admin_ajax.nonce },
            success: function(response) {
                if (response.success) {
                    alert('Analytics have been reset.');
                    location.reload();
                } else {
                    alert('An error occurred: ' + response.data);
                    $button.text('Reset Analytics Data').prop('disabled', false);
                }
            },
            error: function() {
                alert('A server error occurred.');
                $button.text('Reset Analytics Data').prop('disabled', false);
            }
        });
    });
});

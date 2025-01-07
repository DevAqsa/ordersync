jQuery(document).ready(function($) {
    console.log('OrderSync admin JS loaded'); // Debug line
    
    $('#create-tracking-page').on('click', function(e) {
        e.preventDefault();
        const button = $(this);
        const resultDiv = $('#tracking-page-result');
        
        // Disable button and show loading
        button.prop('disabled', true).text('Creating...');
        
        $.ajax({
            url: ordersyncAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'create_tracking_page',
                nonce: ordersyncAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    resultDiv.html('<div class="notice notice-success"><p>' + response.data.message + '</p></div>');
                    setTimeout(function() {
                        window.location.reload();
                    }, 1500);
                } else {
                    resultDiv.html('<div class="notice notice-error"><p>Failed to create tracking page</p></div>');
                    button.prop('disabled', false).text('Create Tracking Page');
                }
            },
            error: function() {
                resultDiv.html('<div class="notice notice-error"><p>An error occurred</p></div>');
                button.prop('disabled', false).text('Create Tracking Page');
            }
        });
    });
});
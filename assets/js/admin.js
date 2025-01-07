jQuery(document).ready(function($) {
    $('#create-order-form-page').on('click', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: ordersyncAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'create_order_form_page',
                nonce: ordersyncAjax.nonce
            },
            success: function(response) {
                if (response.success) {
                    alert('Order form page created successfully!');
                    window.location.reload();
                } else {
                    alert('Failed to create order form page.');
                }
            },
            error: function() {
                alert('An error occurred while creating the page.');
            }
        });
    });
});
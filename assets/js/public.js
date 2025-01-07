

jQuery(document).ready(function($) {
    $('#ordersync-form').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        formData.append('action', 'submit_order');
        formData.append('nonce', orderSync.nonce);

        $.ajax({
            url: orderSync.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    alert('Order submitted successfully! Your order ID is: #' + response.data.order_id);
                    $('#ordersync-form')[0].reset();
                } else {
                    alert('Error submitting order: ' + response.data);
                }
            },
            error: function() {
                alert('Error submitting order. Please try again.');
            }
        });
    });
});
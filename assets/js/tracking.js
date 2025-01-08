jQuery(document).ready(function($) {
    $('#ordersync-tracking-form').on('submit', function(e) {
        e.preventDefault();

        var $form = $(this);
        var $result = $('#ordersync-tracking-result');
        var $error = $('#ordersync-tracking-error');
        var $details = $('.tracking-details');

        // Clear previous results
        $result.hide();
        $error.hide();
        $details.empty();

        // Get form data
        var data = {
            action: 'ordersync_track_order',
            nonce: ordersyncAjax.nonce,
            order_id: $('#order_id').val(),
            tracking_code: $('#tracking_code').val()
        };

        // Send AJAX request
        $.post(ordersyncAjax.ajaxurl, data, function(response) {
            if (response.success) {
                var order = response.data;
                var html = `
                    <div class="order-detail">
                        <p><strong>Order ID:</strong> #${order.order_id}</p>
                        <p><strong>Status:</strong> ${order.status}</p>
                        <p><strong>Client:</strong> ${order.client_name}</p>
                        <p><strong>Project Type:</strong> ${order.project_type}</p>
                        <p><strong>Order Date:</strong> ${order.order_date}</p>
                        <p><strong>Last Updated:</strong> ${order.last_update}</p>
                    </div>
                `;
                
                $details.html(html);
                $result.fadeIn();
            } else {
                $error.html(response.data).fadeIn();
            }
        }).fail(function() {
            $error.html('An error occurred while tracking the order. Please try again.').fadeIn();
        });
    });
});
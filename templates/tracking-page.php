<?php
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$token = isset($_GET['token']) ? sanitize_text_field($_GET['token']) : '';

if (!$order_id || !$token) {
    ?>
    <div class="ordersync-tracking-lookup">
        <h2>Order Tracking</h2>
        <form class="tracking-form">
            <div class="form-group">
                <label for="track_order_id">Order ID</label>
                <input type="text" id="track_order_id" name="order_id" required>
            </div>
            <div class="form-group">
                <label for="track_token">Tracking Token</label>
                <input type="text" id="track_token" name="token" required>
            </div>
            <button type="submit">Track Order</button>
        </form>
    </div>
    <?php
    return;
}

$order = get_post($order_id);
$stored_token = get_post_meta($order_id, '_tracking_token', true);

if (!$order || $order->post_type !== 'ordersync_order' || $token !== $stored_token) {
    echo '<p class="error-message">Invalid order information provided.</p>';
    return;
}
?>

<div class="ordersync-tracking-details">
    <h2>Order #<?php echo $order_id; ?></h2>
    
    <div class="order-status">
        <span class="status-label">Current Status:</span>
        <span class="status-value <?php echo esc_attr($order->post_status); ?>">
            <?php echo ucfirst($order->post_status); ?>
        </span>
    </div>

    <div class="order-details">
        <h3>Order Information</h3>
        <div class="details-grid">
            <div class="detail-item">
                <span class="label">Client Name:</span>
                <span class="value"><?php echo esc_html(get_post_meta($order_id, '_client_name', true)); ?></span>
            </div>
            <div class="detail-item">
                <span class="label">Project Type:</span>
                <span class="value"><?php echo esc_html(get_post_meta($order_id, '_project_type', true)); ?></span>
            </div>
            <div class="detail-item">
                <span class="label">Delivery Date:</span>
                <span class="value"><?php echo esc_html(get_post_meta($order_id, '_delivery_date', true)); ?></span>
            </div>
            <div class="detail-item">
                <span class="label">Priority:</span>
                <span class="value"><?php echo esc_html(get_post_meta($order_id, '_priority', true)); ?></span>
            </div>
        </div>
    </div>

    <div class="order-updates">
        <h3>Project Updates</h3>
        <?php
        $updates = get_comments(array(
            'post_id' => $order_id,
            'order' => 'DESC'
        ));

        if ($updates) {
            echo '<div class="updates-timeline">';
            foreach ($updates as $update) {
                ?>
                <div class="update-item">
                    <div class="update-meta">
                        <span class="update-date"><?php echo date('M j, Y H:i', strtotime($update->comment_date)); ?></span>
                        <span class="update-author"><?php echo esc_html($update->comment_author); ?></span>
                    </div>
                    <div class="update-content">
                        <?php echo wp_kses_post($update->comment_content); ?>
                    </div>
                </div>
                <?php
            }
            echo '</div>';
        } else {
            echo '<p class="no-updates">No updates available yet.</p>';
        }
        ?>
    </div>

    <div class="order-communication">
        <h3>Add Comment</h3>
        <form id="order-comment-form" class="comment-form">
            <?php wp_nonce_field('add_order_comment', 'comment_nonce'); ?>
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
            <input type="hidden" name="token" value="<?php echo $token; ?>">
            
            <div class="form-group">
                <textarea name="comment" rows="4" required placeholder="Enter your message..."></textarea>
            </div>
            
            <button type="submit">Send Message</button>
        </form>
    </div>
</div>
<?php
if (!defined('ABSPATH')) {
    exit;
}

class OrderSync_Meta_Boxes {
    public function init() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
    }

    public function add_meta_boxes() {
        add_meta_box(
            'ordersync_details',
            'Order Details',
            array($this, 'render_meta_box'),
            'ordersync_order',
            'normal',
            'high'
        );
    }

    public function render_meta_box($post) {
        // Add nonce for security
        wp_nonce_field('ordersync_meta_box', 'ordersync_meta_box_nonce');

        // Get saved values
        $customer_name = get_post_meta($post->ID, '_customer_name', true);
        ?>
        <p>
            <label for="customer_name">Customer Name:</label>
            <input type="text" id="customer_name" name="customer_name" value="<?php echo esc_attr($customer_name); ?>">
        </p>
        <?php
    }
}
<?php

class OrderSync_Tracking {
    public function __construct() {
        $this->init();
    }

    public function init() {
        // Register shortcode
        add_shortcode('ordersync_tracking', array($this, 'render_tracking_form'));
        
        // Register AJAX handlers
        add_action('wp_ajax_ordersync_track_order', array($this, 'handle_track_order'));
        add_action('wp_ajax_nopriv_ordersync_track_order', array($this, 'handle_track_order'));
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        // Add tracking meta box to orders
        add_action('add_meta_boxes', array($this, 'add_tracking_meta_box'));
        add_action('save_post', array($this, 'save_tracking_meta'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style('ordersync-tracking', plugin_dir_url(dirname(__FILE__)) . 'assets/css/tracking.css');
        wp_enqueue_script('ordersync-tracking', plugin_dir_url(dirname(__FILE__)) . 'assets/js/tracking.js', array('jquery'), '1.0', true);
        wp_localize_script('ordersync-tracking', 'ordersyncAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ordersync_tracking_nonce')
        ));
    }

    public function render_tracking_form() {
        ob_start();
        ?>
        <div class="ordersync-tracking-wrapper">
            <div class="ordersync-tracking-form">
                <h2>Track Your Order</h2>
                <form id="ordersync-tracking-form">
                    <?php wp_nonce_field('ordersync_tracking', 'tracking_nonce'); ?>
                    
                    <div class="form-row">
                        <label for="order_id">Order ID:</label>
                        <input type="text" id="order_id" name="order_id" required>
                    </div>

                    <div class="form-row">
                        <label for="tracking_code">Tracking Code:</label>
                        <input type="text" id="tracking_code" name="tracking_code" required>
                    </div>

                    <div class="form-row">
                        <button type="submit" class="button button-primary">Track Order</button>
                    </div>
                </form>
            </div>

            <div id="ordersync-tracking-result" style="display: none;">
                <div class="tracking-info">
                    <h3>Order Information</h3>
                    <div class="tracking-details"></div>
                </div>
            </div>

            <div id="ordersync-tracking-error" class="error-message" style="display: none;"></div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function handle_track_order() {
        check_ajax_referer('ordersync_tracking_nonce', 'nonce');

        $order_id = isset($_POST['order_id']) ? sanitize_text_field($_POST['order_id']) : '';
        $tracking_code = isset($_POST['tracking_code']) ? sanitize_text_field($_POST['tracking_code']) : '';

        if (empty($order_id) || empty($tracking_code)) {
            wp_send_json_error('Please provide both Order ID and Tracking Code.');
        }

        // Get order
        $order = get_post($order_id);
        if (!$order || $order->post_type !== 'ordersync_order') {
            wp_send_json_error('Order not found.');
        }

        // Verify tracking code
        $stored_tracking_code = get_post_meta($order_id, '_tracking_code', true);
        if ($tracking_code !== $stored_tracking_code) {
            wp_send_json_error('Invalid tracking code.');
        }

        // Get order details
        $order_data = array(
            'order_id' => $order_id,
            'status' => get_post_meta($order_id, '_order_status', true),
            'client_name' => get_post_meta($order_id, '_client_name', true),
            'project_type' => get_post_meta($order_id, '_project_type', true),
            'order_date' => get_the_date('F j, Y', $order_id),
            'last_update' => get_the_modified_date('F j, Y g:i a', $order_id)
        );

        wp_send_json_success($order_data);
    }

    public function add_tracking_meta_box() {
        add_meta_box(
            'ordersync_tracking_meta',
            'Order Tracking Information',
            array($this, 'render_tracking_meta_box'),
            'ordersync_order',
            'side',
            'high'
        );
    }

    public function render_tracking_meta_box($post) {
        $tracking_code = get_post_meta($post->ID, '_tracking_code', true);
        if (!$tracking_code) {
            $tracking_code = $this->generate_tracking_code();
            update_post_meta($post->ID, '_tracking_code', $tracking_code);
        }
        ?>
        <div class="tracking-meta-box">
            <p>
                <strong>Tracking Code:</strong><br>
                <input type="text" value="<?php echo esc_attr($tracking_code); ?>" readonly class="widefat">
            </p>
            <p class="description">
                Share this tracking code with your client to allow them to track their order.
            </p>
        </div>
        <?php
    }

    private function generate_tracking_code() {
        return strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    }

    public function save_tracking_meta($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        if (!isset($_POST['post_type']) || $_POST['post_type'] !== 'ordersync_order') return;

        // Generate tracking code if it doesn't exist
        $tracking_code = get_post_meta($post_id, '_tracking_code', true);
        if (!$tracking_code) {
            $tracking_code = $this->generate_tracking_code();
            update_post_meta($post_id, '_tracking_code', $tracking_code);
        }
    }
}
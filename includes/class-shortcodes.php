<?php
// Update your class-shortcodes.php file with this code:

if (!defined('ABSPATH')) {
    exit;
}

class OrderSync_Shortcodes {
    public function init() {
        add_shortcode('ordersync_form', array($this, 'render_order_form'));
        add_shortcode('ordersync_tracking', array($this, 'render_tracking_page'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_submit_order', array($this, 'handle_order_submission'));
        add_action('wp_ajax_nopriv_submit_order', array($this, 'handle_order_submission'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style('ordersync-public', plugins_url('assets/css/public.css', dirname(__FILE__)));
        wp_enqueue_script('ordersync-public', plugins_url('assets/js/public.js', dirname(__FILE__)), array('jquery'), '1.0', true);
        
        wp_localize_script('ordersync-public', 'orderSync', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ordersync-submit-order')
        ));
    }

    public function render_order_form() {
        ob_start();
        include_once ORDERSYNC_PLUGIN_PATH . 'templates/order-form.php';
        return ob_get_clean();
    }

    public function handle_order_submission() {
        check_ajax_referer('ordersync-submit-order', 'nonce');

        $order_data = array(
            'post_title'   => 'Order - ' . date('Y-m-d H:i:s'),
            'post_type'    => 'ordersync_order',
            'post_status'  => 'publish',
            'meta_input'   => array(
                '_client_name'    => sanitize_text_field($_POST['client_name']),
                '_client_email'   => sanitize_email($_POST['client_email']),
                '_client_phone'   => sanitize_text_field($_POST['client_phone']),
                '_project_type'   => sanitize_text_field($_POST['project_type']),
                '_description'    => sanitize_textarea_field($_POST['description']),
                '_delivery_date'  => sanitize_text_field($_POST['delivery_date']),
                '_priority'       => sanitize_text_field($_POST['priority']),
                '_budget'         => sanitize_text_field($_POST['budget']),
                '_ordersync_status' => 'pending'
            )
        );

        $order_id = wp_insert_post($order_data);

        if ($order_id) {
            // Handle file uploads if present
            if (!empty($_FILES['project_files'])) {
                $files = $_FILES['project_files'];
                $uploaded_files = array();

                // Handle multiple files
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] === 0) {
                        $file = array(
                            'name'     => $files['name'][$i],
                            'type'     => $files['type'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'error'    => $files['error'][$i],
                            'size'     => $files['size'][$i]
                        );

                        $upload = wp_handle_upload($file, array('test_form' => false));
                        if (!isset($upload['error'])) {
                            $uploaded_files[] = $upload['url'];
                        }
                    }
                }

                if (!empty($uploaded_files)) {
                    update_post_meta($order_id, '_project_files', $uploaded_files);
                }
            }

            // Generate tracking token
            $tracking_token = wp_generate_password(12, false);
            update_post_meta($order_id, '_tracking_token', $tracking_token);

            // Send email notification
            $this->send_order_notification($order_id);

            wp_send_json_success(array(
                'order_id' => $order_id,
                'tracking_token' => $tracking_token,
                'message' => 'Order submitted successfully!'
            ));
        } else {
            wp_send_json_error('Failed to submit order');
        }
    }

    private function send_order_notification($order_id) {
        $admin_email = get_option('admin_email');
        $client_name = get_post_meta($order_id, '_client_name', true);
        $project_type = get_post_meta($order_id, '_project_type', true);
        
        $subject = 'New Order Received - #' . $order_id;
        
        $message = "A new order has been submitted:\n\n";
        $message .= "Order ID: #" . $order_id . "\n";
        $message .= "Client Name: " . $client_name . "\n";
        $message .= "Project Type: " . $project_type . "\n";
        $message .= "View Order: " . admin_url('post.php?post=' . $order_id . '&action=edit') . "\n";
        
        wp_mail($admin_email, $subject, $message);
    }
}
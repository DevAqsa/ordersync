<?php
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
        wp_enqueue_script('ordersync-public', plugins_url('assets/js/public.js', dirname(__FILE__)), array('jquery'), '', true);
        
        wp_localize_script('ordersync-public', 'orderSync', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ordersync-submit-order')
        ));
    }

    public function render_order_form() {
        ob_start();
        include ORDERSYNC_PLUGIN_PATH . 'templates/order-form.php';
        return ob_get_clean();
    }

    public function render_tracking_page() {
        ob_start();
        include ORDERSYNC_PLUGIN_PATH . 'templates/tracking-page.php';
        return ob_get_clean();
    }

    public function handle_order_submission() {
        check_ajax_referer('ordersync-submit-order', 'nonce');

        $order_data = array(
            'post_title'   => 'Order - ' . date('Y-m-d H:i:s'),
            'post_type'    => 'ordersync_order',
            'post_status'  => 'new'
        );

        $order_id = wp_insert_post($order_data);

        if ($order_id) {
            // Save order details as post meta
            $fields = array(
                'client_name', 'client_email', 'client_phone',
                'project_type', 'description', 'delivery_date',
                'priority', 'budget'
            );

            foreach ($fields as $field) {
                if (isset($_POST[$field])) {
                    update_post_meta($order_id, '_' . $field, sanitize_text_field($_POST[$field]));
                }
            }

            // Generate tracking token
            $tracking_token = wp_generate_password(12, false);
            update_post_meta($order_id, '_tracking_token', $tracking_token);

            // Handle file upload if exists
            if (!empty($_FILES['project_files'])) {
                require_once(ABSPATH . 'wp-admin/includes/file.php');
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                require_once(ABSPATH . 'wp-admin/includes/media.php');

                $attachment_id = media_handle_upload('project_files', $order_id);
                if (!is_wp_error($attachment_id)) {
                    update_post_meta($order_id, '_project_files', $attachment_id);
                }
            }

            wp_send_json_success(array(
                'order_id' => $order_id,
                'tracking_token' => $tracking_token,
                'tracking_url' => add_query_arg(array(
                    'order_id' => $order_id,
                    'token' => $tracking_token
                ), get_permalink(get_page_by_path('order-tracking')))
            ));
        } else {
            wp_send_json_error('Failed to create order');
        }
    }
}
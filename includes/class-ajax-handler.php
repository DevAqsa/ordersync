<?php
if (!defined('ABSPATH')) {
    exit;
}

class OrderSync_Ajax_Handler {
    public function init() {
        add_action('wp_ajax_ordersync_action', array($this, 'handle_ajax'));
        add_action('wp_ajax_nopriv_ordersync_action', array($this, 'handle_ajax'));
    }

    public function handle_ajax() {
        
        if (!check_ajax_referer('ordersync_nonce', 'nonce', false)) {
            wp_send_json_error('Invalid nonce');
        }

        // Handle the ajax request
        $action = isset($_POST['action_type']) ? sanitize_text_field($_POST['action_type']) : '';
        
        switch ($action) {
            case 'create_order':
                // Handle order creation
                break;
            default:
                wp_send_json_error('Invalid action');
                break;
        }

        wp_die();
    }
}
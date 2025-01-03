<?php
/**
 * Plugin Name: OrderSync
 * Description: A plugin for managing orders and client communication
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('ORDERSYNC_VERSION', '1.0.0');
define('ORDERSYNC_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('ORDERSYNC_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include class files
require_once ORDERSYNC_PLUGIN_DIR . 'classes/class-admin.php';
require_once ORDERSYNC_PLUGIN_DIR . 'classes/class-order-post-type.php';
require_once ORDERSYNC_PLUGIN_DIR . 'classes/class-meta-boxes.php';
require_once ORDERSYNC_PLUGIN_DIR . 'classes/class-ajax-handler.php';

// Initialize the plugin
function ordersync_init() {
    if (is_admin()) {
        $admin = new OrderSync_Admin();
        $admin->init();
    }
    
    $post_type = new OrderSync_Post_Type();
    $post_type->init();
    
    $meta_boxes = new OrderSync_Meta_Boxes();
    $meta_boxes->init();
    
    $ajax = new OrderSync_Ajax_Handler();
    $ajax->init();
}

add_action('plugins_loaded', 'ordersync_init');

// Activation hook
register_activation_hook(__FILE__, 'ordersync_activate');
function ordersync_activate() {
    flush_rewrite_rules();
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'ordersync_deactivate');
function ordersync_deactivate() {
    flush_rewrite_rules();
}
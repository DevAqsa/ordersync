<?php
/**
 * Plugin Name: OrderSync
 * Description: A plugin for managing orders and client communication
 * Version: 1.0.0
 * Author: Aqsa Mumtaz
 * License: GPL v2 or later
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin paths
define('ORDERSYNC_PLUGIN_PATH', plugin_dir_path(__FILE__));

// Include required files
require_once ORDERSYNC_PLUGIN_PATH . 'admin/class-admin.php';
require_once ORDERSYNC_PLUGIN_PATH . 'includes/class-order-post-type.php';
require_once ORDERSYNC_PLUGIN_PATH . 'includes/class-meta-boxes.php';
require_once ORDERSYNC_PLUGIN_PATH . 'includes/class-ajax-handler.php';





// Initialize plugin components
function ordersync_init() {
    $admin = new OrderSync_Admin();
    $admin->init();
    
    $post_type = new OrderSync_Post_Type();
    $post_type->init();
}

add_action('init', 'ordersync_init');

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
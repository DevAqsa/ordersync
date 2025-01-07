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
require_once ORDERSYNC_PLUGIN_PATH . 'includes/class-shortcodes.php';


function ordersync_init() {
    $admin = new OrderSync_Admin();
    $admin->init();
    
    $post_type = new OrderSync_Post_Type();
    $post_type->init();
    
    $shortcodes = new OrderSync_Shortcodes();
    $shortcodes->init();
}


add_action('admin_init', 'register_ordersync_settings');

function register_ordersync_settings() {
    register_setting('ordersync_form_options', 'ordersync_enable_uploads');
    register_setting('ordersync_form_options', 'ordersync_max_file_size');
    register_setting('ordersync_form_options', 'ordersync_allowed_file_types');
}


require_once plugin_dir_path(__FILE__) . 'includes/class-tracking.php';


$tracking = new OrderSync_Tracking();
$tracking->init();


// Initialize plugin components
// function ordersync_init() {
//     $admin = new OrderSync_Admin();
//     $admin->init();
    
//     $post_type = new OrderSync_Post_Type();
//     $post_type->init();
// }

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
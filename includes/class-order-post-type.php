<?php
if (!defined('ABSPATH')) {
    exit;
}

class OrderSync_Post_Type {
    public function init() {
        add_action('init', array($this, 'register_post_type'));
    }

    public function register_post_type() {
        $args = array(
            'public' => true,
            'label'  => 'Orders',
            'labels' => array(
                'name' => 'Orders',
                'singular_name' => 'Order'
            ),
            'menu_icon' => 'dashicons-controls-repeat',
            'supports' => array('title', 'editor')
        );
        
        register_post_type('ordersync_order', $args);
    }
}

function register_ordersync_post_type() {
    $args = array(
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => false, 
        'supports' => array('title'),
        'labels' => array(
            'name' => 'Orders',
            'singular_name' => 'Order',
        ),
        'capability_type' => 'post',
    );
    
    register_post_type('ordersync_order', $args);
}
add_action('init', 'register_ordersync_post_type');
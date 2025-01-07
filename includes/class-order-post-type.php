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
            'menu_icon' => 'dashicons-cart',
            'supports' => array('title', 'editor')
        );
        
        register_post_type('ordersync_order', $args);
    }
}
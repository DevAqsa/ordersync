<?php
if (!defined('ABSPATH')) {
    exit;
}

class OrderSync_Post_Type {
    public function init() {
        add_action('init', array($this, 'register_post_type'));
        add_action('init', array($this, 'register_post_status'));
    }

    public function register_post_type() {
        $labels = array(
            'name' => 'Orders',
            'singular_name' => 'Order',
            'menu_name' => 'OrderSync',
            'add_new' => 'Add New Order',
            'add_new_item' => 'Add New Order',
            'edit_item' => 'Edit Order',
            'new_item' => 'New Order',
            'view_item' => 'View Order',
            'search_items' => 'Search Orders',
            'not_found' => 'No orders found',
            'not_found_in_trash' => 'No orders found in trash'
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => array('slug' => 'order'),
            'supports' => array('title', 'editor', 'comments'),
            'menu_icon' => 'dashicons-clipboard'
        );

        register_post_type('ordersync_order', $args);
    }

    public function register_post_status() {
        register_post_status('new', array(
            'label' => 'New',
            'public' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('New <span class="count">(%s)</span>', 'New <span class="count">(%s)</span>')
        ));

        register_post_status('in_progress', array(
            'label' => 'In Progress',
            'public' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('In Progress <span class="count">(%s)</span>', 'In Progress <span class="count">(%s)</span>')
        ));

        register_post_status('completed', array(
            'label' => 'Completed',
            'public' => true,
            'show_in_admin_status_list' => true,
            'label_count' => _n_noop('Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>')
        ));
    }
}
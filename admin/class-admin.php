<?php
if (!defined('ABSPATH')) {
    exit;
}

class OrderSync_Admin {
    public function init() {
        add_action('admin_menu', array($this, 'add_menu_page'));
    }

    public function add_menu_page() {
        add_menu_page(
            'OrderSync', 
            'OrderSync', 
            'manage_options', 
            'ordersync', 
            array($this, 'render_admin_page'),
            'dashicons-cart',
            30
        );
    }

    public function render_admin_page() {
        ?>
        <div class="wrap">
            <h1>OrderSync Dashboard</h1>
            <p>Welcome to OrderSync plugin dashboard.</p>
        </div>
        <?php
    }
}
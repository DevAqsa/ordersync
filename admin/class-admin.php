<?php
if (!defined('ABSPATH')) {
    exit;
}

class OrderSync_Admin {
    public function init() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    public function enqueue_scripts($hook) {
        if ('ordersync_order' !== get_post_type()) {
            return;
        }

        wp_enqueue_style(
            'ordersync-admin-style',
            ORDERSYNC_PLUGIN_URL . 'assets/css/admin-style.css',
            array(),
            ORDERSYNC_VERSION
        );

        wp_enqueue_script(
            'ordersync-admin-script',
            ORDERSYNC_PLUGIN_URL . 'assets/js/admin-script.js',
            array('jquery'),
            ORDERSYNC_VERSION,
            true
        );
    }

    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=ordersync_order',
            'OrderSync Settings',
            'Settings',
            'manage_options',
            'ordersync-settings',
            array($this, 'render_settings_page')
        );
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('ordersync_options');
                do_settings_sections('ordersync_options');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
<?php
if (!defined('ABSPATH')) {
    exit;
}

class OrderSync_Admin {
    public function init() {
        add_action('admin_menu', array($this, 'add_menu_pages'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('wp_ajax_create_order_form_page', array($this, 'create_order_form_page'));
    }

    public function add_menu_pages() {
        // Main menu
        add_menu_page(
            'OrderSync', 
            'OrderSync', 
            'manage_options', 
            'ordersync', 
            array($this, 'render_admin_page'),
            'dashicons-controls-repeat',
            30
        );

        // Submenu for Form Page
        add_submenu_page(
            'ordersync',
            'Order Form',
            'Order Form',
            'manage_options',
            'ordersync-form',
            array($this, 'render_form_page')
        );

        add_submenu_page(
            'ordersync',
            'Tracking page',
            'Tracking page',
            'manage_options',
            'ordersync-form',
            array($this, 'render_form_page')
        );
    }

    public function enqueue_admin_scripts($hook) {
        if ('toplevel_page_ordersync' !== $hook && 'ordersync_page_ordersync-form' !== $hook) {
            return;
        }
        wp_enqueue_style('ordersync-admin-css', plugins_url('assets/css/admin.css', dirname(__FILE__)));
        wp_enqueue_script('ordersync-admin-js', plugins_url('assets/js/admin.js', dirname(__FILE__)), array('jquery'), '1.0', true);
        wp_localize_script('ordersync-admin-js', 'ordersyncAjax', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ordersync_nonce')
        ));
    }

    public function render_form_page() {
        // Get the shortcode URL
        $shortcode = '[ordersync_form]';
        
        // Check if the Order Form page exists
        $page = get_page_by_path('order-form');
        $form_url = $page ? get_permalink($page->ID) : '';
        
        ?>
        <div class="wrap ordersync-admin">
            <h1>Order Form Settings</h1>
            
            <div class="card">
                <h2>Shortcode</h2>
                <p>Use this shortcode to display the order form on any page:</p>
                <code><?php echo esc_html($shortcode); ?></code>
                
                <div class="shortcode-instructions">
                    <h3>How to use:</h3>
                    <ol>
                        <li>Create a new page in WordPress</li>
                        <li>Name it "Order Form" or any preferred name</li>
                        <li>Add the shortcode above to the page content</li>
                        <li>Publish the page</li>
                    </ol>
                </div>
            </div>

            <?php if ($form_url): ?>
            <div class="card">
                <h2>Form Preview</h2>
                <p>View the form page: <a href="<?php echo esc_url($form_url); ?>" target="_blank">Order Form</a></p>
            </div>
            <?php else: ?>
            <div class="card">
                <h2>Create Order Form Page</h2>
                <p>No order form page detected. Would you like to create one?</p>
                <button class="button button-primary" id="create-order-form-page">Create Order Form Page</button>
            </div>
            <?php endif; ?>

            <div class="card">
                <h2>Form Settings</h2>
                <form method="post" action="options.php" class="ordersync-form-settings">
                    <?php
                    settings_fields('ordersync_form_options');
                    do_settings_sections('ordersync_form_options');
                    ?>
                    <table class="form-table">
                        <tr>
                            <th scope="row">Enable File Uploads</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="ordersync_enable_uploads" value="1" 
                                        <?php checked(get_option('ordersync_enable_uploads'), 1); ?>>
                                    Allow users to upload files with their orders
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Maximum File Size (MB)</th>
                            <td>
                                <input type="number" name="ordersync_max_file_size" value="<?php echo esc_attr(get_option('ordersync_max_file_size', 5)); ?>" min="1" max="50">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Allowed File Types</th>
                            <td>
                                <input type="text" name="ordersync_allowed_file_types" value="<?php echo esc_attr(get_option('ordersync_allowed_file_types', 'pdf,doc,docx,jpg,png')); ?>" class="regular-text">
                                <p class="description">Comma-separated list of file extensions (e.g., pdf,doc,docx,jpg,png)</p>
                            </td>
                        </tr>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>
        </div>
        <?php
    }

    public function render_admin_page() {
        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'dashboard';
        ?>
        <div class="wrap ordersync-admin">
            <h1>OrderSync Dashboard</h1>
            
            <nav class="nav-tab-wrapper">
                <a href="?page=ordersync&tab=dashboard" class="nav-tab <?php echo $current_tab === 'dashboard' ? 'nav-tab-active' : ''; ?>">
                    Dashboard
                </a>
                <a href="?page=ordersync&tab=orders" class="nav-tab <?php echo $current_tab === 'orders' ? 'nav-tab-active' : ''; ?>">
                    Orders
                </a>
                <a href="?page=ordersync&tab=settings" class="nav-tab <?php echo $current_tab === 'settings' ? 'nav-tab-active' : ''; ?>">
                    Settings
                </a>
            </nav>

            <div class="tab-content">
                <?php
                switch ($current_tab) {
                    case 'orders':
                        $this->render_orders_tab();
                        break;
                    case 'settings':
                        $this->render_settings_tab();
                        break;
                    default:
                        $this->render_dashboard_tab();
                        break;
                }
                ?>
            </div>
        </div>
        <?php
    }

    private function render_dashboard_tab() {
        $total_orders = $this->get_total_orders();
        $pending_orders = $this->get_orders_by_status('pending');
        $in_progress_orders = $this->get_orders_by_status('in-progress');
        $completed_orders = $this->get_orders_by_status('completed');
        ?>
        <div class="ordersync-dashboard-widgets">
            <div class="widget">
                <h3>Total Orders</h3>
                <div class="count"><?php echo esc_html($total_orders); ?></div>
            </div>
            <div class="widget">
                <h3>Pending Orders</h3>
                <div class="count"><?php echo esc_html($pending_orders); ?></div>
            </div>
            <div class="widget">
                <h3>In Progress</h3>
                <div class="count"><?php echo esc_html($in_progress_orders); ?></div>
            </div>
            <div class="widget">
                <h3>Completed</h3>
                <div class="count"><?php echo esc_html($completed_orders); ?></div>
            </div>
        </div>

        <div class="ordersync-recent-orders">
            <h2>Recent Orders</h2>
            <?php $this->render_orders_table(5); ?>
        </div>
        <?php
    }

    public function render_orders_tab() {
        ?>
        <div class="ordersync-orders-tab">
            <h2>All Orders</h2>
            <?php $this->render_orders_table(); ?>
        </div>
        <?php
    }

    public function render_settings_tab() {
        ?>
        <div class="ordersync-settings-tab">
            <h2>OrderSync Settings</h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('ordersync_settings');
                do_settings_sections('ordersync_settings');
                ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">Email Notifications</th>
                        <td>
                            <label>
                                <input type="checkbox" name="ordersync_email_notifications" value="1" 
                                    <?php checked(get_option('ordersync_email_notifications'), 1); ?>>
                                Enable email notifications
                            </label>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    private function render_orders_table($limit = -1) {
        $args = array(
            'post_type' => 'ordersync_order',
            'posts_per_page' => $limit,
            'orderby' => 'date',
            'order' => 'DESC'
        );

        $orders = get_posts($args);

        if (empty($orders)) {
            echo '<p>No orders found.</p>';
            return;
        }

        ?>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Client Name</th>
                    <th>Project Type</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>#<?php echo $order->ID; ?></td>
                        <td><?php echo esc_html(get_post_meta($order->ID, '_client_name', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($order->ID, '_project_type', true)); ?></td>
                        <td><?php echo esc_html(get_post_meta($order->ID, '_ordersync_status', true)); ?></td>
                        <td><?php echo get_the_date('', $order->ID); ?></td>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=ordersync&action=view&id=' . $order->ID); ?>" 
                               class="button button-small">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php
    }

   

//------------------------------------------------------

    private function get_total_orders() {
        $counts = wp_count_posts('ordersync_order');
        return isset($counts->publish) ? $counts->publish : 0;
    }

    private function get_orders_by_status($status) {
        $args = array(
            'post_type' => 'ordersync_order',
            'meta_key' => '_ordersync_status',
            'meta_value' => $status,
            'posts_per_page' => -1
        );
        return count(get_posts($args));
    }

    public function create_order_form_page() {
        check_ajax_referer('ordersync_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Insufficient permissions');
        }

        $page_data = array(
            'post_title'    => 'Order Form',
            'post_content'  => '[ordersync_form]',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_name'     => 'order-form'
        );

        $page_id = wp_insert_post($page_data);

        if ($page_id && !is_wp_error($page_id)) {
            wp_send_json_success(array(
                'message' => 'Order form page created successfully',
                'url' => get_permalink($page_id)
            ));
        } else {
            wp_send_json_error('Failed to create order form page');
        }
    }
}
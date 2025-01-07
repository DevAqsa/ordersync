<?php
if (!defined('ABSPATH')) {
    exit;
}

class OrderSync_Meta_Boxes {
    public function init() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_ordersync_order', array($this, 'save_meta_box_data'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
    }

    public function enqueue_styles($hook) {
        global $post;
        if ($hook == 'post.php' && $post && $post->post_type === 'ordersync_order') {
            wp_enqueue_style('ordersync-admin-meta', plugins_url('assets/css/admin-meta.css', dirname(__FILE__)));
            
            // Add custom styles for the view page
            $custom_css = "
                .ordersync-order-details {
                    background: #fff;
                    padding: 20px;
                    border: 1px solid #e5e5e5;
                    box-shadow: 0 1px 1px rgba(0,0,0,.04);
                }
                .order-section {
                    margin-bottom: 25px;
                    padding-bottom: 20px;
                    border-bottom: 1px solid #eee;
                }
                .order-section:last-child {
                    border-bottom: none;
                }
                .status-section {
                    background: #f9f9f9;
                    padding: 15px;
                    border-radius: 4px;
                }
                .status-badge {
                    display: inline-block;
                    padding: 5px 12px;
                    border-radius: 3px;
                    font-weight: bold;
                    text-transform: uppercase;
                    font-size: 12px;
                }
                .status-pending { background: #fff6e5; color: #956100; }
                .status-in-progress { background: #e5f6ff; color: #006aa1; }
                .status-completed { background: #e5ffe7; color: #006a13; }
                .status-cancelled { background: #ffe5e5; color: #6a0000; }
                .form-row {
                    margin-bottom: 15px;
                    display: flex;
                    align-items: flex-start;
                }
                .form-row label {
                    width: 150px;
                    font-weight: bold;
                }
                .form-row .field-value {
                    flex: 1;
                }
                .files-list {
                    margin: 0;
                    padding: 0;
                    list-style: none;
                }
                .files-list li {
                    padding: 8px;
                    background: #f9f9f9;
                    margin-bottom: 5px;
                    border-radius: 3px;
                }
                .files-list li a {
                    text-decoration: none;
                    color: #2271b1;
                }
                .files-list .dashicons {
                    margin-right: 5px;
                    color: #666;
                }
            ";
            wp_add_inline_style('ordersync-admin-meta', $custom_css);
        }
    }

    public function add_meta_boxes() {
        add_meta_box(
            'ordersync_order_details',
            __('Order Details', 'ordersync'),
            array($this, 'render_details_meta_box'),
            'ordersync_order',
            'normal',
            'high'
        );
    }

    public function render_details_meta_box($post) {
        // Security nonce
        wp_nonce_field('ordersync_order_details', 'ordersync_order_details_nonce');

        // Get order data
        $order_data = array(
            'status' => get_post_meta($post->ID, '_ordersync_status', true),
            'client_name' => get_post_meta($post->ID, '_client_name', true),
            'client_email' => get_post_meta($post->ID, '_client_email', true),
            'client_phone' => get_post_meta($post->ID, '_client_phone', true),
            'project_type' => get_post_meta($post->ID, '_project_type', true),
            'description' => get_post_meta($post->ID, '_description', true),
            'delivery_date' => get_post_meta($post->ID, '_delivery_date', true),
            'priority' => get_post_meta($post->ID, '_priority', true),
            'budget' => get_post_meta($post->ID, '_budget', true),
            'tracking_token' => get_post_meta($post->ID, '_tracking_token', true),
            'project_files' => get_post_meta($post->ID, '_project_files', true),
            'submission_date' => get_the_date('F j, Y g:i a', $post->ID)
        );

        $status_classes = array(
            'pending' => 'status-pending',
            'in-progress' => 'status-in-progress',
            'completed' => 'status-completed',
            'cancelled' => 'status-cancelled'
        );
        ?>
        <div class="ordersync-order-details">
            <!-- Order Status and Summary Section -->
            <div class="order-section status-section">
                <h3><?php _e('Order Status & Summary', 'ordersync'); ?></h3>
                <div class="form-row">
                    <label><?php _e('Status:', 'ordersync'); ?></label>
                    <div class="field-value">
                        <span class="status-badge <?php echo esc_attr($status_classes[$order_data['status']] ?? ''); ?>">
                            <?php echo esc_html(ucfirst($order_data['status'])); ?>
                        </span>
                    </div>
                </div>
                <div class="form-row">
                    <label><?php _e('Order ID:', 'ordersync'); ?></label>
                    <div class="field-value">#<?php echo $post->ID; ?></div>
                </div>
                <div class="form-row">
                    <label><?php _e('Submitted:', 'ordersync'); ?></label>
                    <div class="field-value"><?php echo esc_html($order_data['submission_date']); ?></div>
                </div>
            </div>

            <!-- Client Information Section -->
            <div class="order-section">
                <h3><?php _e('Client Information', 'ordersync'); ?></h3>
                <div class="form-row">
                    <label><?php _e('Name:', 'ordersync'); ?></label>
                    <div class="field-value"><?php echo esc_html($order_data['client_name']); ?></div>
                </div>
                <div class="form-row">
                    <label><?php _e('Email:', 'ordersync'); ?></label>
                    <div class="field-value">
                        <a href="mailto:<?php echo esc_attr($order_data['client_email']); ?>">
                            <?php echo esc_html($order_data['client_email']); ?>
                        </a>
                    </div>
                </div>
                <?php if (!empty($order_data['client_phone'])): ?>
                <div class="form-row">
                    <label><?php _e('Phone:', 'ordersync'); ?></label>
                    <div class="field-value">
                        <a href="tel:<?php echo esc_attr($order_data['client_phone']); ?>">
                            <?php echo esc_html($order_data['client_phone']); ?>
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Project Details Section -->
            <div class="order-section">
                <h3><?php _e('Project Details', 'ordersync'); ?></h3>
                <div class="form-row">
                    <label><?php _e('Project Type:', 'ordersync'); ?></label>
                    <div class="field-value"><?php echo esc_html(ucwords(str_replace('_', ' ', $order_data['project_type']))); ?></div>
                </div>
                <div class="form-row">
                    <label><?php _e('Description:', 'ordersync'); ?></label>
                    <div class="field-value"><?php echo nl2br(esc_html($order_data['description'])); ?></div>
                </div>
                <div class="form-row">
                    <label><?php _e('Delivery Date:', 'ordersync'); ?></label>
                    <div class="field-value"><?php echo esc_html(date('F j, Y', strtotime($order_data['delivery_date']))); ?></div>
                </div>
                <div class="form-row">
                    <label><?php _e('Priority:', 'ordersync'); ?></label>
                    <div class="field-value"><?php echo esc_html(ucfirst($order_data['priority'])); ?></div>
                </div>
                <?php if (!empty($order_data['budget'])): ?>
                <div class="form-row">
                    <label><?php _e('Budget:', 'ordersync'); ?></label>
                    <div class="field-value">$<?php echo number_format((float)$order_data['budget'], 2); ?> USD</div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Project Files Section -->
            <div class="order-section">
                <h3><?php _e('Project Files', 'ordersync'); ?></h3>
                <div class="project-files">
                    <?php if (!empty($order_data['project_files']) && is_array($order_data['project_files'])): ?>
                        <ul class="files-list">
                            <?php foreach ($order_data['project_files'] as $file): ?>
                                <li>
                                    <a href="<?php echo esc_url($file); ?>" target="_blank">
                                        <span class="dashicons dashicons-media-default"></span>
                                        <?php echo esc_html(basename($file)); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="no-files"><?php _e('No files uploaded', 'ordersync'); ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tracking Information -->
            <div class="order-section">
                <h3><?php _e('Tracking Information', 'ordersync'); ?></h3>
                <div class="form-row">
                    <label><?php _e('Tracking Token:', 'ordersync'); ?></label>
                    <div class="field-value">
                        <code><?php echo esc_html($order_data['tracking_token']); ?></code>
                    </div>
                </div>
                <?php if ($order_data['tracking_token']): ?>
                    <div class="form-row">
                        <label><?php _e('Tracking URL:', 'ordersync'); ?></label>
                        <div class="field-value">
                            <?php 
                            $tracking_url = add_query_arg(
                                array(
                                    'order_id' => $post->ID,
                                    'token' => $order_data['tracking_token']
                                ),
                                home_url('/order-tracking/')
                            );
                            ?>
                            <a href="<?php echo esc_url($tracking_url); ?>" target="_blank"><?php echo esc_url($tracking_url); ?></a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Update Status Section -->
            <div class="order-section">
                <h3><?php _e('Update Status', 'ordersync'); ?></h3>
                <div class="form-row">
                    <label><?php _e('Change Status:', 'ordersync'); ?></label>
                    <div class="field-value">
                        <select name="ordersync_status" id="ordersync_status">
                            <option value="pending" <?php selected($order_data['status'], 'pending'); ?>><?php _e('Pending', 'ordersync'); ?></option>
                            <option value="in-progress" <?php selected($order_data['status'], 'in-progress'); ?>><?php _e('In Progress', 'ordersync'); ?></option>
                            <option value="completed" <?php selected($order_data['status'], 'completed'); ?>><?php _e('Completed', 'ordersync'); ?></option>
                            <option value="cancelled" <?php selected($order_data['status'], 'cancelled'); ?>><?php _e('Cancelled', 'ordersync'); ?></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function save_meta_box_data($post_id) {
        // Verify nonce
        if (!isset($_POST['ordersync_order_details_nonce']) || 
            !wp_verify_nonce($_POST['ordersync_order_details_nonce'], 'ordersync_order_details')) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Update status
        if (isset($_POST['ordersync_status'])) {
            $status = sanitize_text_field($_POST['ordersync_status']);
            update_post_meta($post_id, '_ordersync_status', $status);
        }
    }
}
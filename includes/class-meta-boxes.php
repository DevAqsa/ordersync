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
            'project_files' => get_post_meta($post->ID, '_project_files', true)
        );
        ?>
        <div class="ordersync-order-details">
            <!-- Order Status Section -->
            <div class="order-section status-section">
                <h3><?php _e('Order Status', 'ordersync'); ?></h3>
                <div class="status-control">
                    <select name="ordersync_status" id="ordersync_status">
                        <option value="pending" <?php selected($order_data['status'], 'pending'); ?>><?php _e('Pending', 'ordersync'); ?></option>
                        <option value="in-progress" <?php selected($order_data['status'], 'in-progress'); ?>><?php _e('In Progress', 'ordersync'); ?></option>
                        <option value="completed" <?php selected($order_data['status'], 'completed'); ?>><?php _e('Completed', 'ordersync'); ?></option>
                        <option value="cancelled" <?php selected($order_data['status'], 'cancelled'); ?>><?php _e('Cancelled', 'ordersync'); ?></option>
                    </select>
                </div>
            </div>

            <!-- Client Information Section -->
            <div class="order-section">
                <h3><?php _e('Client Information', 'ordersync'); ?></h3>
                <div class="form-row">
                    <label><?php _e('Name:', 'ordersync'); ?></label>
                    <div class="field-value">
                        <input type="text" name="client_name" value="<?php echo esc_attr($order_data['client_name']); ?>" class="widefat">
                    </div>
                </div>
                <div class="form-row">
                    <label><?php _e('Email:', 'ordersync'); ?></label>
                    <div class="field-value">
                        <input type="email" name="client_email" value="<?php echo esc_attr($order_data['client_email']); ?>" class="widefat">
                    </div>
                </div>
                <div class="form-row">
                    <label><?php _e('Phone:', 'ordersync'); ?></label>
                    <div class="field-value">
                        <input type="tel" name="client_phone" value="<?php echo esc_attr($order_data['client_phone']); ?>" class="widefat">
                    </div>
                </div>
            </div>

            <!-- Project Details Section -->
            <div class="order-section">
                <h3><?php _e('Project Details', 'ordersync'); ?></h3>
                <div class="form-row">
                    <label><?php _e('Project Type:', 'ordersync'); ?></label>
                    <div class="field-value">
                        <select name="project_type" class="widefat">
                            <option value="web_development" <?php selected($order_data['project_type'], 'web_development'); ?>><?php _e('Web Development', 'ordersync'); ?></option>
                            <option value="graphic_design" <?php selected($order_data['project_type'], 'graphic_design'); ?>><?php _e('Graphic Design', 'ordersync'); ?></option>
                            <option value="digital_marketing" <?php selected($order_data['project_type'], 'digital_marketing'); ?>><?php _e('Digital Marketing', 'ordersync'); ?></option>
                            <option value="content_writing" <?php selected($order_data['project_type'], 'content_writing'); ?>><?php _e('Content Writing', 'ordersync'); ?></option>
                            <option value="other" <?php selected($order_data['project_type'], 'other'); ?>><?php _e('Other', 'ordersync'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <label><?php _e('Description:', 'ordersync'); ?></label>
                    <div class="field-value">
                        <textarea name="description" rows="5" class="widefat"><?php echo esc_textarea($order_data['description']); ?></textarea>
                    </div>
                </div>
                <div class="form-row">
                    <label><?php _e('Delivery Date:', 'ordersync'); ?></label>
                    <div class="field-value">
                        <input type="date" name="delivery_date" value="<?php echo esc_attr($order_data['delivery_date']); ?>" class="widefat">
                    </div>
                </div>
                <div class="form-row">
                    <label><?php _e('Priority:', 'ordersync'); ?></label>
                    <div class="field-value">
                        <select name="priority" class="widefat">
                            <option value="normal" <?php selected($order_data['priority'], 'normal'); ?>><?php _e('Normal', 'ordersync'); ?></option>
                            <option value="urgent" <?php selected($order_data['priority'], 'urgent'); ?>><?php _e('Urgent', 'ordersync'); ?></option>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <label><?php _e('Budget:', 'ordersync'); ?></label>
                    <div class="field-value">
                        <input type="number" name="budget" value="<?php echo esc_attr($order_data['budget']); ?>" class="widefat" min="0" step="0.01">
                    </div>
                </div>
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

        // Update fields
        $fields = array(
            'ordersync_status' => 'text',
            'client_name' => 'text',
            'client_email' => 'email',
            'client_phone' => 'text',
            'project_type' => 'text',
            'description' => 'textarea',
            'delivery_date' => 'text',
            'priority' => 'text',
            'budget' => 'float'
        );

        foreach ($fields as $field => $type) {
            if (isset($_POST[$field])) {
                $value = $_POST[$field];
                switch ($type) {
                    case 'email':
                        $value = sanitize_email($value);
                        break;
                    case 'textarea':
                        $value = sanitize_textarea_field($value);
                        break;
                    case 'float':
                        $value = floatval($value);
                        break;
                    default:
                        $value = sanitize_text_field($value);
                }
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
    }
}
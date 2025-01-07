<?php
// Create a new file: view-order.php in your plugin's admin folder

if (!defined('ABSPATH')) {
    exit;
}

// Get order ID from URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$order_id) {
    wp_die('Invalid order ID');
}

// Get order data
$order = get_post($order_id);
if (!$order || $order->post_type !== 'ordersync_order') {
    wp_die('Order not found');
}

// Get all order meta data
$order_data = array(
    'client_name' => get_post_meta($order_id, '_client_name', true),
    'client_email' => get_post_meta($order_id, '_client_email', true),
    'client_phone' => get_post_meta($order_id, '_client_phone', true),
    'project_type' => get_post_meta($order_id, '_project_type', true),
    'description' => get_post_meta($order_id, '_description', true),
    'delivery_date' => get_post_meta($order_id, '_delivery_date', true),
    'priority' => get_post_meta($order_id, '_priority', true),
    'budget' => get_post_meta($order_id, '_budget', true),
    'status' => get_post_meta($order_id, '_ordersync_status', true),
    'project_files' => get_post_meta($order_id, '_project_files', true)
);
?>

<div class="wrap ordersync-order-view">
    <h1 class="wp-heading-inline">View Order #<?php echo $order_id; ?></h1>
    <hr class="wp-header-end">

    <?php if (isset($_POST['update_status']) && check_admin_referer('update_order_status')): ?>
        <div class="notice notice-success">
            <p>Order status updated successfully!</p>
        </div>
    <?php endif; ?>

    <div class="order-details-container">
        <!-- Status Section -->
        <div class="order-section postbox">
            <h2 class="hndle"><span>Order Status</span></h2>
            <div class="inside">
                <form method="post" action="">
                    <?php wp_nonce_field('update_order_status'); ?>
                    <table class="form-table">
                        <tr>
                            <th>Current Status:</th>
                            <td>
                                <select name="order_status">
                                    <option value="pending" <?php selected($order_data['status'], 'pending'); ?>>Pending</option>
                                    <option value="in-progress" <?php selected($order_data['status'], 'in-progress'); ?>>In Progress</option>
                                    <option value="completed" <?php selected($order_data['status'], 'completed'); ?>>Completed</option>
                                    <option value="cancelled" <?php selected($order_data['status'], 'cancelled'); ?>>Cancelled</option>
                                </select>
                                <input type="submit" name="update_status" class="button button-primary" value="Update Status">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>

        <!-- Client Information -->
        <div class="order-section postbox">
            <h2 class="hndle"><span>Client Information</span></h2>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th>Name:</th>
                        <td><?php echo esc_html($order_data['client_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><a href="mailto:<?php echo esc_attr($order_data['client_email']); ?>"><?php echo esc_html($order_data['client_email']); ?></a></td>
                    </tr>
                    <?php if (!empty($order_data['client_phone'])): ?>
                    <tr>
                        <th>Phone:</th>
                        <td><a href="tel:<?php echo esc_attr($order_data['client_phone']); ?>"><?php echo esc_html($order_data['client_phone']); ?></a></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Project Details -->
        <div class="order-section postbox">
            <h2 class="hndle"><span>Project Details</span></h2>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th>Project Type:</th>
                        <td><?php echo esc_html(ucwords(str_replace('_', ' ', $order_data['project_type']))); ?></td>
                    </tr>
                    <tr>
                        <th>Description:</th>
                        <td><?php echo nl2br(esc_html($order_data['description'])); ?></td>
                    </tr>
                    <tr>
                        <th>Delivery Date:</th>
                        <td><?php echo esc_html(date('F j, Y', strtotime($order_data['delivery_date']))); ?></td>
                    </tr>
                    <tr>
                        <th>Priority:</th>
                        <td><?php echo esc_html(ucfirst($order_data['priority'])); ?></td>
                    </tr>
                    <?php if (!empty($order_data['budget'])): ?>
                    <tr>
                        <th>Budget:</th>
                        <td>$<?php echo number_format((float)$order_data['budget'], 2); ?> USD</td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Project Files -->
        <?php if (!empty($order_data['project_files']) && is_array($order_data['project_files'])): ?>
        <div class="order-section postbox">
            <h2 class="hndle"><span>Project Files</span></h2>
            <div class="inside">
                <ul class="project-files-list">
                    <?php foreach ($order_data['project_files'] as $file): ?>
                        <li>
                            <span class="dashicons dashicons-media-default"></span>
                            <a href="<?php echo esc_url($file); ?>" target="_blank">
                                <?php echo esc_html(basename($file)); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.ordersync-order-view .order-section {
    margin-bottom: 20px;
}
.ordersync-order-view .form-table th {
    width: 150px;
    padding: 15px;
}
.ordersync-order-view .form-table td {
    padding: 15px;
}
.project-files-list {
    margin: 0;
    padding: 0;
    list-style: none;
}
.project-files-list li {
    margin-bottom: 10px;
    padding: 5px;
    background: #f9f9f9;
    border-radius: 3px;
}
.project-files-list .dashicons {
    margin-right: 5px;
    color: #666;
}
</style><?php
// Create a new file: view-order.php in your plugin's admin folder

if (!defined('ABSPATH')) {
    exit;
}

// Get order ID from URL
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$order_id) {
    wp_die('Invalid order ID');
}

// Get order data
$order = get_post($order_id);
if (!$order || $order->post_type !== 'ordersync_order') {
    wp_die('Order not found');
}

// Get all order meta data
$order_data = array(
    'client_name' => get_post_meta($order_id, '_client_name', true),
    'client_email' => get_post_meta($order_id, '_client_email', true),
    'client_phone' => get_post_meta($order_id, '_client_phone', true),
    'project_type' => get_post_meta($order_id, '_project_type', true),
    'description' => get_post_meta($order_id, '_description', true),
    'delivery_date' => get_post_meta($order_id, '_delivery_date', true),
    'priority' => get_post_meta($order_id, '_priority', true),
    'budget' => get_post_meta($order_id, '_budget', true),
    'status' => get_post_meta($order_id, '_ordersync_status', true),
    'project_files' => get_post_meta($order_id, '_project_files', true)
);
?>

<div class="wrap ordersync-order-view">
    <h1 class="wp-heading-inline">View Order #<?php echo $order_id; ?></h1>
    <hr class="wp-header-end">

    <?php if (isset($_POST['update_status']) && check_admin_referer('update_order_status')): ?>
        <div class="notice notice-success">
            <p>Order status updated successfully!</p>
        </div>
    <?php endif; ?>

    <div class="order-details-container">
        <!-- Status Section -->
        <div class="order-section postbox">
            <h2 class="hndle"><span>Order Status</span></h2>
            <div class="inside">
                <form method="post" action="">
                    <?php wp_nonce_field('update_order_status'); ?>
                    <table class="form-table">
                        <tr>
                            <th>Current Status:</th>
                            <td>
                                <select name="order_status">
                                    <option value="pending" <?php selected($order_data['status'], 'pending'); ?>>Pending</option>
                                    <option value="in-progress" <?php selected($order_data['status'], 'in-progress'); ?>>In Progress</option>
                                    <option value="completed" <?php selected($order_data['status'], 'completed'); ?>>Completed</option>
                                    <option value="cancelled" <?php selected($order_data['status'], 'cancelled'); ?>>Cancelled</option>
                                </select>
                                <input type="submit" name="update_status" class="button button-primary" value="Update Status">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>

        <!-- Client Information -->
        <div class="order-section postbox">
            <h2 class="hndle"><span>Client Information</span></h2>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th>Name:</th>
                        <td><?php echo esc_html($order_data['client_name']); ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><a href="mailto:<?php echo esc_attr($order_data['client_email']); ?>"><?php echo esc_html($order_data['client_email']); ?></a></td>
                    </tr>
                    <?php if (!empty($order_data['client_phone'])): ?>
                    <tr>
                        <th>Phone:</th>
                        <td><a href="tel:<?php echo esc_attr($order_data['client_phone']); ?>"><?php echo esc_html($order_data['client_phone']); ?></a></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Project Details -->
        <div class="order-section postbox">
            <h2 class="hndle"><span>Project Details</span></h2>
            <div class="inside">
                <table class="form-table">
                    <tr>
                        <th>Project Type:</th>
                        <td><?php echo esc_html(ucwords(str_replace('_', ' ', $order_data['project_type']))); ?></td>
                    </tr>
                    <tr>
                        <th>Description:</th>
                        <td><?php echo nl2br(esc_html($order_data['description'])); ?></td>
                    </tr>
                    <tr>
                        <th>Delivery Date:</th>
                        <td><?php echo esc_html(date('F j, Y', strtotime($order_data['delivery_date']))); ?></td>
                    </tr>
                    <tr>
                        <th>Priority:</th>
                        <td><?php echo esc_html(ucfirst($order_data['priority'])); ?></td>
                    </tr>
                    <?php if (!empty($order_data['budget'])): ?>
                    <tr>
                        <th>Budget:</th>
                        <td>$<?php echo number_format((float)$order_data['budget'], 2); ?> USD</td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <!-- Project Files -->
        <?php if (!empty($order_data['project_files']) && is_array($order_data['project_files'])): ?>
        <div class="order-section postbox">
            <h2 class="hndle"><span>Project Files</span></h2>
            <div class="inside">
                <ul class="project-files-list">
                    <?php foreach ($order_data['project_files'] as $file): ?>
                        <li>
                            <span class="dashicons dashicons-media-default"></span>
                            <a href="<?php echo esc_url($file); ?>" target="_blank">
                                <?php echo esc_html(basename($file)); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<style>
.ordersync-order-view .order-section {
    margin-bottom: 20px;
}
.ordersync-order-view .form-table th {
    width: 150px;
    padding: 15px;
}
.ordersync-order-view .form-table td {
    padding: 15px;
}
.project-files-list {
    margin: 0;
    padding: 0;
    list-style: none;
}
.project-files-list li {
    margin-bottom: 10px;
    padding: 5px;
    background: #f9f9f9;
    border-radius: 3px;
}
.project-files-list .dashicons {
    margin-right: 5px;
    color: #666;
}
</style>
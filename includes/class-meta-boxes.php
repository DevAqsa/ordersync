<?php
namespace OrderSync;

class Meta_Boxes {
    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_order_meta'));
    }

    public function add_meta_boxes() {
        add_meta_box(
            'order_details',
            'Order Details',
            array($this, 'render_order_meta_box'),
            'ordersync_order',
            'normal',
            'high'
        );
    }

    public function render_order_meta_box($post) {
        wp_nonce_field('ordersync_order_meta', 'ordersync_order_meta_nonce');
        
        $fields = array(
            'client_name' => get_post_meta($post->ID, '_client_name', true),
            'client_email' => get_post_meta($post->ID, '_client_email', true),
            'client_phone' => get_post_meta($post->ID, '_client_phone', true),
            'delivery_date' => get_post_meta($post->ID, '_delivery_date', true),
            'priority' => get_post_meta($post->ID, '_priority', true),
            'budget' => get_post_meta($post->ID, '_budget', true)
        );

        include ORDERSYNC_PLUGIN_DIR . 'templates/meta-box.php';
    }

    public function save_order_meta($post_id) {
        if (!isset($_POST['ordersync_order_meta_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['ordersync_order_meta_nonce'], 'ordersync_order_meta')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        $fields = array(
            'client_name',
            'client_email',
            'client_phone',
            'delivery_date',
            'priority',
            'budget'
        );

        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, sanitize_text_field($_POST[$field]));
            }
        }
    }
}
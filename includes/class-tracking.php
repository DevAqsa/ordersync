<?php
// In class-tracking.php:

class OrderSync_Tracking {
    public function init() {
        // Register shortcode
        add_shortcode('ordersync_tracking', array($this, 'render_tracking_form'));
        
        // Register AJAX handlers
        add_action('wp_ajax_track_order', array($this, 'track_order'));
        add_action('wp_ajax_nopriv_track_order', array($this, 'track_order'));
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function enqueue_scripts() {
        // Only enqueue on pages with our shortcode
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'ordersync_tracking')) {
            wp_enqueue_style('ordersync-tracking', plugins_url('assets/css/tracking.css', dirname(__FILE__)));
            wp_enqueue_script('ordersync-tracking', plugins_url('assets/js/tracking.js', dirname(__FILE__)), array('jquery'), '1.0', true);
            wp_localize_script('ordersync-tracking', 'ordersyncTrack', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('track_order')
            ));
        }
    }

    public function render_tracking_form() {
        ob_start();
        ?>
        <div class="ordersync-tracking-container">
            <form id="ordersync-tracking-form" class="tracking-form">
                <?php wp_nonce_field('track_order', 'tracking_nonce'); ?>
                
                <div class="form-group">
                    <label for="track_order_id">Order ID</label>
                    <input type="text" id="track_order_id" name="order_id" required>
                </div>
                
                <div class="form-group">
                    <label for="track_token">Tracking Token</label>
                    <input type="text" id="track_token" name="token" required>
                </div>
                
                <button type="submit" class="button">Track Order</button>
            </form>
            
            <div id="tracking-result" style="display: none;"></div>
        </div>
        <?php
        return ob_get_clean();
    }
}
<?php
/**
 * Plugin Name: Fluid Share Buttons
 * Description: A mobile-only, modern floating share button with a progress scroll-to-top and advanced controls.
 * Version: 5.0
 * Author: Jared Celemin
 * Author URI: https://midnightrebels.com
 * License: GPL2
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('FSB_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FSB_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Main loader function for the plugin.
 */
function fsb_plugin_init() {
    // Front-end logic only loads if the user is on a mobile device.
    if (wp_is_mobile()) {
        require_once FSB_PLUGIN_PATH . 'includes/functions.php';
        add_action('wp_enqueue_scripts', 'fsb_enqueue_assets');
        add_action('wp_footer', 'fsb_add_floating_buttons_to_footer');

        // Only use the internal view counter if Jetpack Stats is not available.
        if (!function_exists('stats_get_post_views')) {
            add_action('wp_head', 'fsb_increment_internal_view_count');
        }
    }
}
add_action('plugins_loaded', 'fsb_plugin_init');

/**
 * Enqueues scripts and styles for the front-end.
 */
function fsb_enqueue_assets() {
    if (is_single()) {
        wp_enqueue_style('fsb-styles', FSB_PLUGIN_URL . 'public/styles.css', array(), '5.0.0');
        wp_enqueue_script('fsb-main', FSB_PLUGIN_URL . 'public/main.js', array('jquery'), '5.0.0', true);
        
        wp_localize_script('fsb-main', 'fsb_settings', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'rest_url' => esc_url_raw(rest_url()),
            'nonce' => wp_create_nonce('wp_rest'),
            'ajax_nonce' => wp_create_nonce('fsb_ajax_nonce'),
            'visibilityRule' => get_option('fsb_visibility_rule', 'always'),
            'scrollPercentage' => get_option('fsb_scroll_percentage', '50'),
            'scrollTopPercentage' => get_option('fsb_scroll_top_percentage', '20')
        ));
    }
}

/**
 * Adds the floating buttons HTML to the site's footer.
 */
function fsb_add_floating_buttons_to_footer() {
    if (is_single() && !is_admin()) {
        echo fsb_get_share_buttons_html();
    }
}

// The admin page needs to load for everyone, so we require it unconditionally.
require_once FSB_PLUGIN_PATH . 'admin/admin-page.php';

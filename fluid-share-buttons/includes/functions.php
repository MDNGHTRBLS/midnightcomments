<?php

if (!defined('ABSPATH')) exit;

// ---- Internal View Counter (Fallback for when Jetpack is not active) ----
function fsb_get_internal_view_count($post_id) {
    $count = get_post_meta($post_id, '_fsb_post_views', true);
    return empty($count) ? 0 : intval($count);
}
function fsb_increment_internal_view_count() {
    if (is_single() && !is_admin()) {
        $post_id = get_the_ID();
        $count = fsb_get_internal_view_count($post_id);
        update_post_meta($post_id, '_fsb_post_views', $count + 1);
    }
}

// --- AJAX HANDLER for Click Tracking ---
function fsb_track_click_handler() {
    check_ajax_referer('fsb_ajax_nonce', 'nonce');
    if (isset($_POST['network'])) {
        $network = sanitize_text_field($_POST['network']);
        $total_clicks = get_option('fsb_click_count_' . $network, 0);
        update_option('fsb_click_count_' . $network, $total_clicks + 1);
        wp_send_json_success();
    }
    wp_die();
}
add_action('wp_ajax_fsb_track_click', 'fsb_track_click_handler');
add_action('wp_ajax_nopriv_fsb_track_click', 'fsb_track_click_handler');

// --- REST API ENDPOINT for Views (Bypasses Caching) ---
function fsb_register_rest_route() {
    register_rest_route('fsb/v1', '/views/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'fsb_get_views_rest_callback',
        'permission_callback' => '__return_true',
    ));
}
add_action('rest_api_init', 'fsb_register_rest_route');

function fsb_get_views_rest_callback($data) {
    $post_id = intval($data['id']);
    if (!$post_id) {
        return new WP_Error('no_post_id', 'Invalid post ID', array('status' => 404));
    }
    
    $view_count = 0;
    if (function_exists('stats_get_post_views')) {
        $view_count = stats_get_post_views($post_id);
    } else {
        $view_count = fsb_get_internal_view_count($post_id);
    }
    
    return new WP_REST_Response(array('views' => number_format_i18n($view_count)), 200);
}


// ---- Share Buttons HTML Generation ----
function fsb_get_share_buttons_html() {
    global $post;
    $post_id = $post->ID;

    // --- Visibility Rules Check (Server-Side for Word Count) ---
    $visibility_rule = get_option('fsb_visibility_rule', 'always');
    if ($visibility_rule === 'word_count') {
        $word_count_threshold = (int) get_option('fsb_word_count_threshold', 300);
        $content = get_the_content(null, false, $post_id);
        $word_count = str_word_count(strip_tags($content));
        if ($word_count < $word_count_threshold) {
            return '';
        }
    }
    
    $post_url = urlencode(get_permalink($post_id));
    $post_title = urlencode(get_the_title($post_id));

    // --- Get settings from admin panel ---
    $bg_color = get_option('fsb_background_color', '#1c1c1c');
    $pos_bottom = get_option('fsb_position_bottom', '20');
    $pos_right = get_option('fsb_position_right', '20');
    $main_gap = get_option('fsb_main_gap', '10');
    $size_fab = get_option('fsb_size_main_fab', '56');
    $size_share = get_option('fsb_size_share_icons', '44');
    $container_width = get_option('fsb_container_width', '64');
    $font_size_shares = get_option('fsb_font_size_shares', '14');
    $font_size_label = get_option('fsb_font_size_label', '10');
    $scroll_size = get_option('fsb_scroll_size', '44');
    $main_fab_icon = get_option('fsb_icon_main_fab', FSB_PLUGIN_URL . 'assets/images/share-default.svg');
    $scroll_icon_url = get_option('fsb_scroll_icon_url', FSB_PLUGIN_URL . 'assets/images/arrow-up.svg');
    $facebook_icon = get_option('fsb_icon_facebook', FSB_PLUGIN_URL . 'assets/images/facebook.svg');
    $threads_icon = get_option('fsb_icon_threads', FSB_PLUGIN_URL . 'assets/images/threads.svg');
    $x_icon = get_option('fsb_icon_x', FSB_PLUGIN_URL . 'assets/images/x.svg');
    $telegram_icon = get_option('fsb_icon_telegram', FSB_PLUGIN_URL . 'assets/images/telegram.svg');
    $copy_icon = get_option('fsb_icon_copy', FSB_PLUGIN_URL . 'assets/images/copy.svg');

    // --- Prepare classes and inline styles ---
    $container_classes = 'fsb-container-wrapper';
    if ($visibility_rule === 'scroll') {
        $container_classes .= ' fsb-hidden-by-rule';
    }
    $styles = "
        bottom: " . esc_attr($pos_bottom) . "px;
        right: " . esc_attr($pos_right) . "px;
        --fsb-main-gap: " . esc_attr($main_gap) . "px;
        --fsb-bg-color: " . esc_attr($bg_color) . ";
        --fsb-fab-size: " . esc_attr($size_fab) . "px;
        --fsb-share-icon-size: " . esc_attr($size_share) . "px;
        --fsb-container-width: " . esc_attr($container_width) . "px;
        --fsb-font-size-shares: " . esc_attr($font_size_shares) . "px;
        --fsb-font-size-label: " . esc_attr($font_size_label) . "px;
        --fsb-scroll-size: " . esc_attr($scroll_size) . "px;
    ";

    // --- Generate HTML ---
    $html = '<div class="' . $container_classes . '" style="' . $styles . '" data-postid="' . esc_attr($post_id) . '">';

    $html .= '<div class="fsb-fab-container">';
    $html .= '<div class="fsb-share-links">';
    $html .= '<a href="https://www.facebook.com/sharer/sharer.php?u=' . $post_url . '" target="_blank" rel="noopener noreferrer" class="fsb-share-btn" data-network="facebook"><img src="' . esc_url($facebook_icon) . '" alt="Facebook"></a>';
    $html .= '<a href="https://threads.net/intent/post?text=' . $post_title . '%20' . $post_url . '" target="_blank" rel="noopener noreferrer" class="fsb-share-btn" data-network="threads"><img src="' . esc_url($threads_icon) . '" alt="Threads"></a>';
    $html .= '<a href="https://twitter.com/intent/tweet?url=' . $post_url . '&text=' . $post_title . '" target="_blank" rel="noopener noreferrer" class="fsb-share-btn" data-network="x"><img src="' . esc_url($x_icon) . '" alt="X"></a>';
    $html .= '<a href="https://t.me/share/url?url=' . $post_url . '&text=' . $post_title . '" target="_blank" rel="noopener noreferrer" class="fsb-share-btn" data-network="telegram"><img src="' . esc_url($telegram_icon) . '" alt="Telegram"></a>';
    $html .= '<button class="fsb-share-btn fsb-copy-link" data-network="copy" data-link="' . esc_url(get_permalink($post_id)) . '">';
    $html .= '<span class="fsb-copy-icon-initial"><img src="' . esc_url($copy_icon) . '" alt="Copy URL"></span>';
    $html .= '<span class="fsb-copy-icon-success">OK!</span>';
    $html .= '</button>';
    $html .= '<div class="fsb-share-counter"><span class="fsb-share-label">Shares</span><span class="fsb-share-number">-</span></div>';
    $html .= '</div>'; // end .fsb-share-links
    $html .= '<button class="fsb-main-fab">';
    $html .= '<img src="' . esc_url($main_fab_icon) . '" alt="Share" class="fsb-main-fab-icon">';
    $html .= '</button>';
    $html .= '</div>'; // end .fsb-fab-container

    $html .= '<button class="fsb-scroll-top-btn fsb-scroll-hidden">';
    $html .= '<svg class="fsb-scroll-svg" width="36" height="36" viewBox="0 0 36 36">';
    $html .= '<path class="fsb-scroll-bg" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />';
    $html .= '<path class="fsb-scroll-progress" stroke="#cfff00" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />';
    $html .= '</svg>';
    $html .= '<img class="fsb-scroll-arrow" src="' . esc_url($scroll_icon_url) . '" alt="Scroll to top">';
    $html .= '</button>';

    $html .= '</div>'; // end .fsb-container-wrapper

    return $html;
}

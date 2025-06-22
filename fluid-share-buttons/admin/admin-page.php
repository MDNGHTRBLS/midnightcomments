<?php

if (!defined('ABSPATH')) exit;

function fsb_admin_menu() {
    add_menu_page('Fluid Share Buttons', 'Fluid Share', 'manage_options', 'fluid-share-buttons', 'fsb_admin_page_html', 'dashicons-share-alt2', 80);
}
add_action('admin_menu', 'fsb_admin_menu');

function fsb_register_settings() {
    // Icons
    register_setting('fsb_settings_group', 'fsb_icon_main_fab');
    register_setting('fsb_settings_group', 'fsb_icon_facebook');
    register_setting('fsb_settings_group', 'fsb_icon_threads');
    register_setting('fsb_settings_group', 'fsb_icon_x');
    register_setting('fsb_settings_group', 'fsb_icon_telegram');
    register_setting('fsb_settings_group', 'fsb_icon_copy');
    register_setting('fsb_settings_group', 'fsb_scroll_icon_url');
    // Style & Layout
    register_setting('fsb_settings_group', 'fsb_background_color');
    register_setting('fsb_settings_group', 'fsb_position_bottom');
    register_setting('fsb_settings_group', 'fsb_position_right');
    register_setting('fsb_settings_group', 'fsb_main_gap');
    register_setting('fsb_settings_group', 'fsb_size_main_fab');
    register_setting('fsb_settings_group', 'fsb_size_share_icons');
    register_setting('fsb_settings_group', 'fsb_container_width');
    register_setting('fsb_settings_group', 'fsb_font_size_shares');
    register_setting('fsb_settings_group', 'fsb_font_size_label');
    register_setting('fsb_settings_group', 'fsb_scroll_size');
    // Visibility
    register_setting('fsb_settings_group', 'fsb_visibility_rule');
    register_setting('fsb_settings_group', 'fsb_scroll_percentage');
    register_setting('fsb_settings_group', 'fsb_word_count_threshold');
    register_setting('fsb_settings_group', 'fsb_scroll_top_percentage');
}
add_action('admin_init', 'fsb_register_settings');

function fsb_admin_page_html() {
    ?>
    <div class="wrap fsb-admin-wrap">
        <h1><span class="dashicons-before dashicons-share-alt2"></span> Fluid Share Buttons Settings</h1>
        <div class="fsb-notice"><strong>Note:</strong> This plugin is mobile-only. The "Shares" count displays the article's total views (from Jetpack, if active).</div>
        
        <div class="fsb-admin-container">
            <div class="fsb-settings-form">
                <form method="post" action="options.php">
                    <?php settings_fields('fsb_settings_group'); ?>
                    <?php do_settings_sections('fsb_settings_group'); ?>

                    <h2><span class="dashicons dashicons-visibility"></span> Visibility Rules</h2>
                     <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><label for="fsb_visibility_rule">Share Button Appearance</label></th>
                            <td>
                                <select name="fsb_visibility_rule" id="fsb_visibility_rule">
                                    <option value="always" <?php selected(get_option('fsb_visibility_rule', 'always'), 'always'); ?>>Always Show</option>
                                    <option value="scroll" <?php selected(get_option('fsb_visibility_rule'), 'scroll'); ?>>Show after user scrolls X%</option>
                                    <option value="word_count" <?php selected(get_option('fsb_visibility_rule'), 'word_count'); ?>>Only on posts longer than X words</option>
                                </select>
                            </td>
                        </tr>
                        <tr valign="top" class="fsb-rule-setting" id="fsb-scroll-setting">
                             <th scope="row"><label for="fsb_scroll_percentage">Share Button Scroll %</label></th>
                             <td><input type="number" name="fsb_scroll_percentage" id="fsb_scroll_percentage" value="<?php echo esc_attr(get_option('fsb_scroll_percentage', 50)); ?>" min="1" max="100" /> %</td>
                        </tr>
                        <tr valign="top">
                             <th scope="row"><label for="fsb_scroll_top_percentage">Scroll-to-Top Appear %</label></th>
                             <td><input type="number" name="fsb_scroll_top_percentage" id="fsb_scroll_top_percentage" value="<?php echo esc_attr(get_option('fsb_scroll_top_percentage', 20)); ?>" min="1" max="100" /> %</td>
                        </tr>
                        <tr valign="top" class="fsb-rule-setting" id="fsb-word-count-setting">
                             <th scope="row"><label for="fsb_word_count_threshold">Word Count Minimum</label></th>
                             <td><input type="number" name="fsb_word_count_threshold" id="fsb_word_count_threshold" value="<?php echo esc_attr(get_option('fsb_word_count_threshold', 300)); ?>" min="1" /> words</td>
                        </tr>
                    </table>

                    <h2><span class="dashicons dashicons-admin-appearance"></span> Layout & Style</h2>
                     <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Position</th>
                            <td>
                                <label style="margin-right:10px">Bottom:</label><input type="number" name="fsb_position_bottom" value="<?php echo esc_attr(get_option('fsb_position_bottom', 20)); ?>" class="small-text" /> px
                                <label style="margin-left:20px;margin-right:10px">Right:</label><input type="number" name="fsb_position_right" value="<?php echo esc_attr(get_option('fsb_position_right', 20)); ?>" class="small-text" /> px
                            </td>
                        </tr>
                         <tr valign="top">
                            <th scope="row">Sizes & Spacing</th>
                            <td>
                                <label style="margin-right:10px">Main Button:</label><input type="number" name="fsb_size_main_fab" value="<?php echo esc_attr(get_option('fsb_size_main_fab', 56)); ?>" class="small-text" /> px
                                <label style="margin-left:20px;margin-right:10px">Share Icons:</label><input type="number" name="fsb_size_share_icons" value="<?php echo esc_attr(get_option('fsb_size_share_icons', 44)); ?>" class="small-text" /> px<br>
                                <label style="margin-right:10px; margin-top:10px; display:inline-block;">Scroll Button:</label><input type="number" name="fsb_scroll_size" value="<?php echo esc_attr(get_option('fsb_scroll_size', 44)); ?>" class="small-text" /> px
                                <label style="margin-left:20px;margin-right:10px">Main Button Gap:</label><input type="number" name="fsb_main_gap" value="<?php echo esc_attr(get_option('fsb_main_gap', 10)); ?>" min="0" class="small-text" /> px
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label for="fsb_container_width">Expanded Container Width</label></th>
                            <td><input type="number" name="fsb_container_width" id="fsb_container_width" value="<?php echo esc_attr(get_option('fsb_container_width', 64)); ?>" min="0" class="small-text" /> px
                                <p class="description">Sets the width of the expanded menu. Should be at least as wide as the 'Share Icons' size.</p>
                            </td>
                        </tr>
                         <tr valign="top">
                            <th scope="row">Font Sizes (Shares)</th>
                            <td>
                                <label style="margin-right:10px">"Shares" Text:</label><input type="number" name="fsb_font_size_label" value="<?php echo esc_attr(get_option('fsb_font_size_label', 10)); ?>" class="small-text" /> px
                                <label style="margin-left:20px;margin-right:10px">Number:</label><input type="number" name="fsb_font_size_shares" value="<?php echo esc_attr(get_option('fsb_font_size_shares', 14)); ?>" class="small-text" /> px
                            </td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><label for="fsb_background_color">Background Color</label></th>
                            <td><input type="text" name="fsb_background_color" value="<?php echo esc_attr(get_option('fsb_background_color', '#1c1c1c')); ?>" class="fsb-color-picker" /></td>
                        </tr>
                    </table>
                    
                    <h2><span class="dashicons dashicons-format-image"></span> Custom Icon URLs</h2>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><label for="fsb_icon_main_fab"><strong>Main Share Button Icon</strong></label></th>
                            <td><input type="url" name="fsb_icon_main_fab" value="<?php echo esc_attr(get_option('fsb_icon_main_fab')); ?>" class="regular-text" placeholder="e.g., https://example.com/share-icon.svg" /></td>
                        </tr>
                         <tr valign="top">
                            <th scope="row"><label for="fsb_scroll_icon_url"><strong>Scroll to Top Icon</strong></label></th>
                            <td><input type="url" name="fsb_scroll_icon_url" value="<?php echo esc_attr(get_option('fsb_scroll_icon_url')); ?>" class="regular-text" placeholder="e.g., https://example.com/arrow-up.svg" /></td>
                        </tr>
                        <tr valign="top"><td colspan="2"><hr/></td></tr>
                        <?php
                        $icons = ['facebook', 'threads', 'x', 'telegram', 'copy'];
                        foreach ($icons as $icon) {
                            echo '<tr valign="top"><th scope="row"><label for="fsb_icon_'.$icon.'">'.ucfirst($icon).' Icon</label></th><td><input type="url" name="fsb_icon_'.$icon.'" value="'.esc_attr(get_option('fsb_icon_'.$icon)).'" class="regular-text" /></td></tr>';
                        }
                        ?>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>
            <div class="fsb-analytics-dashboard">
                <h2><span class="dashicons dashicons-chart-bar"></span> Click Analytics</h2>
                <div class="fsb-analytics-grid">
                    <?php
                    $networks = ['facebook', 'threads', 'x', 'telegram', 'copy'];
                    foreach ($networks as $network) {
                        $count = get_option('fsb_click_count_' . $network, 0);
                        echo '<div class="fsb-analytic-item"><span class="fsb-analytic-network">' . ucfirst($network) . '</span><span class="fsb-analytic-count">' . number_format_i18n($count) . '</span><span class="fsb-analytic-label">clicks</span></div>';
                    }
                    ?>
                </div>
                <button id="fsb-reset-analytics" class="button button-secondary">Reset Analytics Data</button>
            </div>
        </div>
    </div>
    <?php
}
add_action('wp_ajax_fsb_reset_analytics', 'fsb_reset_analytics_handler');
function fsb_reset_analytics_handler() {
    check_ajax_referer('fsb_admin_nonce', 'nonce');
    if (!current_user_can('manage_options')) wp_send_json_error('Permission denied.');
    $networks = ['facebook', 'threads', 'x', 'telegram', 'copy'];
    foreach ($networks as $network) delete_option('fsb_click_count_' . $network);
    wp_send_json_success('Analytics reset successfully.');
    wp_die();
}
function fsb_admin_assets($hook) {
    if ($hook !== 'toplevel_page_fluid-share-buttons') return;
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_style('fsb-admin-styles', FSB_PLUGIN_URL . 'admin/admin-styles.css', [], '4.6.0');
    wp_enqueue_script('fsb-admin-scripts', FSB_PLUGIN_URL . 'admin/admin-scripts.js', ['jquery', 'wp-color-picker'], '4.6.0', true);
    wp_localize_script('fsb-admin-scripts', 'fsb_admin_ajax', ['ajax_url' => admin_url('admin-ajax.php'),'nonce' => wp_create_nonce('fsb_admin_nonce')]);
}
add_action('admin_enqueue_scripts', 'fsb_admin_assets');

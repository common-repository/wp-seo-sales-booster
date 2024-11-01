<?php

/**
 * Plugin Name:       WP SEO & Sales Booster
 * Plugin URI:        http://wpseobooster.com
 * Description:       WP SEO & Sales Booster Plugin Helps You to Boost up Your SEO & Sales For Better Targeting Both WordPress & WooCommerce Platform on Search Engines.
 * Version:           1.0.0
 * Author:            WPSEOBooster
 * Author URI:        https://wpseobooster.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp_seo_sales_booster
 * Domain Path:       /languages
 * WC requires at least: 3.0.0
 * WC tested up to: 5.4.2
 * Requires PHP: 5.6
 * Stable tag: 3.0.8
 */

if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * Currently plugin version.
 * Rename this for your plugin and update it as you release new versions.
 */
if (!defined('WP_SEO_SALES_BOOSTER_VERSION')) {
    define('WP_SEO_SALES_BOOSTER_VERSION', '1.0.0');
}

if (!defined('WP_SEO_SALES_BOOSTER_PLUGIN_FILE')) {
    define('WP_SEO_SALES_BOOSTER_PLUGIN_FILE', __FILE__);
}
if (!defined('WP_SEO_SALES_BOOSTER_ABSPATH')) {
    define('WP_SEO_SALES_BOOSTER_ABSPATH', dirname(WP_SEO_SALES_BOOSTER_PLUGIN_FILE) . '/');
}

if (!defined('WP_SEO_SALES_BOOSTER_URL')) {
    define('WP_SEO_SALES_BOOSTER_URL', plugin_dir_url(__FILE__));
}
if (!defined('WP_SEO_SALES_BOOSTER_IMG_URL')) {
    define('WP_SEO_SALES_BOOSTER_IMG_URL', plugin_dir_url(__FILE__) . 'assets/img/');
}

// Load plugin basic class files
include_once ABSPATH . 'wp-admin/includes/plugin.php';
include_once 'includes/class-wp-seo-sales-booster.php';


function wp_seo_sales_booster()
{
    if (!wssb_woocommerce_is_active()) {
        return;
    }
    // Load dependencies.
    $instance = Wp_Seo_Sales_Booster::get_instance(__FILE__, WP_SEO_SALES_BOOSTER_VERSION);
    // var_dump($instance->aaa);exit;
    if (is_null($instance->settings)) {
        $instance->settings = Wp_Seo_Sales_Booster_Settings::instance($instance);
    }
    if (is_null($instance->conversion) && get_option('wp_seo_sales_booster_conversion_tracking')=='enable') {
        $instance->settings = Wssb_Conversion_Tracking::instance($instance);
    }

    return $instance;
}
add_action('plugins_loaded', 'wp_seo_sales_booster');

function wssb_woocommerce_is_active()
{
    return is_plugin_active('woocommerce/woocommerce.php');
}

function wssb_woocommerce_activation_checking()
{
    if (!wssb_woocommerce_is_active()) {
        deactivate_plugins(plugin_basename(__FILE__));
        unset($_GET['activate']); // Input variable okay.
        //showing error message.
        add_action('admin_notices', 'wssb_admin_notice__error');
    }
}
function wssb_admin_notice__error()
{
    $class = 'notice notice-error';
    $message = __("Sorry, you can't active this plugin without WooCommerce. Please  install and active woocommerce plugin first.", 'acl-wooain');
    printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
}
function wssb_on_activate()
{
    
    if (!get_option('wp_seo_sales_booster_keyword_planner')) {
        update_option('wp_seo_sales_booster_keyword_planner', 'enable');
    }
    if (!get_option('wp_seo_sales_booster_conversion_tracking')) {
        update_option('wp_seo_sales_booster_conversion_tracking', 'enable');
    }
    if (!get_option('wp_seo_sales_booster_internal_link')) {
        update_option('wp_seo_sales_booster_internal_link', 'enable');
    }
    if (!get_option('wp_seo_sales_booster_live_sales_notification')) {
        update_option('wp_seo_sales_booster_live_sales_notification', 'enable');
    }
    if (!get_option('wp_seo_sales_booster_auto_image_attr')) {
        update_option('wp_seo_sales_booster_auto_image_attr', 'enable');
    }
    if (!get_option('wp_seo_sales_booster_customer_client_id')) {
        update_option('wp_seo_sales_booster_customer_client_id', '');
    }
    if (!get_option('wp_seo_sales_booster_own_api_key')) {
        update_option('wp_seo_sales_booster_own_api_key', 'off');
    }
    if (!get_option('wp_seo_sales_booster_client_id')) {
        update_option('wp_seo_sales_booster_client_id', '');
    }
    if (!get_option('wp_seo_sales_booster_client_secret')) {
        update_option('wp_seo_sales_booster_client_secret', '');
    }
    if (!get_option('wp_seo_sales_booster_redirect_url')) {
        update_option('wp_seo_sales_booster_redirect_url', '');
    }
    if (!get_option('wp_seo_sales_booster_conversion')) {
        update_option('wp_seo_sales_booster_conversion', ['google','facebook','twitter','custom']);
    }
    if (!get_option('wp_seo_sales_booster_internal_link')) {
        update_option('wp_seo_sales_booster_internal_link', 'enable');
    }
    if (!get_option('wp_seo_sales_booster_notification_type')) {
        update_option('wp_seo_sales_booster_notification_type', ['live_sales_notification']);
    }
    if (!get_option('wp_seo_sales_booster_notification_show')) {
        update_option('wp_seo_sales_booster_notification_show', ['shop','category','product']);
    }
    if (!get_option('wp_seo_sales_booster_close_button')) {
        update_option('wp_seo_sales_booster_close_button', 1);
    }
    if (!get_option('wp_seo_sales_booster_progress_bar')) {
        update_option('wp_seo_sales_booster_progress_bar', 1);
    }
    if (!get_option('wp_seo_sales_booster_font_color')) {
        update_option('wp_seo_sales_booster_font_color', '#ffffff');
    }
    if (!get_option('wp_seo_sales_booster_show_easing')) {
        update_option('wp_seo_sales_booster_show_easing', 'swing');
    }
    if (!get_option('wp_seo_sales_booster_hide_easing')) {
        update_option('wp_seo_sales_booster_hide_easing', 'linear');
    }
    if (!get_option('wp_seo_sales_booster_show_duration')) {
        update_option('wp_seo_sales_booster_show_duration', '300');
    }
    if (!get_option('wp_seo_sales_booster_hide_duration')) {
        update_option('wp_seo_sales_booster_hide_duration', '1000');
    }
    if (!get_option('wp_seo_sales_booster_time_out')) {
        update_option('wp_seo_sales_booster_time_out', '5000');
    }
    if (!get_option('wp_seo_sales_booster_extended_time_out')) {
        update_option('wp_seo_sales_booster_extended_time_out', '1000');
    }
    if (!get_option('wp_seo_sales_booster_auto_image_attribute')) {
        update_option('wp_seo_sales_booster_auto_image_attribute', 'enable');
    }
    if (!get_option('wp_seo_sales_booster_image_attribute_settings')) {
        update_option('wp_seo_sales_booster_image_attribute_settings', ['image_title', 'image_caption', 'image_description', 'image_alt']);
    }

}
// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'wssb_on_activate');

//Redirect to setting page.
if (!function_exists('wp_seo_sales_booster_settings_redirect')) {
    function wp_seo_sales_booster_settings_redirect($plugin)
    {
        if ($plugin == plugin_basename(__FILE__)) {
            wp_redirect(admin_url('admin.php?page=wssb-settings'));
            exit();
        }
    }
    add_action('activated_plugin', 'wp_seo_sales_booster_settings_redirect');
}

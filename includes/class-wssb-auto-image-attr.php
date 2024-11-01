<?php

if (!defined('ABSPATH')) {
    exit;
}
// if (get_option('wp_seo_sales_booster_auto_image_attr') == 'enable') {
    require __DIR__ . '/../vendor/autoload.php';
// }
class Wssb_Auto_Image_Attr
{
    // public $wssb_remaining_image = 1;
    public function __construct()
    {
        self::init();
        // self::wssb_remaining_image = $this -> wssb_count_remaining_images();
    }
    public function init()
    {

        add_action('add_attachment', array($this, 'wssb_auto_image_attributes'));
        add_action('wp_ajax_wssb_update_old_image', array($this, 'wssb_update_old_image'));
        add_action('wp_ajax_wssb_count_remaining_images', array($this, 'wssb_count_remaining_images'));
        add_action('wp_ajax_wssb_reset_bulk_updater_counter', array($this, 'wssb_reset_bulk_updater_counter'));
    }
    public function wssb_auto_image_attributes($post_id)
    {
        // Return if attachment is not an image
        if (!wp_attachment_is_image($post_id))
            return;
        // Retrieve image object from its ID
        $image = get_post($post_id);

        // Get the image name from filename
        $image_name = self::Wssb_image_name_from_filename($image->ID);

        // Update image attributes
        self::wssb_update_image($image->ID, $image_name);
    }
    public function Wssb_image_name_from_filename($image_id, $bulk = false)
    {
        // Return if no image ID is passed
        if ($image_id === NULL) return;

        // Extract the image name from the image url
        $image_url            = wp_get_attachment_url($image_id);
        $image_extension     = pathinfo($image_url);
        $image_name         = basename($image_url, '.' . $image_extension['extension']);

        if ($bulk === true) {

            $image_name = str_replace('-', ' ', $image_name);    // replace hyphens with spaces
            $image_name = str_replace('_', ' ', $image_name);    // replace underscores with spaces
            return $image_name;
        }

        // Final cleanup
        $image_name = preg_replace('/\s\s+/', ' ', $image_name); // Replace multiple spaces with a single spaces
        $image_name = trim($image_name);        // Remove white spaces from both ends

        return $image_name;
    }

    function wssb_update_image($image_id, $text, $bulk = false)
    {

        // Return if no image ID is passed
        if ($image_id === NULL) return false;

        // Get Settings
        $general_settings = get_option('wp_seo_sales_booster_image_attribute_settings');

        $image            = array();
        $image['ID']     = $image_id;

        if ($bulk == true) {

            $image['post_title']     = $text;    // Image Title
            $image['post_excerpt']     = $text;    // Image Caption
            $image['post_content']     = $text;    // Image Description

            // Update Image Alt Text (stored in post_meta table)
            update_post_meta($image_id, '_wp_attachment_image_alt', $text); // Image Alt Text
        } else {

            if (isset($general_settings) && in_array('image_title', $general_settings)) {
                $image['post_title']     = $text;    // Image Title
            }
            if (isset($general_settings) && in_array('image_caption', $general_settings)) {
                $image['post_excerpt'] = $text;    // Image Caption
            }
            if (isset($general_settings) && in_array('image_description', $general_settings)) {
                $image['post_content'] = $text;    // Image Description
            }
            if (isset($general_settings) && in_array('image_alt', $general_settings)) {
                update_post_meta($image_id, '_wp_attachment_image_alt', $text); // Image Alt Text
            }
        }


        $return_id = wp_update_post($image); // Retruns the ID of the post if the post is successfully updated in the database. Otherwise returns 0.

        if ($return_id == 0) return false;

        return true;
    }
}
new Wssb_Auto_Image_Attr();

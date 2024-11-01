<?php

if (!defined('ABSPATH')) {
    exit;
}
// require __DIR__ . '/../../vendor/autoload.php';
class Wp_Seo_Sales_Booster_Settings
{
    /**
     * The single instance of Wp_Seo_Sales_Booster_Plugin_Settings.
     * @var     object
     * @access  private
     * @since     1.0.0
     */
    private static $_instance = null;

    /**
     * The main plugin object.
     * @var     object
     * @access  public
     * @since     1.0.0
     */
    public $parent = null;

    /**
     * Prefix for plugin settings.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $base = '';

    /**
     * Available settings for plugin.
     * @var     array
     * @access  public
     * @since   1.0.0
     */
    public $settings = array();

    public function __construct($parent)
    {
        $this->parent = $parent;

        $this->base = 'wp_seo_sales_booster_';

        // Initialize settings

        add_action('init', array($this, 'init_settings'), 11);

        // Register plugin settings
        add_action('admin_init', array($this, 'register_settings'));

        // Add settings page to menu
        add_action('admin_menu', array($this, 'add_menu_item'));
        /**
         * Have to include all others page here .
         */
        require_once 'wssb-info-page.php';
        require_once 'wssb-pro-link.php';

        // Add settings link to plugins page
        //add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );
    }

    /**
     * Initialise settings
     * @return void
     */
    public function init_settings()
    {
        $this->settings = $this->settings_fields();
    }

    /**
     * Add settings page to admin menu
     * @return void
     */
    public function add_menu_item()
    {
        add_submenu_page(
            'wp_seo_sales_booster',
            'Settings',
            'Settings',
            'import',
            'wssb-settings',
            array($this, 'settings_page')
        );
    }


    /**
     * Add settings link to plugin list table
     * @param  array $links Existing links
     * @return array         Modified links
     */
    public function add_settings_link($links)
    {
        $settings_link = '<a href="options-general.php?page=' . $this->parent->_token . '_settings">' . __('Settings', 'wp_seo_sales_booster') . '</a>';
        array_push($links, $settings_link);
        return $links;
    }

    /**
     * Build settings fields
     * @return array Fields to be displayed on settings page
     */
    public function settings_fields()
    {
        $settings['wssb_general_settings'] = array(
            'title' => __('General', 'wp_seo_sales_booster'),
            'description' => __('In this general settings page you will get five great features of this plugin. Just enable the features and boost-up your seo & sales.', 'wp_seo_sales_booster'),
            'fields' => array(
                array(
                    'id' => 'keyword_planner',
                    'label' => __('Keyword Planner', 'wp_seo_sales_booster'),
                    'type' => 'radio',
                    'options' => array('enable' => 'Enabled', 'disable' => 'Disabled'),
                    'default' => 'enable',
                    'tooltip' => 'to run the google adwords keyword planner feature then enable it.'
                ),
                array(
                    'id' => 'conversion_tracking',
                    'label' => __('Conversion Tracking', 'wp_seo_sales_booster'),
                    'type' => 'radio',
                    'options' => array('enable' => 'Enabled', 'disable' => 'Disabled'),
                    'default' => 'enable',
                    'tooltip' => 'before enable the feature must need to be install woocommerce plguin'
                ),
                array(
                    'id' => 'internal_link',
                    'label' => __('Internal Link', 'wp_seo_sales_booster'),
                    'type' => 'radio',
                    'options' => array('enable' => 'Enabled', 'disable' => 'Disabled'),
                    'default' => 'enable',
                    'tooltip' => 'to run the internal link feature then enable it.'

                ),
                array(
                    'id' => 'live_sales_notification',
                    'label' => __('Live Sales Notification', 'wp_seo_sales_booster'),
                    'type' => 'radio',
                    'options' => array('enable' => 'Enabled', 'disable' => 'Disabled'),
                    'default' => 'enable',
                    'tooltip' => 'before enable the feature must need to be install woocommerce plguin'
                ),
                array(
                    'id' => 'auto_image_attr',
                    'label' => __('Auto Image Attribute', 'wp_seo_sales_booster'),
                    'type' => 'radio',
                    'options' => array('enable' => 'Enabled', 'disable' => 'Disabled'),
                    'default' => 'enable',
                    'tooltip' => 'to run the auto image attribute feature then enable it.'
                ),
            ),
        );
        if (get_option('wp_seo_sales_booster_keyword_planner') == 'enable') {
            $settings['wssb_keyword_planner'] = array(
                'title' => __('Keyword Planner', 'wp_seo_sales_booster'),
                'description' => __('By using our keyword planner features you can easily find out the profitable keywords for your business and   also you can save your time by adding auto yoast focus kw, add to tags and saving kw list', 'wp_seo_sales_booster'),
                'fields' => array(
                    array(
                        'id' => 'customer_client_id',
                        'label' => __('Customer Client Id', 'wp_seo_sales_booster'),
                        'type' => 'text',
                        'default' => '',
                        'placeholder' => 'XXX-XXX-XXXX',
                        'description' => 'For customer client id <a href="http://adwords.google.com/">click here </a> & For video tutorial <a href="https://www.youtube.com/watch?v=_3IGVOFtmbY&feature=youtu.be"> click here </a>',
                        'tooltip' => 'set your customer client id',

                    ),
                    // array(
                    //     'id'             => 'own_api_key',
                    //     'label'            => __('Do you want to use your own API keys ?', 'wp_seo_sales_booster'),
                    //     'description'    => __('If Enable Amazon Search Globally option is checked then it will work for all search forms.', 'wp_seo_sales_booster'),
                    //     'type'            => 'checkbox',
                    //     'default'        => 'off',
                    //     'tooltip' => 'set your customer client id'
                    // ),
                    array(
                        'id' => 'client_id',
                        'label' => __('Client Id', 'wp_seo_sales_booster'),
                        'type' => 'text',
                        // 'class' => 'hidden',
                        'placeholder' => '',
                        'description' => 'For client id, secret id & redirect url <a href="https://console.developers.google.com/">click here </a> & see video tutorial <a href="https://www.youtube.com/watch?v=7RRxVqdmGvE&feature=youtu.be"> click here </a>',
                        'tooltip' => 'set your client id'
                    ),
                    array(
                        'id' => 'client_secret',
                        'label' => __('Client Secret', 'wp_seo_sales_booster'),
                        'type' => 'text',
                        // 'class' => 'hidden',
                        'placeholder' => '',
                        'description' => 'For client id, secret id & redirect url <a href="https://console.developers.google.com/">click here </a> & see video tutorial <a href="https://www.youtube.com/watch?v=7RRxVqdmGvE&feature=youtu.be"> click here </a>',
                        'tooltip' => 'set client secret id'
                    ),
                    array(
                        'id' => 'redirect_url',
                        'label' => __('Redirect url', 'wp_seo_sales_booster'),
                        'type' => 'text',
                        // 'class' => 'hidden',
                        'placeholder' => '',
                        'description' => 'For client id, secret id & redirect url <a href="https://console.developers.google.com/">click here </a> & see video tutorial <a href="https://www.youtube.com/watch?v=7RRxVqdmGvE&feature=youtu.be"> click here </a>',
                        'tooltip' => 'set redirect url'
                    )
                )
            );
        }
        if (get_option('wp_seo_sales_booster_conversion_tracking') == 'enable') {
            $settings['wssb_conversion_tracking'] = array(
                'title' => __('Conversion Tracking', 'wp_seo_sales_booster'),
                'description' => __('Connect your website with Ad Platforms like Facebook, Twitter, Google and send conversion data.', 'wp_seo_sales_booster'),
                'fields' => array(
                    array(
                        'id'             => 'conversion',
                        'label'            => __('Events', 'wp_seo_sales_booster'),
                        'description'    => __('You can select multiple items and they will be stored as an array.', 'wp_seo_sales_booster'),
                        'type'            => 'checkbox_multi',
                        'options'        => array('google' => 'Google', 'facebook' => 'Facebook', 'twitter' => 'Twitter', 'custom' => 'Custom'),
                        'tooltip' => 'check the box to enable the feature of facebook,  twitter, google adwords and custom.'
                    ),
                )
            );
        }
        if (get_option('wp_seo_sales_booster_live_sales_notification') == 'enable') {
            $settings['wssb_live_sales_notification'] = array(
                'title' => __('Live Sales Notification', 'wp_seo_sales_booster'),
                'description' => __('Live Sales Notification for Frontend Users (Recent Sales PopUps or Custom Sales Notifications)', 'wp_seo_sales_booster'),
                'fields' => array(
                    array(
                        'id' => 'notification_type',
                        'label' => __('Notification Type', 'wp_seo_sales_booster'),
                        'type' => 'checkbox_multi',
                        'options' => array('live_sales_notification' => 'Live Sales Notification'),
                        'tooltip' => 'if checked live sales notifications then your users will see the last 24 hours sales notifications from frontend and for custom sales notification whatever you want you can show to your customers.'
                    ),
                    array(
                        'id' => 'notification_show',
                        'label' => __('Notification Show In', 'wp_seo_sales_booster'),
                        'type' => 'checkbox_multi',
                        'options' => array('shop' => 'Shop Page', 'category' => 'Category Page', 'product' => 'Single Product Page'),
                        'default' => array('shop', 'category', 'product'),
                        'tooltip' => 'your live or custom sales notifications will be displayed for shop, prodcut single page or shop category pages'
                    ),
                    array(
                        'id' => 'close_button',
                        'label' => __('Close Button', 'wp_seo_sales_booster'),
                        'type' => 'radio',
                        'options' => array('1' => 'Enabled', '0' => 'Disabled'),
                        'default' => '1',
                        'tooltip' => 'notifications close button enable or disable'
                    ),
                    array(
                        'id' => 'progress_bar',
                        'label' => __('Progress Bar', 'wp_seo_sales_booster'),
                        'type' => 'radio',
                        'options' => array('1' => 'Enabled', '0' => 'Disabled'),
                        'default' => 'enable',
                        'tooltip' => 'progress bar of the notifications'
                    ),
                    array(
                        'id' => 'font_color',
                        'label' => __('Font Color', 'wp_seo_sales_booster'),
                        'type' => 'color',
                        'options'  => '',
                        'default' => '#ffffff',
                        'tooltip' => 'choose your live sales or custom notofuications font color'
                    ),
                    array(
                        'id' => 'show_easing',
                        'label' => __('Show Easing', 'wp_seo_sales_booster'),
                        'type' => 'radio',
                        'options' => array('swing' => 'Swing', 'linear' => 'Linear'),
                        'default' => 'swing',
                        'tooltip' => 'choose easing'
                    ),
                    array(
                        'id' => 'hide_easing',
                        'label' => __('Hide Easing', 'wp_seo_sales_booster'),
                        'type' => 'radio',
                        'options' => array('swing' => 'Swing', 'linear' => 'Linear'),
                        'default' => 'linear',
                        'tooltip' => 'choose linear easing'
                    ),
                    array(
                        'id' => 'show_duration',
                        'label' => __('Show Duration', 'wp_seo_sales_booster'),
                        'type' => 'text',
                        'default' => '300',
                        'placeholder' => '',
                        'description' => '',
                        'tooltip' => 'set your duration time'
                    ),
                    array(
                        'id' => 'hide_duration',
                        'label' => __('Hide Duration', 'wp_seo_sales_booster'),
                        'type' => 'text',
                        'default' => '1000',
                        'placeholder' => '',
                        'description' => '',
                        'tooltip' => 'set hide duration time'
                    ),
                    array(
                        'id' => 'time_out',
                        'label' => __('Time out', 'wp_seo_sales_booster'),
                        'type' => 'text',
                        'default' => '5000',
                        'placeholder' => '',
                        'description' => '',
                        'tooltip' => 'set when dialog box time out'
                    ),
                    array(
                        'id' => 'extended_time_out',
                        'label' => __('Extended time out', 'wp_seo_sales_booster'),
                        'type' => 'text',
                        'default' => '1000',
                        'placeholder' => '',
                        'description' => '',
                        'tooltip' => 'set when dialog box extended time out'
                    ),

                )

            );
        }
        if (get_option('wp_seo_sales_booster_auto_image_attr') == 'enable') {
            $settings['wssb_auto_image_attribute'] = array(
                'title' => __('Auto Image Attribute', 'wp_seo_sales_booster'),
                'description' => __('Auto image attribute from your images file name will boost-up your seo dramatically because no need to concentrate for your image alt text, caption, desc or title', 'wp_seo_sales_booster'),
                'fields' => array(
                    array(
                        'id' => 'auto_image_attribute',
                        'label' => __('Auto Image Attribute', 'wp_seo_sales_booster'),
                        'type' => 'radio',
                        'options' => array('enable' => 'Enabled', 'disable' => 'Disabled'),
                        'default' => 'enable',
                        'tooltip' => 'enable the feature'
                    ),
                    array(
                        'id' => 'image_attribute_settings',
                        'label' => __('General Settings', 'wp_seo_sales_booster'),
                        'type' => 'checkbox_multi',
                        'options' => array('image_title' => 'Set Image Title for new uploads', 'image_caption' => ' Set Image Caption for new uploads', 'image_description' => 'Set Image Description for new uploads', 'image_alt' => 'Set Image Alt Text for new uploads'),
                        'default' => array('image_title', 'image_caption', 'image_description', 'image_alt'),
                        'tooltip' => 'set your image attribute options'
                    ),
                )
            );
        }


        //import templates settings
        /*if (class_exists('TESTING_TM')) {
        $options_class= new TESTING_TM();
        $option=$options_class->general_options();
        array_push($settings['wooain_general']['fields'],$option);
        }*/
        $settings = apply_filters($this->parent->_token . '_settings_fields', $settings);
        return $settings;
    }

    /**
     * Register plugin settings
     * @return void
     */
    public function register_settings()
    {
        if (is_array($this->settings)) {

            // Check posted/selected tab
            $current_section = '';
            if (isset($_POST['tab']) && $_POST['tab']) {
                $current_section = sanitize_text_field($_POST['tab']);
            } else {
                if (isset($_GET['tab']) && $_GET['tab']) {
                    $current_section = sanitize_text_field($_GET['tab']);
                }
            }

            foreach ($this->settings as $section => $data) {

                if ($current_section && $current_section != $section) {
                    continue;
                }
                // var_dump(get_option('wp_seo_sales_booster_own_api_key'));exit;
                // Add section to page
                add_settings_section($section, $data['title'], array($this, 'settings_section'), $this->parent->_token . '_settings');
                foreach ($data['fields'] as $field) {
                    // var_dump($field);exit;
                    // Validation callback for field
                    $validation = '';
                    if (isset($field['callback'])) {
                        $validation = $field['callback'];
                    }
                    // Register field
                    $option_name = $this->base . $field['id'];
                    register_setting($this->parent->_token . '_settings', $option_name, $validation);
                    // Add field to page
                    // if (isset($field['class'])) {
                    //     if (empty(get_option('wp_seo_sales_booster_own_api_key'))) {
                    //         add_settings_field($field['id'], $field['label'], array($this->parent->admin, 'display_field'), $this->parent->_token . '_settings', $section,  array('field' => $field, 'prefix' => $this->base, 'class' => 'hidden wssb_own_api'));
                    //     } else {
                    //         add_settings_field($field['id'], $field['label'], array($this->parent->admin, 'display_field'), $this->parent->_token . '_settings', $section,  array('field' => $field, 'prefix' => $this->base, 'class' => 'wssb_own_api'));
                    //     }
                    // } else {
                    add_settings_field($field['id'], $field['label'], array($this->parent->admin, 'display_field'), $this->parent->_token . '_settings', $section, array('field' => $field, 'prefix' => $this->base));
                    // }
                }
                if (!$current_section) {
                    break;
                }
            }
        }
    }

    public function settings_section($section)
    {
        $html = '<p> ' . $this->settings[$section['id']]['description'] . '</p>' . "\n";
        echo $html;
    }

    /**
     * Load settings page content
     * @return void
     */
    public function settings_page()
    {
        // var_dump($instance->aaa);exit;
        // Build page HTML
        $html = '<div class="wrap" id="' . $this->parent->_token . '_settings">' . "\n";
        $html .= '<h2>' . __('WP SEO & Sales Booster Settings', 'wp_seo_sales_booster') . '</h2>' . "\n";

        $tab = '';
        if (isset($_GET['tab']) && $_GET['tab']) {
            $tab .= sanitize_text_field($_GET['tab']);
        }

        // Show page tabs
        if (is_array($this->settings) && 1 < count($this->settings)) {
            $html .= '<h2 class="nav-tab-wrapper">' . "\n";

            $c = 0;
            foreach ($this->settings as $section => $data) {

                // Set tab class
                $class = 'nav-tab';
                if (!isset($_GET['tab'])) {
                    if (0 == $c) {
                        $class .= ' nav-tab-active';
                    }
                } else {
                    if (isset($_GET['tab']) && $section == $_GET['tab']) {
                        $class .= ' nav-tab-active';
                    }
                }

                // Set tab link
                $tab_link = add_query_arg(array('tab' => $section));
                if (isset($_GET['settings-updated'])) {
                    $tab_link = remove_query_arg('settings-updated', $tab_link);
                }

                // Output tab
                $html .= '<a href="' . $tab_link . '" class="' . esc_attr($class) . '">' . esc_html($data['title']) . '</a>' . "\n";

                ++$c;
            }
            $html .= '</h2>' . "\n";
        }
        $html .= '<form method="post" class="form_settings" action="options.php" enctype="multipart/form-data">' . "\n";
        // Get settings fields
        ob_start();
        settings_fields($this->parent->_token . '_settings');
        do_settings_sections($this->parent->_token . '_settings');

        $html .= ob_get_clean();

        $html .= '<p class="submit">' . "\n";
        $html .= '<input type="hidden" name="tab" value="' . esc_attr($tab) . '" />' . "\n";
        $html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr(__('Save Settings', 'wp_seo_sales_booster')) . '" />' . "\n";
        $html .= '</p>' . "\n";
        $html .= '</form>' . "\n";
        $html .= '</div>' . "\n";

        echo $html;
    }
    /**
     * Main Wp_Seo_Sales_Booster_Plugin_Settings Instance
     *
     * Ensures only one instance of Wp_Seo_Sales_Booster_Plugin_Settings is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see Wp_Seo_Sales_Booster_Plugin()
     * @return Main Wp_Seo_Sales_Booster_Plugin_Settings instance
     */
    public static function instance($parent)
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($parent);
        }
        return self::$_instance;
    } // End instance()

    /**
     * Cloning is forbidden.
     *
     * @since 1.0.0
     */
    public function __clone()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->parent->_version);
    } // End __clone()

    /**
     * Unserializing instances of this class is forbidden.
     *
     * @since 1.0.0
     */
    public function __wakeup()
    {
        _doing_it_wrong(__FUNCTION__, __('Cheatin&#8217; huh?'), $this->parent->_version);
    } // End __wakeup()
}
//new ACL_Wp_Seo_Sales_Booster_Settings($parent);
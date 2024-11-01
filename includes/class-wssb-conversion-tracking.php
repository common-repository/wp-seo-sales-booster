<?php

if (!defined('ABSPATH')) {
    exit;
}

class Wssb_Conversion_Tracking
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
    public $conversion_settings = array();

    public $facebook_events = array();
    //public $twitter_events = array();
    public $twitter_events;

    public function __construct($parent)
    {

        $this->parent = $parent;
        $this->base = 'wp_seo_sales_booster_';
        $this->facebook_events[] = get_option($this->base . 'facebook_events');
        $this->twitter_events = get_option($this->base . 'twitter_events');

        // var_dump($this->facebook_events);exit;



        add_action('wp_head', array($this, 'enqueue_script'));
        add_action('wp_head', array($this, 'enqueue_facebook_script'));
        add_action('wp_head', array($this, 'enqueue_twitter_script'));
        add_action('woocommerce_thankyou', array($this, 'thankyou_page'));
        add_action('woocommerce_thankyou', array($this, 'purchase'));
        add_action('woocommerce_thankyou', array($this, 'checkout_twitter_script'));
        add_action('woocommerce_thankyou', array($this, 'custom_checkout_script'));
        add_action('woocommerce_registration_redirect', array($this, 'wc_redirect_url'));


        add_action('woocommerce_add_to_cart', array($this, 'add_to_cart'), 9999, 4);
        add_action('woocommerce_after_checkout_form', array($this, 'initiate_checkout'));
        // Initialize settings

        add_action('init', array($this, 'init_conversion_settings'), 11);

        // Register plugin settings
        add_action('admin_init', array($this, 'register_conversion_settings'));

        // Add settings page to menu
        add_action('admin_menu', array($this, 'add_menu_item'));
        /**
         * Have to include all others page here .
         */
        // add_action('wp_enqueue_scripts', array($this, 'settings_assets'));
        // Add settings link to plugins page
        //add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );
        //add_action('woocommerce_created_customer', araay($this,'get_new_customer_mail'), 10 , 3);

    }

    /**
     * Initialise settings
     * @return void
     */
    public function init_conversion_settings()
    {
        $this->conversion_settings = $this->conversion_settings_fields();
    }

    /**
     * Add settings page to admin menu
     * @return void
     */
    public function add_menu_item()
    {
        $menu_page      = apply_filters('wssb_menu_page', 'wp_seo_sales_booster');
        $capability     = apply_filters('wssb_capability', 'manage_options');

        add_submenu_page($menu_page, __('Conversion Tracking', 'wp_seo_sales_booster'), __('Conversion Tracking', 'wp_seo_sales_booster'), $capability, 'wssb-conversion-tracking', array($this, 'wssb_conversion_tracking_template'), 1);
    }

    /**
     * Build settings fields
     * @return array Fields to be displayed on settings page
     */

    public function conversion_settings_fields()
    {
        if (in_array('google', get_option('wp_seo_sales_booster_conversion'))) {
            $conversion_settings['wssb_google_conversion_tracking'] = array(
                'title' => __('Google', 'wp_seo_sales_booster'),
                'description' => __('Select below template to display the products as default template', 'wp_seo_sales_booster'),
                'fields' => array(

                    array(
                        'id'             => 'account_id',
                        'label'            => __('Account Id', 'wp_seo_sales_booster'),
                        'description'    => __("", 'wp_seo_sales_booster'),
                        'type'            => 'text',
                        'default'        => '',
                        'placeholder'    => __('AW-123456789', 'wp_seo_sales_booster'),
                        'tooltip' => 'hello world'
                    ),
                    array(
                        'id'             => 'events',
                        'label'            => __('Events', 'wp_seo_sales_booster'),
                        'description'    => __('You can select multiple items and they will be stored as an array.', 'wp_seo_sales_booster'),
                        'type'            => 'checkbox_multi_google',
                        //'options'        => array( 'purchase' => 'Purchase', 'sign-up' => 'Sign-up')
                        'options' => array(
                            'Purchase'  => array(
                                'event_label_box'   => true,
                                'label'             => __('Purchase', 'wp_seo_sales_booster'),
                                'label_name'       => 'label',
                                'placeholder'      => 'Add Your Purchase Label'
                            )
                        )
                    ),
                ),
            );
        }
        if (in_array('facebook', get_option('wp_seo_sales_booster_conversion'))) {
            $conversion_settings['wp_seo_sales_booster_facebook'] = array(
                'title' => __('Facebook', 'wp_seo_sales_booster'),
                'description' => __('SLorem Ipsum is simply dummy text of the printing and typesetting industry.', 'wp_seo_sales_booster'),
                'fields' => array(
                    array(
                        'id'             => 'pixel_id',
                        'label'            => __('Pixel Id', 'wp_seo_sales_booster'),
                        'description'    => __("Find the Pixel ID from here.", 'wp_seo_sales_booster'),
                        'type'            => 'text',
                        'default'        => '',
                        'placeholder'    => __('AW-123456789', 'wp_seo_sales_booster'),
                        'tooltip' => 'hello world'
                    ),
                    array(
                        'id'             => 'facebook_events',
                        'label'            => __('Events', 'wp_seo_sales_booster'),
                        'description'    => __('You can select multiple items and they will be stored as an array.', 'wp_seo_sales_booster'),
                        'type'            => 'checkbox_multi',
                        'options'        => array('add-to-cart' => 'AddToCart', 'initial-checkout' => 'InitiateCheckout', 'purchase' => 'Purchase', 'registration' => 'Registration'),
                        'tooltip' => 'hello world'
                    ),
                ),
            );
        }
        if (in_array('twitter', get_option('wp_seo_sales_booster_conversion'))) {
            $conversion_settings['wp_seo_sales_booster_twitter'] = array(
                'title' => __('Twitter', 'wp_seo_sales_booster'),
                'description' => __('Lorem Ipsum is simply dummy text of the printing and typesetting industry.', 'wp_seo_sales_booster'),
                'fields' => array(
                    array(
                        'id'             => 'twitter_events',
                        'label'            => __('Events', 'wp_seo_sales_booster'),
                        'description'    => __('You can select multiple items and they will be stored as an array.', 'wp_seo_sales_booster'),
                        'type'            => 'checkbox_multi_twitter',
                        //'options'        => array( 'purchase' => 'Purchase', 'sign-up' => 'Sign-up')
                        'options' => array(
                            'Purchase'  => array(
                                'event_label_box'   => true,
                                'label'             => __('Purchase', 'wp_seo_sales_booster'),
                                'label_name'       => 'label',
                                'placeholder'      => 'Add Your Universal Tag'

                            )
                        )
                    ),
                ),
            );
        }
        if (in_array('custom', get_option('wp_seo_sales_booster_conversion'))) {
            $conversion_settings['wp_seo_sales_booster_custom'] = array(
                'title' => __('Custom', 'wp_seo_sales_booster'),
                'description' => __('Lorem Ipsum is simply dummy text of the printing and typesetting industry.', 'wp_seo_sales_booster'),
                'fields' => array(
                    array(
                        'id'             => 'custom_order_script',
                        'label'            => __('Order Completion', 'wp_seo_sales_booster'),
                        'description'    => __("Put your JavaScript tracking scripts here. ", 'wp_seo_sales_booster'),
                        'type'            => 'textarea',
                        'default'        => '',
                        'placeholder'    => __('', 'wp_seo_sales_booster')
                    )
                ),
            );
        }
        //import templates settings
        /*if (class_exists('TESTING_TM')) {
        $options_class= new TESTING_TM();
        $option=$options_class->general_options();
        array_push($settings['wooain_general']['fields'],$option);
        }*/
        $conversion_settings = apply_filters($this->parent->_token . '_conversion_settings_fields', $conversion_settings);
        return $conversion_settings;
    }

    /**
     * Register plugin settings
     * @return void
     */
    public function register_conversion_settings()
    {
        if (is_array($this->conversion_settings)) {

            // Check posted/selected tab
            $current_section = '';
            if (isset($_POST['tab']) && $_POST['tab']) {
                $current_section = sanitize_text_field($_POST['tab']);
            } else {
                if (isset($_GET['tab']) && $_GET['tab']) {
                    $current_section = sanitize_text_field($_GET['tab']);
                }
            }

            foreach ($this->conversion_settings as $section => $data) {

                if ($current_section && $current_section != $section) {
                    continue;
                }
                // Add section to page
                add_settings_section($section, $data['title'], array($this, 'conversion_settings_section'), $this->parent->_token . '_conversion_settings');
                foreach ($data['fields'] as $field) {
                    // Validation callback for field
                    $validation = '';
                    if (isset($field['callback'])) {
                        $validation = $field['callback'];
                    }
                    // Register field

                    $option_name = $this->base . $field['id'];

                    register_setting($this->parent->_token . '_conversion_settings', $option_name, $validation);
                    // Add field to page
                    add_settings_field($field['id'], $field['label'], array($this->parent->admin, 'display_field'), $this->parent->_token . '_conversion_settings', $section, array('field' => $field, 'prefix' => $this->base));
                }
                //var_dump($validation);exit;
                register_setting($this->parent->_token . '_conversion_settings', $option_name . '_label', '');
                register_setting($this->parent->_token . '_conversion_settings', $option_name . '_sign', '');
                if (!$current_section) {
                    break;
                }
            }
        }
    }

    public function conversion_settings_section($section)
    {
        $html = '<p> ' . $this->conversion_settings[$section['id']]['description'] . '</p>' . "\n";
        echo $html;
    }

    /**
     * Load settings page content
     * @return void
     */
    public function wssb_conversion_tracking_template()
    {

        // Build page HTML
        $html = '<div class="wrap" id="' . $this->parent->_token . '_conversion_settings">' . "\n";
        $html .= '<h2>' . __('WooCommerce General Settings', 'wp_seo_sales_booster') . '</h2>' . "\n";

        $tab = '';
        if (isset($_GET['tab']) && $_GET['tab']) {
            $tab .= sanitize_text_field($_GET['tab']);
        }

        // Show page tabs
        if (is_array($this->conversion_settings) && 1 < count($this->conversion_settings)) {

            $html .= '<h2 class="nav-tab-wrapper">' . "\n";

            $c = 0;
            //var_dump($this->conversion_settings);exit;
            foreach ($this->conversion_settings as $section => $data) {

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
        if (isset($_GET['tab']) && 'wooain_store' == $_GET['tab']) {
            $html .= '<img src="' . ACL_Wp_Seo_Sales_Booster_IMG_URL . 'drop-shipping-pro.png" alt="Drop Shipping Settings Pro Features">' . "\n";
        } else {
            if ($tab !== "wooain_ajax_search") {
                $html .= '<form class="form_settings" method="post" action="options.php" enctype="multipart/form-data">' . "\n";

                // Get settings fields
                ob_start();
                settings_fields($this->parent->_token . '_conversion_settings');
                do_settings_sections($this->parent->_token . '_conversion_settings');
                $html .= ob_get_clean();

                $html .= '<p class="submit">' . "\n";
                $html .= '<input type="hidden" name="tab" value="' . esc_attr($tab) . '" />' . "\n";
                $html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr(__('Save Settings', 'wp_seo_sales_booster')) . '" />' . "\n";
                $html .= '</p>' . "\n";
                $html .= '</form>' . "\n";
                $html .= '</div>' . "\n";
            } else {
                ob_start();
                settings_fields($this->parent->_token . '_conversion_settings');
                do_settings_sections($this->parent->_token . '_conversion_settings');
                $html .= ob_get_clean();
            }
        }

        echo $html;
    }

    public function enqueue_script()
    {
        if (!get_option('wp_seo_sales_booster_events')) {
            return;
        }
        $account_id = !empty(get_option('wp_seo_sales_booster_account_id')) ? get_option('wp_seo_sales_booster_account_id') : '';
        if (empty($account_id)) {
            return;
        }
        // 
?>
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo esc_attr($account_id);
                                                                        ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments)
            };
            gtag('js', new Date());

            gtag('config', '<?php echo esc_attr($account_id); ?>');
        </script>
    <?php
    }
    public function build_event_snippet($event_name, $params = array(), $method = 'event')
    {
        return sprintf("gtag('%s', '%s', %s);", $method, $event_name, json_encode($params, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT | JSON_UNESCAPED_SLASHES));
    }
    public function build_facebook_event_snippet($event_name, $params = array(), $method = 'track')
    {
        return sprintf("fbq('%s', '%s', %s);", $method, $event_name, json_encode($params, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT));
    }
    public function build_twitter_event_snippet($event_name, $params = array(), $method = 'track')
    {
        return sprintf("twq('%s', '%s', %s);", $method, $event_name, json_encode($params, JSON_PRETTY_PRINT | JSON_FORCE_OBJECT));
    }
    public function thankyou_page($order_id)
    {
        if (!get_option('wp_seo_sales_booster_events_label')) {
            return;
        }

        //$settings   = $this->get_integration_settings();
        $account_id = !empty(get_option('wp_seo_sales_booster_account_id')) ? get_option('wp_seo_sales_booster_account_id') : '';
        $label  = !empty(get_option('wp_seo_sales_booster_events_label')) ? get_option('wp_seo_sales_booster_events_label') : '';

        if (empty($account_id) || empty($label)) {
            return;
        }

        $order = new WC_Order($order_id);
        foreach ($order->get_items() as $item) {
            $product = wc_get_product($item->get_product_id());
            $item_sku[] = $product->get_sku();
        }
        $sku_srt = implode(",", $item_sku);
        $code = $this->build_event_snippet('conversion', array(
            'send_to'        => sprintf("%s/%s", $account_id, $label),
            'transaction_id' => $order_id,
            'product_sku' => $sku_srt,
            'value'          => $order->get_total() ? $order->get_total() : 0,
            'currency'       => get_woocommerce_currency()
        ));
        wc_enqueue_js($code);
    }

    public function wc_redirect_url($redirect)
    {

        $redirect = add_query_arg(array(
            '_wc_user_reg' => 'true'
        ), $redirect);

        return $redirect;
    }

    public function get_product_ids_from_cart($cart)
    {
        $product_ids = array();

        foreach ($cart as $item) {
            $product_ids[] = $item['data']->get_id();
        }

        return $product_ids;
    }
    public function add_to_cart()
    {
        if (is_array($this->facebook_events) && !in_array("add-to-cart", $this->facebook_events)) {
            return;
        }

        $product_ids = $this->get_product_ids_from_cart(WC()->cart->get_cart());

        $code = $this->build_facebook_event_snippet('AddToCart', array(
            'content_ids'  => json_encode($product_ids),
            'content_type' => 'product',
            'value'        => WC()->cart->total ? WC()->cart->total : 0,
            'currency'     => get_woocommerce_currency()
        ));

        wc_enqueue_js($code);
    }
    public function add_to_cart_ajax()
    {
        if (is_array($this->facebook_events) && !in_array("add-to-cart", $this->facebook_events)) {
            return;
        }

        $facebook_pixel_id      = !empty(get_option('wp_seo_sales_booster_pixel_id')) ? get_option('wp_seo_sales_booster_pixel_id') : '';
    ?>
        <script type="text/javascript">
            jQuery(function($) {
                $(document).on('added_to_cart', function(event, fragments, dhash, button) {
                    var currencySymbol = $($(button.get()[0]).closest('.product')
                        .find('.woocommerce-Price-currencySymbol').get()[0]).text();

                    var price = $(button.get()[0]).closest('.product').find('.amount').text();
                    var originalPrice = price.split(currencySymbol).slice(-1).pop();

                    wcfbq('<?php echo esc_attr($facebook_pixel_id) ?>', 'AddToCart', {
                        content_ids: [$(button).data('product_id')],
                        content_type: 'product',
                        value: originalPrice,
                        currency: '<?php echo esc_attr(get_woocommerce_currency()) ?>'
                    });
                });
            });
        </script>
    <?php
    }
    public function initiate_checkout()
    {
        if (is_array($this->facebook_events) && !in_array("initial-checkout", $this->facebook_events)) {
            return;
        }

        $product_ids = $this->get_product_ids_from_cart(WC()->cart->get_cart());

        $code = $this->build_facebook_event_snippet('initial-checkout', array(
            'num_items'    => WC()->cart->get_cart_contents_count(),
            'content_ids'  => json_encode($product_ids),
            'content_type' => 'product',
            'value'        => WC()->cart->total ? WC()->cart->total : 0,
            'currency'     => get_woocommerce_currency()
        ));

        wc_enqueue_js($code);
    }
    public function purchase($order_id)
    {
        if (is_array($this->facebook_events) && !in_array("purchase", $this->facebook_events)) {
            return;
        }
        $order        = new WC_Order($order_id);
        $content_type = 'product';
        $product_ids  = array();

        foreach ($order->get_items() as $item) {
            $product = wc_get_product($item['product_id']);

            $product_ids[] = $product->get_id();

            if ($product->get_type() === 'variable') {
                $content_type = 'product_group';
            }
        }

        $code = $this->build_facebook_event_snippet('Purchase', array(
            'content_ids'  => json_encode($product_ids),
            'content_type' => $content_type,
            'value'        => $order->get_total() ? $order->get_total() : 0,
            'currency'     => get_woocommerce_currency()
        ));

        wc_enqueue_js($code);
    }
    public function registration_script()
    {
        if (is_array($this->facebook_events) && !in_array("registration", $this->facebook_events)) {
            return;
        }


        $code = $this->build_facebook_event_snippet('Registration');
        wc_enqueue_js($code);
    }
    public function track_registration()
    {
        if (isset($_GET['_wc_user_reg']) && $_GET['_wc_user_reg'] == 'true') {
            $this->registration_script();
        }
    }

    public function enqueue_facebook_script()
    {

        if (get_option('wp_seo_sales_booster_facebook_events') == null) {
            return;
        }
        $facebook_pixel_id      = !empty(get_option('wp_seo_sales_booster_pixel_id')) ? get_option('wp_seo_sales_booster_pixel_id') : '';
    ?>
        <script>
            ! function(f, b, e, v, n, t, s) {
                if (f.fbq) return;
                n = f.fbq = function() {
                    n.callMethod ?
                        n.callMethod.apply(n, arguments) : n.queue.push(arguments)
                };
                if (!f._fbq) f._fbq = n;
                n.push = n;
                n.loaded = !0;
                n.version = '2.0';
                n.queue = [];
                t = b.createElement(e);
                t.async = !0;
                t.src = v;
                s = b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t, s)
            }(window,
                document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');

            <?php
            if (is_user_logged_in()) {
                $user_email = wp_get_current_user()->user_email;

                echo $this->build_facebook_event_snippet($facebook_pixel_id, array('em' => $user_email), 'init');
            } else {
                echo $this->build_facebook_event_snippet($facebook_pixel_id, array(), 'init');
            }

            echo $this->build_facebook_event_snippet('PageView', array());
            ?>
        </script>
    <?php

        $this->print_facebook_event_script();
        $this->add_to_cart_ajax();
    }

    public function print_facebook_event_script()
    {
    ?>
        <script>
            (function(window, document) {
                if (window.wcfbq) return;
                window.wcfbq = (function() {
                    if (arguments.length > 0) {
                        var pixelId, trackType, contentObj;

                        if (typeof arguments[0] == 'string') pixelId = arguments[0];
                        if (typeof arguments[1] == 'string') trackType = arguments[1];
                        if (typeof arguments[2] == 'object') contentObj = arguments[2];

                        var params = [];
                        if (typeof pixelId === 'string' && pixelId.replace(/\s+/gi, '') != '' &&
                            typeof trackType === 'string' && trackType.replace(/\s+/gi, '')) {
                            params.push('id=' + encodeURIComponent(pixelId));
                            switch (trackType) {
                                case 'PageView':
                                case 'ViewContent':
                                case 'Search':
                                case 'AddToCart':
                                case 'InitiateCheckout':
                                case 'AddPaymentInfo':
                                case 'Lead':
                                case 'CompleteRegistration':
                                case 'Purchase':
                                case 'AddToWishlist':
                                    params.push('ev=' + encodeURIComponent(trackType));
                                    break;
                                default:
                                    return;
                            }

                            params.push('dl=' + encodeURIComponent(document.location.href));
                            if (document.referrer) params.push('rl=' + encodeURIComponent(document.referrer));
                            params.push('if=false');
                            params.push('ts=' + new Date().getTime());

                            if (typeof contentObj == 'object') {
                                for (var u in contentObj) {
                                    if (typeof contentObj[u] == 'object' && contentObj[u] instanceof Array) {
                                        if (contentObj[u].length > 0) {
                                            for (var y = 0; y < contentObj[u].length; y++) {
                                                contentObj[u][y] = (contentObj[u][y] + '').replace(/^\s+|\s+$/gi, '').replace(/\s+/gi, ' ').replace(/,/gi, '§');
                                            }
                                            params.push('cd[' + u + ']=' + encodeURIComponent(contentObj[u].join(',').replace(/^/gi, '[\'').replace(/$/gi, '\']').replace(/,/gi, '\',\'').replace(/§/gi, '\,')));
                                        }
                                    } else if (typeof contentObj[u] == 'string')
                                        params.push('cd[' + u + ']=' + encodeURIComponent(contentObj[u]));
                                }
                            }

                            params.push('v=' + encodeURIComponent('2.7.19'));

                            var imgId = new Date().getTime();
                            var img = document.createElement('img');
                            img.id = 'fb_' + imgId, img.src = 'https://www.facebook.com/tr/?' + params.join('&'), img.width = 1, img.height = 1, img.style = 'display:none;';
                            document.body.appendChild(img);
                            window.setTimeout(function() {
                                var t = document.getElementById('fb_' + imgId);
                                t.parentElement.removeChild(t);
                            }, 1000);
                        }
                    }
                });
            })(window, document);
        </script>
    <?php
    }
    public function enqueue_twitter_script()
    {
        if (empty(get_option('wp_seo_sales_booster_twitter_events'))) {
            return;
        }

        $universal_tag_id    = !empty(get_option('wp_seo_sales_booster_twitter_events_label')) ? get_option('wp_seo_sales_booster_twitter_events_label') : '';
        $advance_event       =   get_option('wp_seo_sales_booster_twitter_events_sign') ? true : false;
    ?>
        <script type="text/javascript">
            ! function(e, t, n, s, u, a) {
                e.twq || (s = e.twq = function() {
                    s.exe ? s.exe.apply(s, arguments) : s.queue.push(arguments);
                }, s.version = '1.1', s.queue = [], u = t.createElement(n), u.async = !0, u.src = '//static.ads-twitter.com/uwt.js', a = t.getElementsByTagName(n)[0], a.parentNode.insertBefore(u, a))
            }(window, document, 'script');

            <?php echo $this->build_twitter_event_snippet($universal_tag_id, array(), 'init'); ?>
            <?php echo $this->build_twitter_event_snippet('PageView'); ?>
        </script>

        <?php if ($advance_event) : ?>
            <script src="//platform.twitter.com/oct.js" type="text/javascript"></script>
        <?php endif; ?>
<?php
    }
    public function checkout_twitter_script($order_id)
    {
        // if (is_array($this->twitter_events) && !in_array("Purchase", $this->twitter_events)) {
        //     return;
        // }
        if (!in_array("Purchase", $this->twitter_events)) {
            return;
        }

        $order        = new WC_Order($order_id);
        $content_type = 'product';
        $product_ids  = array();

        foreach ($order->get_items() as $item) {
            $product = wc_get_product($item['product_id']);

            $product_ids[] = $product->get_id();

            if ($product->get_type() === 'variable') {
                $content_type = 'product_group';
            }
        }
        $code = $this->build_twitter_event_snippet('Purchase', array(
            'content_ids'  => json_encode($product_ids),
            'content_type' => $content_type,
            'value'        => $order->get_total() ? $order->get_total() : 0,
            'currency'     => get_woocommerce_currency()
        ));

        wc_enqueue_js($code);
    }
    public function custom_checkout_script($order_id)
    {
        if (empty(get_option('wp_seo_sales_booster_custom_order_script'))) {
            return;
        }

        $code = get_option('wp_seo_sales_booster_custom_order_script');
        echo $this->process_custom_script($code, $order_id);
    }
    public function process_custom_script($code, $order_id)
    {

        $order = wc_get_order($order_id);

        // bail out if not a valid instance
        if (!is_a($order, 'WC_Order')) {
            return $code;
        }


        $order_currency = $order->get_currency();
        $payment_method = $order->get_payment_method();

        $customer       = $order->get_user();
        $order_currency = $order_currency;
        $order_total    = $order->get_total() ? $order->get_total() : 0;
        $order_number   = $order->get_order_number();
        $order_subtotal = $order->get_subtotal();
        $order_discount = $order->get_total_discount();
        $order_shipping = $order->get_total_shipping();


        // customer details
        if ($customer) {
            $code = str_replace('{customer_id}', $customer->ID, $code);
            $code = str_replace('{customer_email}', $customer->user_email, $code);
            $code = str_replace('{customer_first_name}', $customer->first_name, $code);
            $code = str_replace('{customer_last_name}', $customer->last_name, $code);
        }

        // order details
        $code = str_replace('{payment_method}', $payment_method, $code);
        $code = str_replace('{currency}', $order_currency, $code);
        $code = str_replace('{order_total}', $order_total, $code);
        $code = str_replace('{order_number}', $order_number, $code);
        $code = str_replace('{order_subtotal}', $order_subtotal, $code);
        $code = str_replace('{order_discount}', $order_discount, $code);
        $code = str_replace('{order_shipping}', $order_shipping, $code);

        return $code;
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

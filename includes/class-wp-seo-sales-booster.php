<?php

defined('ABSPATH') || exit;

final class Wp_Seo_Sales_Booster
{
    /**
     * The single instance of WPSEO & Sales Booster Plugin.
     * @var     object
     * @access  private
     * @since     1.0.0
     */
    private static $instance;

    /**
     * Settings class object
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $settings = null;

    /**
     * Conversion class object
     * @var     object
     * @access  public
     * @since   1.0.0
     */
    public $conversion = null;
    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_version;

    /**
     * The token.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_token;

    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The main plugin directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin assets directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * The plugin assets URL.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;

    /**
     * Suffix for Javascripts.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $image_path;

    /**
     * Suffix for Javascripts.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $script_suffix;
    public $data;

    /**
     * The main plugin object.
     * @var     object
     * @access  public
     * @since     1.0.0
     */
    public $parent = null;

    // public $aaa = 1;

    public static function get_instance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor function.
     * @access  public
     * @since   1.0.0
     * @return  void
     */
    public function __construct()
    {
        $this->init();
        $this->includes();
        if (is_admin()) {
            $this->admin = new WP_SEO_SALES_BOOSTER_ADMIN_API();
        }
    }

    public function init()
    {
        add_action('init', array($this, 'register_post_type'));
        add_action('admin_menu', array($this, 'add_menu_item'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_styles'));
    }
    public function includes()
    {
        include_once WP_SEO_SALES_BOOSTER_ABSPATH . 'includes/admin/wssb-admin-api.php';
        include_once WP_SEO_SALES_BOOSTER_ABSPATH . 'includes/admin/wssb-settings.php';
        if (get_option('wp_seo_sales_booster_keyword_planner') == 'enable') {
            include_once WP_SEO_SALES_BOOSTER_ABSPATH . 'includes/class-wssb-get-authenticate-with-google.php';
            include_once WP_SEO_SALES_BOOSTER_ABSPATH . 'includes/class-wssb-keyword-planner.php';
            include_once WP_SEO_SALES_BOOSTER_ABSPATH . 'includes/class-wssb-get-keyword-ideas.php';
        }
        if (get_option('wp_seo_sales_booster_conversion_tracking') == 'enable') {
            include_once WP_SEO_SALES_BOOSTER_ABSPATH . 'includes/class-wssb-conversion-tracking.php';
        }
        if (get_option('wp_seo_sales_booster_internal_link') == 'enable') {
            include_once WP_SEO_SALES_BOOSTER_ABSPATH . 'includes/class-wssb-internal-link.php';
        }
        if (get_option('wp_seo_sales_booster_live_sales_notification') == 'enable') {
            include_once WP_SEO_SALES_BOOSTER_ABSPATH . 'includes/class-wssb-live-sales-notification.php';
        }
        if (get_option('wp_seo_sales_booster_auto_image_attr') == 'enable') {
            include_once WP_SEO_SALES_BOOSTER_ABSPATH . 'includes/class-wssb-auto-image-attr.php';
        }
    }
    // adding sub menu in woocommerce dashboard
    public function add_menu_item()
    {
        add_menu_page(
            'WP SEO & Sales Booster',
            'WP SEO & Sales Booster',
            'manage_option',
            'wp_seo_sales_booster',
            array($this, 'plugin_homepage'),
            WP_SEO_SALES_BOOSTER_URL . 'assets/img/wp_seo_booster.png',
            25

        );
    }

    // enqueue scripts
    public function enqueue_scripts()
    {
        if (is_admin()) {
            wp_enqueue_script('wp_seo_sales_booster_admin_scripts',  WP_SEO_SALES_BOOSTER_URL . 'assets/js/wp_seo_sales_booster_admin_scripts.js', array('jquery', 'jquery-ui-autocomplete', 'jquery-ui-dialog'), '1.0');
            wp_localize_script(
                'wp_seo_sales_booster_admin_scripts',
                'wssb_admin_ajax_object',
                array(
                    'ajax_url' => admin_url('admin-ajax.php')
                )
            );
            wp_register_script('datatables', WP_SEO_SALES_BOOSTER_URL . 'assets/js/jquery.dataTables.min.js', array('jquery'), true);
            wp_enqueue_script('datatables');
            wp_register_script('datatables_bootstrap', WP_SEO_SALES_BOOSTER_URL . 'assets/js/bootstrap.bundle.min.js', array('jquery'), true);
            wp_enqueue_script('datatables_bootstrap');
            //color-picker
            wp_enqueue_script('iris', admin_url('js/iris.min.js'), array('jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch'), false, 1);
        } else {
            wp_enqueue_script('wp_seo_sales_booster_frontend_scripts', WP_SEO_SALES_BOOSTER_URL .'assets/js/wp_seo_sales_booster_frontend_scripts.js', array('jquery'), '1.0');
            wp_localize_script(
                'wp_seo_sales_booster_frontend_scripts',
                'wssb_ajax_object',
                array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'close_button' => get_option('wp_seo_sales_booster_close_button'),
                    'progress_bar' => get_option('wp_seo_sales_booster_progress_bar'),
                    'font_color' => get_option('wp_seo_sales_booster_font_color'),
                    'show_easing' => get_option('wp_seo_sales_booster_show_easing'),
                    'hide_easing' => get_option('wp_seo_sales_booster_hide_easing'),
                    'show_duration' => get_option('wp_seo_sales_booster_show_duration'),
                    'hide_duration' => get_option('wp_seo_sales_booster_hide_duration'),
                    'time_out' => get_option('wp_seo_sales_booster_time_out'),
                    'extended_time_out' => get_option('wp_seo_sales_booster_extended_time_out'),
                    // 
                )
            );
            wp_register_script('toastr', WP_SEO_SALES_BOOSTER_URL . 'assets/js/toastr.min.js', array('jquery'), true);
            wp_enqueue_script('toastr');
        }
    }

    public function enqueue_styles()
    {
        if (is_admin()) {
            wp_enqueue_style('wp_seo_sales_booster_styles',  WP_SEO_SALES_BOOSTER_URL . 'assets/css/wp_seo_sales_booster_admin_styles.css');
            wp_register_style('bootstrap_style', WP_SEO_SALES_BOOSTER_URL . 'assets/css/bootstrap.min.css');
            wp_enqueue_style('bootstrap_style');
            wp_register_style('datatables_style', WP_SEO_SALES_BOOSTER_URL . 'assets/css/jquery.dataTables.min.css');
            wp_enqueue_style('datatables_style');
            wp_register_style('wssb-jquery-ui', WP_SEO_SALES_BOOSTER_URL . 'assets/css/jquery.dataTables.min.css');
            wp_enqueue_style('wssb-jquery-ui');
            //color-picket
            wp_enqueue_style('wp-color-picker');
        } else {
            wp_enqueue_style('wp_seo_sales_booster_styles', WP_SEO_SALES_BOOSTER_URL . 'assets/css/wp_seo_sales_booster_frontend_styles.css');
            wp_register_style('toastr_styles', WP_SEO_SALES_BOOSTER_URL . 'assets/css/toastr.min.css');
            wp_enqueue_style('toastr_styles');
        }
    }
    public  function register_post_type()
    {
        if (get_option('wp_seo_sales_booster_internal_link') == 'enable') {
            register_post_type('wssb_links', array(
                'labels' => array(
                    'name' => __('WSSB Links', 'wp_seo_sales_booster'),
                    'singular_name' => __('WSSB Links', 'wp_seo_sales_booster'),
                ),
                'rewrite' => false,
                'query_var' => false,
            ));
        }
    }
}

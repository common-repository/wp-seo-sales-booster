<?php

if (!defined('ABSPATH')) {
    exit;
}

class Wssb_Internal_Link
{
    //properties used in add_autolinks()
    private $ail_id;
    private $ail_a;
    private $parsed_autolink;
    private $autolinks_ca = null;

    public function __construct()
    {
        $this->init();
    }
    public function init()
    {
        if (get_option('wp_seo_sales_booster_internal_link') === "enable") {
            add_action('admin_menu', array($this, 'add_menu_item'));
        }

        add_filter('the_content', array($this, 'add_autolinks'));
        add_action('wp_ajax_link_type', array($this, 'link_type')); // Call when user logged in

    }
    public function add_menu_item()
    {
        add_submenu_page(
            'wp_seo_sales_booster',
            'Internal Link Manager',
            'Internal Link Manager',
            'import',
            'wssb-internal-link',
            array($this, 'internal_link_page')
        );
    }
    public function internal_link_page($args = "")
    {
        // var_dump($_GET['action']);exit;
        if (isset($_POST['wssb-link-submit'])) {
            $keyword = sanitize_text_field($_POST['wssb-link-keyword']);
            $title = sanitize_text_field($_POST['wssb-link-tittle']);
            $url = sanitize_text_field($_POST['wssb-page-url'] ? sanitize_text_field($_POST['wssb-page-url']) : sanitize_text_field($_POST['wssb-custom-url']));
            $page_post_id = sanitize_text_field($_POST['wssb-post-id'] ? $_POST['wssb-post-id'] : '');
            $post_types_str = array_map( 'sanitize_text_field', wp_unslash( $_POST['wssb-post-types'] ) );
            $post_types = implode(', ', $post_types_str);
            $args = array(
                'post_title' => $keyword,
                'post_content' => $keyword,
                'post_type' => 'wssb_links',
                'post_status' => 'unpublish',
                'comment_status' => 'closed',
                'ping_status' => 'closed'
            );
            $post_id = wp_insert_post($args);
            if ($post_id) {
                update_post_meta($post_id, '_keyword', $keyword);
                update_post_meta($post_id, '_title', $title);
                update_post_meta($post_id, '_page_post_id', $page_post_id);
                update_post_meta($post_id, '_url', $url);
                update_post_meta($post_id, '_post_types', $post_types);
                update_post_meta($post_id, '_dates', date('Y-m-d'));
            }
        } else if (isset($_GET['action']) && $_GET['action'] == 'edit') {
            $p = intval($_GET['id']);
            $keyword = get_post_meta($p, "_keyword", true);
            $title = get_post_meta($p, "_title", true);
            $page_post_id = get_post_meta($p, "_page_post_id", true);
            $url = get_post_meta($p, "_url", true);
            $post_types = get_post_meta($p, "_post_types", true);

        } else if (isset($_POST['wssb-link-update'])) {
            $post_id = intval($_POST['wssb-link-id']);
            update_post_meta($post_id, '_keyword', sanitize_text_field($_POST['wssb-link-keyword']));
            update_post_meta($post_id, '_title', sanitize_text_field($_POST['wssb-link-tittle']));
            update_post_meta($post_id, '_page_post_id', sanitize_text_field($_POST['wssb-post-id']) ? sanitize_text_field($_POST['wssb-post-id']) : '');
            update_post_meta($post_id, '_url', sanitize_text_field($_POST['wssb-page-url']) ? sanitize_text_field($_POST['wssb-page-url']) : sanitize_text_field($_POST['wssb-custom-url']));
            $posts_str = array_map( 'sanitize_text_field', wp_unslash( $_POST['wssb-post-types'] ) );
            update_post_meta($post_id, '_post_types', implode(', ', $posts_str));

        }
?>
        <div class="wrap">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7 main-box">
                        <h2 class="internalh2">Create Internal & External Link. Boostup Your SEO & Sales</h2>
                        <form class="internal_link_form" method="post" action="admin.php?page=wssb-internal-link">
                            <div class="kwform">
                                <label for="">Keyword</label>
                                <input type="text" class="common_input" name="wssb-link-keyword" value="<?php echo isset($keyword) ? $keyword : ''; ?>">
                            </div>
                            <div class="kwform">
                                <label for="">Title</label>
                                <input type="text" class="common_input" name="wssb-link-tittle" value="<?php echo isset($title) ? $title : ''; ?>">
                            </div>

                            <div class="kwform">
                                <?php if (isset($_GET['action']) == 'edit') { ?>
                                    <input type="hidden" name="wssb-link-id" value="<?php echo $p; ?>">
                                    <div id="wssb-website" class="kwform" style="<?php echo !empty($page_post_id) ? '' : 'display:none'; ?>">
                                        <label for="">Page </label>
                                        <input type="hidden" name="wssb-page-url" id="wssb-page-url" value="<?php echo !empty($page_post_id) ? $url : ''; ?>">
                                        <input type="hidden" name="wssb-post-id" id="wssb-post-id" value="<?php echo !empty($page_post_id) ? $page_post_id : ''; ?>">
                                        <input type="text" class="search-autocomplete" placeholder="Search Page" name="s" id='wssb-title-search' value="<?php echo !empty($page_post_id) ? get_the_title($page_post_id) : ''; ?>">
                                        <button class="btn custom-url-link-btn"><a href="#" id="custom-url">Custom url link</a></button>
                                    </div>
                                    <div class="" id="wssb-custom" style="<?php echo !empty($page_post_id) ? 'display:none' : ''; ?>">
                                        <label for="">Url </label>
                                        <input type="text" class="common_input" placeholder="Url" name="wssb-custom-url" value="<?php echo empty($page_post_id) && !empty($url) ? $url : ''; ?>">
                                        <a href="#" id="web-pge">Website page</a>
                                    </div>
                                <?php  } else {
                                ?>
                                    <div class="" id="wssb-website">
                                        <label for="">Page</label>
                                        <input type="hidden" class="common_input" name="wssb-page-url" id="wssb-page-url" value="">
                                        <input type="hidden" name="wssb-post-id" id="wssb-post-id" value="">
                                        <input type="text" class="search-autocomplete" placeholder="Search Page" name="s" id='wssb-title-search'>
                                        <a href="#" id="custom-url">Custom url link</a>
                                    </div>
                                    <div class="" id="wssb-custom" style="display:none">
                                        <label for="">Url </label>
                                        <input type="text" class="common_input_c" placeholder="Custom url" name="wssb-custom-url">
                                        <a href="#" id="web-pge">Website page</a>
                                    </div>
                                <?php
                                } ?>

                            </div>
                            <div class="post_type_wp">
                                <label for="">Post Type</label>
                                <input type="checkbox" name="wssb-post-types[]" id="" value="post" <?php echo isset($post_types) && str_contains($post_types, 'post') == true ? 'checked' : ''; ?>>
                                <label for="">Post</label>
                                <input type="checkbox" name="wssb-post-types[]" id="" value="page" <?php echo isset($post_types) && str_contains($post_types, 'page') == true ? 'checked' : ''; ?>>
                                <label for="">Page</label>
                                <br>
                                <?php if (isset($_GET['action']) == 'edit') {
                                ?>
                                    <input type="submit" name="wssb-link-update" value="update">
                                <?php
                                } else {
                                ?>
                                    <input class="custom-submit" type="submit" name="wssb-link-submit" value="Save">
                                <?php
                                }
                                ?>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-4 offset-lg-1 doc-prepare">
                        <div class="help_bg">
                            <div class="help_bg_h6">
                            
                                <p class="help_video">Help Video Link</p>
                                <p>If you couldn't understand that how our features helps you then checkout our videos to rank and boost-up your sales </p>
                                
                                <button class="btn btn-warning btn-medium learn_more"><a href="https://www.youtube.com/watch?v=kHzHIQ3LCDk&feature=youtu.be"> Go Now </a></button>
                            </div>
                        </div>

                        <div class="help_bg">
                        <div class="help_bg_h6">
                            
                            <p class="help_video">Plugin Docs</p>
                            <p>Looking for WP SEO & Sales Booster Plugin's Docs? We are here to help you to find our docs for enhancing your business </p>
                            
                            <button class="btn btn-warning btn-medium learn_more"> <a href="https://wpseobooster.com/wp-seo-sales-booster-docs/">Click Here </a>  </button>
                        </div>
                        </div>
                    </div>
                </div>
            </div>



    <?php

    }
    public function link_type()
    {

        $args = array(
            'post_type' => array('post', 'page', 'product'),
            'post_status' => 'publish',
            'posts_per_page' => 10,
            's' => sanitize_text_field(stripslashes($_POST['search'])),
        );
        $the_query = new WP_Query($args);
        $item = array();
        foreach ($the_query->posts  as $post) {
            $item[] = [
                'id' => $post->ID,
                'value' => $post->guid,
                'label' => $post->post_title
            ];
        }
        wp_send_json_success($item);

    }
    public function add_autolinks($content, $check_query = true, $post_type = '', $post_id = false)
    {
        if ($check_query) {
            if (!is_singular() or is_attachment() or is_feed()) {
                return $content;
            }
        }
        if ($post_id === false) {
            $post_id = get_the_ID();
        }
        $this->ail_id = 0;
        $this->ail_a = array();
        $this->post_id = $post_id;

        $results = array();
        $posts = get_posts(array(
            'post_type'   => 'wssb_links',
            'post_status' => 'unpublish',
            'posts_per_page' => -1,
            'fields' => 'ids'
        ));
        //loop over each post
        foreach ($posts as $p) {
            //get the meta you need form each post
            $keyword = get_post_meta($p, "_keyword", true);
            $url = get_post_meta($p, "_url", true);
            $post_types = get_post_meta($p, "_post_types", true);

            $results[] =  [
                'id' => $p,
                'keyword' => $keyword,
                'url' => $url,
                'post_types' => $post_types
            ];
            //do whatever you want with it
        }

        $this->autolinks_ca = $this->save_autolinks_in_custom_array($results);
        //cycle through all the defined autolinks
        foreach ($results as $key => $value) {
            $this->parsed_autolink = $value;
            $activate_post_types = preg_replace('/\s+/', '', $value['post_types']);
            $post_types_a = explode(",", $activate_post_types);

            if ($post_type != '') {
                if (in_array($post_type, $post_types_a) === false) {
                    continue;
                }
            } else {
                if (in_array(get_post_type(), $post_types_a) === false) {
                    continue;
                }
            }


            //escape regex characters and the '/' regex delimiter
            $autolink_keyword = preg_quote(stripslashes($value['keyword']), '/');
            $content = preg_replace_callback(
                '/(\b)(' . $autolink_keyword . ')(\b)/u',
                array(
                    $this, 'preg_replace_callback_10000'
                ),
                $content
            );
        }
        $content = preg_replace_callback(
            '/\[ail\](\d+)\[\/ail\]/',
            array($this, 'preg_replace_callback_2'),
            $content
        );
        
        return $content;
    }
    public function preg_replace_callback_10000($m)
    {
        $this->ail_id++;
        $this->ail_a[$this->ail_id]['autolink_id'] = $this->parsed_autolink['id'];
        $this->ail_a[$this->ail_id]['url'] = $this->parsed_autolink['url'];
        $this->ail_a[$this->ail_id]['text'] = $m[2];
        return '[ail]' . $this->ail_id . '[/ail]';
    }
    public function preg_replace_callback_2($m)
    {

        $link_text = $this->ail_a[$m[1]]['text'];
        //Get the autolink_id
        $autolink_id = $this->ail_a[$m[1]]['autolink_id'];
        //get the "url" value
        $link_url = $this->autolinks_ca[$autolink_id]['url'];
        //generate the title attribute HTML if the "title" field is not empty
        if (strlen(trim($this->autolinks_ca[$autolink_id]['title'])) > 0) {
            $title_attribute = 'title="' . esc_attr(stripslashes($this->autolinks_ca[$autolink_id]['title'])) . '"';
        } else {
            $title_attribute = '';
        }


        // echo'1';exit
        return ' <a title="'.esc_attr($title_attribute).'" data-ail="' . esc_attr($this->post_id) . '"  href="' . esc_attr($link_url) . '>' . esc_html($link_text ). '</a>';
    }
    public function save_autolinks_in_custom_array($autolinks)
    {

        $autolinks_ca = array();

        foreach ($autolinks as $key => $autolink) {

            $autolinks_ca[$autolink['id']] = $autolink;
        }

        return $autolinks_ca;
    }
}
new Wssb_Internal_Link();

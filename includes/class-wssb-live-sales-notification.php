<?php

if (!defined('ABSPATH')) {
    exit;
}



class Wssb_Sales_Notification
{
    public function __construct()
    {
        $this->init();
    }
    public function init()
    {
        add_action('wp_ajax_wssb_notification_data', array($this, 'wssb_notification_data')); // Call when user logged in
        add_action('wp_ajax_nopriv_wssb_notification_data', array($this, 'wssb_notification_data')); // Call when user in not
        add_action('wp_footer', array($this, 'notification_html'));
    }


    public function wssb_notification_data()
    {
        if ($_POST['page'] == 'shop') {
            $p_ids = get_posts(array(
                'post_type' => 'product',
                'numberposts' => -1,
                'post_status' => 'publish',
                'fields' => 'ids',
            ));
        } else if ($_POST['page'] == 'category') {

            $p_ids = get_posts(array(
                'post_type' => 'product',
                'numberposts' => -1,
                'post_status' => 'publish',
                'fields' => 'ids',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'product_cat',
                        'field' => 'slug',
                        'terms' => sanitize_text_field($_POST['slug']),
                        'operator' => 'IN',
                    )
                ),
            ));
        } else if ($_POST['page'] == 'product') {
            $p_ids = array();
            $p_ids[] = intval($_POST['p_id']);
        }

        if (!empty($p_ids)) {
            $result = $this->get_purchased_data($p_ids);
        }
        wp_send_json_success($result);
    }
    public function get_purchased_data($p_ids)
    {
        global $wpdb;
        $order_status = array('wc-processing', 'wc-completed');
        $results = $wpdb->get_results("
        SELECT order_items.order_id, order_item_meta.meta_value as product_id
        FROM {$wpdb->prefix}woocommerce_order_items as order_items
        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as order_item_meta ON order_items.order_item_id = order_item_meta.order_item_id
        LEFT JOIN {$wpdb->posts} AS posts ON order_items.order_id = posts.ID
        WHERE posts.post_type = 'shop_order'
        AND UNIX_TIMESTAMP(posts.post_date) >= (UNIX_TIMESTAMP(NOW()) - (86400))
        AND posts.post_status IN ( '" . implode("','", $order_status) . "' )
        AND order_items.order_item_type = 'line_item'
        AND order_item_meta.meta_key = '_product_id'
        AND order_item_meta.meta_value IN ( '" . implode("','", $p_ids) . "' ) 
        ORDER BY RAND() LIMIT 3
    ");
        // $data = array();
        foreach ($results as $result) {
            $product = wc_get_product($result->product_id);
            $product_name = $product->get_title();
            // Get an instance of the WC_Order Object from the Order ID (if required)

            $order = wc_get_order($result->order_id);
            // Customer billing information details
            $billing_first_name = $order->get_billing_first_name();
            $billing_last_name  = $order->get_billing_last_name();
            $billing_city       = $order->get_billing_city();
            $billing_country      = WC()->countries->countries[$order->get_billing_country()];
            $billing_state    = WC()->countries->states[$order->get_billing_country()][$order->get_billing_state()];

            $now_dt = new WC_DateTime();
            $order_date = $order->get_date_created();
            $elapsed = $order_date->diff($now_dt)->format('%h hours %i minutes');
            $url = get_permalink($result->product_id);
            $image = get_the_post_thumbnail_url($result->product_id);

            $data[] = [
                'type' => 'live_sale_notification',
                'product_url' => $url,
                'product_name' => $product_name,
                'product_image' => $image,
                'billing_first_name' => $billing_first_name,
                'billing_last_name' => $billing_last_name,
                'billing_city' =>  $billing_city,
                'billing_state' => $billing_state,
                'billing_country' => $billing_country,
                'elapsed' => $elapsed
            ];
        }
        return $data;
    }
    public function notification_html()
    {
?>
        <div id="wssb-notification-body" style="display:none" wssb-page=<?php if (is_shop()) {
                                                                            echo "shop";
                                                                        } else if (is_product_category()) {
                                                                            echo "category";
                                                                            $term = get_queried_object();
                                                                            echo ' wssb-cat-slug=' . $term->slug;
                                                                        } else if (is_product()) {
                                                                            echo "product";
                                                                            echo ' wssb-p-id=' . get_the_ID();
                                                                        } ?>>
        </div>
<?php
    }
}
new Wssb_Sales_Notification();

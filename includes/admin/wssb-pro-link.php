<?php

class Wp_Seo_Sales_Booster_Pro_Link
{

    function __construct()
    {
        add_action('admin_menu', array($this, 'menu_item'));
    }

    function menu_item(){
        add_submenu_page(
            'wp_seo_sales_booster',
            'WP SEO & Sales Booster',
            'Migrate to Pro',
            'manage_options',
            'https://wpseobooster.com', 
            '',
            null,
            array($this, 'page_content')
        );
    }

}



new Wp_Seo_Sales_Booster_Pro_Link();
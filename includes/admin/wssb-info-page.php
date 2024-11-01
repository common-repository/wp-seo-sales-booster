<?php

class Wp_Seo_Sales_Booster_Info_Page
{

    function __construct()
    {
        add_action('admin_menu', array($this, 'menu_item'));
    }

    function menu_item(){
        add_submenu_page(
            'wp_seo_sales_booster',
            'WP SEO & Sales Booster',
            'Help',
            'manage_options',
            'wssb-info-page',
            array($this, 'page_content')
        );
    }
    function page_content(){
        ?>
        <style>


            .wooain-info-page-container,.wooain-support-container {
                padding-left: 20px;
            }

            .wooain-info-page-headline {
                border-bottom: 1px dashed #cccccc;
                margin: 0 0 10px;
                padding: 10px 0;
            }
        </style>
        <div class="wrap">

            <div id="poststuff">

                <div id="post-body" class="metabox-holder columns-2">

                    <div id="post-body-content" style="position: relative;">

                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12">
                                <h2 class="wooain-info-page-headline">Help & Info</h2>
                                </div>
                            </div>
                        </div>


                        <div class="container wooain-info-page-container">
                            <div class="row">
                                        <div class="col-lg-4 help_page_float">
                                            <div class="help_page">
                                                <div class="help_page_h6">
                                                
                                                    <p class="help_video">Help Video Link</p>
                                                    <p>If you couldn't understand that how our features helps you then checkout our videos to rank and boost-up your sales </p>
                                                    
                                                    <button class="btn btn-warning btn-medium learn_more"><a href="https://www.youtube.com/watch?v=kHzHIQ3LCDk&feature=youtu.be"> Go Now </a></button>
                                                </div>
                                                <!--help-page-h6-->
                                            </div>
                                            <!--help-page-->
                                        </div>
                                        <!--col-lg-4-->

                                        <div class="col-lg-4">
                                            <div class="help_page">
                                                <div class="help_page_h6">
                                                
                                                    <p class="help_video">Plugin Documentations</p>
                                                    <p>Looking for WP SEO & Sales Booster Plugin's Docs? We are here to help you to find our docs for enhancing your business </p>
                                                    
                                                    <button class="btn btn-warning btn-medium learn_more"><a href="https://wpseobooster.com/wp-seo-sales-booster-docs/"> Go Now </a></button>
                                                </div>
                                                <!--help-page-h6-->
                                            </div>
                                            <!--help-page-->
                                        </div>
                                        <!--col-lg-4-->

                                        <div class="col-lg-4">
                                            <div class="help_page">
                                                <div class="help_page_h6">
                                                
                                                    <p class="help_video">Pro Support Link</p>
                                                    <p>Looking for pro version support? Yes you are in right place. We are happy to assist you. Just create an account by clicking here </p>
                                                    
                                                    <button class="btn btn-warning btn-medium learn_more">Go Now</button>
                                                </div>
                                                <!--help-page-h6-->
                                            </div>
                                            <!--help-page-->
                                        </div>
                                        <!--col-lg-4-->

                                        <div class="col-lg-4">
                                            <div class="help_page">
                                                <div class="help_page_h6">
                                                
                                                    <p class="help_video">Our Others Products & Services</p>
                                                    <p>View our all products & we provide web services like web design, web development, theme & plugin customizations, SEO  & Many more. </p>
                                                    
                                                    <button class="btn btn-warning btn-medium learn_more">View Products</button>
                                                </div>
                                                <!--help-page-h6-->
                                            </div>
                                            <!--help-page-->
                                        </div>
                                        <!--col-lg-4-->

                                        <div class="col-lg-4">
                                            <div class="help_page">
                                                <div class="help_page_h6">
                                                
                                                    <p class="help_video">Google Adwords Keyword Planner Plugin</p>
                                                    <p>Our google adwords keyword planner plugin helps you to find best competitive keywords for your business without any hassle</p>
                                                    
                                                    <button class="btn btn-warning btn-medium learn_more">See More</button>
                                                </div>
                                                <!--help-page-h6-->
                                            </div>
                                            <!--help-page-->
                                        </div>
                                        <!--col-lg-4-->

                                        <div class="col-lg-4">
                                            <div class="help_page">
                                                <div class="help_page_h6">
                                                
                                                    <p class="help_video">Conversion Tracking for Retarget Plugin</p>
                                                    <p>Connect your site with Ad Platforms like Facebook, Twitter, Google, Microsoft Ads, Perfect Audience and send conversion data. </p>
                                                    
                                                    <button class="btn btn-warning btn-medium learn_more">See More</button>
                                                </div>
                                                <!--help-page-h6-->
                                            </div>
                                            <!--help-page-->
                                        </div>
                                        <!--col-lg-4-->

                                        <div class="col-lg-4">
                                            <div class="help_page">
                                                <div class="help_page_h6">
                                                
                                                    <p class="help_video">Live Sales Notification with PopUp Plugin</p>
                                                    <p>Our live sales notification plugin will help you to generate sales for your new business and also for your old business it can generate more.</p>
                                                    
                                                    <button class="btn btn-warning btn-medium learn_more">See More</button>
                                                </div>
                                                <!--help-page-h6-->
                                            </div>
                                            <!--help-page-->
                                        </div>
                                        <!--col-lg-4-->


                                        <div class="col-lg-4">
                                            <div class="help_page">
                                                <div class="help_page_h6">
                                                
                                                    <p class="help_video">Internal & External Link Manager Plugin</p>
                                                    <p>Our Internal & External Link Manager Plugin will boost-up your seo within a very short time which will brings effective results for you. </p>
                                                    
                                                    <button class="btn btn-warning btn-medium learn_more">See More</button>
                                                </div>
                                                <!--help-page-h6-->
                                            </div>
                                            <!--help-page-->
                                        </div>
                                        <!--col-lg-4-->


                                        <div class="col-lg-4">
                                            <div class="help_page">
                                                <div class="help_page_h6">
                                                
                                                    <p class="help_video">Auto Image Attribute Bulk Updater Plugin</p>
                                                    <p>This plugin designed for boost-up your web & images seo. By using this plugin you can update your existing images attributes too.  </p>
                                                    
                                                    <button class="btn btn-warning btn-medium learn_more">See More</button>
                                                </div>
                                                <!--help-page-h6-->
                                            </div>
                                            <!--help-page-->
                                        </div>
                                        <!--col-lg-4-->


                                        <div class="col-lg-8">
                                            <div class="admin-info">
                                                <div class="col-lg-12 allinone">
                                                <p class="develop_p"><strong class="developed_by">Devoloped by:</strong> <a href="https://www.wpseobooster.com"> wpseobooster.com</a> </p>
                                                <p><strong>Email:</strong> wpseobooster@gmail.com</p>
                                                <p><strong>Whatsapp:</strong> +88-01793191910</p>
                                                <p><strong>Plugin Web Address:</strong><a href="https://www.wpseobooster.com"> www.wpseobooster.com</a> </p>
                                            </div>

                                            
                                            </div>
                                        </div>

                                        
                                        
                                        
                              

                            </div>
                            <!--row-->

                        </div>

                        <!--container-->
                           

                    </div>
                    <!-- /post-body-content -->


                </div>
                <!-- /post-body-->

            </div>
            <!-- /poststuff

        </div>
        <!-- /wrap -->

        <?php
    }
}

new Wp_Seo_Sales_Booster_Info_Page();
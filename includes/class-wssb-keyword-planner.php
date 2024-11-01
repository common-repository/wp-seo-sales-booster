<?php
require __DIR__ . '/../vendor/autoload.php';
use Google\Auth\AccessToken;
defined('ABSPATH') || exit;

class Wssb_Keyword_Planner
{
    public function __construct()
    {

        $this->init();
        Wssb_Get_Authenticate_With_Google::get_refresh_token();
    }

    public function init()
    {

        add_action('add_meta_boxes', array($this, 'wssb_add_meta_box'));
        add_action('wp_ajax_google_keyword', array($this, 'google_keyword')); // Call when user logged in
        add_action('wp_ajax_wssb_logout_process', array($this, 'wssb_logout_process')); // Call when user logged in
        // add_action('admin_init', array($this, 'wssb_logout'));

    }

    public function wssb_add_meta_box()
    {

        $post_types = get_post_types(['public' => true]);
        unset($post_types['attachment']);
        foreach ($post_types as $post_type) {
            add_meta_box(
                'wssb_meta_1',
                'Keyword Planner',
                array($this, 'wssb_metabox_callback'),
                $post_type,
                'normal',
                'high'
            );
        }
    }
    public function wssb_metabox_callback()
    {
        //delete_option('wssb_google_refresh_code');

        if (!empty(get_option('wssb_google_refresh_code'))) {
            // if (!empty(get_option('ytfytryry'))) {
            ?>

            <div class="right">

            <button type="button" class="btn btn-primary ownstyle" id="wssb-google-logout">Logout</button>

           


            <form method="POST" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" name="get_keyword">

                <div class="container inline-flex keyword_research">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="">Keyword: </label><br />
                            <input type="text" placeholder="Search your keyword here ..." id="keyword">
                        </div>
                        <br>
                        <br>
                        <div class="col-md-4 test">
                            <label for="">Language: </label><br />
                            <?php
$this->google_language();
            ?>
                        </div>
                        <div class="col-md-4">
                            <label for="">Location: </label><br />
                            <?php
$this->wssb_dropdown_country();
            ?>
                        </div>
                    </div>
                    <div class="text-right">
                        <button id="get-result" class="wssb-btn-reset btn btn-primary btn-sm">Get Result</button> <span></span>
                        <button type="reset" class="wssb-btn-reset btn btn-danger btn-sm">Reset</button>
                    </div>
                </div>

            </form>
            <div id="loader" class="lds-dual-ring hidden overlay"></div>
            <br>
            <br>
            <table id="keyword-table" class="table table-striped table-hover" style="display: none;">
                <thead>
                    <tr class="trtest">
                        <th>KW (by relevance)</th>
                        <th>Av. Monthly Searches</th>
                        <th>12 Month Searches</th>
                        <th>Av. CPC</th>
                    </tr>
                </thead>
            </table>
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true" data-backdrop="false">
                <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary">Save changes</button>
                        </div>
                    </div>
                </div>
            </div>
            </div>

        <?php

        } else {
            ?>
            <button type="button" class="btn btn-primary ownstyle"><a href="<?php echo Wssb_Get_Authenticate_With_Google::generate_login_uri() ?>">Get Authenticate With
                    Google</a></button>
        <?php
}
    }
    public function wssb_logout_process()
    {
        $token = get_option('wssb_google_refresh_code');
        $revokeToken = new AccessToken;
        $revokeResult = $revokeToken->revoke($token);
        if($revokeResult == true){
            $logout = delete_option('wssb_google_refresh_code');
            wp_send_json_success($logout);
        }
    }
    public static function wssb_current_url()
    {
        if (
            isset($_SERVER['HTTPS']) &&
            ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
        ) {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }

    public function google_keyword()
    {
        $keyword = sanitize_text_field($_POST['keyword']);
        $language = sanitize_text_field($_POST['language']);
        $country = sanitize_text_field($_POST['country']);
        ob_start();
        $get_keyword = new Wssb_Get_Keyword_Ideas();
        $results = $get_keyword->main($keyword, $language, $country);
        ob_get_clean();
        //wp_send_json_success($html);
        wp_send_json_success($results);
    }
    public function wssb_dropdown_country()
    {
        add_filter('https_ssl_verify', '__return_false');
        
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $real_ip_adress = $_SERVER['HTTP_CLIENT_IP'];
        }
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $real_ip_adress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $real_ip_adress = $_SERVER['REMOTE_ADDR'];
        }
        $cip = $real_ip_adress;
        $iptolocation = 'http://api.hostip.info/country.php?ip=' . $cip;
        $creatorlocation = wp_remote_retrieve_body(wp_remote_get($iptolocation));
        // var_dump($creatorlocation);exit;

        $response = wp_remote_retrieve_body(wp_remote_get(WP_SEO_SALES_BOOSTER_URL . 'assets/json/country.json'));
        $countries = json_decode($response, true);
        ?>
        <select id="countries">
            <?php
foreach ($countries as $country) {
            ?>
                <option value="<?php echo $country["Criteria ID"] ?>" title="<?php htmlspecialchars($country["Criteria ID"])?>" <?php
if ($country["Country Code"] == $creatorlocation) {
                echo "selected";
            }
            ?>><?=htmlspecialchars($country["Canonical Name"])?></option>
            <?php
}
        ?>
        </select>
    <?php
}
    public function google_language()
    {
        $languages = array(
            "Arabic" => "1019",
            "Bengali" => "1056",
            "Bulgarian" => "1020",
            "Catalan" => "1038",
            "Chinese (simplified)" => "1017",
            "Chinese (traditional)" => "1018",
            "Croatian" => "1039",
            "Czech" => "1021",
            "Danish" => "1009",
            "Dutch" => "1010",
            "English" => "1000",
            "Estonian" => "1043",
            "Filipino" => "1042",
            "Finnish" => "1011",
            "French" => "1002",
            "German" => "1001",
            "Greek" => "1022",
            "Hebrew" => "1027",
            "Hindi" => "1023",
            "Hungarian" => "1024",
            "Icelandic" => "1026",
            "Indonesian" => "1025",
            "Italian" => "1004",
            "Japanese" => "1005",
            "Korean" => "1012",
            "Latvian" => "1028",
            "Lithuanian" => "1029",
            "Malay" => "1102",
            "Norwegian" => "1013",
            "Persian" => "1064",
            "Polish" => "1030",
            "Portuguese" => "1014",
            "Romanian" => "1032",
            "Russian" => "1031",
            "Serbian" => "1035",
            "Slovak" => "1033",
            "Slovenian" => "1034",
            "Spanish" => "1003",
            "Swedish" => "1015",
            "Tamil" => "1130",
            "Telugu" => "1131",
            "Thai" => "1044",
            "Turkish" => "1037",
            "Ukrainian" => "1036",
            "Urdu" => "1041",
            "Vietnamese" => "1040",
        );
        ?>
        <select id="languages">
            <?php

        foreach ($languages as $key => $value) {

            ?>
                <option value="<?php echo $value; ?>" title="<?php htmlspecialchars($key)?>" <?php if ($key == "English") {
                echo "selected";
            }?>>
                    <?=htmlspecialchars($key);?></option>
            <?php

        }

        ?>
        </select>

<?php
}
}
new Wssb_Keyword_Planner();

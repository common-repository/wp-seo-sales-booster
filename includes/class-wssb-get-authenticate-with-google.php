<?php
require __DIR__ . '/../vendor/autoload.php';

use Google\Auth\OAuth2;


defined('ABSPATH') || exit;

// if(get_option('wp_seo_sales_booster_own_api_key') == "on"){
define('WSSB_CLIENTID', get_option("wp_seo_sales_booster_client_id"));
define('WSSB_CLIENTSECRET', get_option('wp_seo_sales_booster_client_secret'));
define('WSSB_REDIRECT_URI', get_option('wp_seo_sales_booster_redirect_url'));

// }else{
//     define('WSSB_CLIENTID', '835744742043-6n5hcmhf8rh102klb6v8b5n39iljq957.apps.googleusercontent.com');
//     define('WSSB_CLIENTSECRET', 'bSW_q6Wx8HrA2yNoeCzfQ7SZ');
//     define('WSSB_REDIRECT_URI', 'http://localhost/WSSB_REDIRECT_URI/');
// }

define('WSSB_AUTHORIZATION_URI', 'https://accounts.google.com/o/oauth2/v2/auth');
define('WSSB_TOKEN_CREDENTIAL_URI', 'https://www.googleapis.com/oauth2/v4/token');
define('WSSB_ADWORDS_API_SCOPE', 'https://www.googleapis.com/auth/adwords');
define('WSSB_CUSTOMER_CLIENT_ID', get_option('wp_seo_sales_booster_customer_client_id'));
define('WSSB_DEV_TOKEN', 'O3DN_-MpR9tcxXxFouXr2g');

class Wssb_Get_Authenticate_With_Google
{
    public static function create_oauth2_instance()
    {
        session_start();
        $oauth2 = new OAuth2([
            'authorizationUri' => WSSB_AUTHORIZATION_URI,
            'tokenCredentialUri' => WSSB_TOKEN_CREDENTIAL_URI,
            'redirectUri' => WSSB_REDIRECT_URI,
            'clientId' => WSSB_CLIENTID,
            'clientSecret' => WSSB_CLIENTSECRET,
            'scope' => WSSB_ADWORDS_API_SCOPE,
            'state' => Wssb_Keyword_Planner::wssb_current_url()
        ]);
        return $oauth2;
    }

    public static function generate_login_uri()
    {
        $oauth2 = self::create_oauth2_instance();
        $uri = $oauth2->buildFullAuthorizationUri();
        return $uri;
    }
    public static function get_refresh_token()
    {
        // if (isset($_GET['wssb-code'])) {
        if (isset($_GET['code'])) {
            delete_option('wssb_google_refresh_code');
            $oauth2 = self::create_oauth2_instance();
            // $oauth2->setCode(sanitize_text_field($_GET['wssb-code']));
            $oauth2->setCode(sanitize_text_field($_GET['code']));
            $authToken = $oauth2->fetchAuthToken();
            // var_dump($authToken);exit;
            // Store the refresh token for your user in your local storage if you
            // requested offline access.
            $refreshToken = $authToken['refresh_token'];

            // printf("Your refresh token is: %s\n\n", $refreshToken);

            update_option('wssb_google_refresh_code', $refreshToken);
            wp_redirect(sanitize_text_field($_GET['state']));
            exit;
        }
    }
}

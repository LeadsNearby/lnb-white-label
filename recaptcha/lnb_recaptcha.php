<?php

class lnbRecaptcha {

    public function __construct() {
        add_action('login_head', array($this, 'captcha_include'));
        add_action('login_form', array($this, 'captcha_form'));
        add_filter('wp_authenticate_user', array($this, 'verify_captcha'));
        add_action('register_form', array($this, 'captcha_form'));
        add_action('register_post', array($this, 'verify_captcha'));
        add_action('lostpassword_form', array($this, 'captcha_form'));
        add_action('lostpassword_post', array($this, 'verify_captcha_lost_password'));

    }

/*** Include Captcha JS ***/

    public function captcha_include() {

        ?>

        <script src="https://www.google.com/recaptcha/api.js" async defer></script>

        <?php

    }

/*** Call Captcha Visual ***/

    public function captcha_form() {
        $google_site_key = get_option("captcha_site_key");
        if ($google_site_key == "default") {
            ?> <div class="g-recaptcha" data-sitekey="6Lf-UTEUAAAAADSMzp-1i0jhKMSfaLQ97E1wy9sX"  data-callback="recaptcha_callback"></div> <?php
} else {
            ?> <div class="g-recaptcha" data-sitekey="<?php echo $google_site_key ?>"  data-callback="recaptcha_callback"></div> <?php
}

        ?>

        <?php

    }

/*** Verify Captcha Response, Compare Against Allowed Domains ***/

    public function verify_captcha($user = true) {

        if (strpos($_SERVER['HTTP_REFERER'], 'https://dashboard.lnbsvcs.com')) {
            return $user;
        }

        $google_secret = get_option("captcha_api_key");

        if (!empty($_POST['g-recaptcha-response'])) {
            if ($google_secret == "default") {
                $response = json_decode(wp_remote_retrieve_body(wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=6Lf-UTEUAAAAAEF2ED7YZNR4Y82AmFwjF5SCsqUb&response=" . $_POST['g-recaptcha-response'])), true);
            } else {
                $response = json_decode(wp_remote_retrieve_body(wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret='.$google_secret.'&response=" . $_POST['g-recaptcha-response'])), true);
            }

            $domains = file_get_contents('https://lnbdev.com/clients/?apikey=dad9547e-c01e-46ee-b177-bffbc54ed3f2');
            $domain_array = json_decode($domains, true);
            $domain_array = array_filter(array_map('trim', $domain_array["domains"]));
            $hostname = preg_replace('/^www\./', '', $response["hostname"]);

            if ($response["success"] && in_array($hostname, $domain_array)) {

                return $user;

            } else {
                $WP_Error = new WP_Error();
                $WP_Error->add('captcha_failed', 'Invalid captcha - hostname does not exist in domain list.');
                return $WP_Error;
            }
        }

        if (isset($_POST['g-recaptcha-response']) && empty($_POST['g-recaptcha-response'])) {
            $WP_Error = new WP_Error();
            $WP_Error->add('captcha_failed', 'Invalid captcha - please try again.');
            return $WP_Error;
        }

        return $user;

    }

/*** Additional Code for Lost Password Form ***/

    public function verify_captcha_lost_password() {

        if (!verify_captcha()) {

            add_filter('allow_password_reset', '__return_false');

        }
    }
}

?>

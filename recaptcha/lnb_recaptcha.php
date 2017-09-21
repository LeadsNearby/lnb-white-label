<?php


/*** WP Hooks For Login and New User ***/
 
add_action( 'login_head', 'captcha_include' );
add_action( 'login_form', 'captcha_form' );
add_filter( 'wp_authenticate_user', 'verify_captcha' );
add_action( 'register_form', 'captcha_form' );
add_action( 'register_post', 'verify_captcha' );




/*** Include Captcha JS ***/

function captcha_include() {

    ?>
    
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    
    <?php
    
}



/*** Call Captcha Visual ***/

function captcha_form() {
    
    ?>
        
    <div class="g-recaptcha" data-sitekey="6Lf-UTEUAAAAADSMzp-1i0jhKMSfaLQ97E1wy9sX" data-callback="recaptcha_callback"></div>';
    
    <?php
}



/*** Verify Captcha Response, Compare Against Allowed Domains ***/

function verify_captcha( $parameter = true ) {
    
    if( isset( $_POST['g-recaptcha-response'] )   ) {
        
        $response = json_decode(wp_remote_retrieve_body( wp_remote_get( "https://www.google.com/recaptcha/api/siteverify?secret=6Lf-UTEUAAAAAEF2ED7YZNR4Y82AmFwjF5SCsqUb&response=" . $_POST['g-recaptcha-response'] ) ), true );
        
        $domain_file = file_get_contents("https://lnbdev.com/other/domain-list-38dcjk32982j3f04jkg3203j48fg.txt");
        $domain_array = explode( "\n", $domain_file );
        $domain_array = array_filter(array_map('trim', $domain_array));
        $hostname = preg_replace('/^www\./', '', $response["hostname"]);

        if( $response["success"] and in_array($hostname, $domain_array)  ) {
            
            return $parameter;
            
            
        } else {
        
            //mail( "brian@leadsnearby.com","Test", $domain_array[125] );
            header('Location: ' . site_url() . '/wp-admin/');
            die();
        }
    }

    return false;
}



/*** Additional Code for Lost Password Form ***/

add_action( 'lostpassword_form', 'captcha_form' );
add_action( 'lostpassword_post', 'verify_captcha_lost_password' );

function verify_captcha_lost_password( ) {
    
    if( !verify_captcha() ) {
        
    add_filter('allow_password_reset', '__return_false');
    
    }
}

?>

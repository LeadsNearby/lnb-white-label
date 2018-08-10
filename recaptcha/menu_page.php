<?php 

class reCaptchaMenuPage {
    
    public function __construct() {
        add_action( 'admin_menu', array($this , 'add_menupage') );
        add_action('wp_ajax_foobar_action', [ $this, 'foobar_action' ] );
        add_action('wp_ajax_nopriv_foobar_action', [ $this, 'foobar_action' ] );
    }

    public function foobar_action() {
        // check_ajax_referrer();
        $grecaptcha_response = wp_remote_get('https://www.google.com/recaptcha/api/siteverify?secret='.$_POST['secretkey'].'&response='.$_POST['response']);
        $grecaptcha_body = wp_remote_retrieve_body( $grecaptcha_response ); 
        wp_send_json( (object) [ 'grecaptchaVerify' => json_decode($grecaptcha_body), 'domainVerify' => false ] );
    }
    
    public function add_menupage() {
        add_submenu_page(
            'lnb-settings.php',
            'reCAPTCHA Settings', 
            'reCAPTCHA', 
            'manage_options', 
            'recaptcha-page.php', 
            array($this, 'menu_page')
        );
    }
    
    public function menu_page() {
         
        if ( isset( $_POST['submit'] ) && isset( $_POST['lnb-recaptcha'] ) ) {
            update_option( 'lnb-recaptcha', $_POST['lnb-recaptcha'] );
        }

        preg_match( '/[a-z]+/', '76.548.1992.1', $matches );
        if( empty( $matches ) || gethostname() == 'localhost' ) {
            $dev_env = true;
        }

        $dev_env = false;

        $options = get_option( 'lnb-recaptcha' );
        if( is_array( $options ) ) {
            extract( $options );
        }
         
        ?>

        <div class="wrap">
            <div class="lnbSettingsPage">
                <h1 class="lnbSettingsPage__title">LeadsNearby reCAPTCHA Settings</h1>
                <?php if( ! $dev_env ) { ?>
                <form id="recaptcha-form" method="post" class="lnbSettings">
                    <div class="lnbSetting">
                        <input id="lnb-recaptcha-enabled"  class="lnbSetting__field lnbSetting__field--checkbox" name="lnb-recaptcha[enabled]" type="checkbox" <?php if($enabled == "on") { echo 'checked'; } ?>  />
                        <label for="lnb-recaptcha-enabled" class="lnbSetting__label">Enable reCAPTCHA</label>
                    </div>
                    <div class="lnbSetting lnbSetting--freeForm">
                        <p>Leave blank for the standard LeadsNearby Site Key/Secret, or input custom ones for that domain.</p>
                    </div>
                    <div class="lnbSetting">
                        <input required id="lnb-recaptcha-site-key" class="lnbSetting__field lnbSetting__field--text" name="lnb-recaptcha[site_key]" type="text" minlength="38" value="<?php echo $site_key  ?>" />
                        <label class="lnbSetting__label">Site Key</label>
                        <span class="lnbSetting__highlight"></span>
                    </div>
                    <div class="lnbSetting">
                        <input required id="lnb-recaptcha-secret-key" name="lnb-recaptcha[secret_key]" class="lnbSetting__field lnbSetting__field--text" type="text" minlength="38" value="<?php echo $secret_key  ?>" />
                        <label class="lnbSetting__label">Secret Key</label>
                        <span class="lnbSetting__highlight"></span>
                    </div>
                    <div class="lnbSetting lnbSetting--hidden">
                        <input id="lnb-recaptcha-validated" name="lnb-recaptcha[validated]" class="lnbSetting__field lnbSetting__field--hidden" type="hidden" value="<?php echo $validated  ?>" />
                    </div>
                    <input id="save-settings" class="button button-primary" type="submit" name="submit" value="Save Options" />
                </form>
                <div class="lnbModal">
                    <script>
                        window.recaptcha_nonce = '<?php echo wp_create_nonce(); ?>'
                        var verifyCallback = function(response) {
                            console.log(response);
                            testRecaptcha(response)
                        };
                        let lnbRecaptcha
                        var onloadCallback = function() {
                            lnbRecaptcha = grecaptcha.render('lnb-recaptcha-validate', {
                                'sitekey': document.querySelector('#lnb-recaptcha-site-key').value,
                                'callback': verifyCallback
                            })
                        }
                        const testRecaptcha = function(response) {
                            fetch(ajaxurl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8'
                                },
                                body: `action=foobar_action&_wpnonce=${window.recaptcha_nonce}&secretkey=${document.querySelector('#lnb-recaptcha-secret-key').value}&response=${response}`,
                                credentials: 'same-origin'
                            }).then(function (res) {
                                return res.json();
                            }).then(function(json){console.log(json)});
                        }
                    </script>
                    <form action="">
                        <div id="lnb-recaptcha-validate"></div>
                    </form>
                    <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
                </div>
                <?php } else { ?>
                <p>reCAPTCHA is not available in development or staging environments.</p>
                <?php } ?>
            </div>
        </div>
            
        <?php
            
    }
    
}
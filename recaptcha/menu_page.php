<?php 

class reCaptchaMenuPage {
    
    public function __construct() {
        add_action( 'admin_menu', array($this , 'add_menupage') );
    }
    
    function add_menupage() {
        add_options_page ( 
            'LNB Recaptcha', 
            'LNB Recaptcha', 
            'manage_options', 
            'recaptcha_menu_page', 
            array($this, 'menu_page')
        );
    }
    
     public function menu_page() {
         
            if ( isset($_POST["submit"]))  {
                if ( isset($_POST["repcatcha-enable"])) {
                    update_option( 'wl-recaptcha-enabled', $_POST["repcatcha-enable"] );
                    if (isset($_POST["captcha_api_key"]) || isset($_POST["captcha_site_key"]  )) {
                        update_option( 'captcha_api_key' , $_POST["captcha_api_key"] );
                        update_option( 'captcha_site_key' , $_POST["captcha_site_key"] );
                    } else {
                        echo "Please Enter Your ApiKey";
                    }
                } else {
                    update_option( 'wl-recaptcha-enabled', "off" );
                    update_option( 'captcha_api_key' , $_POST["captcha_api_key"] );
                    update_option( 'captcha_site_key' , $_POST["captcha_site_key"] );
                } 
            }
         
         ?>
         
            <div class="recaptcha-menu-page">
                <br /><br />
                <img class="plugin_logo" style="width: 450px" src="https://media-exp2.licdn.com/media/AAEAAQAAAAAAAAt5AAAAJDZmZjBjODAwLTFlNGItNDRlNy04NmFmLWYxNjhmZmNjNjdmMA.png">
                <h1>LNB Recaptcha Settings</h1>
                <br />
                <form id="recaptcha-form" method="post">
                    <img style="width: 100px" src="https://www.google.com/recaptcha/intro/images/hero-recaptcha-invisible.gif" ><br /><br />
                    <span style="font-size:14px">Use "default" for the standard LNB Site Key/Secret, or input custom ones for that domain.</span><br /><br /><br />
                    <span style="font-size: 18px;">Enable ReCaptcha &nbsp; &nbsp; </span>
                        <input class="rc-api-key-check" type="checkbox" name="repcatcha-enable" <?php if (get_option("wl-recaptcha-enabled") == "on") { echo "checked"; } ?>  /><br /> <br /> 
                  
                        <span style="font-size: 18px;">Google Recaptcha Site Key &nbsp; &nbsp; </span><input type="textbox" style="width: 400px; text-align: left;" name="captcha_site_key" 
                        value="<?php echo get_option("captcha_site_key");  ?>" /><br /> <br />
                    <span class="class="rc-api-key">
                        <span style="font-size: 18px;">Google Recaptcha Secret &nbsp; &nbsp; </span><input type="textbox" style="width: 400px; text-align: left;" name="captcha_api_key" 
                        value="<?php echo get_option("captcha_api_key");  ?>" /><br /> <br />
                    </span>
                
                    <span style="color:red"> <?php if (get_option("wl-recaptcha-enabled") == "on" && strlen(get_option("captcha_api_key")) < 38 && get_option("captcha_api_key") !== "default" ) { echo "Please Enter a Valid Google Recaptcha Secret to Enable"; } ?> </span><br/></ >
                     <span style="color:red"> <?php if (get_option("wl-recaptcha-enabled") == "on" && strlen(get_option("captcha_site_key")) < 38 && get_option("captcha_site_key") !== "default" ) { echo "Please Enter a Valid Google Recaptcha Site Key to Enable"; } ?> </span>
                    <br />
                    <input type="submit" name="submit" value="Save Options"  />
                </form>
            </div>
            
            <?php
            
            //print_r($_POST);
            
     }
    
}
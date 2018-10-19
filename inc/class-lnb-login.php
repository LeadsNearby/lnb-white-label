<?php

namespace lnb;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit('Direct script access denied.');
}

if (!class_exists('\lnb\LoginPage')) {
    class LoginPage {

        private static $instance = null;

        private function __construct() {
            add_filter('login_headerurl', [$this, 'customize_loginlogo_url']);
            add_action('login_enqueue_scripts', array($this, 'load_styles'), 99);
            add_action('login_head', array($this, 'login_head'));
            add_action('login_footer', [$this, 'login_footer']);
        }

        public static function get_instance() {
            if (static::$instance == null) {
                static::$instance = new self();
            }
            return static::$instance;
        }

        public function customize_loginlogo_url($url) {
            return 'https://www.leadsnearby.com/';
        }

        public function load_styles() {
            wp_register_style('lnb-white-label-login', plugins_url('../assets/css/login-style.css', __FILE__));
            wp_enqueue_style('lnb-white-label-login');
        }

        public function login_head() {?>
            <style>
                :root {
                    --logo: url('<?php echo plugins_url('../assets/images/logo.svg', __FILE__); ?>');
                }
            </style>
            <script>
                (function(){
                    window.onload = function() {
                        var form = document.querySelector('.login-wrapper')
                        var logo = new Image()
                        logo.src = '<?php echo plugins_url('../assets/images/logo.svg', __FILE__); ?>'
                        logo.onload = function() {
                            form.classList.add('animate')
                        }
                    }
                })()
            </script>
        <?php }

        public function login_footer() {?>

            <script>
                document.querySelector('#login > h1 > a').target = 'blank'

                var login = document.querySelector('#login')
                var loginWrapper = document.createElement('div')
                loginWrapper.classList.add('login-wrapper')
                loginWrapper.appendChild(login)
                var body = document.querySelector('body')
                body.appendChild(loginWrapper)
                var inputs = document.getElementsByClassName('input')
                for(let i = 0; i < inputs.length; i++) {
                    inputs[i].parentElement.parentElement.prepend(inputs[i])
                }
                inputs[0].required  = true
                inputs[1].required  = true
            </script>

        <?php }
    }
}

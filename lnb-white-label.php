<?php
/*
Plugin Name: LeadsNearby White Label
Plugin URI: http://www.leadsnearby.com
Description: Brands the Wordpress Backend for LeadsNearby
Version: 3.0.0
Author: LeadsNearby
Author URI: http://www.leadsnearby.com
 */

require_once plugin_dir_path(__FILE__) . 'classes/class-dashboard.php';
use lnb\Dashboard;

$dashboard = Dashboard::get_instance(__FILE__);

require 'recaptcha/lnb_recaptcha.php';
require 'recaptcha/menu_page.php';

add_filter('login_headerurl', 'custom_loginlogo_url');
function custom_loginlogo_url($url) {
    return 'https://www.leadsnearby.com/';
}

class LNB_White_Label {

    public function __construct() {

        add_action('login_enqueue_scripts', array($this, 'init_login_styles'), 99);
        add_action('login_enqueue_scripts', array($this, 'init_login_scripts'), 0);
        add_action('login_head', array($this, 'init_login_head'));

        add_action('wp_before_admin_bar_render', array($this, 'remove_admin_bar_links'));
        add_action('admin_bar_menu', array($this, 'add_admin_bar_links'), 25);

        add_action('login_footer', [$this, 'login_footer']);

        add_action('admin_head', [$this, 'hide_theme_menus']);

        if (is_admin()) {
            $this->admin_init();
        }

        add_filter('upload_size_limit', [$this, 'limit_upload_size']);
        add_filter('wp_handle_upload_prefilter', [$this, 'filter_limit_upload_size']);
    }

    public function init_admin_styles() {
        wp_register_style('lnb-white-label', plugins_url('assets/css/admin-style.css', __FILE__));
        wp_enqueue_style('lnb-white-label');
    }

    public function init_login_styles() {
        wp_register_style('lnb-white-label-login', plugins_url('assets/css/login-style.css', __FILE__));
        wp_enqueue_style('lnb-white-label-login');
    }

    public function init_login_scripts() {
    }

    public function init_login_head() {?>
        <style>
            :root {
                --logo: url('<?php echo plugins_url('assets/images/logo.svg', __FILE__); ?>');
            }
        </style>
        <script>
            (function(){
                window.onload = function() {
                    var form = document.querySelector('.login-wrapper')
                    var logo = new Image()
                    logo.src = '<?php echo plugins_url('assets/images/logo.svg', __FILE__); ?>'
                    logo.onload = function() {
                        form.classList.add('animate')
                    }
                }
            })()
        </script>
    <?php }

    public function login_footer() {?>

        <script>
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

    public static function add_user_role() {
        $permissions = array(
            'delete_others_pages' => true,
            'delete_others_posts' => true,
            'delete_pages' => true,
            'delete_posts' => true,
            'delete_private_pages' => true,
            'delete_private_posts' => true,
            'delete_published_pages' => true,
            'delete_published_posts' => true,
            'edit_others_pages' => true,
            'edit_others_posts' => true,
            'edit_pages' => true,
            'edit_posts' => true,
            'edit_private_pages' => true,
            'edit_private_posts' => true,
            'edit_published_pages' => true,
            'edit_published_posts' => true,
            'manage_categories' => true,
            'manage_links' => true,
            'moderate_comments' => false,
            'publish_pages' => true,
            'publish_posts' => true,
            'read' => true,
            'read_private_pages' => true,
            'read_private_posts' => true,
            'unfiltered_html' => true,
            'upload_files' => true,
            'install_plugins' => false,
            'install_themes' => false,
            'list_users' => false,
            'manage_options' => false,
            'promote_users' => false,
            'remove_users' => false,
            'switch_themes' => false,
            'update_themes' => false,
            'edit_dashboard' => false,
            'edit_themes' => false,
            'update_plugin' => false,
            'update_core' => false,
            'activate_plugins' => false,
            'create_users' => false,
            'delete_plugins' => false,
            'delete_themes' => false,
            'delete_users' => false,
            'edit_files' => false,
            'edit_plugins' => false,
            'edit_theme_options' => true,
            'edit_users' => false,
            'export' => false,
            'import' => false,
            'gravityforms_view_entries' => true,
            'gravityforms_edit_entries' => true,
            'gravityforms_delete_entries' => true,
            'gravityforms_edit_forms' => true,
        );
        $wp_roles = new WP_Roles();

        $lnb_role = $wp_roles->get_role('lnb_client');

        if (!$lnb_role) {
            add_role('lnb_client', __('LeadsNearby Client'), $permissions);
        } else {
            foreach ($permissions as $cap => $grant) {
                $lnb_role->add_cap($cap, $grant);
            }
        }

        // Removes old LNB Admin Role
        $wp_roles->remove_role('admin');
        $wp_roles->remove_role('client');
    }

    public static function remove_user_role() {

        $wp_roles = new WP_Roles();

        $wp_roles->remove_role('lnb_client');
        $wp_roles->remove_role('admin');
        $wp_roles->remove_role('client');

    }

    public function admin_init() {
        require_once plugin_dir_path(__FILE__) . '/updater/github-updater.php';
        new GitHubPluginUpdater(__FILE__, 'LeadsNearby', 'lnb-white-label');

        add_action('admin_enqueue_scripts', array($this, 'init_admin_styles'));
        add_filter('admin_footer_text', array($this, 'add_footer_text'));

    }

    public function hide_theme_menus() {
        remove_submenu_page('themes.php', 'themes.php'); // hide the theme selection submenu
        // remove_submenu_page('themes.php', 'widgets.php'); // hide the widgets submenu
        remove_submenu_page('themes.php', 'customize.php?return=%2Fwp-admin%2Ftools.php'); // hide the customizer submenu
        remove_submenu_page('themes.php', 'customize.php?return=%2Fwp-admin%2Fwidgets.php'); // hide the background submenu
        remove_submenu_page('themes.php', 'customize.php?return=%2Fwp-admin%2Fnav-menus.php'); // hide the background submenu
        remove_submenu_page('themes.php', 'customize.php?return=%2Fwp-admin%2Fadmin.php%3Fpage%3Dfire-options'); // hide the background submenu
        remove_submenu_page('themes.php', 'customize.php?return=%2Fwp-admin%2Fedit.php'); // hide the background submenu
    }

    public function add_footer_text() {
        echo '<a href="https://www.leadsnearby.com/" target="_blank" title="LeadsNearby Local SEO and Web Design"><img style="display:inline-block;vertical-align:middle;width:150px;padding-right:1rem" src="' . plugins_url('assets/images/logo.svg', __FILE__) . '"></a>';
        echo '<span id="footer-thankyou">Developed by <a href="https://www.leadsnearby.com" target="_blank">LeadsNearby</a></span> | <a href="https://www.leadsnearby.com" target="_blank">Contact Us</a></span> | Call Us: <a href="http://www.leadsnearby.com" target="_blank">919-758-8420</a></span><span style="display:inline-block;width:1rem"></span>';
    }

    // Removes WP Logo from Dashboard
    public function remove_admin_bar_links() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('wp-logo');
    }

    // Add LeadsNearby Menu Links
    public function add_admin_bar_links() {

        $menu_array = array(
            array(
                'id' => 'lnb_parent_menu',
                'title' => 'LeadsNearby',
                'href' => __('www.leadsnearby.com'),
                'meta' => array(
                    'target' => 'blank',
                ),
            ),
            array(
                'parent' => 'lnb_parent_menu',
                'id' => 'lnb_menu_contact',
                'title' => __('Contact Us'),
                'href' => __('https://www.leadsnearby.com/contact-us/'),
                'meta' => array(
                    'target' => 'blank',
                ),
            ),
            // 2 => array(
            //     'parent' => 'lnb_parent_menu',
            //     'id' => 'lnb_menu_client_group',
            //     'title' => __('Client Area'),
            //     'href' => __('www.leadsnearby.com/login/'),
            //     'meta' => array(
            //         'class' => 'st_menu_download',
            //     ),
            // ),
            // 3 => array(
            //     'parent' => 'lnb_menu_client_group',
            //     'id' => 'lnb_menu_client_login',
            //     'title' => __('Client Login'),
            //     'href' => __('www.leadsnearby.com/login/'),
            // ),
            // 4 => array(
            //     'parent' => 'lnb_menu_client_group',
            //     'id' => 'lnb_menu_client_resources',
            //     'title' => __('Client Resources'),
            //     'href' => __('www.leadsnearby.com/resources/'),
            // ),
        );

        $user = wp_get_current_user();
        $theme = wp_get_theme();
        if (is_multisite() && in_array('lnb_client', (array) $user->roles)) {
            $sites = get_blogs_of_user($user->id);
            if (count($sites) > 1) {
                $menu_array[] = array(
                    'id' => 'lnb_site_list',
                    'title' => 'Your Sites',
                    'href' => '#',
                );

                foreach ($sites as $site) {
                    $menu_array[] = array(
                        'parent' => 'lnb_site_list',
                        'id' => 'lnb_site_list_' . $site->userblog_id,
                        'title' => '<span style="display:inline-block;background-image: url(data:image/svg+xml;base64,PHN2ZyBhcmlhLWhpZGRlbj0idHJ1ZSIgZGF0YS1wcmVmaXg9ImZhcyIgZGF0YS1pY29uPSJmaXJlIiBjbGFzcz0ic3ZnLWlubGluZS0tZmEgZmEtZmlyZSBmYS13LTEyIiByb2xlPSJpbWciIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgdmlld0JveD0iMCAwIDM4NCA1MTIiIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+PHBhdGggZmlsbD0iYmxhbmsiIGQ9Ik0yMTYgMjMuODU4YzAtMjMuODAyLTMwLjY1My0zMi43NjUtNDQuMTQ5LTEzLjAzOEM0OCAxOTEuODUxIDIyNCAyMDAgMjI0IDI4OGMwIDM1LjYyOS0yOS4xMTQgNjQuNDU4LTY0Ljg1IDYzLjk5NEMxMjMuOTggMzUxLjUzOCA5NiAzMjIuMjIgOTYgMjg3LjA0NnYtODUuNTFjMC0yMS43MDMtMjYuNDcxLTMyLjIyNS00MS40MzItMTYuNTA0QzI3LjgwMSAyMTMuMTU4IDAgMjYxLjMzMiAwIDMyMGMwIDEwNS44NjkgODYuMTMxIDE5MiAxOTIgMTkyczE5Mi04Ni4xMzEgMTkyLTE5MmMwLTE3MC4yOS0xNjgtMTkzLjAwMy0xNjgtMjk2LjE0MnoiPjwvcGF0aD48L3N2Zz4=);width: 20px;height:100%;vertical-align:middle;background-size:contain;background-repeat:no-repeat;padding-left:4px; color:inherit;"></span>' . $site->blogname,
                        'href' => get_admin_url($site->userblog_id),
                    );
                }
            }
        }

        if (!is_super_admin()) {
            global $wp_admin_bar;

            foreach ($menu_array as $menu_item => $args) {
                $wp_admin_bar->add_menu($args);
            }
        }
    }

    public function limit_upload_size($limit) {
        $user = \wp_get_current_user();
        if (in_array('lnb_client', (array) $user->roles)) {
            $limit = 1024 * 1000;
        }
        return $limit;
    }

    public function filter_limit_upload_size($file) {
        $user = \wp_get_current_user();
        if (in_array('lnb_client', (array) $user->roles)) {
            $limit = 1024 * 1000;
            if ($file['size'] > $limit) {
                $file['error'] = __('Maximum filesize is 1mb');
            }
        }
        return $file;
    }

}

$white_label = new LNB_White_Label();

if (is_admin()) {
    $recaptcha_menu_page = new reCaptchaMenuPage();
}

$recaptcha_options = get_option('lnb-recaptcha');
if ($recaptcha['enabled'] == "on" && strlen(get_option("captcha_api_key")) > 38 && strlen(get_option("captcha_site_key")) > 38) {
    $recaptcha = new lnbRecaptcha();
} elseif (get_option("wl-recaptcha-enabled") == "on" && get_option("captcha_api_key") == "default" && get_option("captcha_site_key") == "default") {
    $recaptcha = new lnbRecaptcha();
}

register_activation_hook(__FILE__, array('LNB_White_Label', 'add_user_role'));
register_uninstall_hook(__FILE__, array('LNB_White_Label', 'remove_user_role'));
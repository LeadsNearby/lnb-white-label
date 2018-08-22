<?php

namespace lnb;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit( 'Direct script access denied.' );
}

class Dashboard {

    private static $instance = null;
    private $plugin_directory = null;
    private $plugin_url = null;
    private $user = null;
    private $user_is_client = false;
    
    private function __construct( $plugin_directory ) {
        $this->plugin_directory = plugin_dir_path( $plugin_directory );
        $this->plugin_url = plugin_dir_url(  $plugin_directory );
        add_action( 'admin_init', [ $this, 'check_if_user_is_client'] );
        add_action( 'admin_menu', [ $this, 'hide_default_dashboard'] );
        // add_action( 'admin_menu', [ $this, 'add_dashboard_menu_item'] );
        // add_action( 'admin_menu', [ $this, 'test_me'] );
    }

    public static function get_instance( $file = 'lnb-white-label.php' ) {
        if ( static::$instance == null ) {
            static::$instance = new self( $file );
        }
        return static::$instance;
    }

    public function check_if_user_is_client() {
        $this->user = \wp_get_current_user();
        if ( in_array( 'lnb_client', (array) $this->user->roles ) ) {
            $this->user_is_client = true;
        }
    }

    public function hide_default_dashboard() {
        global $menu;

        if( ! $this->user ) {
            $this->check_if_user_is_client();
        }

        if( ! $this->user_is_client ) {
            return;
        }

        $dashboard_key = 0;
        foreach( $menu as $index => $value ) {
            if( $value[0] == 'Dashboard' ) {
                $dashboard_key = $index;
                break;
            }
        }
        if( $dashboard_key ) {
            unset( $menu[$dashboard_key] );
        }
        if ( preg_match( '#wp-admin/?(index.php)?$#', $_SERVER['REQUEST_URI'] ) ) {
            wp_redirect( site_url() . '/wp-admin/admin.php?page=fire-options' );
        }
    }

    public function render_dashboard() {
        ob_start();
        
        ?>
        <div class="wrap">
            <h1>LeadsNearby Client Dashboard</h1>
        </div>

        <?php

        echo ob_get_clean();

    }

    public function add_dashboard_menu_item() {
        add_menu_page(
            'Client Dashboard',
            'Client Dashboard',
            'read',
            'lnb-client-dashboard.php',
            [ $this, 'render_dashboard' ],
            $this->plugin_url . 'assets/images/icon-bw.svg',
            2
        );
    }

    public function test_me() {
        global $menu;
        ob_start();
        echo '<pre>';
        print_r($menu);
        echo '</pre>';
        wp_die(ob_get_clean());
    }

}

?>
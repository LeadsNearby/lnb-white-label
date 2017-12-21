<?PHP
/*
Plugin Name: LeadsNearby White Label
Plugin URI: http://www.leadsnearby.com
Description: Brands the Wordpress Backend for LeadsNearby
Version: 2.2.3
Author: LeadsNearby
Author URI: http://www.leadsnearby.com
License: GPLv3
*/

require 'recaptcha/lnb_recaptcha.php';
require 'recaptcha/menu_page.php';


class LNB_White_Label {

	public function __construct() {

		add_action( 'login_enqueue_scripts', array( $this, 'init_login_styles' ) );
		add_action( 'login_enqueue_scripts', array( $this, 'init_login_scripts' ), 0);
		add_action( 'login_head', array( $this, 'init_login_head' ) );

		add_action( 'wp_before_admin_bar_render', array( $this, 'remove_admin_bar_links' ) );
		add_action( 'admin_bar_menu', array( $this, 'add_admin_bar_links' ) ,25 );

		if( is_admin() ) {
			$this->admin_init();
		}
	}

	function init_admin_styles() {
		wp_register_style( 'lnb-white-label', plugins_url( '/css/style.css', __FILE__ ) );
		wp_enqueue_style( 'lnb-white-label' );

		if ( !current_user_can( 'update_core' ) ) {
			echo '<style>.update-nag, .updated { display: none; }</style>';
		}
	}

	function init_login_styles() {
		wp_register_style( 'lnb-white-label-animate', plugins_url( '/css/animate.min.css', __FILE__ ) );
		wp_register_style( 'lnb-white-label-login', plugins_url( '/css/login-style.css', __FILE__ ) );
		wp_enqueue_style( 'lnb-white-label-animate' );
		wp_enqueue_style( 'lnb-white-label-login' );
	}

	function init_login_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_register_script( 'whitelabel-admin-commons', plugins_url( '/js/admin-commons.js',__FILE__ ),'','1.1', true );
		wp_enqueue_script( 'whitelabel-admin-commons' );
	}

	function init_login_head() { ?>
		<script type="text/javascript">
			jQuery(document).ready(function($){
				var num = ['one', 'two', 'three', 'four', 'five', 'six'];
				for( i = 0; i < num.length; i++ ) {
					$('#login').prepend('<div class="pin section-' + num[i] + ' animated bounceInDown"><img src="<?php echo plugins_url( '/images/lead-gen-white.png', __FILE__ ); ?>" /></div>');
				}
			});
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
			'moderate_comments' => true,
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
			'manage_options' => true, 				
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
			'edit_theme_options' => false,							
			'edit_users' => false,				
			'export' => false,				
			'import' => false,  
			);
		$wp_roles = new WP_Roles();

		$lnb_role = $wp_roles->get_role( 'lnb_client' );

		if( ! $lnb_role ) {
			add_role( 'lnb_client', __( 'LeadsNearby Client' ), $permissions );
		}
		else {
			$wp_roles->remove_role( 'lnb_client' );
			add_role( 'lnb_client', __( 'LeadsNearby Client' ), $permissions );
		}

		// Removes old LNB Admin Role
		$wp_roles->remove_role( 'admin' );
		$wp_roles->remove_role( 'client' );
	}

	public static function remove_user_role() {

		$wp_roles = new WP_Roles();

		$wp_roles->remove_role( 'lnb_client' );
		$wp_roles->remove_role( 'admin' );
		$wp_roles->remove_role( 'client' );

	}

	function admin_init() {
		require_once( plugin_dir_path( __FILE__ ) .'/updater/github-updater.php' );
		new GitHubPluginUpdater( __FILE__, 'LeadsNearby', 'lnb-white-label' );

		add_action( 'admin_enqueue_scripts', array( $this, 'init_admin_styles' ) );
		add_filter('admin_footer_text', array( $this, 'add_footer_text' ) );
	}

	function add_footer_text() {
		echo '<a href="http://www.leadsnearby.com/" target="_blank" title="LeadsNearby Local SEO and Web Design"><img src="' . plugins_url( 'images/lnb_white_label_footer.png' , __FILE__ ) . '"></a>';
		echo '<span id="footer-thankyou">Developed by <a href="http://www.leadsnearby.com" target="_blank">LeadsNearby</a></span> | <a href="http://www.leadsnearby.com" target="_blank">Contact Us</a></span> | Call Us: <a href="http://www.leadsnearby.com" target="_blank">919-758-8420</a></span>';
	}

	// Removes WP Logo from Dashboard
	function remove_admin_bar_links() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('wp-logo');
	}
	
	// Add LeadsNearby Menu Links
	function add_admin_bar_links() {
		
		$menu_array = array(
			0 => array(
				'id' => 'lnb_parent_menu',
				'title' => __( 'LeadsNearby Menu'),
				'href' => __( 'www.leadsnearby.com' ),
				),
			1 => array(
				'parent' => 'lnb_parent_menu',
				'id' => 'lnb_menu_contact',
				'title' => __( 'Contact LeadsNearby | 919-758-8420'),
				'href' => __('www.leadsnearby.com/contact-us/'),
				),
			2 => array(
				'parent' => 'lnb_parent_menu',
				'id'     => 'lnb_menu_client_group',
				'title' => __( 'Client Area'),
				'href' => __('www.leadsnearby.com/login/'),
				'meta'   => array(
					'class' => 'st_menu_download',
					)
				),
			3 => array(
				'parent' => 'lnb_menu_client_group',
				'id'     => 'lnb_menu_client_login',
				'title' => __( 'Client Login'),
				'href' => __('www.leadsnearby.com/login/'),
				),
			4 => array(
				'parent' => 'lnb_menu_client_group',
				'id'     => 'lnb_menu_client_resources',
				'title' => __( 'Client Resources'),
				'href' => __('www.leadsnearby.com/resources/'),
				)
			);

		if( !is_super_admin() ) {
			global $wp_admin_bar;

			foreach( $menu_array as $menu_item => $args ) {
				$wp_admin_bar->add_menu( $args );
			}
		}
	}

}

$white_label = new LNB_White_Label();

if (is_admin()) {
    $recaptcha_menu_page = new reCaptchaMenuPage();
}

if (get_option("wl-recaptcha-enabled") == "on" && strlen(get_option("captcha_api_key")) > 38 && strlen(get_option("captcha_site_key")) > 38) {
    $recaptcha = new lnbRecaptcha();
} elseif (get_option("wl-recaptcha-enabled") == "on" && get_option("captcha_api_key") == "default" && get_option("captcha_site_key") == "default" ) {
    $recaptcha = new lnbRecaptcha();
}


register_activation_hook( __FILE__, array( 'LNB_White_Label', 'add_user_role' ) );
register_uninstall_hook( __FILE__, array( 'LNB_White_Label', 'remove_user_role' ) );

?>
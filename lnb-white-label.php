<?PHP
/*
Plugin Name: LeadsNearby White Label
Plugin URI: http://www.leadsnearby.com
Description: Brands the Wordpress Backend for LeadsNearby
Version: 2.0
Author: LeadsNearby
Author URI: http://www.leadsnearby.com
License: GPLv2
*/
	function lnb_white_label_styles() {
		wp_register_style( 'lnb_white_label_stylesheet', plugins_url( '/css/style.css', __FILE__ ) );
		wp_enqueue_style( 'lnb_white_label_stylesheet' );
	}
	add_action( 'admin_enqueue_scripts', 'lnb_white_label_styles' );
	
	function lnb_white_label_footer_text () {
		echo '<a href="http://www.leadsnearby.com/" target="_blank" title="LeadsNearby Local SEO and Web Design"><img src="' . plugins_url( 'images/lnb_white_label_footer.png' , __FILE__ ) . '"></a>';
	}
	add_filter( 'admin_footer_text', 'lnb_white_label_footer_text' );
	
	function custom_login_css() {
		wp_register_style( 'lnb_white_label_animate_stylesheet', plugins_url( '/css/animate.css', __FILE__ ) );
		wp_register_style( 'lnb_white_label_login_stylesheet', plugins_url( '/css/login-style.css', __FILE__ ) );
		wp_enqueue_style( 'lnb_white_label_animate_stylesheet' );
		wp_enqueue_style( 'lnb_white_label_login_stylesheet' );
	}
	add_action('login_head', 'custom_login_css');

	add_action( 'login_enqueue_scripts', 'whitelabel_pins' );
	function whitelabel_pins() {
	?>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
		<script type="text/javascript">
		jQuery(function() {
			jQuery('#login').prepend('<div class="pin section-one animated bounceInDown"><img src="<?php echo plugins_url( '/images/lead-gen-white.png', __FILE__ ); ?>" /></div>');
			jQuery('#login').prepend('<div class="pin section-two animated bounceInDown"><img src="<?php echo plugins_url( '/images/lead-gen-white.png', __FILE__ ); ?>" /></div>');
			jQuery('#login').prepend('<div class="pin section-three animated bounceInDown"><img src="<?php echo plugins_url( '/images/lead-gen-white.png', __FILE__ ); ?>" /></div>');
			jQuery('#login').prepend('<div class="pin section-four animated bounceInDown"><img src="<?php echo plugins_url( '/images/lead-gen-white.png', __FILE__ ); ?>" /></div>');
			jQuery('#login').prepend('<div class="pin section-five animated bounceInDown"><img src="<?php echo plugins_url( '/images/lead-gen-white.png', __FILE__ ); ?>" /></div>');
			jQuery('#login').prepend('<div class="pin section-six animated bounceInDown"><img src="<?php echo plugins_url( '/images/lead-gen-white.png', __FILE__ ); ?>" /></div>');
			jQuery('#login').prepend('<div class="pin section-seven animated bounceInDown"><img src="<?php echo plugins_url( '/images/lead-gen-white.png', __FILE__ ); ?>" /></div>');
			jQuery('#login').prepend('<div class="pin section-eight animated bounceInDown"><img src="<?php echo plugins_url( '/images/lead-gen-white.png', __FILE__ ); ?>" /></div>');
			jQuery('#login').prepend('<div class="pin section-nine animated bounceInDown"><img src="<?php echo plugins_url( '/images/lead-gen-white.png', __FILE__ ); ?>" /></div>');
		});
		</script>
	<?php
	}

	add_action('login_enqueue_scripts', 'whitelabel_admin_scripts');
	function whitelabel_admin_scripts() {
		/* Register our script. */
		//wp_enqueue_script('jquery');
		wp_register_script('whitelabel-admin-commons', plugins_url('/js/admin-commons.js',__FILE__),'','1.1', true);
		wp_enqueue_script('whitelabel-admin-commons');
	}

	//Replaces text on WP Admin Dashboard
	function remove_footer_admin ()
	{
		echo '<span id="footer-thankyou">Developed by <a href="http://www.leadsnearby.com" target="_blank">LeadsNearby</a></span> | <a href="http://www.leadsnearby.com" target="_blank">Contact Us</a></span> | Call Us: <a href="http://www.leadsnearby.com" target="_blank">919-758-8420</a></span>';
	}
	add_filter('admin_footer_text', 'remove_footer_admin');	

	//Removes WP Logo from Dashboard
	function remove_admin_bar_links() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('wp-logo');
	}
	add_action( 'wp_before_admin_bar_render', 'remove_admin_bar_links' );
	
	//Removes notifications for non admin users
	function my_admin_theme_style() {
		if (!current_user_can( 'update_core' )) {
			echo '<style>.update-nag, .updated { display: none; }</style>';
		}
	}
	add_action('admin_enqueue_scripts', 'my_admin_theme_style');
	add_action('login_enqueue_scripts', 'my_admin_theme_style');
	
	function add_lnb_admin_bar_link() {
		global $wp_admin_bar;
		if ( !is_super_admin() || !is_admin_bar_showing() )
			return;
		$wp_admin_bar->add_menu( array(
		'id' => 'lnb_link',
		'title' => __( 'LeadsNearby Menu'),
		'href' => __('http://leadsnearby.com'),
		));

		// Add sub menu link "View All Posts"
		$wp_admin_bar->add_menu( array(
			'parent' => 'lnb_link',
			'id'     => 'lnb_all',
			'title' => __( 'Contact LeadsNearby | 919-758-8420'),
			'href' => __('http://www.leadsnearby.com/contact-us/'),
		));

		// Add sub menu link "Downloads"
		$wp_admin_bar->add_menu( array(
			'parent' => 'lnb_link',
			'id'     => 'lnb_clients',
			'title' => __( 'Client Area'),
			'href' => __('https://www.leadsnearby.com/login/'),
			'meta'   => array(
				'class' => 'st_menu_download',),
		));
		$wp_admin_bar->add_menu( array(
			'parent' => 'lnb_clients',
			'id'     => 'lnb_login',
			'title' => __( 'Client Login'),
			'href' => __('https://www.leadsnearby.com/login/'),
		));
		$wp_admin_bar->add_menu( array(
			'parent' => 'lnb_clients',
			'id'     => 'lnb_resources',
			'title' => __( 'Client Resources'),
			'href' => __('http://www.leadsnearby.com/resources/'),
		));
	}
	add_action('admin_bar_menu', 'add_lnb_admin_bar_link',25);

    // Add LeadsNearby Client User Role    
    $result = add_role( 'client', __('LeadsNearby Client' ),   
		array(    
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
			'edit_theme_options' => false,							
			'edit_users' => false,				
			'export' => false,				
			'import' => false   
		)   
    );
	
	//Get the role again - need to do this incase the role already exists, and add_role returned null.
	$lnb_client = get_role('client');
	$lnb_client->add_cap('level_1');

	//Removes old LNB Admin Role
	$wp_roles = new WP_Roles(); $wp_roles->remove_role("admin")
?>
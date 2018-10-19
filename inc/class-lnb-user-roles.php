<?php

namespace lnb;

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit('Direct script access denied.');
}

use \WP_Roles;

if (!class_exists('\lnb\UserRoles')) {
    class UserRoles {

        private static $roles = array(
            array(
                'id' => 'lnb_client',
                'title' => 'LeadsNearby Client',
                'perms' => array(
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
                ),
            ),
            array(
                'id' => 'lnb_csm',
                'title' => 'LeadsNearby CSM',
                'perms' => array(
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
                ),
            ),
        );

        public function __construct() {}

        public static function add_user_roles() {
            $wp_roles = new WP_Roles();
            if (is_multisite()) {
                $sites = get_sites();
                foreach ($sites as $site) {
                    switch_to_blog($site);
                    foreach (static::$roles as $role) {
                        static::add_user_role($role, $wp_roles);
                    }

                    // Removes old LNB Admin Role
                    $wp_roles->remove_role('admin');
                    $wp_roles->remove_role('client');
                    restore_current_blog();
                }
                return;
            }

            foreach (static::$roles as $role) {
                static::add_user_role($role, $wp_roles);
            }

            // Removes old LNB Admin Role
            $wp_roles->remove_role('admin');
            $wp_roles->remove_role('client');
        }

        private static function add_user_role($role, $wp_roles = null) {
            if (!$wp_roles) {
                $wp_roles = new WP_Roles();
            }

            $role_exists = $wp_roles->get_role($role['id']);
            if (!$role_exists) {
                return add_role($role['id'], __($role['title']), $role['perms']);
            } else {
                remove_role($role['id']);
                return add_role($role['id'], __($role['title']), $role['perms']);
                // foreach ($role['perms'] as $cap => $grant) {
                //     $role_exists->add_cap($cap, $grant);
                // }
                // return true;
            }

        }

        public static function remove_user_roles() {

            $wp_roles = new WP_Roles();
            $wp_roles->remove_role('lnb_client');
            $wp_roles->remove_role('lnb_csm');
            $wp_roles->remove_role('admin');
            $wp_roles->remove_role('client');

        }

    }
}

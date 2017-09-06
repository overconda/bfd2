<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.facebook.com/kergrit.robkop
 * @since             1.0.0
 * @package           Singhabeerfinder
 *
 * @wordpress-plugin
 * Plugin Name:       Singha Beer Finder
 * Plugin URI:        http://www.singhabeerfinder.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            KERGRIT ROBKOP
 * Author URI:        https://www.facebook.com/kergrit.robkop
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       singhabeerfinder
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-singhabeerfinder-activator.php
 */
function activate_singhabeerfinder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-singhabeerfinder-activator.php';
	Singhabeerfinder_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-singhabeerfinder-deactivator.php
 */
function deactivate_singhabeerfinder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-singhabeerfinder-deactivator.php';
	Singhabeerfinder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_singhabeerfinder' );
register_deactivation_hook( __FILE__, 'deactivate_singhabeerfinder' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-singhabeerfinder.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_singhabeerfinder() {

	$plugin = new Singhabeerfinder();
	$plugin->run();

}
run_singhabeerfinder();

/* Hard Code Here */
remove_action('welcome_panel', 'wp_welcome_panel');
add_action('wp_dashboard_setup', function(){
		remove_meta_box('dashboard_right_now', 'dashboard', 'normal');   // Right Now
		remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); // Recent Comments
		remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');  // Incoming Links
		remove_meta_box('dashboard_plugins', 'dashboard', 'normal');   // Plugins
		remove_meta_box('dashboard_quick_press', 'dashboard', 'side');  // Quick Press
		remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');  // Recent Drafts
		remove_meta_box('dashboard_primary', 'dashboard', 'side');   // WordPress blog
		remove_meta_box('dashboard_secondary', 'dashboard', 'side');   // Other WordPress News
		remove_meta_box('dashboard_activity', 'dashboard', 'normal');
	}
);

add_filter('screen_options_show_screen', '__return_false');
add_filter( 'contextual_help', function ($old_help, $screen_id, $screen){
    $screen->remove_help_tabs();
    return $old_help;
}, 999, 3 );

add_action( 'wp_before_admin_bar_render', function(){
 global $wp_admin_bar;
    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_menu('new-content');  
    $wp_admin_bar->remove_menu('wp-logo'); 
});

add_action( 'admin_head', function(){	
	global $post;
	$style = '';
    $style .= '<style type="text/css">';
    $style .= '#footer-thankyou, #setting-error-settings_updated, #message, #tagsdiv-reward_category, .term-description-wrap,.term-slug-wrap, .inline.hide-if-no-js, .row-actions .view, #edit-slug-box, #wp-admin-bar-view, #minor-publishing-actions, #visibility, .num-revisions, .curtime, #post-body-content ul.qtranxs-lang-switch-wrap:nth-child(1), #qtranxs-meta-box-lsb';
    if($post->post_type=="page"){
    	$style .= ',.page-title-action,.edit-post-status,#delete-action';
    }
    $style .= '{display: none; }';
    $style .= '</style>';
    echo $style;
});

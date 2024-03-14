<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.scribit.it/
 * @since             1.0.5.1
 * @package           proofreading
 *
 * @wordpress-plugin
 * Plugin Name:       Proofreading
 * Plugin URI:        https://www.scribit.it/en/wordpress-plugins/proofreading-wordpress-plugin-corrects-your-errors/
 * Description:       Proofreading allows you to correct texts on your Wordpress site. This plugin allows you to proofread in 30 different languages on articles and pages of your site also providing useful tips for the improvement of your writings.
 * Version:           1.2.1
 * Author:            Scribit
 * Author URI:        https://www.scribit.it/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       proofreading
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'proofreading-consts.php';

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-proofreading-activator.php
 */
function activate_proofreading() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-proofreading-activator.php';
	Proofreading_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-proofreading-deactivator.php
 */
function deactivate_proofreading() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-proofreading-deactivator.php';
	Proofreading_Deactivator::deactivate();
}

/**
 * The code that runs during plugin uninstall.
 * This action is documented in includes/class-proofreading-uninstaller.php
 */
function uninstall_proofreading() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-proofreading-uninstaller.php';
	Proofreading_Uninstaller::uninstall();
}

register_activation_hook( __FILE__, 'activate_proofreading' );
register_deactivation_hook( __FILE__, 'deactivate_proofreading' );
register_uninstall_hook( __FILE__, 'uninstall_proofreading' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-proofreading.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_proofreading() {

	$plugin = new Proofreading();
	$plugin->run();

}
run_proofreading();

		
require_once plugin_dir_path( __FILE__ ) . 'admin/includes/class-ajax-handler.php';
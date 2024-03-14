<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @since      1.0.0
 * @package    proofreading
 * @author     Scribit <wordpress@scribit.it>
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require_once plugin_dir_path( __FILE__ ) . 'proofreading-consts.php';

global $wpdb;

$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}proofreading_languages");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}proofreading_rules");
$wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}proofreading_rules_settings");

delete_option( 'proofreading-language-default' );
delete_option( PROOFREADING_VERSION_SETTINGNAME );
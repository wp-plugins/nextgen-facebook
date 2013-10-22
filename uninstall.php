<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) || ! defined( 'WP_UNINSTALL_PLUGIN' ) )
	die( 'These aren\'t the droids you\'re looking for...' );

$slug = 'nextgen-facebook';
$acronym = 'ngfb';
$options = get_option( $acronym.'_options' );

if ( empty( $options['plugin_preserve'] ) ) {

	delete_option( $acronym.'_options' );
	delete_option( $acronym.'_update_error' );
	delete_option( 'external_updates-'.$slug );
	delete_site_option( $acronym.'_options_site' );

	// remove all stored admin notices
	foreach ( array( 'nag', 'err', 'inf' ) as $type ) {
		$msg_opt = $acronym.'_notices_'.$type;
		delete_option( $msg_opt );
		foreach ( get_users( array( 'meta_key' => $msg_opt ) ) as $user )
			delete_user_option( $user->ID, $msg_opt );
	}

	// remove metabox preferences from all users
	foreach ( array( 'meta-box-order', 'metaboxhidden', 'closedpostboxes' ) as $meta_name ) {
		foreach ( array( 'toplevel_page', 'open-graph_page' ) as $page_prefix ) {
			foreach ( array( 'general', 'advanced', 'social', 'style', 'about' ) as $settings_page ) {
				$meta_key = $meta_name.'_'.$page_prefix.'_'.$acronym.'-'.$settings_page;
				foreach ( get_users( array( 'meta_key' => $meta_key ) ) as $user )
					delete_user_option( $user->ID, $meta_key, true );
			}
		}
	}
}

?>

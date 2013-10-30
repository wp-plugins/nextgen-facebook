<?php
/*
Plugin Name: NGFB Open Graph+
Plugin URI: http://surniaulula.com/extend/plugins/nextgen-facebook/
Author: Jean-Sebastien Morisset
Author URI: http://surniaulula.com/
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Description: Improve the appearance and ranking of WordPress Posts, Pages, and eCommerce Products in Google Search and social website shares.
Version: 6.14dev4

Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'ngfbPlugin' ) ) {

	class ngfbPlugin {

		// class object variables
		public $debug;
		public $util;
		public $notices;
		public $opt;
		public $user;
		public $media;
		public $meta;
		public $style;
		public $script;
		public $cache;
		public $admin;
		public $head;
		public $tags;
		public $webpage;
		public $social;
		public $seo;
		public $pro;
		public $update;

		public $cf = array();		// config array defined in construct method
		public $is_avail = array();	// assoc array for other plugin checks
		public $options = array();	// individual blog/site options
		public $site_options = array();	// multisite options
		public $ngg_options = array();	// nextgen gallery options
		public $ngg_version = 0;	// nextgen gallery version

		public function __construct() {

			// define the config values
			require_once ( dirname( __FILE__ ).'/lib/config.php' );
			$classname = __CLASS__.'Config';
			$this->cf = $classname::get_config();
			$classname::set_constants( __FILE__ );
			$classname::require_libs();		// keep in construct for widgets

			register_activation_hook( __FILE__, array( &$this, 'network_activate' ) );
			register_deactivation_hook( __FILE__, array( &$this, 'network_deactivate' ) );
			register_uninstall_hook( __FILE__, array( __CLASS__, 'network_uninstall' ) );

			add_action( 'init', array( &$this, 'init_plugin' ), NGFB_INIT_PRIORITY );	// since wp 1.2.0
		}

		public function network_activate( $sitewide ) {
			self::do_multisite( $sitewide, array( &$this, 'activate_plugin' ) );
		}

		public function network_deactivate( $sitewide ) {
			self::do_multisite( $sitewide, array( &$this, 'deactivate_plugin' ) );
		}

		public static function network_uninstall() {
			$sitewide = true;
			self::do_multisite( $sitewide, array( __CLASS__, 'uninstall_plugin' ) );

			$classname = __CLASS__.'Config';
			$lca = $classname::get_config( 'lca' );
			delete_site_option( $lca.'_site_options' );
		}

		private static function do_multisite( $sitewide, $method, $args = array() ) {
			if ( is_multisite() && $sitewide ) {
				global $wpdb, $blog_id;
				$dbquery = 'SELECT blog_id FROM '.$wpdb->blogs;
				$ids = $wpdb->get_col( $dbquery );
				foreach ( $ids as $id ) {
					switch_to_blog( $id );
					call_user_func_array( $method, array( $args ) );
				}
				switch_to_blog( $blog_id );
			} else call_user_func_array( $method, array( $args ) );
		}

		private function activate_plugin() {
			$classname = $this->cf['lca'].'Check';
			$this->check = new $classname( $this );
			$this->check->wp_version();
			$this->setup_vars( true );
		}

		private function deactivate_plugin() {
			wp_clear_scheduled_hook( 'plugin_updates-'.$this->cf['slug'] );
		}

		private static function uninstall_plugin() {
			global $wpdb;
			$classname = __CLASS__.'Config';
			$lca = $classname::get_config( 'lca' );
			$slug = $classname::get_config( 'slug' );
			$options = get_option( $lca.'_options' );

			if ( empty( $options['plugin_preserve'] ) ) {

				// delete plugin settings
				delete_option( $lca.'_options' );

				// delete all custom post meta
				delete_post_meta_by_key( '_'.$lca.'_meta' );

				// delete metabox preferences for all users
				foreach ( array( 'meta-box-order', 'metaboxhidden', 'closedpostboxes' ) as $meta_name ) {
					foreach ( array( 'toplevel_page', 'open-graph_page' ) as $page_prefix ) {
						foreach ( array( 'general', 'advanced', 'social', 'style', 'about', 'network' ) as $settings_page ) {
							$meta_key = $meta_name.'_'.$page_prefix.'_'.$lca.'-'.$settings_page;
							foreach ( get_users( array( 'meta_key' => $meta_key ) ) as $user )
								delete_user_option( $user->ID, $meta_key, true );
						}
					}
				}

			}

			// delete update related options
			delete_option( 'external_updates-'.$slug );
			delete_option( $lca.'_update_error' );
			delete_option( $lca.'_update_time' );

			// delete all stored admin notices
			foreach ( array( 'nag', 'err', 'inf' ) as $type ) {
				$msg_opt = $lca.'_notices_'.$type;
				delete_option( $msg_opt );
				foreach ( get_users( array( 'meta_key' => $msg_opt ) ) as $user )
					delete_user_option( $user->ID, $msg_opt );
			}

			// delete transients
			$dbquery = 'SELECT option_name FROM '.$wpdb->options.' WHERE option_name LIKE \'_transient_timeout_'.$lca.'_%\';';
			$expired = $wpdb->get_col( $dbquery ); 
			foreach( $expired as $transient ) { 
				$key = str_replace('_transient_timeout_', '', $transient);
				delete_transient( $key );
			}
		}

		// called by WP init action
		public function init_plugin() {
			if ( is_feed() ) return;	// nothing to do in the feeds
			load_plugin_textdomain( NGFB_TEXTDOM, false, dirname( NGFB_PLUGINBASE ).'/languages/' );
			$this->setup_vars();
			if ( $this->debug->is_on() == true ) {
				foreach ( array( 'wp_head', 'wp_footer' ) as $action ) {
					foreach ( array( 1, 9999 ) as $prio )
						add_action( $action, create_function( '', 
							"echo '<!-- ".$this->cf['full']." add_action( \'$action\' ) Priority $prio Test = PASSED -->\n';" ), $prio );
				}
			}
		}

		// get the options, upgrade the options (if necessary), and validate their values
		private function setup_vars( $activate = false ) {

			/*
			 * load all plugin options
			 */
			$this->check = new ngfbCheck( $this );
			$this->is_avail = $this->check->available();
			$this->update_error = get_option( $this->cf['lca'].'_update_error' );
			$this->set_options();		// local method for early load

			if ( $this->is_avail['aop'] == true ) 
				$this->cf['full'] = $this->cf['full_pro'];
			if ( $this->is_avail['ngg'] == true ) {
				$this->ngg_options = get_option( 'ngg_options' );
				if ( defined( 'NEXTGEN_GALLERY_PLUGIN_VERSION' ) && NEXTGEN_GALLERY_PLUGIN_VERSION )
					$this->ngg_version = NEXTGEN_GALLERY_PLUGIN_VERSION;
			}
	
			/*
			 * create essential class objects
			 */
			$html_debug = ! empty( $this->options['plugin_debug'] ) || 
				( defined( 'NGFB_HTML_DEBUG' ) && NGFB_HTML_DEBUG ) ? true : false;
			$wp_debug = defined( 'NGFB_WP_DEBUG' ) && NGFB_WP_DEBUG ? true : false;
			$this->debug = new ngfbDebug( $this->cf['full'], 'NGFB', array( 'html' => $html_debug, 'wp' => $wp_debug ) );
			$this->check = new ngfbCheck( $this );
			$this->util = new ngfbUtil( $this );
			$this->notices = new ngfbNotices( $this );
			$this->opt = new ngfbOptions( $this );

			// uses ngfbOptions class, so must be after object creation
			if ( is_multisite() && ( ! is_array( $this->site_options ) || empty( $this->site_options ) ) )
				$this->site_options = $this->opt->get_site_defaults();

			/*
			 * plugin is being activated - create default options, if necessary, and exit
			 */
			if ( $activate == true || ( 
				! empty( $_GET['action'] ) && $_GET['action'] == 'activate-plugin' &&
				! empty( $_GET['plugin'] ) && $_GET['plugin'] == NGFB_PLUGINBASE ) ) {

				$this->debug->log( 'plugin activation detected' );

				if ( ! is_array( $this->options ) || empty( $this->options ) ||
					! empty( $this->options['plugin_reset'] ) || ( defined( 'NGFB_RESET' ) && NGFB_RESET ) ) {

					$this->options = $this->opt->get_defaults();
					$this->options['options_version'] = $this->opt->options_version;
					$this->options['plugin_version'] = $this->cf['version'];
					delete_option( NGFB_OPTIONS_NAME );
					add_option( NGFB_OPTIONS_NAME, $this->options, null, 'yes' );
					$this->debug->log( 'default options have been added to the database' );
				}
				$this->debug->log( 'exiting early: init_plugin() to follow' );
				// no need to continue, init_plugin() will handle the rest
				return;
			}

			/*
			 * continue creating remaining object classes
			 */
			$this->user = new ngfbUser( $this );
			$this->media = new ngfbMedia( $this );
			$this->webpage = new ngfbWebPage( $this );		// title, desc, etc., plus shortcodes
			$this->meta = new ngfbPostMeta( $this );
			$this->social = new ngfbSocial( $this );		// wp_head and wp_footer js and buttons
			$this->style = new ngfbStyle( $this );
			$this->script = new ngfbScript( $this );
			$this->cache = new ngfbCache( $this );

			if ( is_admin() ) {
				$this->msg = new ngfbMessages( $this );
				$this->admin = new ngfbAdmin( $this );
			} else {
				$this->head = new ngfbHead( $this );		// wp_head / opengraph
				$this->tags = new ngfbTags( $this );		// ngg image tags and wp post/page tags
			}

			// create pro class object last - it extends several previous classes
			if ( $this->is_avail['aop'] == true )
				$this->pro = new ngfbAddOnPro( $this );

			/*
			 * check options array read from database - upgrade options if necessary
			 */
			$this->options = $this->opt->check_options( $this->options );

			/*
			 * setup class properties, etc. based on option values
			 */
			$this->debug->log( 'calling add_image_size('.NGFB_OG_SIZE_NAME.', '.
				$this->options['og_img_width'].', '.
				$this->options['og_img_height'].', '.
				( empty( $this->options['og_img_crop'] ) ? 'false' : 'true' ).')' );
			add_image_size( NGFB_OG_SIZE_NAME, 
				$this->options['og_img_width'], 
				$this->options['og_img_height'], 
				( empty( $this->options['og_img_crop'] ) ? false : true ) );

			// set the file cache expiration values
			$this->cache->object_expire = $this->options['plugin_object_cache_exp'];
			$this->cache->file_expire = 0;
			if ( $this->check->pro_active() ) {
				if ( $this->debug->is_on( 'wp' ) == true ) 
					$this->cache->file_expire = NGFB_DEBUG_FILE_EXP;
				else $this->cache->file_expire = $this->options['plugin_file_cache_hrs'] * 60 * 60;
			}

			// set the object cache expiration value
			if ( $this->debug->is_on( 'html' ) == true ) {
				if ( ! defined( 'NGFB_TRANSIENT_CACHE_DISABLE' ) )
					define( 'NGFB_TRANSIENT_CACHE_DISABLE', true );
				$this->debug->log( 'HTML debug mode active: transient cache '.
					( NGFB_TRANSIENT_CACHE_DISABLE ? 'is' : 'could not be' ).' disabled' );
				$this->notices->inf( ( NGFB_TRANSIENT_CACHE_DISABLE ?
						__( 'HTML debug mode is active, transient cache is disabled.', NGFB_TEXTDOM ) :
						__( 'HTML debug mode is active, transient cache could not be disabled.', NGFB_TEXTDOM ) ).' '.
					__( 'Activity information is being added to webpages as hidden HTML comments.', NGFB_TEXTDOM ) );
			}

			// setup the update checks if we have an Authentication ID
			if ( ! empty( $this->options['plugin_pro_tid'] ) ) {
				add_filter( $this->cf['lca'].'_installed_version', array( &$this, 'filter_installed_version' ), 10, 1 );
				$this->update = new ngfbUpdate( $this );
				if ( is_admin() ) {
					// if update_hours * 2 has passed without an update check, then force one now
					$last_update = get_option( $this->cf['lca'].'_update_time' );
					if ( empty( $last_update ) || 
						( ! empty( $this->cf['update_hours'] ) && $last_update + ( $this->cf['update_hours'] * 7200 ) < time() ) )
							$this->update->check_for_updates();
				}
			}

		}

		public function filter_installed_version( $version ) {
			if ( $this->is_avail['aop'] == true )
				return $version;
			else return '0.'.$version;	// force upgrade to any current pro version
		}

		public function set_options() {
			$this->options = get_option( NGFB_OPTIONS_NAME );
			if ( is_multisite() ) {
				$this->site_options = get_site_option( NGFB_SITE_OPTIONS_NAME );

				// if multisite options are found, check for overwrite of site specific options
				if ( is_array( $this->options ) && is_array( $this->site_options ) ) {
					foreach ( $this->site_options as $key => $val ) {
						if ( array_key_exists( $key, $this->options ) && 
							array_key_exists( $key.'_use', $this->site_options ) ) {

							if ( $this->site_options[$key.'_use'] == 'force' ||
								( $this->site_options[$key.'_use'] == 'empty' && empty( $this->options[$key] ) ) )
									$this->options[$key] = $this->site_options[$key];
						}
					}
				}
			}
		}

	}

        global $ngfb;
	$ngfb = new ngfbPlugin();
}

?>

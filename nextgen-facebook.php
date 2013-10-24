<?php
/*
Plugin Name: NGFB Open Graph+
Plugin URI: http://surniaulula.com/extend/plugins/nextgen-facebook/
Author: Jean-Sebastien Morisset
Author URI: http://surniaulula.com/
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Description: Improve the appearance and ranking of your Posts, Pages and eCommerce Products in Google Search and social websites.
Version: 6.13dev6

Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'ngfbPlugin' ) ) {

	class ngfbPlugin {

		public $version = '6.13dev6';
		public $acronym = 'ngfb';
		public $acronym_uc = 'NGFB';
		public $menuname = 'Open Graph+';
		public $fullname = 'NGFB Open Graph+';
		public $fullname_pro = 'NGFB Open Graph+ Pro';
		public $slug = 'nextgen-facebook';
		public $update_hours = 12;
		public $min_wp_version = '3.0';

		public $debug;		// ngfbDebug
		public $util;		// ngfbUtil
		public $notices;	// ngfbNotices
		public $opt;		// ngfbOptions
		public $user;		// ngfbUser
		public $media;		// ngfbMedia
		public $meta;		// ngfbPostMeta
		public $style;		// ngfbStyle
		public $script;		// ngfbStyle
		public $cache;		// ngfbCache
		public $admin;		// ngfbAdmin
		public $head;		// ngfbHead
		public $tags;		// ngfbTags
		public $webpage;	// ngfbWebPage
		public $social;		// ngfbSocial
		public $seo;		// ngfbSeo*
		public $pro;		// ngfbAddOnPro
		public $update;		// ngfbUpdate

		public $is_avail = array();	// assoc array for function/class/method/etc. checks
		public $options = array();
		public $site_options = array();
		public $ngg_options = array();
		public $ngg_version = 0;

		public $cf = array(
			'lib' => array(
				'setting' => array (
					'general' => 'General',
					'advanced' => 'Advanced',
					'contact' => 'Contact Methods',
					'social' => 'Social Sharing',
					'style' => 'Social Style',
					'about' => 'About',
				),
				'network_setting' => array(
					'network' => 'Network',
				),
				'website' => array(
					'facebook' => 'Facebook', 
					'gplus' => 'GooglePlus',
					'twitter' => 'Twitter',
					'linkedin' => 'LinkedIn',
					'pinterest' => 'Pinterest',
					'stumbleupon' => 'StumbleUpon',
					'tumblr' => 'Tumblr',
					'youtube' => 'YouTube',
					'skype' => 'Skype',
				),
				'shortcode' => array(
					'ngfb' => 'Ngfb',
				),
				'widget' => array(
					'social' => 'SocialSharing',
				),
				'ecom' => array(
					'woocommerce' => 'WooCommerce',
					'marketpress' => 'MarketPress',
					'wpecommerce' => 'WPeCommerce',
				),
				'seo' => array(
					'aioseop' => 'AllinOneSEOPack',
					'seou' => 'SEOUltimate',
					'wpseo' => 'WordPressSEO',
				),
			),
			'opt' => array(
				'pre' => array(
					'facebook' => 'fb', 
					'gplus' => 'gp',
					'twitter' => 'twitter',
					'linkedin' => 'linkedin',
					'pinterest' => 'pin',
					'stumbleupon' => 'stumble',
					'tumblr' => 'tumblr',
					'youtube' => 'yt',
					'skype' => 'skype',
				),
			),
			'wp' => array(
				'contact' => array(
					'aim' => 'AIM',
					'jabber' => 'Jabber / Google Talk',
					'yim' => 'Yahoo IM',
				),
			),
			'css' => array(
				'social' => 'Buttons Style',
				'excerpt' => 'Excerpt Style',
				'content' => 'Content Style',
				'shortcode' => 'Shortcode Style',
				'widget' => 'Widget Style',
			),
			'url' => array(
				'feed' => 'http://feed.surniaulula.com/category/application/wordpress/wp-plugins/ngfb/feed/',
				'readme' => 'http://plugins.svn.wordpress.org/nextgen-facebook/trunk/readme.txt',
				'purchase' => 'http://plugin.surniaulula.com/extend/plugins/nextgen-facebook/',
				'faq' => 'http://wordpress.org/plugins/nextgen-facebook/faq/',
				'notes' => 'http://wordpress.org/plugins/nextgen-facebook/other_notes/',
				'changelog' => 'http://wordpress.org/plugins/nextgen-facebook/changelog/',
				'support' => 'http://wordpress.org/support/plugin/nextgen-facebook',
				'pro_faq' => 'http://faq.nextgen-facebook.surniaulula.com/',
				'pro_notes' => 'http://notes.nextgen-facebook.surniaulula.com/',
				'pro_support' => 'http://support.nextgen-facebook.surniaulula.com/',
				'pro_request' => 'http://request.nextgen-facebook.surniaulula.com/',
				'pro_update' => 'http://update.surniaulula.com/extend/plugins/nextgen-facebook/update/',
			),
			'img' => array(
				'follow' => array(
					'size' => 32,
					'src' => array(
						'facebook.png' => 'https://www.facebook.com/pages/Surnia-Ulula/200643823401977',
						'gplus.png' => 'https://plus.google.com/u/2/103457833348046432604/posts',
						'linkedin.png' => 'https://www.linkedin.com/in/jsmoriss',
						'twitter.png' => 'https://twitter.com/surniaululacom',
						'youtube.png' => 'https://www.youtube.com/user/SurniaUlulaCom',
						'feed.png' => 'http://feed.surniaulula.com/category/application/wordpress/wp-plugins/ngfb/feed/',
					),
				),
			),
		);

		public function __construct() {
			$this->define_constants();	// define constants first for option defaults
			$this->load_libs();		// keep in __construct() to extend widgets etc.

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
			$slug = 'nextgen-facebook';
			$acronym = 'ngfb';
			self::do_multisite( $sitewide, array( __CLASS__, 'uninstall_plugin' ) );
			delete_site_option( $acronym.'_site_options' );
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
			$classname = $this->acronym.'Check';
			$this->check = new $classname( $this );
			$this->check->wp_version();
			$this->setup_vars( true );
		}

		private function deactivate_plugin() {
			wp_clear_scheduled_hook( 'plugin_updates-'.$this->slug );
		}

		public static function uninstall_plugin() {
			$slug = 'nextgen-facebook';
			$acronym = 'ngfb';
			$options = get_option( $acronym.'_options' );
			if ( empty( $options['plugin_preserve'] ) ) {

				delete_option( $acronym.'_options' );
				delete_option( $acronym.'_update_error' );
				delete_option( 'external_updates-'.$slug );

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
		}

		public function filter_installed_version( $version ) {
			if ( $this->is_avail['aop'] == true )
				return $version;
			else return '0.'.$version;
		}

		// called by WP init action
		public function init_plugin() {
			load_plugin_textdomain( NGFB_TEXTDOM, false, dirname( NGFB_PLUGINBASE ).'/languages/' );
			$this->setup_vars();
			$this->check->conflicts();
			if ( $this->debug->is_on() == true ) {
				foreach ( array( 'wp_head', 'wp_footer' ) as $action ) {
					foreach ( array( 1, 9999 ) as $prio )
						add_action( $action, create_function( '', 
							"echo '<!-- ".$this->fullname." add_action( \'$action\' ) Priority $prio Test = PASSED -->\n';" ), $prio );
				}
			}
		}

		private function define_constants() { 

			define( 'NGFB_FILEPATH', __FILE__ );
			define( 'NGFB_PLUGINDIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );	// since wp 1.2.0 
			define( 'NGFB_PLUGINBASE', plugin_basename( __FILE__ ) );			// since wp 1.5
			define( 'NGFB_TEXTDOM', $this->slug );
			define( 'NGFB_URLPATH', trailingslashit( plugins_url( '', __FILE__ ) ) );
			define( 'NGFB_NONCE', md5( NGFB_PLUGINDIR ) );
			define( 'AUTOMATTIC_README_MARKDOWN', NGFB_PLUGINDIR.'lib/ext/markdown.php' );

			// allow some constants to be pre-defined in wp-config.php

			// NGFB_RESET
			// NGFB_WP_DEBUG
			// NGFB_HTML_DEBUG
			// NGFB_OPEN_GRAPH_DISABLE
			// NGFB_MIN_IMG_SIZE_DISABLE
			// NGFB_OBJECT_CACHE_DISABLE
			// NGFB_TRANSIENT_CACHE_DISABLE
			// NGFB_FILE_CACHE_DISABLE
			// NGFB_CURL_DISABLE
			// NGFB_CURL_PROXY
			// NGFB_CURL_PROXYUSERPWD
			// NGFB_WISTIA_API_PWD

			if ( defined( 'NGFB_DEBUG' ) && ! defined( 'NGFB_HTML_DEBUG' ) )
				define( 'NGFB_HTML_DEBUG', NGFB_DEBUG );	// backwards compat

			if ( ! defined( 'NGFB_CACHEDIR' ) )
				define( 'NGFB_CACHEDIR', NGFB_PLUGINDIR.'cache/' );

			if ( ! defined( 'NGFB_CACHEURL' ) )
				define( 'NGFB_CACHEURL', NGFB_URLPATH.'cache/' );

			if ( ! defined( 'NGFB_OPTIONS_NAME' ) )
				define( 'NGFB_OPTIONS_NAME', $this->acronym.'_options' );

			if ( ! defined( 'NGFB_SITE_OPTIONS_NAME' ) )
				define( 'NGFB_SITE_OPTIONS_NAME', $this->acronym.'_site_options' );

			if ( ! defined( 'NGFB_META_NAME' ) )
				define( 'NGFB_META_NAME', '_'.$this->acronym.'_meta' );

			if ( ! defined( 'NGFB_MENU_PRIORITY' ) )
				define( 'NGFB_MENU_PRIORITY', '99.10' );

			if ( ! defined( 'NGFB_INIT_PRIORITY' ) )
				define( 'NGFB_INIT_PRIORITY', 12 );

			if ( ! defined( 'NGFB_HEAD_PRIORITY' ) )
				define( 'NGFB_HEAD_PRIORITY', 10 );

			if ( ! defined( 'NGFB_SOCIAL_PRIORITY' ) )
				define( 'NGFB_SOCIAL_PRIORITY', 100 );
			
			if ( ! defined( 'NGFB_FOOTER_PRIORITY' ) )
				define( 'NGFB_FOOTER_PRIORITY', 100 );
			
			if ( ! defined( 'NGFB_OG_SIZE_NAME' ) )
				define( 'NGFB_OG_SIZE_NAME', $this->acronym.'-open-graph' );

			if ( ! defined( 'NGFB_MIN_DESC_LEN' ) )
				define( 'NGFB_MIN_DESC_LEN', 156 );

			if ( ! defined( 'NGFB_MIN_IMG_SIZE' ) )
				define( 'NGFB_MIN_IMG_SIZE', 200 );

			if ( ! defined( 'NGFB_MAX_IMG_OG' ) )
				define( 'NGFB_MAX_IMG_OG', 20 );

			if ( ! defined( 'NGFB_MAX_VID_OG' ) )
				define( 'NGFB_MAX_VID_OG', 20 );

			if ( ! defined( 'NGFB_MAX_CACHE_HRS' ) )
				define( 'NGFB_MAX_CACHE_HRS', 24 );

			if ( ! defined( 'NGFB_DEBUG_FILE_EXP' ) )
				define( 'NGFB_DEBUG_FILE_EXP', 30 );

			if ( ! defined( 'NGFB_CURL_USERAGENT' ) )
				define( 'NGFB_CURL_USERAGENT', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:18.0) Gecko/20100101 Firefox/18.0' );

			if ( ! defined( 'NGFB_CURL_CAINFO' ) )
				define( 'NGFB_CURL_CAINFO', NGFB_PLUGINDIR.'share/curl/cacert.pem' );

		}

		private function load_libs() {

			require_once ( NGFB_PLUGINDIR.'lib/debug.php' );
			require_once ( NGFB_PLUGINDIR.'lib/check.php' );
			require_once ( NGFB_PLUGINDIR.'lib/util.php' );
			require_once ( NGFB_PLUGINDIR.'lib/notices.php' );
			require_once ( NGFB_PLUGINDIR.'lib/options.php' );
			require_once ( NGFB_PLUGINDIR.'lib/user.php' );
			require_once ( NGFB_PLUGINDIR.'lib/media.php' );
			require_once ( NGFB_PLUGINDIR.'lib/webpage.php' );
			require_once ( NGFB_PLUGINDIR.'lib/postmeta.php' );
			require_once ( NGFB_PLUGINDIR.'lib/social.php' );
			require_once ( NGFB_PLUGINDIR.'lib/style.php' );
			require_once ( NGFB_PLUGINDIR.'lib/script.php' );
			require_once ( NGFB_PLUGINDIR.'lib/cache.php' );
			require_once ( NGFB_PLUGINDIR.'lib/update.php' );

			if ( is_admin() ) {
				require_once ( NGFB_PLUGINDIR.'lib/messages.php' );
				require_once ( NGFB_PLUGINDIR.'lib/admin.php' );

				// settings classes extend lib/admin.php and objects are created by lib/admin.php
				foreach ( $this->cf['lib']['setting'] as $id => $name )
					require_once ( NGFB_PLUGINDIR.'lib/settings/'.$id.'.php' );
				unset ( $id, $name );

				if ( is_multisite() ) {
					foreach ( $this->cf['lib']['network_setting'] as $id => $name )
						require_once ( NGFB_PLUGINDIR.'lib/settings/'.$id.'.php' );
					unset ( $id, $name );
				}

				require_once ( NGFB_PLUGINDIR.'lib/form.php' );
				require_once ( NGFB_PLUGINDIR.'lib/ext/parse-readme.php' );

			} else {

				require_once ( NGFB_PLUGINDIR.'lib/head.php' );
				require_once ( NGFB_PLUGINDIR.'lib/opengraph.php' );
				require_once ( NGFB_PLUGINDIR.'lib/tags.php' );
				require_once ( NGFB_PLUGINDIR.'lib/functions.php' );

				// the ngfb_shortcode class object is created by lib/webpage.php
				foreach ( $this->cf['lib']['shortcode'] as $id => $name )
					require_once ( NGFB_PLUGINDIR.'lib/shortcodes/'.$id.'.php' );
				unset ( $id, $name );
			}

			// website classes extend both lib/social.php and lib/settings/social.php
			foreach ( $this->cf['lib']['website'] as $id => $name )
				if ( file_exists( NGFB_PLUGINDIR.'lib/websites/'.$id.'.php' ) )
					require_once ( NGFB_PLUGINDIR.'lib/websites/'.$id.'.php' );
			unset ( $id, $name );

			// widgets are added to wp when library file is loaded
			foreach ( $this->cf['lib']['widget'] as $id => $name )
				if ( file_exists( NGFB_PLUGINDIR.'lib/widgets/'.$id.'.php' ) )
					require_once ( NGFB_PLUGINDIR.'lib/widgets/'.$id.'.php' );
			unset ( $id, $name );

			// pro version classes
			// additional classes are loaded and created by pro construct
			if ( file_exists( NGFB_PLUGINDIR.'lib/pro/addon.php' ) )
				require_once ( NGFB_PLUGINDIR.'lib/pro/addon.php' );

		}

		// get the options, upgrade the options (if necessary), and validate their values
		private function setup_vars( $activate = false ) {

			/*
			 * Allow override of default plugin variables
			 */
			if ( defined( 'NGFB_UPDATE_URL' ) && NGFB_UPDATE_URL )
				$this->cf['url']['pro_update'] = NGFB_UPDATE_URL;

			/*
			 * load all plugin options
			 */
			$this->check = new ngfbCheck( $this );
			$this->is_avail = $this->check->available();
			$this->update_error = get_option( $this->acronym.'_update_error' );
			$this->set_options();		// local method for early load

			if ( $this->is_avail['aop'] == true ) 
				$this->fullname = $this->fullname_pro;
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
			$this->debug = new ngfbDebug( $this->fullname, 'NGFB', array( 'html' => $html_debug, 'wp' => $wp_debug ) );
			$this->check = new ngfbCheck( $this );
			$this->util = new ngfbUtil( $this );
			$this->notices = new ngfbNotices( $this );
			$this->opt = new ngfbOptions( $this );

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
					$this->options['plugin_version'] = $this->version;
					delete_option( NGFB_OPTIONS_NAME );
					add_option( NGFB_OPTIONS_NAME, $this->options, null, 'yes' );
					$this->debug->log( 'default options have been added to the database' );
				}
				$this->debug->log( 'exiting early: init_plugin() to follow' );
				// no need to continue, init_plugin() will handle the rest
				return;
			}
			if ( is_multisite() && ( ! is_array( $this->site_options ) || empty( $this->site_options ) ) )
				$this->site_options = $this->opt->get_site_defaults();

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
				else
					$this->cache->file_expire = $this->options['plugin_file_cache_hrs'] * 60 * 60;
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
				add_filter( $this->acronym.'_installed_version', array( &$this, 'filter_installed_version' ), 10, 1 );
				$this->update = new ngfbUpdate( $this );
			}

		}

		public function set_options() {
			$this->options = get_option( NGFB_OPTIONS_NAME );
			if ( is_multisite() ) {
				$this->site_options = get_site_option( NGFB_SITE_OPTIONS_NAME );

				// if multisite options are found, allow those to overwrite the site specific options
				if ( is_array( $this->options ) && is_array( $this->site_options ) ) {
					foreach ( $this->site_options as $key => $val ) {
						switch ( $key ) {
							case 'plugin_pro_tid' :
								if ( $this->site_options['plugin_pro_tid_use'] == 'force' || 
									empty( $this->options['plugin_pro_tid'] ) )
										$this->options['plugin_pro_tid'] = $this->site_options['plugin_pro_tid'];
								break;
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

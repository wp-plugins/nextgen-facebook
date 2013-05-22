<?php
/*
Plugin Name: NextGEN Facebook Open Graph
Plugin URI: http://surniaulula.com/wordpress-plugins/nextgen-facebook-open-graph/
Description: Adds complete Open Graph meta tags for Facebook, Google+, Twitter, LinkedIn, etc., plus optional social sharing buttons in content or widget.
Version: 5.0rc2
Author: Jean-Sebastien Morisset
Author URI: http://surniaulula.com/

Copyright 2012 - Jean-Sebastien Morisset - http://surniaulula.com/

This script is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This script is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details at
http://www.gnu.org/licenses/.
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbPlugin' ) ) {

	class ngfbPlugin {

		public $version = '5.0rc2';	// only for display purposes
		public $opts_version = '23';	// increment when adding/removing default options
		public $is_avail = array();	// assoc array for function/class/method/etc. checks
		public $options = array();
		public $ngg_options = array();

		public $debug;		// ngfbDebug
		public $util;		// ngfbUtil
		public $notices;	// ngfbNotices
		public $opt;		// ngfbOptions
		public $head;		// ngfbHead
		public $social;		// ngfbSocial
		public $user;		// ngfbUser
		public $tags;		// ngfbTags
		public $media;		// ngfbMedia
		public $webpage;	// ngfbWebPage
		public $admin;		// ngfbAdmin
		public $pro;		// ngfbPro
		public $cache;		// ngfbCache

		public $social_prefix = array(
			'facebook' => 'fb', 
			'gplus' => 'gp',
			'twitter' => 'twitter',
			'linkedin' => 'linkedin',
			'pinterest' => 'pin',
			'stumbleupon' => 'stumble',
			'tumblr' => 'tumblr' );

		public $social_names = array(
			'facebook' => 'Facebook', 
			'gplus' => 'GooglePlus',
			'twitter' => 'Twitter',
			'linkedin' => 'LinkedIn',
			'pinterest' => 'Pinterest',
			'stumbleupon' => 'StumbleUpon',
			'tumblr' => 'Tumblr' );

		public $shortcode_names = array(
			'ngfb' => 'Ngfb' );

		public function __construct() {

			$this->define_constants();	// define constants first for option defaults
			$this->load_libs();

			register_activation_hook( __FILE__, array( &$this, 'activate' ) );
			register_uninstall_hook( __FILE__, array( 'ngfbPlugin', 'uninstall' ) );

			add_action( 'init', array( &$this, 'init_plugin' ) );
		}

		// create new default options on plugin activation if ngfb_reset = 1, NGFB_RESET is true,
		// NGFB_OPTIONS_NAME is not an array, or NGFB_OPTIONS_NAME is an empty array
		public function activate() {
			if ( ! empty( $this->options['ngfb_reset'] ) 
				|| ( defined( 'NGFB_RESET' ) && NGFB_RESET ) 
				|| ! is_array( $this->options ) 
				|| empty( $this->options ) ) {

				$opts = $this->opt->get_defaults();
				$opts['ngfb_version'] = $this->opts_version;

				delete_option( NGFB_OPTIONS_NAME );	// remove old options, if any
				add_option( NGFB_OPTIONS_NAME, $opts, null, 'yes' );
			}
		}

		// delete options table entries only when plugin deactivated and deleted
		public function uninstall() {
			delete_option( NGFB_OPTIONS_NAME );
		}

		// called by WP init action
		public function init_plugin() {

			// run check_deps() before setup_vars() to get ngg options (if the plugin is installed)
			$this->check_deps();
			$this->setup_vars();

			// add_action() tests and debug output
			if ( $this->debug->on ) {
				foreach ( array( 'wp_head', 'wp_footer' ) as $action ) {
					foreach ( array( 1, 9999 ) as $prio )
						add_action( $action, create_function( '', 
							"echo '<!-- " . NGFB_ACRONYM . " add_action( \'$action\' ) Priority $prio Test = PASSED -->\n';" ), $prio );
				}
			}

		}

		private function define_constants() { 

			define( 'NGFB_SHORTNAME', 'ngfb' );
			define( 'NGFB_ACRONYM', 'NGFB' );
			define( 'NGFB_FULLNAME', 'NextGEN Facebook Open Graph' );
			define( 'NGFB_LONGNAME', 'NextGEN Facebook Open Graph (NGFB)' );
			define( 'NGFB_URL', 'http://surniaulula.com/wordpress-plugins/nextgen-facebook-open-graph/' );
			define( 'NGFB_PLUGINDIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
			define( 'NGFB_URLPATH', trailingslashit( plugins_url( '', __FILE__ ) ) );
			define( 'NGFB_CACHEDIR', NGFB_PLUGINDIR . 'cache/' );
			define( 'NGFB_CACHEURL', NGFB_URLPATH . 'cache/' );

			// allow some constants to be pre-defined in wp-config.php

			// NGFB_DEBUG
			// NGFB_RESET
			// NGFB_OPEN_GRAPH_DISABLE
			// NGFB_MIN_IMG_SIZE_DISABLE

			if ( ! defined( 'NGFB_OPTIONS_NAME' ) )
				define( 'NGFB_OPTIONS_NAME', 'ngfb_options' );

			if ( ! defined( 'NGFB_HEAD_PRIORITY' ) )
				define( 'NGFB_HEAD_PRIORITY', 5 );

			if ( ! defined( 'NGFB_SOCIAL_PRIORITY' ) )
				define( 'NGFB_SOCIAL_PRIORITY', 100 );
			
			if ( ! defined( 'NGFB_FOOTER_PRIORITY' ) )
				define( 'NGFB_FOOTER_PRIORITY', 10 );
			
			if ( ! defined( 'NGFB_MIN_DESC_LEN' ) )
				define( 'NGFB_MIN_DESC_LEN', 160 );

			if ( ! defined( 'NGFB_MIN_IMG_WIDTH' ) )
				define( 'NGFB_MIN_IMG_WIDTH', 200 );

			if ( ! defined( 'NGFB_MIN_IMG_HEIGHT' ) )
				define( 'NGFB_MIN_IMG_HEIGHT', 200 );

			if ( ! defined( 'NGFB_MAX_IMG_OG' ) )
				define( 'NGFB_MAX_IMG_OG', 20 );

			if ( ! defined( 'NGFB_MAX_VID_OG' ) )
				define( 'NGFB_MAX_VID_OG', 20 );

			if ( ! defined( 'NGFB_MAX_CACHE' ) )
				define( 'NGFB_MAX_CACHE', 24 );

			if ( ! defined( 'NGFB_CONTACT_FIELDS' ) )
				define( 'NGFB_CONTACT_FIELDS', 'facebook:Facebook URL,gplus:Google+ URL' );

			// NGFB_USER_AGENT is used by the ngfbCache class
			// Google Plus javascript is different for (what it considers) invalid user agents
			// visiting crawlers might cause a refresh of the Google Plus javascript, so make
			// sure all requests we make have a valid user agent string (which one doesn't matter)
			if ( ! defined( 'NGFB_USER_AGENT' ) )
				define( 'NGFB_USER_AGENT', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:18.0) Gecko/20100101 Firefox/18.0' );

			if ( ! defined( 'NGFB_PEM_FILE' ) )
				define( 'NGFB_PEM_FILE', NGFB_PLUGINDIR . 'share/curl/cacert.pem' );
		}

		private function load_libs() {

			require_once ( dirname ( __FILE__ ) . '/lib/debug.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/notices.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/options.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/util.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/head.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/opengraph.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/social.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/user.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/tags.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/media.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/webpage.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/cache.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/googl.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/widgets.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/functions.php' );

			if ( is_admin() ) {
				require_once ( dirname ( __FILE__ ) . '/lib/admin.php' );
				require_once ( dirname ( __FILE__ ) . '/lib/form.php' );
			}

			foreach ( $this->social_names as $id => $name )
				require_once ( dirname ( __FILE__ ) . '/lib/websites/' . $id . '.php' );
			unset ( $id, $name );

			foreach ( $this->shortcode_names as $id => $name )
				require_once ( dirname ( __FILE__ ) . '/lib/shortcodes/' . $id . '.php' );
			unset ( $id, $name );

			if ( file_exists( dirname ( __FILE__ ) . '/lib/pro.php' ) )
				require_once ( dirname ( __FILE__ ) . '/lib/pro.php' );
		}

		private function check_deps() {
		
			// ngfb pro
			$this->is_avail['ngfbpro'] = class_exists( 'ngfbPro' ) ? true : false;

			// php v4.0.6+
			$this->is_avail['mbdecnum'] = function_exists( 'mb_decode_numericentity' ) ? true : false;

			// post thumbnail feature is supported by wp theme
			$this->is_avail['postthumb'] = function_exists( 'has_post_thumbnail' ) ? true : false;

			// nextgen gallery plugin
			$this->is_avail['ngg'] = class_exists( 'nggdb' ) && method_exists( 'nggdb', 'find_image' ) ? true : false;

			// cdn linker plugin
			$this->is_avail['cdnlink'] = class_exists( 'CDNLinksRewriterWordpress' ) ? true : false;

			// wikibox plugin
			$this->is_avail['wikibox'] = function_exists( 'wikibox_summary' ) ? true : false;

			// exclude pages plugin
			$this->is_avail['expages'] = function_exists( 'ep_get_excluded_ids' ) ? true : false;
		}

		// get the options, upgrade the option names (if necessary), and validate their values
		private function setup_vars() {

			// load options first for use in __construct() methods
			$this->options = get_option( NGFB_OPTIONS_NAME );

			if ( $this->is_avail['ngg'] == true )
				$this->ngg_options = get_option( 'ngg_options' );

			$this->debug = new ngfbDebug( &$this );
			$this->util = new ngfbUtil( &$this );
			$this->notices = new ngfbNotices( &$this );
			$this->opt = new ngfbOptions( &$this );
			$this->head = new ngfbHead( $this );
			$this->social = new ngfbSocial( $this );
			$this->user = new ngfbUser( &$this );
			$this->tags = new ngfbTags( &$this );
			$this->media = new ngfbMedia( &$this );
			$this->webpage = new ngfbWebPage( &$this );

			if ( is_admin() ) {
				$this->admin = new ngfbAdmin( $this );
				$this->admin->plugin_name = plugin_basename( __FILE__ );
			}

			if ( $this->is_avail['ngfbpro'] == true )
				$this->pro = new ngfbPro( $this );

			if ( $this->is_avail['mbdecnum'] != true )
				$this->notices->err( 'The <code><a href="http://php.net/manual/en/function.mb-decode-numericentity.php" 
					target="_blank">mb_decode_numericentity()</a></code> function (available since PHP v4.0.6) is missing. 
					This function is required to decode UTF8 entities. Please update your PHP installation as soon as possible.' );

			// make sure we have something to work with
			if ( ! empty( $this->options ) && is_array( $this->options ) ) {
				if ( empty( $this->options['ngfb_version'] ) 
					|| $this->options['ngfb_version'] !== $this->opts_version )
						$this->options = $this->opt->upgrade( $this->options, $this->opt->get_defaults() );
			} else {
				$this->notices->err( 'WordPress returned an error when reading the "' . NGFB_OPTIONS_NAME . '" array from the options database table. 
					All plugin settings have been returned to their default values (though nothing has been saved back to the database). 
					<a href="' . $this->util->get_options_url() . '">Please visit the settings page to review and change the default values</a>.' );
				$this->options = $this->opt->get_defaults();
			}

			// set caching properties
			$this->cache = new ngfbCache( $this );
			$this->cache->base_dir = trailingslashit( NGFB_CACHEDIR );
			$this->cache->base_url = trailingslashit( NGFB_CACHEURL );
			$this->cache->pem_file = NGFB_PEM_FILE;
			$this->cache->verify_cert = $this->options['ngfb_verify_certs'];
			$this->cache->user_agent = NGFB_USER_AGENT;
			$this->cache->file_expire = $this->options['ngfb_file_cache_hrs'] * 60 * 60;

			if ( ! empty( $this->options['ngfb_debug'] ) || ( defined( 'NGFB_DEBUG' ) && NGFB_DEBUG ) ) {

				$this->debug->on = $this->options['ngfb_debug'];
				$this->cache->object_expire = 1;
				$this->debug->log( 'debug mode active - setting ngfb_object_cache_exp = ' . $this->cache->object_expire . ' seconds' );
				$this->notices->inf( 'Debug mode is turned ON. Debugging information is being generated and added to webpages as hidden HTML comments. 
					WP object cache expiration time has been temporarily set to ' . $this->cache->object_expire . ' second 
					(instead of ' . $this->options['ngfb_object_cache_exp'] . ' seconds).' );

			} else $this->cache->object_expire = $this->options['ngfb_object_cache_exp'];
		}

	}

        global $ngfb;
	$ngfb = new ngfbPlugin();
}

?>

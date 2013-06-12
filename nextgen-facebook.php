<?php
/*
Plugin Name: NGFB Open Graph
Plugin URI: http://surniaulula.com/extend/plugins/nextgen-facebook/
Author: Jean-Sebastien Morisset
Author URI: http://surniaulula.com/
Description: Improve display and ranking on Google Search, and enhance social sharing with Facebook, Google+, Twitter, LinkedIn, and many more.
Version: 5.2.3

Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/

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

		public $version = '5.2.3';	// only for display purposes
		public $acronym = 'ngfb';
		public $acronym_uc = 'NGFB';
		public $menuname = 'Open Graph';
		public $fullname = 'NGFB Open Graph';
		public $slug = 'nextgen-facebook';
		public $update_hours = 12;

		public $debug;		// ngfbDebug
		public $util;		// ngfbUtil
		public $notices;	// ngfbNotices
		public $opt;		// ngfbOptions
		public $user;		// ngfbUser
		public $media;		// ngfbMedia
		public $meta;		// ngfbPostMeta
		public $style;		// ngfbStyle
		public $cache;		// ngfbCache
		public $admin;		// ngfbAdmin
		public $head;		// ngfbHead
		public $tags;		// ngfbTags
		public $webpage;	// ngfbWebPage
		public $social;		// ngfbSocial

		public $is_avail = array();	// assoc array for function/class/method/etc. checks
		public $options = array();
		public $ngg_options = array();
		public $msgs = array();

		public $urls = array(
			'news_feed' => 'http://surniaulula.com/category/application/wordpress/wp-plugins/ngfb/feed/',
			'plugin' => 'http://surniaulula.com/extend/plugins/nextgen-facebook/',
			'update' => 'http://surniaulula.com/extend/plugins/nextgen-facebook/update/',
			'readme' => 'http://plugins.svn.wordpress.org/nextgen-facebook/trunk/readme.txt',
			'support' => 'http://wordpress.org/support/plugin/nextgen-facebook',
			'support_feed' => 'http://wordpress.org/support/rss/plugin/nextgen-facebook',
		);

		public $social_prefix = array(
			'facebook' => 'fb', 
			'gplus' => 'gp',
			'twitter' => 'twitter',
			'linkedin' => 'linkedin',
			'pinterest' => 'pin',
			'stumbleupon' => 'stumble',
			'tumblr' => 'tumblr' );

		public $website_libs = array(
			'facebook' => 'Facebook', 
			'gplus' => 'GooglePlus',
			'twitter' => 'Twitter',
			'linkedin' => 'LinkedIn',
			'pinterest' => 'Pinterest',
			'stumbleupon' => 'StumbleUpon',
			'tumblr' => 'Tumblr' );

		public $shortcode_libs = array(
			'ngfb' => 'Ngfb' );

		public $widget_libs = array(
			'social' => 'SocialSharing' );

		public $setting_libs = array(
			'about' => 'About',
			'webpage' => 'Webpage',
			'social' => 'Social Sharing',
			'advanced' => 'Advanced' );

		public function __construct() {

			$this->define_constants();	// define constants first for option defaults
			$this->load_libs();		// keep in __construct() to extend widgets etc.

			register_activation_hook( __FILE__, array( &$this, 'activate' ) );
			register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );
			register_uninstall_hook( __FILE__, array( 'ngfbPlugin', 'uninstall' ) );

			add_action( 'init', array( &$this, 'init_plugin' ) );
		}

		public function activate() {
			$this->check_deps();
			$this->setup_vars( true );
		}

		public function deactivate() {
			wp_clear_scheduled_hook( 'plugin_updates-' . $this->slug );
		}

		// delete options table entries only when plugin deactivated and deleted
		public function uninstall() {
			$options = get_option( NGFB_OPTIONS_NAME );
			if ( empty( $options['ngfb_preserve'] ) ) {
				delete_option( NGFB_OPTIONS_NAME );
				delete_option( 'external_updates-nextgen-facebook' );
			}
		}

		// called by WP init action
		public function init_plugin() {
			$this->check_deps();
			$this->setup_vars();
			if ( $this->debug->is_on() == true ) {
				foreach ( array( 'wp_head', 'wp_footer' ) as $action ) {
					foreach ( array( 1, 9999 ) as $prio )
						add_action( $action, create_function( '', 
							"echo '<!-- " . $this->fullname . " add_action( \'$action\' ) Priority $prio Test = PASSED -->\n';" ), $prio );
				}
			}

		}

		private function define_constants() { 

			define( 'NGFB_FILEPATH', __FILE__ );
			define( 'NGFB_PLUGINDIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
			define( 'NGFB_URLPATH', trailingslashit( plugins_url( '', __FILE__ ) ) );
			define( 'NGFB_CACHEDIR', NGFB_PLUGINDIR . 'cache/' );
			define( 'NGFB_CACHEURL', NGFB_URLPATH . 'cache/' );
			define( 'NGFB_NONCE', md5( NGFB_PLUGINDIR ) );
			define( 'AUTOMATTIC_README_MARKDOWN', NGFB_PLUGINDIR . 'lib/ext/markdown.php' );

			// allow some constants to be pre-defined in wp-config.php

			// NGFB_DEBUG
			// NGFB_WP_DEBUG
			// NGFB_RESET
			// NGFB_MIN_IMG_SIZE_DISABLE
			// NGFB_CURL_DISABLE
			// NGFB_CURL_PROXY
			// NGFB_CURL_PROXYUSERPWD
			// NGFB_OPEN_GRAPH_DISABLE

			if ( ! defined( 'NGFB_OPTIONS_NAME' ) )
				define( 'NGFB_OPTIONS_NAME', 'ngfb_options' );

			if ( ! defined( 'NGFB_META_NAME' ) )
				define( 'NGFB_META_NAME', 'ngfb_meta' );

			if ( ! defined( 'NGFB_MENU_PRIORITY' ) )
				define( 'NGFB_MENU_PRIORITY', '99.10' );

			if ( ! defined( 'NGFB_HEAD_PRIORITY' ) )
				define( 'NGFB_HEAD_PRIORITY', 10 );

			if ( ! defined( 'NGFB_SOCIAL_PRIORITY' ) )
				define( 'NGFB_SOCIAL_PRIORITY', 100 );
			
			if ( ! defined( 'NGFB_FOOTER_PRIORITY' ) )
				define( 'NGFB_FOOTER_PRIORITY', 100 );
			
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

			if ( ! defined( 'NGFB_CURL_USERAGENT' ) )
				define( 'NGFB_CURL_USERAGENT', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:18.0) Gecko/20100101 Firefox/18.0' );

			if ( ! defined( 'NGFB_CURL_CAINFO' ) )
				define( 'NGFB_CURL_CAINFO', NGFB_PLUGINDIR . 'share/curl/cacert.pem' );

		}

		private function load_libs() {

			require_once ( NGFB_PLUGINDIR . 'lib/debug.php' );
			require_once ( NGFB_PLUGINDIR . 'lib/util.php' );
			require_once ( NGFB_PLUGINDIR . 'lib/notices.php' );
			require_once ( NGFB_PLUGINDIR . 'lib/options.php' );
			require_once ( NGFB_PLUGINDIR . 'lib/user.php' );
			require_once ( NGFB_PLUGINDIR . 'lib/media.php' );
			require_once ( NGFB_PLUGINDIR . 'lib/postmeta.php' );
			require_once ( NGFB_PLUGINDIR . 'lib/style.php' );
			require_once ( NGFB_PLUGINDIR . 'lib/cache.php' );

			if ( is_admin() ) {
				require_once ( NGFB_PLUGINDIR . 'lib/admin.php' );
				require_once ( NGFB_PLUGINDIR . 'lib/form.php' );
				foreach ( $this->setting_libs as $id => $name )
					require_once ( NGFB_PLUGINDIR . 'lib/settings/' . $id . '.php' );
				unset ( $id, $name );
				require_once ( NGFB_PLUGINDIR . 'lib/ext/parse-readme.php' );
			} else {
				require_once ( NGFB_PLUGINDIR . 'lib/head.php' );
				require_once ( NGFB_PLUGINDIR . 'lib/opengraph.php' );
				require_once ( NGFB_PLUGINDIR . 'lib/tags.php' );
				require_once ( NGFB_PLUGINDIR . 'lib/webpage.php' );
				require_once ( NGFB_PLUGINDIR . 'lib/social.php' );
				require_once ( NGFB_PLUGINDIR . 'lib/functions.php' );
				require_once ( NGFB_PLUGINDIR . 'lib/ext/googl.php' );
				foreach ( $this->shortcode_libs as $id => $name )
					require_once ( NGFB_PLUGINDIR . 'lib/shortcodes/' . $id . '.php' );
				unset ( $id, $name );
			}

			foreach ( $this->website_libs as $id => $name )
				require_once ( NGFB_PLUGINDIR . 'lib/websites/' . $id . '.php' );
			unset ( $id, $name );

			foreach ( $this->widget_libs as $id => $name )
				require_once ( NGFB_PLUGINDIR . 'lib/widgets/' . $id . '.php' );
			unset ( $id, $name );

			if ( file_exists( NGFB_PLUGINDIR . 'lib/addon.php' ) )
				require_once ( NGFB_PLUGINDIR . 'lib/addon.php' );
		}

		private function check_deps() {
		
			// ngfb pro
			$this->is_avail['aop'] = class_exists( 'ngfbAddOnPro' ) ? true : false;

			// php v4.0.6+
			$this->is_avail['mbdecnum'] = function_exists( 'mb_decode_numericentity' ) ? true : false;

			// post thumbnail feature is supported by wp theme
			$this->is_avail['postthumb'] = function_exists( 'has_post_thumbnail' ) ? true : false;

			// nextgen gallery plugin
			$this->is_avail['ngg'] = class_exists( 'nggdb' ) && method_exists( 'nggdb', 'find_image' ) ? true : false;

			// cdn linker plugin
			$this->is_avail['cdnlink'] = class_exists( 'CDNLinksRewriterWordpress' ) ? true : false;
		}

		private function setup_msgs() {
			// define some re-usable text strings
			$this->msgs = array(
				'pro_feature' => '<div class="pro_feature"><a href="' . $this->urls['plugin'] . '" 
					target="_blank">Upgrade to the Pro version to enable the following features</a>.</div>',
				'purchase' => 'This plugin has taken many months to develop, test, fine-tune and support. 
					If you appreciate the result of those efforts, please support us by 
					<a href="' . $this->urls['plugin'] . '" target="_blank">purchasing the Pro version</a>.',
				'review' => 'You can also contribute by 
					<a href="http://wordpress.org/support/view/plugin-reviews/nextgen-facebook" 
					target="_blank">rating the plugin</a> on WordPress.org.',
				'thankyou' => 'Thank you for your purchase! I hope the ' . $this->fullname . 
					' plugin will exceed all of your expectations.',
				'help_boxes' => 'Individual option boxes (like this one) can be opened / closed by clicking on their title bar, 
					moved and re-ordered by dragging them, and removed / added from the <em>Screen Options</em> tab (top-right).',
				'help_forum' => 'Need help? Visit the <a href="http://wordpress.org/support/plugin/nextgen-facebook" 
					target="_blank">NGFB Open Graph Support Forum</a> on WordPress.org.',
			);
		}

		// get the options, upgrade the options (if necessary), and validate their values
		private function setup_vars( $activate = false ) {

			// load options 
			$this->options = get_option( NGFB_OPTIONS_NAME );
			if ( $this->is_avail['ngg'] == true ) $this->ngg_options = get_option( 'ngg_options' );
			if ( $this->is_avail['aop'] == true ) $this->fullname .= ' Pro';
			$this->setup_msgs();
	
			// create essential object classes
			$this->debug = new ngfbDebug( $this->fullname, 'NGFB', array( 
					'html' => ( ! empty( $this->options['ngfb_debug'] ) || 
						( defined( 'NGFB_DEBUG' ) && NGFB_DEBUG ) ? true : false ),
					'wp' => ( defined( 'NGFB_WP_DEBUG' ) && NGFB_WP_DEBUG ? true : false ),
				)
			);
			$this->util = new ngfbUtil( $this );
			$this->notices = new ngfbNotices( $this );
			$this->opt = new ngfbOptions( $this );

			// plugin is being activated - create default options
			if ( $activate == true ) {
				$this->debug->log( 'plugin activated' );
				if ( ! is_array( $this->options ) || empty( $this->options ) ||
					! empty( $this->options['ngfb_reset'] ) || ( defined( 'NGFB_RESET' ) && NGFB_RESET ) ) {
	
					$this->options = $this->opt->get_defaults();
					$this->options['ngfb_version'] = $this->opt->version;
					delete_option( NGFB_OPTIONS_NAME );
					add_option( NGFB_OPTIONS_NAME, $this->options, null, 'yes' );
					$this->debug->log( 'default options have been added to the database' );
				}
				$this->debug->log( 'exiting early for: init_plugin() to follow' );
				return;	// no need to continue, init_plugin() will handle the rest
			}

			// continue creating object classes
			$this->user = new ngfbUser( $this );
			$this->media = new ngfbMedia( $this );
			$this->meta = new ngfbPostMeta( $this );
			$this->style = new ngfbStyle( $this );
			$this->cache = new ngfbCache( $this );

			if ( is_admin() ) {
				$this->admin = new ngfbAdmin( $this );
				$this->admin->plugin_name = plugin_basename( __FILE__ );
			} else {
				$this->head = new ngfbHead( $this );		// wp_head / opengraph
				$this->tags = new ngfbTags( $this );		// ngg image tags and wp post/page tags
				$this->webpage = new ngfbWebPage( $this );	// title, desc, etc., plus shortcodes
				$this->social = new ngfbSocial( $this );	// wp_head and wp_footer js and buttons
			}

			// create pro class object last since it extends previous classes (util, meta, and admin->settings)
			if ( $this->is_avail['aop'] == true )
				$this->pro = new ngfbAddOnPro( $this );

			// error checks / messages
			if ( $this->is_avail['mbdecnum'] != true )
				$this->notices->err( 'The <code><a href="http://php.net/manual/en/function.mb-decode-numericentity.php" 
					target="_blank">mb_decode_numericentity()</a></code> function (available since PHP v4.0.6) is missing. 
					This function is required to decode UTF8 entities. Please update your PHP installation as soon as possible.' );

			// make sure we have something to work with - upgrade options if necessary
			if ( ! empty( $this->options ) && is_array( $this->options ) ) {
				if ( empty( $this->options['ngfb_version'] ) || $this->options['ngfb_version'] !== $this->opt->version )
					$this->options = $this->opt->upgrade( $this->options, $this->opt->get_defaults() );
			} else {
				if ( $this->options === false )
					$err_msg = 'did not find an "' . NGFB_OPTIONS_NAME . '" entry in';
				elseif ( ! is_array( $this->options ) )
					$err_msg = 'returned a non-array value when reading "' . NGFB_OPTIONS_NAME . '" from';
				elseif ( empty( $this->options ) )
					$err_msg = 'returned an empty array when reading "' . NGFB_OPTIONS_NAME . '" from';
				else 
					$err_msg = 'returned an unknown condition when reading "' . NGFB_OPTIONS_NAME . '" from';

				$this->notices->err( 'WordPress ' . $err_msg . ' the options database table. 
					All plugin settings have been returned to their default values (though nothing has been saved back to the database yet). 
					<a href="' . $this->util->get_admin_url() . '">Please visit the plugin settings pages to review and save the options</a>.' );

				$this->options = $this->opt->get_defaults();
			}

			if ( is_admin() )
				if ( $this->debug->is_on( 'wp' ) == true ) 
					$this->cache->file_expire = 0;
				else 
					$this->cache->file_expire = $this->update_hours * 60 * 60;
			elseif ( $this->is_avail['aop'] == true )
				$this->cache->file_expire = ! empty( $this->options['ngfb_file_cache_hrs'] ) ? 
					$this->options['ngfb_file_cache_hrs'] * 60 * 60 : 0;
			else $this->cache->file_expire = 0;

			if ( $this->debug->is_on( 'wp' ) == true ) {
				$this->debug->log( 'NGFB WP debug mode is ON' );
				$this->debug->log( 'File cache expiration set to ' . $this->cache->file_expire . ' second(s)' );
			}

			if ( $this->debug->is_on( 'html' ) == true ) {
				$this->cache->object_expire = 1;
				$this->debug->log( 'NGFB HTML debug mode is ON' );
				$this->debug->log( 'WP object cache expiration set to ' . $this->cache->object_expire . ' second(s) for new objects' );
				$this->notices->inf( 'NGFB HTML debug mode is ON. Activity messages are being added to webpages as hidden HTML comments. 
					WP object cache expiration <em>temporarily</em> set at ' . $this->cache->object_expire . ' second(s).' );
				if ( $this->is_avail['aop'] == false )
					$this->notices->inf( $this->msgs['purchase'] );
			} else $this->cache->object_expire = $this->options['ngfb_object_cache_exp'];
		}

	}

        global $ngfb;
	$ngfb = new ngfbPlugin();
}

?>

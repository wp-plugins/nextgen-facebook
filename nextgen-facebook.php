<?php
/*
Plugin Name: NextGEN Facebook Open Graph
Plugin URI: http://surniaulula.com/wordpress-plugins/nextgen-facebook-open-graph/
Description: Adds complete Open Graph meta tags for Facebook, Google+, Twitter, LinkedIn, etc., plus optional social sharing buttons in content or widget.
Version: 5.0.dev7
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

		public $version = '5.0.dev7';	// only for display purposes
		public $opts_version = '21';	// increment when adding/removing $default_options
		public $is_avail = array();	// assoc array for function/class/method/etc. checks
		public $options = array();
		public $ngg_options = array();

		public $debug;		// ngfbDebug
		public $cache;		// ngfbCache
		public $user;		// ngfbUser
		public $og;		// ngfbOpenGraph
		public $social;		// ngfbSocial
		public $shortcodes;	// ngfbShortCodes
		public $pro;		// ngfbPro
		public $admin;		// ngfbAdmin

		public $default_options = array(
			'link_author_field' => 'gplus',
			'link_publisher_url' => '',
			'og_art_section' => '',
			'og_img_size' => 'medium',
			'og_img_max' => 1,
			'og_vid_max' => 1,
			'og_def_img_id_pre' => 'wp',
			'og_def_img_id' => '',
			'og_def_img_url' => '',
			'og_def_img_on_index' => 1,
			'og_def_img_on_search' => 1,
			'og_def_author_id' => 0,
			'og_def_author_on_index' => 0,
			'og_def_author_on_search' => 0,
			'og_ngg_tags' => 0,
			'og_page_parent_tags' => 0,
			'og_page_title_tag' => 0,
			'og_author_field' => 'facebook',
			'og_author_fallback' => 1,
			'og_title_sep' => '|',
			'og_title_len' => 100,
			'og_desc_len' => 280,
			'og_desc_strip' => 0,
			'og_desc_wiki' => 0,
			'og_wiki_tag' => 'Wiki-',
			'og_admins' => '',
			'og_app_id' => '',
			'og_empty_tags' => 0,
			'buttons_on_index' => 0,
			'buttons_on_ex_pages' => 0,
			'buttons_location' => 'bottom',
			'fb_enable' => 0,
			'fb_order' => 1,
			'fb_js_loc' => 'header',
			'fb_lang' => 'en_US',
			'fb_send' => 1,
			'fb_layout' => 'button_count',
			'fb_width' => 200,
			'fb_colorscheme' => 'light',
			'fb_font' => 'arial',
			'fb_show_faces' => 0,
			'fb_action' => 'like',
			'fb_markup' => 'xfbml',
			'gp_enable' => 0,
			'gp_order' => 2,
			'gp_js_loc' => 'header',
			'gp_lang' => 'en-US',
			'gp_action' => 'plusone',
			'gp_size' => 'medium',
			'gp_annotation' => 'bubble',
			'twitter_enable' => 0,
			'twitter_order' => 3,
			'twitter_js_loc' => 'header',
			'twitter_lang' => 'en',
			'twitter_caption' => 'title',
			'twitter_cap_len' => 140,
			'twitter_count' => 'horizontal',
			'twitter_size' => 'medium',
			'twitter_dnt' => 1,
			'twitter_shorten' => 1,
			'linkedin_enable' => 0,
			'linkedin_order' => 4,
			'linkedin_js_loc' => 'header',
			'linkedin_counter' => 'right',
			'linkedin_showzero' => 1,
			'pin_enable' => 0,
			'pin_order' => 5,
			'pin_js_loc' => 'header',
			'pin_count_layout' => 'horizontal',
			'pin_img_size' => 'large',
			'pin_caption' => 'both',
			'pin_cap_len' => 500,
			'tumblr_enable' => 0,
			'tumblr_order' => 7,
			'tumblr_js_loc' => 'footer',
			'tumblr_button_style' => 'share_1',
			'tumblr_desc_len' => 300,
			'tumblr_photo' => 1,
			'tumblr_img_size' => 'large',
			'tumblr_caption' => 'both',
			'tumblr_cap_len' => 500,
			'stumble_enable' => 0,
			'stumble_order' => 6,
			'stumble_js_loc' => 'header',
			'stumble_badge' => 1,
			'inc_description' => 1,
			'inc_fb:admins' => 1,
			'inc_fb:app_id' => 1,
			'inc_og:site_name' => 1,
			'inc_og:title' => 1,
			'inc_og:type' => 1,
			'inc_og:url' => 1,
			'inc_og:description' => 1,
			'inc_og:image' => 1,
			'inc_og:image:width' => 1,
			'inc_og:image:height' => 1,
			'inc_og:video' => 1,
			'inc_og:video:width' => 1,
			'inc_og:video:height' => 1,
			'inc_og:video:type' => 1,
			'inc_article:author' => 1,
			'inc_article:published_time' => 1,
			'inc_article:modified_time' => 1,
			'inc_article:section' => 1,
			'inc_article:tag' => 1,
			'ngfb_version' => '',
			'ngfb_donated' => 0,
			'ngfb_reset' => 0,
			'ngfb_debug' => 0,
			'ngfb_enable_shortcode' => 0,
			'ngfb_filter_title' => 1,
			'ngfb_filter_excerpt' => 0,
			'ngfb_filter_content' => 1,
			'ngfb_skip_small_img' => 1,
			'ngfb_verify_certs' => 0,
			'ngfb_file_cache_hrs' => 0,
			'ngfb_object_cache_exp' => 60,
			'ngfb_googl_api_key' => '' );

		public $social_options_prefix = array(
			'facebook' => 'fb', 
			'gplus' => 'gp',
			'twitter' => 'twitter',
			'linkedin' => 'linkedin',
			'pinterest' => 'pin',
			'stumbleupon' => 'stumble',
			'tumblr' => 'tumblr' );

		public $social_class_names = array(
			'facebook' => 'Facebook', 
			'gplus' => 'GooglePlus',
			'twitter' => 'Twitter',
			'linkedin' => 'LinkedIn',
			'pinterest' => 'Pinterest',
			'stumbleupon' => 'StumbleUpon',
			'tumblr' => 'Tumblr' );

		private $renamed_options = array(
			'add_meta_desc' => 'inc_description',
			'og_def_img' => 'og_def_img_url',
			'og_def_home' => 'og_def_img_on_index',
			'og_def_on_home' => 'og_def_img_on_index',
			'og_def_on_search' => 'og_def_img_on_search',
			'buttons_on_home' => 'buttons_on_index',
			'buttons_lang' => 'gp_lang',
			'ngfb_cache_hours' => 'ngfb_file_cache_hrs' );

		public function __construct() {

			$this->define_constants();	// define constants first for option defaults
			$this->load_libs();

			register_activation_hook( __FILE__, array( &$this, 'activate' ) );
			register_uninstall_hook( __FILE__, array( 'ngfbPlugin', 'uninstall' ) );

			add_action( 'init', array( &$this, 'init_plugin' ) );

			add_filter( 'language_attributes', array( &$this, 'add_og_doctype' ) );
		}

		// called by WP init action
		public function init_plugin() {

			// run check_deps() before set_vars() to get ngg options (if the plugin is installed)
			$this->check_deps();
			$this->set_vars();

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
				define( 'NGFB_HEAD_PRIORITY', 20 );

			if ( ! defined( 'NGFB_CONTENT_PRIORITY' ) )
				define( 'NGFB_CONTENT_PRIORITY', 100 );
			
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
				define( 'NGFB_PEM_FILE', NGFB_PLUGINDIR . 'lib/curl/cacert.pem' );
		}

		private function load_libs() {

			require_once ( dirname ( __FILE__ ) . '/lib/util.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/debug.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/cache.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/googl.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/head.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/opengraph.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/social.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/widgets.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/shortcodes.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/functions.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/user.php' );

			if ( is_admin() ) {
				require_once ( dirname ( __FILE__ ) . '/lib/admin.php' );
				require_once ( dirname ( __FILE__ ) . '/lib/form.php' );
			}

			foreach ( $this->social_class_names as $filename => $classname )
				require_once ( dirname ( __FILE__ ) . '/lib/websites/' . $filename . '.php' );

			if ( file_exists( dirname ( __FILE__ ) . '/lib/pro.php' ) )
				require_once ( dirname ( __FILE__ ) . '/lib/pro.php' );
		}

		// get the options, upgrade the option names (if necessary), and validate their values
		private function set_vars() {

			// load options first for use in __construct() methods
			$this->options = get_option( NGFB_OPTIONS_NAME );

			if ( $this->is_avail['ngg'] == true )
				$this->ngg_options = get_option( 'ngg_options' );

			$this->debug = new ngfbDebug();
			$this->util = new ngfbUtil( &$this );
			$this->head = new ngfbHead( $this );
			$this->og = new ngfbOpenGraph( $this );
			$this->social = new ngfbSocial( $this );
			$this->user = new ngfbUser( &$this );

			if ( ! empty( $this->options['ngfb_enable_shortcode'] ) )
				$this->shortcodes = new ngfbShortCodes( $this );

			if ( is_admin() ) {
				$this->admin = new ngfbAdmin( $this );
				$this->admin->plugin_name = plugin_basename( __FILE__ );
			}

			if ( $this->is_avail['ngfbpro'] == true )
				$this->pro = new ngfbPro( $this );

			// make sure we have something to work with
			if ( ! empty( $this->options ) && is_array( $this->options ) ) {
				if ( empty( $this->options['ngfb_version'] ) 
					|| $this->options['ngfb_version'] !== $this->opts_version )
					$this->options = $this->upgrade_options( $this->options );
			} else {
				$this->admin->msg_err[] = 'WordPress returned an error when reading the "' . NGFB_OPTIONS_NAME . '" array from the options database table. 
					All plugin settings have been returned to their default values (though nothing has been saved back to the database). 
					<a href="' . $this->admin->get_options_url() . '">Please visit the settings page to review and change the default values</a>.';
				$this->debug->show( print_r( get_option( NGFB_OPTIONS_NAME ) ), 'get_option("' . NGFB_OPTIONS_NAME . '")' );
				$this->options = $this->default_options;
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
				$this->debug->push( 'debug mode active - setting ngfb_object_cache_exp = ' . $this->cache->object_expire . ' seconds' );
				$this->admin->msg_inf[] = 'Debug mode is turned ON. Debugging information is being generated and added to webpages as hidden HTML comments. 
					WP object cache expiration time has been temporarily set to ' . $this->cache->object_expire . ' second 
					(instead of ' . $this->options['ngfb_object_cache_exp'] . ' seconds).';

			} else $this->cache->object_expire = $this->options['ngfb_object_cache_exp'];
		}

		// called by WP language_attributes filter
		// it would be better to use '<head prefix="">' but WP doesn't offer hooks into <head>
		public function add_og_doctype( $output ) {
			return $output . ' xmlns:og="http://ogp.me/ns" xmlns:fb="http://ogp.me/ns/fb"';
		}

		// create new default options on plugin activation if ngfb_reset = 1, NGFB_RESET is true,
		// NGFB_OPTIONS_NAME is not an array, or NGFB_OPTIONS_NAME is an empty array
		public function activate() {
			if ( ! empty( $this->options['ngfb_reset'] ) 
				|| ( defined( 'NGFB_RESET' ) && NGFB_RESET ) 
				|| ! is_array( $this->options ) 
				|| empty( $this->options ) ) {

				$opts = $this->default_options;
				$opts['ngfb_version'] = $this->opts_version;

				delete_option( NGFB_OPTIONS_NAME );	// remove old options, if any
				add_option( NGFB_OPTIONS_NAME, $opts, null, 'yes' );
			}
		}

		// delete options table entries only when plugin deactivated and deleted
		public function uninstall() {
			delete_option( NGFB_OPTIONS_NAME );
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

			if ( $this->is_avail['mbdecnum'] != true )
				$this->admin->msg_err[] = 'The <code><a href="http://php.net/manual/en/function.mb-decode-numericentity.php" 
					target="_blank">mb_decode_numericentity()</a></code> function (available since PHP v4.0.6) is missing. 
					This function is required to decode UTF8 entities. Please update your PHP installation as soon as possible.';
		}

		private function upgrade_options( &$opts = array() ) {

			// make sure we have something to work with
			if ( ! empty( $opts ) && is_array( $opts ) ) {

				$this->admin->msg_inf[] = 'Option settings from the database have been read and updated in memory. 
					These updates have NOT been saved back to the database. 
					<a href="' . $this->admin->get_options_url() . '">Please review and save these new settings</a>.';
	
				// move old option values to new option names
				foreach ( $this->renamed_options as $old => $new )
					if ( empty( $opts[$new] ) && ! empty( $opts[$old] ) ) {
						$this->admin->msg_inf[] = 'Renamed \'' . $old . '\' option to \'' . $new . '\' with a value of \'' . $opts[$old] . '\'.';
						$opts[$new] = $opts[$old];
					}
				unset ( $old, $new );
	
				// unset options that no longer exist
				foreach ( $opts as $key => $val )
					// check that the key is not empty, and doesn't exist in the default options
					if ( ! empty( $key ) && ! array_key_exists( $key, $this->default_options ) )
						unset( $opts[$key] );
				unset ( $key, $val );
	
				// add missing options and set to defaults
				foreach ( $this->default_options as $key => $def_val ) {
					if ( ! empty( $key ) && ! array_key_exists( $key, $opts ) ) {
						$this->admin->msg_inf[] = 'Adding missing \'' . $key . '\' option with the default value of \'' . $def_val . '\'.';
						$opts[$key] = $def_val;
					}
				}

				// sanitize and verify the options - just in case
				$opts = $this->sanitize_options( $opts, $this->default_options );

				// don't show message if already donated, or pro version installed
				if ( empty( $opts['ngfb_donated'] ) && $this->is_avail['ngfbpro'] == false )
					$this->admin->msg_inf[] = '<b>' . NGFB_LONGNAME . ' has taken many, many months to develop and fine-tune. 
						Please suppport us by <a href="' . $this->admin->get_options_url() . '">donating</a> and 
						<a href="http://wordpress.org/support/view/plugin-reviews/nextgen-facebook">rating it on wordpress.org</a>.</b>';
			}
			return $opts;
		}

		// sanitize and validate input
		public function sanitize_options( &$opts = array(), &$def_opts = array() ) {

			// make sure we have something to work with
			if ( ! empty( $opts ) && is_array( $opts ) ) {

				// loop through all the known option keys
				foreach ( $def_opts as $key => $def_val ) {

					switch ( $key ) {

						// remove HTML
						case 'og_def_img_url' :
						case 'og_app_id' :
							$opts[$key] = wp_filter_nohtml_kses( $opts[$key] );
							break;

						// stip off leading URLs (leaving just the account names)
						case 'og_admins' :
							$opts[$key] = preg_replace( '/(http|https):\/\/[^\/]*?\//', '', 
								wp_filter_nohtml_kses( $opts[$key] ) );
							break;

						// must be a URL
						case 'link_publisher_url' :
						case 'og_def_img_url' :
							if ( $opts[$key] && ! preg_match( '/:\/\//', $opts[$key] ) ) 
								$opts[$key] = $def_val;
							break;

						// must be numeric (blank or zero is ok)
						case 'og_desc_len' : 
						case 'og_img_max' :
						case 'og_vid_max' :
						case 'og_def_img_id' :
						case 'og_def_author_id' :
						case 'ngfb_file_cache_hrs' :
							if ( $opts[$key] && ! is_numeric( $opts[$key] ) ) 
								$opts[$key] = $def_val;
							break;

						// integer options that cannot be zero
						case 'og_title_len' : 
						case 'fb_order' : 
						case 'fb_width' : 
						case 'gp_order' : 
						case 'twitter_order' : 
						case 'linkedin_order' : 
						case 'pin_order' : 
						case 'pin_cap_len' : 
						case 'tumblr_order' : 
						case 'tumblr_desc_len' : 
						case 'tumblr_cap_len' :
						case 'stumble_order' : 
						case 'stumble_badge' :
						case 'ngfb_object_cache_exp' :
							if ( empty( $opts[$key] ) || ! is_numeric( $opts[$key] ) )
								$opts[$key] = $def_val;
							break;

						// options that cannot be blank
						case 'link_author_field' :
						case 'og_img_size' : 
						case 'og_author_field' :
						case 'buttons_location' : 
						case 'fb_js_loc' : 
						case 'fb_markup' : 
						case 'gp_js_loc' : 
						case 'gp_lang' : 
						case 'gp_action' : 
						case 'gp_size' : 
						case 'gp_annotation' : 
						case 'twitter_js_loc' : 
						case 'twitter_count' : 
						case 'twitter_size' : 
						case 'linkedin_js_loc' : 
						case 'linkedin_counter' :
						case 'pin_js_loc' : 
						case 'pin_count_layout' :
						case 'pin_img_size' :
						case 'pin_caption' :
						case 'tumblr_js_loc' : 
						case 'tumblr_button_style' :
						case 'tumblr_img_size' :
						case 'tumblr_caption' :
						case 'stumble_js_loc' : 
							$opts[$key] = wp_filter_nohtml_kses( $opts[$key] );
							if ( empty( $opts[$key] ) ) $opts[$key] = $def_val;
							break;

						// everything else is assumed to be a true/false checkbox option
						default :
							// make sure the default option is true/false - just in case
							if ( $def_val === 0 || $def_val === 1 )
								$opts[$key] = empty( $opts[$key] ) ? 0 : 1;
							break;
					}
				}
				unset ( $key, $def_val );

				if ( $opts['og_desc_len'] < NGFB_MIN_DESC_LEN ) 
					$opts['og_desc_len'] = NGFB_MIN_DESC_LEN;
	
			}
			return $opts;
		}

		// called from Tumblr class
		public function get_quote() {
			global $post;
			if ( empty( $post ) ) return;
			if ( has_excerpt( $post->ID ) ) $content = get_the_excerpt( $post->ID );
			else $content = $post->post_content;				// fallback to regular content
			$content = $this->util->cleanup_html_tags( $content, false );	// remove shortcodes, etc., but don't strip html tags
			return $content;
		}

		// called from Tumblr, Pinterest, and Twitter classes
		public function get_caption( $type = 'title', $length = 300, $use_post = true ) {
			$caption = '';
			switch( strtolower( $type ) ) {
				case 'title' :
					$caption = $this->get_title( $length, '...', $use_post );
					break;
				case 'excerpt' :
					$caption = $this->get_description( $length, '...', $use_post );
					break;
				case 'both' :
					$title = $this->get_title( null, null, $use_post);
					$caption = $title . ' : ' . $this->get_description( $length - strlen( $title ) - 3, '...', $use_post );
					break;
			}
			return $caption;
		}

		public function get_title( $textlen = 100, $trailing = '', $use_post = false ) {
			global $post, $page, $paged;
			$title = '';
			$page_num = '';
			$parent_title = '';

			if ( is_category() ) { 

				$title = single_cat_title( '', false );
				$this->debug->push( 'single_cat_title() = "' . $title . '"' );
				$cat_parents = get_category_parents( get_cat_ID( $title ), false, ' ' . $this->options['og_title_sep'] . ' ', false );

				// use is_wp_error() to avoid "Object of class WP_Error could not be converted to string" error
				if ( is_wp_error( $cat_parents ) ) {
					$this->debug->push( 'get_category_parents() returned WP_Error object.' );
				} else {
					$this->debug->push( 'get_category_parents() = "' . $cat_parents . '"' );
					if ( ! empty( $cat_parents ) ) {
						$title = trim( $cat_parents, ' ' . $this->options['og_title_sep'] );
						// beautify title with category names that end with three dots
						$title = preg_replace( '/\.\.\. \\' . $this->options['og_title_sep'] . ' /', '... ', $title );
					}
				}
				unset ( $cat_parents );

			} elseif ( ! is_singular() && ! empty( $post ) && ! empty( $use_post ) ) {

				$this->debug->push( '$use_post = ' . ( $use_post ? 'true' : 'false' ) );
				$title = get_the_title();
				$this->debug->push( 'get_the_title() = "' . $title . '"' );
				if ( $post->post_parent ) {
					$parent_title = get_the_title( $post->post_parent );
					if ( $parent_title ) $title .= ' (' . $parent_title . ')';
				}

			} else {
				/* The title text depends on the query:
				 *	Single post = the title of the post 
				 *	Date-based archive = the date (e.g., "2006", "2006 - January") 
				 *	Category = the name of the category 
				 *	Author page = the public name of the user 
				 */
				$title = trim( wp_title( $this->options['og_title_sep'], false, 'right' ), ' ' . $this->options['og_title_sep'] );
				$this->debug->push( 'wp_title() = "' . $title . '"' );
			}

			// just in case
			if ( ! $title ) {
				$title = get_bloginfo( 'name', 'display' );
				$this->debug->push( 'get_bloginfo() = "' . $title . '"' );
			}

			// add a page number if necessary
			if ( $paged >= 2 || $page >= 2 ) {
				$page_num = ' ' . $this->options['og_title_sep'] . ' ' . sprintf( 'Page %s', max( $paged, $page ) );
				$textlen = $textlen - strlen( $page_num );	// make room for the page number
			}

			$title = $this->util->decode( $title );

			if ( ! empty( $this->options['ngfb_filter_title'] ) ) {
				$title = apply_filters( 'the_title', $title );
				$this->debug->push( 'apply_filters() = "' . $title . '"' );
			}

			$title = $this->util->cleanup_html_tags( $title );
			$this->debug->push( 'this->util->cleanup_html_tags() = "' . $title . '"' );

			// append the text number after the trailing character string
			if ( $textlen > 0 ) $title = $this->util->limit_text_length( $title, $textlen, $trailing );

			return $title . $page_num;
		}

		// called from the view/gallery-uwf.php template
		public function get_wiki_summary() {
			global $post;
			$desc = '';
			if ( $this->is_avail['wikibox'] !== true ) return $desc;
			$tag_prefix = $this->options['og_wiki_tag'];
			$tags = wp_get_post_tags( $post->ID, array( 'fields' => 'names') );
			$this->debug->push( 'post tags = ' . implode( ', ', $tags ) );
			foreach ( $tags as $tag_name ) {
				if ( $tag_prefix ) {
					if ( preg_match( "/^$tag_prefix/", $tag_name ) ) {
						$tag_name = preg_replace( "/^$tag_prefix/", '', $tag_name );
						if ( $tag_name == 'NoWikiText' ) return $desc;
					}
					else continue;	// skip tags that don't have the prefix
				}
				$desc .= wikibox_summary( $tag_name, 'en', false ); 
				$this->debug->push( 'wikibox_summary("' . $tag_name . '") = ' . $desc );
			}
			if ( empty( $desc ) ) {
				$title = the_title( '', '', false );
				$desc .= wikibox_summary( $title, 'en', false );
				$this->debug->push( 'wikibox_summary("' . $title . '") = ' . $desc );
			}
			return $desc;
		}

		public function get_description( $textlen = 300, $trailing = '', $use_post = false ) {
			global $post;
			$desc = '';
			if ( is_singular() || ( ! empty( $post ) && ! empty( $use_post ) ) ) {

				$this->debug->push( 'is_singular() = ' . ( is_singular() ? 'true' : 'false' ) );
				$this->debug->push( 'use_post = ' . ( $use_post  ? 'true' : 'false' ) );

				// use the excerpt, if we have one
				if ( has_excerpt( $post->ID ) ) {
					$this->debug->push( 'has_excerpt() = true' );
					$desc = $post->post_excerpt;
					if ( ! empty( $this->options['ngfb_filter_excerpt'] ) )
						$desc = apply_filters( 'the_excerpt', $desc );
		
				// if there's no excerpt, then use WP-WikiBox for page content (if wikibox is active and og_desc_wiki option is true)
				} elseif ( is_page() && ! empty( $this->options['og_desc_wiki'] ) && $this->is_avail['wikibox'] == true ) {
					$this->debug->push( 'is_page() && options["og_desc_wiki"] = 1 && is_avail["wikibox"] = true' );
					$desc = $this->get_wiki_summary();
				} 
		
				if ( empty( $desc ) ) {
					$this->debug->push( 'calling this->get_content_filtered()' );
					$desc = $this->get_content_filtered( $this->options['ngfb_filter_content'] );
				}
		
				// ignore everything until the first paragraph tag if $this->options['og_desc_strip'] is true
				if ( $this->options['og_desc_strip'] ) $desc = preg_replace( '/^.*?<p>/i', '', $desc );	// question mark makes regex un-greedy
		
			} elseif ( is_author() ) { 
		
				$this->debug->push( 'is_author() = true' );
				the_post();
				$desc = sprintf( 'Authored by %s', get_the_author_meta( 'display_name' ) );
				$author_desc = preg_replace( '/[\r\n\t ]+/s', ' ', get_the_author_meta( 'description' ) );	// put everything on one line
				if ( $author_desc ) $desc .= ' : '.$author_desc;		// add the author's profile description, if there is one
		
			} elseif ( is_tag() ) {
		
				$this->debug->push( 'is_tag() = true' );
				$desc = sprintf( 'Tagged with %s', single_tag_title( '', false ) );
				$tag_desc = preg_replace( '/[\r\n\t ]+/s', ' ', tag_description() );	// put everything on one line
				if ( $tag_desc ) $desc .= ' : '.$tag_desc;			// add the tag description, if there is one
		
			} elseif ( is_category() ) { 
		
				$this->debug->push( 'is_category() = true' );
				$desc = sprintf( '%s Category', single_cat_title( '', false ) ); 
				$cat_desc = preg_replace( '/[\r\n\t ]+/', ' ', category_description() );	// put everything on one line
				if ($cat_desc) $desc .= ' : '.$cat_desc;			// add the category description, if there is one
			}
			elseif ( is_day() ) $desc = sprintf( 'Daily Archives for %s', get_the_date() );
			elseif ( is_month() ) $desc = sprintf( 'Monthly Archives for %s', get_the_date('F Y') );
			elseif ( is_year() ) $desc = sprintf( 'Yearly Archives for %s', get_the_date('Y') );
			else $desc = get_bloginfo( 'description', 'display' );

			$desc = $this->util->cleanup_html_tags( $desc );

			if ( $textlen > 0 ) 
				$desc = $this->util->limit_text_length( $desc, $textlen, '...' );

			return $desc;
		}

		public function get_tags() {
			$tags = array();
			if ( is_singular() ) {
				global $post;
				$tags = array_merge( $tags, $this->get_wp_tags( $post->ID ) );
				if ( $this->options['og_ngg_tags'] && $this->is_avail['postthumb'] == true && has_post_thumbnail( $post->ID ) ) {
					$pid = get_post_thumbnail_id( $post->ID );
					if ( is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' )
						$tags = array_merge( $tags, $this->get_ngg_tags( $pid ) );
				}
			} elseif ( is_search() )
				$tags = preg_split( '/ *, */', get_search_query( false ) );
		
			return array_unique( array_map( 'strtolower', $tags ) );	// filter for duplicate (lowercase) element values - just in case
		}

		private function get_wp_tags( $post_id ) {
			$tags = array();
			$post_ids = array ( $post_id );	// array of one
			if ( $this->options['og_page_parent_tags'] && is_page( $post_id ) )
				$post_ids = array_merge( $post_ids, get_post_ancestors( $post_id ) );
			$tag_prefix = empty( $this->options['og_wiki_tag'] ) ? '' : $this->options['og_wiki_tag'];
			foreach ( $post_ids as $id ) {
				if ( $this->options['og_page_title_tag'] && is_page( $id ) )
					$tags[] = get_the_title( $id );
				foreach ( wp_get_post_tags( $id, array( 'fields' => 'names') ) as $tag_name ) {
					if ( $this->options['og_desc_wiki'] && $tag_prefix ) 
						$tag_name = preg_replace( "/^$tag_prefix/", '', $tag_name );
					$tags[] = $tag_name;
				}
			}
			return $tags;
		}

		// called from the view/gallery-meta.php template
		public function get_ngg_tags( $pid ) {
			$tags = array();
			if ( $this->is_avail['ngg'] == true && is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' ) {
				$tags = wp_get_object_terms( substr( $pid, 4 ), 'ngg_tag', 'fields=names' );
			}
			return array_map( 'strtolower', $tags );
		}

		public function get_content_filtered( $filter_content = true ) {
			global $post;
			if ( empty( $post ) ) return;
			$this->debug->push( 'using content from post id ' . $post->ID );
			$cache_salt = __METHOD__ . '(post:' . $post->ID . ( $filter_content  ? '_filtered' : '_unfiltered' ) . ')';
			$cache_id = NGFB_SHORTNAME . '_' . md5( $cache_salt );
			$cache_type = 'object cache';
			$content = wp_cache_get( $cache_id, __METHOD__ );
			$this->debug->push( $cache_type . ': filtered content wp_cache id salt "' . $cache_salt . '"' );

			if ( $content !== false ) {
				$this->debug->push( $cache_type . ': filtered content retrieved from wp_cache for id "' . $cache_id . '"' );
				return $content;
			} 
			$content = $post->post_content;
			$content_strlen_before = strlen( $content );

			// remove singlepics, which we detect and use before-hand 
			$content = preg_replace( '/\[singlepic[^\]]+\]/', '', $content, -1, $count );
			if ( $count > 0 ) $this->debug->push( $count . ' [singlepic] shortcode(s) removed from content' );

			if ( $filter_content == true ) {

				// temporarily remove this->social->add_content() to prevent recursion
				$filter_removed = remove_filter( 'the_content', array( &$this->social, 'add_content' ), NGFB_CONTENT_PRIORITY );
				$this->debug->push( 'this->social->add_content() filter removed = ' . ( $filter_removed  ? 'true' : 'false' ) );

				// temporarily remove ngfb shortcode to prevent recursion
				if ( ! empty( $this->options['ngfb_enable_shortcode'] ) ) {
					remove_shortcode( NGFB_SHORTNAME );
					$this->debug->push( NGFB_SHORTNAME . ' shortcode removed' );
				}

				$this->debug->push( 'calling apply_filters()' );
				$content = apply_filters( 'the_content', $content );

				// cleanup for NGG album shortcode
				unset ( $GLOBALS['subalbum'] );
				unset ( $GLOBALS['nggShowGallery'] );

				if ( ! empty( $filter_removed ) ) {
					add_filter( 'the_content', array( &$this->social, 'add_content' ), NGFB_CONTENT_PRIORITY );
					$this->debug->push( 'this->social->add_content() filter re-added' );
				}

				if ( ! empty( $this->options['ngfb_enable_shortcode'] ) ) {
					add_shortcode( NGFB_SHORTNAME, array( &$this->shortcodes, 'ngfb_shortcode' ) );
					$this->debug->push( NGFB_SHORTNAME . ' shortcode re-added' );
				}
			}
			$content = preg_replace( '/<a +rel="author" +href="" +style="display:none;">Google\+<\/a>/', ' ', $content );
			$content = preg_replace( '/[\r\n\t ]+/s', ' ', $content );	// put everything on one line
			$content = str_replace( ']]>', ']]&gt;', $content );
			$content_strlen_after = strlen( $content );
			$this->debug->push( 'content strlen() before = ' . $content_strlen_before . ', after = ' . $content_strlen_after );

			wp_cache_set( $cache_id, $content, __METHOD__, $this->cache->object_expire );
			$this->debug->push( $cache_type . ': filtered content saved to wp_cache for id "' . $cache_id . '" (' . $this->cache->object_expire . ' seconds)');

			return $content;
		}

		public function get_attachment_image_src( $pid, $size_name = 'thumbnail' ) {
			$image_url = '';
			$size_info = $this->get_size_info( $size_name );
			$cropped = ( $size_info['crop'] == 1 ? 'true' : 'false' );
			list( $image_url, $size_info['width'], $size_info['height'] ) = wp_get_attachment_image_src( $pid, $size_name );
			$this->debug->push( 'image for pid:' . $pid . ' size:' . $size_name . ' = ' . 
				$image_url . ' (' . $size_info['width'] . ' x ' . $size_info['height'] . ')' );
			$image_url = $this->util->fix_relative_url( $image_url );
			if ( $this->util->is_uniq_url( $image_url ) )
				return array( $image_url, $size_info['width'], $size_info['height'], $cropped );
			else return array( null, null, null, null );
		}

		// called to get an image URL from an NGG picture ID and a media size name (the pid must be formatted as 'ngg-#')
		public function get_ngg_image_src( $pid, $size_name = 'thumbnail' ) {

			if ( $this->is_avail['ngg'] != true ) return;

			$image_url = '';
			$size_info = array( 'width' => '', 'height' => '', 'crop' => '' );
			$cropped = '';

			if ( is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' ) {
				global $nggdb;
				$pid = substr( $pid, 4 );
				$image = $nggdb->find_image( $pid );	// returns an nggImage object
				if ( ! empty( $image ) ) {
					$size_info = $this->get_size_info( $size_name );
					$crop = ( $size_info['crop'] == 1 ? 'crop' : '' );
					$cropped = ( $size_info['crop'] == 1 ? 'true' : 'false' );
					$image_url = $image->cached_singlepic_file( $size_info['width'], $size_info['height'], $crop ); 
					
					if ( empty( $image_url ) )	// if the image file doesn't exist, use the dynamic image url
						$image_url = trailingslashit( site_url() ) . 
							'index.php?callback=image&amp;pid=' . $pid .
							'&amp;width=' . $size_info['width'] . 
							'&amp;height=' . $size_info['height'] . 
							'&amp;mode=' . $crop;
					else {
						// get the REAL image width and height
						$cachename = $image->pid . '_' . $crop . '_'. $size_info['width'] . 'x' . $size_info['height'] . '_' . $image->filename;
						$cachefolder = WINABSPATH . $this->ngg_options['gallerypath'] . 'cache/';
						$cached_url = site_url() . '/' . $this->ngg_options['gallerypath'] . 'cache/' . $cachename;
						$cached_file = $cachefolder . $cachename;
						$file_info =  getimagesize( $cached_file );
						if ( ! empty( $file_info[0] ) && ! empty( $file_info[1] ) ) {
							$size_info['width'] = $file_info[0];
							$size_info['height'] = $file_info[1];
						}
					}
				}
			}
			$this->debug->push( 'image for pid:' . $pid . ' size:' . $size_name . ' = ' . 
				$image_url . ' (' . $size_info['width'] . ' x ' . $size_info['height'] . ')' );
			$image_url = $this->util->fix_relative_url( $image_url );
			if ( $this->util->is_uniq_url( $image_url ) )
				return array( $image_url, $size_info['width'], $size_info['height'], $cropped );
			else return array( null, null, null, null );
		}

		public function is_excluded() {
			global $post;
			if ( is_page() && $post->ID && $this->is_avail['expages'] == true && empty( $this->options['buttons_on_ex_pages'] ) ) {
				$excluded_ids = ep_get_excluded_ids();
				$delete_ids = array_unique( $excluded_ids );
				if ( in_array( $post->ID, $delete_ids ) ) return true;
			}
			return false;
		}

		public function get_sharing_url( $strip_query = 'notrack', $url = '', $use_post = false ) {

			// $use_post = false when used for Open Graph meta tags and buttons in widget
			// $use_post = true when buttons are added to individual posts on an index webpage

			if ( ! empty( $url ) ) 
				$url = $this->util->fix_relative_url( $url );
			else {
				global $post;
				$is_nggalbum = false;

				// check for album/gallery query strings and an [nggalbum] shortcode
				if ( is_singular() ) {
					global $wp_query;
					// sanitize query values
					$ngg_album = empty( $wp_query->query['album'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['album'] );
					$ngg_gallery = empty( $wp_query->query['gallery'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['gallery'] );

					if ( ( ! empty( $ngg_album ) || ! empty( $ngg_gallery ) ) && ! empty( $post ) && 
						preg_match( '/\[(nggalbum|album)(| [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*| [^\]]*)\]/im', $post->post_content ) ) {

						$this->debug->push( 'is_singular with nggalbum shortcode and query' );
						$is_nggalbum = true;
						$strip_query = 'notrack';	// keep the album/gallery query values
					}
				}

				// use permalink for singular pages (without nggalbum query info) or posts within a loop (use_post is true)
				if ( ( is_singular() && $is_nggalbum == false ) || ( $use_post && ! empty( $post ) ) ) {
					$url = get_permalink( $post->ID );
					$strip_query = 'none';	// don't modify the permalinks
					//$this->debug->push( 'using permalink url = ' . $url );
				} else {
					$url = empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://';
					$url .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
					//$this->debug->push( 'using php server url = ' . $url );
				}
			}
			switch ( $strip_query ) {
				case 'noquery' :
					if ( strpos( $url, '?' ) !== false ) $url = reset( explode( '?', $url ) );
					break;
				case 'notrack' :
					// strip out tracking query arguments by Facebook, Google, etc.
					$url = preg_replace( '/([\?&])(fb_action_ids|fb_action_types|fb_source|fb_aggregation_id|utm_source|utm_medium|utm_campaign|utm_term|gclid|pk_campaign|pk_kwd)=[^&]*&?/i', '$1', $url );
					break;
				// leave url as-is
				default :
					break;
			}
			return $url;
		}

		public function get_size_info( $size_name = 'thumbnail' ) {

			global $_wp_additional_image_sizes;

			if ( is_integer( $size_name ) ) return;
	
			if ( isset( $_wp_additional_image_sizes[$size_name]['width'] ) )
				$width = intval( $_wp_additional_image_sizes[$size_name]['width'] );
			else $width = get_option( "{$size_name}_size_w" );
		
			if ( isset( $_wp_additional_image_sizes[$size_name]['height'] ) )
				$height = intval( $_wp_additional_image_sizes[$size_name]['height'] );
			else $height = get_option( "{$size_name}_size_h" );
		
			if ( isset( $_wp_additional_image_sizes[$size_name]['crop'] ) )
				$crop = intval( $_wp_additional_image_sizes[$size_name]['crop'] );
			else $crop = get_option( "{$size_name}_crop" );

			return array( 'width' => $width, 'height' => $height, 'crop' => $crop );
		}

	}

        global $ngfb;
	$ngfb = new ngfbPlugin();
}

?>

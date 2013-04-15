<?php
/*
Plugin Name: NextGEN Facebook Open Graph
Plugin URI: http://surniaulula.com/nextgen-facebook-open-graph/
Description: Adds complete Open Graph meta tags for Facebook, Google+, Twitter, LinkedIn, etc., plus optional social sharing buttons in content or widget.
Version: 4.0.3
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

		var $version = '4.0.3';		// for display purposes
		var $opts_version = '21';	// increment when adding/removing $default_options
		var $admin_msgs_inf = array();
		var $admin_msgs_err = array();
		var $minimum_wp_version = '3.0';

		var $social_nice_names = array(
			'facebook' => 'Facebook', 
			'gplus' => 'Google+',
			'twitter' => 'Twitter',
			'linkedin' => 'Linkedin',
			'pinterest' => 'Pinterest',
			'stumbleupon' => 'StumbleUpon',
			'tumblr' => 'Tumblr' );

		var $social_options_prefix = array(
			'facebook' => 'fb', 
			'gplus' => 'gp',
			'twitter' => 'twitter',
			'linkedin' => 'linkedin',
			'pinterest' => 'pin',
			'stumbleupon' => 'stumble',
			'tumblr' => 'tumblr' );

		var $is_avail = array();	// assoc array for function/class/method/etc. checks
		var $options = array();
		var $ngg_options = array();
		var $default_options = array(
			'link_author_field' => 'gplus',
			'link_publisher_url' => '',
			'og_art_section' => '',
			'og_img_size' => 'thumbnail',
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
		var $renamed_options = array(
			'add_meta_desc' => 'inc_description',
			'og_def_img' => 'og_def_img_url',
			'og_def_home' => 'og_def_img_on_index',
			'og_def_on_home' => 'og_def_img_on_index',
			'og_def_on_search' => 'og_def_img_on_search',
			'buttons_on_home' => 'buttons_on_index',
			'buttons_lang' => 'gp_lang',
			'ngfb_cache_hours' => 'ngfb_file_cache_hrs' );

		function __construct() {

			$this->define_constants();	// define constants first for option defaults
			$this->load_dependencies();

			if ( defined( 'NGFB_DEBUG' ) && NGFB_DEBUG )
				echo '<!-- ', NGFB_FULLNAME, ' Plugin Loading -->', "\n";

			$this->plugin_name = basename( dirname( __FILE__ ) ) . '/' . basename( __FILE__ );

			register_activation_hook( __FILE__, array( &$this, 'activate' ) );
			register_uninstall_hook( __FILE__, array( 'ngfbPlugin', 'uninstall' ) );

			add_action( 'init', array( &$this, 'init_plugin' ) );
			add_action( 'admin_init', array( &$this, 'check_wp_version' ) );
			add_action( 'admin_notices', array( &$this, 'show_admin_messages' ) );

			add_filter( 'language_attributes', array( &$this, 'add_og_doctype' ) );
			add_filter( 'wp_head', array( &$this, 'add_header' ), NGFB_HEAD_PRIORITY );
			add_filter( 'wp_head', array( &$this, 'add_open_graph' ), NGFB_OG_PRIORITY );
			add_filter( 'the_content', array( &$this, 'add_content_buttons' ), NGFB_CONTENT_PRIORITY );
			add_filter( 'wp_footer', array( &$this, 'add_footer' ), NGFB_FOOTER_PRIORITY );
			add_filter( 'plugin_action_links', array( &$this, 'plugin_action_links' ), 10, 2 );
			add_filter( 'user_contactmethods', array( &$this, 'user_contactmethods' ), 20, 1 );
		}

		function get_options_url() {
			return get_admin_url( null, 'options-general.php?page=' . NGFB_SHORTNAME );
		}
	
		function init_plugin() {

			// run load_is_avail before load_options to get NGG options (if the plugin is installed)
			$this->load_is_avail();
			$this->load_options();

			// add_action() tests and debug output
			if ( ! is_admin() && $this->debug->on ) {
				echo '<!-- ', NGFB_FULLNAME, ' ', $this->version, ' Plugin Initialized -->', "\n";

				foreach ( array( 'wp_head', 'wp_footer' ) as $action ) {
					foreach ( array( 1, 9999 ) as $prio )
						add_action( $action, create_function( '', 
							"echo '<!-- " . NGFB_ACRONYM . " add_action( \'$action\' ) Priority $prio Test = PASSED -->\n';" ), $prio );
				}
				$defined_constants = get_defined_constants( true );
				$this->debug->show( $this->preg_grep_keys( '/^(NGFB_|WP)/', $defined_constants['user'] ) );
				$this->debug->show( $this->is_avail );
				$this->debug->show( $this->options );
			}

		}

		function define_constants() { 

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
				define( 'NGFB_HEAD_PRIORITY', 10 );

			if ( ! defined( 'NGFB_OG_PRIORITY' ) )
				define( 'NGFB_OG_PRIORITY', 20 );

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

		function load_dependencies() {

			require_once ( dirname ( __FILE__ ) . '/lib/debug.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/cache.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/buttons.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/shortcodes.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/widgets.php' );
			require_once ( dirname ( __FILE__ ) . '/lib/googl.php' );

			if ( is_admin() ) {
				require_once ( dirname ( __FILE__ ) . '/lib/admin.php' );
				$this->admin = new ngfbAdmin();
			}
		}

		function user_contactmethods( $fields = array() ) { 
			foreach ( preg_split( '/ *, */', NGFB_CONTACT_FIELDS ) as $field_list ) {
				$field_name = preg_split( '/ *: */', $field_list );
				$fields[$field_name[0]] = $field_name[1];
			}
			ksort( $fields, SORT_STRING );
			return $fields;
		}

		function check_wp_version() {
			global $wp_version;
			$plugin = plugin_basename( __FILE__ );
			$plugin_data = get_plugin_data( __FILE__, false );
			if ( version_compare( $wp_version, $this->minimum_wp_version, "<" ) ) {
				if( is_plugin_active( $plugin ) ) {
					deactivate_plugins( $plugin );
					wp_die( '"' . $plugin_data['Name'] . '" requires WordPress ' . $this->minimum_wp_version .  ' or higher and has therefore been deactivated. Please upgrade WordPress and try again. Thank you.<br /><br />Back to <a href="' . admin_url() . '">WordPress admin</a>.' );
				}
			}
		}

		// it would be better to use '<head prefix="">' but WP doesn't offer hooks into <head>
		function add_og_doctype( $output ) {
			return $output . ' xmlns:og="http://ogp.me/ns" xmlns:fb="http://ogp.me/ns/fb"';
		}

		// create new default options on plugin activation if ngfb_reset = 1, NGFB_RESET is true,
		// NGFB_OPTIONS_NAME is not an array, or NGFB_OPTIONS_NAME is an empty array
		function activate() {
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
		function uninstall() {
			delete_option( NGFB_OPTIONS_NAME );
		}

		// display a settings link on the main plugins page
		function plugin_action_links( $links, $file ) {
			if ( $file == plugin_basename( __FILE__ ) ) {
				array_push( $links, '<a href="' . $this->get_options_url() . '">' . __( 'Settings' ) . '</a>' );
			}
			return $links;
		}

		function load_is_avail() {
		
			// php v4.0.6+
			$this->is_avail['mbdecnum'] = function_exists( 'mb_decode_numericentity' ) ? 1 : 0;

			// post thumbnail feature is supported by wp theme
			$this->is_avail['postthumb'] = function_exists( 'has_post_thumbnail' ) ? 1 : 0;

			// nextgen gallery plugin
			$this->is_avail['ngg'] = class_exists( 'nggdb' ) && method_exists( 'nggdb', 'find_image' ) ? 1 : 0;

			// cdn linker plugin
			$this->is_avail['cdnlink'] = class_exists( 'CDNLinksRewriterWordpress' ) ? 1 : 0;

			// wikibox plugin
			$this->is_avail['wikibox'] = function_exists( 'wikibox_summary' ) ? 1 : 0;

			// exclude pages plugin
			$this->is_avail['expages'] = function_exists( 'ep_get_excluded_ids' ) ? 1 : 0;

			if ( empty( $this->is_avail['mbdecnum'] ) )
				$this->admin_msgs_err[] = 'The <code><a href="http://php.net/manual/en/function.mb-decode-numericentity.php" target="_blank">mb_decode_numericentity()</a></code> function (available since PHP v4.0.6) is missing. This function is required to decode UTF8 entities. Please update your PHP installation as soon as possible.';
		}

		// get the options, upgrade the option names (if necessary), and validate their values
		function load_options() {

			$this->options = get_option( NGFB_OPTIONS_NAME );

			if ( ! empty( $this->is_avail['ngg'] ) )
				$this->ngg_options = get_option( 'ngg_options' );

			// make sure we have something to work with
			if ( ! empty( $this->options ) && is_array( $this->options ) ) {
				if ( empty( $this->options['ngfb_version'] ) 
					|| $this->options['ngfb_version'] !== $this->opts_version )
					$this->options = $this->upgrade_options( $this->options );
			} else {
				$this->admin_msgs_err[] = 'WordPress returned an error when reading the \'' . NGFB_OPTIONS_NAME . '\' array from the database.<br/>All plugin settings have been returned to their default values, though nothing has been saved yet. Please visit the <a href="' . $this->get_options_url() . '">' . NGFB_FULLNAME . ' settings page</a> to review and save these new settings</a>.';
				$this->debug->show( print_r( get_option( NGFB_OPTIONS_NAME ) ), 'get_option("' . NGFB_OPTIONS_NAME . '")' );
				$this->options = $this->default_options;
			}

			$this->debug = new ngfbDebug();
			$this->buttons = new ngfbButtons();
			$this->cache = new ngfbCache();

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
				$this->admin_msgs_inf[] = 'Debug mode is turned ON. Debugging information is being generated and added to webpages as hidden HTML comments. WP object cache expiration time has been set to ' . $this->cache->object_expire . ' second (instead of ' . $this->options['ngfb_object_cache_exp'] . ' seconds).';
			} else $this->cache->object_expire = $this->options['ngfb_object_cache_exp'];

			if ( ! empty( $this->options['ngfb_enable_shortcode'] ) )
				$this->shortcodes = new ngfbShortCodes();

		}

		function upgrade_options( &$opts = array() ) {

			// make sure we have something to work with
			if ( ! empty( $opts ) && is_array( $opts ) ) {

				$this->admin_msgs_inf[] = 'Option settings from the database have been read and updated in memory. These updates have NOT been saved back to the database. <a href="' . $this->get_options_url() . '">Review and save the new settings to disable this update notice</a>.';
	
				// move old option values to new option names
				foreach ( $this->renamed_options as $old => $new )
					if ( empty( $opts[$new] ) && ! empty( $opts[$old] ) ) {
						$this->admin_msgs_inf[] = 'Renamed \'' . $old . '\' option to \'' . $new . '\' with a value of \'' . $opts[$old] . '\'.';
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
						$this->admin_msgs_inf[] = 'Adding missing \'' . $key . '\' option with the default value of \'' . $def_val . '\'.';
						$opts[$key] = $def_val;
					}
				}

				// sanitize and verify the options - just in case
				$opts = $this->sanitize_options( $opts );

				if ( empty( $opts['ngfb_donated'] ) )
					$this->admin_msgs_inf[] = '<b>' . NGFB_FULLNAME . ' (NGFB) has taken many, many months to develop and fine-tune. Please say thank you by <a href="' . $this->get_options_url() . '">donating</a> and <a href="http://wordpress.org/support/view/plugin-reviews/nextgen-facebook">rating it on wordpress.org</a>.</b>';
			}
			return $opts;
		}

		// sanitize and validate input
		function sanitize_options( &$opts = array() ) {

			// make sure we have something to work with
			if ( ! empty( $opts ) && is_array( $opts ) ) {

				// loop through all the known option keys
				foreach ( $this->default_options as $key => $def_val ) {

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

		function add_header() {
			echo "\n<!-- ", NGFB_LONGNAME, ' Version ', $this->version, " -->\n";
			echo '<!-- About NGFB : ', NGFB_URL, " -->\n";
			echo $this->get_buttons_js( 'header' );
		}

		function add_footer() {
			echo $this->get_buttons_js( 'footer' );
		}

		// add button javascript for enabled buttons in content and widget(s)
		function get_buttons_js( $location = 'footer', $ids = array() ) {

			if ( empty( $ids ) ) {

				// if using the Exclude Pages from Navigation plugin, skip social buttons on those pages
				if ( is_page() && $this->is_excluded() ) return;

				$widget = new ngfbSocialButtonsWidget();
		 		$widget_settings = $widget->get_settings();

				foreach ( $this->social_options_prefix as $id => $opt_prefix ) {

					// check for enabled buttons on settings page
					if ( $this->options[$opt_prefix.'_enable'] 
						&& ( is_singular() || $this->options['buttons_on_index'] ) )
							$ids[] = $id;

					// check for enabled buttons in widget
					foreach ( $widget_settings as $instance ) {
						if ( (int) $instance[$id] )
							$ids[] = $id;
					}
				}
				unset ( $id, $opt_prefix );
			}
			natsort( $ids );
			$ids = array_unique( $ids );
			$this->debug->push( $location . ' ids = ' . implode( ', ', $ids ) );
			$button_html = "<!-- " . NGFB_LONGNAME . " " . $location . " javascript BEGIN -->\n";
			$button_html .= $location == 'header' ? $this->buttons->header_js() : '';

			if ( preg_match( '/^pre/i', $location ) ) $location_check = 'header';
			elseif ( preg_match( '/^post/i', $location ) ) $location_check = 'footer';
			else $location_check = $location;

			if ( ! empty( $ids ) ) {
				foreach ( $ids as $id ) {
					$id = preg_replace( '/[^a-z]/', '', $id );	// sanitize input before eval
					$opt_name = $this->social_options_prefix[$id] . '_js_loc';
					if ( ! empty( $this->options[ $opt_name ] ) && $this->options[ $opt_name ] == $location_check )
						$button_html .= eval( "if ( method_exists( \$this->buttons, '${id}_js' ) ) 
							return \$this->buttons->${id}_js( \$location );" );
				}
			}

			$button_html .= "<!-- " . NGFB_LONGNAME . " " . $location . " javascript END -->\n";
			return $button_html;
		}

		function get_buttons_html( $ids = array(), $atts = array() ) {
			global $post;
			$button_html = '';
			foreach ( $ids as $id ) {
				$id = preg_replace( '/[^a-z]/', '', $id );	// sanitize input before eval
				$this->debug->push( 'calling ' . $id . '_button()' );
				$button_html .= eval( "if ( method_exists( \$this->buttons, '${id}_button' ) ) 
					return \$this->buttons->${id}_button( \$atts );" );
			}
			if ( $button_html )
				$button_html = "<div class=\"" . NGFB_SHORTNAME . "-buttons\">$button_html</div>\n";
			return $button_html;
		}

		function add_open_graph() {

			if ( ( defined( 'DISABLE_NGFB_OPEN_GRAPH' ) && DISABLE_NGFB_OPEN_GRAPH ) 
				|| ( defined( 'NGFB_OPEN_GRAPH_DISABLE' ) && NGFB_OPEN_GRAPH_DISABLE ) ) {

				echo "\n<!-- ", NGFB_LONGNAME, " Open Graph DISABLED -->\n\n";
				return;
			}

			if ( is_search() ) $sharing_url = $this->get_sharing_url( 'notrack' );
			else $sharing_url = $this->get_sharing_url();

			$cache_salt = __METHOD__ . '(sharing_url:' . $sharing_url . ')';
			$cache_id = NGFB_SHORTNAME . '_' . md5( $cache_salt );
			$cache_type = 'object cache';
			$og = get_transient( $cache_id );
			$this->debug->push( $cache_type . ' : og array transient id salt "' . $cache_salt . '"' );

			if ( $og !== false ) {
				$this->debug->push( $cache_type . ' : og array retrieved from transient for id "' . $cache_id . '"' );
				$this->show_meta( $og );
				$this->debug->show();
				return;
			}

			global $post;
			$has_video_image = '';
			$og = array();
			$og['og:url'] = $sharing_url;
			$og['fb:admins'] = $this->options['og_admins'];
			$og['fb:app_id'] = $this->options['og_app_id'];
			$og['og:site_name'] = get_bloginfo( 'name', 'display' );	
			$og['og:title'] = $this->get_title( $this->options['og_title_len'], '...' );
			$og['og:description'] = $this->get_description( $this->options['og_desc_len'], '...' );

			if ( $this->options['og_vid_max'] > 0 ) {
				$this->debug->push( 'calling get_content_videos_og(' . $this->options['og_vid_max'] . ')' );
				$og['og:video'] = $this->get_content_videos_og( $this->options['og_vid_max'] );
				if ( is_array( $og['og:video'] ) ) {
					foreach ( $og['og:video'] as $val ) {
						if ( is_array( $val ) && ! empty( $val['og:image'] ) ) {
							$this->debug->push( 'og:image found in og:video array (no default image required)' );
							$has_video_image = 1;
						}
					}
					unset ( $vid );
				}
			}

			if ( $this->options['og_img_max'] > 0 ) {
				$this->debug->push( 'calling get_all_images_og(' . $this->options['og_img_max'] . ', "' . $this->options['og_img_size'] . '")' );
				$og['og:image'] = $this->get_all_images_og( $this->options['og_img_max'], $this->options['og_img_size'] );

				// if we didn't find any images, then use the default image
				if ( empty( $og['og:image'] ) && empty( $has_video_image ) ) {
					$this->debug->push( 'calling get_default_image_og("' . $this->options['og_img_size'] . '")' );
					$og['og:image'] = array_merge( $og['og:image'], $this->get_default_image_og( $this->options['og_img_size'] ) );
				}
			}

			// any singular page is type 'article'
			if ( is_singular() ) {
				$og['og:type'] = 'article';

				if ( ! empty( $post ) && $post->post_author )
					$og['article:author'] = $this->get_author_url( $post->post_author, 
						$this->options['og_author_field'] );

				elseif ( ! empty( $this->options['og_def_author_id'] ) )
					$og['article:author'] = $this->get_author_url( $this->options['og_def_author_id'], 
						$this->options['og_author_field'] );

			// check for default author info on indexes and searches
			} elseif ( ( ! is_singular() && ! is_search() && ! empty( $this->options['og_def_author_on_index'] ) && ! empty( $this->options['og_def_author_id'] ) )
				|| ( is_search() && ! empty( $this->options['og_def_author_on_search'] ) && ! empty( $this->options['og_def_author_id'] ) ) ) {

				$og['og:type'] = "article";
				$og['article:author'] = $this->get_author_url( $this->options['og_def_author_id'], 
					$this->options['og_author_field'] );

			// default
			} else $og['og:type'] = 'website';

			// if the page is an article, then define the other article meta tags
			if ( $og['og:type'] == 'article' ) {
				$og['article:tag'] = $this->get_tags();
				$og['article:section'] = $this->options['og_art_section'];
				$og['article:modified_time'] = get_the_modified_date('c');
				$og['article:published_time'] = get_the_date('c');
			}
		
			set_transient( $cache_id, $og, $this->cache->object_expire );
			$this->debug->push( $cache_type . ' : og array saved to transient for id "' . $cache_id . '" (' . $this->cache->object_expire . ' seconds)');

			// show debug before printing 
			$this->show_meta( $og );
			$this->debug->show();
			return;
		}

		function add_content_buttons( $content ) {

			// if using the Exclude Pages plugin, skip social buttons on those pages
			if ( is_page() && $this->is_excluded() ) return $content;

			if ( is_singular() || $this->options['buttons_on_index'] ) {
				global $post;
				// we should always have a unique post ID for each content
				$cache_salt = __METHOD__ . '(post:' . $post->ID . ')';
				$cache_id = NGFB_SHORTNAME . '_' . md5( $cache_salt );
				$cache_type = 'object cache';
				$button_html = get_transient( $cache_id );
				$this->debug->push( $cache_type . ' : button_html transient id salt "' . $cache_salt . '"' );

				if ( $button_html !== false ) {
					$this->debug->push( $cache_type . ' : button_html retrieved from transient for id "' . $cache_id . '"' );
				} else {
					$sorted_ids = array();
					foreach ( $this->social_options_prefix as $id => $opt_prefix )
						if ( $this->options[$opt_prefix.'_enable'] )
							$sorted_ids[$this->options[$opt_prefix.'_order'] . '-' . $id] = $id;	// sort by number, then by name
					ksort( $sorted_ids );
	
					$this->debug->push( 'calling get_buttons_html()' );
					$button_html = $this->get_buttons_html( $sorted_ids );
	
					if ( ! empty( $button_html ) ) {
						$button_html = "\n<!-- " . NGFB_LONGNAME . " content buttons BEGIN -->\n" .
							"<div class=\"" . NGFB_SHORTNAME . "-content-buttons\">\n" . $button_html . "</div>\n" .
							"<!-- " . NGFB_LONGNAME . " content buttons END -->\n";

						set_transient( $cache_id, $button_html, $this->cache->object_expire );
						$this->debug->push( $cache_type . ' : button_html saved to transient for id "' . $cache_id . '" (' . $this->cache->object_expire . ' seconds)');
					}
				}
				if ( $this->options['buttons_location'] == "top" )
					$content = $this->debug->get() . $button_html . $content;
				else $content .= $this->debug->get() . $button_html;
			}
			return $content;
		}

		function is_assoc( $arr ) {
			if ( ! is_array( $arr ) ) return 0;
			return is_numeric( implode( array_keys( $arr ) ) ) ? 0 : 1;
		}

		function get_author_url( $author_id, $field_name = 'url' ) {
			switch ( $field_name ) {
				case 'none' :
					break;
				case 'index' :
					$url = get_author_posts_url( $author_id );
					break;
				default :
					$url = get_the_author_meta( $field_name, $author_id );

					// if empty or not a URL, then fallback to the author index page
					if ( $this->options['og_author_fallback'] && ( empty( $url ) || ! preg_match( '/:\/\//', $url ) ) )
						$url = get_author_posts_url( $author_id );

					break;
			}
			return $url;
		}

		function get_size_values( $size_name = 'thumbnail' ) {

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

		function get_quote() {
			global $post;
			if ( empty( $post ) ) return;
			if ( has_excerpt( $post->ID ) ) $page_text = get_the_excerpt( $post->ID );
			else $page_text = $post->post_content;		// fallback to regular content
			// don't run through strip_all_tags() to keep formatting and HTML (if any)
			$page_text = strip_shortcodes( $page_text );	// remove any remaining shortcodes
			$page_text = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/i', ' ', $page_text);
			return $page_text;
		}

		function get_caption( $type = 'title', $length = 300, $use_post = true ) {
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

		function get_title( $textlen = 100, $trailing = '', $use_post = false ) {
			global $post, $page, $paged;
			$title = '';
			$page_num = '';
			$parent_title = '';

			if ( is_category() ) { 

				$this->debug->push( 'is_category() = true' );
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

				$this->debug->push( 'is_singular() = ' . ( is_singular() ? 'true' : 'false' ) );
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

			$title = $this->str_decode( $title );

			if ( ! empty( $this->options['ngfb_filter_title'] ) ) {
				$title = apply_filters( 'the_title', $title );
				$this->debug->push( 'apply_filters() = "' . $title . '"' );
			}

			$title = $this->strip_all_tags( $title );
			$this->debug->push( 'strip_all_tags() = "' . $title . '"' );

			// append the text number after the trailing character string
			if ( $textlen > 0 ) $title = $this->limit_text_length( $title, $textlen, $trailing );

			return $title . $page_num;
		}

		function get_wiki_summary() {
			global $post;
			$desc = '';
			$tag_prefix = $this->options['og_wiki_tag'];
			$tags = wp_get_post_tags( $post->ID, array( 'fields' => 'names') );
			$this->debug->push( 'wp_get_post_tags() = ' . implode( ', ', $tags ) );

			foreach ( $tags as $tag_name ) {
				if ( $tag_prefix ) {
					if ( preg_match( "/^$tag_prefix/", $tag_name ) )
						$tag_name = preg_replace( "/^$tag_prefix/", '', $tag_name );
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

		function get_description( $textlen = 300, $trailing = '', $use_post = false ) {
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
				} elseif ( is_page() && ! empty( $this->options['og_desc_wiki'] ) && ! empty( $this->is_avail['wikibox'] ) ) {

					$this->debug->push( 'is_page() && options["og_desc_wiki"] = 1 && is_avail["wikibox"] = 1' );
					$desc = $this->get_wiki_summary();
				} 
		
				if ( empty( $desc ) ) {
					$this->debug->push( 'calling get_filtered_content()' );
					$desc = $this->get_filtered_content( $this->options['ngfb_filter_content'] );
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

			$desc = $this->strip_all_tags( $desc );

			if ( $textlen > 0 ) 
				$desc = $this->limit_text_length( $desc, $textlen, '...' );

			return $desc;
		}

		function get_content_videos_og( $num = 0 ) {
			global $post;
			$og_ret = array();
			$this->debug->push( 'calling get_filtered_content()' );
			$content = $this->get_filtered_content( $this->options['ngfb_filter_content'] );

			if ( preg_match_all( '/<(iframe|embed)[^>]*? src=[\'"]([^\'"]+\/(embed|video)\/[^\'"]+)[\'"][^>]*>/i', $content, $match_all, PREG_SET_ORDER ) ) {
				foreach ( $match_all as $media ) {
					$this->debug->push( 'media found = tag:' . $media[1] . ' src:' . $media[2] );
					$og_video = array(
						'og:image' => '',
						'og:video' => $this->get_sharing_url( 'noquery', $media[2] ),
						'og:video:width' => '',
						'og:video:height' => '',
						'og:video:type' => 'application/x-shockwave-flash'
					);
					if ( $og_video['og:video'] ) {

						// set the height and width based on the iframe/embed attributes
						if ( preg_match( '/ width=[\'"]?([0-9]+)[\'"]?/i', $media[0], $match) ) $og_video['og:video:width'] = $match[1];
						if ( preg_match( '/ height=[\'"]?([0-9]+)[\'"]?/i', $media[0], $match) ) $og_video['og:video:height'] = $match[1];

						// fix URLs and define video images for known websites (youtube, vimeo, etc.)
						if ( preg_match( '/^.*(youtube|youtube-nocookie)\.com\/.*\/([^\/\?\&]+)$/i', $og_video['og:video'], $match ) ) {

							$og_video['og:video'] = 'http://www.youtube.com/v/'.$match[2];
							$og_video['og:image'] = 'http://img.youtube.com/vi/'.$match[2].'/0.jpg';

						} elseif ( preg_match( '/^.*(vimeo)\.com\/.*\/([^\/\?\&]+)$/i', $og_video['og:video'], $match ) ) {

							$api_url = "http://vimeo.com/api/v2/video/$match[2].php";
							$this->debug->push( 'fetching video details from ' . $api_url );
							$hash = unserialize( $this->cache->get( $api_url, 'raw', 'transient' ) );

							if ( ! empty( $hash ) ) {
								$this->debug->push( 'setting og:video and og:image from Vimeo API hash' );
								$og_video['og:video'] = $hash[0]['url'];
								$og_video['og:image'] = $hash[0]['thumbnail_large'];
							}
						}
						array_push( $og_ret, $og_video );
						$this->debug->push( 'image info = ' . $og_video['og:image'] );
						$this->debug->push( 'video info = ' . $og_video['og:video'] . ' (width:' . $og_video['og:video:width'] . 
							' height:' . $og_video['og:video:height'] . ')' );
					}
				}
			}
			if ( $num > 0 ) $og_ret = array_slice( $og_ret, 0, $num );
			return $og_ret;
		}

		function get_all_images_og( $num = 0, $size_name = 'thumbnail' ) {
			global $post;
			$og_ret = array();

			if ( is_attachment( $post->ID ) ) {
				$this->debug->push( 'is_attachment() = true' );
				$og_image = array();
				list( 
					$og_image['og:image'], 
					$og_image['og:image:width'], 
					$og_image['og:image:height']
				) = wp_get_attachment_image_src( $post->ID, $size_name );

				$this->debug->push( 'wp_get_attachment_image_src(' . $post->ID . ',"' . $size_name . '") = ' . 
					$og_image['og:image'] .  ' (' . $og_image['og:image:width'] . ' x ' . $og_image['og:image:height'] . ')' );

				// if this is an attachment webpage, and we have an attachment, 
				// then stop here and return the image array
				if ( ! empty( $og_image['og:image'] ) ) {
					array_push( $og_ret, $og_image );	// everything ok, so push the image
					return $og_ret;
				};
			}

			// check for index-type pages with option enabled to force a default image
			if ( ( ! is_singular() && ! is_search() && ! empty( $this->options['og_def_img_on_index'] ) )
				|| ( is_search() && ! empty( $this->options['og_def_img_on_search'] ) ) ) {
					$this->debug->push( 'is_singular() = ' . is_singular() );
					$this->debug->push( 'is_search() = ' . is_search() );
					$this->debug->push( 'calling get_default_image_og("' . $size_name . '")' );

					$og_ret = array_merge( $og_ret, $this->get_default_image_og( $size_name ) );
					return $og_ret;	// stop here and return the image array
			}

			// check for featured or attaches image(s)
			if ( ! empty( $post ) ) {
				$this->debug->push( 'calling get_featured_og(' . $post->ID . ', "' . $size_name . '")' );
				$og_ret = array_merge( $og_ret, $this->get_featured_og( $post->ID, $size_name ) );

				// if we haven't reached the limit of images yet, keep going
				if ( $num == 0 || count( $og_ret ) < $num ) {
					$this->debug->push( 'calling get_attached_images_og(' . $post->ID . ', "' . $size_name . '")' );
					$og_ret = array_merge( $og_ret, $this->get_attached_images_og( $post->ID, $size_name ) );
				}
			}

			// if we haven't reached the limit of images yet, keep going
			if ( $num == 0 || count( $og_ret ) < $num ) {
				$this->debug->push( 'calling get_content_images_og(' . $num . ', "' . $size_name . '")' );
				$og_ret = array_merge( $og_ret, $this->get_content_images_og( $num, $size_name ) );
			}

			// if we have a limit, and we're over, then slice the array
			if ( $num > 0 && count( $og_ret ) > $num ) {
				$this->debug->push( 'slicing array from ' . count( $og_ret ) . ' to ' . $num . ' elements' );
				$og_ret = array_slice( $og_ret, 0, $num );
			}

			return $og_ret;
		}

		function get_content_images_og( $num = 0, $size_name = 'thumbnail' ) {
			global $post;
			$found = array();
			$og_ret = array();
			$size_info = $this->get_size_values( $size_name );

			// check for singlepics before calling get_filtered_content()
			if ( ! empty( $post ) && preg_match_all( '/\[singlepic[^\]]+id=([0-9]+)/i', $post->post_content, $match, PREG_SET_ORDER ) ) {
				$this->debug->push( '[singlepic] shortcode(s) found' );
				foreach ( $match as $singlepic ) {
					$og_image = array();
					$pid = $singlepic[1];
					list( 
						$og_image['og:image'], 
						$og_image['og:image:width'], 
						$og_image['og:image:height'], 
						$og_image['og:image:cropped'] 
					) = $this->get_ngg_image_src( 'ngg-' . $pid, $size_name );

					$this->debug->push( 'get_ngg_image_src(' . $pid . ') = ' .  $og_image['og:image'] );

					if ( ! empty( $og_image['og:image'] ) && empty( $found[$og_image['og:image']] ) ) {
						$found[$og_image['og:image']] = 1;
						array_push( $og_ret, $og_image );
						// stop and slice here if we have enough images
						if ( $num > 0 && count( $og_ret ) >= $num ) {
							$this->debug->push( 'max images reached ( ' . count( $og_ret ) . ' >= ' . $num . ' )' );
							return array_slice( $og_ret, 0, $num );
						}
					}
				}
			} else $this->debug->push( 'no [singlepic] shortcode found' );

			$this->debug->push( 'calling get_filtered_content()' );
			$content = $this->get_filtered_content( $this->options['ngfb_filter_content'] );
			if ( empty( $content ) ) { 
				$this->debug->push( 'exiting early for empty content' ); 
				return $og_ret;
			}

			// check for NGG image ids
			if ( preg_match_all( '/<div[^>]*? id=[\'"]ngg-image-([0-9]+)[\'"][^>]*>/is', 
				$content, $match, PREG_SET_ORDER ) ) {
				$this->debug->push( '<div id="ngg-image-#"> html tag(s) found' );
				foreach ( $match as $pid ) {
					$og_image = array();
					list( 
						$og_image['og:image'], 
						$og_image['og:image:width'], 
						$og_image['og:image:height'],
						$og_image['og:image:cropped'] 
					) = $this->get_ngg_image_src( 'ngg-' . $pid[1], $size_name );

					$this->debug->push( 'get_ngg_image_src(ngg-' . $pid[1] . ') = ' . $og_image['og:image'] );

					// avoid duplicates
					if ( ! empty( $og_image['og:image'] ) && empty( $found[$og_image['og:image']] ) ) {
						$found[$og_image['og:image']] = 1;
						array_push( $og_ret, $og_image );	// everything ok, so push the image
						// stop and slice here if we have enough images
						if ( $num > 0 && count( $og_ret ) >= $num ) {
							$this->debug->push( 'max images reached ( ' . count( $og_ret ) . ' >= ' . $num . ' )' );
							return array_slice( $og_ret, 0, $num );
						}
					}
				}
			} else $this->debug->push( 'no <div id="ngg-image-#"> html tag found' );

			// img attributes in order of preference
			if ( preg_match_all( '/<img[^>]*? (share-'.$size_name.'|share|src)=[\'"]([^\'"]+)[\'"][^>]*>/is', 
				$content, $match, PREG_SET_ORDER ) ) {
				$this->debug->push( '<img src=""> html tag(s) found' );
				foreach ( $match as $img ) {
					$src_name = $img[1];
					$og_image = array(
						'og:image' => $img[2],
						'og:image:width' => '',
						'og:image:height' => ''
					);
					// check for NGG image pids
					if ( preg_match( '/\/cache\/([0-9]+)_(crop)?_[0-9]+x[0-9]+_[^\/]+$/', $og_image['og:image'], $match) ) {
						$this->debug->push( $src_name . ' ngg cache image = ' . $og_image['og:image'] );
						list( 
							$og_image['og:image'], 
							$og_image['og:image:width'], 
							$og_image['og:image:height'],
							$og_image['og:image:cropped'] 
						) = $this->get_ngg_image_src( 'ngg-' . $match[1], $size_name );

						$this->debug->push( 'get_ngg_image_src(\'ngg-' . $match[1] . '\') = ' . $og_image['og:image'] );
					} else {
						if ( preg_match( '/ width=[\'"]?([0-9]+)[\'"]?/i', $img[0], $match) ) 
							$og_image['og:image:width'] = $match[1];
						if ( preg_match( '/ height=[\'"]?([0-9]+)[\'"]?/i', $img[0], $match) ) 
							$og_image['og:image:height'] = $match[1];
					}

					$this->debug->push( $src_name . ' = ' . $og_image['og:image'] . 
						' (' . $og_image['og:image:width'] . ' x ' . $og_image['og:image:height'] . ')' );

					if ( ! is_numeric( $og_image['og:image:width'] ) ) $og_image['og:image:width'] = 0;
					if ( ! is_numeric( $og_image['og:image:height'] ) ) $og_image['og:image:height'] = 0;

					// if we're picking up an img from 'src', make sure it's width and height is large enough
					if ( $src_name == 'share-' . $size_name || $src_name == 'share' 
						|| ( $src_name == 'src' && defined( 'NGFB_MIN_IMG_SIZE_DISABLE' ) && NGFB_MIN_IMG_SIZE_DISABLE ) 
						|| ( $src_name == 'src' && $this->options['ngfb_skip_small_img'] && 
							$og_image['og:image:width'] >= $size_info['width'] && 
							$og_image['og:image:height'] >= $size_info['height'] ) ) {

						// fix relative URLs - just in case
						if ( ! preg_match( '/:\/\//', $og_image['og:image'] ) ) {
							$this->debug->push( 'relative url found = ' . $og_image['og:image'] );

							// if it starts with a slash, just add the site_url() prefix
							if ( preg_match( '/^\//', $og_image['og:image'] ) )
								$og_image['og:image'] = site_url() . $og_image['og:image'];
							else 
								// remove any query string from the current url
								$og_image['og:image'] = trailingslashit( $this->get_sharing_url( 'noquery' ), false ) . $og_image['og:image'];

							$this->debug->push( 'relative url fixed = ' . $og_image['og:image'] );
						}
						if ( empty( $found[$og_image['og:image']] ) ) {
							$found[$og_image['og:image']] = 1;
							array_push( $og_ret, $og_image );	// everything ok, so push the image
							// stop and slice here if we have enough images
							if ( $num > 0 && count( $og_ret ) >= $num ) {
								$this->debug->push( 'max images reached ( ' . count( $og_ret ) . ' >= ' . $num . ' )' );
								return array_slice( $og_ret, 0, $num );
							}
						// check and report duplicates after relative URLs have been fixed
						} else $this->debug->push( $src_name . ' image rejected = already in array' );
					} else $this->debug->push( $src_name . ' image rejected = width and height attributes missing or too small' );
				}
			} else $this->debug->push( 'no <img src=""> html tag found' );

			if ( $num > 0 && count( $og_ret ) > $num ) {
				$this->debug->push( 'slicing array from ' . count( $og_ret ) . ' to ' . $num . ' elements' );
				$og_ret = array_slice( $og_ret, 0, $num );
			}
			return $og_ret;
		}

		function get_attached_images_og( $post_id = '', $size_name = 'thumbnail' ) {
			$og_ret = array();
			$og_image = array();
			if ( ! empty( $post_id ) ) {
				$images = get_children( array( 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image') );
				foreach ( $images as $attachment ) {
					list( 
						$og_image['og:image'], 
						$og_image['og:image:width'], 
						$og_image['og:image:height'] 
					) = wp_get_attachment_image_src( $attachment->ID, $size_name );

					$this->debug->push( 'wp_get_attachment_image_src(' . $attachment->ID . ', "' . 
						$size_name . '") = ' . $og_image['og:image'] );
				}
			}
			// returned array must be two-dimensional
			if ( ! empty( $og_image ) ) array_push( $og_ret, $og_image );
			return $og_ret;
		}

		function get_featured_og( $post_id = '', $size_name = 'thumbnail' ) {
			$og_ret = array();
			$og_image = array();
			if ( ! empty( $post_id ) && ! empty( $this->is_avail['postthumb'] ) && has_post_thumbnail( $post_id ) ) {
				$pid = get_post_thumbnail_id( $post_id );
				if ( is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' ) {
					list( 
						$og_image['og:image'], 
						$og_image['og:image:width'], 
						$og_image['og:image:height'],
						$og_image['og:image:cropped'] 
					) = $this->get_ngg_image_src( $pid, $size_name );

					$this->debug->push( 'get_ngg_image_src(' . $pid . ', "' . 
						$size_name . '") = ' . $og_image['og:image'] );
				} else {
					list( 
						$og_image['og:image'], 
						$og_image['og:image:width'], 
						$og_image['og:image:height'] 
					) = wp_get_attachment_image_src( $pid, $size_name );

					$this->debug->push( 'wp_get_attachment_image_src(' . $pid . ', "' . 
						$size_name . '") = ' . $og_image['og:image'] );
				}
			}
			// returned array must be two-dimensional
			if ( ! empty( $og_image ) ) array_push( $og_ret, $og_image );
			return $og_ret;
		}

		function get_default_image_og( $size_name = 'thumbnail' ) {
			$og_ret = array();
			$og_image = array();
			if ( $this->options['og_def_img_id'] > 0 ) {
				if ($this->options['og_def_img_id_pre'] == 'ngg') {
					$pid = $this->options['og_def_img_id_pre'].'-'.$this->options['og_def_img_id'];
					list(
						$og_image['og:image'], 
						$og_image['og:image:width'], 
						$og_image['og:image:height'], 
						$og_image['og:image:cropped'] 
					) = $this->get_ngg_image_src( $pid, $size_name );

					$this->debug->push( 'get_ngg_image_src(' . $pid . ') = ' .  $og_image['og:image'] );
				} else {
					list( 
						$og_image['og:image'], 
						$og_image['og:image:width'], 
						$og_image['og:image:height'] 
					) = wp_get_attachment_image_src( $this->options['og_def_img_id'], $size_name );

					$this->debug->push( 'wp_get_attachment_image_src(' . 
						$this->options['og_def_img_id'] . ') = ' . $og_image['og:image'] );
				}
			}
			// if still empty, use the default url (if one is defined, empty string otherwise)
			if ( empty( $og_image['og:image'] ) ) {
				$og_image['og:image'] = empty( $this->options['og_def_img_url'] ) ? '' : $this->options['og_def_img_url'];
				$this->debug->push( 'og_def_img_url = ' . $og_image['og:image'] );
			}
			// returned array must be two-dimensional
			if ( ! empty( $og_image ) ) array_push( $og_ret, $og_image );
			return $og_ret;
		}

		function get_tags() {
			$tags = array();
			if ( is_singular() ) {
				global $post;
				$tags = array_merge( $tags, $this->get_wp_tags( $post->ID ) );
				if ( $this->options['og_ngg_tags'] && ! empty( $this->is_avail['postthumb'] ) && has_post_thumbnail( $post->ID ) ) {
					$pid = get_post_thumbnail_id( $post->ID );
					if ( is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' )
						$tags = array_merge( $tags, $this->get_ngg_tags( $pid ) );
				}
			} elseif ( is_search() )
				$tags = preg_split( '/ *, */', get_search_query( false ) );
		
			return array_unique( array_map( 'strtolower', $tags ) );	// filter for duplicate (lowercase) element values - just in case
		}

		function get_wp_tags( $post_id ) {
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

		function get_ngg_tags( $pid ) {
			$tags = array();
			if ( ! empty( $this->is_avail['ngg'] )
				&& is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' ) {
				$tags = wp_get_object_terms( substr( $pid, 4 ), 'ngg_tag', 'fields=names' );
			}
			return array_map( 'strtolower', $tags );
		}

		function show_meta( &$arr = array() ) {
			global $post;
			$author_url = '';
		
			echo "\n<!-- ", NGFB_LONGNAME, " meta tags BEGIN -->\n";

			// show the array structure before the html block
			$this->debug->show( print_r( $arr, true ) );

			// echo the publisher link
			if ( ! empty( $arr['link:publisher'] ) )
				echo '<link rel="publisher" href="', $arr['link:publisher'], '" />', "\n";
			elseif ( $this->options['link_publisher_url'] )
				echo '<link rel="publisher" href="', $this->options['link_publisher_url'], '" />', "\n";

			// echo the author link
			if ( ! empty( $arr['link:author'] ) ) {
				echo '<link rel="author" href="', $arr['link:author'], '" />', "\n";
			} else {
				if ( ! empty( $post ) && $post->post_author )
					$author_url = $this->get_author_url( $post->post_author, 
						$this->options['link_author_field'] );

				elseif ( ! empty( $this->options['og_def_author_id'] ) )
					$author_url = $this->get_author_url( $this->options['og_def_author_id'], 
						$this->options['link_author_field'] );

				if ( $author_url ) echo '<link rel="author" href="', $author_url, '" />', "\n";
			}

			// echo the description meta
			if ( ! empty( $arr['og:description'] ) && ! empty( $this->options['inc_description'] ) )
				echo '<meta name="description" content="', $arr['og:description'], '" />', "\n";

			// echo the open graph multi-dimensional array
			ksort( $arr );
			foreach ( $arr as $d_name => $d_val ) {						// first-dimension array (associative)
				if ( is_array( $d_val ) ) {
					foreach ( $d_val as $dd_num => $dd_val ) {			// second-dimension array
						if ( $this->is_assoc( $dd_val ) ) {
							ksort( $dd_val );
							foreach ( $dd_val as $ddd_name => $ddd_val ) {	// third-dimension array (associative)
								echo $this->get_meta_html( $ddd_name, $ddd_val, $d_name . ':' . ( $dd_num + 1 ) );
							}
							unset ( $ddd_name, $ddd_val );
						} else echo $this->get_meta_html( $d_name, $dd_val, $d_name . ':' . ( $dd_num + 1 ) );
					}
					unset ( $dd_num, $dd_val );
				} else echo $this->get_meta_html( $d_name, $d_val );
			}
			unset ( $d_name, $d_val );

			echo "<!-- ", NGFB_LONGNAME, " meta tags END -->\n";
		}

		function get_meta_html( $name, $val = '', $cmt = '' ) {
			$meta_html = '';
			if ( ! empty( $this->options['inc_'.$name] ) 
				&& ( ! empty( $val ) || ( ! empty( $this->options['og_empty_tags'] ) && preg_match( '/^og:/', $name ) ) ) ) {

				$charset = get_bloginfo( 'charset' );
				$val = htmlentities( $this->strip_all_tags( $this->str_decode( $val ) ), 
					ENT_QUOTES, $charset, false );
				if ( $cmt ) $meta_html .= "<!-- $cmt -->";
				$meta_html .= '<meta property="' . $name . '" content="' . $val . '" />';
				$meta_html .= "\n";
			}
			return $meta_html;
		}

		function get_filtered_content( $filter_content = true ) {
			global $post;
			if ( empty( $post ) ) return;
			$this->debug->push( 'using content from post ID ' . $post->ID );
			$cache_salt = __METHOD__ . '(post:' . $post->ID . ( $filter_content  ? '_filtered' : '_unfiltered' ) . ')';
			$cache_id = NGFB_SHORTNAME . '_' . md5( $cache_salt );
			$cache_type = 'object cache';
			$content = wp_cache_get( $cache_id, __METHOD__ );
			$this->debug->push( $cache_type . ' : filtered content wp_cache id salt "' . $cache_salt . '"' );

			if ( $content !== false ) {
				$this->debug->push( $cache_type . ' : filtered content retrieved from wp_cache for id "' . $cache_id . '"' );
				return $content;
			} 
			$content = $post->post_content;
			$content_strlen_before = strlen( $content );

			// remove singlepics, which we parse before-hand in get_content_images_og()
			$content = preg_replace( '/\[singlepic[^\]]+\]/', '', $content, -1, $count );
			if ( $count > 0 ) $this->debug->push( '[singlepic] shortcode removed from content' );

			if ( $filter_content == true ) {

				// temporarily remove add_content_buttons() to prevent recursion
				$filter_removed = remove_filter( 'the_content', 
					array( &$this, 'add_content_buttons' ), NGFB_CONTENT_PRIORITY );
				$this->debug->push( 'add_content_buttons() filter removed = ' . ( $filter_removed  ? 'true' : 'false' ) );

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
					add_filter( 'the_content', 
						array( &$this, 'add_content_buttons' ), NGFB_CONTENT_PRIORITY );
					$this->debug->push( 'add_content_buttons() filter re-added' );
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
			$this->debug->push( $cache_type . ' : filtered content saved to wp_cache for id "' . $cache_id . '" (' . $this->cache->object_expire . ' seconds)');

			return $content;
		}

		// called to get an image URL from an NGG picture ID and a media size name (the pid must be formatted as 'ngg-#')
		function get_ngg_image_src( $pid, $size_name = 'thumbnail' ) {

			if ( empty( $this->is_avail['ngg'] ) ) return;

			$cropped = '';
			$image_url = '';
			$size_info = array( 'width' => '', 'height' => '', 'crop' => '' );

			if ( is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' ) {
				global $nggdb;
				$pid = substr( $pid, 4 );
				$image = $nggdb->find_image( $pid );	// returns an nggImage object
				if ( ! empty( $image ) ) {
					$size_info = $this->get_size_values( $size_name );
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
						$cachefolder = WINABSPATH .$this->ngg_options['gallerypath'] . 'cache/';
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
			return array( $image_url, $size_info['width'], $size_info['height'], $cropped );
		}

		// $ngg_images = array of ngg image objects
		function get_ngg_images_og( $ngg_images = array(), $size_name = 'thumbnail' ) {
			$og_ret = array();
			foreach ( $ngg_images as $image ) {
				$og_image = array();
				list( 
					$og_image['og:image'], 
					$og_image['og:image:width'], 
					$og_image['og:image:height'], 
					$og_image['og:image:cropped'] 
				) = $this->get_ngg_image_src( 'ngg-' . $image->pid, $size_name );

				$this->debug->push( 'get_ngg_image_src(' . $image->pid . ') = '.$og_image['og:image'] );
				if ( $og_image['og:image'] ) array_push( $og_ret, $og_image );
			}
			return $og_ret;
		}

		function cdn_linker_rewrite( $url = '' ) {
			if ( ! empty( $this->is_avail['cdnlink'] ) ) {
				$rewriter = new CDNLinksRewriterWordpress();
				$url = '"'.$url.'"';	// rewrite function uses var reference, so pad here first
				$url = trim( $rewriter->rewrite( $url ), "\"" );
			}
			return $url;
		}

		function is_excluded() {
			global $post;
			if ( is_page() 
				&& $post->ID 
				&& ! empty( $this->is_avail['expages'] ) 
				&& empty( $this->options['buttons_on_ex_pages'] ) ) {

				$excluded_ids = ep_get_excluded_ids();
				$delete_ids = array_unique( $excluded_ids );
				if ( in_array( $post->ID, $delete_ids ) ) return true;
			}
			return false;
		}

		function strip_all_tags( $text ) {
			$text = strip_shortcodes( $text );					// remove any remaining shortcodes
			$text = preg_replace( '/<\?.*\?>/i', ' ', $text);			// remove php
			$text = preg_replace( '/<script\b[^>]*>(.*?)<\/script>/i', ' ', $text);	// remove javascript
			$text = strip_tags( $text );						// remove html tags
			return trim( $text );
		}

		function limit_text_length( $text, $textlen = 300, $trailing = '' ) {
			$text = preg_replace( '/<\/p>/i', ' ', $text);				// replace end of paragraph with a space
			$text = preg_replace( '/[\r\n\t ]+/s', ' ', $text );			// put everything on one line
			$text = $this->strip_all_tags( $text );					// remove any remaining html tags
			if ( strlen( $trailing ) > $textlen )
				$trailing = substr( $text, 0, $textlen );			// trim the trailing string, if too long
			if ( strlen( $text ) > $textlen ) {
				$text = substr( $text, 0, $textlen - strlen( $trailing ) );
				$text = trim( preg_replace( '/[^ ]*$/', '', $text ) );		// remove trailing bits of words
				$text = preg_replace( '/[,\.]*$/', '', $text );			// remove trailing puntuation
			} else $trailing = '';							// truncate trailing string if text is shorter than limit
			$text = esc_attr( $text ) . $trailing;					// trim and add trailing string (if provided)
			return $text;
		}

		function str_decode( $str ) {
			// if we don't have something to decode, return immediately
			if ( strpos( $str, '&#' ) === false ) return $str;

			// convert certain entities manually to something non-standard
			$str = preg_replace( '/&#8230;/', '...', $str );

			// if mb_decode_numericentity is not available, return the string un-converted
			if ( empty( $this->is_avail['mbdecnum'] ) ) return $str;

			return preg_replace( '/&#\d{2,5};/ue', 'ngfbPlugin::utf8_entity_decode( \'$0\' )', $str );
		}

		function utf8_entity_decode( $entity ) {
			$convmap = array( 0x0, 0x10000, 0, 0xfffff );
			return mb_decode_numericentity( $entity, $convmap, 'UTF-8' );
		}


		function show_admin_messages() {

			$p_start = '<p style="padding:0;margin:5px;"><a href="' . $this->get_options_url() . '">' . NGFB_ACRONYM . '</a>';
			$p_end = '</p>';

			if ( ! empty( $this->admin_msgs_err ) ) 
				echo '<div id="message" class="error">';

			// warnings and errors
			foreach ( $this->admin_msgs_err as $msg )
				echo $p_start, ' Warning : ', $msg, $p_end;

			if ( ! empty( $this->admin_msgs_err ) ) echo '</div>';

			// notices and informational
			if ( ! empty( $this->admin_msgs_inf ) ) 
				echo '<div id="message" class="updated fade">';

			foreach ( $this->admin_msgs_inf as $msg )
				echo $p_start, ' Notice : ', $msg, $p_end;

			if ( ! empty( $this->admin_msgs_inf ) ) echo '</div>';
		}

		function preg_grep_keys( $pattern, $input, $flags = 0 ) {
			$keys = preg_grep( $pattern, array_keys( $input ), $flags );
			$vals = array();
			foreach ( $keys as $key ) $vals[$key] = $input[$key]; 
			return $vals;
		}

		function get_sharing_url( $strip_query = 'noquery', $url = '', $use_post = false ) {
			// $use_post = false; when used for Open Graph meta tags and buttons in widget
			// $use_post = true; when buttons are added to individual posts on an index webpage
			if ( empty( $url ) ) {
				global $post;
				if ( is_singular() || ( ! empty( $post ) && $use_post ) ) {
					$url = get_permalink( $post->ID );
					$strip_query = 'none';	// don't modify the permalinks
				} else {
					$url = empty( $_SERVER['HTTPS'] ) ? 'http://' : 'https://';
					$url .= $_SERVER["SERVER_NAME"] .  $_SERVER["REQUEST_URI"];
				}
			}
			switch ( $strip_query ) {
				case 'noquery' :
					if ( strpos( $url, '?' ) !== false ) 
						$url = reset( explode( '?', $url ) );
					break;
				case 'notrack' :
					// strip out tracking query arguments by Facebook, Google, etc.
					$url = preg_replace( '/([\?&])(fb_action_ids|fb_action_types|fb_source|fb_aggregation_id|utm_source|utm_medium|utm_campaign|utm_term|gclid|pk_campaign|pk_kwd)=[^&]*&?/i', '$1', $url );
					break;
			}
			return $url;
		}

	}

        global $ngfb;
	$ngfb = new ngfbPlugin();
}

/* You can enable social buttons in the content, use the social buttons widget,
 * and call the ngfb_get_social_buttons() function from your template(s) -- all
 * at the same time -- but all social buttons share the same settings from the
 * admin options page (the layout of each can differ by using the available CSS
 * class names - see the Other Notes tab at
 * http://wordpress.org/extend/plugins/nextgen-facebook/other_notes/ for
 * additional information).
 */
if ( ! function_exists( 'ngfb_get_social_buttons' ) ) {
	function ngfb_get_social_buttons( $ids = array(), $atts = array() ) {
		global $ngfb;
		$cache_salt = __METHOD__ . '(url:' . $ngfb->get_sharing_url( 'notrack' ) . '_ids:' . ( implode( '_', $ids ) ) . '_atts:' . ( implode( '_', $atts ) ) . ')';
		$cache_id = 'ngfb_' . md5( $cache_salt );
		$cache_type = 'object cache';
		$ngfb->debug->push( $cache_type . ' : social buttons transient id salt "' . $cache_salt . '"' );
		$button_html = get_transient( $cache_id );

		if ( $button_html !== false ) {
			$ngfb->debug->push( $cache_type . ' : button_html retrieved from transient for id "' . $cache_id . '"' );
		} else {
			$button_html = "\n<!-- " . NGFB_LONGNAME . " social buttons BEGIN -->\n" .
				$ngfb->get_buttons_js( 'pre-social-buttons', $ids ) .
				$ngfb->get_buttons_html( $ids, $atts ) .
				$ngfb->get_buttons_js( 'post-social-buttons', $ids ) .
				"<!-- " . NGFB_LONGNAME . " social buttons END -->\n";

			set_transient( $cache_id, $button_html, $ngfb->cache->object_expire );
			$ngfb->debug->push( $cache_type . ' : button_html saved to transient for id "' . $cache_id . '" (' . $ngfb->cache->object_expire . ' seconds)');
		}
		return $ngfb->debug->get() . $button_html;
	}
}

?>

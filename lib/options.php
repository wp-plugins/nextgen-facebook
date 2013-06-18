<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbOptions' ) ) {

	class ngfbOptions {

		public $version = '30';		// increment when adding/removing default options
		public $on_page = 'social';	// the settings page where the last option was modified

		public $defaults = array(
			'link_author_field' => 'gplus',
			'link_publisher_url' => '',
			'og_art_section' => '',
			'og_img_size' => 'medium',
			'og_img_width' => 600,
			'og_img_height' => 600,
			'og_img_crop' => 1,
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
			'og_admins' => '',
			'og_app_id' => '',
			'og_empty_tags' => 0,
			'buttons_on_index' => 0,
			'buttons_location_the_excerpt' => 'bottom',
			'buttons_location_the_content' => 'bottom',
			'buttons_link_css' => 0,
			'buttons_css_data' => '',
			'fb_on_the_excerpt' => 0,
			'fb_on_the_content' => 0,
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
			'gp_on_the_excerpt' => 0,
			'gp_on_the_content' => 0,
			'gp_order' => 2,
			'gp_js_loc' => 'header',
			'gp_lang' => 'en-US',
			'gp_action' => 'plusone',
			'gp_size' => 'medium',
			'gp_annotation' => 'bubble',
			'twitter_on_the_excerpt' => 0,
			'twitter_on_the_content' => 0,
			'twitter_order' => 3,
			'twitter_js_loc' => 'header',
			'twitter_lang' => 'en',
			'twitter_caption' => 'title',
			'twitter_cap_len' => 140,
			'twitter_count' => 'horizontal',
			'twitter_size' => 'medium',
			'twitter_dnt' => 1,
			'twitter_shorten' => 1,
			'linkedin_on_the_excerpt' => 0,
			'linkedin_on_the_content' => 0,
			'linkedin_order' => 4,
			'linkedin_js_loc' => 'header',
			'linkedin_counter' => 'right',
			'linkedin_showzero' => 1,
			'pin_on_the_excerpt' => 0,
			'pin_on_the_content' => 0,
			'pin_order' => 5,
			'pin_js_loc' => 'header',
			'pin_count_layout' => 'horizontal',
			'pin_img_size' => 'large',
			'pin_caption' => 'both',
			'pin_cap_len' => 500,
			'tumblr_on_the_excerpt' => 0,
			'tumblr_on_the_content' => 0,
			'tumblr_order' => 7,
			'tumblr_js_loc' => 'footer',
			'tumblr_button_style' => 'share_1',
			'tumblr_desc_len' => 300,
			'tumblr_photo' => 1,
			'tumblr_img_size' => 'large',
			'tumblr_caption' => 'both',
			'tumblr_cap_len' => 500,
			'stumble_on_the_excerpt' => 0,
			'stumble_on_the_content' => 0,
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
			'ngfb_pro_tid' => '',
			'ngfb_reset' => 0,
			'ngfb_preserve' => 0,
			'ngfb_debug' => 0,
			'ngfb_enable_shortcode' => 0,
			'ngfb_filter_title' => 1,
			'ngfb_filter_excerpt' => 0,
			'ngfb_filter_content' => 1,
			'ngfb_skip_small_img' => 1,
			'ngfb_verify_certs' => 0,
			'ngfb_file_cache_hrs' => 0,
			'ngfb_object_cache_exp' => 60,
			'ngfb_googl_api_key' => '',
			'ngfb_cdn_urls' => '',
			'ngfb_cdn_folders' => 'wp-content, wp-includes',
			'ngfb_cdn_excl' => '',
			'ngfb_cdn_not_https' => 1,
			'ngfb_cdn_www_opt' => 1,
		);

		private $renamed = array(
			'add_meta_desc' => 'inc_description',
			'og_def_img' => 'og_def_img_url',
			'og_def_home' => 'og_def_img_on_index',
			'og_def_on_home' => 'og_def_img_on_index',
			'og_def_on_search' => 'og_def_img_on_search',
			'buttons_on_home' => 'buttons_on_index',
			'buttons_lang' => 'gp_lang',
			'ngfb_cache_hours' => 'ngfb_file_cache_hrs',
			'fb_enable' => 'fb_on_the_content', 
			'gp_enable' => 'gp_on_the_content',
			'twitter_enable' => 'twitter_on_the_content',
			'linkedin_enable' => 'linkedin_on_the_content',
			'pin_enable' => 'pin_on_the_content',
			'stumble_enable' => 'stumble_on_the_content',
			'tumblr_enable' => 'tumblr_on_the_content',
			'buttons_location' => 'buttons_location_the_content' );

		private $ngfb;		// ngfbPlugin

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
		}

		public function get_defaults( $idx = '' ) {
			if ( ! empty( $idx ) ) return $this->defaults[$idx];
			else return $this->defaults;
		}

		// sanitize and validate input
		public function sanitize( $opts = array(), $def_opts = array() ) {

			// make sure we have something to work with
			if ( ! empty( $def_opts ) && is_array( $def_opts ) ) {

				// loop through all the known option keys
				foreach ( $def_opts as $key => $def_val ) {

					switch ( $key ) {

						// remove HTML
						case 'og_title' :
						case 'og_desc' :
						case 'og_app_id' :
							$opts[$key] = wp_filter_nohtml_kses( $opts[$key] );
							break;

						// stip off leading URLs (leaving just the account names)
						case 'og_admins' :
							$opts[$key] = preg_replace( '/(http|https):\/\/[^\/]*?\//', '', 
								wp_filter_nohtml_kses( $opts[$key] ) );
							break;

						// must be a URL
						case 'og_img_url' :
						case 'og_def_img_url' :
						case 'link_publisher_url' :
						case 'ngfb_cdn_urls' :
							if ( $opts[$key] && ! preg_match( '/:\/\//', $opts[$key] ) ) 
								$opts[$key] = $def_val;
							break;

						// must be numeric (blank or zero is ok)
						case 'og_desc_len' : 
						case 'og_img_max' :
						case 'og_vid_max' :
						case 'og_img_id' :
						case 'og_def_img_id' :
						case 'og_def_author_id' :
						case 'ngfb_file_cache_hrs' :
							if ( ! empty( $opts[$key] ) && ! is_numeric( $opts[$key] ) )
								$opts[$key] = $def_val;
							break;

						// integer options that must me 1 or more (not zero)
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
						case 'og_art_section' :
						case 'link_author_field' :
						case 'og_img_size' : 
						case 'og_img_id_pre' : 
						case 'og_def_img_id_pre' : 
						case 'og_author_field' :
						case 'buttons_location_the_excerpt' : 
						case 'buttons_location_the_content' : 
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

				if ( array_key_exists( 'og_desc_len', $opts ) && $opts['og_desc_len'] < NGFB_MIN_DESC_LEN ) 
					$opts['og_desc_len'] = NGFB_MIN_DESC_LEN;
	
			}
			return $opts;
		}

		public function upgrade( &$opts = array(), $def_opts = array() ) {

			// make sure we have something to work with
			if ( ! empty( $opts ) && is_array( $opts ) ) {

				// move old option values to new option names
				foreach ( $this->renamed as $old => $new )
					// rename if the old array key exists, but not the new one (we don't want to overwrite current values)
					if ( ! empty( $old ) && ! empty( $new ) && array_key_exists( $old, $opts ) && ! array_key_exists( $new, $opts ) ) {
						if ( $this->ngfb->debug->is_on() == true )
							$this->ngfb->notices->inf( 'Renamed \'' . $old . '\' option to \'' . 
								$new . '\' with a value of \'' . $opts[$old] . '\'.' );
						$opts[$new] = $opts[$old];
						unset( $opts[$old] );
					}
				unset ( $old, $new );
	
				// unset options that no longer exist
				foreach ( $opts as $key => $val )
					// check that the key doesn't exist in the default options (which is a complete list of the current options used)
					if ( ! empty( $key ) && ! array_key_exists( $key, $def_opts ) ) {
						if ( $this->ngfb->debug->is_on() == true )
							$this->ngfb->notices->inf( 'Removing deprecated option \'' . 
								$key . '\' with a value of \'' . $val . '\'.' );
						unset( $opts[$key] );
					}
				unset ( $key, $val );
	
				// add missing options and set to defaults
				foreach ( $def_opts as $key => $def_val ) {
					if ( ! empty( $key ) && ! array_key_exists( $key, $opts ) ) {
						if ( $this->ngfb->debug->is_on() == true )
							$this->ngfb->notices->inf( 'Adding missing \'' . $key . 
								'\' option with the default value of \'' . $def_val . '\'.' );
						$opts[$key] = $def_val;
					}
				}

				// sanitize and verify the options - just in case
				$opts = $this->sanitize( $opts, $def_opts );

				$this->ngfb->notices->inf( 'Plugin settings from the database have been read and updated dynamically --
					<em>the updated settings have not been saved back to the database yet</em>.
					<a href="' . $this->ngfb->util->get_admin_url( $this->on_page ) . '">Please review and save the settings</a> 
					to make these changes permanent.' );
	
				if ( $this->ngfb->is_avail['aop'] == false )
					$this->ngfb->notices->inf( $this->ngfb->msgs['purchase'] );

			}
			return $opts;
		}

	}

}
?>

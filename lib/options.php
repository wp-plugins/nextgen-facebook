<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'ngfbOptions' ) ) {

	class ngfbOptions {

		private $p;

		private $renamed = array(
			'add_meta_desc' => 'inc_description',
			'og_def_img' => 'og_def_img_url',
			'og_def_home' => 'og_def_img_on_index',
			'og_def_on_home' => 'og_def_img_on_index',
			'og_def_on_search' => 'og_def_img_on_search',
			'buttons_on_home' => 'buttons_on_index',
			'buttons_lang' => 'gp_lang',
			'ngfb_cache_hours' => 'plugin_file_cache_hrs',
			'fb_enable' => 'fb_on_the_content', 
			'gp_enable' => 'gp_on_the_content',
			'twitter_enable' => 'twitter_on_the_content',
			'linkedin_enable' => 'linkedin_on_the_content',
			'pin_enable' => 'pin_on_the_content',
			'stumble_enable' => 'stumble_on_the_content',
			'tumblr_enable' => 'tumblr_on_the_content',
			'buttons_location' => 'buttons_location_the_content',
			'og_admins' => 'fb_admins',
			'og_app_id' => 'fb_app_id',
			'ngfb_version' => 'options_version',
			'link_desc_len' => 'meta_desc_len',
			'ngfb_opts_ver' => 'options_version',
			'ngfb_plugin_ver' => 'plugin_version',
			'ngfb_pro_tid' => 'plugin_pro_tid',
			'ngfb_preserve' => 'plugin_preserve',
			'ngfb_reset' => 'plugin_reset',
			'ngfb_debug' => 'plugin_debug',
			'ngfb_enable_shortcode' => 'plugin_shortcode_ngfb',
			'ngfb_skip_small_img' => 'plugin_ignore_small_img',
			'ngfb_filter_content' => 'plugin_filter_content',
			'ngfb_filter_excerpt' => 'plugin_filter_excerpt',
			'ngfb_add_to_post' => 'plugin_add_to_post',
			'ngfb_add_to_page' => 'plugin_add_to_page',
			'ngfb_add_to_attachment' => 'plugin_add_to_attachment',
			'ngfb_verify_certs' => 'plugin_verify_certs',
			'ngfb_file_cache_hrs' => 'plugin_file_cache_hrs',
			'ngfb_object_cache_exp' => 'plugin_object_cache_exp',
			'ngfb_min_shorten' => 'plugin_min_shorten',
			'ngfb_googl_api_key' => 'plugin_googl_api_key',
			'ngfb_bitly_login' => 'plugin_bitly_login',
			'ngfb_bitly_api_key' => 'plugin_bitly_api_key',
			'ngfb_cdn_urls' => 'plugin_cdn_urls',
			'ngfb_cdn_folders' => 'plugin_cdn_folders',
			'ngfb_cdn_excl' => 'plugin_cdn_excl',
			'ngfb_cdn_not_https' => 'plugin_cdn_not_https',
			'ngfb_cdn_www_opt' => 'plugin_cdn_www_opt',
			'ngfb_cm_fb_name' => 'plugin_cm_fb_name', 
			'ngfb_cm_fb_label' => 'plugin_cm_fb_label', 
			'ngfb_cm_fb_enabled' => 'plugin_cm_fb_enabled',
			'ngfb_cm_gp_name' => 'plugin_cm_gp_name', 
			'ngfb_cm_gp_label' => 'plugin_cm_gp_label', 
			'ngfb_cm_gp_enabled' => 'plugin_cm_gp_enabled',
			'ngfb_cm_linkedin_name' => 'plugin_cm_linkedin_name', 
			'ngfb_cm_linkedin_label' => 'plugin_cm_linkedin_label', 
			'ngfb_cm_linkedin_enabled' => 'plugin_cm_linkedin_enabled',
			'ngfb_cm_pin_name' => 'plugin_cm_pin_name', 
			'ngfb_cm_pin_label' => 'plugin_cm_pin_label', 
			'ngfb_cm_pin_enabled' => 'plugin_cm_pin_enabled',
			'ngfb_cm_tumblr_name' => 'plugin_cm_tumblr_name', 
			'ngfb_cm_tumblr_label' => 'plugin_cm_tumblr_label', 
			'ngfb_cm_tumblr_enabled' => 'plugin_cm_tumblr_enabled',
			'ngfb_cm_twitter_name' => 'plugin_cm_twitter_name', 
			'ngfb_cm_twitter_label' => 'plugin_cm_twitter_label', 
			'ngfb_cm_twitter_enabled' => 'plugin_cm_twitter_enabled',
			'ngfb_cm_yt_name' => 'plugin_cm_yt_name', 
			'ngfb_cm_yt_label' => 'plugin_cm_yt_label', 
			'ngfb_cm_yt_enabled' => 'plugin_cm_yt_enabled',
			'ngfb_cm_skype_name' => 'plugin_cm_skype_name', 
			'ngfb_cm_skype_label' => 'plugin_cm_skype_label', 
			'ngfb_cm_skype_enabled' => 'plugin_cm_skype_enabled',
		);

		public $options_version = '96';	// increment when adding/removing default options

		public $admin_sharing = array(
			'fb_button' => 'share',
			'fb_type' => 'button_count',
			'gp_action' => 'share',
			'gp_size' => 'medium',
			'gp_annotation' => 'bubble',
			'gp_expandto' => '',
			'twitter_count' => 'horizontal',
			'twitter_size' => 'medium',
			'linkedin_counter' => 'right',
			'linkedin_showzero' => 1,
			'pin_count_layout' => 'horizontal',
			'tumblr_button_style' => 'share_1',
			'stumble_badge' => 1,
		);

		public $site_defaults = array(
			'options_version' => '',
			'plugin_version' => '',
			'plugin_pro_tid' => '',
			'plugin_pro_tid_use' => 'default',
		);

		public $defaults = array(
			'meta_desc_len' => 156,
			'link_author_field' => '',
			'link_def_author_id' => 0,
			'link_def_author_on_index' => 0,
			'link_def_author_on_search' => 0,
			'link_publisher_url' => '',
			'fb_admins' => '',
			'fb_app_id' => '',
			'og_site_name' => '',
			'og_publisher_url' => '',
			'og_art_section' => '',
			'og_img_width' => 1200,
			'og_img_height' => 630,
			'og_img_crop' => 1,
			'og_img_resize' => 1,
			'og_img_max' => 1,
			'og_vid_max' => 1,
			'og_vid_https' => 1,
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
			'og_author_field' => '',
			'og_author_fallback' => 0,
			'og_title_sep' => '-',
			'og_title_len' => 70,
			'og_desc_len' => 300,
			'og_desc_strip' => 0,
			'og_empty_tags' => 0,
			'buttons_on_index' => 0,
			'buttons_on_front' => 1,
			'buttons_add_to_post' => 1,
			'buttons_add_to_page' => 1,
			'buttons_add_to_attachment' => 1,
			'buttons_location_the_excerpt' => 'bottom',
			'buttons_location_the_content' => 'bottom',
			'buttons_link_css' => 1,
			'buttons_css_excerpt' => '',
			'buttons_css_content' => '',
			'buttons_css_shortcode' => '',
			'buttons_css_social' => '',
			'buttons_css_widget' => '',
			'fb_on_the_excerpt' => 0,
			'fb_on_the_content' => 0,
			'fb_on_admin_sharing' => 1,
			'fb_order' => 1,
			'fb_js_loc' => 'header',
			'fb_lang' => 'en_US',
			'fb_button' => 'like',
			'fb_markup' => 'xfbml',
			'fb_send' => 1,
			'fb_layout' => 'button_count',
			'fb_width' => 200,
			'fb_font' => 'arial',
			'fb_show_faces' => 0,
			'fb_colorscheme' => 'light',
			'fb_action' => 'like',
			'fb_type' => 'button_count',
			'gp_on_the_excerpt' => 0,
			'gp_on_the_content' => 0,
			'gp_on_admin_sharing' => 1,
			'gp_order' => 2,
			'gp_js_loc' => 'header',
			'gp_lang' => 'en-US',
			'gp_action' => 'plusone',
			'gp_size' => 'medium',
			'gp_annotation' => 'bubble',
			'gp_expandto' => '',
			'tc_enable' => 0,
			'tc_site' => '',
			'tc_desc_len' => 200,
			'tc_gal_min' => 4,
			'tc_gal_size' => 'medium',
			'tc_photo_size' => 'large',
			'tc_large_size' => 'medium',
			'tc_sum_size' => 'thumbnail',
			'tc_prod_size' => 'medium',
			'tc_prod_def_l2' => 'Location',
			'tc_prod_def_d2' => 'Unknown',
			'twitter_on_the_excerpt' => 0,
			'twitter_on_the_content' => 0,
			'twitter_on_admin_sharing' => 1,
			'twitter_order' => 3,
			'twitter_js_loc' => 'header',
			'twitter_lang' => 'en',
			'twitter_caption' => 'title',
			'twitter_cap_len' => 140,
			'twitter_count' => 'horizontal',
			'twitter_size' => 'medium',
			'twitter_via' => 1,
			'twitter_rel_author' => 1,
			'twitter_dnt' => 1,
			'twitter_shortener' => '',
			'linkedin_on_the_excerpt' => 0,
			'linkedin_on_the_content' => 0,
			'linkedin_on_admin_sharing' => 1,
			'linkedin_order' => 4,
			'linkedin_js_loc' => 'header',
			'linkedin_counter' => 'right',
			'linkedin_showzero' => 1,
			'pin_on_the_excerpt' => 0,
			'pin_on_the_content' => 0,
			'pin_on_admin_sharing' => 1,
			'pin_order' => 5,
			'pin_js_loc' => 'header',
			'pin_count_layout' => 'horizontal',
			'pin_img_size' => 'large',
			'pin_caption' => 'both',
			'pin_cap_len' => 500,
			'tumblr_on_the_excerpt' => 0,
			'tumblr_on_the_content' => 0,
			'tumblr_on_admin_sharing' => 1,
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
			'stumble_on_admin_sharing' => 1,
			'stumble_order' => 6,
			'stumble_js_loc' => 'header',
			'stumble_badge' => 1,
			'inc_description' => 0,
			'inc_fb:admins' => 1,
			'inc_fb:app_id' => 1,
			'inc_og:locale' => 1,
			'inc_og:site_name' => 1,
			'inc_og:title' => 1,
			'inc_og:type' => 1,
			'inc_og:url' => 1,
			'inc_og:description' => 1,
			'inc_og:image' => 1,
			'inc_og:image:secure_url' => 1,
			'inc_og:image:width' => 1,
			'inc_og:image:height' => 1,
			'inc_og:video' => 1,
			'inc_og:video:secure_url' => 1,
			'inc_og:video:width' => 1,
			'inc_og:video:height' => 1,
			'inc_og:video:type' => 1,
			'inc_article:author' => 1,
			'inc_article:publisher' => 1,
			'inc_article:published_time' => 1,
			'inc_article:modified_time' => 1,
			'inc_article:section' => 1,
			'inc_article:tag' => 1,
			'inc_product:price:amount' => 1,
			'inc_product:price:currency' => 1,
			'inc_product:availability' => 1,
			'inc_twitter:card' => 1,
			'inc_twitter:creator' => 1,
			'inc_twitter:site' => 1,
			'inc_twitter:title' => 1,
			'inc_twitter:description' => 1,
			'inc_twitter:image' => 1,
			'inc_twitter:image:width' => 1,
			'inc_twitter:image:height' => 1,
			'inc_twitter:image0' => 1,
			'inc_twitter:image1' => 1,
			'inc_twitter:image2' => 1,
			'inc_twitter:image3' => 1,
			'inc_twitter:player' => 1,
			'inc_twitter:player:width' => 1,
			'inc_twitter:player:height' => 1,
			'inc_twitter:data1' => 1,
			'inc_twitter:label1' => 1,
			'inc_twitter:data2' => 1,
			'inc_twitter:label2' => 1,
			'inc_twitter:data3' => 1,
			'inc_twitter:label3' => 1,
			'inc_twitter:data4' => 1,
			'inc_twitter:label4' => 1,
			'options_version' => '',
			'plugin_version' => '',
			'plugin_pro_tid' => '',
			'plugin_preserve' => 0,
			'plugin_reset' => 0,
			'plugin_debug' => 0,
			'plugin_shortcode_ngfb' => 0,
			'plugin_ignore_small_img' => 1,
			'plugin_filter_content' => 1,
			'plugin_filter_excerpt' => 0,
			'plugin_add_to_post' => 1,
			'plugin_add_to_page' => 1,
			'plugin_add_to_attachment' => 1,
			'plugin_verify_certs' => 0,
			'plugin_file_cache_hrs' => 0,
			'plugin_object_cache_exp' => 900,
			'plugin_min_shorten' => 21,
			'plugin_googl_api_key' => '',
			'plugin_bitly_login' => '',
			'plugin_bitly_api_key' => '',
			'plugin_cdn_urls' => '',
			'plugin_cdn_folders' => 'wp-content, wp-includes',
			'plugin_cdn_excl' => '',
			'plugin_cdn_not_https' => 1,
			'plugin_cdn_www_opt' => 1,
			'plugin_cm_fb_name' => 'facebook', 
			'plugin_cm_fb_label' => 'Facebook URL', 
			'plugin_cm_fb_enabled' => 1,
			'plugin_cm_gp_name' => 'gplus', 
			'plugin_cm_gp_label' => 'Google+ URL', 
			'plugin_cm_gp_enabled' => 1,
			'plugin_cm_linkedin_name' => 'linkedin', 
			'plugin_cm_linkedin_label' => 'LinkedIn URL', 
			'plugin_cm_linkedin_enabled' => 0,
			'plugin_cm_pin_name' => 'pinterest', 
			'plugin_cm_pin_label' => 'Pinterest URL', 
			'plugin_cm_pin_enabled' => 0,
			'plugin_cm_tumblr_name' => 'tumblr', 
			'plugin_cm_tumblr_label' => 'Tumblr URL', 
			'plugin_cm_tumblr_enabled' => 0,
			'plugin_cm_twitter_name' => 'twitter', 
			'plugin_cm_twitter_label' => 'Twitter @username', 
			'plugin_cm_twitter_enabled' => 1,
			'plugin_cm_yt_name' => 'youtube', 
			'plugin_cm_yt_label' => 'YouTube Channel URL', 
			'plugin_cm_yt_enabled' => 0,
			'plugin_cm_skype_name' => 'skype', 
			'plugin_cm_skype_label' => 'Skype Username', 
			'plugin_cm_skype_enabled' => 0,
			'wp_cm_aim_label' => 'AIM', 
			'wp_cm_aim_enabled' => 1,
			'wp_cm_jabber_label' => 'Jabber / Google Talk', 
			'wp_cm_jabber_enabled' => 1,
			'wp_cm_yim_label' => 'Yahoo IM', 
			'wp_cm_yim_enabled' => 1,
		);

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
		}

		public function get_site_defaults( $idx = '' ) {
			if ( ! empty( $idx ) ) {
				if ( array_key_exists( $idx, $this->site_defaults ) )
					return $this->site_defaults[$idx];
				else return false;
			} else return $this->site_defaults;
		}

		public function get_defaults( $idx = '' ) {

			// css files are only loaded once into defaults
			foreach ( $this->p->cf['css'] as $id => $name ) {
				$css_file = NGFB_PLUGINDIR.'css/'.$id.'-buttons.css';
				if ( empty( $this->defaults['buttons_css_'.$id] ) ) {
					if ( ! $fh = @fopen( $css_file, 'rb' ) )
						$this->p->notices->err( 'Failed to open <u>'.$css_file.'</u> for reading.' );
					else {
						$this->defaults['buttons_css_'.$id] = fread( $fh, filesize( $css_file ) );
						$this->p->debug->log( 'read css from file '.$css_file );
						fclose( $fh );
					}
				}
			}
			unset ( $id, $name );

			$this->defaults = $this->add_to_post_types( $this->defaults );

			$this->defaults['link_author_field'] = empty( $this->p->options['plugin_cm_gp_name'] ) ? 
				$this->defaults['plugin_cm_gp_name'] : $this->p->options['plugin_cm_gp_name'];

			$this->defaults['og_author_field'] = empty( $this->p->options['plugin_cm_fb_name'] ) ? 
				$this->defaults['plugin_cm_fb_name'] : $this->p->options['plugin_cm_fb_name'];

			$this->defaults['og_site_name'] = get_bloginfo( 'name', 'display' );

			// check for default values from network admin settings
			if ( is_array( $this->p->site_options ) ) {
				foreach ( $this->p->site_options as $key => $val ) {
					if ( array_key_exists( $key, $this->defaults ) && 
						array_key_exists( $key.'_use', $this->p->site_options ) ) {

						if ( $this->p->site_options[$key.'_use'] == 'default' )
							$this->defaults[$key] = $this->p->site_options[$key];
					}
				}
			}

			if ( ! empty( $idx ) ) 
				if ( array_key_exists( $idx, $this->defaults ) )
					return $this->defaults[$idx];
				else return false;
			else return $this->defaults;
		}

		public function add_to_post_types( &$opts = array() ) {
			foreach ( array( 'buttons_add_to_', 'plugin_add_to_' ) as $pre ) {
				foreach ( get_post_types( array( 'show_ui' => true, 'public' => true ), 'objects' ) as $post_type ) {
					$key = $pre.$post_type->name;
					if ( ! array_key_exists( $key, $opts ) ) {
						switch ( $post_type->name ) {
							case 'shop_coupon' :
								$opts[$key] = 0;
								break;
							default :
								$opts[$key] = 1;
								break;
						}
					}
				}
			}
			return $opts;
		}

		public function check_options( &$opts = array() ) {
			$opts_err_msg = '';
			if ( ! empty( $opts ) && is_array( $opts ) ) {
				if ( empty( $opts['plugin_version'] ) || $opts['plugin_version'] !== $this->p->cf['version'] ||
					empty( $opts['options_version'] ) || $opts['options_version'] !== $this->options_version ) {

					$this->p->debug->log( 'plugin version different than options version: calling upgrade() method.' );
					$opts = $this->upgrade( $opts, $this->get_defaults() );
				}
				// add support for post types that may have been added
				$opts = $this->add_to_post_types( $opts );
			} else {
				if ( $opts === false )
					$opts_err_msg = 'could not find an entry for '.NGFB_OPTIONS_NAME.' in';
				elseif ( ! is_array( $opts ) )
					$opts_err_msg = 'returned a non-array value when reading '.NGFB_OPTIONS_NAME.' from';
				elseif ( empty( $opts ) )
					$opts_err_msg = 'returned an empty array when reading '.NGFB_OPTIONS_NAME.' from';
				else 
					$opts_err_msg = 'returned an unknown condition when reading '.NGFB_OPTIONS_NAME.' from';

				$this->p->debug->log( 'WordPress '.$opts_err_msg.' the options database table.' );
				$opts = $this->get_defaults();
			}
			if ( is_admin() ) {
				if ( ! empty( $opts_err_msg ) ) {
					$url = $this->p->util->get_admin_url( 'general' );
					$this->p->notices->err( 'WordPress '.$opts_err_msg.' the options table. 
						Plugin settings have been returned to their default values (though nothing has been saved back to the database yet). 
						<a href="'.$url.'">Please review and save the new settings</a>.' );
				}
				if ( $this->p->options['og_img_width'] < $this->p->cf['img']['og_min_width'] || 
					$this->p->options['og_img_height'] < $this->p->cf['img']['og_min_height'] ) {

					$url = $this->p->util->get_admin_url( 'general' );
					$size_desc = $this->p->options['og_img_width'].'x'.$this->p->options['og_img_height'];
					$this->p->notices->inf( 'The image size of '.$size_desc.' for images in the Open Graph meta tags
						is smaller than the minimum of '.$this->p->cf['img']['og_min_width'].'x'.$this->p->cf['img']['og_min_height'].'. 
						<a href="'.$url.'">Please enter a larger image dimensions on the General Settings page</a>.' );
				}
				if ( $this->p->is_avail['aop'] == true && empty( $this->p->options['plugin_pro_tid'] ) )
					$this->p->notices->nag( $this->p->msg->get( 'pro_activate' ) );

			}
			return $opts;
		}

		// sanitize and validate input
		public function sanitize( $opts = array(), $def_opts = array() ) {

			// make sure we have something to work with
			if ( ! empty( $def_opts ) && is_array( $def_opts ) ) {

				// loop through all the known option keys
				foreach ( $def_opts as $key => $def_val ) {

					// remove html, decode entities, and strip slashes
					if ( array_key_exists( $key, $opts ) )
						$opts[$key] = stripslashes( html_entity_decode( wp_filter_nohtml_kses( $opts[$key] ) ) );

					switch ( $key ) {

						// twitter-style usernames
						case 'tc_site' :
							$opts[$key] = substr( preg_replace( '/[^a-z0-9_]/', '', 
								strtolower( $opts[$key] ) ), 0, 15 );
							if ( ! empty( $opts[$key] ) ) 
								$opts[$key] = '@'.$opts[$key];
							break;

						// stip leading urls off Facebook usernames
						case 'fb_admins' :
							$opts[$key] = preg_replace( '/(http|https):\/\/[^\/]*?\//', '', 
								$opts[$key] );
							break;

						// must be a url (reset to default if not)
						case 'og_img_url' :
						case 'og_vid_url' :
						case 'og_def_img_url' :
						case 'og_publisher_url' :
						case 'link_publisher_url' :
						case 'plugin_cdn_urls' :
							if ( ! empty( $opts[$key] ) && 
								strpos( $opts[$key], '://' ) === false ) 
									$opts[$key] = $def_val;
							break;

						// must be numeric (blank or zero is ok)
						case 'link_def_author_id' :
						case 'og_desc_len' : 
						case 'og_img_max' :
						case 'og_vid_max' :
						case 'og_img_id' :
						case 'og_def_img_id' :
						case 'og_def_author_id' :
						case 'plugin_file_cache_hrs' :
							if ( ! empty( $opts[$key] ) && 
								! is_numeric( $opts[$key] ) )
									$opts[$key] = $def_val;
							break;

						// integer options that must me 1 or more (not zero)
						case 'meta_desc_len' : 
						case 'og_img_width' : 
						case 'og_img_height' : 
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
						case 'plugin_object_cache_exp' :
						case 'plugin_min_shorten' :
							if ( empty( $opts[$key] ) || 
								! is_numeric( $opts[$key] ) )
									$opts[$key] = $def_val;
							break;

						// needs to be filtered
						case 'og_title_sep' :
							$opts[$key] = $this->p->util->decode( trim( wptexturize( ' '.$opts[$key].' ' ) ) );

						// text strings that can be blank
						case 'fb_app_id' :
						case 'gp_expandto' :
						case 'og_title' :
						case 'og_desc' :
						case 'og_site_name' :
						case 'meta_desc' :
						case 'tc_desc' :
						case 'pin_desc' :
						case 'tumblr_img_desc' :
						case 'tumblr_vid_desc' :
						case 'twitter_desc' :
						case 'plugin_pro_tid' :
						case 'plugin_googl_api_key' :
						case 'plugin_bitly_api_key' :
						case 'plugin_cdn_folders' :
						case 'plugin_cdn_excl' :
							if ( ! empty( $opts[$key] ) )
								$opts[$key] = trim( $opts[$key] );
							break;

						// options that cannot be blank
						case 'og_art_section' :
						case 'link_author_field' :
						case 'og_img_id_pre' : 
						case 'og_def_img_id_pre' : 
						case 'og_author_field' :
						case 'buttons_location_the_excerpt' : 
						case 'buttons_location_the_content' : 
						case 'buttons_css_excerpt' :
						case 'buttons_css_content' :
						case 'buttons_css_shortcode' :
						case 'buttons_css_social' :
						case 'buttons_css_widget' :
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
						case 'plugin_cm_fb_name' : 
						case 'plugin_cm_fb_label' : 
						case 'plugin_cm_gp_name' : 
						case 'plugin_cm_gp_label' : 
						case 'plugin_cm_linkedin_name' : 
						case 'plugin_cm_linkedin_label' : 
						case 'plugin_cm_pin_name' : 
						case 'plugin_cm_pin_label' : 
						case 'plugin_cm_tumblr_name' : 
						case 'plugin_cm_tumblr_label' : 
						case 'plugin_cm_twitter_name' : 
						case 'plugin_cm_twitter_label' : 
						case 'plugin_cm_yt_name' : 
						case 'plugin_cm_yt_label' : 
						case 'plugin_cm_skype_name' : 
						case 'plugin_cm_skype_label' : 
						case 'wp_cm_aim_label' : 
						case 'wp_cm_jabber_label' : 
						case 'wp_cm_yim_label' : 
							if ( empty( $opts[$key] ) ) 
								$opts[$key] = $def_val;
							break;

						// everything else is assumed to be a true / false checkbox option
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
			if ( empty( $opts ) || ! is_array( $opts ) ) {
				$this->p->debug->log( 'exiting early: options variable is empty and/or not array' );
				return $opts;
			}

			$opts = $this->rename_keys( $this->renamed, $opts );

			// these option names may have been used in the past, so remove them, just in case
			if ( $opts['options_version'] < 30 ) {
				unset( $opts['og_img_width'] );
				unset( $opts['og_img_height'] );
				unset( $opts['og_img_crop'] );
			}

			if ( ! empty( $opts['twitter_shorten'] ) )
				$opts['twitter_shortener'] = 'googl';

			// upgrade the old 'og_img_size' name into width / height / crop values
			if ( array_key_exists( 'og_img_size', $opts ) ) {
				if ( ! empty( $opts['og_img_size'] ) && $opts['og_img_size'] !== 'medium' ) {
					$size_info = $this->p->media->get_size_info( $opts['og_img_size'] );
					if ( $size_info['width'] > 0 && $size_info['height'] > 0 ) {
						$opts['og_img_width'] = $size_info['width'];
						$opts['og_img_height'] = $size_info['height'];
						$opts['og_img_crop'] = $size_info['crop'];
					}
					unset( $opts['og_img_size'] );
				}
			}

			// unset options that no longer exist
			foreach ( $opts as $key => $val )
				// check that the key doesn't exist in the default options (which is a complete list of the current options used)
				if ( ! empty( $key ) && ! array_key_exists( $key, $def_opts ) ) {
					if ( $this->p->debug->is_on() == true )
						$this->p->notices->inf( 'Removing deprecated option \''.$key.'\' with a value of \''.$val.'\'.' );
					unset( $opts[$key] );
				}
			unset ( $key, $val );

			// add missing options and set to defaults
			foreach ( $def_opts as $key => $def_val ) {
				if ( ! empty( $key ) && ! array_key_exists( $key, $opts ) ) {
					$this->p->debug->log( 'adding missing '.$key.' option.' );
					$opts[$key] = $def_val;
				}
			}

			// sanitize and verify the options - just in case
			$opts = $this->sanitize( $opts, $def_opts );

			// mark the new options as current
			$old_opts_ver = $opts['options_version'];
			$opts['options_version'] = $this->options_version;
			$opts['plugin_version'] = $this->p->cf['version'];

			// don't save unless someone is there to see the success / error messages
			// plugin activation may hide notices, so main plugin class tests for activation and exits early
			if ( is_admin() ) {

				// update_option() returns false if options are the same or there was an error, 
				// so check to make sure they need to be updated to avoid throwing a false error
				if ( get_option( NGFB_OPTIONS_NAME ) !== $opts ) {

					if ( $this->p->is_avail['aop'] !== true && empty( $this->p->options['plugin_pro_tid'] ) )
						$this->p->notices->nag( $this->p->msg->get( 'pro_details' ) );

					if ( update_option( NGFB_OPTIONS_NAME, $opts ) == true ) {
						if ( $old_opts_ver !== $this->options_version ) {
							$this->p->debug->log( 'upgraded plugin options have been saved' );
							$this->p->notices->inf( 'Plugin settings have been upgraded and saved.' );
						}
					} else {
						$this->p->debug->log( 'failed to save the upgraded plugin options' );
						$this->p->notices->err( 'The plugin settings have been upgraded, 
							but WordPress returned an error when saving them.' );
						return $opts;
					}
				} else $this->p->debug->log( 'new and old options array is identical' );
			} else $this->p->debug->log( 'not in admin interface: postponing options save' );

			$this->p->debug->log( 'options successfully upgraded' );
			return $opts;
		}

		public function rename_keys( $renamed = array(), $opts = array() ) {
			// move old option values to new option names
			foreach ( $renamed as $old => $new )
				// rename if the old array key exists, but not the new one (we don't want to overwrite current values)
				if ( ! empty( $old ) && ! empty( $new ) && array_key_exists( $old, $opts ) && ! array_key_exists( $new, $opts ) ) {
					if ( $this->p->debug->is_on() == true )
						$this->p->notices->inf( 'Renamed \''.$old.'\' option to \''.
							$new.'\' with a value of \''.$opts[$old].'\'.' );
					$opts[$new] = $opts[$old];
					unset( $opts[$old] );
				}
			unset ( $old, $new );
			return $opts;
		}

	}

}
?>

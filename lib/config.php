<?php
/*
 * License: GPLv3
 * License URI: http://www.gnu.org/licenses/gpl.txt
 * Copyright 2012-2015 - Jean-Sebastien Morisset - http://surniaulula.com/
 */

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbConfig' ) ) {

	class NgfbConfig {

		private static $cf = array(
			'lca' => 'ngfb',		// lowercase acronym
			'uca' => 'NGFB',		// uppercase acronym
			'menu' => 'NGFB',		// menu item label
			'color' => 'f60',		// menu item color - dark orange
			'feed_cache_exp' => 86400,	// 24 hours
			'plugin' => array(
				'ngfb' => array(
					'version' => '8.6.4.4',		// plugin version
					'short' => 'NGFB',		// short plugin name
					'name' => 'NextGEN Facebook (NGFB)',
					'desc' => 'Want to improve your shared content? NGFB makes sure your content looks its best on all social websites - no matter how it\'s shared or re-shared!',
					'slug' => 'nextgen-facebook',
					'base' => 'nextgen-facebook/nextgen-facebook.php',
					'update_auth' => 'tid',
					'img' => array(
						'icon_small' => 'images/icon-128x128.png',
						'icon_medium' => 'images/icon-256x256.png',
						'background' => 'images/background.jpg',
					),
					'url' => array(
						// wordpress
						'download' => 'https://wordpress.org/plugins/nextgen-facebook/',
						'review' => 'https://wordpress.org/support/view/plugin-reviews/nextgen-facebook#postform',
						'readme' => 'https://plugins.svn.wordpress.org/nextgen-facebook/trunk/readme.txt',
						'setup' => 'https://plugins.svn.wordpress.org/nextgen-facebook/trunk/setup.html',
						'wp_support' => 'https://wordpress.org/support/plugin/nextgen-facebook',
						// surniaulula
						'update' => 'http://surniaulula.com/extend/plugins/nextgen-facebook/update/',
						'purchase' => 'http://surniaulula.com/extend/plugins/nextgen-facebook/',
						'changelog' => 'http://surniaulula.com/extend/plugins/nextgen-facebook/changelog/',
						'codex' => 'http://surniaulula.com/codex/plugins/nextgen-facebook/',
						'faq' => 'http://surniaulula.com/codex/plugins/nextgen-facebook/faq/',
						'notes' => 'http://surniaulula.com/codex/plugins/nextgen-facebook/notes/',
						'feed' => 'http://surniaulula.com/category/application/wordpress/wp-plugins/ngfb/feed/',
						'pro_support' => 'http://nextgen-facebook.support.surniaulula.com/',
					),
					'lib' => array(			// libraries
						'setting' => array (
							'ngfb-separator-0' => 'NGFB',
							'image-dimensions' => 'Social Image Dimensions',
							'social-accounts' => 'Website / Business Social Accounts',
							'contact-fields' => 'User Profile Contact Methods',
							'ngfb-separator-1' => '',
						),
						'submenu' => array (
							'general' => 'General',
							'advanced' => 'Advanced',
							'sharing' => 'Sharing Buttons',
							'style' => 'Sharing Styles',
							'readme' => 'Read Me',
							'setup' => 'Setup Guide',
							'licenses' => 'Extension Plugins and Pro Licenses',
						),
						'sitesubmenu' => array(
							'siteadvanced' => 'Advanced',
							'sitereadme' => 'Read Me',
							'sitesetup' => 'Setup Guide',
							'sitelicenses' => 'Extension Plugins and Pro Licenses',
						),
						'website' => array(
							'facebook' => 'Facebook', 
							'gplus' => 'GooglePlus',
							'twitter' => 'Twitter',
							'pinterest' => 'Pinterest',
							'linkedin' => 'LinkedIn',
							'buffer' => 'Buffer',
							'reddit' => 'Reddit',
							'managewp' => 'ManageWP',
							'stumbleupon' => 'StumbleUpon',
							'tumblr' => 'Tumblr',
							'youtube' => 'YouTube',
							'skype' => 'Skype',
						),
						'shortcode' => array(
							'sharing' => 'Sharing',
						),
						'widget' => array(
							'sharing' => 'Sharing',
						),
						'gpl' => array(
							'admin' => array(
								'general' => 'General Settings',
								'advanced' => 'Advanced Settings',
								'image-dimensions' => 'Social Image Dimensions',
								'sharing' => 'Button Settings',
								'style' => 'Style Settings',
								'post' => 'Post Social Settings',
								'taxonomy' => 'Taxonomy Social Settings',
								'user' => 'User Social Settings',
							),
							'ecom' => array(
								'woocommerce' => 'WooCommerce',
							),
							'forum' => array(
								'bbpress' => 'bbPress',
							),
							'social' => array(
								'buddypress' => 'BuddyPress',
							),
							'util' => array(
								'post' => 'Post Social Settings',
								'taxonomy' => 'Taxonomy Social Settings',
								'user' => 'User Social Settings',
							),
						),
						'pro' => array(
							'admin' => array(
								'general' => 'General Settings',
								'advanced' => 'Advanced Settings',
								'image-dimensions' => 'Social Image Dimensions',
								'sharing' => 'Button Settings',
								'style' => 'Style Settings',
								'post' => 'Post Social Settings',
								'taxonomy' => 'Taxonomy Social Settings',
								'user' => 'User Social Settings',
							),
							'ecom' => array(
								'edd' => 'Easy Digital Downloads',
								'marketpress' => 'MarketPress',
								'woocommerce' => 'WooCommerce',
								'wpecommerce' => 'WP e-Commerce',
							),
							'forum' => array(
								'bbpress' => 'bbPress',
							),
							'head' => array(
								'twittercard' => 'Twitter Cards',
							),
							'lang' => array(
								'polylang' => 'Polylang',
							),
							'media' => array(
								'gravatar' => 'Author Gravatar',
								'ngg' => 'NextGEN Gallery',
								'photon' => 'Jetpack Photon',
								'slideshare' => 'Slideshare API',
								'vimeo' => 'Vimeo Video API',
								'wistia' => 'Wistia Video API',
								'youtube' => 'YouTube Video / Playlist API',
							),
							'seo' => array(
								'aioseop' => 'All in One SEO Pack',
								'headspace2' => 'HeadSpace2 SEO',
								'wpseo' => 'WordPress SEO',
							),
							'social' => array(
								'buddypress' => 'BuddyPress',
							),
							'util' => array(
								'language' => 'Publisher Language',
								'shorten' => 'URL Shortening',
								'post' => 'Post Social Settings',
								'taxonomy' => 'Taxonomy Social Settings',
								'user' => 'User Social Settings',
							),
						),
					),
				),
				'ngfbum' => array(
					'short' => 'NGFB UM',
					'name' => 'NextGEN Facebook (NGFB) Pro Update Manager',
					'desc' => 'Update Manager for the NextGEN Facebook (NGFB) Pro plugin and its extensions.',
					'slug' => 'nextgen-facebook-um',
					'base' => 'nextgen-facebook-um/nextgen-facebook-um.php',
					'update_auth' => '',
					'img' => array(
						'icon_small' => 'https://surniaulula.github.io/nextgen-facebook-um/assets/icon-128x128.png',
						'icon_medium' => 'https://surniaulula.github.io/nextgen-facebook-um/assets/icon-256x256.png',
					),
					'url' => array(
						// surniaulula
						'download' => 'http://surniaulula.com/extend/plugins/nextgen-facebook-um/',
						'latest_zip' => 'http://surniaulula.com/extend/plugins/nextgen-facebook-um/latest/',
						'update' => 'http://surniaulula.com/extend/plugins/nextgen-facebook-um/update/',
					),
				),
			),
			'opt' => array(						// options
				'version' => 'ngfb348',				// increment when changing default options
				'defaults' => array(
					'options_filtered' => false,
					'options_version' => '',
					'schema_desc_len' => 250,		// meta itemprop="description" maximum text length
					'schema_website_json' => 1,
					'schema_publisher_json' => 1,
					'schema_author_json' => 1,
					'schema_logo_url' => '',
					'seo_desc_len' => 156,			// meta name="description" maximum text length
					'seo_author_name' => 'none',		// meta name="author" format
					'seo_def_author_id' => 0,
					'seo_def_author_on_index' => 0,
					'seo_def_author_on_search' => 0,
					'seo_author_field' => '',		// default value set by NgfbOptions::get_defaults()
					'seo_publisher_url' => '',
					'fb_publisher_url' => '',
					'fb_admins' => '',
					'fb_app_id' => '',
					'fb_lang' => 'en_US',
					'instgram_publisher_url' => '',
					'linkedin_publisher_url' => '',
					'myspace_publisher_url' => '',
					'og_site_name' => '',
					'og_site_description' => '',
					'og_art_section' => 'none',
					'og_img_width' => 600,
					'og_img_height' => 600,
					'og_img_crop' => 1,
					'og_img_crop_x' => 'center',
					'og_img_crop_y' => 'center',
					'og_img_max' => 1,
					'og_vid_max' => 1,
					'og_vid_https' => 1,
					'og_vid_prev_img' => 0,
					'og_vid_html_type' => 1,
					'og_def_img_id_pre' => 'wp',
					'og_def_img_id' => '',
					'og_def_img_url' => '',
					'og_def_img_on_index' => 1,
					'og_def_img_on_author' => 0,
					'og_def_img_on_search' => 0,
					'og_def_vid_url' => '',
					'og_def_vid_on_index' => 1,
					'og_def_vid_on_author' => 0,
					'og_def_vid_on_search' => 0,
					'og_def_author_id' => 0,
					'og_def_author_on_index' => 0,
					'og_def_author_on_search' => 0,
					'og_ngg_tags' => 0,
					'og_page_parent_tags' => 0,
					'og_page_title_tag' => 0,
					'og_author_field' => '',		// default value set by NgfbOptions::get_defaults()
					'og_author_fallback' => 0,
					'og_title_sep' => '-',
					'og_title_len' => 70,
					'og_desc_len' => 300,
					'og_desc_hashtags' => 3,
					'og_desc_strip' => 0,
					'og_desc_alt' => 1,
					'rp_publisher_url' => '',
					'rp_author_name' => 'display_name',	// rich-pin specific article:author
					'rp_img_width' => 600,
					'rp_img_height' => 600,
					'rp_img_crop' => 0,
					'rp_img_crop_x' => 'center',
					'rp_img_crop_y' => 'center',
					'rp_dom_verify' => '',
					'tc_enable' => 1,
					'tc_site' => '',
					'tc_desc_len' => 200,
					// summary card
					'tc_sum_width' => 300,
					'tc_sum_height' => 300,
					'tc_sum_crop' => 1,
					'tc_sum_crop_x' => 'center',
					'tc_sum_crop_y' => 'center',
					// large image summary card
					'tc_lrgimg_width' => 600,
					'tc_lrgimg_height' => 600,
					'tc_lrgimg_crop' => 0,
					'tc_lrgimg_crop_x' => 'center',
					'tc_lrgimg_crop_y' => 'center',
					// photo card
					'tc_photo_width' => 600,
					'tc_photo_height' => 600,
					'tc_photo_crop' => 0,
					'tc_photo_crop_x' => 'center',
					'tc_photo_crop_y' => 'center',
					// gallery card
					'tc_gal_min' => 4,
					'tc_gal_width' => 300,
					'tc_gal_height' => 300,
					'tc_gal_crop' => 0,
					'tc_gal_crop_x' => 'center',
					'tc_gal_crop_y' => 'center',
					// product card
					'tc_prod_width' => 300,
					'tc_prod_height' => 300,
					'tc_prod_crop' => 1,			// prefers square product images
					'tc_prod_crop_x' => 'center',
					'tc_prod_crop_y' => 'center',
					'tc_prod_labels' => 2,
					'tc_prod_def_label2' => 'Location',
					'tc_prod_def_data2' => 'Unknown',
					// enable/disable header html tags
					'add_link_rel_author' => 1,
					'add_link_rel_publisher' => 1,
					'add_meta_property_fb:admins' => 1,
					'add_meta_property_fb:app_id' => 1,
					'add_meta_property_og:locale' => 1,
					'add_meta_property_og:site_name' => 1,
					'add_meta_property_og:description' => 1,
					'add_meta_property_og:title' => 1,
					'add_meta_property_og:type' => 1,
					'add_meta_property_og:url' => 1,
					'add_meta_property_og:image' => 1,
					'add_meta_property_og:image:secure_url' => 1,
					'add_meta_property_og:image:width' => 1,
					'add_meta_property_og:image:height' => 1,
					'add_meta_property_og:video:url' => 1,
					'add_meta_property_og:video:secure_url' => 1,
					'add_meta_property_og:video:width' => 1,
					'add_meta_property_og:video:height' => 1,
					'add_meta_property_og:video:type' => 1,
					'add_meta_property_article:author' => 1,
					'add_meta_property_article:publisher' => 1,
					'add_meta_property_article:published_time' => 1,
					'add_meta_property_article:modified_time' => 1,
					'add_meta_property_article:section' => 1,
					'add_meta_property_article:tag' => 1,
					'add_meta_property_product:price:amount' => 1,
					'add_meta_property_product:price:currency' => 1,
					'add_meta_property_product:availability' => 1,
					'add_meta_name_author' => 1,
					'add_meta_name_canonical' => 0,
					'add_meta_name_description' => 1,
					'add_meta_name_generator' => 1,
					'add_meta_name_p:domain_verify' => 1,
					'add_meta_name_twitter:card' => 1,
					'add_meta_name_twitter:creator' => 1,
					'add_meta_name_twitter:domain' => 1,
					'add_meta_name_twitter:site' => 1,
					'add_meta_name_twitter:title' => 1,
					'add_meta_name_twitter:description' => 1,
					'add_meta_name_twitter:image' => 1,
					'add_meta_name_twitter:image:width' => 1,
					'add_meta_name_twitter:image:height' => 1,
					'add_meta_name_twitter:image0' => 1,
					'add_meta_name_twitter:image1' => 1,
					'add_meta_name_twitter:image2' => 1,
					'add_meta_name_twitter:image3' => 1,
					'add_meta_name_twitter:player' => 1,
					'add_meta_name_twitter:player:width' => 1,
					'add_meta_name_twitter:player:height' => 1,
					'add_meta_name_twitter:data1' => 1,
					'add_meta_name_twitter:label1' => 1,
					'add_meta_name_twitter:data2' => 1,
					'add_meta_name_twitter:label2' => 1,
					'add_meta_name_twitter:data3' => 1,
					'add_meta_name_twitter:label3' => 1,
					'add_meta_name_twitter:data4' => 1,
					'add_meta_name_twitter:label4' => 1,
					'add_meta_itemprop_name' => 1,
					'add_meta_itemprop_headline' => 1,
					'add_meta_itemprop_datepublished' => 1,
					'add_meta_itemprop_description' => 1,
					'add_meta_itemprop_url' => 1,
					'add_meta_itemprop_image' => 1,
					// advanced plugin options
					'plugin_version' => '',
					'plugin_ngfb_tid' => '',
					'plugin_show_opts' => 'basic',
					'plugin_preserve' => 0,
					'plugin_debug' => 0,
					'plugin_cache_info' => 0,
					'plugin_check_head' => 1,
					'plugin_filter_title' => 1,
					'plugin_filter_content' => 0,
					'plugin_filter_excerpt' => 0,
					'plugin_filter_lang' => 1,
					'plugin_shortcodes' => 1,
					'plugin_widgets' => 1,
					'plugin_auto_img_resize' => 1,
					'plugin_ignore_small_img' => 1,
					'plugin_page_excerpt' => 1,
					'plugin_page_tags' => 1,
					'plugin_gravatar_api' => 1,
					'plugin_slideshare_api' => 1,
					'plugin_vimeo_api' => 1,
					'plugin_wistia_api' => 1,
					'plugin_youtube_api' => 1,
					'plugin_cf_img_url' => '_format_image_url',
					'plugin_cf_vid_url' => '_format_video_url',
					'plugin_cf_vid_embed' => '_format_video_embed',
					'plugin_add_to_post' => 1,
					'plugin_add_to_page' => 1,
					'plugin_add_to_taxonomy' => 1,
					'plugin_add_to_user' => 1,
					'plugin_add_to_attachment' => 1,
					'plugin_object_cache_exp' => 86400,	// 24 hours
					'plugin_file_cache_exp' => 0,
					'plugin_verify_certs' => 0,
					'plugin_shortener' => 'none',
					'plugin_min_shorten' => 22,
					'plugin_bitly_login' => '',
					'plugin_bitly_api_key' => '',
					'plugin_google_api_key' => '',
					'plugin_google_shorten' => 0,
					'plugin_cm_fb_name' => 'facebook', 
					'plugin_cm_fb_label' => 'Facebook URL', 
					'plugin_cm_fb_enabled' => 1,
					'plugin_cm_gp_name' => 'gplus', 
					'plugin_cm_gp_label' => 'Google+ URL', 
					'plugin_cm_gp_enabled' => 1,
					'plugin_cm_instgram_name' => 'instagram', 
					'plugin_cm_instgram_label' => 'Instagram URL', 
					'plugin_cm_instgram_enabled' => 1,
					'plugin_cm_linkedin_name' => 'linkedin', 
					'plugin_cm_linkedin_label' => 'LinkedIn URL', 
					'plugin_cm_linkedin_enabled' => 1,
					'plugin_cm_myspace_name' => 'myspace', 
					'plugin_cm_myspace_label' => 'MySpace URL', 
					'plugin_cm_myspace_enabled' => 1,
					'plugin_cm_pin_name' => 'pinterest', 
					'plugin_cm_pin_label' => 'Pinterest URL', 
					'plugin_cm_pin_enabled' => 1,
					'plugin_cm_tumblr_name' => 'tumblr', 
					'plugin_cm_tumblr_label' => 'Tumblr URL', 
					'plugin_cm_tumblr_enabled' => 1,
					'plugin_cm_twitter_name' => 'twitter', 
					'plugin_cm_twitter_label' => 'Twitter @username', 
					'plugin_cm_twitter_enabled' => 1,
					'plugin_cm_yt_name' => 'youtube', 
					'plugin_cm_yt_label' => 'YouTube Channel URL', 
					'plugin_cm_yt_enabled' => 1,
					'plugin_cm_skype_name' => 'skype', 
					'plugin_cm_skype_label' => 'Skype Username', 
					'plugin_cm_skype_enabled' => 1,
					'wp_cm_aim_name' => 'aim', 
					'wp_cm_aim_label' => 'AIM', 
					'wp_cm_aim_enabled' => 1,
					'wp_cm_jabber_name' => 'jabber', 
					'wp_cm_jabber_label' => 'Google Talk', 
					'wp_cm_jabber_enabled' => 1,
					'wp_cm_yim_name' => 'yim',
					'wp_cm_yim_label' => 'Yahoo IM', 
					'wp_cm_yim_enabled' => 1,
				),
				'site_defaults' => array(
					'options_filtered' => false,
					'options_version' => '',
					'plugin_version' => '',
					'plugin_ngfb_tid' => '',
					'plugin_ngfb_tid:use' => 'default',
					'plugin_preserve' => 0,
					'plugin_preserve:use' => 'default',
					'plugin_debug' => 0,
					'plugin_debug:use' => 'default',
					'plugin_object_cache_exp' => 86400,	// 24 hours
					'plugin_object_cache_exp:use' => 'default',
					'plugin_file_cache_exp' => 0,
					'plugin_file_cache_exp:use' => 'default',
					'plugin_verify_certs' => 0,
					'plugin_verify_certs:use' => 'default',
					'plugin_shortener' => 'none',
					'plugin_shortener:use' => 'default',
					'plugin_min_shorten' => 22,
					'plugin_min_shorten:use' => 'default',
					'plugin_bitly_login' => '',
					'plugin_bitly_login:use' => 'default',
					'plugin_bitly_api_key' => '',
					'plugin_bitly_api_key:use' => 'default',
					'plugin_google_api_key' => '',
					'plugin_google_api_key:use' => 'default',
					'plugin_google_shorten' => 0,
					'plugin_google_shorten:use' => 'default',
				),
				'pre' => array(
					'email' => 'email', 
					'facebook' => 'fb', 
					'gplus' => 'gp',
					'twitter' => 'twitter',
					'instagram' => 'instgram',
					'linkedin' => 'linkedin',
					'myspace' => 'myspace',
					'pinterest' => 'pin',
					'pocket' => 'pocket',
					'buffer' => 'buffer',
					'reddit' => 'reddit',
					'managewp' => 'managewp',
					'stumbleupon' => 'stumble',
					'tumblr' => 'tumblr',
					'youtube' => 'yt',
					'skype' => 'skype',
					'vk' => 'vk',
				),
			),
			'wp' => array(				// wordpress
				'min_version' => '3.0',		// minimum wordpress version
				'cm' => array(
					'aim' => 'AIM',
					'jabber' => 'Google Talk',
					'yim' => 'Yahoo IM',
				),
			),
			'php' => array(				// php
				'min_version' => '4.1.0',	// minimum php version
			),
			'follow' => array(
				'size' => 32,
				'src' => array(
					'facebook.png' => 'https://www.facebook.com/SurniaUlulaCom',
					'gplus.png' => 'https://plus.google.com/+SurniaUlula/',
					'linkedin.png' => 'https://www.linkedin.com/in/jsmoriss',
					'twitter.png' => 'https://twitter.com/surniaululacom',
					'youtube.png' => 'https://www.youtube.com/user/SurniaUlulaCom',
					'feed.png' => 'http://feed.surniaulula.com/category/application/wordpress/wp-plugins/ngfb/feed/',
				),
			),
			'form' => array(
				'og_image_col_width' => '70px',
				'og_image_col_height' => '37px',
				'tooltip_class' => 'sucom_tooltip',
				'max_hashtags' => 10,
				'max_media_items' => 20,
				'yes_no' => array(
					'1' => 'Yes',
					'0' => 'No',
				),
				'file_cache_hrs' => array(
					0 => 0,
					3600 => 1,
					7200 => 3,
					21600 => 6,
					32400 => 9,
					43200 => 12,
					86400 => 24,
					129600 => 36,
					172800 => 48,
					259200 => 72,
					604800 => 168,
				),
				'script_locations' => array(
					'none' => '[none]',
					'header' => 'Header',
					'footer' => 'Footer',
				),
				'caption_types' => array(
					'none' => '[none]',
					'title' => 'Title Only',
					'excerpt' => 'Excerpt Only',
					'both' => 'Title and Excerpt',
				),
				'user_name_fields' => array(
					'none' => '[none]',
					'fullname' => 'First and Last Names',
					'display_name' => 'Display Name',
					'nickname' => 'Nickname',
				),
				'show_options' => array(
					'basic' => 'Basic Options',
					'all' => 'All Options',
				),
				'site_option_use' => array(
					'default' => 'Default value',
					'empty' => 'If value is empty',
					'force' => 'Force this value',
				),
				'position_crop_x' => array(
					'left' => 'Left',
					'center' => 'Center',
					'right' => 'Right',
				),
				'position_crop_y' => array(
					'top' => 'Top',
					'center' => 'Center',
					'bottom' => 'Bottom',
				),
				'shorteners' => array(
					'none' => '[none]',
					'bitly' => 'Bit.ly',
					'googl' => 'Goo.gl',
				),
			),
			'head' => array(
				'max_img_ratio' => 3,
				'min_img_dim' => 200,
				'min_desc_len' => 156,
			),
			'cache' => array(
				'file' => false,
				'object' => true,
				'transient' => true,
			),
		);

		// get_config is called very early, so don't apply filters unless instructed
		public static function get_config( $idx = false, $filter = false ) { 

			if ( ! isset( self::$cf['config_filtered'] ) || self::$cf['config_filtered'] !== true ) {

				// remove the sharing libs if social sharing features are disabled
				if ( defined( 'NGFB_SOCIAL_SHARING_DISABLE' ) && NGFB_SOCIAL_SHARING_DISABLE ) {
					foreach ( array_keys( self::$cf['plugin'] ) as $lca ) {
						unset (
							self::$cf['plugin'][$lca]['lib']['website'],
							self::$cf['plugin'][$lca]['lib']['submenu']['sharing'],
							self::$cf['plugin'][$lca]['lib']['submenu']['style'],
							self::$cf['plugin'][$lca]['lib']['shortcode']['sharing'],
							self::$cf['plugin'][$lca]['lib']['widget']['sharing'],
							self::$cf['plugin'][$lca]['lib']['gpl']['admin']['sharing'],
							self::$cf['plugin'][$lca]['lib']['gpl']['admin']['style'],
							self::$cf['plugin'][$lca]['lib']['pro']['admin']['sharing'],
							self::$cf['plugin'][$lca]['lib']['pro']['admin']['style']
						);
					}
				}

				if ( $filter === true ) {
					self::$cf = apply_filters( self::$cf['lca'].'_get_config', self::$cf );
					self::$cf['config_filtered'] = true;
					self::$cf['*'] = array( 
						'lib' => array(),
						'version' => '',
					);
					foreach ( self::$cf['plugin'] as $lca => $info ) {
						if ( isset( $info['lib'] ) && is_array( $info['lib'] ) )
							self::$cf['*']['lib'] = SucomUtil::array_merge_recursive_distinct( self::$cf['*']['lib'], $info['lib'] );
						if ( isset( $info['version'] ) )
							self::$cf['*']['version'] .= '-'.$lca.$info['version'];
					}
					self::$cf['*']['version'] = trim( self::$cf['*']['version'], '-' );
				}

				// complete relative paths in the image array
				foreach ( self::$cf['plugin'] as $lca => $info ) {
					if ( isset( $info['base'] ) ) {
						$base = self::$cf['plugin'][$lca]['base'];	// nextgen-facebook/nextgen-facebook.php
						foreach ( array( 'img' ) as $sub ) {
							if ( isset( $info[$sub] ) && is_array( $info[$sub] ) ) {
								foreach ( $info[$sub] as $id => $url ) {
									if ( ! empty( $url ) && strpos( $url, '//' ) === false )
										self::$cf['plugin'][$lca][$sub][$id] = trailingslashit( plugins_url( '', $base ) ).$url;
								}
							}
						}
					}
				}
			}

			if ( $idx !== false ) {
				if ( array_key_exists( $idx, self::$cf ) )
					return self::$cf[$idx];
				else return false;
			} else return self::$cf;
		}

		public static function set_constants( $plugin_filepath ) { 

			$cf = self::get_config();
			$slug = $cf['plugin'][$cf['lca']]['slug'];
			$version = $cf['plugin'][$cf['lca']]['version'];

			define( 'NGFB_FILEPATH', $plugin_filepath );						
			define( 'NGFB_PLUGINDIR', trailingslashit( realpath( dirname( $plugin_filepath ) ) ) );
			define( 'NGFB_PLUGINBASE', plugin_basename( $plugin_filepath ) );
			define( 'NGFB_TEXTDOM', $slug );
			define( 'NGFB_URLPATH', trailingslashit( plugins_url( '', $plugin_filepath ) ) );
			define( 'NGFB_NONCE', md5( NGFB_PLUGINDIR.'-'.$version.
				( defined( 'NONCE_SALT' ) ? NONCE_SALT : '' ) ) );

			if ( defined( 'NGFB_DEBUG' ) && 
				! defined( 'NGFB_HTML_DEBUG' ) )
					define( 'NGFB_HTML_DEBUG', NGFB_DEBUG );

			if ( ! defined( 'NGFB_DEBUG_FILE_EXP' ) )
				define( 'NGFB_DEBUG_FILE_EXP', 300 );

			if ( ! defined( 'NGFB_CACHEDIR' ) )
				define( 'NGFB_CACHEDIR', NGFB_PLUGINDIR.'cache/' );

			if ( ! defined( 'NGFB_CACHEURL' ) )
				define( 'NGFB_CACHEURL', NGFB_URLPATH.'cache/' );

			if ( ! defined( 'NGFB_TOPICS_LIST' ) )
				define( 'NGFB_TOPICS_LIST', NGFB_PLUGINDIR.'share/topics.txt' );

			if ( ! defined( 'NGFB_SHARING_SHORTCODE' ) )
				define( 'NGFB_SHARING_SHORTCODE', 'ngfb' );

			if ( ! defined( 'NGFB_MENU_ORDER' ) )
				define( 'NGFB_MENU_ORDER', '99.11' );

			if ( ! defined( 'NGFB_MENU_ICON_HIGHLIGHT' ) )
				define( 'NGFB_MENU_ICON_HIGHLIGHT', true );

			/*
			 * NGFB option and meta array names
			 */
			if ( ! defined( 'NGFB_OPTIONS_NAME' ) )
				define( 'NGFB_OPTIONS_NAME', 'ngfb_options' );

			if ( ! defined( 'NGFB_SITE_OPTIONS_NAME' ) )
				define( 'NGFB_SITE_OPTIONS_NAME', 'ngfb_site_options' );

			if ( ! defined( 'NGFB_META_NAME' ) )
				define( 'NGFB_META_NAME', '_ngfb_meta' );

			if ( ! defined( 'NGFB_PREF_NAME' ) )
				define( 'NGFB_PREF_NAME', '_ngfb_pref' );

			/*
			 * NGFB option and meta array alternate / fallback names
			 */
			if ( ! defined( 'NGFB_OPTIONS_NAME_ALT' ) )
				define( 'NGFB_OPTIONS_NAME_ALT', 'wpsso_options' );

			if ( ! defined( 'NGFB_SITE_OPTIONS_NAME_ALT' ) )
				define( 'NGFB_SITE_OPTIONS_NAME_ALT', 'wpsso_site_options' );

			if ( ! defined( 'NGFB_META_NAME_ALT' ) )
				define( 'NGFB_META_NAME_ALT', '_wpsso_meta' );

			if ( ! defined( 'NGFB_PREF_NAME_ALT' ) )
				define( 'NGFB_PREF_NAME_ALT', '_wpsso_pref' );

			/*
			 * NGFB hook priorities
			 */
			if ( ! defined( 'NGFB_ADD_MENU_PRIORITY' ) )
				define( 'NGFB_ADD_MENU_PRIORITY', -20 );

			if ( ! defined( 'NGFB_ADD_SETTINGS_PRIORITY' ) )
				define( 'NGFB_ADD_SETTINGS_PRIORITY', -10 );

			if ( ! defined( 'NGFB_META_SAVE_PRIORITY' ) )
				define( 'NGFB_META_SAVE_PRIORITY', 6 );

			if ( ! defined( 'NGFB_META_CACHE_PRIORITY' ) )
				define( 'NGFB_META_CACHE_PRIORITY', 9 );

			if ( ! defined( 'NGFB_INIT_PRIORITY' ) )
				define( 'NGFB_INIT_PRIORITY', 14 );

			if ( ! defined( 'NGFB_DOCTYPE_PRIORITY' ) )
				define( 'NGFB_DOCTYPE_PRIORITY', 100 );

			if ( ! defined( 'NGFB_HEAD_PRIORITY' ) )
				define( 'NGFB_HEAD_PRIORITY', 10 );

			if ( ! defined( 'NGFB_SOCIAL_PRIORITY' ) )
				define( 'NGFB_SOCIAL_PRIORITY', 100 );
			
			if ( ! defined( 'NGFB_FOOTER_PRIORITY' ) )
				define( 'NGFB_FOOTER_PRIORITY', 100 );
			
			if ( ! defined( 'NGFB_SEO_FILTERS_PRIORITY' ) )
				define( 'NGFB_SEO_FILTERS_PRIORITY', 100 );
			
			/*
			 * NGFB curl settings
			 */
			if ( ! defined( 'NGFB_CURL_USERAGENT' ) )
				define( 'NGFB_CURL_USERAGENT', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/36.0.1985.125 Safari/537.36' );

			if ( ! defined( 'NGFB_CURL_CAINFO' ) )
				define( 'NGFB_CURL_CAINFO', NGFB_PLUGINDIR.'share/curl/cacert.pem' );

			/*
			 * Disable caching plugins for the duplicate meta tag check feature
			 */
			if ( ! empty( $_GET['NGFB_META_TAGS_DISABLE'] ) ) {

				if ( ! defined( 'DONOTCACHEPAGE' ) )
					define( 'DONOTCACHEPAGE', true );	// wp super cache

				if ( ! defined( 'QUICK_CACHE_ALLOWED' ) )
					define( 'QUICK_CACHE_ALLOWED', false );	// quick cache

				if ( ! defined( 'ZENCACHE_ALLOWED' ) )
					define( 'ZENCACHE_ALLOWED', false );	// zencache
			}
		}

		public static function require_libs( $plugin_filepath ) {
			
			require_once( NGFB_PLUGINDIR.'lib/com/util.php' );
			require_once( NGFB_PLUGINDIR.'lib/com/cache.php' );
			require_once( NGFB_PLUGINDIR.'lib/com/notice.php' );
			require_once( NGFB_PLUGINDIR.'lib/com/script.php' );
			require_once( NGFB_PLUGINDIR.'lib/com/style.php' );
			require_once( NGFB_PLUGINDIR.'lib/com/webpage.php' );

			require_once( NGFB_PLUGINDIR.'lib/register.php' );
			require_once( NGFB_PLUGINDIR.'lib/check.php' );
			require_once( NGFB_PLUGINDIR.'lib/util.php' );
			require_once( NGFB_PLUGINDIR.'lib/options.php' );
			require_once( NGFB_PLUGINDIR.'lib/meta.php' );
			require_once( NGFB_PLUGINDIR.'lib/post.php' );		// extends meta.php
			require_once( NGFB_PLUGINDIR.'lib/taxonomy.php' );	// extends meta.php
			require_once( NGFB_PLUGINDIR.'lib/user.php' );		// extends meta.php
			require_once( NGFB_PLUGINDIR.'lib/media.php' );
			require_once( NGFB_PLUGINDIR.'lib/head.php' );
			require_once( NGFB_PLUGINDIR.'lib/opengraph.php' );
			require_once( NGFB_PLUGINDIR.'lib/schema.php' );
			require_once( NGFB_PLUGINDIR.'lib/functions.php' );

			if ( is_admin() ) {
				require_once( NGFB_PLUGINDIR.'lib/messages.php' );
				require_once( NGFB_PLUGINDIR.'lib/admin.php' );
				require_once( NGFB_PLUGINDIR.'lib/com/form.php' );
				require_once( NGFB_PLUGINDIR.'lib/ext/parse-readme.php' );
			}

			if ( ( ! defined( 'NGFB_SOCIAL_SHARING_DISABLE' ) || 
				( defined( 'NGFB_SOCIAL_SHARING_DISABLE' ) && ! NGFB_SOCIAL_SHARING_DISABLE ) ) &&
				empty( $_SERVER['NGFB_SOCIAL_SHARING_DISABLE'] ) &&
				file_exists( NGFB_PLUGINDIR.'lib/sharing.php' ) )
					require_once( NGFB_PLUGINDIR.'lib/sharing.php' );

			if ( file_exists( NGFB_PLUGINDIR.'lib/loader.php' ) )
				require_once( NGFB_PLUGINDIR.'lib/loader.php' );

			add_filter( 'ngfb_load_lib', array( 'NgfbConfig', 'load_lib' ), 10, 3 );
		}

		public static function load_lib( $ret = false, $filespec = '', $classname = '' ) {
			if ( $ret === false && ! empty( $filespec ) ) {
				$filepath = NGFB_PLUGINDIR.'lib/'.$filespec.'.php';
				if ( file_exists( $filepath ) ) {
					require_once( $filepath );
					if ( empty( $classname ) )
						return 'ngfb'.str_replace( array( '/', '-' ), '', $filespec );
					else return $classname;
				}
			}
			return $ret;
		}
	}
}

?>

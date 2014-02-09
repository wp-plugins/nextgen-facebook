<?php
/*
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.txt
Copyright 2012-2014 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbConfig' ) ) {

	class NgfbConfig {

		private static $cf = array(
			'version' => '7.1rc1',			// plugin version
			'lca' => 'ngfb',			// lowercase acronym
			'cca' => 'Ngfb',			// camelcase acronym
			'uca' => 'NGFB',			// uppercase acronym
			'slug' => 'nextgen-facebook',
			'menu' => 'Open Graph+',		// menu item label
			'full' => 'NGFB Open Graph+',		// full plugin name
			'full_pro' => 'NGFB Open Graph+ Pro',
			'update_hours' => 12,			// check for pro updates
			'cache' => array(
				'file' => true,
				'object' => true,
				'transient' => true,
			),
			'lib' => array(				// libraries
				'setting' => array (
					'contact' => 'Contact Methods',
				),
				'submenu' => array (
					'general' => 'General',
					'advanced' => 'Advanced',
					'sharing' => 'Social Sharing',
					'style' => 'Social Style',
					'about' => 'About',
				),
				'sitesubmenu' => array(
					'network' => 'Network',
				),
				'website' => array(
					'facebook' => 'Facebook', 
					'gplus' => 'GooglePlus',
					'twitter' => 'Twitter',
					'linkedin' => 'LinkedIn',
					'managewp' => 'ManageWP',
					'pinterest' => 'Pinterest',
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
						'sharing' => 'Social Sharing',
						'style' => 'Social Style',
						'apikeys' => 'API Keys',
						'rewrite' => 'URL Rewrite',
						'postmeta' => 'Custom Post Meta',
					),
					'forum' => array(
						'bbpress' => 'bbPress',
					),
					'social' => array(
						'buddypress' => 'BuddyPress',
					),
					'util' => array(
						'postmeta' => 'Custom Post Meta',
					),
				),
				'pro' => array(
					'admin' => array(
						'general' => 'General Settings',
						'advanced' => 'Advanced Settings',
						'sharing' => 'Social Sharing',
						'style' => 'Social Style',
						'apikeys' => 'API Keys',
						'rewrite' => 'URL Rewrite',
						'postmeta' => 'Custom Post Meta',
					),
					'ecom' => array(
						'woocommerce' => 'WooCommerce',
						'marketpress' => 'MarketPress',
						'wpecommerce' => 'WP e-Commerce',
					),
					'forum' => array(
						'bbpress' => 'bbPress',
					),
					'head' => array(
						'twittercard' => 'Twitter Cards',
					),
					'media' => array(
						'ngg' => 'NextGEN Gallery',
						'photon' => 'Jetpack Photon',
						'slideshare' => 'Slideshare API',
						'vimeo' => 'Vimeo Video API',
						'wistia' => 'Wistia Video API',
						'youtube' => 'YouTube Video / Playlist API',
					),
					'seo' => array(
						'aioseop' => 'All in One SEO Pack',
						'wpseo' => 'WordPress SEO',
					),
					'social' => array(
						'buddypress' => 'BuddyPress',
					),
					'util' => array(
						'language' => 'WP Locale Language',
						'rewrite' => 'URL Rewriter',
						'shorten' => 'URL Shortener',
						'postmeta' => 'Custom Post Meta',
					),
				),
			),
			'opt' => array(				// options
				'version' => '248',		// increment when changing default options
				'defaults' => array(
					'meta_desc_len' => 157,
					'link_author_field' => '',
					'link_def_author_id' => 0,
					'link_def_author_on_index' => 0,
					'link_def_author_on_search' => 0,
					'link_publisher_url' => '',
					'fb_admins' => '',
					'fb_app_id' => '',
					'fb_lang' => 'en_US',
					'og_site_name' => '',
					'og_site_description' => '',
					'og_publisher_url' => '',
					'og_art_section' => '',
					'og_img_width' => 1200,
					'og_img_height' => 630,
					'og_img_crop' => 1,
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
					'og_desc_hashtags' => 0,
					'og_desc_strip' => 0,
					'og_empty_tags' => 0,
					'buttons_on_index' => 0,
					'buttons_on_front' => 0,
					'buttons_add_to_post' => 1,
					'buttons_add_to_page' => 1,
					'buttons_add_to_attachment' => 1,
					'buttons_pos_content' => 'bottom',
					'buttons_pos_excerpt' => 'bottom',
					'buttons_use_social_css' => 1,
					'buttons_enqueue_social_css' => 0,
					'buttons_css_admin_edit' => '',
					'buttons_css_sharing' => '',
					'buttons_css_content' => '',
					'buttons_css_excerpt' => '',
					'buttons_css_sidebar' => '',
					'buttons_css_shortcode' => '',
					'buttons_css_widget' => '',
					'buttons_js_sidebar' => '/* Save an empty style text box to reload the default javascript */

jQuery("#ngfb-sidebar").mouseenter( function(){ 
	jQuery("#ngfb-sidebar-buttons").css({
		display:"block",
		width:"auto",
		height:"auto",
		overflow:"visible",
		"border-style":"solid",
	}); } );
jQuery("#ngfb-sidebar").click( function(){ 
	jQuery("#ngfb-sidebar-buttons").toggle(); } );',
					'buttons_preset_content' => '',
					'buttons_preset_excerpt' => '',
					'buttons_preset_sidebar' => 'large_share_vertical',
					'buttons_preset_admin_edit' => 'small_share_count',
					'fb_on_content' => 0,
					'fb_on_excerpt' => 0,
					'fb_on_admin_edit' => 1,
					'fb_on_sidebar' => 0,
					'fb_order' => 1,
					'fb_js_loc' => 'header',
					'fb_button' => 'like',
					'fb_markup' => 'xfbml',
					'fb_send' => 1,
					'fb_layout' => 'button_count',
					'fb_font' => 'arial',
					'fb_show_faces' => 0,
					'fb_colorscheme' => 'light',
					'fb_action' => 'like',
					'fb_type' => 'button_count',
					'gp_on_content' => 0,
					'gp_on_excerpt' => 0,
					'gp_on_admin_edit' => 1,
					'gp_on_sidebar' => 0,
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
					// summary card
					'tc_sum_width' => 200,
					'tc_sum_height' => 200,
					'tc_sum_crop' => 1,
					// large image summary card
					'tc_lrgimg_width' => 300,
					'tc_lrgimg_height' => 300,
					'tc_lrgimg_crop' => 0,
					// photo card
					'tc_photo_width' => 800,
					'tc_photo_height' => 800,
					'tc_photo_crop' => 0,
					// gallery card
					'tc_gal_min' => 4,
					'tc_gal_width' => 300,
					'tc_gal_height' => 300,
					'tc_gal_crop' => 0,
					// product card
					'tc_prod_width' => 300,
					'tc_prod_height' => 300,
					'tc_prod_crop' => 1,	// prefers square product images
					'tc_prod_def_l2' => 'Location',
					'tc_prod_def_d2' => 'Unknown',
					'twitter_on_content' => 0,
					'twitter_on_excerpt' => 0,
					'twitter_on_admin_edit' => 1,
					'twitter_on_sidebar' => 0,
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
					'linkedin_on_content' => 0,
					'linkedin_on_excerpt' => 0,
					'linkedin_on_admin_edit' => 1,
					'linkedin_on_sidebar' => 0,
					'linkedin_order' => 4,
					'linkedin_js_loc' => 'header',
					'linkedin_counter' => 'right',
					'linkedin_showzero' => 1,
					'managewp_on_content' => 0,
					'managewp_on_excerpt' => 0,
					'managewp_on_admin_edit' => 1,
					'managewp_on_sidebar' => 0,
					'managewp_order' => 5,
					'managewp_js_loc' => 'header',
					'managewp_type' => 'small',
					'stumble_on_content' => 0,
					'stumble_on_excerpt' => 0,
					'stumble_on_admin_edit' => 1,
					'stumble_on_sidebar' => 0,
					'stumble_order' => 6,
					'stumble_js_loc' => 'header',
					'stumble_badge' => 1,
					'pin_on_content' => 0,
					'pin_on_excerpt' => 0,
					'pin_on_admin_edit' => 1,
					'pin_on_sidebar' => 0,
					'pin_order' => 7,
					'pin_js_loc' => 'header',
					'pin_button_lang' => 'en',
					'pin_button_shape' => 'rect',
					'pin_button_color' => 'gray',
					'pin_button_height' => 'small',
					'pin_count_layout' => 'beside',
					'pin_img_width' => 800,
					'pin_img_height' => 800,
					'pin_img_crop' => 0,
					'pin_caption' => 'both',
					'pin_cap_len' => 500,
					'tumblr_on_content' => 0,
					'tumblr_on_excerpt' => 0,
					'tumblr_on_admin_edit' => 1,
					'tumblr_on_sidebar' => 0,
					'tumblr_order' => 8,
					'tumblr_js_loc' => 'footer',
					'tumblr_button_style' => 'share_1',
					'tumblr_desc_len' => 300,
					'tumblr_photo' => 1,
					'tumblr_img_width' => 800,
					'tumblr_img_height' => 800,
					'tumblr_img_crop' => 0,
					'tumblr_caption' => 'both',
					'tumblr_cap_len' => 500,
					'inc_description' => 0,
					'inc_fb:admins' => 1,
					'inc_fb:app_id' => 1,
					'inc_og:locale' => 1,
					'inc_og:site_name' => 1,
					'inc_og:description' => 1,
					'inc_og:title' => 1,
					'inc_og:type' => 1,
					'inc_og:url' => 1,
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
					'plugin_tid' => '',
					'plugin_preserve' => 0,
					'plugin_debug' => 0,
					'plugin_filter_content' => 1,
					'plugin_filter_excerpt' => 0,
					'plugin_filter_lang' => 1,
					'plugin_shortcodes' => 1,
					'plugin_widgets' => 1,
					'plugin_auto_img_resize' => 1,
					'plugin_ignore_small_img' => 1,
					'plugin_slideshare_api' => 0,
					'plugin_vimeo_api' => 0,
					'plugin_wistia_api' => 0,
					'plugin_youtube_api' => 1,
					'plugin_add_to_post' => 1,
					'plugin_add_to_page' => 1,
					'plugin_add_to_attachment' => 1,
					'plugin_cf_vid_url' => '_format_video_embed',
					'plugin_verify_certs' => 0,
					'plugin_file_cache_hrs' => 0,
					'plugin_object_cache_exp' => 3600,
					'plugin_min_shorten' => 21,
					'plugin_google_api_key' => '',
					'plugin_google_shorten' => 0,
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
					'wp_cm_aim_name' => 'aim', 
					'wp_cm_aim_label' => 'AIM', 
					'wp_cm_aim_enabled' => 1,
					'wp_cm_jabber_name' => 'jabber', 
					'wp_cm_jabber_label' => 'Jabber / Google Talk', 
					'wp_cm_jabber_enabled' => 1,
					'wp_cm_yim_name' => 'yim',
					'wp_cm_yim_label' => 'Yahoo IM', 
					'wp_cm_yim_enabled' => 1,
				),
				'site_defaults' => array(
					'options_version' => '',
					'plugin_version' => '',
					'plugin_tid' => '',
					'plugin_tid:use' => 'default',
				),
				'preset' => array(
					'small_share_count' => array(
						'fb_button' => 'share',
						'fb_send' => 0,
						'fb_show_faces' => 0,
						'fb_action' => 'like',
						'fb_type' => 'button_count',
						'gp_action' => 'share',
						'gp_size' => 'medium',
						'gp_annotation' => 'bubble',
						'gp_expandto' => '',
						'twitter_size' => 'medium',
						'twitter_count' => 'horizontal',
						'linkedin_counter' => 'right',
						'linkedin_showzero' => 1,
						'managewp_type' => 'small',
						'pin_button_shape' => 'rect',
						'pin_button_height' => 'small',
						'pin_count_layout' => 'beside',
						'tumblr_button_style' => 'share_1',
						'stumble_badge' => 1,
					),
					'large_share_vertical' => array(
						'fb_button' => 'share',
						'fb_send' => 0,
						'fb_show_faces' => 0,
						'fb_action' => 'like',
						'fb_type' => 'box_count',
						'fb_layout' => 'box_count',
						'gp_action' => 'share',
						'gp_size' => 'tall',
						'gp_annotation' => 'vertical-bubble',
						'gp_expandto' => '',
						'twitter_size' => 'medium',
						'twitter_count' => 'vertical',
						'linkedin_counter' => 'top',
						'linkedin_showzero' => '1',
						'managewp_type' => 'big',
						'pin_button_shape' => 'rect',
						'pin_button_height' => 'large',
						'pin_count_layout' => 'above',
						'tumblr_button_style' => 'share_2',
						'stumble_badge' => 5,
					),
				),
				'pre' => array(
					'facebook' => 'fb', 
					'gplus' => 'gp',
					'twitter' => 'twitter',
					'linkedin' => 'linkedin',
					'managewp' => 'managewp',
					'pinterest' => 'pin',
					'stumbleupon' => 'stumble',
					'tumblr' => 'tumblr',
					'youtube' => 'yt',
					'skype' => 'skype',
				),
			),
			'wp' => array(				// wordpress
				'min_version' => '3.0',		// minimum wordpress version
				'cm' => array(
					'aim' => 'AIM',
					'jabber' => 'Jabber / Google Talk',
					'yim' => 'Yahoo IM',
				),
			),
			'url' => array(
				'readme' => 'http://plugins.svn.wordpress.org/nextgen-facebook/trunk/readme.txt',
				'changelog' => 'http://wordpress.org/plugins/nextgen-facebook/changelog/',
				'purchase' => 'http://surniaulula.com/extend/plugins/nextgen-facebook/',
				'codex' => 'http://surniaulula.com/codex/plugins/nextgen-facebook/',
				'faq' => 'http://surniaulula.com/codex/plugins/nextgen-facebook/faq/',
				'notes' => 'http://surniaulula.com/codex/plugins/nextgen-facebook/notes/',
				'feed' => 'http://feed.surniaulula.com/category/application/wordpress/wp-plugins/ngfb/feed/',
				'support' => 'http://wordpress.org/support/plugin/nextgen-facebook',
				'pro_support' => 'http://support.ngfb.surniaulula.com/',
				'pro_ticket' => 'http://ticket.ngfb.surniaulula.com/',
				'pro_update' => 'http://update.surniaulula.com/extend/plugins/nextgen-facebook/update/',
			),
			'follow' => array(
				'size' => 32,
				'src' => array(
					'facebook.png' => 'https://www.facebook.com/SurniaUlulaCom',
					'gplus.png' => 'https://plus.google.com/b/112667121431724484705/112667121431724484705/posts',
					'linkedin.png' => 'https://www.linkedin.com/in/jsmoriss',
					'twitter.png' => 'https://twitter.com/surniaululacom',
					'youtube.png' => 'https://www.youtube.com/user/SurniaUlulaCom',
					'feed.png' => 'http://feed.surniaulula.com/category/application/wordpress/wp-plugins/ngfb/feed/',
				),
			),
			'form' => array(
				'max_desc_hashtags' => 10,
				'max_media_items' => 20,
				'file_cache_hours' => array( 0, 1, 3, 6, 9, 12, 24, 36, 48, 72, 168 ),
				'tooltip_class' => 'sucom_tooltip',
			),
			'head' => array(
				'min_img_dim' => 200,
				'min_desc_len' => 156,
			),
			'sharing' => array(
				'show_on' => array( 
					'content' => 'Content', 
					'excerpt' => 'Excerpt', 
					'sidebar' => 'Sidebar', 
					'admin_edit' => 'Admin Edit',
				),
				'style' => array(
					'sharing' => 'Buttons Style',
					'content' => 'Content Style',
					'excerpt' => 'Excerpt Style',
					'sidebar' => 'Sidebar Style',
					'shortcode' => 'Shortcode Style',
					'widget' => 'Widget Style',
					'admin_edit' => 'Admin Edit Style',
				),
			),
		);
		private static $cf_filtered = false;

		public static function get_config( $idx = '' ) { 
			// remove the sharing libs if disabled
			if ( defined( 'NGFB_SOCIAL_SHARING_DISABLE' ) && NGFB_SOCIAL_SHARING_DISABLE ) {
				unset (
					self::$cf['lib']['submenu']['sharing'],
					self::$cf['lib']['submenu']['style'],
					self::$cf['lib']['shortcode']['sharing'],
					self::$cf['lib']['widget']['sharing'],
					self::$cf['lib']['util']['rewrite'],
					self::$cf['lib']['util']['shorten']
				);
				self::$cf['lib']['website'] = array();
			}
			if ( ! empty( $idx ) ) {
				if ( array_key_exists( $idx, self::$cf ) )
					return self::$cf[$idx];
				else return false;
			} else return self::$cf;
		}

		public static function set_constants( $plugin_filepath ) { 

			$cf = self::get_config();

			define( 'NGFB_FILEPATH', $plugin_filepath );						
			define( 'NGFB_PLUGINDIR', trailingslashit( plugin_dir_path( $plugin_filepath ) ) );
			define( 'NGFB_PLUGINBASE', plugin_basename( $plugin_filepath ) );
			define( 'NGFB_TEXTDOM', $cf['slug'] );
			define( 'NGFB_URLPATH', trailingslashit( plugins_url( '', $plugin_filepath ) ) );
			define( 'NGFB_NONCE', md5( NGFB_PLUGINDIR.'-'.$cf['version'] ) );

			/*
			 * Allow some constants to be pre-defined in wp-config.php
			 */

			if ( defined( 'NGFB_DEBUG' ) && 
				! defined( 'NGFB_HTML_DEBUG' ) )
					define( 'NGFB_HTML_DEBUG', NGFB_DEBUG );

			if ( ! defined( 'NGFB_CACHEDIR' ) )
				define( 'NGFB_CACHEDIR', NGFB_PLUGINDIR.'cache/' );

			if ( ! defined( 'NGFB_CACHEURL' ) )
				define( 'NGFB_CACHEURL', NGFB_URLPATH.'cache/' );

			if ( ! defined( 'NGFB_OPTIONS_NAME' ) )
				define( 'NGFB_OPTIONS_NAME', $cf['lca'].'_options' );

			if ( ! defined( 'NGFB_SITE_OPTIONS_NAME' ) )
				define( 'NGFB_SITE_OPTIONS_NAME', $cf['lca'].'_site_options' );

			if ( ! defined( 'NGFB_META_NAME' ) )
				define( 'NGFB_META_NAME', '_'.$cf['lca'].'_meta' );

			if ( ! defined( 'NGFB_MENU_PRIORITY' ) )
				define( 'NGFB_MENU_PRIORITY', '99.11' );

			if ( ! defined( 'NGFB_INIT_PRIORITY' ) )
				define( 'NGFB_INIT_PRIORITY', 13 );

			if ( ! defined( 'NGFB_HEAD_PRIORITY' ) )
				define( 'NGFB_HEAD_PRIORITY', 10 );

			if ( ! defined( 'NGFB_SOCIAL_PRIORITY' ) )
				define( 'NGFB_SOCIAL_PRIORITY', 100 );
			
			if ( ! defined( 'NGFB_FOOTER_PRIORITY' ) )
				define( 'NGFB_FOOTER_PRIORITY', 100 );
			
			if ( ! defined( 'NGFB_DEBUG_FILE_EXP' ) )
				define( 'NGFB_DEBUG_FILE_EXP', 300 );

			if ( ! defined( 'NGFB_CURL_USERAGENT' ) )
				define( 'NGFB_CURL_USERAGENT', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.7; rv:18.0) Gecko/20100101 Firefox/18.0' );

			if ( ! defined( 'NGFB_CURL_CAINFO' ) )
				define( 'NGFB_CURL_CAINFO', NGFB_PLUGINDIR.'share/curl/cacert.pem' );

			if ( ! defined( 'NGFB_TOPICS_LIST' ) )
				define( 'NGFB_TOPICS_LIST', NGFB_PLUGINDIR.'share/topics.txt' );
		}

		public static function require_libs( $plugin_filepath ) {
			
			$cf = self::get_config();
			$plugin_dir = NGFB_PLUGINDIR;

			require_once( $plugin_dir.'lib/com/util.php' );
			require_once( $plugin_dir.'lib/com/cache.php' );
			require_once( $plugin_dir.'lib/com/notice.php' );
			require_once( $plugin_dir.'lib/com/script.php' );
			require_once( $plugin_dir.'lib/com/style.php' );
			require_once( $plugin_dir.'lib/com/webpage.php' );
			require_once( $plugin_dir.'lib/com/opengraph.php' );

			require_once( $plugin_dir.'lib/check.php' );
			require_once( $plugin_dir.'lib/util.php' );
			require_once( $plugin_dir.'lib/options.php' );
			require_once( $plugin_dir.'lib/user.php' );
			require_once( $plugin_dir.'lib/postmeta.php' );
			require_once( $plugin_dir.'lib/media.php' );
			require_once( $plugin_dir.'lib/head.php' );

			if ( is_admin() ) {
				require_once( $plugin_dir.'lib/messages.php' );
				require_once( $plugin_dir.'lib/admin.php' );
				require_once( $plugin_dir.'lib/com/form.php' );
				require_once( $plugin_dir.'lib/ext/parse-readme.php' );
			} else require_once( $plugin_dir.'lib/functions.php' );

			if ( file_exists( $plugin_dir.'lib/sharing.php' ) &&
				( ! defined( 'NGFB_SOCIAL_SHARING_DISABLE' ) || 
					! NGFB_SOCIAL_SHARING_DISABLE ) )
						require_once( $plugin_dir.'lib/sharing.php' );

			if ( file_exists( $plugin_dir.'lib/opengraph.php' ) &&
				( ! defined( 'NGFB_OPEN_GRAPH_DISABLE' ) || ! NGFB_OPEN_GRAPH_DISABLE ) &&
				empty( $_SERVER['NGFB_OPEN_GRAPH_DISABLE'] ) )
					require_once( $plugin_dir.'lib/opengraph.php' );	// extends lib/com/opengraph.php

			// additional classes are loaded and extended by the pro addon construct
			if ( file_exists( $plugin_dir.'lib/pro/addon.php' ) )
				require_once( $plugin_dir.'lib/pro/addon.php' );

			add_action( 'ngfb_load_lib', array( 'NgfbConfig', 'load_lib' ), 10, 2 );
		}

		public static function load_lib( $sub, $id ) {
			if ( empty( $sub ) && ! empty( $id ) )
				$filepath = NGFB_PLUGINDIR.'lib/'.$id.'.php';
			elseif ( ! empty( self::$cf['lib'][$sub][$id] ) )
				$filepath = NGFB_PLUGINDIR.'lib/'.$sub.'/'.$id.'.php';
			else return false;
			if ( file_exists( $filepath ) )
				require_once( $filepath );
		}

	}
}

?>

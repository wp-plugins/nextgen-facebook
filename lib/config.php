<?php
/*
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.txt
Copyright 2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbConfig' ) ) {

	class NgfbConfig {

		private static $cf = array(
			'version' => '6.21.0',			// plugin version
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
					'social' => 'Social Sharing',
					'style' => 'Social Style',
					'about' => 'About',
				),
				'site_submenu' => array(
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
					'ngfb' => 'Ngfb',
				),
				'widget' => array(
					'social' => 'SocialSharing',
				),
				'pro' => array(
					'seo' => array(
						'aioseop' => 'All in One SEO Pack',
						'seou' => 'SEO Ultimate',
						'wpseo' => 'WordPress SEO',
					),
					'ecom' => array(
						'woocommerce' => 'WooCommerce',
						'marketpress' => 'MarketPress',
						'wpecommerce' => 'WP e-Commerce',
					),
					'forum' => array(
						'bbpress' => 'bbPress',
					),
					'social' => array(
						'buddypress' => 'BuddyPress',
					),
					'media' => array(
						'ngg' => 'NextGEN Gallery',
						'photon' => 'Jetpack Photon',
						'wistia' => 'Wistia Video API',
					),
					'util' => array(
						'rewrite' => 'URL Rewriter',
						'shorten' => 'URL Shortener',
					),
				),
			),
			'opt' => array(				// options
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
			'css' => array(				// filter with 'ngfb_style_tabs'
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
				'pro_codex' => 'http://codex.ngfb.surniaulula.com/',
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
				'min_img_width' => 200,
				'min_img_height' => 200,
				'min_desc_len' => 156,
			),
			'social' => array(
				'show_on' => array( 
					'the_content' => 'Content', 
					'the_excerpt' => 'Excerpt', 
					'admin_sharing' => 'Edit Post/Page',
				),
			),
			// original list from http://en.wikipedia.org/wiki/Category:Websites_by_topic
			'topics' => array(
				'Animation',
				'Architecture',
				'Art',
				'Automotive',
				'Aviation',
				'Chat',
				'Children\'s',
				'Comics',
				'Commerce',
				'Community',
				'Dance',
				'Dating',
				'Digital Media',
				'Documentary',
				'Download',
				'Economics',
				'Educational',
				'Employment',
				'Entertainment',
				'Environmental',
				'Erotica and Pornography',
				'Fashion',
				'File Sharing',
				'Food and Drink',
				'Fundraising',
				'Genealogy',
				'Health',
				'History',
				'Humor',
				'Law Enforcement',
				'Legal',
				'Literature',
				'Medical',
				'Military',
				'Nature',
				'News',
				'Nostalgia',
				'Parenting',
				'Pets',
				'Photography',
				'Political',
				'Religious',
				'Review',
				'Reward',
				'Route Planning',
				'Satirical',
				'Science Fiction',
				'Science',
				'Shock',
				'Social Networking',
				'Spiritual',
				'Sport',
				'Technology',
				'Travel',
				'Vegetarian',
				'Webmail',
				'Women\'s',
			),
		);
		private static $cf_filtered = false;

		public static function get_config( $idx = '' ) { 
			// remove the social sharing libs if disabled
			if ( defined( 'NGFB_SOCIAL_SHARING_DISABLE' ) && NGFB_SOCIAL_SHARING_DISABLE ) {
				unset (
					self::$cf['lib']['submenu']['social'],
					self::$cf['lib']['submenu']['style'],
					self::$cf['lib']['shortcode']['ngfb'],
					self::$cf['lib']['widget']['social'],
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

		}

		public static function require_libs( $plugin_filepath ) {
			
			$cf = self::get_config();
			$plugin_dir = NGFB_PLUGINDIR;

			require_once( $plugin_dir.'lib/com/functions.php' );
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
			require_once( $plugin_dir.'lib/style.php' );		// extends lib/com/style.php
			require_once( $plugin_dir.'lib/head.php' );

			if ( is_admin() ) {
				require_once( $plugin_dir.'lib/messages.php' );
				require_once( $plugin_dir.'lib/admin.php' );
				require_once( $plugin_dir.'lib/com/form.php' );
				require_once( $plugin_dir.'lib/ext/parse-readme.php' );
			} else require_once( $plugin_dir.'lib/functions.php' );

			if ( file_exists( $plugin_dir.'lib/social.php' ) &&
				( ! defined( 'NGFB_SOCIAL_SHARING_DISABLE' ) || 
					! NGFB_SOCIAL_SHARING_DISABLE ) )
						require_once( $plugin_dir.'lib/social.php' );

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

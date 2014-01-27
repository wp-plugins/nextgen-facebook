<?php
/*
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.txt
Copyright 2012-2014 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbSharing' ) ) {

	class NgfbSharing {

		protected $p;
		protected $website = array();

		public $sharing_css_min_file;
		public $sharing_css_min_url;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->sharing_css_min_file = NGFB_CACHEDIR.$this->p->cf['lca'].'-sharing-styles.min.css';
			$this->sharing_css_min_url = NGFB_CACHEURL.$this->p->cf['lca'].'-sharing-styles.min.css';
			$this->set_objects();

			add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_styles' ) );
			add_action( 'wp_head', array( &$this, 'add_header' ), NGFB_HEAD_PRIORITY );
			add_action( 'wp_footer', array( &$this, 'add_footer' ), NGFB_FOOTER_PRIORITY );

			$this->add_filter( 'get_the_excerpt' );
			$this->add_filter( 'the_excerpt' );
			$this->add_filter( 'the_content' );

			if ( is_admin() ) {
				add_action( 'add_meta_boxes', array( &$this, 'add_metaboxes' ) );
				$this->p->util->add_plugin_filters( $this, array( 
					'save_options' => 2,		// update the sharing css file
					'status_gpl_features' => 1,	// include sharing, shortcode, and widget status
					'status_pro_features' => 1,	// include social file cache status
					'tooltip_side' => 2,		// tooltip messages for side boxes
					'tooltip_plugin' => 2,		// tooltip messages for advanced settings
					'tooltip_postmeta' => 3,	// tooltip messages for post meta custom settings
				) );
			} else $this->p->debug->mark();
		}

		public function filter_save_options( $opts, $options_name ) {
			if ( $options_name === NGFB_OPTIONS_NAME )
				$this->update_sharing_css( $opts );
			return $opts;
		}

		public function filter_status_gpl_features( $features ) {
			if ( ! empty( $this->p->cf['lib']['submenu']['sharing'] ) )
				$features['Sharing Buttons'] = array( 'class' => $this->p->cf['lca'].'Sharing' );

			if ( ! empty( $this->p->cf['lib']['shortcode']['sharing'] ) )
				$features['Sharing Shortcode'] = array( 'class' => $this->p->cf['lca'].'ShortcodeSharing' );

			if ( ! empty( $this->p->cf['lib']['submenu']['style'] ) )
				$features['Sharing Stylesheet'] = array( 'status' => $this->p->options['buttons_use_social_css'] ? 'on' : 'off' );

			if ( ! empty( $this->p->cf['lib']['widget']['sharing'] ) )
				$features['Sharing Widget'] = array( 'class' => $this->p->cf['lca'].'WidgetSharing' );

			return $features;
		}

		public function filter_status_pro_features( $features ) {
			if ( ! empty( $this->p->cf['lib']['submenu']['style'] ) )
				$features['Social File Cache'] = array( 'status' => $this->p->is_avail['cache']['file'] ? 'on' : 'off' );

			return $features;
		}

		public function filter_tooltip_side( $text, $idx ) {
			switch ( $idx ) {
				case 'tooltip-side-sharing-buttons':
					$text = 'Social sharing features include the Open Graph+ '.$this->p->util->get_admin_url( 'sharing', 'Social Sharing' ).
					' and '.$this->p->util->get_admin_url( 'style', 'Social Style' ).' settings pages (aka social sharing buttons), 
					the Custom Settings / Social Sharing tab on Post or Page editing pages, along with the social sharing shortcode 
					and widget. All social sharing features can be disabled using one of the available PHP
					<a href="http://surniaulula.com/codex/plugins/nextgen-facebook/notes/constants/" target="_blank">constants</a>.';
					break;
				case 'tooltip-side-sharing-shortcode':
					$text = 'Support for shortcode(s) can be enabled / disabled on the '.
					$this->p->util->get_admin_url( 'advanced', 'Advanced settings page' ).'. Shortcodes are disabled by default
					to optimize WordPress performance and content processing.';
					break;
				case 'tooltip-side-sharing-stylesheet':
					$text = 'A stylesheet can be included on all webpages for the social sharing buttons. Enable or disable the
					addition of the stylesheet from the '.$this->p->util->get_admin_url( 'style', 'Social Style settings page' ).'.';
					break;
				case 'tooltip-side-sharing-widget':
					$text = 'The social sharing widget feature adds an \'NGFB Social Sharing\' widget in the WordPress Appearance - Widgets page.
					The widget can be used in any number of widget areas, to share the current webpage. The widget, along with all social
					sharing featured, can be disabled using an available 
					<a href="http://surniaulula.com/codex/plugins/nextgen-facebook/notes/constants/" target="_blank">constant</a>.';
					break;
				case 'tooltip-side-social-file-cache':
					$text = $this->p->cf['full_pro'].' can save social sharing images and JavaScript to a cache folder, 
					and provide URLs to these cached files instead of the originals. The current \'File Cache Expiry\'
					value, as defined on the '.$this->p->util->get_admin_url( 'advanced', 'Advanced settings page' ).', is '.
					$this->p->options['plugin_file_cache_hrs'].' Hours (the default value of 0 Hours disables the 
					file caching feature).';
					break;
				case 'tooltip-side-url-rewriter':
					$text = $this->p->cf['full_pro'].' can rewrite image URLs in meta tags, cached images and JavaScript, 
					and for social sharing buttons like Pinterest and Tumblr (which use encoded image URLs). 
					Rewriting image URLs can be an important part of optimizing page load speeds. See the \'Static Content URL(s)\'
					option on the '.$this->p->util->get_admin_url( 'advanced', 'Advanced settings page' ).' to enable URL rewriting.';
					break;
				case 'tooltip-side-url-shortener':
					$text = '<strong>When using the Twitter social sharing button provided by '.$this->p->cf['full_pro'].'</strong>, 
					the webpage URL (aka the <em>canonical</em> or <em>permalink</em> URL) within the Tweet, 
					can be shortened by one of the available URL shortening services. Enable URL shortening for Twitter
					from the '.$this->p->util->get_admin_url( 'sharing', 'Social Sharing' ).' settings page.';
					break;
			}
			return $text;
		}

		public function filter_tooltip_plugin( $text, $idx ) {
			switch ( $idx ) {
				/*
				 * 'API Keys' (URL Shortening) settings
				 */
				case 'tooltip-plugin_bitly_login':
					$text = 'The Bit.ly username for the following API key. If you don\'t already have one, see 
					<a href="https://bitly.com/a/your_api_key" target="_blank">Your Bit.ly API Key</a>.';
					break;
				case 'tooltip-plugin_bitly_api_key':
					$text = 'The Bit.ly API key for this website. If you don\'t already have one, see 
					<a href="https://bitly.com/a/your_api_key" target="_blank">Your Bit.ly API Key</a>.';
					break;
				case 'tooltip-plugin_google_api_key':
					$text = 'The Google BrowserKey for this website / project. If you don\'t already have one, visit
					<a href="https://cloud.google.com/console#/project" target="_blank">Google\'s Cloud Console</a>,
					create a new project for your website, and under the API &amp; auth - Registered apps, 
					register a new \'Web Application\' (name it \'NGFB Open Graph+\' for example), 
					and enter it\'s BrowserKey here.';
					break;
				case 'tooltip-plugin_google_shorten':
					$text = 'In order to use Google\'s URL Shortener for URLs in Tweets, you must turn on the 
					URL Shortener API from <a href="https://cloud.google.com/console#/project" 
					target="_blank">Google\'s Cloud Console</a>, under the API &amp; auth - APIs 
					menu options. Confirm that you have enabled Google\'s URL Shortener by checking 
					the \'Yes\' option here. You can then select the Google URL Shortener in the '.
					$this->p->util->get_admin_url( 'sharing', 'Twitter settings' ).'.';
					break;
				/*
				 * 'URL Rewrite' settings
				 */
				case 'tooltip-plugin_min_shorten':
					$text = 'URLs shorter than this length will not be shortened (default is '.$this->p->opt->get_defaults( 'plugin_min_shorten' ).').';
					break;
				case 'tooltip-plugin_cdn_urls':
					$text = 'Rewrite image URLs in the Open Graph, Rich Pin, and Twitter Card meta tags, encoded image URLs shared by social buttons 
					(like Pinterest and Tumblr), and cached social media files. Leave this option blank to disable the URL rewriting feature 
					(default is disabled). Wildcarding and multiple CDN hostnames are supported -- see the 
					<a href="http://surniaulula.com/codex/plugins/nextgen-facebook/notes/url-rewriting/" target="_blank">URL Rewriting</a> 
					notes for more information and examples.';
					break;
				case 'tooltip-plugin_cdn_folders':
					$text = 'A comma delimited list of patterns to match. These patterns must be present in the URL for the rewrite to take place 
					(the default value is "<em>wp-content, wp-includes</em>").';
					break;
				case 'tooltip-plugin_cdn_excl':
					$text = 'A comma delimited list of patterns to match. If these patterns are found in the URL, the rewrite will be skipped (the default value is blank).
					If you are caching social website images and JavaScript (see the <em>Social File Cache Expiry</em> option), 
					the URLs to this cached content will be rewritten as well (that\'s a good thing).
					To exclude the '.$this->p->cf['full'].' cache folder URLs from being rewritten, enter \'/nextgen-facebook/cache/\' as a value here.';
					break;
				case 'tooltip-plugin_cdn_not_https':
					$text = 'Skip rewriting URLs when using HTTPS (useful if your CDN provider does not offer HTTPS, for example).';
					break;
				case 'tooltip-plugin_cdn_www_opt':
					$text = 'The www hostname prefix (if any) in the WordPress site URL is optional (default is checked).';
					break;
			}
			return $text;
		}

		public function filter_tooltip_postmeta( $text, $idx, $atts ) {
			$ptn = empty( $atts['ptn'] ) ? 'Post' : $atts['ptn'];
			switch ( $idx ) {
				 case 'tooltip-postmeta-pin_desc':
					$text = 'A custom caption text, used by the Pinterest social sharing button, 
					for the custom Image ID, attached or featured image.';
				 	break;
				 case 'tooltip-postmeta-tumblr_img_desc':
				 	$text = 'A custom caption, used by the Tumblr social sharing button, 
					for the custom Image ID, attached or featured image.';
				 	break;
				 case 'tooltip-postmeta-tumblr_vid_desc':
					$text = 'A custom caption, used by the Tumblr social sharing button, 
					for the custom Video URL or embedded video.';
				 	break;
				 case 'tooltip-postmeta-twitter_desc':
				 	$text = 'A custom Tweet text for the Twitter social sharing button. 
					This text is in addition to any Twitter Card description.';
				 	break;
				 case 'tooltip-postmeta-buttons_disabled':
					$text = 'Disable all social sharing buttons (content, excerpt, widget, shortcode) for this '.$ptn.'.';
				 	break;
			}
			return $text;
		}

		public function wp_enqueue_styles( $hook ) {
			// only include sharing styles if option is checked
			if ( ! empty( $this->p->options['buttons_use_social_css'] ) ) {
				if ( ! file_exists( $this->sharing_css_min_file ) ) {
					$this->p->debug->log( 'updating '.$this->sharing_css_min_file );
					$this->update_sharing_css( $this->p->options );
				}
				if ( ! empty( $this->p->options['buttons_enqueue_social_css'] ) ) {
					$this->p->debug->log( 'wp_enqueue_style = '.$this->p->cf['lca'].'_sharing_buttons' );
					wp_register_style( $this->p->cf['lca'].'_sharing_buttons', $this->sharing_css_min_url, false, $this->p->cf['version'] );
					wp_enqueue_style( $this->p->cf['lca'].'_sharing_buttons' );
				} else {
					echo '<style type="text/css">';
					if ( $fh = @fopen( $this->sharing_css_min_file, 'rb' ) ) {
						echo fread( $fh, filesize( $this->sharing_css_min_file ) );
						fclose( $fh );
					}
					echo '</style>',"\n";
				}
			}
		}

		public function update_sharing_css( $opts ) {
			if ( ! empty( $opts['buttons_use_social_css'] ) ) {
				if ( ! $fh = @fopen( $this->sharing_css_min_file, 'wb' ) )
					$this->p->debug->log( 'Error opening '.$this->sharing_css_min_file.' for writing.' );
				else {
					$css_data = '';
					$style_tabs = apply_filters( $this->p->cf['lca'].'_style_tabs', $this->p->cf['style'] );
					foreach ( $style_tabs as $id => $name )
						$css_data .= $opts['buttons_css_'.$id];
					require_once ( NGFB_PLUGINDIR.'lib/ext/compressor.php' );
					$css_data = SuextMinifyCssCompressor::process( $css_data );
					fwrite( $fh, $css_data );
					fclose( $fh );
					$this->p->debug->log( 'updated css file '.$this->sharing_css_min_file );
				}
			} else $this->unlink_sharing_css();
		}

		public function unlink_sharing_css() {
			if ( file_exists( $this->sharing_css_min_file ) ) {
				if ( ! @unlink( $this->sharing_css_min_file ) )
					add_settings_error( NGFB_OPTIONS_NAME, 'cssnotrm', 
						'<b>'.$this->p->cf['uca'].' Error</b> : Error removing minimized stylesheet. 
							Does the web server have sufficient privileges?', 'error' );
			}
		}

		public function add_metaboxes() {
			if ( ! is_admin() )
				return;

			// is there at least one button enabled for the admin_sharing metabox?
			$have_buttons = false;
			foreach ( $this->p->cf['opt']['pre'] as $id => $pre ) {
				if ( ! empty( $this->p->options[$pre.'_on_admin_sharing'] ) ) {
					$have_buttons = true;
					break;
				}
			}
			if ( ! $have_buttons )
				return;

			// get the current object / post type
			if ( ( $obj = $this->p->util->get_the_object() ) === false ) {
				$this->p->debug->log( 'exiting early: invalid object type' );
				return;
			}
			$post_type = get_post_type_object( $obj->post_type );

			if ( ! empty( $this->p->options[ 'buttons_add_to_'.$post_type->name ] ) ) {
				// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
				add_meta_box( '_'.$this->p->cf['lca'].'_share', $this->p->cf['menu'].' Sharing', 
					array( &$this, 'show_admin_sharing' ), $post_type->name, 'side', 'high' );
			}
		}

		private function set_objects() {
			foreach ( $this->p->cf['lib']['website'] as $id => $name ) {
				do_action( $this->p->cf['lca'].'_load_lib', 'website', $id );
				$classname = __CLASS__.$id;
				if ( class_exists( $classname ) )
					$this->website[$id] = new $classname( $this->p );
			}
		}

		public function add_filter( $type = 'the_content' ) {
			add_filter( $type, array( &$this, 'filter_'.$type ), NGFB_SOCIAL_PRIORITY );
			$this->p->debug->log( 'filter for '.$type.' added' );
		}

		public function remove_filter( $type = 'the_content' ) {
			$rc = remove_filter( $type, array( &$this, 'filter_'.$type ), NGFB_SOCIAL_PRIORITY );
			$this->p->debug->log( 'filter for '.$type.' removed ('.( $rc  ? 'true' : 'false' ).')' );
			return $rc;
		}

		public function add_header() {
			echo $this->header_js();
			echo $this->get_js( 'header' );
			$this->p->debug->show_html( null, 'Debug Log' );
		}

		public function add_footer() {
			echo $this->get_js( 'footer' );
			$this->p->debug->show_html( null, 'Debug Log' );
		}

		public function filter_the_excerpt( $text ) {
			$id = $this->p->cf['lca'].' excerpt-buttons';
			$text = preg_replace_callback( '/(<!-- '.$id.' begin -->.*<!-- '.$id.' end -->)(<\/p>)?/Usi', 
				array( __CLASS__, 'remove_paragraph_tags' ), $text );
			return $text;
		}

		// callback for filter_the_excerpt()
		public function remove_paragraph_tags( $match = array() ) {
			if ( empty( $match ) || ! is_array( $match ) ) return;
			$text = empty( $match[1] ) ? '' : $match[1];
			$suff = empty( $match[2] ) ? '' : $match[2];
			$ret = preg_replace( '/(<\/*[pP]>|\n)/', '', $text );
			return $suff.$ret; 
		}

		public function filter_get_the_excerpt( $text ) {
			return $this->filter( $text, 'the_excerpt', $this->p->options );
		}

		public function filter_the_content( $text ) {
			return $this->filter( $text, 'the_content', $this->p->options );
		}

		public function filter( &$text, $type = 'the_content', &$opts = array() ) {
			if ( empty( $opts ) ) 
				$opts =& $this->p->options;

			// should we skip the sharing buttons for this content type or webpage?
			if ( is_admin() ) {
				if ( $type !== 'admin_sharing' ) {
					$this->p->debug->log( $type.' filter skipped: '.$type.' ignored with is_admin()'  );
					return $text;
				}
			} else {
				if ( ! is_singular() && empty( $opts['buttons_on_index'] ) ) {
					$this->p->debug->log( $type.' filter skipped: index page without buttons_on_index enabled' );
					return $text;
				} elseif ( is_front_page() && empty( $opts['buttons_on_front'] ) ) {
					$this->p->debug->log( $type.' filter skipped: front page without buttons_on_front enabled' );
					return $text;
				}
				if ( $this->is_disabled() ) {
					$this->p->debug->log( $type.' filter skipped: buttons disabled' );
					return $text;
				}
			}

			// is there at least one sharing button enabled?
			$enabled = false;
			foreach ( $this->p->cf['opt']['pre'] as $id => $pre ) {
				if ( ! empty( $opts[$pre.'_on_'.$type] ) ) {
					$enabled = true;
					break;
				}
			}
			if ( $enabled == false ) {
				$this->p->debug->log( $type.' filter exiting early: no buttons enabled' );
				return $text;
			}

			// get the post id for the transient cache salt
			if ( ( $obj = $this->p->util->get_the_object( true ) ) === false ) {
				$this->p->debug->log( 'exiting early: invalid object type' );
				return $text;
			}

			$html = false;
			if ( $this->p->is_avail['cache']['transient'] ) {
				// if the post id is 0, then add the sharing url to ensure a unique salt string
				$cache_salt = __METHOD__.'(lang:'.get_locale().'_post:'.$obj->ID.'_type:'.$type.
					( empty( $obj->ID ) ? '_sharing_url:'.$this->p->util->get_sharing_url( true ) : '' ).')';
				$cache_id = $this->p->cf['lca'].'_'.md5( $cache_salt );
				$cache_type = 'object cache';
				$this->p->debug->log( $cache_type.': '.$type.' html transient salt '.$cache_salt );
				$html = get_transient( $cache_id );
			}

			if ( $html !== false ) {
				$this->p->debug->log( $cache_type.': '.$type.' html retrieved from transient '.$cache_id );
			} else {
				// sort enabled sharing buttons by their preferred order
				$sorted_ids = array();
				foreach ( $this->p->cf['opt']['pre'] as $id => $pre )
					if ( ! empty( $opts[$pre.'_on_'.$type] ) )
						$sorted_ids[$opts[$pre.'_order'].'-'.$id] = $id;
				unset ( $id, $pre );
				ksort( $sorted_ids );

				$css_type = $atts['css_id'] = preg_replace( '/^(the_)/', '', $type ).'-buttons';
				$html = $this->get_html( $sorted_ids, $atts, $opts );
				if ( ! empty( $html ) ) {
					$html = '<!-- '.$this->p->cf['lca'].' '.$css_type.' begin -->'.
						'<div class="'.$this->p->cf['lca'].'-'.$css_type.'">'.$html.'</div>'.
						'<!-- '.$this->p->cf['lca'].' '.$css_type.' end -->';

					if ( ! empty( $cache_id ) ) {
						set_transient( $cache_id, $html, $this->p->cache->object_expire );
						$this->p->debug->log( $cache_type.': '.$type.' html saved to transient '.
							$cache_id.' ('.$this->p->cache->object_expire.' seconds)' );
					}
				}
			}

			$buttons_location = empty( $opts['buttons_location_'.$type] ) ? 'bottom' : $opts['buttons_location_'.$type];

			switch ( $buttons_location ) {
				case 'top' : 
					$text = $this->p->debug->get_html().$html.$text; 
					break;
				case 'bottom': 
					$text = $this->p->debug->get_html().$text.$html; 
					break;
				case 'both' : 
					$text = $this->p->debug->get_html().$html.$text.$html; 
					break;
			}
			return $text;
		}

		// get_html() is called by the widget, shortcode, function, and perhaps some filter hooks
		public function get_html( &$ids = array(), &$atts = array(), &$opts = array() ) {
			if ( empty( $opts ) ) 
				$opts =& $this->p->options;

			$html = '';
			$custom_opts = false;
			$filter_id = empty( $atts['filter_id'] ) ? '' : 
				preg_replace( '/[^a-z0-9\-_]/', '', $atts['filter_id'] );	// sanitize the filter name
			if ( ! empty( $filter_id ) )
				$custom_opts = apply_filters( $this->p->cf['lca'].'_sharing_html_'.$filter_id.'_options', $opts );

			foreach ( $ids as $id ) {
				$id = preg_replace( '/[^a-z]/', '', $id );	// sanitize the website object name
				if ( method_exists( $this->website[$id], 'get_html' ) )
					$html .= $custom_opts === false ? 
						$this->website[$id]->get_html( $atts, $opts ) :
						$this->website[$id]->get_html( $atts, $custom_opts );
			}
			if ( ! empty( $html ) ) 
				$html = '<div class="'.$this->p->cf['lca'].'-buttons">'.$html.'</div>';
			return $html;
		}

		// add javascript for enabled buttons in content, widget, shortcode, etc.
		public function get_js( $pos = 'header', $ids = array() ) {

			if ( ( $obj = $this->p->util->get_the_object() ) === false ) {
				$this->p->debug->log( 'exiting early: invalid object type' );
				return;
			}

			if ( ! is_admin() && is_singular() && $this->is_disabled() ) {
				$this->p->debug->log( 'exiting early: buttons disabled' );
				return;
			} elseif ( is_admin() && ( empty( $obj->filter ) || $obj->filter !== 'edit' ) ) {
				$this->p->debug->log( 'exiting early: admin non-editing page' );
				return;
			}

			// determine which (if any) sharing buttons are enabled
			// loop through the sharing button option prefixes (fb, gp, etc.)
			if ( empty( $ids ) ) {
				if ( class_exists( 'NgfbWidgetSharing' ) ) {
					$widget = new NgfbWidgetSharing();
		 			$widget_settings = $widget->get_settings();
				} else $widget_settings = array();

				foreach ( $this->p->cf['opt']['pre'] as $id => $pre ) {
					// check for enabled buttons on settings page
					if ( is_admin() && ! empty( $obj ) ) {
						if ( ! empty( $this->p->options[$pre.'_on_admin_sharing'] ) )
							$ids[] = $id;
					} else {
						if ( is_singular() 
							|| ( ! is_singular() && ! empty( $this->p->options['buttons_on_index'] ) ) 
							|| ( is_front_page() && ! empty( $this->p->options['buttons_on_front'] ) ) ) {
	
							// exclude buttons enabled for admin editing pages
							foreach ( SucomUtil::preg_grep_keys( '/^'.$pre.'_on_/', $this->p->options ) as $key => $val )
								if ( $key !== $pre.'_on_admin_sharing' && ! empty( $val ) )
									$ids[] = $id;
	
						}
						// check for enabled buttons in widget(s)
						foreach ( $widget_settings as $instance )
							if ( array_key_exists( $id, $instance ) && (int) $instance[$id] )
								$ids[] = $id;
					}
				}
				if ( empty( $ids ) ) {
					$this->p->debug->log( 'exiting early: no buttons enabled' );
					return;
				}
			}

			natsort( $ids );
			$ids = array_unique( $ids );
			$js = '<!-- '.$this->p->cf['lca'].' '.$pos.' javascript begin -->';

			if ( strpos( $pos, '-header' ) ) 
				$js_loc = 'header';
			elseif ( strpos( $pos, '-footer' ) ) 
				$js_loc = 'footer';
			else $js_loc = $pos;

			if ( ! empty( $ids ) ) {
				foreach ( $ids as $id ) {
					$id = preg_replace( '/[^a-z]/', '', $id );
					$opt_name = $this->p->cf['opt']['pre'][$id].'_js_loc';
					if ( method_exists( $this->website[$id], 'get_js' ) && 
						! empty( $this->p->options[$opt_name] ) && 
						$this->p->options[$opt_name] == $js_loc )
							$js .= $this->website[$id]->get_js( $pos );
				}
			}
			$js .= '<!-- '.$this->p->cf['lca'].' '.$pos.' javascript end -->';
			return $js;
		}

		public function header_js( $pos = 'id' ) {
			$lang = empty( $this->p->options['gp_lang'] ) ? 'en-US' : $this->p->options['gp_lang'];
			$lang = apply_filters( $this->p->cf['lca'].'_lang', $lang, SucomUtil::get_lang( 'gplus' ) );
			return '<script type="text/javascript" id="ngfb-header-script">
				window.___gcfg = { lang: "'.$lang.'" };
				function '.$this->p->cf['lca'].'_insert_js( script_id, url, async ) {
					if ( document.getElementById( script_id + "-js" ) ) return;
					var async = typeof async !== "undefined" ? async : true;
					var script_pos = document.getElementById( script_id );
					var js = document.createElement( "script" );
					js.id = script_id + "-js";
					js.async = async;
					js.type = "text/javascript";
					js.language = "JavaScript";
					js.src = url;
					script_pos.parentNode.insertBefore( js, script_pos );
				};</script>'."\n";
		}

		public function get_css( $css_name, &$atts = array(), $css_class_extra = '', $css_id_extra = '' ) {
			global $post;
			$css_class = $css_name.'-'.( empty( $atts['css_class'] ) ? 
				'button' : $atts['css_class'] );
			$css_id = $css_name.'-'.( empty( $atts['css_id'] ) ? 
				'button' : $atts['css_id'] );

			if ( ! empty( $css_class_extra ) ) 
				$css_class = $css_class_extra.' '.$css_class;
			if ( ! empty( $css_id_extra ) ) 
				$css_id = $css_id_extra.' '.$css_id;

			if ( is_singular() && ! empty( $post->ID ) ) 
				$css_id .= ' '.$css_id.'-post-'.$post->ID;

			return 'class="'.$css_class.'" id="'.$css_id.'"';
		}

		public function is_disabled() {
			global $post;
			if ( ! empty( $post ) ) {
				$post_type = $post->post_type;
				if ( $this->p->addons['util']['postmeta']->get_options( $post->ID, 'buttons_disabled' ) ) {
					$this->p->debug->log( 'found custom meta buttons disabled = true' );
					return true;
				} elseif ( ! empty( $post_type ) && empty( $this->p->options['buttons_add_to_'.$post_type] ) ) {
					$this->p->debug->log( 'sharing buttons disabled for post '.$post->ID.' of type '.$post_type );
					return true;
				}
			}
			return false;
		}

		public function show_admin_sharing( $post ) {
			$post_type = get_post_type_object( $post->post_type );	// since 3.0
			$post_type_name = ucfirst( $post_type->name );
			echo '<table class="sucom-setting side"><tr><td>';
			if ( get_post_status( $post->ID ) == 'publish' ) {
				$content = '';
				if ( ! empty( $this->p->cf['opt']['admin_sharing'] ) )
					$opts = array_merge( $this->p->options, $this->p->cf['opt']['admin_sharing'] );
				$this->add_header();
				echo $this->filter( $content, 'admin_sharing', $opts );
				$this->add_footer();
			} else echo '<p class="centered">The '.$post_type_name.' must be published<br/>before it can be shared.</p>';
			echo '</td></tr></table>';
		}
	}
}

?>

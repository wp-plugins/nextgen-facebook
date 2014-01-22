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
			$this->p->debug->mark();
			$this->sharing_css_min_file = NGFB_CACHEDIR.$this->p->cf['lca'].'-sharing-styles.min.css';
			$this->sharing_css_min_url = NGFB_CACHEURL.$this->p->cf['lca'].'-sharing-styles.min.css';
			$this->set_objects();

			add_action( 'wp_enqueue_scripts', array( &$this, 'wp_enqueue_styles' ) );
			add_action( 'wp_head', array( &$this, 'add_header' ), NGFB_HEAD_PRIORITY );
			add_action( 'wp_footer', array( &$this, 'add_footer' ), NGFB_FOOTER_PRIORITY );

			$this->add_filter( 'get_the_excerpt' );
			$this->add_filter( 'the_excerpt' );
			$this->add_filter( 'the_content' );

			if ( is_admin() )
				add_action( 'add_meta_boxes', array( &$this, 'add_metaboxes' ) );
			$this->p->util->add_plugin_filters( $this, array( 'save_options' => 2 ) );
		}

		public function wp_enqueue_styles( $hook ) {
			// only include sharing styles if option is checked and sharing features are not disabled
			if ( ! empty( $this->p->options['buttons_link_css'] ) ) {
				wp_register_style( $this->p->cf['lca'].'_sharing_buttons', $this->sharing_css_min_url, false, $this->p->cf['version'] );
				if ( ! file_exists( $this->sharing_css_min_file ) ) {
					$this->p->debug->log( 'updating '.$this->sharing_css_min_file );
					$this->update_sharing( $this->p->options );
				}
				$this->p->debug->log( 'wp_enqueue_style = '.$this->p->cf['lca'].'_sharing_buttons' );
				wp_enqueue_style( $this->p->cf['lca'].'_sharing_buttons' );
			}
		}

		public function filter_save_options( $opts, $options_name ) {
			if ( $options_name === NGFB_OPTIONS_NAME ) {
				if ( ! empty( $this->p->options['buttons_link_css'] ) ) {
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
			return $opts;
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
			// is there at least one sharing button enabled for the admin_sharing metabox?
			$add_admin_sharing = false;
			foreach ( $this->p->cf['opt']['pre'] as $id => $pre ) {
				if ( ! empty( $this->p->options[$pre.'_on_admin_sharing'] ) ) {
					$add_admin_sharing = true;
					break;
				}
			}
			// include the custom settings metabox on the editing page for that post type
			foreach ( $this->p->util->get_post_types( 'plugin' ) as $post_type ) {
				if ( ! empty( $this->p->options[ 'plugin_add_to_'.$post_type->name ] ) ) {
					if ( $add_admin_sharing === true ) {
						add_meta_box( '_'.$this->p->cf['lca'].'_share', $this->p->cf['menu'].' Sharing', 
							array( &$this, 'show_admin_sharing' ), $post_type->name, 'side', 'high' );
					}
					break;
				}
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
			$this->p->debug->args( array( 'text' => 'N/A', 'type' => $type, 'opts' => 'N/A' ) );
			if ( empty( $opts ) ) 
				$opts =& $this->p->options;

			// should we skip the sharing buttons for this content type or webpage?
			if ( is_admin() ) {
				if ( $type != 'admin_sharing' ) {
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

					if ( $this->p->is_avail['cache']['transient'] ) {
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

		public function get_html( &$ids = array(), &$atts = array(), &$opts = array() ) {
			if ( empty( $opts ) ) $opts = $this->p->options;
			$html = '';
			foreach ( $ids as $id ) {
				$id = preg_replace( '/[^a-z]/', '', $id );	// sanitize
				if ( method_exists( $this->website[$id], 'get_html' ) )
					$html .= $this->website[$id]->get_html( $atts, $opts );
			}
			if ( ! empty( $html ) ) 
				$html = '<div class="'.$this->p->cf['lca'].'-buttons">'.$html.'</div>';
			return $html;
		}

		// add javascript for enabled buttons in content and widget(s)
		public function get_js( $pos = 'footer', $ids = array() ) {
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
			if ( class_exists( 'NgfbWidgetSharing' ) ) {
				$widget = new NgfbWidgetSharing();
		 		$widget_settings = $widget->get_settings();
			} else $widget_settings = array();

			// determine which (if any) sharing buttons are enabled
			// loop through the sharing button option prefixes (fb, gp, etc.)
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
			unset ( $id, $pre );
			if ( empty( $ids ) ) {
				$this->p->debug->log( 'exiting early: no buttons enabled' );
				return;
			}
			natsort( $ids );
			$ids = array_unique( $ids );
			$js = '<!-- '.$this->p->cf['lca'].' '.$pos.' javascript begin -->';

			if ( preg_match( '/^pre/i', $pos ) ) $pos_section = 'header';
			elseif ( preg_match( '/^post/i', $pos ) ) $pos_section = 'footer';
			else $pos_section = $pos;

			if ( ! empty( $ids ) ) {
				foreach ( $ids as $id ) {
					$id = preg_replace( '/[^a-z]/', '', $id );
					$opt_name = $this->p->cf['opt']['pre'][$id].'_js_loc';
					if ( method_exists( $this->website[$id], 'get_js' ) && 
						! empty( $this->p->options[$opt_name] ) && 
						$this->p->options[$opt_name] == $pos_section )
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

		public function get_css( $css_name, $atts = array(), $css_class_extra = '', $css_id_extra = '' ) {
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
				if ( $this->p->meta->get_options( $post->ID, 'buttons_disabled' ) ) {
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

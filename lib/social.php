<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'ngfbSocial' ) ) {

	class ngfbSocial {

		protected $p;
		protected $website = array();

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
			$this->setup_vars();

			add_action( 'wp_head', array( &$this, 'add_header' ), NGFB_HEAD_PRIORITY );
			add_action( 'wp_footer', array( &$this, 'add_footer' ), NGFB_FOOTER_PRIORITY );

			$this->add_filter( 'get_the_excerpt' );
			$this->add_filter( 'the_excerpt' );
			$this->add_filter( 'the_content' );
		}

		private function setup_vars() {
			foreach ( $this->p->cf['lib']['website'] as $id => $name ) {
				$classname = $this->p->cf['lca'].'Social'.preg_replace( '/ /', '', $name );
				if ( class_exists( $classname ) )
					$this->website[$id] = new $classname( $this->p );
			}
			unset ( $id, $name );
		}

		public function add_filter( $type = 'the_content' ) {
			add_filter( $type, array( &$this, 'filter_'.$type ), NGFB_SOCIAL_PRIORITY );
			$this->p->debug->log( 'filter_'.$type.'() added' );
		}

		public function remove_filter( $type = 'the_content' ) {
			$rc = remove_filter( $type, array( &$this, 'filter_'.$type ), NGFB_SOCIAL_PRIORITY );
			$this->p->debug->log( 'filter_'.$type.'() removed = '.( $rc  ? 'true' : 'false' ) );
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
			$text = preg_replace_callback( '/(<!-- '.$id.' begin -->.*<!-- '.$id.' end -->)(<\/[pP]>)/Us', 
				function( $m ) { $r = preg_replace( '/(<\/*[pP]>|\n)/', '', $m[1] ); return $m[2].$r; }, $text );
			return $text;
		}

		public function filter_get_the_excerpt( $text ) {
			return $this->filter( $text, 'the_excerpt', $this->p->options );
		}

		public function filter_the_content( $text ) {
			return $this->filter( $text, 'the_content', $this->p->options );
		}

		public function filter( &$text, $type = 'the_content', &$opts = array() ) {
			if ( empty( $opts ) ) 
				$opts = $this->p->options;
			if ( ! is_admin() ) {
				if ( ! is_singular() && empty( $opts['buttons_on_index'] ) ) {
					$this->p->debug->log( $type.' filter skipped: index page without buttons_on_index enabled' );
					return $text;
				} elseif ( is_front_page() && empty( $opts['buttons_on_front'] ) ) {
					$this->p->debug->log( $type.' filter skipped: front page without buttons_on_front enabled' );
					return $text;
				} elseif ( $this->is_disabled() ) {
					$this->p->debug->log( $type.' filter skipped: buttons disabled' );
					return $text;
				}
			}
			$enabled = false;
			foreach ( $this->p->cf['opt']['pre'] as $id => $pre ) {
				if ( ! empty( $opts[$pre.'_on_'.$type] ) ) {
					$enabled = true;
					break;
				}
			}
			unset( $id, $pre );
			if ( $enabled == false ) {
				$this->p->debug->log( $type.' filter exiting early: no buttons enabled' );
				return $text;
			}
			// we should always have a unique post ID
			global $post;
			$html = false;

			if ( defined( 'NGFB_TRANSIENT_CACHE_DISABLE' ) && NGFB_TRANSIENT_CACHE_DISABLE )
				$this->p->debug->log( 'transient cache is disabled' );
			else {
				$cache_salt = __METHOD__.'(lang:'.get_locale().'_post:'.$post->ID.'_type:'.$type.')';
				$cache_id = $this->p->cf['lca'].'_'.md5( $cache_salt );
				$cache_type = 'object cache';
				$this->p->debug->log( $cache_type.': '.$type.' html transient salt '.$cache_salt );
				$html = get_transient( $cache_id );
			}

			if ( $html !== false )
				$this->p->debug->log( $cache_type.': '.$type.' html retrieved from transient '.$cache_id );
			else {
				// sort enabled social buttons by their prefered order
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

					if ( ! defined( 'NGFB_TRANSIENT_CACHE_DISABLE' ) || ! NGFB_TRANSIENT_CACHE_DISABLE ) {
						set_transient( $cache_id, $html, $this->p->cache->object_expire );
						$this->p->debug->log( $cache_type.': '.$type.' html saved to transient '.
							$cache_id.' ('.$this->p->cache->object_expire.' seconds)' );
					}
				}
			}
			if ( $type == 'admin_sharing' )
				$text = $this->p->debug->get_html().$text.$html; 
			elseif ( ! empty( $opts[ 'buttons_location_'.$type ] ) ) {
				switch ( $opts[ 'buttons_location_'.$type ] ) {
					case 'top' : 
						$text = $this->p->debug->get_html().$html.$text; 
						break;
					case 'bottom' : 
						$text = $this->p->debug->get_html().$text.$html; 
						break;
					case 'both' : 
						$text = $this->p->debug->get_html().$html.$text.$html; 
						break;
				}
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
			// is_singular = false on admin edit page
			if ( ! is_admin() && is_singular() && $this->p->social->is_disabled() ) {
				$this->p->debug->log( 'exiting early: buttons disabled' );
				return;
			}
			global $post;
			$widget = new ngfbWidgetSocialSharing();
		 	$widget_settings = $widget->get_settings();

			// determine which (if any) social buttons are enabled
			foreach ( $this->p->cf['opt']['pre'] as $id => $pre ) {
				// check for enabled buttons on settings page
				if ( is_admin() && ! empty( $post ) ) {
					if ( ! empty( $this->p->options[$pre.'_on_admin_sharing'] ) )
						$ids[] = $id;
				} else {
					if ( is_singular() 
						|| ( ! is_singular() && ! empty( $this->p->options['buttons_on_index'] ) ) 
						|| ( is_front_page() && ! empty( $this->p->options['buttons_on_front'] ) ) ) {
	
						if ( ! empty( $this->p->options[$pre.'_on_the_content'] ) 
							|| ! empty( $this->p->options[$pre.'_on_the_excerpt'] ) ) {
								$ids[] = $id;
						}
					}
					// check for enabled buttons in widget
					foreach ( $widget_settings as $instance ) {
						if ( array_key_exists( $id, $instance ) && (int) $instance[$id] )
							$ids[] = $id;
					}
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
			$lang = apply_filters( $this->p->cf['lca'].'_lang', $lang, $this->p->util->get_lang( 'gplus' ) );
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
				};</script>';
		}

		public function get_css( $css_name, $atts = array(), $css_class_other = '' ) {
			global $post;
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;

			$css_class = $css_name.'-'.( empty( $atts['css_class'] ) ? 
				'button' : $atts['css_class'] );

			if ( ! empty( $css_class_other ) ) 
				$css_class = $css_class_other.' '.$css_class;

			$css_id = $css_name.'-'.( empty( $atts['css_id'] ) ? 
				'button' : $atts['css_id'] );

			if ( $use_post == true && ! empty( $post ) ) 
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
					$this->p->debug->log( 'social buttons disabled for post type '.$post_type );
					return true;
				}
			}
			return false;
		}
	}

}
?>

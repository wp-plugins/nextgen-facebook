<?php
/*
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/

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

if ( ! class_exists( 'ngfbSocial' ) ) {

	class ngfbSocial {

		protected $ngfb;
		protected $website = array();

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->lognew();

			foreach ( $this->ngfb->website_libs as $id => $name ) {
				$classname = 'ngfbSocial' . preg_replace( '/ /', '', $name );
				$this->website[$id] = new $classname( $ngfb_plugin );
			}
			unset ( $id, $name );

			add_action( 'wp_head', array( &$this, 'add_header' ), NGFB_HEAD_PRIORITY );
			add_action( 'wp_footer', array( &$this, 'add_footer' ), NGFB_FOOTER_PRIORITY );

			$this->add_filter( 'the_excerpt' );
			$this->add_filter( 'the_content' );
		}

		public function add_filter( $type = 'the_content' ) {
			add_filter( $type, array( &$this, 'filter_' . $type ), NGFB_SOCIAL_PRIORITY );
			$this->ngfb->debug->log( 'this->filter_' . $type . '() added' );
		}

		public function remove_filter( $type = 'the_content' ) {
			$rc = remove_filter( $type, array( &$this, 'filter_'. $type ), NGFB_SOCIAL_PRIORITY );
			$this->ngfb->debug->log( 'this->filter_' . $type . '() removed = ' . ( $rc  ? 'true' : 'false' ) );
			return $rc;
		}

		public function add_header() {
			echo $this->get_js( 'header' );
			$this->ngfb->debug->show( null, 'Debug Log' );
		}

		public function add_footer() {
			echo $this->get_js( 'footer' );
			$this->ngfb->debug->show( null, 'Debug Log' );
		}

		public function filter_the_excerpt( $text ) {
			return $this->filter( $text, 'the_excerpt' );
		}

		public function filter_the_content( $text ) {
			return $this->filter( $text, 'the_content' );
		}

		public function filter( &$text, $type = 'the_content' ) {

			// don't add social buttons on index pages, unless buttons_on_index option is checked
			if ( ! is_singular() && empty( $this->ngfb->options['buttons_on_index'] ) )
				return $text;

			if ( $this->is_disabled() ) 
				return $text;

			$enabled = false;
			foreach ( $this->ngfb->social_prefix as $id => $opt_prefix )
				if ( ! empty( $this->ngfb->options[ $opt_prefix . '_on_' . $type ] ) ) {
					$enabled = true;
					break;
			}
			if ( $enabled == false ) {
				$this->ngfb->debug->log( 'exiting early for: no buttons enabled for ' . $type );
				return $text;
			}

			// we should always have a unique post ID
			global $post;
			$cache_salt = __METHOD__ . '(post:' . $post->ID . '_type:' . $type . ')';
			$cache_id = $this->ngfb->acronym . '_' . md5( $cache_salt );
			$cache_type = 'object cache';
			$html = get_transient( $cache_id );
			$this->ngfb->debug->log( $cache_type . ': ' . $type . ' html transient id salt "' . $cache_salt . '"' );

			if ( $html !== false ) {
				$this->ngfb->debug->log( $cache_type . ': ' . $type . ' html retrieved from transient for id "' . $cache_id . '"' );
			} else {
				$sorted_ids = array();
				foreach ( $this->ngfb->social_prefix as $id => $opt_prefix )
					if ( ! empty( $this->ngfb->options[ $opt_prefix . '_on_' . $type ] ) )
						$sorted_ids[ $this->ngfb->options[ $opt_prefix.'_order' ] . '-' . $id ] = $id;	// sort by number, then by name
				unset ( $id, $opt_prefix );
				ksort( $sorted_ids );

				$this->ngfb->debug->log( 'calling this->get_html()' );
				$html = $this->get_html( $sorted_ids );

				if ( ! empty( $html ) ) {
					$css_type = preg_replace( '/^(the_)/', '', $type );
					$html = "\n<!-- " . $this->ngfb->fullname . ' ' . $css_type . " buttons BEGIN -->\n" .
						'<div class="' . $this->ngfb->acronym . '-' . $css_type . "-buttons\">\n" . $html . "</div>\n" .
						'<!-- ' . $this->ngfb->fullname . ' ' . $css_type . " buttons END -->\n";

					set_transient( $cache_id, $html, $this->ngfb->cache->object_expire );
					$this->ngfb->debug->log( $cache_type . ': ' . $type . ' html saved to transient for id "' . 
						$cache_id . '" (' . $this->ngfb->cache->object_expire . ' seconds)' );
				}
			}
			if ( ! empty( $this->ngfb->options[ 'buttons_location_' . $type ] ) &&
				$this->ngfb->options[ 'buttons_location_' . $type ] == "top" )
					$text = $this->ngfb->debug->get() . $html . $text;
			else $text .= $this->ngfb->debug->get() . $html;
			return $text;
		}

		public function get_html( $ids = array(), $atts = array() ) {
			$html = '';
			foreach ( $ids as $id ) {
				$id = preg_replace( '/[^a-z]/', '', $id );
				$this->ngfb->debug->log( 'calling this->website[' . $id . ']->get_html()' );
				if ( method_exists( $this->website[$id], 'get_html' ) )
					$html .= $this->website[$id]->get_html( $atts );
			}
			if ( $html ) $html = "<div class=\"" . $this->ngfb->acronym . "-buttons\">$html</div>\n";
			return $html;
		}

		// add javascript for enabled buttons in content and widget(s)
		public function get_js( $pos = 'footer', $ids = array() ) {
			if ( empty( $ids ) ) {

				if ( $this->ngfb->social->is_disabled() ) return;

				$widget = new ngfbWidgetSocialSharing();
		 		$widget_settings = $widget->get_settings();

				// determine which (if any) social buttons are enabled
				foreach ( $this->ngfb->social_prefix as $id => $opt_prefix ) {

					// check for enabled buttons on settings page
					if ( ( ! empty( $this->ngfb->options[ $opt_prefix . '_on_the_content' ] ) || ! empty( $this->ngfb->options[ $opt_prefix . '_on_the_excerpt' ] ) ) && 
						( is_singular() || $this->ngfb->options['buttons_on_index'] ) )
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
			$this->ngfb->debug->log( $pos . ' ids = ' . implode( ', ', $ids ) );
			$js = "<!-- " . $this->ngfb->fullname . " " . $pos . " javascript BEGIN -->\n";
			$js .= $pos == 'header' ? $this->header_js() : '';	// always add the ngfb_header_js() javascript function

			if ( preg_match( '/^pre/i', $pos ) ) $pos_section = 'header';
			elseif ( preg_match( '/^post/i', $pos ) ) $pos_section = 'footer';
			else $pos_section = $pos;

			if ( ! empty( $ids ) ) {
				foreach ( $ids as $id ) {
					$id = preg_replace( '/[^a-z]/', '', $id );
					$opt_name = $this->ngfb->social_prefix[$id] . '_js_loc';
					$this->ngfb->debug->log( 'calling this->website[' . $id . ']->get_js()' );
					if ( method_exists( $this->website[$id], 'get_js' ) && 
						! empty( $this->ngfb->options[ $opt_name ] ) && 
						$this->ngfb->options[ $opt_name ] == $pos_section )
							$js .= $this->website[$id]->get_js( $pos );
				}
			}
			$js .= "<!-- " . $this->ngfb->fullname . " " . $pos . " javascript END -->\n";
			return $js;
		}

		public function header_js( $pos = 'id' ) {
			$lang = empty( $this->ngfb->options['gp_lang'] ) ? 'en-US' : $this->ngfb->options['gp_lang'];
			return '<script type="text/javascript" id="ngfb-header-script">
				window.___gcfg = { lang: "' .  $lang . '" };
				function ngfb_header_js( script_id, url, async ) {
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
				};' . "\n</script>\n";
		}

		protected function get_css( $css_name, $atts = array(), $css_class_other = '' ) {
			global $post;
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;

			$atts['css_class'] = empty( $atts['css_class'] ) ? 'button' : $atts['css_class'];
			$atts['css_class'] = $css_name . '-' . $atts['css_class'];
			if ( ! empty( $css_class_other ) ) 
				$atts['css_class'] = $css_class_other . ' ' . $atts['css_class'];

			$atts['css_id'] = empty( $atts['css_id'] ) ? 'button' : $atts['css_id'];
			$atts['css_id'] = $css_name . '-' . $atts['css_id'];
			if ( $use_post == true && ! empty( $post ) ) 
				$atts['css_id'] .= ' ' . $atts['css_id'] . '-post-' . $post->ID;

			return 'class="' . $atts['css_class'] . '" id="' . $atts['css_id'] . '"';
		}

		protected function get_first_attached_image_id( $post_id = '' ) {
			if ( ! empty( $post_id ) ) {
				$images = get_children( array( 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image') );
				foreach ( $images as $attachment ) return $attachment->ID;
			}
			return;
		}

		public function is_disabled() {
			global $post;
			if ( ! empty( $post ) && $this->ngfb->meta->get_options( $post->ID, 'buttons_disabled' ) ) {
				$this->ngfb->debug->log( 'found custom meta buttons disabled = true' );
				return true;
			}
			return false;
		}
	}

}
?>

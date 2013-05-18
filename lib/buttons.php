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

if ( ! class_exists( 'ngfbButtons' ) ) {

	class ngfbButtons {

		var $website = array();

		function __construct() {
			$this->load_libs();
			add_action( 'wp_head', array( &$this, 'add_header' ), NGFB_HEAD_PRIORITY );
			add_action( 'wp_footer', array( &$this, 'add_footer' ), NGFB_FOOTER_PRIORITY );
		}

		function load_libs() {
			global $ngfb;
			foreach ( $ngfb->social_nice_names as $filename => $classname ) {

				require_once ( dirname ( __FILE__ ) . '/websites/' . $filename . '.php' );

				$classname = 'ngfbWebSite' . $classname;
				$this->website[$filename] = new $classname();

				//$r = new ReflectionClass( $classname );
				//$this->website[$filename] = $r->newInstance();
			}
		}

		function add_header() {
			echo $this->get_js( 'header' );
		}

		function add_footer() {
			echo $this->get_js( 'footer' );
		}

		function get_html( $ids = array(), $atts = array() ) {
			global $ngfb, $post;
			$html = '';
			foreach ( $ids as $id ) {
				$id = preg_replace( '/[^a-z]/', '', $id );
				$ngfb->debug->push( 'calling this->website[' . $id . ']->get_html()' );
				if ( is_object( $this->website[$id] ) )
					$html .= $this->website[$id]->get_html( $atts );
			}
			if ( $html ) $html = "<div class=\"" . NGFB_SHORTNAME . "-buttons\">$html</div>\n";
			return $html;
		}

		// add javascript for enabled buttons in content and widget(s)
		function get_js( $pos = 'footer', $ids = array() ) {
			global $ngfb;
			if ( empty( $ids ) ) {

				// if using the Exclude Pages from Navigation plugin, skip social buttons on those pages
				if ( is_page() && $ngfb->is_excluded() ) return;

				$widget = new ngfbSocialButtonsWidget();
		 		$widget_settings = $widget->get_settings();

				foreach ( $ngfb->social_options_prefix as $id => $opt_prefix ) {

					// check for enabled buttons on settings page
					if ( $ngfb->options[$opt_prefix.'_enable'] 
						&& ( is_singular() || $ngfb->options['buttons_on_index'] ) )
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
			$ngfb->debug->push( $pos . ' ids = ' . implode( ', ', $ids ) );
			$js = "<!-- " . NGFB_FULLNAME . " " . $pos . " javascript BEGIN -->\n";
			$js .= $pos == 'header' ? $this->header_js() : '';

			if ( preg_match( '/^pre/i', $pos ) ) $pos_section = 'header';
			elseif ( preg_match( '/^post/i', $pos ) ) $pos_section = 'footer';
			else $pos_section = $pos;

			if ( ! empty( $ids ) ) {
				foreach ( $ids as $id ) {
					$id = preg_replace( '/[^a-z]/', '', $id );
					$opt_name = $ngfb->social_options_prefix[$id] . '_js_loc';
					$ngfb->debug->push( 'calling this->website[' . $id . ']->get_js()' );
					if ( is_object( $this->website[$id] ) && ! empty( $ngfb->options[ $opt_name ] ) && $ngfb->options[ $opt_name ] == $pos_section )
						$js .= $this->website[$id]->get_js( $pos );
				}
			}

			$js .= "<!-- " . NGFB_FULLNAME . " " . $pos . " javascript END -->\n";
			return $js;
		}

		function header_js( $pos = 'id' ) {
			global $ngfb;
			$lang = empty( $ngfb->options['gp_lang'] ) ? 'en-US' : $ngfb->options['gp_lang'];
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

		function get_cache_url( $url ) {
			global $ngfb;

			// facebook javascript sdk doesn't work when hosted locally
			if ( preg_match( '/connect.facebook.net/', $url ) ) return $url;

			// make sure the cache expiration is greater than 0 hours
			if ( empty( $ngfb->options['ngfb_file_cache_hrs'] ) ) return $url;

			return ( $ngfb->cdn_linker_rewrite( $ngfb->cache->get( $url ) ) );
		}

		function get_short_url( $url, $short = true ) {
			global $ngfb;
			if ( function_exists('curl_init') && ! empty( $short ) ) {
				$goo = new ngfbGoogl( $ngfb->options['ngfb_googl_api_key'] );
				$url = $goo->shorten( $url );
			}
			return $url;
		}

		function get_css( $css_name, $atts = array(), $css_class_other = '' ) {
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

		function get_first_attached_image_id( $post_id = '' ) {
			if ( ! empty( $post_id ) ) {
				$images = get_children( array( 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image') );
				foreach ( $images as $attachment ) return $attachment->ID;
			}
			return;
		}

	}

}
?>

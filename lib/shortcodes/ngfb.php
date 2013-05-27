<?php
/*
Copyright 2013 - Jean-Sebastien Morisset - http://surniaulula.com/

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

if ( ! class_exists( 'ngfbShortCodeNGFB' ) ) {

	class ngfbShortCodeNgfb {

		private $ngfb;
		private $name = 'ngfb';

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->lognew();
			$this->add();
		}

		public function add() {
			if ( ! empty( $this->ngfb->options[$this->name . '_enable_shortcode'] ) ) {
        			add_shortcode( $this->name, array( &$this, 'shortcode' ) );
				$this->ngfb->debug->log( '[' . $this->name . '] shortcode added' );
			}
		}

		public function remove() {
			if ( ! empty( $this->ngfb->options[$this->name . '_enable_shortcode'] ) ) {
				remove_shortcode( $this->name );
				$this->ngfb->debug->log( '[' . $this->name . '] shortcode removed' );
			}
		}

		public function shortcode( $atts, $content = null ) { 
			// using extract method here turns each key in the merged array into its own variable
			// $atts or the default array will not be modified after the call to shortcode_atts()
			extract( shortcode_atts( array( 
				'buttons' => null,
				'css_class' => 'button',
				'css_id' => 'shortcode',
			), $atts ) );

			global $post;
			$ids = array();
			$html = '';

			if ( ! empty( $buttons ) ) {
				$ids = array_map( 'trim', explode( ',', $buttons ) );	// trim white spaces during explode
				$cache_salt = __METHOD__ . '(post:' . $post->ID . '_buttons:' . ( implode( '_', $ids ) ) . '_css:' . $css_class . '_' . $css_id . ')';
				$cache_id = 'ngfb_' . md5( $cache_salt );
				$cache_type = 'object cache';
				$html = get_transient( $cache_id );
				$this->ngfb->debug->log( $cache_type . ' : shortcode transient id salt "' . $cache_salt . '"' );

				if ( $html !== false ) {
					$this->ngfb->debug->log( $cache_type . ' : html retrieved from transient for id "' . $cache_id . '"' );
				} else {
					$html .= "\n<!-- " . $this->ngfb->fullname . " shortcode BEGIN -->\n";
					$html .= $this->ngfb->social->get_js( 'pre-shortcode', $ids );
					$html .= "<div class=\"" . $this->ngfb->acronym . "-shortcode-buttons\">\n" . 
						$this->ngfb->social->get_html( $ids, array( 'css_class' => $css_class, 'css_id' => $css_id ) ) . "</div>\n";
					$html .= $this->ngfb->social->get_js( 'post-shortcode', $ids );
					$html .= "<!-- " . $this->ngfb->fullname . " shortcode END -->\n";

					set_transient( $cache_id, $html, $this->ngfb->cache->object_expire );
					$this->ngfb->debug->log( $cache_type . ' : html saved to transient for id "' . $cache_id . '" (' . $this->ngfb->cache->object_expire . ' seconds)');
				}
			}
			return $this->ngfb->debug->get() . $html;
		}
	}
}

?>

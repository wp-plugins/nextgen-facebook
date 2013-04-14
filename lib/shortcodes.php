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

if ( ! class_exists( 'ngfbShortCodes' ) ) {

	class ngfbShortCodes {

		function __construct() {
        		add_shortcode( 'ngfb', array( &$this, 'ngfb_shortcode' ) );
		}

		function ngfb_shortcode( $atts, $content = null ) { 
			// using extract method here turns each key in the merged array into its own variable
			// $atts or the default array will not be modified after the call to shortcode_atts()
			extract( shortcode_atts( array( 
				'buttons' => null,
				'css_class' => 'button',
				'css_id' => 'shortcode',
			), $atts ) );

			global $ngfb, $post;
			$ids = array();
			$shortcode_html = '';

			if ( ! empty( $buttons ) ) {
				$ids = array_map( 'trim', explode( ',', $buttons ) );	// trim white spaces during explode
				$cache_salt = __METHOD__ . '(post:' . $post->ID . '_buttons:' . ( implode( '_', $ids ) ) . '_css:' . $css_class . '_' . $css_id . ')';
				$cache_id = 'ngfb_' . md5( $cache_salt );
				$cache_type = 'object cache';
				$shortcode_html = get_transient( $cache_id );
				$ngfb->debug->push( $cache_type . ' : shortcode transient id salt "' . $cache_salt . '"' );

				if ( $shortcode_html !== false ) {
					$ngfb->debug->push( $cache_type . ' : shortcode_html retrieved from transient for id "' . $cache_id . '"' );
				} else {
					$shortcode_html .= "\n<!-- " . NGFB_LONGNAME . " shortcode BEGIN -->\n";
					$shortcode_html .= $ngfb->get_buttons_js( 'pre-shortcode', $ids );
					$shortcode_html .= "<div class=\"" . NGFB_SHORTNAME . "-shortcode-buttons\">\n" . 
						$ngfb->get_buttons_html( $ids, array( 'css_class' => $css_class, 'css_id' => $css_id ) ) . "</div>\n";
					$shortcode_html .= $ngfb->get_buttons_js( 'post-shortcode', $ids );
					$shortcode_html .= "<!-- " . NGFB_LONGNAME . " shortcode END -->\n";

					set_transient( $cache_id, $shortcode_html, $ngfb->cache->object_expire );
					$ngfb->debug->push( $cache_type . ' : shortcode_html saved to transient for id "' . $cache_id . '" (' . $ngfb->cache->object_expire . ' seconds)');
				}
			}
			return $ngfb->debug->get() . $shortcode_html;
		}
	}
}
?>

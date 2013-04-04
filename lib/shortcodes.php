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

			global $ngfb;
			$ids = array();
			$button_html = '';
			if ( ! empty( $atts['buttons'] ) ) {
				$ids = explode( ',', $buttons );
				$button_html .= $ngfb->get_buttons_js( 'pre-shortcode', $ids );
				$button_html .= "<div class=\"" . NGFB_SHORTNAME . "-shortcode-buttons\">\n" . 
					$ngfb->get_buttons_html( $ids, array( 'css_class' => $css_class, 'css_id' => $css_id ) ) . "</div>\n";
				$button_html .= $ngfb->get_buttons_js( 'post-shortcode', $ids );
			}
			return $button_html;
		}
	}
}

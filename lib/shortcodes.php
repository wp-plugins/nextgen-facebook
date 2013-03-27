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

if ( ! class_exists( 'ngfbShortCodes' ) ) {

	class ngfbShortCodes {

		function __construct() {
        		add_shortcode( 'ngfb', array( &$this, 'ngfb_shortcode' ) );
		}

		function ngfb_shortcode( $atts, $content = null ) { 
			extract( shortcode_atts( array( 
				'buttons' => null,
				'css_class' => 'button',
				'css_id' => 'shortcode',
			), $atts ) );

			global $post;
			$button_html = '';
			if ( ! empty( $atts['buttons'] ) ) {
				$button_ids = explode( ',', $atts['buttons'] );
				$button_html = ngfb_get_social_buttons( $button_ids, array( 'css_id' => $css_id ) );
			}
			return $button_html;
		}
	}
}

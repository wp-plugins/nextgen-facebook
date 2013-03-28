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

if ( preg_match( '#/nextgen-facebook/lib/'.basename(__FILE__).'#', $_SERVER['PHP_SELF'] ) ) 
	die( 'Sorry, you cannot execute the '.$_SERVER['PHP_SELF'].' webpage directly.' );

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

			if ( ! empty( $atts['buttons'] ) )
				return ngfb_get_social_buttons( explode( ',', $buttons ), 
					array( 'css_class' => $css_class, 'css_id' => $css_id ) );
			else return;
		}
	}
}

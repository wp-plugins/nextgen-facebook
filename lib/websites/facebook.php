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

if ( ! class_exists( 'ngfbWebSiteFacebook' ) ) {

	class ngfbWebSiteFacebook extends ngfbButtons {

		function __construct() {
		}

		function get_html( $atts = array() ) {
			global $ngfb, $post; 
			$html = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			if ( empty( $atts['url'] ) ) $atts['url'] = $ngfb->get_sharing_url( 'notrack', null, $use_post );
			$fb_send = $ngfb->options['fb_send'] ? 'true' : 'false';
			$fb_show_faces = $ngfb->options['fb_show_faces'] ? 'true' : 'false';

			switch ( $ngfb->options['fb_markup'] ) {
				case 'xfbml' :
					// XFBML
					$html = '
					<!-- Facebook Button -->
					<div ' . $this->get_css( 'facebook', $atts, 'fb-like' ) . '><fb:like 
						href="' . $atts['url'] . '" 
						send="' . $fb_send . '" 
						layout="' . $ngfb->options['fb_layout'] . '" 
						show_faces="' . $fb_show_faces . '" 
						font="' . $ngfb->options['fb_font'] . '" 
						action="' . $ngfb->options['fb_action'] . '" 
						colorscheme="' . $ngfb->options['fb_colorscheme'] . '"></fb:like></div>
					';
					break;
				case 'html5' :
				default :
					// HTML5
					$html = '
					<!-- Facebook Button -->
					<div ' . $this->get_css( 'facebook', $atts, 'fb-like' ) . '
						data-href="' . $atts['url'] . '"
						data-send="' . $fb_send . '" 
						data-layout="' . $ngfb->options['fb_layout'] . '" 
						data-width="' . $ngfb->options['fb_width'] . '" 
						data-show-faces="' . $fb_show_faces . '" 
						data-font="' . $ngfb->options['fb_font'] . '" 
						data-action="' . $ngfb->options['fb_action'] . '"
						data-colorscheme="' . $ngfb->options['fb_colorscheme'] . '"></div>
					';
					break;
			}
			$ngfb->debug->push( 'returning html (' . strlen( $html ) . ' chars)' );
			return $html;
		}
		
		function get_js( $pos = 'id' ) {
			global $ngfb; 
			$lang = empty( $ngfb->options['fb_lang'] ) ? 'en_US' : $ngfb->options['fb_lang'];
			return '<script type="text/javascript" id="facebook-script-' . $pos . '">
				ngfb_header_js( "facebook-script-' . $pos . '", "' . 
					$this->get_cache_url( 'https://connect.facebook.net/' . 
					$lang . '/all.js#xfbml=1&appId=' . $ngfb->options['og_app_id'] ) . '" );
			</script>' . "\n";
		}

	}

}
?>

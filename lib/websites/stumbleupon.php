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

if ( ! class_exists( 'ngfbWebSiteStumbleUpon' ) ) {

	class ngfbWebSiteStumbleUpon extends ngfbButtons {

		function __construct() {
		}

		function get_html( $atts = array() ) {
			global $ngfb, $post; 
			$html = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			if ( empty( $atts['url'] ) ) $atts['url'] = $ngfb->get_sharing_url( 'notrack', null, $use_post );
			if ( empty( $atts['stumble_badge'] ) ) $atts['stumble_badge'] = $ngfb->options['stumble_badge'];
			$html = '
				<!-- StumbleUpon Button -->
				<div ' . $this->get_css( 'stumbleupon', $atts, 'stumble-button' ) . '><su:badge 
					layout="' . $atts['stumble_badge'] . '" location="' . $atts['url'] . '"></su:badge></div>
			';
			$ngfb->debug->push( 'returning html (' . strlen( $html ) . ' chars)' );
			return $html;
		}

		function get_js( $pos = 'id' ) {
			return '<script type="text/javascript" id="stumbleupon-script-' . $pos . '">
				ngfb_header_js( "stumbleupon-script-' . $pos . '", "' . $this->get_cache_url( 'https://platform.stumbleupon.com/1/widgets.js' ) . '" );
			</script>' . "\n";
		}

	}

}
?>

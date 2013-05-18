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

if ( ! class_exists( 'ngfbWebSiteGooglePlus' ) ) {

	class ngfbWebSiteGooglePlus extends ngfbButtons {

		function __construct() {
		}

		function get_html( $atts = array() ) {
			global $ngfb, $post; 
			$html = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			if ( empty( $atts['url'] ) ) $atts['url'] = $ngfb->get_sharing_url( 'notrack', null, $use_post );
			$gp_class = $ngfb->options['gp_action'] == 'share' ? 'class="g-plus" data-action="share"' : 'class="g-plusone"';
			$html = '
				<!-- GooglePlus Button -->
				<div ' . $this->get_css( 'gplus', $atts, 'g-plusone-button' ) . '>
					<span '. $gp_class . ' 
						data-size="' . $ngfb->options['gp_size'] . '" 
						data-annotation="' . $ngfb->options['gp_annotation'] . '" 
						data-href="' . $atts['url'] . '"></span>
				</div>' . "\n";
			$ngfb->debug->push( 'returning html (' . strlen( $html ) . ' chars)' );
			return $html;
		}
		
		function get_js( $pos = 'id' ) {
			return '<script type="text/javascript" id="gplus-script-' . $pos . '">
				ngfb_header_js( "gplus-script-' . $pos . '", "' . $this->get_cache_url( 'https://apis.google.com/js/plusone.js' ) . '" );
			</script>' . "\n";
		}
		
	}

}
?>

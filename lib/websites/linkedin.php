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

if ( ! class_exists( 'ngfbSettingsLinkedIn' ) && class_exists( 'ngfbSettingsSocialSharing' ) ) {

	class ngfbSettingsLinkedIn extends ngfbSettingsSocialSharing {

		protected $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->lognew();
		}

		public function get_rows() {
			return array(
				'<th colspan="2" class="social">LinkedIn</th>',
				'<td colspan="2" style="height:5px;"></td>',
				'<th>Add to Excerpt Text</th><td>' . $this->ngfb->admin->form->get_checkbox( 'linkedin_on_the_excerpt' ) . '</td>',
				'<th>Add to Content Text</th><td>' . $this->ngfb->admin->form->get_checkbox( 'linkedin_on_the_content' ) . '</td>',
				'<th>Preferred Order</th><td>' . $this->ngfb->admin->form->get_select( 'linkedin_order', range( 1, count( $this->ngfb->social_prefix ) ), 'short' ) . '</td>',
				'<th>JavaScript in</th><td>' . $this->ngfb->admin->form->get_select( 'linkedin_js_loc', $this->js_locations ) . '</td>',
				'<th>Counter Mode</th><td>' . $this->ngfb->admin->form->get_select( 'linkedin_counter', 
					array( 
						'none' => '',
						'right' => 'Horizontal',
						'top' => 'Vertical',
					)
				) . '</td>',
				'<th>Show Zero in Counter</th><td>' . $this->ngfb->admin->form->get_checkbox( 'linkedin_showzero' ) . '</td>',
				'<td colspan="2"></td>',
				'<td colspan="2"></td>',
				'<td colspan="2"></td>',
				'<td colspan="2"></td>',
				'<td colspan="2"></td>',
			);
		}

	}
}

if ( ! class_exists( 'ngfbSocialLinkedIn' ) && class_exists( 'ngfbSocial' ) ) {

	class ngfbSocialLinkedIn extends ngfbSocial {

		protected $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->lognew();
		}

		public function get_html( $atts = array() ) {
			global $post; 
			$html = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			if ( empty( $atts['url'] ) ) $atts['url'] = $this->ngfb->util->get_sharing_url( 'notrack', null, $use_post );
			$html = '
				<!-- LinkedIn Button -->
				<div ' . $this->get_css( 'linkedin', $atts ) . '>
				<script type="IN/Share" data-url="' . $atts['url'] . '"';

			if ( ! empty( $this->ngfb->options['linkedin_counter'] ) ) 
				$html .= ' data-counter="' . $this->ngfb->options['linkedin_counter'] . '"';

			if ( ! empty( $this->ngfb->options['linkedin_showzero'] ) ) 
				$html .= ' data-showzero="true"';

			$html .= '></script></div>'."\n";
			$this->ngfb->debug->log( 'returning html (' . strlen( $html ) . ' chars)' );
			return $html;
		}
		
		public function get_js( $pos = 'id' ) {
			return  '<script type="text/javascript" id="linkedin-script-' . $pos . '">
				ngfb_header_js( "linkedin-script-' . $pos . '", "' . $this->ngfb->util->get_cache_url( 'https://platform.linkedin.com/in.js' ) . '" );
			</script>' . "\n";
		}

	}

}
?>

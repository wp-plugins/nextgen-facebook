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

if ( ! class_exists( 'ngfbAdminPinterest' ) && class_exists( 'ngfbAdmin' ) ) {

	class ngfbAdminPinterest extends ngfbAdmin {

		private $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
		}

		public function get_rows() {
			return array(
				'<th colspan="2" class="social">Pinterest</th>',
				'<td colspan="2" style="height:5px;"></td>',
				'<td colspan="2"><p>The Pinterest "Pin It" button will only appear on Posts and Pages with a <em>featured</em> or <em>attached</em> image.</p></td>',
				'<th>Add Button to Content</th><td>' . $this->ngfb->admin->form->get_checkbox( 'pin_enable' ) . '</td>',
				'<th>Preferred Order</th><td>' . $this->ngfb->admin->form->get_select( 'pin_order', range( 1, count( $this->ngfb->social_options_prefix ) ), 'short' ) . '</td>',
				'<th>JavaScript in</th><td>' . $this->ngfb->admin->form->get_select( 'pin_js_loc', $this->js_locations ) . '</td>',
				'<th>Pin Count Layout</th><td>' . $this->ngfb->admin->form->get_select( 'pin_count_layout', 
					array( 
						'horizontal' => 'Horizontal',
						'vertical' => 'Vertical',
						'none' => 'None',
					)
				) . '</td>',
				'<th>Featured Image Size to Share</th><td>' . $this->ngfb->admin->form->get_select_img_size( 'pin_img_size' ) . '</td>',
				'<th>Image Caption Text</th><td>' . $this->ngfb->admin->form->get_select( 'pin_caption', $this->captions ) . '</td>',
				'<th>Maximum Caption Length</th><td>' . $this->ngfb->admin->form->get_input( 'pin_cap_len', 'short' ) . ' Characters</td>',
			);
		}

	}
}

if ( ! class_exists( 'ngfbSocialPinterest' ) && class_exists( 'ngfbSocial' ) ) {

	class ngfbSocialPinterest extends ngfbSocial {

		private $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
		}

		public function get_html( $atts = array() ) {
			global $post; 
			$html = '';
			$query = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			if ( empty( $atts['url'] ) ) $atts['url'] = $this->ngfb->get_sharing_url( 'notrack', null, $use_post );
			if ( empty( $atts['size'] ) ) $atts['size'] = $this->ngfb->options['pin_img_size'];
			if ( empty( $atts['photo'] ) ) {
				if ( empty( $atts['pid'] ) ) {
					// allow on index pages only if in content (not a widget)
					if ( $use_post == true ) {
						if ( $this->ngfb->is_avail['postthumb'] == true && has_post_thumbnail( $post->ID ) ) {
							$atts['pid'] = get_post_thumbnail_id( $post->ID );
							$this->ngfb->debug->push( 'get_post_thumbnail_id() = ' . $atts['pid'] );
						} else {
							$atts['pid'] = $this->get_first_attached_image_id( $post->ID );
							$this->ngfb->debug->push( 'get_first_attached_image_id() = ' . $atts['pid'] );
						}
					}
				}
				if ( ! empty( $atts['pid'] ) ) {
					// if the post thumbnail id has the form ngg- then it's a NextGEN image
					if ( is_string( $atts['pid'] ) && substr( $atts['pid'], 0, 4 ) == 'ngg-' ) {
						$this->ngfb->debug->push( 'calling ngfb->get_ngg_image_src("' . $atts['pid'] . '", "' . $atts['size'] . '")' );
						list( $atts['photo'], $atts['width'], $atts['height'], 
							$atts['cropped'] ) = $this->ngfb->get_ngg_image_src( $atts['pid'], $atts['size'] );
					} else {
						$this->ngfb->debug->push( 'calling ngfb->get_attachment_image_src("' . $atts['pid'] . '", "' . $atts['size'] . '")' );
						list( $atts['photo'], $atts['width'], $atts['height'],
							$atts['cropped'] ) = $this->ngfb->get_attachment_image_src( $atts['pid'], $atts['size'] );
					}
				}
			}
			if ( empty( $atts['photo'] ) ) return;
			if ( empty( $atts['pin_count_layout'] ) ) $atts['pin_count_layout'] = $this->ngfb->options['pin_count_layout'];
			if ( empty( $atts['caption'] ) ) $atts['caption'] = $this->ngfb->get_caption( $this->ngfb->options['pin_caption'], $this->ngfb->options['pin_cap_len'], $use_post );

			$query .= 'url=' . urlencode( $atts['url'] );
			$query .= '&amp;media='. urlencode( $this->ngfb->cdn_linker_rewrite( $atts['photo'] ) );
			$query .= '&amp;description=' . urlencode( $this->ngfb->str_decode( $atts['caption'] ) );

			$html = '
				<!-- Pinterest Button -->
				<div ' . $this->get_css( 'pinterest', $atts ) . '><a 
					href="http://pinterest.com/pin/create/button/?' . $query . '" 
					class="pin-it-button" count-layout="' . $atts['pin_count_layout'] . '" 
					title="Share on Pinterest"><img border="0" alt="Pin It"
					src="' . $this->get_cache_url( 'https://assets.pinterest.com/images/PinExt.png' ) . '" /></a></div>
			';
			$this->ngfb->debug->push( 'returning html (' . strlen( $html ) . ' chars)' );
			return $html;
		}

		public function get_js( $pos = 'id' ) {
			return '<script type="text/javascript" id="pinterest-script-' . $pos . '">
				ngfb_header_js( "pinterest-script-' . $pos . '", "' . $this->get_cache_url( 'https://assets.pinterest.com/js/pinit.js' ) . '" );
			</script>' . "\n";
		}
		
	}

}
?>

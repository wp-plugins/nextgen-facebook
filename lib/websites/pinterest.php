<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbSettingsPinterest' ) && class_exists( 'ngfbSettingsSocialSharing' ) ) {

	class ngfbSettingsPinterest extends ngfbSettingsSocialSharing {

		protected $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
		}

		public function get_rows() {
			return array(
				'<td colspan="2"><p>The Pinterest "Pin It" button will only appear on Posts and Pages with a <em>featured</em> or <em>attached</em> image.</p></td>',
				'<th class="short">Add to Content Text</th><td>' . $this->ngfb->admin->form->get_checkbox( 'pin_on_the_content' ) . '</td>',
				'<th class="short">Add to Excerpt Text</th><td>' . $this->ngfb->admin->form->get_checkbox( 'pin_on_the_excerpt' ) . '</td>',
				'<th class="short">Preferred Order</th><td>' . $this->ngfb->admin->form->get_select( 'pin_order', range( 1, count( $this->ngfb->social_prefix ) ), 'short' ) . '</td>',
				'<th class="short">JavaScript in</th><td>' . $this->ngfb->admin->form->get_select( 'pin_js_loc', $this->js_locations ) . '</td>',
				'<th class="short">Pin Count Layout</th><td>' . $this->ngfb->admin->form->get_select( 'pin_count_layout', 
					array( 
						'none' => '',
						'horizontal' => 'Horizontal',
						'vertical' => 'Vertical',
					)
				) . '</td>',
				'<th class="short">Image Size to Share</th><td>' . $this->ngfb->admin->form->get_select_img_size( 'pin_img_size' ) . '</td>',
				'<th class="short">Image Caption Text</th><td>' . $this->ngfb->admin->form->get_select( 'pin_caption', $this->captions ) . '</td>',
				'<th class="short">Caption Length</th><td>' . $this->ngfb->admin->form->get_input( 'pin_cap_len', 'short' ) . ' Characters or less</td>',
			);
		}

	}
}

if ( ! class_exists( 'ngfbSocialPinterest' ) && class_exists( 'ngfbSocial' ) ) {

	class ngfbSocialPinterest extends ngfbSocial {

		protected $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
		}

		public function get_html( $atts = array() ) {
			global $post; 
			$html = '';
			$query = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			if ( empty( $atts['url'] ) ) $atts['url'] = $this->ngfb->util->get_sharing_url( 'notrack', null, $use_post );
			if ( empty( $atts['size'] ) ) $atts['size'] = $this->ngfb->options['pin_img_size'];
			if ( empty( $atts['photo'] ) ) {
				if ( empty( $atts['pid'] ) ) {
					// allow on index pages only if in content (not a widget)
					if ( $use_post == true ) {
						if ( $this->ngfb->is_avail['postthumb'] == true && has_post_thumbnail( $post->ID ) ) {
							$atts['pid'] = get_post_thumbnail_id( $post->ID );
							$this->ngfb->debug->log( 'get_post_thumbnail_id() = ' . $atts['pid'] );
						} else {
							$atts['pid'] = $this->get_first_attached_image_id( $post->ID );
							$this->ngfb->debug->log( 'get_first_attached_image_id() = ' . $atts['pid'] );
						}
					}
				}
				if ( ! empty( $atts['pid'] ) ) {
					// if the post thumbnail id has the form ngg- then it's a NextGEN image
					if ( is_string( $atts['pid'] ) && substr( $atts['pid'], 0, 4 ) == 'ngg-' ) {
						$this->ngfb->debug->log( 'calling ngfb->media->get_ngg_image_src("' . 
							$atts['pid'] . '", "' . $atts['size'] . '", false)' );
						list( $atts['photo'], $atts['width'], $atts['height'], 
							$atts['cropped'] ) = $this->ngfb->media->get_ngg_image_src( $atts['pid'], $atts['size'], false );
					} else {
						$this->ngfb->debug->log( 'calling ngfb->media->get_attachment_image_src("' . 
							$atts['pid'] . '", "' . $atts['size'] . '", false)' );
						list( $atts['photo'], $atts['width'], $atts['height'],
							$atts['cropped'] ) = $this->ngfb->media->get_attachment_image_src( $atts['pid'], $atts['size'], false );
					}
				}
			}
			if ( empty( $atts['photo'] ) ) return;
			if ( empty( $atts['pin_count_layout'] ) ) $atts['pin_count_layout'] = $this->ngfb->options['pin_count_layout'];
			if ( empty( $atts['caption'] ) ) $atts['caption'] = $this->ngfb->webpage->get_caption( $this->ngfb->options['pin_caption'], $this->ngfb->options['pin_cap_len'], $use_post );

			$query .= 'url=' . urlencode( $atts['url'] );
			$query .= '&amp;media='. urlencode( $atts['photo'] );
			$query .= '&amp;description=' . urlencode( $this->ngfb->util->decode( $atts['caption'] ) );

			$html = '
				<!-- Pinterest Button -->
				<div ' . $this->get_css( 'pinterest', $atts ) . '><a 
					href="http://pinterest.com/pin/create/button/?' . $query . '" 
					class="pin-it-button" count-layout="' . $atts['pin_count_layout'] . '" 
					title="Share on Pinterest"><img border="0" alt="Pin It"
					src="' . $this->ngfb->util->get_cache_url( 'https://assets.pinterest.com/images/PinExt.png' ) . '" /></a></div>
			';
			$this->ngfb->debug->log( 'returning html (' . strlen( $html ) . ' chars)' );
			return $html;
		}

		public function get_js( $pos = 'id' ) {
			return '<script type="text/javascript" id="pinterest-script-' . $pos . '">
				ngfb_header_js( "pinterest-script-' . $pos . '", "' . $this->ngfb->util->get_cache_url( 'https://assets.pinterest.com/js/pinit.js' ) . '" );
			</script>' . "\n";
		}
		
	}

}
?>

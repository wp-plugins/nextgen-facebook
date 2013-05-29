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

if ( ! class_exists( 'ngfbSettingsTumblr' ) && class_exists( 'ngfbSettingsSocialSharing' ) ) {

	class ngfbSettingsTumblr extends ngfbSettingsSocialSharing {

		protected $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->lognew();
		}

		public function get_rows() {
			$buttons = '<div class="btn_wizard_row clearfix" id="button_styles">' . "\n";
			foreach ( range( 1, 4 ) as $i ) {
				$buttons .= '<div class="btn_wizard_column share_' . $i . '">' . "\n";
				foreach ( array( '', 'T' ) as $t ) {
					$buttons .= '
						<div class="btn_wizard_example clearfix">
							<label for="share_' . $i . $t . '">
								<input type="radio" id="share_' . $i . $t . '" 
									name="' . $this->ngfb->admin->form->options_name . '[tumblr_button_style]" 
									value="share_' . $i . $t . '" ' . 
									checked( 'share_' . $i . $t, $this->ngfb->options['tumblr_button_style'], false ) . '/>
								<img src="http://platform.tumblr.com/v1/share_' . $i . $t . '.png" 
									height="20" class="share_button_image"/>
							</label>
						</div>
					';
				}
				$buttons .= '</div>' . "\n";
			}
			$buttons .= '</div>' . "\n";

			return array(
				'<td colspan="2"><p>The tumblr button shares a <em>featured</em> or <em>attached</em> image (when prioritized), embedded video, the content of <em>quote</em> format Posts, or the webpage link.</p></td>',
				'<th>Add to Content Text</th><td>' . $this->ngfb->admin->form->get_checkbox( 'tumblr_on_the_content' ) . '</td>',
				'<th>Add to Excerpt Text</th><td>' . $this->ngfb->admin->form->get_checkbox( 'tumblr_on_the_excerpt' ) . '</td>',
				'<th>Preferred Order</th><td>' . $this->ngfb->admin->form->get_select( 'tumblr_order', range( 1, count( $this->ngfb->social_prefix ) ), 'short' ) . '</td>',
				'<th>JavaScript in</th><td>' . $this->ngfb->admin->form->get_select( 'tumblr_js_loc', $this->js_locations ) . '</td>',
				'<th>Button Style</th><td>' . $buttons . '</td>',
				'<th><u>Link</u> Description Length</th><td>' . $this->ngfb->admin->form->get_input( 'tumblr_desc_len', 'short' ) . ' Characters or less</td>',
				'<th>Prioritize Images</th><td>' . $this->ngfb->admin->form->get_checkbox( 'tumblr_photo' ) . '</td>',
				'<th>Image Size to Share</th><td>' . $this->ngfb->admin->form->get_select_img_size( 'tumblr_img_size' ) . '</td>',
				'<th>Image and Video Caption Text</th><td>' . $this->ngfb->admin->form->get_select( 'tumblr_caption', $this->captions ) . '</td>',
				'<th>Caption Length</th><td>' . $this->ngfb->admin->form->get_input( 'tumblr_cap_len', 'short' ) . ' Characters or less</td>',
			);
		}

	}
}

if ( ! class_exists( 'ngfbSocialTumblr' ) && class_exists( 'ngfbSocial' ) ) {

	class ngfbSocialTumblr extends ngfbSocial {

		protected $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->lognew();
		}

		public function get_html( $atts = array() ) {
			global $post; 
			$html = '';
			$query = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			if ( empty( $atts['url'] ) ) $atts['url'] = $this->ngfb->util->get_sharing_url( 'notrack', null, $use_post );
			if ( empty( $atts['tumblr_button_style'] ) ) $atts['tumblr_button_style'] = $this->ngfb->options['tumblr_button_style'];
			if ( empty( $atts['size'] ) ) $atts['size'] = $this->ngfb->options['tumblr_img_size'];

			// only use featured image if 'tumblr_photo' option allows it
			if ( empty( $atts['photo'] ) && $this->ngfb->options['tumblr_photo'] ) {
				if ( empty( $atts['pid'] ) ) {
					// allow on index pages only if in content (not a widget)
					if ( $use_post == true ) {
						if ( $this->ngfb->is_avail['postthumb'] == true && has_post_thumbnail( $post->ID ) )
							$atts['pid'] = get_post_thumbnail_id( $post->ID );
						else $atts['pid'] = $this->get_first_attached_image_id( $post->ID );
					}
				}
				if ( ! empty( $atts['pid'] ) ) {
					// if the post thumbnail id has the form ngg- then it's a NextGEN image
					if ( is_string( $atts['pid'] ) && substr( $atts['pid'], 0, 4 ) == 'ngg-' ) {
						list( $atts['photo'], $atts['width'], $atts['height'], 
							$atts['cropped'] ) = $this->ngfb->media->get_ngg_image_src( $atts['pid'], $atts['size'] );
					} else {
						list( $atts['photo'], $atts['width'], $atts['height'],
							$atts['cropped'] ) = $this->ngfb->media->get_attachment_image_src( $atts['pid'], $atts['size'] );
					}
				}
			}

			if ( empty( $atts['photo'] ) && empty( $atts['embed'] ) ) {
				// allow on index pages only if in content (not a widget)
				if ( $use_post == true ) {
					if ( ! empty( $post ) && ! empty( $post->post_content ) ) {
						$videos = array();
						$videos = $this->ngfb->head->og->get_content_videos( 1 );	// get the first video, if any
						if ( ! empty( $videos[0]['og:video'] ) ) 
							$atts['embed'] = $videos[0]['og:video'];
					}
				}
			}

			if ( empty( $atts['photo'] ) && empty( $atts['embed'] ) && empty( $atts['quote'] ) ) {
				// allow on index pages only if in content (not a widget)
				if ( $use_post == true ) {
					if ( ! empty( $post ) && get_post_format( $post->ID ) == 'quote' ) 
						$atts['quote'] = $this->ngfb->webpage->get_quote();
				}
			}

			// we only need the caption / title / description for some cases
			if ( ! empty( $atts['photo'] ) || ! empty( $atts['embed'] ) ) {
				if ( empty( $atts['caption'] ) ) 
					$atts['caption'] = $this->ngfb->webpage->get_caption( $this->ngfb->options['tumblr_caption'], $this->ngfb->options['tumblr_cap_len'], $use_post );
			} else {
				if ( empty( $atts['title'] ) ) 
					$atts['title'] = $this->ngfb->webpage->get_title( null, null, $use_post);
				if ( empty( $atts['description'] ) ) 
					$atts['description'] = $this->ngfb->webpage->get_description( $this->ngfb->options['tumblr_desc_len'], '...', $use_post );
			}

			// define the button, based on what we have
			if ( ! empty( $atts['photo'] ) ) {
				$query .= 'photo?source='. urlencode( $this->ngfb->util->cdn_rewrite( $atts['photo'] ) );
				$query .= '&amp;clickthru=' . urlencode( $atts['url'] );
				$query .= '&amp;caption=' . urlencode( $this->ngfb->util->decode( $atts['caption'] ) );
			} elseif ( ! empty( $atts['embed'] ) ) {
				$query .= 'video?embed=' . urlencode( $atts['embed'] );
				$query .= '&amp;caption=' . urlencode( $this->ngfb->util->decode( $atts['caption'] ) );
			} elseif ( ! empty( $atts['quote'] ) ) {
				$query .= 'quote?quote=' . urlencode( $atts['quote'] );
				$query .= '&amp;source=' . urlencode( $this->ngfb->util->decode( $atts['title'] ) );
			} elseif ( ! empty( $atts['url'] ) ) {
				$query .= 'link?url=' . urlencode( $atts['url'] );
				$query .= '&amp;name=' . urlencode( $this->ngfb->util->decode( $atts['title'] ) );
				$query .= '&amp;description=' . urlencode( $this->ngfb->util->decode( $atts['description'] ) );
			}
			if ( empty( $query ) ) return;

			$html = '
				<!-- Tumblr Button -->
				<div ' . $this->get_css( 'tumblr', $atts ) . '><a href="http://www.tumblr.com/share/'. $query . '" 
					title="Share on Tumblr"><img border="0" alt="Share on Tumblr"
					src="' . $this->ngfb->util->get_cache_url( 'http://platform.tumblr.com/v1/' . $atts['tumblr_button_style'] . '.png' ) . '" /></a></div>
			';
			$this->ngfb->debug->log( 'returning html (' . strlen( $html ) . ' chars)' );
			return $html;
		}

		// the tumblr host does not have a valid SSL cert, and it's javascript does not work in async mode
		public function get_js( $pos = 'id' ) {
			return '<script type="text/javascript" id="tumblr-script-' . $pos . '"
				src="' . $this->ngfb->util->get_cache_url( 'http://platform.tumblr.com/v1/share.js' ) . '"></script>' . "\n";
		}
		
	}

}
?>

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

if ( ! class_exists( 'ngfbWebSiteTumblr' ) ) {

	class ngfbWebSiteTumblr extends ngfbButtons {

		private $ngfb;

		public function __construct( &$ngfb ) {
			$this->ngfb =& $ngfb;
		}

		public function get_lang() {
			return array();
		}

		public function get_html( $atts = array() ) {
			global $post; 
			$html = '';
			$query = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			if ( empty( $atts['url'] ) ) $atts['url'] = $this->ngfb->get_sharing_url( 'notrack', null, $use_post );
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
							$atts['cropped'] ) = $this->ngfb->get_ngg_image_src( $atts['pid'], $atts['size'] );
					} else {
						list( $atts['photo'], $atts['width'], $atts['height'],
							$atts['cropped'] ) = $this->ngfb->get_attachment_image_src( $atts['pid'], $atts['size'] );
					}
				}
			}

			if ( empty( $atts['photo'] ) && empty( $atts['embed'] ) ) {
				// allow on index pages only if in content (not a widget)
				if ( $use_post == true ) {
					if ( ! empty( $post ) && ! empty( $post->post_content ) ) {
						$videos = array();
						$videos = $this->ngfb->og->get_content_videos( 1 );	// get the first video, if any
						if ( ! empty( $videos[0]['og:video'] ) ) 
							$atts['embed'] = $videos[0]['og:video'];
					}
				}
			}

			if ( empty( $atts['photo'] ) && empty( $atts['embed'] ) && empty( $atts['quote'] ) ) {
				// allow on index pages only if in content (not a widget)
				if ( $use_post == true ) {
					if ( ! empty( $post ) && get_post_format( $post->ID ) == 'quote' ) 
						$atts['quote'] = $this->ngfb->get_quote();
				}
			}

			// we only need the caption / title / description for some cases
			if ( ! empty( $atts['photo'] ) || ! empty( $atts['embed'] ) ) {
				if ( empty( $atts['caption'] ) ) 
					$atts['caption'] = $this->ngfb->get_caption( $this->ngfb->options['tumblr_caption'], $this->ngfb->options['tumblr_cap_len'], $use_post );
			} else {
				if ( empty( $atts['title'] ) ) 
					$atts['title'] = $this->ngfb->get_title( null, null, $use_post);
				if ( empty( $atts['description'] ) ) 
					$atts['description'] = $this->ngfb->get_description( $this->ngfb->options['tumblr_desc_len'], '...', $use_post );
			}

			// define the button, based on what we have
			if ( ! empty( $atts['photo'] ) ) {
				$query .= 'photo?source='. urlencode( $this->ngfb->cdn_linker_rewrite( $atts['photo'] ) );
				$query .= '&amp;clickthru=' . urlencode( $atts['url'] );
				$query .= '&amp;caption=' . urlencode( $this->ngfb->str_decode( $atts['caption'] ) );
			} elseif ( ! empty( $atts['embed'] ) ) {
				$query .= 'video?embed=' . urlencode( $atts['embed'] );
				$query .= '&amp;caption=' . urlencode( $this->ngfb->str_decode( $atts['caption'] ) );
			} elseif ( ! empty( $atts['quote'] ) ) {
				$query .= 'quote?quote=' . urlencode( $atts['quote'] );
				$query .= '&amp;source=' . urlencode( $this->ngfb->str_decode( $atts['title'] ) );
			} elseif ( ! empty( $atts['url'] ) ) {
				$query .= 'link?url=' . urlencode( $atts['url'] );
				$query .= '&amp;name=' . urlencode( $this->ngfb->str_decode( $atts['title'] ) );
				$query .= '&amp;description=' . urlencode( $this->ngfb->str_decode( $atts['description'] ) );
			}
			if ( empty( $query ) ) return;

			$html = '
				<!-- Tumblr Button -->
				<div ' . $this->get_css( 'tumblr', $atts ) . '><a href="http://www.tumblr.com/share/'. $query . '" 
					title="Share on Tumblr"><img border="0" alt="Share on Tumblr"
					src="' . $this->get_cache_url( 'http://platform.tumblr.com/v1/' . $atts['tumblr_button_style'] . '.png' ) . '" /></a></div>
			';
			$this->ngfb->debug->push( 'returning html (' . strlen( $html ) . ' chars)' );
			return $html;
		}

		// the tumblr host does not have a valid SSL cert, and it's javascript does not work in async mode
		public function get_js( $pos = 'id' ) {
			return '<script type="text/javascript" id="tumblr-script-' . $pos . '"
				src="' . $this->get_cache_url( 'http://platform.tumblr.com/v1/share.js' ) . '"></script>' . "\n";
		}
		
	}

}
?>

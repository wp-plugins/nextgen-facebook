<?php
/*
Copyright 2012 - Jean-Sebastien Morisset - http://surniaulula.com/

This script is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This script is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details at
http://www.gnu.org/licenses/.
*/

if ( ! class_exists( 'ngfbButtons' ) ) {

	class ngfbButtons {

		function header_async_js() {
			return '<script type="text/javascript">
				function ngfbJavascript( d, s, id, url ) {
					var js, ngfb_js = d.getElementsByTagName( s )[0];
					if ( d.getElementById( id ) ) return;
					js = d.createElement( s );
					js.id = id;
					js.async = true;
					js.src = url;
					ngfb_js.parentNode.insertBefore( js, ngfb_js );
				};' . "\n</script>\n";
		}

		function __construct() {
		}

		/* 	StumbleUpon
		 *	-----------
		 */
		function stumbleupon_button( $attr = array() ) {
			global $ngfb, $post; 
			$button_html = '';
			if ( empty( $attr['url'] ) && empty( $post ) ) return;
			if ( empty( $attr['url'] ) ) $attr['url'] = get_permalink( $post->ID );
			if ( empty( $attr['stumble_badge'] ) ) $attr['stumble_badge'] = $ngfb->options['stumble_badge'];
			$button_html = '
				<!-- StumbleUpon Button -->
				<div class="stumble-button stumbleupon-button"><su:badge layout="' . $attr['stumble_badge'] . '" 
					location="' . $attr['url'] . '"></su:badge></div>
			';
			return $button_html;	
		}

		function stumbleupon_header() {
			return '<script type="text/javascript">ngfbJavascript( document, "script", 
				"stumbleupon", "https://platform.stumbleupon.com/1/widgets.js" );</script>' . "\n";
		}

		/*	Pinterest
		 *	---------
		 */
		function pinterest_button( $attr = array() ) {
			global $ngfb, $post; 
			$button_html = '';
			if ( empty( $attr['url'] ) && empty( $post ) ) return;
			if ( empty( $attr['url'] ) ) $attr['url'] = get_permalink( $post->ID );
			if ( empty( $attr['pin_count_layout'] ) ) $attr['pin_count_layout'] = $ngfb->options['pin_count_layout'];
			if ( empty( $attr['size'] ) ) $attr['size'] = $ngfb->options['pin_img_size'];
			if ( empty( $attr['caption'] ) ) $attr['caption'] = $ngfb->get_caption( $ngfb->options['pin_caption'], $ngfb->options['pin_cap_len'] );
			if ( empty( $attr['photo'] ) ) {
				if ( empty( $attr['pid'] ) && ! empty( $ngfb->is_active['postthumb'] ) && has_post_thumbnail( $post->ID ) ) {
					$attr['pid'] = get_post_thumbnail_id( $post->ID );
				}
				if ( ! empty( $attr['pid'] ) ) {
					// if the post thumbnail id has the form ngg- then it's a NextGEN image
					if ( is_string( $attr['pid'] ) && substr( $attr['pid'], 0, 4 ) == 'ngg-' ) {
						$attr['photo'] = $ngfb->get_ngg_url( $attr['pid'], $attr['size'] );
					} else {
						$out = wp_get_attachment_image_src( $attr['pid'], $attr['size'] );
						$attr['photo'] = $out[0];
					}
				}
			}
			// define the button, based on what we have
			if ( ! empty( $attr['photo'] ) ) {
				$button_html .= '?url=' . urlencode( $attr['url'] );
				$button_html .= '&amp;media='. urlencode( $ngfb->cdn_linker_rewrite( $attr['photo'] ) );
				$button_html .= '&amp;description=' . urlencode( $ngfb->str_decode( $attr['caption'] ) );
			}
			// if we have something, then complete the button code
			if ( ! empty( $button_html ) ) {
				$button_html = '
					<!-- Pinterest Button -->
					<div class="pinterest-button"><a href="http://pinterest.com/pin/create/button/' . $button_html . '" 
						class="pin-it-button" count-layout="' . $attr['pin_count_layout'] . '" 
						title="Share on Pinterest"><img border="0" alt="Pin It"
						src="http://assets.pinterest.com/images/PinExt.png" /></a></div>
				';
			}
			return $button_html;	
		}

		function pinterest_header() {
			return '<script type="text/javascript">ngfbJavascript( document, "script", 
				"pinterest", "https://assets.pinterest.com/js/pinit.js" );</script>' . "\n";
		}
		
		/*	tumblr
		 *	------
		 */
		function tumblr_button( $attr = array() ) {
			global $ngfb, $post; 
			$button_html = '';
			if ( empty( $attr['url'] ) && empty( $post ) ) return;
			if ( empty( $attr['url'] ) ) $attr['url'] = get_permalink( $post->ID );
			if ( empty( $attr['tumblr_button_style'] ) ) $attr['tumblr_button_style'] = $ngfb->options['tumblr_button_style'];
			if ( empty( $attr['size'] ) ) $attr['size'] = $ngfb->options['tumblr_img_size'];
			if ( empty( $attr['title'] ) ) $attr['title'] = $ngfb->get_title();
			if ( empty( $attr['caption'] ) ) $attr['caption'] = $ngfb->get_caption( $ngfb->options['tumblr_caption'], $ngfb->options['tumblr_cap_len'] );
			if ( empty( $attr['description'] ) ) $attr['description'] = $ngfb->get_description( $ngfb->options['tumblr_desc_len'], '...' );
			if ( empty( $attr['quote'] ) && ! empty( $post ) && get_post_format( $post->ID ) == 'quote' ) $attr['quote'] = $ngfb->get_quote();

			if ( empty( $attr['embed'] ) ) {
				$videos = array();
				$content_filtered = '';
				if ( ! empty( $post ) )
					$content_filtered = $ngfb->apply_content_filter( $post->post_content, 
						$ngfb->options['ngfb_filter_content'] );

				if ( ! empty( $content_filtered ) )
					$videos = $ngfb->get_videos_og( $content_filtered, 1 );	// get the first video, if any

				if ( ! empty( $videos[0]['og:video'] ) ) 
					$attr['embed'] = $videos[0]['og:video'];
			}
		
			// only use featured image if 'tumblr_photo' option allows it
			if ( empty( $attr['photo'] ) && $ngfb->options['tumblr_photo'] ) {
				if ( empty( $attr['pid'] ) && ! empty( $ngfb->is_active['postthumb'] ) && has_post_thumbnail( $post->ID ) )
					$attr['pid'] = get_post_thumbnail_id( $post->ID );
				
				if ( ! empty( $attr['pid'] ) ) {
					if ( is_string( $attr['pid'] ) && substr( $attr['pid'], 0, 4 ) == 'ngg-' ) {
						$attr['photo'] = $ngfb->get_ngg_url( $attr['pid'], $attr['size'] );
					} else {
						$out = wp_get_attachment_image_src( $attr['pid'], $attr['size'] );
						$attr['photo'] = $out[0];
					}
				}
			}
			
			// define the button, based on what we have
			if ( ! empty( $attr['photo'] ) ) {
				$button_html .= 'photo?source='. urlencode( $ngfb->cdn_linker_rewrite( $attr['photo'] ) );
				$button_html .= '&amp;caption=' . urlencode( $ngfb->str_decode( $attr['caption'] ) );
				$button_html .= '&amp;clickthru=' . urlencode( $attr['url'] );
			} elseif ( ! empty( $attr['embed'] ) ) {
				$button_html .= 'video?embed=' . urlencode( $attr['embed'] );
				$button_html .= '&amp;caption=' . urlencode( $ngfb->str_decode( $attr['caption'] ) );
			} elseif ( ! empty( $attr['quote'] ) ) {
				$button_html .= 'quote?quote=' . urlencode( $attr['quote'] );
				$button_html .= '&amp;source=' . urlencode( $ngfb->str_decode( $attr['title'] ) );
			} elseif ( ! empty( $attr['url'] ) ) {
				$button_html .= 'link?url=' . urlencode( $attr['url'] );
				$button_html .= '&amp;name=' . urlencode( $ngfb->str_decode( $attr['title'] ) );
				$button_html .= '&amp;description=' . urlencode( $ngfb->str_decode( $attr['description'] ) );
			}
			// if we have something, then complete the button code
			if ( $button_html ) {
				$button_html = '
					<!-- tumblr Button -->
					<div class="tumblr-button"><a href="http://www.tumblr.com/share/'. $button_html . '" 
						title="Share on Tumblr"><img border="0" alt="Share on Tumblr"
						src="http://platform.tumblr.com/v1/' . $attr['tumblr_button_style'] . '.png" /></a></div>
				';
			}
			return $button_html;
		}

		// the tumblr host have a valid SSL cert and it's javascript does not work in async mode
		function tumblr_footer() {
			return '<script type="text/javascript" src="http://platform.tumblr.com/v1/share.js"></script>' . "\n";
		}
		
		/*	Facebook
		 *	--------
		 */
		function facebook_button( $attr = array() ) {
			global $ngfb, $post; 
			if ( empty( $attr['url'] ) && empty( $post ) ) return;
			if ( empty( $attr['url'] ) ) $attr['url'] = get_permalink( $post->ID );
			$fb_send = $ngfb->options['fb_send'] ? 'true' : 'false';
			$fb_show_faces = $ngfb->options['fb_show_faces'] ? 'true' : 'false';
			return '
				<!-- Facebook Button -->
				<div class="facebook-button"><fb:like 
					href="' . $attr['url'] . '"
					send="' . $fb_send . '" 
					layout="' . $ngfb->options['fb_layout'] . '" 
					show_faces="' . $fb_show_faces . '" 
					font="' . $ngfb->options['fb_font'] . '" 
					action="' . $ngfb->options['fb_action'] . '"
					colorscheme="' . $ngfb->options['fb_colorscheme'] . '"></fb:like></div>
			';
		}
		
		function facebook_header() {
			global $ngfb; 
			return '<script type="text/javascript">ngfbJavascript( document, "script", 
				"facebook", "https://connect.facebook.net/en_US/all.js#xfbml=1&appId=' . $ngfb->options['og_app_id'] . '" );</script>' . "\n";
		}

		/*	Google+
		 *	-------
		 */
		function gplus_button( $attr = array() ) {
			global $ngfb, $post; 
			$button_html;
			if ( empty( $attr['url'] ) && empty( $post ) ) return;
			if ( empty( $attr['url'] ) ) $attr['url'] = get_permalink( $post->ID );
			$gp_class = $ngfb->options['gp_action'] == 'share' ? 'class="g-plus" data-action="share"' : 'class="g-plusone"';
			return '
				<!-- Google+ Button -->
				<div class="gplus-button g-plusone-button"><span '. $gp_class . ' 
				data-size="' . $ngfb->options['gp_size'] . '" 
				data-annotation="' . $ngfb->options['gp_annotation'] . '" 
				data-href="' . $attr['url'] . '"></span></div>
			';
		}
		
		function gplus_header() {
			return '<script type="text/javascript">ngfbJavascript( document, "script", 
				"gplus", "https://apis.google.com/js/plusone.js" );</script>' . "\n";
		}
		
		/*	Twitter
		 *	-------
		 */
		function twitter_button( $attr = array() ) {
			global $ngfb, $post; 
			if ( empty( $attr['url'] ) && empty( $post ) ) return;
			if ( empty( $attr['url'] ) ) $attr['url'] = get_permalink( $post->ID );

			$goo = new ngfbGoogl( $ngfb->options['ngfb_googl_api_key'] );
			if ( ! empty( $ngfb->options['twitter_shorten'] ) ) 
				$attr['url'] = $goo->shorten( $attr['url'] );
			$twitter_dnt = $ngfb->options['twitter_dnt'] ? 'true' : 'false';
			return '
				<!-- Twitter Button -->
				<div class="twitter-button">
				<a href="https://twitter.com/share" 
					class="twitter-share-button"
					data-url="' . $attr['url'] . '" 
					data-count="' . $ngfb->options['twitter_count'] . '" 
					data-size="' . $ngfb->options['twitter_size'] . '" 
					data-dnt="' . $twitter_dnt . '">Tweet</a></div>
			';
		}
		
		function twitter_header() {
			return '<script type="text/javascript">ngfbJavascript( document, "script", 
				"twitter", "https://platform.twitter.com/widgets.js" );</script>' . "\n";
		}
		
		/*	LinkedIn
		 *	--------
		 */
		function linkedin_button( $attr = array() ) {
			global $ngfb, $post; 
			$button_html;
			if ( empty( $attr['url'] ) && empty( $post ) ) return;
			if ( empty( $attr['url'] ) ) $attr['url'] = get_permalink( $post->ID );
			$linkedin_counter = $ngfb->options['linkedin_counter'];
			$button_html = '
				<!-- LinkedIn Button -->
				<div class="linkedin-button">
				<script type="IN/Share" data-url="' . $attr['url'] . '"';

			if ( $ngfb->options['linkedin_counter'] ) 
				$button_html .= ' data-counter="' . $ngfb->options['linkedin_counter'] . '"';

			$button_html .= '></script></div>'."\n";
			return $button_html;
		}
		
		function linkedin_header() {
			return  '<script type="text/javascript">ngfbJavascript( document, "script", 
				"linkedin", "https://platform.linkedin.com/in.js" );</script>' . "\n";
		}
		
	}
}
?>

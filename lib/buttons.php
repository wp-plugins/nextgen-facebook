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

		function __construct() {
		}

		function stumbleupon_button( &$attr = array() ) {
			global $ngfb, $post; 
			$button_html = '';
			if ( empty( $attr['stumble_badge'] ) ) $attr['stumble_badge'] = $ngfb->options['stumble_badge'];
			if ( empty( $attr['url'] ) ) $attr['url'] = get_permalink( $post->ID );
			$button_html = '
				<!-- StumbleUpon Button -->
				<div class="stumble-button stumbleupon-button"><su:badge layout="' . $attr['stumble_badge'] . '" 
					location="' . $attr['url'] . '"></su:badge></div>
			';
			return $button_html;	
		}

		function stumbleupon_footer() {
			return '
				<!-- StumbleUpon Javascript -->
				<script type="text/javascript">
				(
					function() { 
						var li = document.createElement("script");
						li.type = "text/javascript";
						li.async = true;
						li.src = ("https:" == document.location.protocol ? "https:" : "http:") + "//platform.stumbleupon.com/1/widgets.js";
						var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(li, s);
					}
				)();
				</script>
			';
		}
		
		function pinterest_button( &$attr = array() ) {
			global $ngfb, $post; 
			$button_html = '';
			if ( empty( $attr['pin_count_layout'] ) ) $attr['pin_count_layout'] = $ngfb->options['pin_count_layout'];
			if ( empty( $attr['url'] ) ) $attr['url'] = get_permalink( $post->ID );
			if ( empty( $attr['size'] ) ) $attr['size'] = $ngfb->options['pin_img_size'];
			if ( empty( $attr['caption'] ) ) $attr['caption'] = $ngfb->get_caption( $ngfb->options['pin_caption'], $ngfb->options['pin_cap_len'] );
			if ( empty( $attr['photo'] ) ) {
				if ( empty( $attr['pid'] ) && function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post->ID ) ) {
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
		
		function pinterest_footer() {
			return '
				<!-- Pinterest Javascript -->
				<script type="text/javascript" src="http://assets.pinterest.com/js/pinit.js"></script>
			';
		}
		
		function tumblr_button( &$attr = array() ) {
			global $ngfb, $post; 
			$button_html = '';
			if ( empty( $attr['tumblr_button_style'] ) ) $attr['tumblr_button_style'] = $ngfb->options['tumblr_button_style'];
			if ( empty( $attr['url'] ) ) $attr['url'] = get_permalink( $post->ID );
			if ( empty( $attr['size'] ) ) $attr['size'] = $ngfb->options['tumblr_img_size'];
			if ( empty( $attr['embed'] ) ) {
				$videos = array();
				$content = $ngfb->apply_content_filter( $post->post_content, $ngfb->options['ngfb_filter_content'] );
				$videos = $ngfb->get_videos( $content, 1 );	// get the first video, if any
				if ( $ngfb->options['ngfb_debug'] ) 
					$ngfb->debug_msg( __FUNCTION__ . ':$videos', print_r( $videos, true ) );
				if ( ! empty( $videos[0]['og:video'] ) ) $attr['embed'] = $videos[0]['og:video'];
			}
			if ( empty( $attr['title'] ) ) $attr['title'] = $ngfb->get_title();
			if ( empty( $attr['caption'] ) ) $attr['caption'] = $ngfb->get_caption( $ngfb->options['tumblr_caption'], $ngfb->options['tumblr_cap_len'] );
			if ( empty( $attr['description'] ) ) $attr['description'] = $ngfb->get_description( $ngfb->options['tumblr_desc_len'], '...' );
		
			// only use an get a featured image if 'tumblr_photo' option allows it
			if ( empty( $attr['photo'] ) && $ngfb->options['tumblr_photo'] ) {
				if ( empty( $attr['pid'] ) && function_exists( 'has_post_thumbnail' ) && has_post_thumbnail( $post->ID ) ) {
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
			if ( empty( $attr['quote'] ) && get_post_format( $post->ID ) == 'quote' ) {
				$attr['quote'] = $ngfb->get_quote();
			}
			if ( $ngfb->options['ngfb_debug'] ) 
				$ngfb->debug_msg( __FUNCTION__ . ':$attr', print_r( $attr, true ) );

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
					<!-- Tumblr Button -->
					<div class="tumblr-button"><a href="http://www.tumblr.com/share/'. $button_html . '" 
						title="Share on tumblr"><img border="0" alt="tumblr"
						src="http://platform.tumblr.com/v1/' . $attr['tumblr_button_style'] . '.png" /></a></div>
				';
			}
			return $button_html;
		}
		
		function tumblr_footer() {
			return '
				<!-- tumblr Javascript -->
				<script type="text/javascript" src="http://platform.tumblr.com/v1/share.js"></script>
			';
		}
		
		function facebook_button( &$attr = array() ) {
			global $ngfb, $post; 
			if ( empty( $attr['url'] ) ) $attr['url'] = get_permalink( $post->ID );
			$fb_send = $ngfb->options['fb_send'] ? 'true' : 'false';
			$fb_show_faces = $ngfb->options['fb_show_faces'] ? 'true' : 'false';
			return '
				<!-- Facebook Button -->
				<div class="facebook-button"><span class="fb-root"><fb:like 
				href="' . $attr['url'] . '"
				send="' . $fb_send . '" 
				layout="' . $ngfb->options['fb_layout'] . '" 
				width="400"
				show_faces="' . $fb_show_faces . '" 
				font="' . $ngfb->options['fb_font'] . '" 
				action="' . $ngfb->options['fb_action'] . '"
				colorscheme="' . $ngfb->options['fb_colorscheme'] . '"></fb:like></span></div>
			';
		}
		
		function facebook_footer() {
			return '
				<!-- Facebook Javascript -->
				<script type="text/javascript" src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>
			';
		}
		
		function gplus_button( &$attr = array() ) {
			global $ngfb, $post; 
			$button_html;
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
		
		function gplus_footer() {
			return '
				<!-- Google+ Javascript -->
				<script type="text/javascript"> ( 
					function() {
						var po = document.createElement("script");
						po.type = "text/javascript"; 
						po.async = true;
						po.src = "https://apis.google.com/js/plusone.js";
						var s = document.getElementsByTagName("script")[0]; 
						s.parentNode.insertBefore(po, s);
					}
				)(); </script>
			';
		}
		
		function twitter_button( &$attr = array() ) {
			global $ngfb, $post; 
			if ( empty( $attr['url'] ) ) $attr['url'] = get_permalink( $post->ID );
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
		
		function twitter_footer() {
			return '
				<!-- Twitter Javascript -->
				<script type="text/javascript">
					! function( d, s, id ) {
						var js, fjs = d.getElementsByTagName( s )[0];
						if ( ! d.getElementById( id ) ){
							js = d.createElement( s );
							js.id = id;
							js.src = "http://platform.twitter.com/widgets.js";
							fjs.parentNode.insertBefore( js, fjs );
						}
					} ( document, "script", "twitter-wjs" );
				</script>
			';
		}
		
		function linkedin_button( &$attr = array() ) {
			global $ngfb, $post; 
			$button_html;
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
		
		function linkedin_footer() {
			return '
				<!-- LinkedIn Javascript -->
				<script type="text/javascript" src="http://platform.linkedin.com/in.js"></script>
			';
		}
		
	}
}
?>

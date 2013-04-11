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

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbButtons' ) ) {

	class ngfbButtons {

		function __construct() {
		}

		function get_cache_url( $url ) {
			global $ngfb;

			// facebook javascript sdk doesn't work when hosted locally
			if ( preg_match( '/connect.facebook.net/', $url ) ) return $url;

			// make sure the cache expiration is greater than 0 hours
			if ( empty( $ngfb->options['ngfb_cache_hours'] ) ) return $url;

			return ( $ngfb->cdn_linker_rewrite( $ngfb->cache->get( $url ) ) );
		}

		function get_short_url( $url, $short = true ) {
			global $ngfb;
			if ( function_exists('curl_init') && ! empty( $short ) ) {
				$goo = new ngfbGoogl( $ngfb->options['ngfb_googl_api_key'] );
				$url = $goo->shorten( $url );
			}
			return $url;
		}

		function get_css( $css_name, $atts = array(), $css_class_other = '' ) {
			global $post;
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;

			$atts['css_class'] = empty( $atts['css_class'] ) ? 'button' : $atts['css_class'];
			$atts['css_class'] = $css_name . '-' . $atts['css_class'];
			if ( ! empty( $css_class_other ) ) 
				$atts['css_class'] = $css_class_other . ' ' . $atts['css_class'];

			$atts['css_id'] = empty( $atts['css_id'] ) ? 'button' : $atts['css_id'];
			$atts['css_id'] = $css_name . '-' . $atts['css_id'];
			if ( $use_post == true && ! empty( $post ) ) 
				$atts['css_id'] .= ' ' . $atts['css_id'] . '-post-' . $post->ID;

			return 'class="' . $atts['css_class'] . '" id="' . $atts['css_id'] . '"';
		}

		function header_js( $loc = 'id' ) {
			global $ngfb;
			$lang = empty( $ngfb->options['gp_lang'] ) ? 'en-US' : $ngfb->options['gp_lang'];
			return '<script type="text/javascript" id="ngfb-header-script">
				window.___gcfg = { lang: "' .  $lang . '" };
				function ngfb_header_js( script_id, url, async ) {
					if ( document.getElementById( script_id + "-js" ) ) return;
					var async = typeof async !== "undefined" ? async : true;
					var script_pos = document.getElementById( script_id );
					var js = document.createElement( "script" );
					js.id = script_id + "-js";
					js.async = async;
					js.type = "text/javascript";
					js.language = "JavaScript";
					js.src = url;
					script_pos.parentNode.insertBefore( js, script_pos );
				};' . "\n</script>\n";
		}

		function get_first_attached_image_id( $post_id = '' ) {
			if ( ! empty( $post_id ) ) {
				$images = get_children( array( 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image') );
				foreach ( $images as $attachment ) return $attachment->ID;
			}
			return;
		}

		/*	Facebook
		 *	--------
		 */
		function facebook_button( $atts = array() ) {
			global $ngfb, $post; 
			$button_html = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			if ( empty( $atts['url'] ) ) $atts['url'] = $ngfb->get_sharing_url( 'noquery', null, $use_post );
			$fb_send = $ngfb->options['fb_send'] ? 'true' : 'false';
			$fb_show_faces = $ngfb->options['fb_show_faces'] ? 'true' : 'false';

			switch ( $ngfb->options['fb_markup'] ) {
				case 'xfbml' :
					// XFBML
					$button_html = '
					<!-- Facebook Button -->
					<div ' . $this->get_css( 'facebook', $atts, 'fb-like' ) . '><fb:like 
						href="' . $attr['url'] . '" 
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
					$button_html = '
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
			return $button_html;
		}
		
		function facebook_js( $loc = 'id' ) {
			global $ngfb; 
			$lang = empty( $ngfb->options['fb_lang'] ) ? 'en_US' : $ngfb->options['fb_lang'];
			return '<script type="text/javascript" id="facebook-script-' . $loc . '">
				ngfb_header_js( "facebook-script-' . $loc . '", "' . 
					$this->get_cache_url( 'https://connect.facebook.net/' . 
					$lang . '/all.js#xfbml=1&appId=' . $ngfb->options['og_app_id'] ) . '" );
			</script>' . "\n";
		}

		/*	Google+
		 *	-------
		 */
		function gplus_button( $atts = array() ) {
			global $ngfb, $post; 
			$button_html = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			if ( empty( $atts['url'] ) ) $atts['url'] = $ngfb->get_sharing_url( 'noquery', null, $use_post );
			$gp_class = $ngfb->options['gp_action'] == 'share' ? 'class="g-plus" data-action="share"' : 'class="g-plusone"';
			return '
				<!-- Google+ Button -->
				<div ' . $this->get_css( 'gplus', $atts, 'g-plusone-button' ) . '>
					<span '. $gp_class . ' 
						data-size="' . $ngfb->options['gp_size'] . '" 
						data-annotation="' . $ngfb->options['gp_annotation'] . '" 
						data-href="' . $atts['url'] . '"></span>
				</div>' . "\n";
		}
		
		function gplus_js( $loc = 'id' ) {
			return '<script type="text/javascript" id="gplus-script-' . $loc . '">
				ngfb_header_js( "gplus-script-' . $loc . '", "' . $this->get_cache_url( 'https://apis.google.com/js/plusone.js' ) . '" );
			</script>' . "\n";
		}
		
		/*	LinkedIn
		 *	--------
		 */
		function linkedin_button( $atts = array() ) {
			global $ngfb, $post; 
			$button_html = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			if ( empty( $atts['url'] ) ) $atts['url'] = $ngfb->get_sharing_url( 'noquery', null, $use_post );
			$button_html = '
				<!-- LinkedIn Button -->
				<div ' . $this->get_css( 'linkedin', $atts ) . '>
				<script type="IN/Share" data-url="' . $atts['url'] . '"';

			if ( ! empty( $ngfb->options['linkedin_counter'] ) ) 
				$button_html .= ' data-counter="' . $ngfb->options['linkedin_counter'] . '"';

			if ( ! empty( $ngfb->options['linkedin_showzero'] ) ) 
				$button_html .= ' data-showzero="true"';

			$button_html .= '></script></div>'."\n";
			return $button_html;
		}
		
		function linkedin_js( $loc = 'id' ) {
			return  '<script type="text/javascript" id="linkedin-script-' . $loc . '">
				ngfb_header_js( "linkedin-script-' . $loc . '", "' . $this->get_cache_url( 'https://platform.linkedin.com/in.js' ) . '" );
			</script>' . "\n";
		}

		/*	Pinterest
		 *	---------
		 */
		function pinterest_button( $atts = array() ) {
			global $ngfb, $post; 
			$button_query = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			if ( empty( $atts['url'] ) ) $atts['url'] = $ngfb->get_sharing_url( 'noquery', null, $use_post );
			if ( empty( $atts['size'] ) ) $atts['size'] = $ngfb->options['pin_img_size'];
			if ( empty( $atts['photo'] ) ) {
				if ( empty( $atts['pid'] ) ) {
					// allow on index pages only if in content (not a widget)
					if ( $use_post == true ) {
						if ( ! empty( $ngfb->is_active['postthumb'] ) && has_post_thumbnail( $post->ID ) )
							$atts['pid'] = get_post_thumbnail_id( $post->ID );
						else $atts['pid'] = $this->get_first_attached_image_id( $post->ID );
					}
				}
				if ( ! empty( $atts['pid'] ) ) {
					// if the post thumbnail id has the form ngg- then it's a NextGEN image
					if ( is_string( $atts['pid'] ) && substr( $atts['pid'], 0, 4 ) == 'ngg-' ) {
						list( $atts['photo'], $atts['width'], $atts['height'], 
							$atts['cropped'] ) = $ngfb->get_ngg_image_src( $atts['pid'], $atts['size'] );
					} else {
						list( $atts['photo'], $atts['width'], 
							$atts['height'] ) = wp_get_attachment_image_src( $atts['pid'], $atts['size'] );
					}
				}
			}
			if ( empty( $atts['photo'] ) ) return;
			if ( empty( $atts['pin_count_layout'] ) ) $atts['pin_count_layout'] = $ngfb->options['pin_count_layout'];
			if ( empty( $atts['caption'] ) ) $atts['caption'] = $ngfb->get_caption( $ngfb->options['pin_caption'], $ngfb->options['pin_cap_len'], $use_post );

			$button_query .= 'url=' . urlencode( $atts['url'] );
			$button_query .= '&amp;media='. urlencode( $ngfb->cdn_linker_rewrite( $atts['photo'] ) );
			$button_query .= '&amp;description=' . urlencode( $ngfb->str_decode( $atts['caption'] ) );

			return '
				<!-- Pinterest Button -->
				<div ' . $this->get_css( 'pinterest', $atts ) . '><a 
					href="http://pinterest.com/pin/create/button/?' . $button_query . '" 
					class="pin-it-button" count-layout="' . $atts['pin_count_layout'] . '" 
					title="Share on Pinterest"><img border="0" alt="Pin It"
					src="' . $this->get_cache_url( 'https://assets.pinterest.com/images/PinExt.png' ) . '" /></a></div>
			';
		}

		function pinterest_js( $loc = 'id' ) {
			return '<script type="text/javascript" id="pinterest-script-' . $loc . '">
				ngfb_header_js( "pinterest-script-' . $loc . '", "' . $this->get_cache_url( 'https://assets.pinterest.com/js/pinit.js' ) . '" );
			</script>' . "\n";
		}
		
		/* 	StumbleUpon
		 *	-----------
		 */
		function stumbleupon_button( $atts = array() ) {
			global $ngfb, $post; 
			$button_html = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			if ( empty( $atts['url'] ) ) $atts['url'] = $ngfb->get_sharing_url( 'noquery', null, $use_post );
			if ( empty( $atts['stumble_badge'] ) ) $atts['stumble_badge'] = $ngfb->options['stumble_badge'];
			$button_html = '
				<!-- StumbleUpon Button -->
				<div ' . $this->get_css( 'stumbleupon', $atts, 'stumble-button' ) . '><su:badge 
					layout="' . $atts['stumble_badge'] . '" location="' . $atts['url'] . '"></su:badge></div>
			';
			return $button_html;	
		}

		function stumbleupon_js( $loc = 'id' ) {
			return '<script type="text/javascript" id="stumbleupon-script-' . $loc . '">
				ngfb_header_js( "stumbleupon-script-' . $loc . '", "' . $this->get_cache_url( 'https://platform.stumbleupon.com/1/widgets.js' ) . '" );
			</script>' . "\n";
		}

		/*	Tumblr
		 *	------
		 */
		function tumblr_button( $atts = array() ) {
			global $ngfb, $post; 
			$button_query = '';
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			if ( empty( $atts['url'] ) ) $atts['url'] = $ngfb->get_sharing_url( 'noquery', null, $use_post );
			if ( empty( $atts['tumblr_button_style'] ) ) $atts['tumblr_button_style'] = $ngfb->options['tumblr_button_style'];
			if ( empty( $atts['size'] ) ) $atts['size'] = $ngfb->options['tumblr_img_size'];

			// only use featured image if 'tumblr_photo' option allows it
			if ( empty( $atts['photo'] ) && $ngfb->options['tumblr_photo'] ) {
				if ( empty( $atts['pid'] ) ) {
					// allow on index pages only if in content (not a widget)
					if ( $use_post == true ) {
						if ( ! empty( $ngfb->is_active['postthumb'] ) && has_post_thumbnail( $post->ID ) )
							$atts['pid'] = get_post_thumbnail_id( $post->ID );
						else $atts['pid'] = $this->get_first_attached_image_id( $post->ID );
					}
				}
				if ( ! empty( $atts['pid'] ) ) {
					// if the post thumbnail id has the form ngg- then it's a NextGEN image
					if ( is_string( $atts['pid'] ) && substr( $atts['pid'], 0, 4 ) == 'ngg-' ) {
						list( $atts['photo'], $atts['width'], $atts['height'], 
							$atts['cropped'] ) = $ngfb->get_ngg_image_src( $atts['pid'], $atts['size'] );
					} else {
						list( $atts['photo'], $atts['width'], 
							$atts['height'] ) = wp_get_attachment_image_src( $atts['pid'], $atts['size'] );
					}
				}
			}

			if ( empty( $atts['photo'] ) && empty( $atts['embed'] ) ) {
				// allow on index pages only if in content (not a widget)
				if ( $use_post == true ) {
					if ( ! empty( $post ) && ! empty( $post->post_content ) ) {
						$videos = array();
						$videos = $ngfb->get_content_videos_og( 1 );	// get the first video, if any
						if ( ! empty( $videos[0]['og:video'] ) ) 
							$atts['embed'] = $videos[0]['og:video'];
					}
				}
			}

			if ( empty( $atts['photo'] ) && empty( $atts['embed'] ) && empty( $atts['quote'] ) ) {
				// allow on index pages only if in content (not a widget)
				if ( $use_post == true ) {
					if ( ! empty( $post ) && get_post_format( $post->ID ) == 'quote' ) 
						$atts['quote'] = $ngfb->get_quote();
				}
			}

			// we only need the caption / title / description for some cases
			if ( ! empty( $atts['photo'] ) || ! empty( $atts['embed'] ) ) {
				if ( empty( $atts['caption'] ) ) 
					$atts['caption'] = $ngfb->get_caption( $ngfb->options['tumblr_caption'], $ngfb->options['tumblr_cap_len'], $use_post );
			} else {
				if ( empty( $atts['title'] ) ) 
					$atts['title'] = $ngfb->get_title( null, null, $use_post);
				if ( empty( $atts['description'] ) ) 
					$atts['description'] = $ngfb->get_description( $ngfb->options['tumblr_desc_len'], '...', $use_post );
			}

			// define the button, based on what we have
			if ( ! empty( $atts['photo'] ) ) {
				$button_query .= 'photo?source='. urlencode( $ngfb->cdn_linker_rewrite( $atts['photo'] ) );
				$button_query .= '&amp;clickthru=' . urlencode( $atts['url'] );
				$button_query .= '&amp;caption=' . urlencode( $ngfb->str_decode( $atts['caption'] ) );
			} elseif ( ! empty( $atts['embed'] ) ) {
				$button_query .= 'video?embed=' . urlencode( $atts['embed'] );
				$button_query .= '&amp;caption=' . urlencode( $ngfb->str_decode( $atts['caption'] ) );
			} elseif ( ! empty( $atts['quote'] ) ) {
				$button_query .= 'quote?quote=' . urlencode( $atts['quote'] );
				$button_query .= '&amp;source=' . urlencode( $ngfb->str_decode( $atts['title'] ) );
			} elseif ( ! empty( $atts['url'] ) ) {
				$button_query .= 'link?url=' . urlencode( $atts['url'] );
				$button_query .= '&amp;name=' . urlencode( $ngfb->str_decode( $atts['title'] ) );
				$button_query .= '&amp;description=' . urlencode( $ngfb->str_decode( $atts['description'] ) );
			}
			if ( empty( $button_query ) ) return;

			return '
				<!-- Tumblr Button -->
				<div ' . $this->get_css( 'tumblr', $atts ) . '><a href="http://www.tumblr.com/share/'. $button_query . '" 
					title="Share on Tumblr"><img border="0" alt="Share on Tumblr"
					src="' . $this->get_cache_url( 'http://platform.tumblr.com/v1/' . $atts['tumblr_button_style'] . '.png' ) . '" /></a></div>
			';
		}

		// the tumblr host does not have a valid SSL cert, and it's javascript does not work in async mode
		function tumblr_js( $loc = 'id' ) {
			return '<script type="text/javascript" id="tumblr-script-' . $loc . '"
				src="' . $this->get_cache_url( 'http://platform.tumblr.com/v1/share.js' ) . '"></script>' . "\n";
		}
		
		/*	Twitter
		 *	-------
		 */
		function twitter_button( $atts = array() ) {
			global $ngfb, $post; 
			$use_post = empty( $atts['is_widget'] ) || is_singular() ? true : false;
			if ( empty( $atts['url'] ) ) $atts['url'] = $ngfb->get_sharing_url( 'noquery', null, $use_post );
			if ( empty( $atts['caption'] ) ) 
				$atts['caption'] = $ngfb->get_caption( $ngfb->options['twitter_caption'], $ngfb->options['twitter_cap_len'], $use_post );

			$long_url = $atts['url'];
			$atts['url'] = $this->get_short_url( $atts['url'], $ngfb->options['twitter_shorten'] );
			$twitter_dnt = $ngfb->options['twitter_dnt'] ? 'true' : 'false';
			$lang = empty( $ngfb->options['twitter_lang'] ) ? 'en' : $ngfb->options['twitter_lang'];

			return '
				<!-- Twitter Button -->
				<!-- URL = ' . $long_url . ' -->
				<div ' . $this->get_css( 'twitter', $atts ) . '>
					<a href="https://twitter.com/share" 
						class="twitter-share-button"
						lang="'. $lang . '"
						data-url="' . $atts['url'] . '" 
						data-text="' . $atts['caption'] . '" 
						data-count="' . $ngfb->options['twitter_count'] . '" 
						data-size="' . $ngfb->options['twitter_size'] . '" 
						data-dnt="' . $twitter_dnt . '">Tweet</a>
				</div>' . "\n";
		}
		
		function twitter_js( $loc = 'id' ) {
			return '<script type="text/javascript" id="twitter-script-' . $loc . '">
				ngfb_header_js( "twitter-script-' . $loc . '", "' . $this->get_cache_url( 'https://platform.twitter.com/widgets.js' ) . '" );
			</script>' . "\n";
		}

	}

}
?>

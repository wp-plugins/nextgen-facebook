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

if ( preg_match( '#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'] ) ) 
	die( 'You are not allowed to call this page directly.' );

if ( ! class_exists( 'ngfbButtons' ) ) {

	class ngfbButtons {

		var $cache = '';

		function __construct() {
			require_once ( dirname ( __FILE__ ) . '/cache.php' );
			$this->cache = new ngfbCache();
		}

		function setup_cache_vars() {
			global $ngfb;
			$this->cache->base_dir = trailingslashit( NGFB_CACHEDIR );
			$this->cache->base_url = trailingslashit( NGFB_CACHEURL );
			$this->cache->pem_file = NGFB_PEM_FILE;
			$this->cache->verify_cert = $ngfb->options['ngfb_verify_certs'];
			$this->cache->expire_time = $ngfb->options['ngfb_cache_hours'] * 60 * 60;
			$this->cache->user_agent = NGFB_USER_AGENT;
		}

		function get_cache_url( $url ) {
			global $ngfb;

			// facebook javascript sdk doesn't work when hosted locally
			if ( preg_match( '/connect.facebook.net/', $url ) ) return $url;

			// make sure the cache expiration is greater than 0 hours
			if ( empty( $ngfb->options['ngfb_cache_hours'] ) ) return $url;

			return ( $ngfb->cdn_linker_rewrite( $this->cache->get( $url ) ) );
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

			$atts['css_class'] = empty( $atts['css_class'] ) ? 'button' : $atts['css_class'];
			$atts['css_class'] = $css_name . '-' . $atts['css_class'];
			if ( ! empty( $css_class_other ) ) $atts['css_class'] = $css_class_other . ' ' . $atts['css_class'];

			$atts['css_id'] = empty( $atts['css_id'] ) ? 'button' : $atts['css_id'];
			$atts['css_id'] = $css_name . '-' . $atts['css_id'];
			if ( ! empty( $post ) ) $atts['css_id'] .= ' ' . $atts['css_id'] . '-post-' . $post->ID;

			return 'class="' . $atts['css_class'] . '" id="' . $atts['css_id'] . '"';
		}


		function header_js( $loc = 'id' ) {
			global $ngfb;
			$this->setup_cache_vars();
			$lang = empty( $ngfb->options['buttons_lang'] ) ? 'en-US' : $ngfb->options['buttons_lang'];
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

		/* 	StumbleUpon
		 *	-----------
		 */
		function stumbleupon_button( $atts = array() ) {
			global $ngfb, $post; 
			$button_html = '';
			if ( empty( $atts['url'] ) && empty( $post ) ) return;
			if ( empty( $atts['url'] ) ) $atts['url'] = get_permalink( $post->ID );
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

		/*	Pinterest
		 *	---------
		 */
		function pinterest_button( $atts = array() ) {
			global $ngfb, $post; 
			$button_html = '';
			if ( empty( $atts['url'] ) && empty( $post ) ) return;
			if ( empty( $atts['url'] ) ) $atts['url'] = get_permalink( $post->ID );
			if ( empty( $atts['pin_count_layout'] ) ) $atts['pin_count_layout'] = $ngfb->options['pin_count_layout'];
			if ( empty( $atts['size'] ) ) $atts['size'] = $ngfb->options['pin_img_size'];
			if ( empty( $atts['caption'] ) ) $atts['caption'] = $ngfb->get_caption( $ngfb->options['pin_caption'], $ngfb->options['pin_cap_len'] );
			if ( empty( $atts['photo'] ) ) {
				if ( empty( $atts['pid'] ) && ! empty( $ngfb->is_active['postthumb'] ) && has_post_thumbnail( $post->ID ) ) {
					$atts['pid'] = get_post_thumbnail_id( $post->ID );
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

			// define the button, based on what we have
			if ( ! empty( $atts['photo'] ) ) {
				$button_query .= 'url=' . urlencode( $atts['url'] );
				$button_query .= '&amp;media='. urlencode( $ngfb->cdn_linker_rewrite( $atts['photo'] ) );
				$button_query .= '&amp;description=' . urlencode( $ngfb->str_decode( $atts['caption'] ) );
			}

			// if we have something, then complete the button code
			if ( ! empty( $button_query ) ) {
				$button_html = '
					<!-- Pinterest Button -->
					<div ' . $this->get_css( 'pinterest', $atts ) . '><a 
						href="http://pinterest.com/pin/create/button/?' . $button_query . '" 
						class="pin-it-button" count-layout="' . $atts['pin_count_layout'] . '" 
						title="Share on Pinterest"><img border="0" alt="Pin It"
						src="' . $this->get_cache_url( 'https://assets.pinterest.com/images/PinExt.png' ) . '" /></a></div>
				';
			}
			return $button_html;	
		}

		function pinterest_js( $loc = 'id' ) {
			return '<script type="text/javascript" id="pinterest-script-' . $loc . '">
				ngfb_header_js( "pinterest-script-' . $loc . '", "' . $this->get_cache_url( 'https://assets.pinterest.com/js/pinit.js' ) . '" );
			</script>' . "\n";
		}
		
		/*	Tumblr
		 *	------
		 */
		function tumblr_button( $atts = array() ) {
			global $ngfb, $post; 
			$button_html = '';
			if ( empty( $atts['url'] ) && empty( $post ) ) return;
			if ( empty( $atts['url'] ) ) $atts['url'] = get_permalink( $post->ID );
			if ( empty( $atts['tumblr_button_style'] ) ) $atts['tumblr_button_style'] = $ngfb->options['tumblr_button_style'];
			if ( empty( $atts['size'] ) ) $atts['size'] = $ngfb->options['tumblr_img_size'];
			if ( empty( $atts['title'] ) ) $atts['title'] = $ngfb->get_title( null, null, true);
			if ( empty( $atts['caption'] ) ) $atts['caption'] = $ngfb->get_caption( $ngfb->options['tumblr_caption'], $ngfb->options['tumblr_cap_len'] );
			if ( empty( $atts['description'] ) ) $atts['description'] = $ngfb->get_description( $ngfb->options['tumblr_desc_len'], '...', true );
			if ( empty( $atts['quote'] ) && ! empty( $post ) && get_post_format( $post->ID ) == 'quote' ) $atts['quote'] = $ngfb->get_quote();

			if ( empty( $atts['embed'] ) && ! empty( $post ) && ! empty( $post->post_content ) ) {
				$videos = array();
				$videos = $ngfb->get_content_videos_og( 1 );	// get the first video, if any
				if ( ! empty( $videos[0]['og:video'] ) ) 
					$atts['embed'] = $videos[0]['og:video'];
			}

			// only use featured image if 'tumblr_photo' option allows it
			if ( empty( $atts['photo'] ) && $ngfb->options['tumblr_photo'] ) {

				if ( empty( $atts['pid'] ) 
					&& ! empty( $ngfb->is_active['postthumb'] ) 
					&& ! empty ( $post ) 
					&& has_post_thumbnail( $post->ID ) )
						$atts['pid'] = get_post_thumbnail_id( $post->ID );
				
				if ( ! empty( $atts['pid'] ) ) {
					if ( is_string( $atts['pid'] ) && substr( $atts['pid'], 0, 4 ) == 'ngg-' ) {
						list( $atts['photo'], $atts['width'], $atts['height'], 
							$atts['cropped'] ) = $ngfb->get_ngg_image_src( $atts['pid'], $atts['size'] );
					} else {
						list( $atts['photo'], $atts['width'], 
							$atts['height'] ) = wp_get_attachment_image_src( $atts['pid'], $atts['size'] );
					}
				}
			}

			// define the button, based on what we have
			if ( ! empty( $atts['photo'] ) ) {
				$button_html .= 'photo?source='. urlencode( $ngfb->cdn_linker_rewrite( $atts['photo'] ) );
				$button_html .= '&amp;clickthru=' . urlencode( $atts['url'] );
				$button_html .= '&amp;caption=' . urlencode( $ngfb->str_decode( $atts['caption'] ) );
			} elseif ( ! empty( $atts['embed'] ) ) {
				$button_html .= 'video?embed=' . urlencode( $atts['embed'] );
				$button_html .= '&amp;caption=' . urlencode( $ngfb->str_decode( $atts['caption'] ) );
			} elseif ( ! empty( $atts['quote'] ) ) {
				$button_html .= 'quote?quote=' . urlencode( $atts['quote'] );
				$button_html .= '&amp;source=' . urlencode( $ngfb->str_decode( $atts['title'] ) );
			} elseif ( ! empty( $atts['url'] ) ) {
				$button_html .= 'link?url=' . urlencode( $atts['url'] );
				$button_html .= '&amp;name=' . urlencode( $ngfb->str_decode( $atts['title'] ) );
				$button_html .= '&amp;description=' . urlencode( $ngfb->str_decode( $atts['description'] ) );
			}
			// if we have something, then complete the button code
			if ( $button_html ) {
				$button_html = '
					<!-- Tumblr Button -->
					<div ' . $this->get_css( 'tumblr', $atts ) . '><a href="http://www.tumblr.com/share/'. $button_html . '" 
						title="Share on Tumblr"><img border="0" alt="Share on Tumblr"
						src="' . $this->get_cache_url( 'http://platform.tumblr.com/v1/' . $atts['tumblr_button_style'] . '.png' ) . '" /></a></div>
				';
			}
			return $button_html;
		}

		// the tumblr host does not have a valid SSL cert, and it's javascript does not work in async mode
		function tumblr_js( $loc = 'id' ) {
			return '<script type="text/javascript" id="tumblr-script-' . $loc . '"
				src="' . $this->get_cache_url( 'http://platform.tumblr.com/v1/share.js' ) . '"></script>' . "\n";
		}
		
		/*	Facebook
		 *	--------
		 */
		function facebook_button( $atts = array() ) {
			global $ngfb, $post; 
			if ( empty( $atts['url'] ) && empty( $post ) ) return;
			if ( empty( $atts['url'] ) ) $atts['url'] = get_permalink( $post->ID );
			$fb_send = $ngfb->options['fb_send'] ? 'true' : 'false';
			$fb_show_faces = $ngfb->options['fb_show_faces'] ? 'true' : 'false';
			return '
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
		}
		
		function facebook_js( $loc = 'id' ) {
			global $ngfb; 
			$lang = empty( $ngfb->options['buttons_lang'] ) ? 'en-US' : $ngfb->options['buttons_lang'];
			$lang = preg_replace( '/-/', '_', $lang );

			return '<script type="text/javascript" id="facebook-script-' . $loc . '">
				ngfb_header_js( "facebook-script-' . $loc . '", "' . $this->get_cache_url( 'https://connect.facebook.net/' . $lang . '/all.js#xfbml=1&appId=' . $ngfb->options['og_app_id'] ) . '" );
			</script>' . "\n";
		}

		/*	Google+
		 *	-------
		 */
		function gplus_button( $atts = array() ) {
			global $ngfb, $post; 
			$button_html = '';
			if ( empty( $atts['url'] ) && empty( $post ) ) return;
			if ( empty( $atts['url'] ) ) $atts['url'] = get_permalink( $post->ID );
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
		
		/*	Twitter
		 *	-------
		 */
		function twitter_button( $atts = array() ) {
			global $ngfb, $post; 
			if ( empty( $atts['url'] ) && empty( $post ) ) return;
			if ( empty( $atts['url'] ) ) $atts['url'] = get_permalink( $post->ID );
			if ( empty( $atts['caption'] ) ) $atts['caption'] = $ngfb->get_caption( $ngfb->options['twitter_caption'], $ngfb->options['twitter_cap_len'] );
			$long_url = $atts['url'];
			$atts['url'] = $this->get_short_url( $atts['url'], $ngfb->options['twitter_shorten'] );
			$twitter_dnt = $ngfb->options['twitter_dnt'] ? 'true' : 'false';
			$lang = empty( $ngfb->options['buttons_lang'] ) ? 'en-US' : $ngfb->options['buttons_lang'];
			$lang = substr( $lang, 0, 2);
			switch ( $lang ) {
				case 'en' :
				case 'fr' :
				case 'de' :
				case 'it' :
				case 'es' :
				case 'ko' :
				case 'ja' :
					break;
				default :
					$lang = 'en';
					break;
			}
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
		
		/*	LinkedIn
		 *	--------
		 */
		function linkedin_button( $atts = array() ) {
			global $ngfb, $post; 
			$button_html = '';
			if ( empty( $atts['url'] ) && empty( $post ) ) return;
			if ( empty( $atts['url'] ) ) $atts['url'] = get_permalink( $post->ID );
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
		
	}

}
?>

<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbMedia' ) ) {

	class ngfbMedia {

		private $p;

		public $ngg;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();

			require_once ( NGFB_PLUGINDIR . 'lib/ngg.php' );
			$this->ngg = new ngfbMediaNgg( $plugin );

			add_filter( 'wp_get_attachment_image_attributes', array( &$this, 'add_attachment_image_attributes' ), 10, 2 );
		}

		// $attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, $attachment );
		public function add_attachment_image_attributes( $attr, $attach ) {
			$attr['data-ngfb-wp-pid'] = $attach->ID;
			return $attr;
		}

		public function get_size_info( $size_name = 'thumbnail' ) {
			global $_wp_additional_image_sizes;
			if ( is_integer( $size_name ) ) return;
			if ( is_array( $size_name ) ) return;
			if ( isset( $_wp_additional_image_sizes[$size_name]['width'] ) )
				$width = intval( $_wp_additional_image_sizes[$size_name]['width'] );
			else $width = get_option( $size_name . '_size_w' );
			if ( isset( $_wp_additional_image_sizes[$size_name]['height'] ) )
				$height = intval( $_wp_additional_image_sizes[$size_name]['height'] );
			else $height = get_option( $size_name . '_size_h' );
			if ( isset( $_wp_additional_image_sizes[$size_name]['crop'] ) )
				$crop = intval( $_wp_additional_image_sizes[$size_name]['crop'] );
			else $crop = get_option( $size_name . '_crop' ) == 1 ? 1 : 0;
			return array( 'width' => $width, 'height' => $height, 'crop' => $crop );
		}

		public function num_remains( &$arr, $num = 0 ) {
			$remains = 0;
			if ( ! is_array( $arr ) ) return false;
			if ( $num > 0 && $num >= count( $arr ) )
				$remains = $num - count( $arr );
			return $remains;
		}

		public function get_post_images( $num = 0, $size_name = 'thumbnail', $post_id, $check_dupes = true ) {
			$this->p->debug->args( array( 'num' => $num, 'size_name' => $size_name, 'post_id' => $post_id, 'check_dupes' => $check_dupes ) );
			$og_ret = array();
			$log_args = ',"'.$size_name.'",'.$post_id.( $check_dupes ? 'true' : 'false' );
			$num_remains = $this->num_remains( $og_ret, $num );
			$og_ret = array_merge( $og_ret, $this->get_meta_image( $num_remains, $size_name, $post_id, $check_dupes ) );
			if ( ! $this->p->util->is_maxed( $og_ret, $num ) ) {
				$num_remains = $this->num_remains( $og_ret, $num );
				$og_ret = array_merge( $og_ret, $this->get_featured( $num_remains, $size_name, $post_id, $check_dupes ) );
			}
			if ( ! $this->p->util->is_maxed( $og_ret, $num ) ) {
				$num_remains = $this->num_remains( $og_ret, $num );
				$og_ret = array_merge( $og_ret, $this->get_attached_images( $num_remains, $size_name, $post_id, $check_dupes ) );
			}
			return $og_ret;
		}

		public function get_featured( $num = 0, $size_name = 'thumbnail', $post_id, $check_dupes = true ) {
			$this->p->debug->args( array( 'num' => $num, 'size_name' => $size_name, 'post_id' => $post_id, 'check_dupes' => $check_dupes ) );
			$og_ret = array();
			$og_image = array();
			$size_info = $this->get_size_info( $size_name );
			if ( ! empty( $post_id ) && $this->p->is_avail['postthumb'] == true && has_post_thumbnail( $post_id ) ) {
				$pid = get_post_thumbnail_id( $post_id );
				// featured images from ngg pre-v2 had 'ngg-' prefix
				if ( $this->p->is_avail['ngg'] == true && is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' ) {
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
						$og_image['og:image:cropped'] ) = $this->ngg->get_image_src( $pid, $size_name, $check_dupes );
				} else {
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
						$og_image['og:image:cropped'] ) = $this->get_attachment_image_src( $pid, $size_name, $check_dupes );
				}
				if ( ! empty( $og_image['og:image'] ) )
					$this->p->util->push_max( $og_ret, $og_image, $num );
			}
			return $og_ret;
		}

		public function get_first_attached_image_id( $post_id = '' ) {
			if ( ! empty( $post_id ) ) {
				$images = get_children( array( 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image' ) );
				$attach = reset( $images );
				if ( ! empty( $attach->ID ) )
					return $attach->ID;
			}
			return;
		}

		public function get_attachment_image( $num = 0, $size_name = 'thumbnail', $attach_id = '', $check_dupes = true ) {
			$this->p->debug->args( array( 'num' => $num, 'size_name' => $size_name, 'attach_id' => $attach_id, 'check_dupes' => $check_dupes ) );
			$og_ret = array();
			if ( ! empty( $attach_id ) ) {
				if ( wp_attachment_is_image( $attach_id ) ) {	// since wp 2.1.0 
					$og_image = array();
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
						$og_image['og:image:cropped'] ) = $this->get_attachment_image_src( $attach_id, $size_name, $check_dupes );
					if ( ! empty( $og_image['og:image'] ) )
						$this->p->util->push_max( $og_ret, $og_image, $num );
				} else $this->p->debug->log( 'attachment id ' . $attach_id . ' is not an image' );
			}
			return $og_ret;
		}

		public function get_attached_images( $num = 0, $size_name = 'thumbnail', $post_id = '', $check_dupes = true ) {
			$this->p->debug->args( array( 'num' => $num, 'size_name' => $size_name, 'post_id' => $post_id, 'check_dupes' => $check_dupes ) );
			$og_ret = array();
			if ( ! empty( $post_id ) ) {
				$images = get_children( array( 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image') );
				if ( is_array( $images ) )
					foreach ( $images as $attachment ) {
						if ( ! empty( $attachment->ID ) ) {
							$og_image = array();
							list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
								$og_image['og:image:cropped'] ) = $this->get_attachment_image_src( $attachment->ID, $size_name, $check_dupes );
							if ( ! empty( $og_image['og:image'] ) )
								$this->p->util->push_max( $og_ret, $og_image, $num );
						}
					}
			}
			return $og_ret;
		}

		public function get_attachment_image_src( $pid, $size_name = 'thumbnail', $check_dupes = true ) {
			$this->p->debug->args( array( 'pid' => $pid, 'size_name' => $size_name, 'check_dupes' => $check_dupes ) );
			$size_info = $this->get_size_info( $size_name );
			$img_url = '';
			$img_width = 0;
			$img_height = 0;
			$img_crop = empty( $size_info['crop'] ) ? 'false' : 'true';	// visual feedback, not a real true / false
			list( $img_url, $img_width, $img_height ) = wp_get_attachment_image_src( $pid, $size_name );

			if ( empty( $img_url ) )
				$this->p->debug->log( 'image rejected: returned image url is empty' );
			else {
				$this->p->debug->log( 'image returned: '.$img_url.' ('.$img_width.'x'.$img_height.')' );

				// make sure the returned image size matches the size we requested, if not then (possibly) resize the image
				// if the image is not cropped, then both sizes have to be off
				// if the image is supposed to be cropped, then only one size needs to be off
				if ( ( empty( $size_info['crop'] ) && ( $img_width != $size_info['width'] && $img_height != $size_info['height'] ) ) ||
					( ! empty( $size_info['crop'] ) && ( $img_width != $size_info['width'] || $img_height != $size_info['height'] ) ) ) {

					$this->p->debug->log( 'width and/or height does not match the size requested ('.$size_info['width'].'x'.$size_info['height'].')' );
					$img_meta = wp_get_attachment_metadata( $pid );

					// if the original image is too small, log the event and continue
					if ( $img_meta['width'] < $size_info['width'] && $img_meta['height'] < $size_info['height'] ) {

						$this->p->debug->log( 'original image ('.$img_meta['width'].'x'.$img_meta['height'].') is smaller than '.
							$size_name.' ('.$size_info['width'].'x'.$size_info['height'].')' );

					// if existing metadata sizes aren't what they should be, then resize the image
					} elseif ( ( empty( $size_info['crop'] ) && ( $img_meta['sizes'][$size_name]['width'] != $size_info['width'] && $img_meta['sizes'][$size_name]['height'] != $size_info['height'] ) ) ||
						( ! empty( $size_info['crop'] ) && ( $img_meta['sizes'][$size_name]['width'] != $size_info['width'] || $img_meta['sizes'][$size_name]['height'] != $size_info['height'] ) ) ) {
	
						include_once( ABSPATH.'wp-admin/includes/image.php' );
						$fullsizepath = get_attached_file( $pid );
						$new_meta = wp_generate_attachment_metadata( $pid, $fullsizepath );
						if ( ! is_wp_error( $new_meta ) && ! empty( $new_meta ) ) {
							wp_update_attachment_metadata( $pid, $new_meta );
							list( $img_url, $img_width, $img_height ) = wp_get_attachment_image_src( $pid, $size_name );
							if ( empty( $img_url ) ) {
								$this->p->debug->log( 'image rejected: returned image url after resize is empty' );
								return array( null, null, null, null );
							}
						}
					}
				}
				$img_url = $this->p->util->fix_relative_url( $img_url );
				if ( $check_dupes == false || $this->p->util->is_uniq_url( $img_url ) )
					return array( $this->p->util->rewrite( $img_url ), $img_width, $img_height, $img_crop );
			} 
			return array( null, null, null, null );
		}

		public function get_meta_image( $num = 0, $size_name = 'thumbnail', $post_id = '', $check_dupes = true ) {
			$this->p->debug->args( array( 'num' => $num, 'size_name' => $size_name, 'post_id' => $post_id, 'check_dupes' => $check_dupes ) );
			$image = array();
			$og_ret = array();
			if ( empty( $post_id ) ) return $og_ret;
			$pid = $this->p->meta->get_options( $post_id, 'og_img_id' );
			$pre = $this->p->meta->get_options( $post_id, 'og_img_id_pre' );
			$url = $this->p->meta->get_options( $post_id, 'og_img_url' );
			if ( $pid > 0 ) {
				if ( $this->p->is_avail['ngg'] == true && $pre == 'ngg' ) {
					$this->p->debug->log( 'found custom meta image id = '.$pre.'-'.$pid );
					$image = $this->ngg->get_image_src( $pre.'-'.$pid, $size_name, $check_dupes );
				} else {
					$this->p->debug->log( 'found custom meta image id = ' . $pid );
					$image = $this->get_attachment_image_src( $pid, $size_name, $check_dupes );
				}
			} elseif ( ! empty( $url ) ) {
				$this->p->debug->log( 'found custom meta image url = ' . $url );
				$image[] = $url;
			}
			if ( ! empty( $image ) ) {
				list( $og_image['og:image'], $og_image['og:image:width'], 
					$og_image['og:image:height'], $og_image['og:image:cropped'] ) = $image;
				if ( ! empty( $og_image['og:image'] ) )
					if ( $this->p->util->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
			}
			return $og_ret;
		}

		public function get_default_image( $num = 0, $size_name = 'thumbnail', $check_dupes = true ) {
			$this->p->debug->args( array( 'num' => $num, 'size_name' => $size_name, 'check_dupes' => $check_dupes ) );
			$og_ret = array();
			$og_image = array();
			$pid = empty( $this->p->options['og_def_img_id'] ) ? '' : $this->p->options['og_def_img_id'];
			$pre = empty( $this->p->options['og_def_img_id_pre'] ) ? '' : $this->p->options['og_def_img_id_pre'];
			$url = empty( $this->p->options['og_def_img_url'] ) ? '' : $this->p->options['og_def_img_url'];
			if ( $pid > 0 ) {
				if ( $this->p->is_avail['ngg'] == true && $pre == 'ngg' )
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
						$og_image['og:image:cropped'] ) = $this->ngg->get_image_src( $pre.'-'.$pid, $size_name, $check_dupes );
				else
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
						$og_image['og:image:cropped'] ) = $this->get_attachment_image_src( $pid, $size_name, $check_dupes );
			}
			if ( empty( $og_image['og:image'] ) && ! empty( $url ) ) {
				$og_image = array();	// clear all array values
				$og_image['og:image'] = $url;
				$this->p->debug->log( 'using default img url = ' . $og_image['og:image'] );
			}
			// returned array must be two-dimensional
			$this->p->util->push_max( $og_ret, $og_image, $num );
			return $og_ret;
		}

		public function get_content_images( $num = 0, $size_name = 'thumbnail', $check_dupes = true, $content = null ) {
			$og_ret = array();
			$size_info = $this->get_size_info( $size_name );
			// allow custom content to be passed
			if ( empty( $content ) )
				$content = $this->p->webpage->get_content( $this->p->options['plugin_filter_content'] );
			if ( empty( $content ) ) { 
				$this->p->debug->log( 'exiting early: empty post content' ); 
				return $og_ret; 
			}
			// check html tags for ngg images
			if ( $this->p->is_avail['ngg'] == true ) {
				$og_ret = $this->ngg->get_content_images( $num, $size_name, $check_dupes, $content );
				if ( $this->p->util->is_maxed( $og_ret, $num ) )
					return $og_ret;
			}
			// img attributes in order of preference
			if ( preg_match_all( '/<img[^>]*? (data-ngfb-wp-pid)=[\'"]([^\'"]+)[\'"][^>]*>/is', $content, $match, PREG_SET_ORDER ) ||
				preg_match_all( '/<img[^>]*? (share-'.$size_name.'|share|src)=[\'"]([^\'"]+)[\'"][^>]*>/is', $content, $match, PREG_SET_ORDER ) ) {
				$this->p->debug->log( count( $match ) . ' x matching <img/> html tag(s) found' );
				foreach ( $match as $img ) {
					$tag_value = $img[0];
					$attr_name = $img[1];
					$attr_value = $img[2];
					switch ( $attr_name ) {
						case 'data-ngfb-wp-pid' :
							list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
								$og_image['og:image:cropped'] ) = $this->get_attachment_image_src( $attr_value, $size_name, $check_dupes );

							if ( ! empty( $og_image['og:image'] ) && $this->p->util->push_max( $og_ret, $og_image, $num ) ) 
								break 2;	// exit the foreach if we have enough images

							break;
						default :
							$og_image = array(
								'og:image' => $this->p->util->get_sharing_url( 'asis', $attr_value ),
								'og:image:width' => '',
								'og:image:height' => '',
								'og:image:cropped' => '',
							);
							// check for ngg pre-v2 image pids in the url
							if ( $this->p->is_avail['ngg'] == true && 
								preg_match( '/\/cache\/([0-9]+)_(crop)?_[0-9]+x[0-9]+_[^\/]+$/', $og_image['og:image'], $match) ) {
		
								$this->p->debug->log( $attr_name . ' ngg pre-v2 cache image = ' . $og_image['og:image'] );
								list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
									$og_image['og:image:cropped'] ) = $this->ngg->get_image_src( 'ngg-'.$match[1], $size_name, $check_dupes );
		
							} elseif ( ( $check_dupes == false && ! empty( $og_image['og:image'] ) ) || 
								$this->p->util->is_uniq_url( $og_image['og:image'] ) == true ) {
		
								// try and get the width and height from the image tag
								if ( preg_match( '/ width=[\'"]?([0-9]+)[\'"]?/i', $tag_value, $match) ) 
									$og_image['og:image:width'] = $match[1];
								if ( preg_match( '/ height=[\'"]?([0-9]+)[\'"]?/i', $tag_value, $match) ) 
									$og_image['og:image:height'] = $match[1];
		
							} else continue;	// skip anything that is "not good" (duplicate or empty)

							// set value to 0 if not valid, to avoid error when comparing image sizes
							if ( ! is_numeric( $og_image['og:image:width'] ) ) 
								$og_image['og:image:width'] = 0;
							if ( ! is_numeric( $og_image['og:image:height'] ) ) 
								$og_image['og:image:height'] = 0;

							$this->p->debug->log( $attr_name . ' = ' . $og_image['og:image'] . 
								' (' . $og_image['og:image:width'] . 'x' . $og_image['og:image:height'] . ')' );

							// if we're picking up an img from 'src', make sure it's width and height is large enough
							if ( $attr_name == 'share-' . $size_name || $attr_name == 'share' || 
								( $attr_name == 'src' && defined( 'NGFB_MIN_IMG_SIZE_DISABLE' ) && NGFB_MIN_IMG_SIZE_DISABLE ) ||
								( $attr_name == 'src' && empty( $this->p->options['plugin_ignore_small_img'] ) ) ||
								( $attr_name == 'src' && $size_info['crop'] == 1 && 
									$og_image['og:image:width'] >= $size_info['width'] && $og_image['og:image:height'] >= $size_info['height'] ) ||
								( $attr_name == 'src' && $size_info['crop'] !== 1 && 
									( $og_image['og:image:width'] >= $size_info['width'] || $og_image['og:image:height'] >= $size_info['height'] ) ) ) {
		
								if ( ! empty( $og_image['og:image'] ) && $this->p->util->push_max( $og_ret, $og_image, $num ) )
									break 2;	// exit the foreach if we have enough images
		
							} else $this->p->debug->log( $attr_name . ' image rejected: width and height attributes missing or too small' );

							break;
					}


				}
				return $og_ret;	// return immediately and ignore any other type of image
			}
			$this->p->debug->log( 'no matching <img/> html tag(s) found' );

			return $og_ret;
		}

		// called by ngfbTwitterCards to build Gallery Card
		public function get_gallery_images( $num = 0, $size_name = 'large', $want_this = 'gallery', $check_dupes = false ) {
			global $post;
			$og_ret = array();
			if ( $want_this == 'gallery' ) {
				if ( empty( $post ) ) { 
					$this->p->debug->log( 'exiting early: empty post object' ); 
					return $og_ret;
				} elseif ( empty( $post->post_content ) ) { 
					$this->p->debug->log( 'exiting early: empty post content' ); 
					return $og_ret;
				}
				if ( preg_match( '/\[(gallery)[^\]]*\]/im', $post->post_content, $match ) ) {
					$shortcode_type = strtolower( $match[1] );
					$this->p->debug->log( '[' . $shortcode_type . '] shortcode found' );
					switch ( $shortcode_type ) {
						case 'gallery' :
							$content = do_shortcode( $match[0] );
							$content = preg_replace( '/\[' . $shortcode_type . '[^\]]*\]/', '', $content );	// prevent loops, just in case
							$og_ret = array_merge( $og_ret, $this->p->media->get_content_images( $num, $size_name, $check_dupes, $content ) );
							if ( ! empty( $og_ret ) ) return $og_ret;	// return immediately and ignore any other type of image
							break;
					}
				} else $this->p->debug->log( '[gallery] shortcode not found' );
			}
			// check for ngg gallery
			if ( $this->p->is_avail['ngg'] == true ) {
				$og_ret = $this->ngg->get_gallery_images( $num , $size_name, $want_this, $check_dupes );
				if ( $this->p->util->is_maxed( $og_ret, $num ) )
					return $og_ret;
			}
			$this->p->util->slice_max( $og_ret, $num );
			return $og_ret;
		}

		public function get_meta_video( $num = 0, $post_id = '', $check_dupes = true ) {
			$this->p->debug->args( array( 'num' => $num, 'post_id' => $post_id, 'check_dupes' => $check_dupes ) );
			$og_ret = array();
			if ( ! empty( $post_id ) ) {
				$embed_url = $this->p->meta->get_options( $post_id, 'og_vid_url' );
				if ( ( $check_dupes == false && ! empty( $embed_url ) ) || $this->p->util->is_uniq_url( $embed_url ) ) {
					$this->p->debug->log( 'found custom meta video url = ' . $embed_url );
					$og_video = $this->get_video_info( $embed_url );
					if ( $this->p->util->push_max( $og_ret, $og_video, $num ) ) return $og_ret;
				}
			}
			return $og_ret;
		}

		// called from the OpenGraph and Tumblr classes
		public function get_content_videos( $num = 0, $check_dupes = true ) {
			$this->p->debug->args( array( 'num' => $num, 'check_dupes' => $check_dupes ) );
			$og_ret = array();
			$content = $this->p->webpage->get_content( $this->p->options['plugin_filter_content'] );
			if ( empty( $content ) ) { $this->p->debug->log( 'exiting early: empty post content' ); return $og_ret; }
			if ( preg_match_all( '/<(iframe|embed)[^>]*? src=[\'"]([^\'"]+\/(embed|video)\/[^\'"]+)[\'"][^>]*>/i', $content, $match_all, PREG_SET_ORDER ) ) {
				$this->p->debug->log( count( $match_all ) . ' x video html tag(s) found' );
				foreach ( $match_all as $media ) {
					$this->p->debug->log( '<' . $media[1] . '/> html tag found = ' . $media[2] );
					$embed_url = $this->p->util->get_sharing_url( 'noquery', $media[2] );
					if ( ( $check_dupes == false && ! empty( $embed_url ) ) || $this->p->util->is_uniq_url( $embed_url ) ) {
						$embed_width = preg_match( '/ width=[\'"]?([0-9]+)[\'"]?/i', $media[0], $match) ? $match[1] : 0;
						$embed_height = preg_match( '/ height=[\'"]?([0-9]+)[\'"]?/i', $media[0], $match) ? $match[1] : 0;
						$og_video = $this->get_video_info( $embed_url, $embed_width, $embed_height );
						if ( $this->p->util->push_max( $og_ret, $og_video, $num ) ) return $og_ret;
					}
				}
			} else $this->p->debug->log( 'no <iframe|embed/> html tag(s) found' );
			return $og_ret;
		}

		private function get_video_info( $embed_url, $embed_width = 0, $embed_height = 0 ) {
			if ( empty( $embed_url ) ) return array();
			$og_video = array(
				'og:video' => '',
				'og:video:type' => 'application/x-shockwave-flash',
				'og:video:width' => $embed_width,
				'og:video:height' => $embed_height,
				'og:image' => '',
				'og:image:width' => '',
				'og:image:height' => '',
			);
			$prot = empty( $this->p->options['og_vid_https'] ) ? 'http://' : 'https://';

			if ( preg_match( '/^.*(wistia\.net|wistia\.com|wi\.st)\/([^\?\&\#]+).*$/i', $embed_url, $match ) ) {
				$vid_name = preg_replace( '/^.*\//', '', $match[2] );
				if ( function_exists( 'simplexml_load_string' ) ) {
					if ( defined( 'NGFB_WISTIA_API_PWD' ) && NGFB_WISTIA_API_PWD ) {
						$api_url = $prot . 'api.wistia.com/v1/medias/' . $vid_name . '.xml';
						$this->p->debug->log( 'fetching video details from ' . $api_url );
						$xml = @simplexml_load_string( $this->p->cache->get( $api_url, 'raw', 'transient', false, 'api:' . NGFB_WISTIA_API_PWD ) );
						if ( ! empty( $xml->embedCode ) ) {
							$embed = preg_match( '/<embed(.*)><\/embed>/i', (string) $xml->embedCode, $match ) ? $match[1] : '';
							$embed_src = preg_match( '/ src=[\'"]?([^\'"]+)[\'"]?/i', $embed, $match ) ? $match[1] : '';
							$embed_var = preg_match( '/ flashvars=[\'"]?([^\'"]+)[\'"]?/i', $embed, $match ) ? $match[1] : '';
							if ( ! empty( $embed_src ) && ! empty( $embed_var ) )
								$og_video['og:video'] = $embed_src . '?' . $embed_var;
							$og_video['og:video:width'] = preg_match( '/ width=[\'"]?([0-9]+)[\'"]?/i', $embed, $match ) ? $match[1] : '';
							$og_video['og:video:height'] = preg_match( '/ height=[\'"]?([0-9]+)[\'"]?/i', $embed, $match ) ? $match[1] : '';
						}
					}
					$api_url = $prot . 'fast.wistia.com/oembed.xml?url=http%3A//home.wistia.com/medias/' . $vid_name;
					$this->p->debug->log( 'fetching video details from ' . $api_url );
					$xml = @simplexml_load_string( $this->p->cache->get( $api_url, 'raw', 'transient' ) );
					if ( ! empty( $xml->thumbnail_url ) ) {
						$og_video['og:image'] = (string) $xml->thumbnail_url;
						$og_video['og:image:width'] = (string) $xml->thumbnail_width;
						$og_video['og:image:height'] = (string) $xml->thumbnail_height;
					}
					if ( ! empty( $this->p->options['og_vid_https'] ) ) {
						$og_video['og:video:secure_url'] = preg_replace( '/http:\/\/embed[^\.]*\./', 'https://embed-ssl.', $og_video['og:video'] );
						$og_video['og:image:secure_url'] = preg_replace( '/http:\/\/embed[^\.]*\./', 'https://embed-ssl.', $og_video['og:image'] );
					}
				}
			} elseif ( preg_match( '/^.*(youtube\.com|youtube-nocookie\.com|youtu\.be)\/([^\?\&\#]+).*$/i', $embed_url, $match ) ) {
				$vid_name = preg_replace( '/^.*\//', '', $match[2] );
				$og_video['og:video'] = $prot . 'www.youtube.com/v/' . $vid_name;
				$og_video['og:image'] = $prot . 'img.youtube.com/vi/' . $vid_name . '/0.jpg';	// 0, hqdefault, maxresdefault
				if ( function_exists( 'simplexml_load_string' ) ) {
					$api_url = $prot . 'gdata.youtube.com/feeds/api/videos?q=' . $vid_name . '&max-results=1&format=5';
					$this->p->debug->log( 'fetching video details from ' . $api_url );
					$xml = @simplexml_load_string( $this->p->cache->get( $api_url, 'raw', 'transient' ) );
					if ( ! empty( $xml->entry[0] ) ) {
						$this->p->debug->log( 'setting og:video and og:image from youtube api xml' );
						$media = $xml->entry[0]->children( 'media', true );
						$content = $media->group->content[0]->attributes();
						if ( $content['type'] == 'application/x-shockwave-flash' )
							$og_video['og:video'] = (string) $content['url'];
						// find the largest thumbnail available (by width)
						foreach ( $media->group->thumbnail as $thumb ) {
							$thumb_attr = $thumb->attributes();
							if ( ! empty( $thumb_attr['width'] ) ) {
								$thumb_url = (string) $thumb_attr['url'];
								$thumb_width = (string) $thumb_attr['width'];
								$thumb_height = (string) $thumb_attr['height'];
								if ( empty( $og_video['og:image:width'] ) || $thumb_width > $og_video['og:image:width'] )
									list( $og_video['og:image'], $og_video['og:image:width'], $og_video['og:image:height'] ) = 
										array( $thumb_url, $thumb_width, $thumb_height );
							}
						}
					}
				}
			} elseif ( preg_match( '/^.*(vimeo\.com)\/.*\/([^\/\?\&\#]+).*$/i', $embed_url, $match ) ) {
				$vid_name = preg_replace( '/^.*\//', '', $match[2] );
				$og_video['og:video'] = $prot . 'vimeo.com/moogaloop.swf?clip_id=' . $vid_name;
				if ( function_exists( 'simplexml_load_string' ) ) {
					$api_url = $prot . 'vimeo.com/api/oembed.xml?url=http%3A//vimeo.com/' . $vid_name;
					$this->p->debug->log( 'fetching video details from ' . $api_url );
					$xml = @simplexml_load_string( $this->p->cache->get( $api_url, 'raw', 'transient' ) );
					if ( ! empty( $xml->thumbnail_url ) ) {
						$this->p->debug->log( 'setting og:video and og:image from vimeo api xml' );
						$og_video['og:image'] = (string) $xml->thumbnail_url;
						$og_video['og:image:width'] = (string) $xml->thumbnail_width;
						$og_video['og:image:height'] = (string) $xml->thumbnail_height;
						$og_video['og:video:width'] = $og_video['og:image:width'];
						$og_video['og:video:height'] = $og_video['og:image:height'];
					}
				}
			}
			$this->p->debug->log( 'image = '.$og_video['og:image'].' ('.$og_video['og:image:width'].'x'.$og_video['og:image:height'].')' );
			$this->p->debug->log( 'video = '.$og_video['og:video'].' ('.$og_video['og:video:width'].'x'.$og_video['og:video:height'].')' );

			if ( empty( $og_video['og:video'] ) ) {
				unset ( 
					$og_video['og:video'],
					$og_video['og:video:type'],
					$og_video['og:video:width'],
					$og_video['og:video:height']
				);
			}
			if ( empty( $og_video['og:video'] ) && empty( $og_video['og:image'] ) ) return array();
			else return $og_video;
		}
		
	}
}

?>

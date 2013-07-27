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

		private $ngfb;		// ngfbPlugin

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
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

		public function get_featured( $num = 0, $size_name = 'thumbnail', $post_id, $check_dupes = true ) {
			$og_ret = array();
			$og_image = array();
			$size_info = $this->get_size_info( $size_name );
			if ( ! empty( $post_id ) && $this->ngfb->is_avail['postthumb'] == true && has_post_thumbnail( $post_id ) ) {
				$pid = get_post_thumbnail_id( $post_id );
				if ( is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' ) {
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
						$og_image['og:image:cropped'] ) = $this->get_ngg_image_src( $pid, $size_name, $check_dupes );
				} else {
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
						$og_image['og:image:cropped'] ) = $this->get_attachment_image_src( $pid, $size_name, $check_dupes );
				}
				if ( ! empty( $og_image['og:image'] ) )
					$this->ngfb->util->push_max( $og_ret, $og_image, $num );
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
			$og_ret = array();
			if ( ! empty( $attach_id ) ) {
				if ( wp_attachment_is_image( $attach_id ) ) {	// since wp 2.1.0 
					$og_image = array();
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
						$og_image['og:image:cropped'] ) = $this->get_attachment_image_src( $attach_id, $size_name, $check_dupes );
					if ( ! empty( $og_image['og:image'] ) )
						$this->ngfb->util->push_max( $og_ret, $og_image, $num );
				} else $this->ngfb->debug->log( 'attachment id ' . $attach_id . ' is not an image' );
			}
			return $og_ret;
		}

		public function get_attached_images( $num = 0, $size_name = 'thumbnail', $post_id = '', $check_dupes = true ) {
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
								$this->ngfb->util->push_max( $og_ret, $og_image, $num );
						}
					}
			}
			return $og_ret;
		}

		public function get_attachment_image_src( $pid, $size_name = 'thumbnail', $check_dupes = true ) {
			$size_info = $this->get_size_info( $size_name );
			$img_url = '';
			$img_width = 0;
			$img_height = 0;
			$img_crop = $size_info['crop'] == 1 ? 'true' : 'false';	// visual feedback, not a real true / false

			list( $img_url, $img_width, $img_height ) = wp_get_attachment_image_src( $pid, $size_name );
			$this->ngfb->debug->log( 'image for pid:' . $pid . ' size:' . $size_name . ' = ' . $img_url . ' (' . $img_width . 'x' . $img_height . ')' );
			$img_url = $this->ngfb->util->fix_relative_url( $img_url );

			if ( ! empty( $img_url ) ) {
				/* skip images from the WP media library that are too small */
				/*
				if ( empty( $this->ngfb->options['ngfb_skip_small_img'] ) ||
					( defined( 'NGFB_MIN_IMG_SIZE_DISABLE' ) && NGFB_MIN_IMG_SIZE_DISABLE ) ||
					( $size_info['crop'] == 1 && $img_width >= $size_info['width'] && $img_height >= $size_info['height'] ) ||
					( $size_info['crop'] !== 1 && ( $img_width >= $size_info['width'] || $img_height >= $size_info['height'] ) ) ) {

					if ( $check_dupes == false || $this->ngfb->util->is_uniq_url( $img_url ) )
						return array( $this->ngfb->util->rewrite( $img_url ), $img_width, $img_height, $img_crop );

				} else $this->ngfb->debug->log( 'image rejected: returned image is smaller than \'' . 
					$size_name . '\' (' . $size_info['width'] . 'x' . $size_info['height'] . 
						( $size_info['crop'] ? ', cropped' : ', not cropped' ) . ')' );
				*/

				if ( $check_dupes == false || $this->ngfb->util->is_uniq_url( $img_url ) )
					return array( $this->ngfb->util->rewrite( $img_url ), $img_width, $img_height, $img_crop );

			} else $this->ngfb->debug->log( 'image rejected: image url is empty' );
			return array( null, null, null, null );
		}

		// called to get an image URL from an NGG picture ID and a media size name (the pid must be formatted as 'ngg-#')
		public function get_ngg_image_src( $pid, $size_name = 'thumbnail', $check_dupes = true ) {
			if ( $this->ngfb->is_avail['ngg'] != true ) return;
			$size_info = $this->get_size_info( $size_name );
			$img_url = '';
			$img_crop = $size_info['crop'] == 1 ? 'true' : 'false';
			$crop_arg = $size_info['crop'] == 1 ? 'crop' : '';

			if ( is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' ) {
				global $nggdb;
				$pid = substr( $pid, 4 );
				$image = $nggdb->find_image( $pid );	// returns an nggImage object
				if ( ! empty( $image ) ) {
					$img_url = $image->cached_singlepic_file( $size_info['width'], $size_info['height'], $crop_arg ); 
					if ( empty( $img_url ) )	// if the image file doesn't exist, use the dynamic image url
						$img_url = trailingslashit( site_url() ) . 
							'index.php?callback=image&amp;pid=' . $pid .
							'&amp;width=' . $size_info['width'] . 
							'&amp;height=' . $size_info['height'] . 
							'&amp;mode=' . $crop_arg;
					else {
						// get the REAL image width and height
						$cachename = $image->pid . '_' . $crop_arg . '_'. $size_info['width'] . 'x' . $size_info['height'] . '_' . $image->filename;
						$cachefolder = WINABSPATH . $this->ngfb->ngg_options['gallerypath'] . 'cache/';
						$cached_url = site_url() . '/' . $this->ngfb->ngg_options['gallerypath'] . 'cache/' . $cachename;
						$cached_file = $cachefolder . $cachename;
						$file_info =  getimagesize( $cached_file );
						if ( ! empty( $file_info[0] ) && ! empty( $file_info[1] ) ) {
							$size_info['width'] = $file_info[0];
							$size_info['height'] = $file_info[1];
						}
					}
				}
			}
			$this->ngfb->debug->log( 'image for pid:' . $pid . ' size:' . $size_name . ' = ' . $img_url . ' (' . $size_info['width'] . 'x' . $size_info['height'] . ')' );
			$img_url = $this->ngfb->util->fix_relative_url( $img_url );

			if ( ! empty( $img_url ) ) {

				if ( $check_dupes == false || $this->ngfb->util->is_uniq_url( $img_url ) )
					return array( $this->ngfb->util->rewrite( $img_url ), $size_info['width'], $size_info['height'], $img_crop );

			} else $this->ngfb->debug->log( 'image rejected: image url is empty' );
			return array( null, null, null, null );
		}

		public function get_gallery_images( $num = 0, $size_name = 'large', $want_this = 'gallery', $check_dupes = false ) {
			$og_ret = array();

			if ( $this->ngfb->is_avail['ngg'] !== true ) 
				return $og_ret;

			global $post, $wp_query, $nggdb;
			$size_info = $this->get_size_info( $size_name );

			if ( empty( $post ) ) { 
				$this->ngfb->debug->log( 'exiting early: empty post object' ); return $og_ret;
			} elseif ( empty( $post->post_content ) ) { 
				$this->ngfb->debug->log( 'exiting early: empty post content' ); return $og_ret;
			}

			// sanitize possible query values
			$ngg_album = empty( $wp_query->query['album'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['album'] );
			$ngg_gallery = empty( $wp_query->query['gallery'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['gallery'] );
			$ngg_pageid = empty( $wp_query->query['pageid'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['pageid'] );
			$ngg_pid = empty( $wp_query->query['pid'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['pid'] );

			if ( ! empty( $ngg_album ) || ! empty( $ngg_gallery ) || ! empty( $ngg_pid ) ) {
				$this->ngfb->debug->log( 'ngg query found = pageid:' . $ngg_pageid . ' album:' . $ngg_album . 
					' gallery:' . $ngg_gallery . ' pid:' . $ngg_pid );
			}

			if ( $want_this == 'gallery' && $ngg_pid > 0 ) {
				$this->ngfb->debug->log( 'exiting early: want gallery but have query for pid:' . $ngg_pid );
				return $og_ret;
			} elseif ( $want_this == 'pid' && empty( $ngg_pid ) ) {
				$this->ngfb->debug->log( 'exiting early: want pid but don\'t have a query for pid' );
				return $og_ret;
			}

			if ( preg_match( '/\[(nggalbum|album|nggallery|nggtags)(| [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*| [^\]]*)\]/im', $post->post_content, $match ) ) {
				$shortcode_type = strtolower( $match[1] );
				$shortcode_id = ! empty( $match[3] ) ? $match[3] : 0;
				$this->ngfb->debug->log( '[' . $shortcode_type . '] shortcode found (id:' . $shortcode_id . ')' );

				switch ( $shortcode_type ) {
					case 'nggtags' :
						$content = do_shortcode( $match[0] );
						$og_ret = array_merge( $og_ret, $this->get_content_images( $num, $size_name, $check_dupes, $content ) );
						break;
					default :
						// always trust hard-coded shortcode ID more than query arguments
						$ngg_album = $shortcode_type == 'nggalbum' || $shortcode_type == 'album' ? $shortcode_id : $ngg_album;
						$ngg_gallery = $shortcode_type == 'nggallery' ? $shortcode_id : $ngg_gallery;
		
						// security checks
						if ( $ngg_gallery > 0 && $ngg_album > 0 ) {
							$nggAlbum = $nggdb->find_album( $ngg_album );
							if ( in_array( $ngg_gallery, $nggAlbum->gallery_ids, true ) ) {
								$this->ngfb->debug->log( 'security check passed = gallery:' . $ngg_gallery . ' is in album:' . $ngg_album );
							} else {
								$this->ngfb->debug->log( 'security check failed = gallery:' . $ngg_gallery . ' is not in album:' . $ngg_album );
								return $og_ret;
							}
						}
		
						if ( $ngg_pid > 0 && $ngg_gallery > 0 ) {
							$pids = $nggdb->get_ids_from_gallery( $ngg_gallery );
							if ( in_array( $ngg_pid, $pids, true ) ) {
								$this->ngfb->debug->log( 'security check passed = pid:' . $ngg_pid . ' is in gallery:' . $ngg_gallery );
							} else {
								$this->ngfb->debug->log( 'security check failed = pid:' . $ngg_pid . ' is not in gallery:' . $ngg_gallery );
								return $og_ret;
							}
						}
		
						switch ( $want_this ) {
							case 'gallery' :
								if ( $ngg_gallery > 0 ) {
									// get_ids_from_gallery($id, $order_by = 'sortorder', $order_dir = 'ASC', $exclude = true)
									foreach ( array_slice( $nggdb->get_ids_from_gallery( $ngg_gallery, 'sortorder', 'ASC', true ), 0, $num ) as $pid ) {
										list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
											$og_image['og:image:cropped'] )= $this->get_ngg_image_src( 'ngg-' . $pid, $size_name, $check_dupes );
										if ( $this->ngfb->util->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
									}
								}
								break;
							case 'pid' :
								if ( $ngg_pid > 0 ) {
									list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
										$og_image['og:image:cropped'] )= $this->get_ngg_image_src( 'ngg-' . $ngg_pid, $size_name, $check_dupes );
									if ( $this->ngfb->util->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
								}
								break;
						}
						break;
				}	
			} else $this->ngfb->debug->log( '[nggalbum|album|nggallery|nggtags] shortcode not found' );

			$this->ngfb->util->slice_max( $og_ret, $num );
			return $og_ret;
		}

		public function get_ngg_query_images( $num = 0, $size_name = 'thumbnail', $check_dupes = true ) {
			$og_ret = array();

			if ( $this->ngfb->is_avail['ngg'] !== true ) 
				return $og_ret;

			global $post, $wp_query, $nggdb;
			$size_info = $this->get_size_info( $size_name );

			if ( empty( $post ) ) { 
				$this->ngfb->debug->log( 'exiting early: empty post object' ); return $og_ret; 
			} elseif ( empty( $post->post_content ) ) { 
				$this->ngfb->debug->log( 'exiting early: empty post content' ); return $og_ret;
			}

			// sanitize possible query values
			$ngg_album = empty( $wp_query->query['album'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['album'] );
			$ngg_gallery = empty( $wp_query->query['gallery'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['gallery'] );
			$ngg_pageid = empty( $wp_query->query['pageid'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['pageid'] );
			$ngg_pid = empty( $wp_query->query['pid'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['pid'] );

			if ( empty( $ngg_album ) && empty( $ngg_gallery ) && empty( $ngg_pid ) ) {
				$this->ngfb->debug->log( 'exiting early: no ngg query values' ); return $og_ret;
			} else {
				$this->ngfb->debug->log( 'ngg query found = pageid:' . $ngg_pageid . ' album:' . $ngg_album . 
					' gallery:' . $ngg_gallery . ' pid:' . $ngg_pid );
			}

			if ( preg_match( '/\[(nggalbum|album|nggallery)(| [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*| [^\]]*)\]/im', $post->post_content, $match ) ) {
				$shortcode_type = $match[1];
				$shortcode_id = ! empty( $match[3] ) ? $match[3] : 0;
				$this->ngfb->debug->log( 'ngg query with [' . $shortcode_type . '] shortcode (id:' . $shortcode_id . ')' );

				// always trust hard-coded shortcode ID more than query arguments
				$ngg_album = $shortcode_type == 'nggalbum' || $shortcode_type == 'album' ? $shortcode_id : $ngg_album;
				$ngg_gallery = $shortcode_type == 'nggallery' ? $shortcode_id : $ngg_gallery;

				// security checks
				if ( $ngg_gallery > 0 && $ngg_album > 0 ) {
					$nggAlbum = $nggdb->find_album( $ngg_album );
					if ( in_array( $ngg_gallery, $nggAlbum->gallery_ids, true ) ) {
						$this->ngfb->debug->log( 'security check passed = gallery:' . $ngg_gallery . ' is in album:' . $ngg_album );
					} else {
						$this->ngfb->debug->log( 'security check failed = gallery:' . $ngg_gallery . ' is not in album:' . $ngg_album );
						return $og_ret;
					}
				}
				if ( $ngg_pid > 0 && $ngg_gallery > 0 ) {
					$pids = $nggdb->get_ids_from_gallery( $ngg_gallery );
					if ( in_array( $ngg_pid, $pids, true ) ) {
						$this->ngfb->debug->log( 'security check passed = pid:' . $ngg_pid . ' is in gallery:' . $ngg_gallery );
					} else {
						$this->ngfb->debug->log( 'security check failed = pid:' . $ngg_pid . ' is not in gallery:' . $ngg_gallery );
						return $og_ret;
					}
				}

				if ( $ngg_pid > 0 ) {
					$this->ngfb->debug->log( 'getting image for ngg query pid:' . $ngg_pid );
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
						$og_image['og:image:cropped'] ) = $this->get_ngg_image_src( 'ngg-' . $ngg_pid, $size_name, $check_dupes );
					if ( $this->ngfb->util->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
				} elseif ( $ngg_gallery > 0 ) {
					$gallery = $nggdb->find_gallery( $ngg_gallery );
					if ( ! empty( $gallery ) ) {
						if ( ! empty( $gallery->previewpic ) ) {
							$this->ngfb->debug->log( 'getting previewpic:' . $gallery->previewpic . ' for gallery:' . $ngg_gallery );
							list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
								$og_image['og:image:cropped'] ) = $this->get_ngg_image_src( 'ngg-' . $gallery->previewpic, $size_name, $check_dupes );
							if ( $this->ngfb->util->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
						} else $this->ngfb->debug->log( 'no previewpic for gallery:' . $ngg_gallery );
					} else $this->ngfb->debug->log( 'no gallery:' . $ngg_gallery . ' found' );
				} elseif ( $ngg_album > 0 ) {
					$album = $nggfb->find_album( $ngg_album );
					if ( ! empty( $albums ) ) {
						if ( ! empty( $album->previewpic ) ) {
							$this->ngfb->debug->log( 'getting previewpic:' . $album->previewpic . ' for album:' . $ngg_album );
							list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
								$og_image['og:image:cropped'] ) = $this->get_ngg_image_src( 'ngg-' . $album->previewpic, $size_name, $check_dupes );
							if ( $this->ngfb->util->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
						} else $this->ngfb->debug->log( 'no previewpic for album:' . $ngg_album );
					} else $this->ngfb->debug->log( 'no album:' . $ngg_album . ' found' );
				}
			} else $this->ngfb->debug->log( 'ngg query without [nggalbum|album|nggallery] shortcode' );

			$this->ngfb->util->slice_max( $og_ret, $num );
			return $og_ret;
		}

		public function get_ngg_shortcode_images( $num = 0, $size_name = 'thumbnail', $check_dupes = true ) {
			$og_ret = array();

			if ( $this->ngfb->is_avail['ngg'] !== true ) 
				return $og_ret;

			global $post, $wpdb;
			$size_info = $this->get_size_info( $size_name );

			if ( empty( $post ) ) { $this->ngfb->debug->log( 'exiting early: empty post object' ); return $og_ret; } 
			elseif ( empty( $post->post_content ) ) { $this->ngfb->debug->log( 'exiting early: empty post content' ); return $og_ret; }

			if ( preg_match_all( '/\[(nggalbum|album)(| [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*| [^\]]*)\]/im', $post->post_content, $match, PREG_SET_ORDER ) ) {
				foreach ( $match as $album ) {
					$this->ngfb->debug->log( '[' . $album[1] . '] shortcode found (id:' . $album[3] . ')' );
					$og_image = array();
					if ( empty( $album[3] ) ) {
						$ngg_album = 0;
						$this->ngfb->debug->log( 'album id zero or not found - setting album id to 0 (all)' );
					} else $ngg_album = $album[3];
					if ( $ngg_album > 0 ) 
						$albums = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->nggalbum . ' WHERE id IN (\'' . $ngg_album . '\')', OBJECT_K );
					else $albums = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->nggalbum, OBJECT_K );
					if ( is_array( $albums ) ) {
						foreach ( $albums as $row ) {
							if ( ! empty( $row->previewpic ) ) {
								$this->ngfb->debug->log( 'getting previewpic:' . $row->previewpic . ' for album:' . $row->id );
								list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
									$og_image['og:image:cropped'] ) = $this->get_ngg_image_src( 'ngg-' . $row->previewpic, $size_name, $check_dupes );
								if ( $this->ngfb->util->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
							} else $this->ngfb->debug->log( 'no previewpic for album:' . $row->id );
						}
					} else $this->ngfb->debug->log( 'no album(s) found' );
				}
			} else $this->ngfb->debug->log( 'no [nggalbum|album] shortcode found' );

			if ( preg_match_all( '/\[(nggallery) [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*\]/im', $post->post_content, $match, PREG_SET_ORDER ) ) {
				foreach ( $match as $gallery ) {
					$this->ngfb->debug->log( '[' . $gallery[1] . '] shortcode found (id:' . $gallery[2] . ')' );
					$og_image = array();
					$ngg_gallery = $gallery[2];
					$galleries = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->nggallery . ' WHERE gid IN (\'' . $ngg_gallery . '\')', OBJECT_K );
					if ( is_array( $galleries ) ) {
						foreach ( $galleries as $row ) {
							if ( ! empty( $row->previewpic ) ) {
								$this->ngfb->debug->log( 'getting previewpic:' . $row->previewpic . ' for gallery:' . $row->gid );
								list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
									$og_image['og:image:cropped'] ) = $this->get_ngg_image_src( 'ngg-' . $row->previewpic, $size_name, $check_dupes );
								if ( $this->ngfb->util->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
							} else $this->ngfb->debug->log( 'no previewpic for gallery:' . $row->gid );
						}
					} else $this->ngfb->debug->log( 'no gallery:' . $ngg_gallery . ' found' );
				}
			} else $this->ngfb->debug->log( 'no [nggallery] shortcode found' );

			if ( preg_match_all( '/\[(singlepic) [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*\]/im', $post->post_content, $match, PREG_SET_ORDER ) ) {
				foreach ( $match as $singlepic ) {
					$this->ngfb->debug->log( '[' . $singlepic[1] . '] shortcode found (id:' . $singlepic[2] . ')' );
					$og_image = array();
					$pid = $singlepic[2];
					$this->ngfb->debug->log( 'getting image for singlepic:' . $pid );
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
						$og_image['og:image:cropped'] ) = $this->get_ngg_image_src( 'ngg-' . $pid, $size_name, $check_dupes );
					if ( $this->ngfb->util->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
				}
			} else $this->ngfb->debug->log( 'no [singlepic] shortcode found' );

			$this->ngfb->util->slice_max( $og_ret, $num );
			return $og_ret;
		}

		public function get_meta_image( $num = 0, $size_name = 'thumbnail', $post_id = '', $check_dupes = true ) {
			$image = array();
			$og_ret = array();
			if ( empty( $post_id ) ) return $og_ret;

			$pid = $this->ngfb->meta->get_options( $post_id, 'og_img_id' );
			$pre = $this->ngfb->meta->get_options( $post_id, 'og_img_id_pre' );
			$url = $this->ngfb->meta->get_options( $post_id, 'og_img_url' );

			if ( $pid > 0 ) {
				if ( $pre == 'ngg' ) {
					$this->ngfb->debug->log( 'found custom meta image id = ' . $pre . '-' . $pid );
					$image = $this->get_ngg_image_src( $pre . '-' . $pid, $size_name, $check_dupes );
				} else {
					$this->ngfb->debug->log( 'found custom meta image id = ' . $pid );
					$image = $this->get_attachment_image_src( $pid, $size_name, $check_dupes );
				}
			} elseif ( ! empty( $url ) ) {
				$this->ngfb->debug->log( 'found custom meta image url = ' . $url );
				$image[] = $url;
			}

			if ( ! empty( $image ) ) {
				list( $og_image['og:image'], $og_image['og:image:width'], 
					$og_image['og:image:height'], $og_image['og:image:cropped'] ) = $image;
				if ( ! empty( $og_image['og:image'] ) )
					if ( $this->ngfb->util->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
			}
			return $og_ret;
		}

		public function get_singlepic_images( $num = 0, $size_name = 'thumbnail', $check_dupes = false ) {
			$og_ret = array();

			if ( $this->ngfb->is_avail['ngg'] !== true ) 
				return $og_ret;

			global $post;
			$size_info = $this->get_size_info( $size_name );

			if ( empty( $post ) ) { $this->ngfb->debug->log( 'exiting early: empty post object' ); return $og_ret; } 
			elseif ( empty( $post->post_content ) ) { $this->ngfb->debug->log( 'exiting early: empty post content' ); return $og_ret; }

			if ( preg_match_all( '/\[(singlepic) [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*\]/im', $post->post_content, $match, PREG_SET_ORDER ) ) {
				foreach ( $match as $singlepic ) {
					$this->ngfb->debug->log( '[' . $singlepic[1] . '] shortcode found (id:' . $singlepic[2] . ')' );
					$og_image = array();
					$pid = $singlepic[2];
					$this->ngfb->debug->log( 'getting image for singlepic:' . $pid );
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
						$og_image['og:image:cropped'] ) = $this->get_ngg_image_src( 'ngg-' . $pid, $size_name, $check_dupes );
					if ( $this->ngfb->util->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
				}
			} else $this->ngfb->debug->log( 'no [singlepic] shortcode found' );

			$this->ngfb->util->slice_max( $og_ret, $num );
			return $og_ret;
		}

		public function get_default_image( $num = 0, $size_name = 'thumbnail', $check_dupes = true ) {
			$og_ret = array();
			$og_image = array();
			$pid = empty( $this->ngfb->options['og_def_img_id'] ) ? '' : $this->ngfb->options['og_def_img_id'];
			$pre = empty( $this->ngfb->options['og_def_img_id_pre'] ) ? '' : $this->ngfb->options['og_def_img_id_pre'];
			$url = empty( $this->ngfb->options['og_def_img_url'] ) ? '' : $this->ngfb->options['og_def_img_url'];
			if ( $pid > 0 ) {
				if ( $pre == 'ngg' )
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
						$og_image['og:image:cropped'] ) = $this->get_ngg_image_src( $pre . '-' . $pid, $size_name, $check_dupes );
				else
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
						$og_image['og:image:cropped'] ) = $this->get_attachment_image_src( $pid, $size_name );
			}
			if ( empty( $og_image['og:image'] ) && ! empty( $url ) ) {
				$og_image = array();	// clear all array values
				$og_image['og:image'] = $url;
				$this->ngfb->debug->log( 'using default img url = ' . $og_image['og:image'] );
			}
			// returned array must be two-dimensional
			$this->ngfb->util->push_max( $og_ret, $og_image, $num );
			return $og_ret;
		}

		// called from the view/gallery-meta.php template
		public function get_ngg_images( $num = 0, $size_name = 'thumbnail', $ngg_images = array() ) {
			$og_ret = array();
			if ( is_array( $ngg_images ) ) {
				foreach ( $ngg_images as $image ) {
					if ( ! empty( $image->pid ) ) {
						$og_image = array();
						list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
							$og_image['og:image:cropped'] ) = $this->get_ngg_image_src( 'ngg-' . $image->pid, $size_name );
						$this->ngfb->util->push_max( $og_ret, $og_image, $num );
					}
				}
			}
			return $og_ret;
		}

		public function get_content_images( $num = 0, $size_name = 'thumbnail', $check_dupes = true, $content = null ) {
			global $post;
			$og_ret = array();
			$size_info = $this->get_size_info( $size_name );

			// allow custom content to be passed
			if ( empty( $content ) ) {
				$this->ngfb->debug->log( 'calling this->ngfb->webpage->get_content()' );
				$content = $this->ngfb->webpage->get_content( $this->ngfb->options['ngfb_filter_content'] );
			}
			if ( empty( $content ) ) { 
				$this->ngfb->debug->log( 'exiting early: empty post content' ); 
				return $og_ret; 
			}

			// check for ngg image ids
			if ( preg_match_all( '/<div[^>]*? id=[\'"]ngg-image-([0-9]+)[\'"][^>]*>/is', $content, $match, PREG_SET_ORDER ) ) {
				$this->ngfb->debug->log( count( $match ) . ' x <div id="ngg-image-#"> html tag(s) found' );
				foreach ( $match as $pid ) {
					$og_image = array();
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
						$og_image['og:image:cropped'] ) = $this->get_ngg_image_src( 'ngg-' . $pid[1], $size_name, $check_dupes );
					if ( $this->ngfb->util->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
				}
			} else $this->ngfb->debug->log( 'no <div id="ngg-image-#"> html tag found' );

			// img attributes in order of preference
			if ( preg_match_all( '/<img[^>]*? (share-'.$size_name.'|share|src)=[\'"]([^\'"]+)[\'"][^>]*>/is', $content, $match, PREG_SET_ORDER ) ) {
				$this->ngfb->debug->log( count( $match ) . ' x <img/> html tag(s) found' );
				foreach ( $match as $img ) {
					$src_name = $img[1];
					$og_image = array(
						'og:image' => $this->ngfb->util->get_sharing_url( 'asis', $img[2] ),
						'og:image:width' => '',
						'og:image:height' => '',
						'og:image:cropped' => '',
					);

					// check for NGG image pids
					if ( preg_match( '/\/cache\/([0-9]+)_(crop)?_[0-9]+x[0-9]+_[^\/]+$/', $og_image['og:image'], $match) ) {
						$this->ngfb->debug->log( $src_name . ' ngg cache image = ' . $og_image['og:image'] );
						list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
							$og_image['og:image:cropped'] ) = $this->get_ngg_image_src( 'ngg-' . $match[1], $size_name, $check_dupes );
					} elseif ( ( $check_dupes == false && ! empty( $og_image['og:image'] ) ) || 
						$this->ngfb->util->is_uniq_url( $og_image['og:image'] ) == true ) {
						if ( preg_match( '/ width=[\'"]?([0-9]+)[\'"]?/i', $img[0], $match) ) 
							$og_image['og:image:width'] = $match[1];
						if ( preg_match( '/ height=[\'"]?([0-9]+)[\'"]?/i', $img[0], $match) ) 
							$og_image['og:image:height'] = $match[1];
					} else continue;	// skip anything that is "not good" (duplicate or empty)

					// set value to 0 if not valid, to avoid error when comparing image sizes
					if ( ! is_numeric( $og_image['og:image:width'] ) ) 
						$og_image['og:image:width'] = 0;
					if ( ! is_numeric( $og_image['og:image:height'] ) ) 
						$og_image['og:image:height'] = 0;

					$this->ngfb->debug->log( $src_name . ' = ' . $og_image['og:image'] . 
						' (' . $og_image['og:image:width'] . 'x' . $og_image['og:image:height'] . ')' );

					// if we're picking up an img from 'src', make sure it's width and height is large enough
					if ( $src_name == 'share-' . $size_name || $src_name == 'share' || 
						( $src_name == 'src' && defined( 'NGFB_MIN_IMG_SIZE_DISABLE' ) && NGFB_MIN_IMG_SIZE_DISABLE ) ||
						( $src_name == 'src' && empty( $this->ngfb->options['ngfb_skip_small_img'] ) ) ||
						( $src_name == 'src' && $size_info['crop'] == 1 && 
							$og_image['og:image:width'] >= $size_info['width'] && $og_image['og:image:height'] >= $size_info['height'] ) ||
						( $src_name == 'src' && $size_info['crop'] !== 1 && 
							( $og_image['og:image:width'] >= $size_info['width'] || $og_image['og:image:height'] >= $size_info['height'] ) ) ) {

						if ( $this->ngfb->util->push_max( $og_ret, $og_image, $num ) ) return $og_ret;

					} else $this->ngfb->debug->log( $src_name . ' image rejected: width and height attributes missing or too small' );
				}
			} else $this->ngfb->debug->log( 'no <img/> html tag(s) found' );

			return $og_ret;
		}

		// called from the OpenGraph and Tumblr classes
		public function get_content_videos( $num = 0, $check_dupes = true ) {
			$og_ret = array();
			$this->ngfb->debug->log( 'calling this->ngfb->webpage->get_content()' );
			$content = $this->ngfb->webpage->get_content( $this->ngfb->options['ngfb_filter_content'] );
			if ( empty( $content ) ) { $this->ngfb->debug->log( 'exiting early: empty post content' ); return $og_ret; }
			if ( preg_match_all( '/<(iframe|embed)[^>]*? src=[\'"]([^\'"]+\/(embed|video)\/[^\'"]+)[\'"][^>]*>/i', $content, $match_all, PREG_SET_ORDER ) ) {
				$this->ngfb->debug->log( count( $match_all ) . ' x video html tag(s) found' );
				foreach ( $match_all as $media ) {
					$this->ngfb->debug->log( '<' . $media[1] . '/> html tag found = ' . $media[2] );
					$embed_url = $this->ngfb->util->get_sharing_url( 'noquery', $media[2] );
					if ( ( $check_dupes == false && ! empty( $embed_url ) ) || $this->ngfb->util->is_uniq_url( $embed_url ) ) {
						$embed_width = preg_match( '/ width=[\'"]?([0-9]+)[\'"]?/i', $media[0], $match) ? $match[1] : 0;
						$embed_height = preg_match( '/ height=[\'"]?([0-9]+)[\'"]?/i', $media[0], $match) ? $match[1] : 0;
						$og_video = $this->get_video_info( $embed_url, $embed_width, $embed_height );
						if ( $this->ngfb->util->push_max( $og_ret, $og_video, $num ) ) return $og_ret;
					}
				}
			} else $this->ngfb->debug->log( 'no <iframe|embed/> html tag(s) found' );
			return $og_ret;
		}

		public function get_meta_video( $num = 0, $post_id = '', $check_dupes = true ) {
			$og_ret = array();
			if ( ! empty( $post_id ) ) {
				$embed_url = $this->ngfb->meta->get_options( $post_id, 'og_vid_url' );
				if ( ( $check_dupes == false && ! empty( $embed_url ) ) || $this->ngfb->util->is_uniq_url( $embed_url ) ) {
					$this->ngfb->debug->log( 'found custom meta video url = ' . $embed_url );
					$og_video = $this->get_video_info( $embed_url );
					if ( $this->ngfb->util->push_max( $og_ret, $og_video, $num ) ) return $og_ret;
				}
			}
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
			$prot = empty( $this->ngfb->options['og_vid_https'] ) ? 'http://' : 'https://';

			if ( preg_match( '/^.*(wistia\.net|wistia\.com|wi\.st)\/([^\?\&\#]+).*$/i', $embed_url, $match ) ) {
				$vid_name = preg_replace( '/^.*\//', '', $match[2] );
				if ( function_exists( 'simplexml_load_string' ) ) {
					if ( defined( 'NGFB_WISTIA_API_PWD' ) && NGFB_WISTIA_API_PWD ) {
						$api_url = $prot . 'api.wistia.com/v1/medias/' . $vid_name . '.xml';
						$this->ngfb->debug->log( 'fetching video details from ' . $api_url );
						$xml = @simplexml_load_string( $this->ngfb->cache->get( $api_url, 'raw', 'transient', false, 'api:' . NGFB_WISTIA_API_PWD ) );
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
					$this->ngfb->debug->log( 'fetching video details from ' . $api_url );
					$xml = @simplexml_load_string( $this->ngfb->cache->get( $api_url, 'raw', 'transient' ) );
					if ( ! empty( $xml->thumbnail_url ) ) {
						$og_video['og:image'] = (string) $xml->thumbnail_url;
						$og_video['og:image:width'] = (string) $xml->thumbnail_width;
						$og_video['og:image:height'] = (string) $xml->thumbnail_height;
					}
					if ( ! empty( $this->ngfb->options['og_vid_https'] ) ) {
						$og_video['og:video:secure_url'] = preg_replace( '/http:\/\/embed[^\.]*\./', 'https://embed-ssl.', $og_video['og:video'] );
						$og_video['og:image:secure_url'] = preg_replace( '/http:\/\/embed[^\.]*\./', 'https://embed-ssl.', $og_video['og:image'] );
					}
				}
			} elseif ( preg_match( '/^.*(youtube\.com|youtube-nocookie\.com|youtu\.be)\/([^\?\&\#]+).*$/i', $embed_url, $match ) ) {
				$vid_name = preg_replace( '/^.*\//', '', $match[2] );
				$og_video['og:video'] = $prot . 'www.youtube.com/v/' . $vid_name;
				$og_video['og:image'] = $prot . 'img.youtube.com/vi/' . $vid_name . '/0.jpg';
				if ( function_exists( 'simplexml_load_string' ) ) {
					$api_url = $prot . 'gdata.youtube.com/feeds/api/videos?q=' . $vid_name;
					$this->ngfb->debug->log( 'fetching video details from ' . $api_url );
					$xml = @simplexml_load_string( $this->ngfb->cache->get( $api_url, 'raw', 'transient' ) );
					if ( ! empty( $xml->entry[0] ) ) {
						$this->ngfb->debug->log( 'setting og:image from youtube api xml' );
						$media = $xml->entry[0]->children( 'media', true );
						$content = $media->group->content[0]->attributes();
						$thumb = $media->group->thumbnail[0]->attributes();
						if ( $content['type'] == 'application/x-shockwave-flash' )
							$og_video['og:video'] = (string) $content['url'];
						if ( ! empty( $thumb['width'] ) && ! empty( $thumb['height'] ) )
							list( $og_video['og:image'], $og_video['og:image:width'], $og_video['og:image:height'] ) = 
								array( (string) $thumb['url'], (string) $thumb['width'], (string) $thumb['height'] );
					}
				}
			} elseif ( preg_match( '/^.*(vimeo\.com)\/.*\/([^\/\?\&\#]+).*$/i', $embed_url, $match ) ) {
				$vid_name = preg_replace( '/^.*\//', '', $match[2] );
				$og_video['og:video'] = $prot . 'vimeo.com/moogaloop.swf?clip_id=' . $vid_name;
				if ( function_exists( 'simplexml_load_string' ) ) {
					$api_url = $prot . 'vimeo.com/api/oembed.xml?url=http%3A//vimeo.com/' . $vid_name;
					$this->ngfb->debug->log( 'fetching video details from ' . $api_url );
					$xml = @simplexml_load_string( $this->ngfb->cache->get( $api_url, 'raw', 'transient' ) );
					if ( ! empty( $xml->thumbnail_url ) ) {
						$this->ngfb->debug->log( 'setting og:video and og:image from vimeo api xml' );
						$og_video['og:image'] = (string) $xml->thumbnail_url;
						$og_video['og:image:width'] = (string) $xml->thumbnail_width;
						$og_video['og:image:height'] = (string) $xml->thumbnail_height;
						$og_video['og:video:width'] = $og_video['og:image:width'];
						$og_video['og:video:height'] = $og_video['og:image:height'];
					}
				}
			}
			$this->ngfb->debug->log( 'image = ' . $og_video['og:image'] . ' (' . $og_video['og:image:width'] .  'x' . $og_video['og:image:height'] . ')' );
			$this->ngfb->debug->log( 'video = ' . $og_video['og:video'] . ' (' . $og_video['og:video:width'] .  'x' . $og_video['og:video:height'] . ')' );

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

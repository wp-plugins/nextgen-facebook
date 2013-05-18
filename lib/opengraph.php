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

if ( ! class_exists( 'ngfbOpenGraph' ) ) {

	class ngfbOpenGraph {
	
		function __construct() {
		}
	
		function get_all_images( $num = 0, $size_name = 'thumbnail' ) {
			global $ngfb, $post;
			$og_ret = array();

			if ( ! empty( $post ) && is_attachment( $post->ID ) ) {
				$og_image = array();
				list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
					$og_image['og:image:cropped'] ) = $ngfb->get_attachment_image_src( $post->ID, $size_name );

				// if this is an attachment webpage, and we have an attachment, then stop here 
				// and return the image array (even if max num hasn't been reached yet)
				if ( ! empty( $og_image['og:image'] ) ) {
					$this->push_to_max( $og_ret, $og_image, $num );
					return $og_ret;
				};
			}

			// check for index-type pages with option enabled to force a default image
			if ( ( ! is_singular() && ! is_search() && ! empty( $ngfb->options['og_def_img_on_index'] ) )
				|| ( is_search() && ! empty( $ngfb->options['og_def_img_on_search'] ) ) ) {

					$ngfb->debug->push( 'calling this->get_default_image(' . $num . ', "' . $size_name . '")' );
					$og_ret = array_merge( $og_ret, $this->get_default_image( $num, $size_name ) );
					return $og_ret;	// stop here and return the image array
			}

			// check for featured or attached image(s)
			if ( ! empty( $post ) ) {
				$ngfb->debug->push( 'calling this->get_featured(' . $num . ', "' . $size_name . '", ' . $post->ID . ')' );
				$og_ret = array_merge( $og_ret, $this->get_featured( $num, $size_name, $post->ID ) );

				if ( ! $this->is_maxed( $og_ret, $num ) ) {
					$ngfb->debug->push( 'calling this->get_attached_images(' . $num . ', "' . $size_name . '", ' . $post->ID . ')' );
					$og_ret = array_merge( $og_ret, $this->get_attached_images( $num, $size_name, $post->ID ) );
				}
				// keep going to find more images - the featured / attached image(s) will be
				// listed first in the open graph meta property tags
			}

			// check for ngg shortcodes and query vars
			if ( $ngfb->is_avail['ngg'] == true && ! $this->is_maxed( $og_ret, $num ) ) {
				$ngfb->debug->push( 'calling this->get_ngg_query_images(' . $num . ', "' . $size_name . '")' );
				$ngg_og_ret = $this->get_ngg_query_images( $num, $size_name );

				if ( count( $ngg_og_ret ) > 0 ) {
					$ngfb->debug->push( count( $ngg_og_ret ) . ' image(s) returned - skipping additional shortcode images' );
					$og_ret = array_merge( $og_ret, $ngg_og_ret );

				// check for ngg shortcodes in content
				} elseif ( ! $this->is_maxed( $og_ret, $num ) ) {
					$ngfb->debug->push( 'calling this->get_ngg_shortcode_images(' . $num . ', "' . $size_name . '")' );
					$og_ret = array_merge( $og_ret, $this->get_ngg_shortcode_images( $num, $size_name ) );
				}
			}

			// if we haven't reached the limit of images yet, keep going
			if ( ! $this->is_maxed( $og_ret, $num ) ) {
				$ngfb->debug->push( 'calling this->get_content_images(' . $num . ', "' . $size_name . '")' );
				$og_ret = array_merge( $og_ret, $this->get_content_images( $num, $size_name ) );
			}

			// if we have a limit, and we're over, then slice the array
			if ( $this->is_maxed( $og_ret, $num ) ) {
				$ngfb->debug->push( 'slicing array from ' . count( $og_ret ) . ' to ' . $num . ' elements' );
				$og_ret = array_slice( $og_ret, 0, $num );
			}

			return $og_ret;
		}

		function get_ngg_query_images( $num = 0, $size_name = 'thumbnail' ) {
			global $ngfb;
			$og_ret = array();
			if ( $ngfb->is_avail['ngg'] !== true ) return $og_ret;

			global $post, $wpdb, $wp_query;
			$size_info = $ngfb->get_size_values( $size_name );

			if ( empty( $post ) ) {
				$ngfb->debug->push( 'exiting early for: empty post object' ); return $og_ret;
			} elseif ( empty( $post->post_content ) ) { 
				$ngfb->debug->push( 'exiting early for: empty post content' ); return $og_ret;
			}

			// sanitize possible query values
			$ngg_album = empty( $wp_query->query['album'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['album'] );
			$ngg_gallery = empty( $wp_query->query['gallery'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['gallery'] );
			$ngg_pageid = empty( $wp_query->query['pageid'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['pageid'] );
			$ngg_pid = empty( $wp_query->query['pid'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['pid'] );

			if ( empty( $ngg_album ) && empty( $ngg_gallery ) && empty( $ngg_pid ) ) {
				$ngfb->debug->push( 'exiting early for: no ngg query values' ); return $og_ret;
			} else {
				$ngfb->debug->push( 'ngg query found (pageid:' . $ngg_pageid . ' album:' . $ngg_album . ' gallery:' . $ngg_gallery . ' pid:' . $ngg_pid . ')' );
			}

			if ( preg_match( '/\[(nggalbum|album|nggallery)(| [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*| [^\]]*)\]/im', $post->post_content, $match ) ) {

				$ngfb->debug->push( 'ngg query with [' . $match[1] . '] shortcode' );
				if ( $ngg_pid > 0 ) {
					$ngfb->debug->push( 'getting image for ngg query pid:' . $ngg_pid );
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
						$og_image['og:image:cropped'] ) = $ngfb->get_ngg_image_src( 'ngg-' . $ngg_pid, $size_name );
					if ( $this->push_to_max( $og_ret, $og_image, $num ) ) return $og_ret;

				} elseif ( $ngg_gallery > 0 ) {
					$galleries = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->nggallery . ' WHERE gid IN (\'' . $ngg_gallery . '\')', OBJECT_K );
					if ( is_array( $galleries ) ) {
						foreach ( $galleries as $row ) {
							$ngfb->debug->push( 'getting image for ngg query gallery:' . $row->gid . ' (previewpic:' . $row->previewpic . ')' );
							if ( ! empty( $row->previewpic ) ) {
								list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
									$og_image['og:image:cropped'] ) = $ngfb->get_ngg_image_src( 'ngg-' . $row->previewpic, $size_name );
								if ( $this->push_to_max( $og_ret, $og_image, $num ) ) return $og_ret;
							}
						}
					}
				} elseif ( $ngg_album > 0 ) {
					$albums = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->nggalbum . ' WHERE id IN (\'' . $ngg_album . '\')', OBJECT_K );
					if ( is_array( $albums ) ) {
						foreach ( $albums as $row ) {
							$ngfb->debug->push( 'getting image for ngg query album:' . $row->id . ' (previewpic:' . $row->previewpic . ')' );
							if ( ! empty( $row->previewpic ) ) {
								list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
									$og_image['og:image:cropped'] ) = $ngfb->get_ngg_image_src( 'ngg-' . $row->previewpic, $size_name );
								if ( $this->push_to_max( $og_ret, $og_image, $num ) ) return $og_ret;
							}
						}
					}
				}
			} else $ngfb->debug->push( 'ngg query without [nggalbum|album|nggallery] shortcode' );

			if ( $num > 0 && count( $og_ret ) > $num ) {
				$ngfb->debug->push( 'slicing array from ' . count( $og_ret ) . ' to ' . $num . ' elements' );
				$og_ret = array_slice( $og_ret, 0, $num );
			}
			return $og_ret;
		}

		function get_ngg_shortcode_images( $num = 0, $size_name = 'thumbnail' ) {
			global $ngfb;
			$og_ret = array();
			if ( $ngfb->is_avail['ngg'] !== true ) return $og_ret;

			$size_info = $ngfb->get_size_values( $size_name );
			global $post, $wpdb;

			if ( empty( $post ) ) {
				$ngfb->debug->push( 'exiting early for: empty post object' ); return $og_ret;
			} elseif ( empty( $post->post_content ) ) { 
				$ngfb->debug->push( 'exiting early for: empty post content' ); return $og_ret;
			}

			if ( preg_match_all( '/\[(nggalbum|album)(| [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*| [^\]]*)\]/im', $post->post_content, $match, PREG_SET_ORDER ) ) {
				foreach ( $match as $album ) {
					$ngfb->debug->push( '[' . $album[1] . '] shortcode found' );
					$og_image = array();
					if ( $album[3] == '' ) {
						$ngg_album = 0;
						$ngfb->debug->push( 'album id not found - setting album id to 0 (all)' );
					} else $ngg_album = $album[3];
					if ( $ngg_album > 0 ) $albums = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->nggalbum . ' WHERE id IN (\'' . $ngg_album . '\')', OBJECT_K );
					else $albums = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->nggalbum, OBJECT_K );
					if ( is_array( $albums ) ) {
						foreach ( $albums as $row ) {
							$ngfb->debug->push( 'getting image for nggalbum:' . $row->id . ' (previewpic:' . $row->previewpic . ')' );
							if ( ! empty( $row->previewpic ) ) {
								list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
									$og_image['og:image:cropped'] ) = $ngfb->get_ngg_image_src( 'ngg-' . $row->previewpic, $size_name );
								if ( $this->push_to_max( $og_ret, $og_image, $num ) ) return $og_ret;
							}
						}
					}
				}
			} else $ngfb->debug->push( 'no [nggalbum|album] shortcode found' );

			if ( preg_match_all( '/\[(nggallery) [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*\]/im', $post->post_content, $match, PREG_SET_ORDER ) ) {
				foreach ( $match as $gallery ) {
					$ngfb->debug->push( '[' . $gallery[1] . '] shortcode found' );
					$og_image = array();
					$ngg_gallery = $gallery[2];
					$galleries = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->nggallery . ' WHERE gid IN (\'' . $ngg_gallery . '\')', OBJECT_K );
					if ( is_array( $galleries ) ) {
						foreach ( $galleries as $row ) {
							$ngfb->debug->push( 'getting image for nggallery:' . $row->gid . ' (previewpic:' . $row->previewpic . ')' );
							if ( ! empty( $row->previewpic ) ) {
								list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
									$og_image['og:image:cropped'] ) = $ngfb->get_ngg_image_src( 'ngg-' . $row->previewpic, $size_name );
								if ( $this->push_to_max( $og_ret, $og_image, $num ) ) return $og_ret;
							}
						}
					}
				}
			} else $ngfb->debug->push( 'no [nggallery] shortcode found' );

			if ( preg_match_all( '/\[(singlepic) [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*\]/im', $post->post_content, $match, PREG_SET_ORDER ) ) {
				foreach ( $match as $singlepic ) {
					$ngfb->debug->push( '[' . $singlepic[1] . '] shortcode found' );
					$og_image = array();
					$pid = $singlepic[2];
					$ngfb->debug->push( 'getting image for singlepic:' . $pid );
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
						$og_image['og:image:cropped'] ) = $ngfb->get_ngg_image_src( 'ngg-' . $pid, $size_name );
					if ( $this->push_to_max( $og_ret, $og_image, $num ) ) return $og_ret;
				}
			} else $ngfb->debug->push( 'no [singlepic] shortcode found' );

			if ( $num > 0 && count( $og_ret ) > $num ) {
				$ngfb->debug->push( 'slicing array from ' . count( $og_ret ) . ' to ' . $num . ' elements' );
				$og_ret = array_slice( $og_ret, 0, $num );
			}
			return $og_ret;
		}

		function get_content_images( $num = 0, $size_name = 'thumbnail' ) {
			global $ngfb, $post;
			$og_ret = array();
			$size_info = $ngfb->get_size_values( $size_name );
			$ngfb->debug->push( 'calling ngfb->get_filtered_content()' );
			$content = $ngfb->get_filtered_content( $ngfb->options['ngfb_filter_content'] );
			if ( empty( $content ) ) { $ngfb->debug->push( 'exiting early for: empty post content' ); return $og_ret; }

			// check for ngg image ids
			if ( preg_match_all( '/<div[^>]*? id=[\'"]ngg-image-([0-9]+)[\'"][^>]*>/is', $content, $match, PREG_SET_ORDER ) ) {
				$ngfb->debug->push( count( $match ) . ' x <div id="ngg-image-#"> html tag(s) found' );
				foreach ( $match as $pid ) {
					$og_image = array();
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
						$og_image['og:image:cropped'] ) = $ngfb->get_ngg_image_src( 'ngg-' . $pid[1], $size_name );
					if ( $this->push_to_max( $og_ret, $og_image, $num ) ) return $og_ret;
				}
			} else $ngfb->debug->push( 'no <div id="ngg-image-#"> html tag found' );

			// img attributes in order of preference
			if ( preg_match_all( '/<img[^>]*? (share-'.$size_name.'|share|src)=[\'"]([^\'"]+)[\'"][^>]*>/is', $content, $match, PREG_SET_ORDER ) ) {
				$ngfb->debug->push( count( $match ) . ' x <img/> html tag(s) found' );
				foreach ( $match as $img ) {
					$src_name = $img[1];
					$og_image = array(
						'og:image' => $ngfb->get_sharing_url( 'asis', $img[2] ),
						'og:image:width' => '',
						'og:image:height' => '',
						'og:image:cropped' => '',
					);

					// check for NGG image pids
					if ( preg_match( '/\/cache\/([0-9]+)_(crop)?_[0-9]+x[0-9]+_[^\/]+$/', $og_image['og:image'], $match) ) {
						$ngfb->debug->push( $src_name . ' ngg cache image = ' . $og_image['og:image'] );
						list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
							$og_image['og:image:cropped'] ) = $ngfb->get_ngg_image_src( 'ngg-' . $match[1], $size_name );

					} elseif ( $ngfb->url_is_good( $og_image['og:image'] ) ) {
						if ( preg_match( '/ width=[\'"]?([0-9]+)[\'"]?/i', $img[0], $match) ) $og_image['og:image:width'] = $match[1];
						if ( preg_match( '/ height=[\'"]?([0-9]+)[\'"]?/i', $img[0], $match) ) $og_image['og:image:height'] = $match[1];

					} else continue;	// skip anything that is "not good" (duplicate or empty)

					$ngfb->debug->push( $src_name . ' = ' . $og_image['og:image'] . 
						' (' . $og_image['og:image:width'] . ' x ' . $og_image['og:image:height'] . ')' );

					// set value to 0 if not valid, to avoid error when comparing image sizes
					if ( ! is_numeric( $og_image['og:image:width'] ) ) $og_image['og:image:width'] = 0;
					if ( ! is_numeric( $og_image['og:image:height'] ) ) $og_image['og:image:height'] = 0;

					// if we're picking up an img from 'src', make sure it's width and height is large enough
					if ( $src_name == 'share-' . $size_name || $src_name == 'share' 
						|| ( $src_name == 'src' && defined( 'NGFB_MIN_IMG_SIZE_DISABLE' ) && NGFB_MIN_IMG_SIZE_DISABLE ) 
						|| ( $src_name == 'src' && $ngfb->options['ngfb_skip_small_img'] && 
							$og_image['og:image:width'] >= $size_info['width'] && 
							$og_image['og:image:height'] >= $size_info['height'] ) ) {

						if ( $this->push_to_max( $og_ret, $og_image, $num ) ) return $og_ret;

					} else $ngfb->debug->push( $src_name . ' image rejected: width and height attributes missing or too small' );
				}
			} else $ngfb->debug->push( 'no <img/> html tag(s) found' );

			return $og_ret;
		}

		function get_content_videos( $num = 0 ) {
			global $ngfb, $post;
			$og_ret = array();
			$ngfb->debug->push( 'calling ngfb->get_filtered_content()' );
			$content = $ngfb->get_filtered_content( $ngfb->options['ngfb_filter_content'] );
			if ( empty( $content ) ) { $ngfb->debug->push( 'exiting early for: empty post content' ); return $og_ret; }

			if ( preg_match_all( '/<(iframe|embed)[^>]*? src=[\'"]([^\'"]+\/(embed|video)\/[^\'"]+)[\'"][^>]*>/i', $content, $match_all, PREG_SET_ORDER ) ) {
				$ngfb->debug->push( count( $match_all ) . ' x video html tag(s) found' );
				foreach ( $match_all as $media ) {
					$ngfb->debug->push( '<' . $media[1] . '/> html tag found = ' . $media[2] );
					$og_video = array(
						'og:image' => '',
						'og:video' => $ngfb->get_sharing_url( 'noquery', $media[2] ),
						'og:video:width' => '',
						'og:video:height' => '',
						'og:video:type' => 'application/x-shockwave-flash'
					);
					if ( $ngfb->url_is_good( $og_video['og:video'] ) ) {

						// set the height and width based on the iframe/embed attributes
						if ( preg_match( '/ width=[\'"]?([0-9]+)[\'"]?/i', $media[0], $match) ) $og_video['og:video:width'] = $match[1];
						if ( preg_match( '/ height=[\'"]?([0-9]+)[\'"]?/i', $media[0], $match) ) $og_video['og:video:height'] = $match[1];

						// fix URLs and define video images for known websites (youtube, vimeo, etc.)
						if ( preg_match( '/^.*(youtube|youtube-nocookie)\.com\/.*\/([^\/\?\&]+)$/i', $og_video['og:video'], $match ) ) {

							$og_video['og:video'] = 'http://www.youtube.com/v/'.$match[2];
							$og_video['og:image'] = 'http://img.youtube.com/vi/'.$match[2].'/0.jpg';

						} elseif ( preg_match( '/^.*(vimeo)\.com\/.*\/([^\/\?\&]+)$/i', $og_video['og:video'], $match ) ) {

							$api_url = "http://vimeo.com/api/v2/video/$match[2].php";
							$ngfb->debug->push( 'fetching video details from ' . $api_url );
							$hash = unserialize( $ngfb->cache->get( $api_url, 'raw', 'transient' ) );

							if ( ! empty( $hash ) ) {
								$ngfb->debug->push( 'setting og:video and og:image from Vimeo API hash' );
								$og_video['og:video'] = $hash[0]['url'];
								$og_video['og:image'] = $hash[0]['thumbnail_large'];
							}
						}
						$ngfb->debug->push( 'image = ' . $og_video['og:image'] );
						$ngfb->debug->push( 'video = ' . $og_video['og:video'] . 
							' (' . $og_video['og:video:width'] .  ' x ' . $og_video['og:video:height'] . ')' );

						if ( $this->push_to_max( $og_ret, $og_video, $num ) ) return $og_ret;
					}
				}
			} else $ngfb->debug->push( 'no <iframe|embed/> html tag(s) found' );

			return $og_ret;
		}

		function get_ngg_images( $ngg_images = array(), $size_name = 'thumbnail' ) {
			$og_ret = array();
			foreach ( $ngg_images as $image ) {
				$og_image = array();
				list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
					$og_image['og:image:cropped'] ) = $ngfb->get_ngg_image_src( 'ngg-' . $image->pid, $size_name );
				if ( $og_image['og:image'] ) array_push( $og_ret, $og_image );
			}
			return $og_ret;
		}

		function get_attached_images( $num = 0, $size_name = 'thumbnail', $post_id = '' ) {
			global $ngfb;
			$og_ret = array();
			$og_image = array();
			if ( ! empty( $post_id ) ) {
				$images = get_children( array( 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image') );
				if ( is_array( $images ) )
					foreach ( $images as $attachment )
						if ( ! empty( $attachment->ID ) )
							list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
								$og_image['og:image:cropped'] ) = $ngfb->get_attachment_image_src( $attachment->ID, $size_name );
			}
			// returned array must be two-dimensional
			$this->push_to_max( $og_ret, $og_image, $num );
			return $og_ret;
		}

		function get_featured( $num = 0, $size_name = 'thumbnail', $post_id ) {
			global $ngfb;
			$og_ret = array();
			$og_image = array();
			if ( ! empty( $post_id ) && $ngfb->is_avail['postthumb'] == true && has_post_thumbnail( $post_id ) ) {
				$pid = get_post_thumbnail_id( $post_id );
				if ( is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' ) {
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
						$og_image['og:image:cropped'] ) = $ngfb->get_ngg_image_src( $pid, $size_name );
				} else {
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
						$og_image['og:image:cropped'] ) = $ngfb->get_attachment_image_src( $pid, $size_name );
				}
			}
			// returned array must be two-dimensional
			$this->push_to_max( $og_ret, $og_image, $num );
			return $og_ret;
		}

		function get_default_image( $num = 0, $size_name = 'thumbnail' ) {
			global $ngfb;
			$og_ret = array();
			$og_image = array();
			if ( $ngfb->options['og_def_img_id'] > 0 ) {
				if ( $ngfb->options['og_def_img_id_pre'] == 'ngg' ) {
					$pid = $ngfb->options['og_def_img_id_pre'] . '-' . $ngfb->options['og_def_img_id'];
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
						$og_image['og:image:cropped'] ) = $ngfb->get_ngg_image_src( $pid, $size_name );
				} else {
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
						$og_image['og:image:cropped'] ) = $ngfb->get_attachment_image_src( $ngfb->options['og_def_img_id'], $size_name );
				}
			}
			// if still empty, use the default url (if one is defined, empty string otherwise)
			if ( empty( $og_image['og:image'] ) ) {
				$og_image['og:image'] = empty( $ngfb->options['og_def_img_url'] ) ? '' : $ngfb->options['og_def_img_url'];
				$ngfb->debug->push( 'using default img url = ' . $og_image['og:image'] );
			}
			// returned array must be two-dimensional
			$this->push_to_max( $og_ret, $og_image, $num );
			return $og_ret;
		}

		private function push_to_max( &$dst, &$src, $num = 0 ) {
			global $ngfb;

			if ( ! is_array( $dst ) || ! is_array( $src ) ) 
				return false;

			if ( ! empty( $src ) ) 
				array_push( $dst, $src );

			if ( $this->is_maxed( $dst, $num ) ) {
				$ngfb->debug->push( 'max values reached (' . count( $dst ) . ' >= ' . $num . ') - slicing array' );
				$dst = array_slice( $dst, 0, $num );
				return true;
			}
			return false;
		}

		private function is_maxed( &$arr, $num = 0 ) {
			if ( is_array( $arr ) && $num > 0 && count( $arr ) >= $num ) return true;
			return false;
		}

	}
}

?>

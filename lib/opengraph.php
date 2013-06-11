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

		private $ngfb;

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();

			add_filter( 'language_attributes', array( &$this, 'add_doctype' ) );
		}
	
		public function add_doctype( $out ) {
			return $out . ' xmlns:og="http://ogp.me/ns" xmlns:fb="http://ogp.me/ns/fb"';
		}

		public function get() {
			$og = array();

			if ( ( defined( 'DISABLE_NGFB_OPEN_GRAPH' ) && DISABLE_NGFB_OPEN_GRAPH ) 
				|| ( defined( 'NGFB_OPEN_GRAPH_DISABLE' ) && NGFB_OPEN_GRAPH_DISABLE ) ) {
				echo "\n<!-- ", $this->ngfb->fullname, " meta tags DISABLED -->\n\n";
				return $og;
			}

			$sharing_url = $this->ngfb->util->get_sharing_url( 'notrack' );
			$cache_salt = __METHOD__ . '(sharing_url:' . $sharing_url . ')';
			$cache_id = $this->ngfb->acronym . '_' . md5( $cache_salt );
			$cache_type = 'object cache';
			$og = get_transient( $cache_id );
			$this->ngfb->debug->log( $cache_type . ': og array transient id salt "' . $cache_salt . '"' );

			if ( $og !== false ) {
				$this->ngfb->debug->log( $cache_type . ': og array retrieved from transient for id "' . $cache_id . '"' );
				return $og;
			}

			global $post;
			$has_video_image = '';
			$og['og:url'] = $sharing_url;
			$og['fb:admins'] = $this->ngfb->options['og_admins'];
			$og['fb:app_id'] = $this->ngfb->options['og_app_id'];
			$og['og:site_name'] = get_bloginfo( 'name', 'display' );	
			$og['og:title'] = $this->ngfb->webpage->get_title( $this->ngfb->options['og_title_len'], '...' );
			$og['og:description'] = $this->ngfb->webpage->get_description( $this->ngfb->options['og_desc_len'], '...' );

			$og_max = array();
			foreach ( array( 'og_vid_max', 'og_img_max' ) as $max_name ) {
				$num_meta = false;
				if ( ! empty( $post ) )
					$num_meta = $this->ngfb->meta->get_options( $post->ID, $max_name );
				if ( $num_meta !== false ) {
					$og_max[$max_name] = $num_meta;
					$this->ngfb->debug->log( 'found custom meta ' . $max_name . ' = ' . $num_meta );
				} else $og_max[$max_name] = $this->ngfb->options[$max_name];
			}
			unset ( $max_name );

			if ( $og_max['og_vid_max'] > 0 ) {
				$this->ngfb->debug->log( 'calling this->get_content_videos(' . $og_max['og_vid_max'] . ')' );
				$og['og:video'] = $this->get_content_videos( $og_max['og_vid_max'] );
				if ( is_array( $og['og:video'] ) ) {
					foreach ( $og['og:video'] as $val ) {
						if ( is_array( $val ) && ! empty( $val['og:image'] ) ) {
							$this->ngfb->debug->log( 'og:image found in og:video array (no default image required)' );
							$has_video_image = 1;
						}
					}
					unset ( $vid );
				}
			} else $this->ngfb->debug->log( 'videos disabled: maximum videos = 0' );

			if ( $og_max['og_img_max'] > 0 ) {
				$this->ngfb->debug->log( 'calling this->get_all_images(' . $og_max['og_img_max'] . ', "' . $this->ngfb->options['og_img_size'] . '")' );
				$og['og:image'] = $this->get_all_images( $og_max['og_img_max'], $this->ngfb->options['og_img_size'] );

				// if we didn't find any images, then use the default image
				if ( empty( $og['og:image'] ) && empty( $has_video_image ) ) {
					$this->ngfb->debug->log( 'calling this->get_default_image(' . $og_max['og_img_max'] . ', "' . $this->ngfb->options['og_img_size'] . '")' );
					$og['og:image'] = $this->get_default_image( $og_max['og_img_max'], $this->ngfb->options['og_img_size'] );
				}
			} else $this->ngfb->debug->log( 'images disabled: maximum videos = 0' );

			// any singular page is type 'article'
			if ( is_singular() ) {
				$og['og:type'] = 'article';

				if ( ! empty( $post ) && $post->post_author )
					$og['article:author'] = $this->ngfb->user->get_author_url( $post->post_author, 
						$this->ngfb->options['og_author_field'] );

				elseif ( ! empty( $this->ngfb->options['og_def_author_id'] ) )
					$og['article:author'] = $this->ngfb->user->get_author_url( $this->ngfb->options['og_def_author_id'], 
						$this->ngfb->options['og_author_field'] );

			// check for default author info on indexes and searches
			} elseif ( ( ! is_singular() && ! is_search() && ! empty( $this->ngfb->options['og_def_author_on_index'] ) && ! empty( $this->ngfb->options['og_def_author_id'] ) )
				|| ( is_search() && ! empty( $this->ngfb->options['og_def_author_on_search'] ) && ! empty( $this->ngfb->options['og_def_author_id'] ) ) ) {

				$og['og:type'] = "article";
				$og['article:author'] = $this->ngfb->user->get_author_url( $this->ngfb->options['og_def_author_id'], 
					$this->ngfb->options['og_author_field'] );

			// default
			} else $og['og:type'] = 'website';

			// if the page is an article, then define the other article meta tags
			if ( $og['og:type'] == 'article' ) {
				$og['article:tag'] = $this->ngfb->tags->get();
				$og['article:section'] = $this->ngfb->webpage->get_section();
				$og['article:modified_time'] = get_the_modified_date('c');
				$og['article:published_time'] = get_the_date('c');
			}
		
			if ( $this->ngfb->is_avail['aop'] ) $og = apply_filters( 'ngfb_og', $og );
			set_transient( $cache_id, $og, $this->ngfb->cache->object_expire );
			$this->ngfb->debug->log( $cache_type . ': og array saved to transient for id "' . $cache_id . '" (' . $this->ngfb->cache->object_expire . ' seconds)');
			return $og;
		}

		private function get_all_images( $num = 0, $size_name = 'thumbnail' ) {
			global $post;
			$og_ret = array();

			// check for attachment page
			if ( ! empty( $post ) && is_attachment( $post->ID ) ) {
				$og_image = array();
				$num_remains = $this->num_remains( $og_ret, $num );
				$this->ngfb->debug->log( 'calling this->get_attachment_image(' . $num_remains . ', "' . $size_name . '", ' . $post->ID . ')' );
				$og_image = $this->get_attachment_image( $num_remains, $size_name, $post->ID );

				// if an attachment is not an image, then use the default image instead
				if ( empty( $og_ret ) ) {
					$num_remains = $this->num_remains( $og_ret, $num );
					$this->ngfb->debug->log( 'calling this->get_default_image(' . $num_remains . ', "' . $size_name . '")' );
					$og_ret = array_merge( $og_ret, $this->get_default_image( $num_remains, $size_name ) );
				} else $og_ret = array_merge( $og_ret, $og_image );

				return $og_ret;
			}

			// check for attachment page without an image, or index-type pages with og_def_img_on_index enabled to force a default image
			if ( ( ! is_singular() && ! is_search() && ! empty( $this->ngfb->options['og_def_img_on_index'] ) ) || 
				( is_search() && ! empty( $this->ngfb->options['og_def_img_on_search'] ) ) ) {

				$num_remains = $this->num_remains( $og_ret, $num );
				$this->ngfb->debug->log( 'calling this->get_default_image(' . $num_remains . ', "' . $size_name . '")' );
				$og_ret = array_merge( $og_ret, $this->get_default_image( $num_remains, $size_name ) );
				return $og_ret;	// stop here and return the image array
			}

			// check for custom meta, featured, or attached image(s)
			if ( ! empty( $post ) ) {

				$num_remains = $this->num_remains( $og_ret, $num );
				$this->ngfb->debug->log( 'calling this->get_meta_images(' . $num_remains . ', "' . $size_name . '", ' . $post->ID . ')' );
				$og_ret = array_merge( $og_ret, $this->get_meta_images( $num_remains, $size_name, $post->ID ) );

				$num_remains = $this->num_remains( $og_ret, $num );
				$this->ngfb->debug->log( 'calling this->get_featured(' . $num_remains . ', "' . $size_name . '", ' . $post->ID . ')' );
				$og_ret = array_merge( $og_ret, $this->get_featured( $num_remains, $size_name, $post->ID ) );

				if ( ! $this->is_maxed( $og_ret, $num ) ) {
					$num_remains = $this->num_remains( $og_ret, $num );
					$this->ngfb->debug->log( 'calling this->get_attached_images(' . $num_remains . ', "' . $size_name . '", ' . $post->ID . ')' );
					$og_ret = array_merge( $og_ret, $this->get_attached_images( $num_remains, $size_name, $post->ID ) );
				}
				// keep going to find more images
				// the featured / attached image(s) will be listed first in the open graph meta property tags
				// and duplicates will be filtered out
			}

			// check for ngg shortcodes and query vars
			if ( $this->ngfb->is_avail['ngg'] == true && ! $this->is_maxed( $og_ret, $num ) ) {
				$num_remains = $this->num_remains( $og_ret, $num );
				$this->ngfb->debug->log( 'calling this->get_ngg_query_images(' . $num_remains . ', "' . $size_name . '")' );
				$ngg_og_ret = $this->get_ngg_query_images( $num_remains, $size_name );

				if ( count( $ngg_og_ret ) > 0 ) {
					$this->ngfb->debug->log( count( $ngg_og_ret ) . ' image(s) returned - skipping additional shortcode images' );
					$og_ret = array_merge( $og_ret, $ngg_og_ret );

				// check for ngg shortcodes in content
				} elseif ( ! $this->is_maxed( $og_ret, $num ) ) {
					$num_remains = $this->num_remains( $og_ret, $num );
					$this->ngfb->debug->log( 'calling this->get_ngg_shortcode_images(' . $num_remains . ', "' . $size_name . '")' );
					$og_ret = array_merge( $og_ret, $this->get_ngg_shortcode_images( $num_remains, $size_name ) );
				}
			}

			// if we haven't reached the limit of images yet, keep going
			if ( ! $this->is_maxed( $og_ret, $num ) ) {
				$num_remains = $this->num_remains( $og_ret, $num );
				$this->ngfb->debug->log( 'calling this->get_content_images(' . $num_remains . ', "' . $size_name . '")' );
				$og_ret = array_merge( $og_ret, $this->get_content_images( $num_remains, $size_name ) );
			}

			$this->slice_max( $og_ret, $num );
			return $og_ret;
		}

		private function get_ngg_query_images( $num = 0, $size_name = 'thumbnail' ) {
			$og_ret = array();
			if ( $this->ngfb->is_avail['ngg'] !== true ) return $og_ret;

			global $post, $wpdb, $wp_query;
			$size_info = $this->ngfb->media->get_size_info( $size_name );

			if ( empty( $post ) ) {
				$this->ngfb->debug->log( 'exiting early for: empty post object' ); return $og_ret;
			} elseif ( empty( $post->post_content ) ) { 
				$this->ngfb->debug->log( 'exiting early for: empty post content' ); return $og_ret;
			}

			// sanitize possible query values
			$ngg_album = empty( $wp_query->query['album'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['album'] );
			$ngg_gallery = empty( $wp_query->query['gallery'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['gallery'] );
			$ngg_pageid = empty( $wp_query->query['pageid'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['pageid'] );
			$ngg_pid = empty( $wp_query->query['pid'] ) ? '' : preg_replace( '/[^0-9]/', '', $wp_query->query['pid'] );

			if ( empty( $ngg_album ) && empty( $ngg_gallery ) && empty( $ngg_pid ) ) {
				$this->ngfb->debug->log( 'exiting early for: no ngg query values' ); return $og_ret;
			} else {
				$this->ngfb->debug->log( 'ngg query found (pageid:' . $ngg_pageid . ' album:' . $ngg_album . 
					' gallery:' . $ngg_gallery . ' pid:' . $ngg_pid . ')' );
			}

			if ( preg_match( '/\[(nggalbum|album|nggallery)(| [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*| [^\]]*)\]/im', $post->post_content, $match ) ) {

				$this->ngfb->debug->log( 'ngg query with [' . $match[1] . '] shortcode' );
				if ( $ngg_pid > 0 ) {
					$this->ngfb->debug->log( 'getting image for ngg query pid:' . $ngg_pid );
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
						$og_image['og:image:cropped'] ) = $this->ngfb->media->get_ngg_image_src( 'ngg-' . $ngg_pid, $size_name );
					if ( $this->push_max( $og_ret, $og_image, $num ) ) return $og_ret;

				} elseif ( $ngg_gallery > 0 ) {
					$galleries = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->nggallery . ' WHERE gid IN (\'' . $ngg_gallery . '\')', OBJECT_K );
					if ( is_array( $galleries ) ) {
						foreach ( $galleries as $row ) {
							$this->ngfb->debug->log( 'getting image for ngg query gallery:' . $row->gid . ' (previewpic:' . $row->previewpic . ')' );
							if ( ! empty( $row->previewpic ) ) {
								list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
									$og_image['og:image:cropped'] ) = $this->ngfb->media->get_ngg_image_src( 'ngg-' . $row->previewpic, $size_name );
								if ( $this->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
							}
						}
					}
				} elseif ( $ngg_album > 0 ) {
					$albums = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->nggalbum . ' WHERE id IN (\'' . $ngg_album . '\')', OBJECT_K );
					if ( is_array( $albums ) ) {
						foreach ( $albums as $row ) {
							$this->ngfb->debug->log( 'getting image for ngg query album:' . $row->id . ' (previewpic:' . $row->previewpic . ')' );
							if ( ! empty( $row->previewpic ) ) {
								list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
									$og_image['og:image:cropped'] ) = $this->ngfb->media->get_ngg_image_src( 'ngg-' . $row->previewpic, $size_name );
								if ( $this->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
							}
						}
					}
				}
			} else $this->ngfb->debug->log( 'ngg query without [nggalbum|album|nggallery] shortcode' );

			$this->slice_max( $og_ret, $num );
			return $og_ret;
		}

		private function get_ngg_shortcode_images( $num = 0, $size_name = 'thumbnail' ) {
			$og_ret = array();
			if ( $this->ngfb->is_avail['ngg'] !== true ) return $og_ret;

			$size_info = $this->ngfb->media->get_size_info( $size_name );
			global $post, $wpdb;

			if ( empty( $post ) ) {
				$this->ngfb->debug->log( 'exiting early for: empty post object' ); return $og_ret;
			} elseif ( empty( $post->post_content ) ) { 
				$this->ngfb->debug->log( 'exiting early for: empty post content' ); return $og_ret;
			}

			if ( preg_match_all( '/\[(nggalbum|album)(| [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*| [^\]]*)\]/im', $post->post_content, $match, PREG_SET_ORDER ) ) {
				foreach ( $match as $album ) {
					$this->ngfb->debug->log( '[' . $album[1] . '] shortcode found' );
					$og_image = array();
					if ( empty( $album[3] ) ) {
						$ngg_album = 0;
						$this->ngfb->debug->log( 'album id zero or not found - setting album id to 0 (all)' );
					} else $ngg_album = $album[3];
					if ( $ngg_album > 0 ) $albums = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->nggalbum . ' WHERE id IN (\'' . $ngg_album . '\')', OBJECT_K );
					else $albums = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->nggalbum, OBJECT_K );
					if ( is_array( $albums ) ) {
						foreach ( $albums as $row ) {
							$this->ngfb->debug->log( 'getting image for nggalbum:' . $row->id . ' (previewpic:' . $row->previewpic . ')' );
							if ( ! empty( $row->previewpic ) ) {
								list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
									$og_image['og:image:cropped'] ) = $this->ngfb->media->get_ngg_image_src( 'ngg-' . $row->previewpic, $size_name );
								if ( $this->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
							}
						}
					}
				}
			} else $this->ngfb->debug->log( 'no [nggalbum|album] shortcode found' );

			if ( preg_match_all( '/\[(nggallery) [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*\]/im', $post->post_content, $match, PREG_SET_ORDER ) ) {
				foreach ( $match as $gallery ) {
					$this->ngfb->debug->log( '[' . $gallery[1] . '] shortcode found' );
					$og_image = array();
					$ngg_gallery = $gallery[2];
					$galleries = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->nggallery . ' WHERE gid IN (\'' . $ngg_gallery . '\')', OBJECT_K );
					if ( is_array( $galleries ) ) {
						foreach ( $galleries as $row ) {
							$this->ngfb->debug->log( 'getting image for nggallery:' . $row->gid . ' (previewpic:' . $row->previewpic . ')' );
							if ( ! empty( $row->previewpic ) ) {
								list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
									$og_image['og:image:cropped'] ) = $this->ngfb->media->get_ngg_image_src( 'ngg-' . $row->previewpic, $size_name );
								if ( $this->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
							}
						}
					}
				}
			} else $this->ngfb->debug->log( 'no [nggallery] shortcode found' );

			if ( preg_match_all( '/\[(singlepic) [^\]]*id=[\'"]*([0-9]+)[\'"]*[^\]]*\]/im', $post->post_content, $match, PREG_SET_ORDER ) ) {
				foreach ( $match as $singlepic ) {
					$this->ngfb->debug->log( '[' . $singlepic[1] . '] shortcode found' );
					$og_image = array();
					$pid = $singlepic[2];
					$this->ngfb->debug->log( 'getting image for singlepic:' . $pid );
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
						$og_image['og:image:cropped'] ) = $this->ngfb->media->get_ngg_image_src( 'ngg-' . $pid, $size_name );
					if ( $this->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
				}
			} else $this->ngfb->debug->log( 'no [singlepic] shortcode found' );

			$this->slice_max( $og_ret, $num );
			return $og_ret;
		}

		private function get_content_images( $num = 0, $size_name = 'thumbnail' ) {
			global $post;
			$og_ret = array();
			$size_info = $this->ngfb->media->get_size_info( $size_name );
			$this->ngfb->debug->log( 'calling this->ngfb->webpage->get_content()' );
			$content = $this->ngfb->webpage->get_content( $this->ngfb->options['ngfb_filter_content'] );
			if ( empty( $content ) ) { $this->ngfb->debug->log( 'exiting early for: empty post content' ); return $og_ret; }

			// check for ngg image ids
			if ( preg_match_all( '/<div[^>]*? id=[\'"]ngg-image-([0-9]+)[\'"][^>]*>/is', $content, $match, PREG_SET_ORDER ) ) {
				$this->ngfb->debug->log( count( $match ) . ' x <div id="ngg-image-#"> html tag(s) found' );
				foreach ( $match as $pid ) {
					$og_image = array();
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
						$og_image['og:image:cropped'] ) = $this->ngfb->media->get_ngg_image_src( 'ngg-' . $pid[1], $size_name );
					if ( $this->push_max( $og_ret, $og_image, $num ) ) return $og_ret;
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
							$og_image['og:image:cropped'] ) = $this->ngfb->media->get_ngg_image_src( 'ngg-' . $match[1], $size_name );

					} elseif ( $this->ngfb->util->is_uniq_url( $og_image['og:image'] ) ) {
						if ( preg_match( '/ width=[\'"]?([0-9]+)[\'"]?/i', $img[0], $match) ) $og_image['og:image:width'] = $match[1];
						if ( preg_match( '/ height=[\'"]?([0-9]+)[\'"]?/i', $img[0], $match) ) $og_image['og:image:height'] = $match[1];

					} else continue;	// skip anything that is "not good" (duplicate or empty)

					$this->ngfb->debug->log( $src_name . ' = ' . $og_image['og:image'] . 
						' (' . $og_image['og:image:width'] . ' x ' . $og_image['og:image:height'] . ')' );

					// set value to 0 if not valid, to avoid error when comparing image sizes
					if ( ! is_numeric( $og_image['og:image:width'] ) ) $og_image['og:image:width'] = 0;
					if ( ! is_numeric( $og_image['og:image:height'] ) ) $og_image['og:image:height'] = 0;

					// if we're picking up an img from 'src', make sure it's width and height is large enough
					if ( $src_name == 'share-' . $size_name || $src_name == 'share' 
						|| ( $src_name == 'src' && defined( 'NGFB_MIN_IMG_SIZE_DISABLE' ) && NGFB_MIN_IMG_SIZE_DISABLE ) 
						|| ( $src_name == 'src' && $this->ngfb->options['ngfb_skip_small_img'] && 
							$og_image['og:image:width'] >= $size_info['width'] && 
							$og_image['og:image:height'] >= $size_info['height'] ) ) {

						if ( $this->push_max( $og_ret, $og_image, $num ) ) return $og_ret;

					} else $this->ngfb->debug->log( $src_name . ' image rejected: width and height attributes missing or too small' );
				}
			} else $this->ngfb->debug->log( 'no <img/> html tag(s) found' );

			return $og_ret;
		}

		// called from the Tumblr class
		public function get_content_videos( $num = 0 ) {
			global $post;
			$og_ret = array();
			$this->ngfb->debug->log( 'calling this->ngfb->webpage->get_content()' );
			$content = $this->ngfb->webpage->get_content( $this->ngfb->options['ngfb_filter_content'] );
			if ( empty( $content ) ) { $this->ngfb->debug->log( 'exiting early for: empty post content' ); return $og_ret; }

			if ( preg_match_all( '/<(iframe|embed)[^>]*? src=[\'"]([^\'"]+\/(embed|video)\/[^\'"]+)[\'"][^>]*>/i', $content, $match_all, PREG_SET_ORDER ) ) {
				$this->ngfb->debug->log( count( $match_all ) . ' x video html tag(s) found' );
				foreach ( $match_all as $media ) {
					$this->ngfb->debug->log( '<' . $media[1] . '/> html tag found = ' . $media[2] );
					$og_video = array(
						'og:image' => '',
						'og:video' => $this->ngfb->util->get_sharing_url( 'noquery', $media[2] ),
						'og:video:width' => '',
						'og:video:height' => '',
						'og:video:type' => 'application/x-shockwave-flash'
					);
					if ( $this->ngfb->util->is_uniq_url( $og_video['og:video'] ) ) {

						// set the height and width based on the iframe/embed attributes
						if ( preg_match( '/ width=[\'"]?([0-9]+)[\'"]?/i', $media[0], $match) ) $og_video['og:video:width'] = $match[1];
						if ( preg_match( '/ height=[\'"]?([0-9]+)[\'"]?/i', $media[0], $match) ) $og_video['og:video:height'] = $match[1];

						// fix URLs and define video images for known websites (youtube, vimeo, etc.)
						if ( preg_match( '/^.*(youtube|youtube-nocookie)\.com\/.*\/([^\/\?\&]+)$/i', $og_video['og:video'], $match ) ) {

							$og_video['og:video'] = 'http://www.youtube.com/v/'.$match[2];
							$og_video['og:image'] = 'http://img.youtube.com/vi/'.$match[2].'/0.jpg';

						} elseif ( preg_match( '/^.*(vimeo)\.com\/.*\/([^\/\?\&]+)$/i', $og_video['og:video'], $match ) ) {

							$api_url = "http://vimeo.com/api/v2/video/$match[2].php";
							$this->ngfb->debug->log( 'fetching video details from ' . $api_url );
							$hash = unserialize( $this->ngfb->cache->get( $api_url, 'raw', 'transient' ) );

							if ( ! empty( $hash ) ) {
								$this->ngfb->debug->log( 'setting og:video and og:image from Vimeo API hash' );
								$og_video['og:video'] = $hash[0]['url'];
								$og_video['og:image'] = $hash[0]['thumbnail_large'];
							}
						}
						$this->ngfb->debug->log( 'image = ' . $og_video['og:image'] );
						$this->ngfb->debug->log( 'video = ' . $og_video['og:video'] . 
							' (' . $og_video['og:video:width'] .  ' x ' . $og_video['og:video:height'] . ')' );

						if ( $this->push_max( $og_ret, $og_video, $num ) ) return $og_ret;
					}
				}
			} else $this->ngfb->debug->log( 'no <iframe|embed/> html tag(s) found' );

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
							$og_image['og:image:cropped'] ) = $this->ngfb->media->get_ngg_image_src( 'ngg-' . $image->pid, $size_name );
						$this->push_max( $og_ret, $og_image, $num );
					}
				}
			}
			return $og_ret;
		}

		private function get_attachment_image( $num = 0, $size_name = 'thumbnail', $attach_id = '' ) {
			$og_ret = array();
			if ( ! empty( $attach_id ) ) {
				if ( wp_attachment_is_image( $attach_id ) ) {
					$og_image = array();
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
						$og_image['og:image:cropped'] ) = $this->ngfb->media->get_attachment_image_src( $attach_id, $size_name );
					$this->push_max( $og_ret, $og_image, $num );
				} else $this->ngfb->debug->log( 'attachment id ' . $attach_id . ' is not an image' );
			}
			return $og_ret;
		}

		private function get_attached_images( $num = 0, $size_name = 'thumbnail', $post_id = '' ) {
			$og_ret = array();
			if ( ! empty( $post_id ) ) {
				$images = get_children( array( 'post_parent' => $post_id, 'post_type' => 'attachment', 'post_mime_type' => 'image') );
				if ( is_array( $images ) )
					foreach ( $images as $attachment ) {
						if ( ! empty( $attachment->ID ) ) {
							$og_image = array();
							list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
								$og_image['og:image:cropped'] ) = $this->ngfb->media->get_attachment_image_src( $attachment->ID, $size_name );
							$this->push_max( $og_ret, $og_image, $num );
						}
					}
			}
			return $og_ret;
		}

		private function get_featured( $num = 0, $size_name = 'thumbnail', $post_id ) {
			$og_ret = array();
			$og_image = array();
			if ( ! empty( $post_id ) && $this->ngfb->is_avail['postthumb'] == true && has_post_thumbnail( $post_id ) ) {
				$pid = get_post_thumbnail_id( $post_id );
				if ( is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' ) {
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
						$og_image['og:image:cropped'] ) = $this->ngfb->media->get_ngg_image_src( $pid, $size_name );
				} else {
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
						$og_image['og:image:cropped'] ) = $this->ngfb->media->get_attachment_image_src( $pid, $size_name );
				}
			}
			// returned array must be two-dimensional
			$this->push_max( $og_ret, $og_image, $num );
			return $og_ret;
		}

		private function get_default_image( $num = 0, $size_name = 'thumbnail' ) {
			$og_ret = array();
			$og_image = array();
			$pid = empty( $this->ngfb->options['og_def_img_id'] ) ? '' : $this->ngfb->options['og_def_img_id'];
			$pre = empty( $this->ngfb->options['og_def_img_id_pre'] ) ? '' : $this->ngfb->options['og_def_img_id_pre'];
			$url = empty( $this->ngfb->options['og_def_img_url'] ) ? '' : $this->ngfb->options['og_def_img_url'];
			if ( $pid > 0 ) {
				if ( $pre == 'ngg' )
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
						$og_image['og:image:cropped'] ) = $this->ngfb->media->get_ngg_image_src( $pre . '-' . $pid, $size_name );
				else
					list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
						$og_image['og:image:cropped'] ) = $this->ngfb->media->get_attachment_image_src( $pid, $size_name );
			}
			if ( empty( $og_image['og:image'] ) && ! empty( $url ) ) {
				$og_image = array();	// clear all array values
				$og_image['og:image'] = $url;
				$this->ngfb->debug->log( 'using default img url = ' . $og_image['og:image'] );
			}
			// returned array must be two-dimensional
			$this->push_max( $og_ret, $og_image, $num );
			return $og_ret;
		}

		private function get_meta_images( $num = 0, $size_name = 'thumbnail', $post_id = '' ) {
			$og_ret = array();
			$og_image = array();
			if ( ! empty( $post_id ) ) {
				$pid = $this->ngfb->meta->get_options( $post_id, 'og_img_id' );
				$pre = $this->ngfb->meta->get_options( $post_id, 'og_img_id_pre' );
				$url = $this->ngfb->meta->get_options( $post_id, 'og_img_url' );
				if ( $pid > 0 ) {
					if ( $pre == 'ngg' ) {
						$this->ngfb->debug->log( 'found custom meta image id = ' . $pre . '-' . $pid );
						list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'], 
							$og_image['og:image:cropped'] ) = $this->ngfb->media->get_ngg_image_src( $pre . '-' . $pid, $size_name );
					} else {
						$this->ngfb->debug->log( 'found custom meta image id = ' . $pid );
						list( $og_image['og:image'], $og_image['og:image:width'], $og_image['og:image:height'],
							$og_image['og:image:cropped'] ) = $this->ngfb->media->get_attachment_image_src( $pid, $size_name );
					}
					$this->push_max( $og_ret, $og_image, $num );
				}
				if ( ! empty( $url ) ) {
					$og_image = array();	// clear all array values
					$this->ngfb->debug->log( 'found custom meta image url = ' . $url );
					$og_image['og:image'] = $url;
					$this->push_max( $og_ret, $og_image, $num );
				}
			}
			return $og_ret;
		}

		private function push_max( &$dst, &$src, $num = 0 ) {
			if ( ! is_array( $dst ) || ! is_array( $src ) ) return false;
			if ( ! empty( $src ) ) array_push( $dst, $src );
			return $this->slice_max( $dst, $num );
		}

		private function slice_max( &$arr, $num = 0 ) {
			if ( ! is_array( $arr ) ) return false;
			$has = count( $arr );
			if ( $num > 0 ) {
				if ( $has == $num ) {
					$this->ngfb->debug->log( 'max values reached (' . $has . ' == ' . $num . ')' );
					return true;
				} elseif ( $has > $num ) {
					$this->ngfb->debug->log( 'max values reached (' . $has . ' > ' . $num . ') - slicing array' );
					$arr = array_slice( $arr, 0, $num );
					return true;
				}
			}
			return false;
		}

		private function is_maxed( &$arr, $num = 0 ) {
			if ( ! is_array( $arr ) ) return false;
			if ( $num > 0 && count( $arr ) >= $num ) return true;
			return false;
		}

		private function num_remains( &$arr, $num = 0 ) {
			$remains = 0;
			if ( ! is_array( $arr ) ) return false;
			if ( $num > 0 && $num >= count( $arr ) ) {
				$remains = $num - count( $arr );
				$this->ngfb->debug->log( 'images count = ' . count( $arr ) . ' of ' . $num . ' max (' . $remains . ' remaining)' );
			}
			return $remains;
		}
	}
}

?>

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

if ( ! class_exists( 'ngfbMedia' ) ) {

	class ngfbMedia {

		private $ngfb;		// ngfbPlugin

		public function __construct( &$ngfb_plugin ) {

			$this->ngfb =& $ngfb_plugin;
		}

		public function get_size_info( $size_name = 'thumbnail' ) {

			global $_wp_additional_image_sizes;

			if ( is_integer( $size_name ) ) return;
	
			if ( isset( $_wp_additional_image_sizes[$size_name]['width'] ) )
				$width = intval( $_wp_additional_image_sizes[$size_name]['width'] );
			else $width = get_option( "{$size_name}_size_w" );
		
			if ( isset( $_wp_additional_image_sizes[$size_name]['height'] ) )
				$height = intval( $_wp_additional_image_sizes[$size_name]['height'] );
			else $height = get_option( "{$size_name}_size_h" );
		
			if ( isset( $_wp_additional_image_sizes[$size_name]['crop'] ) )
				$crop = intval( $_wp_additional_image_sizes[$size_name]['crop'] );
			else $crop = get_option( "{$size_name}_crop" );

			return array( 'width' => $width, 'height' => $height, 'crop' => $crop );
		}

		public function get_attachment_image_src( $pid, $size_name = 'thumbnail' ) {
			$image_url = '';
			$size_info = $this->get_size_info( $size_name );
			$cropped = ( $size_info['crop'] == 1 ? 'true' : 'false' );
			list( $image_url, $size_info['width'], $size_info['height'] ) = wp_get_attachment_image_src( $pid, $size_name );
			$this->ngfb->debug->log( 'image for pid:' . $pid . ' size:' . $size_name . ' = ' . 
				$image_url . ' (' . $size_info['width'] . ' x ' . $size_info['height'] . ')' );
			$image_url = $this->ngfb->util->fix_relative_url( $image_url );
			if ( $this->ngfb->util->is_uniq_url( $image_url ) )
				return array( $image_url, $size_info['width'], $size_info['height'], $cropped );
			else return array( null, null, null, null );
		}

		// called to get an image URL from an NGG picture ID and a media size name (the pid must be formatted as 'ngg-#')
		public function get_ngg_image_src( $pid, $size_name = 'thumbnail' ) {

			if ( $this->ngfb->is_avail['ngg'] != true ) return;

			$image_url = '';
			$size_info = array( 'width' => '', 'height' => '', 'crop' => '' );
			$cropped = '';

			if ( is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' ) {
				global $nggdb;
				$pid = substr( $pid, 4 );
				$image = $nggdb->find_image( $pid );	// returns an nggImage object
				if ( ! empty( $image ) ) {
					$size_info = $this->get_size_info( $size_name );
					$crop = ( $size_info['crop'] == 1 ? 'crop' : '' );
					$cropped = ( $size_info['crop'] == 1 ? 'true' : 'false' );
					$image_url = $image->cached_singlepic_file( $size_info['width'], $size_info['height'], $crop ); 
					
					if ( empty( $image_url ) )	// if the image file doesn't exist, use the dynamic image url
						$image_url = trailingslashit( site_url() ) . 
							'index.php?callback=image&amp;pid=' . $pid .
							'&amp;width=' . $size_info['width'] . 
							'&amp;height=' . $size_info['height'] . 
							'&amp;mode=' . $crop;
					else {
						// get the REAL image width and height
						$cachename = $image->pid . '_' . $crop . '_'. $size_info['width'] . 'x' . $size_info['height'] . '_' . $image->filename;
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
			$this->ngfb->debug->log( 'image for pid:' . $pid . ' size:' . $size_name . ' = ' . 
				$image_url . ' (' . $size_info['width'] . ' x ' . $size_info['height'] . ')' );
			$image_url = $this->ngfb->util->fix_relative_url( $image_url );
			if ( $this->ngfb->util->is_uniq_url( $image_url ) )
				return array( $image_url, $size_info['width'], $size_info['height'], $cropped );
			else return array( null, null, null, null );
		}

	}

}
?>

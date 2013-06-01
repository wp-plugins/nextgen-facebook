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

if ( ! class_exists( 'ngfbTags' ) ) {

	class ngfbTags {

		private $ngfb;		// ngfbPlugin

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->lognew();
		}

		public function get() {
			$tags = array();
			if ( is_singular() ) {
				global $post;
				$tags = array_merge( $tags, $this->get_wp( $post->ID ) );
				if ( $this->ngfb->options['og_ngg_tags'] && $this->ngfb->is_avail['postthumb'] == true && has_post_thumbnail( $post->ID ) ) {
					$pid = get_post_thumbnail_id( $post->ID );
					if ( is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' )
						$tags = array_merge( $tags, $this->get_ngg( $pid ) );
				}
			} elseif ( is_search() )
				$tags = preg_split( '/ *, */', get_search_query( false ) );
		
			// filter for duplicate (lowercase) element values - just in case
			$tags = array_unique( array_map( 'strtolower', $tags ) );
			if ( $this->ngfb->is_avail['ngfbpro'] ) 
				return apply_filters( 'ngfb_tags', $tags );
			else return $tags;
		}

		public function get_wp( $post_id ) {
			$tags = array();
			$post_ids = array ( $post_id );	// array of one
			if ( $this->ngfb->options['og_page_parent_tags'] && is_page( $post_id ) )
				$post_ids = array_merge( $post_ids, get_post_ancestors( $post_id ) );
			foreach ( $post_ids as $id ) {
				if ( $this->ngfb->options['og_page_title_tag'] && is_page( $id ) )
					$tags[] = get_the_title( $id );
				foreach ( wp_get_post_tags( $id, array( 'fields' => 'names') ) as $tag_name ) {
					$tags[] = $tag_name;
				}
			}
			$tags = array_map( 'strtolower', $tags );
			if ( $this->ngfb->is_avail['ngfbpro'] ) 
				return apply_filters( 'ngfb_wp_tags', $tags );
			else return $tags;
		}

		// called from the view/gallery-meta.php template
		public function get_ngg( $pid ) {
			$tags = array();
			if ( $this->ngfb->is_avail['ngg'] == true && is_string( $pid ) && substr( $pid, 0, 4 ) == 'ngg-' ) {
				$tags = wp_get_object_terms( substr( $pid, 4 ), 'ngg_tag', 'fields=names' );
			}
			$tags = array_map( 'strtolower', $tags );
			if ( $this->ngfb->is_avail['ngfbpro'] ) 
				return apply_filters( 'ngfb_ngg_tags', $tags );
			else return $tags;
		}

	}

}
?>

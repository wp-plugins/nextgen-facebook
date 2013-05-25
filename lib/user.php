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

if ( ! class_exists( 'ngfbUser' ) ) {

	class ngfbUser {

		private $ngfb;		// ngfbPlugin

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->lognew();

			add_filter( 'user_contactmethods', array( &$this, 'contactmethods' ), 20, 1 );
		}

		public function contactmethods( $fields = array() ) { 
			foreach ( preg_split( '/ *, */', NGFB_CONTACT_FIELDS ) as $field_list ) {
				$field_name = preg_split( '/ *: */', $field_list );
				$fields[$field_name[0]] = $field_name[1];
			}
			ksort( $fields, SORT_STRING );
			return $fields;
		}

		// called from head and opengraph classes
		public function get_author_url( $author_id, $field_name = 'url' ) {
			switch ( $field_name ) {
				case 'none' :
					break;
				case 'index' :
					$url = get_author_posts_url( $author_id );
					break;
				default :
					$url = get_the_author_meta( $field_name, $author_id );

					// if empty or not a URL, then fallback to the author index page
					if ( $this->ngfb->options['og_author_fallback'] && ( empty( $url ) || ! preg_match( '/:\/\//', $url ) ) )
						$url = get_author_posts_url( $author_id );

					break;
			}
			return $url;
		}

	}

}
?>

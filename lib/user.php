<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbUser' ) ) {

	class ngfbUser {

		private $ngfb;		// ngfbPlugin

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();

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
					$url = get_the_author_meta( $field_name, $author_id );	// since wp 2.8.0 

					// if empty or not a URL, then fallback to the author index page
					if ( $this->ngfb->options['og_author_fallback'] && ( empty( $url ) || ! preg_match( '/:\/\//', $url ) ) )
						$url = get_author_posts_url( $author_id );

					break;
			}
			return $url;
		}

		public function collapse_metaboxes( $page, $ids = array(), $force = false ) {
			$user_id = get_current_user_id();				// since wp 3.0
			$option_name = 'closedpostboxes_' . $page;
			$option_arr = get_user_option( $option_name, $user_id );	// since wp 2.0.0 

			if ( ! is_array( $option_arr ) )
				$option_arr = array();

			if ( empty( $option_arr ) || $force == true )
				foreach ( $ids as $id ) $option_arr[] = $page . '_' . $id;

			update_user_option( $user_id, $option_name, array_unique( $option_arr ), true );	// since wp 2.0
		}

	}

}
?>

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

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();

			add_action( 'edit_user_profile_update', array( &$this, 'sanitize_contact_methods' ) );
			add_action( 'personal_options_update', array( &$this, 'sanitize_contact_methods' ) );

			add_filter( 'user_contactmethods', array( &$this, 'add_contact_methods' ), 20, 1 );
		}

		public function add_contact_methods( $fields = array() ) { 
			$social_prefix = $this->p->social_prefix;
			foreach ( $social_prefix as $id => $opt_prefix ) {
				$cm_opt = 'ngfb_cm_'.$opt_prefix.'_';
				// not all social websites have a contact method field
				if ( array_key_exists( $cm_opt.'name', $this->p->options ) ) {
					$enabled = $this->p->options[$cm_opt.'enabled'];
					$name = $this->p->options[$cm_opt.'name'];
					$label = $this->p->options[$cm_opt.'label'];
					if ( ! empty( $enabled ) && ! empty( $name ) && ! empty( $label ) )
						$fields[$name] = $label;
				}
			}
			if ( $this->p->is_avail['aop'] == true ) {
				$wp_contacts = $this->p->wp_contacts;
				foreach ( $wp_contacts as $id => $th_val ) {
					$cm_opt = 'wp_cm_'.$id.'_';
					if ( array_key_exists( $cm_opt.'enabled', $this->p->options ) ) {
						$enabled = $this->p->options[$cm_opt.'enabled'];
						$label = $this->p->options[$cm_opt.'label'];
						if ( ! empty( $enabled ) ) {
							if ( ! empty( $label ) )
								$fields[$id] = $label;
						} else unset( $fields[$id] );
					}
				}
			}
			ksort( $fields, SORT_STRING );
			return $fields;
		}

		public function sanitize_contact_methods( $user_id ) {
			if ( current_user_can( 'edit_user', $user_id ) ) {
				foreach ( $this->p->social_prefix as $id => $opt_prefix ) {
					$cm_opt = 'ngfb_cm_'.$opt_prefix.'_';
					// not all social websites have a contact method field
					if ( array_key_exists( $cm_opt.'name', $this->p->options ) ) {
						$enabled = $this->p->options[$cm_opt.'enabled'];
						$name = $this->p->options[$cm_opt.'name'];
						$label = $this->p->options[$cm_opt.'label'];
						if ( ! empty( $enabled ) && ! empty( $name ) && ! empty( $label ) ) {
							// sanitize values only for those enabled contact methods
							$val = wp_filter_nohtml_kses( $_POST[$name] );
							if ( ! empty( $val ) ) {
								// use the social_prefix id to decide on actions
								switch ( $id ) {
									case 'skype' :
										// no change
										break;
									case 'twitter' :
										$val = substr( preg_replace( '/[^a-z0-9_]/', '', 
											strtolower( $val ) ), 0, 15 );
										if ( ! empty( $val ) ) 
											$val = '@'.$val;
										break;
									default :
										if ( strpos( $val, '://' ) === false )
											$val = '';
										break;
								}
							}
							$_POST[$name] = $val;
						}
					}
				}
			}
		}

		// called from head and opengraph classes
		public function get_author_url( $author_id, $field_id = 'url' ) {
			switch ( $field_id ) {
				case 'none' :
					break;
				case 'index' :
					$url = get_author_posts_url( $author_id );
					break;
				default :
					$url = get_the_author_meta( $field_id, $author_id );	// since wp 2.8.0 
					// if empty or not a url, then fallback to the author index page
					if ( $this->p->options['og_author_fallback'] && ( empty( $url ) || ! preg_match( '/:\/\//', $url ) ) )
						$url = get_author_posts_url( $author_id );
					break;
			}
			return $url;
		}

		public function reset_metaboxes( $page, $box_ids = array(), $force = false ) {
			$user_id = get_current_user_id();				// since wp 3.0
			if ( $force == true )
				foreach ( array( 'meta-box-order', 'metaboxhidden', 'closedpostboxes' ) as $meta_name )
					delete_user_option( $user_id, $meta_name . '_' . $page, true );
			$meta_key = 'closedpostboxes_'.$page;
			$opts = get_user_option( $meta_key, $user_id );	// since wp 2.0.0 
			if ( ! is_array( $opts ) )
				$opts = array();
			if ( empty( $opts ) )
				foreach ( $box_ids as $id ) 
					$opts[] = $page . '_' . $id;
			update_user_option( $user_id, $meta_key, array_unique( $opts ), true );	// since wp 2.0
		}

		public function get_options( $user_id = false ) {
			$user_id = $user_id == false ? get_current_user_id() : $user_id;	// since wp 3.0
			$opts = get_user_option( NGFB_OPTIONS_NAME, $user_id );		// since wp 2.0.0 
			if ( ! is_array( $opts ) )
				$opts = array();
			return $opts;
		}

		public function save_options( $opts = array(), $user_id = false ) {
			$user_id = $user_id == false ? get_current_user_id() : $user_id;	// since wp 3.0
			update_user_option( $user_id, NGFB_OPTIONS_NAME, array_unique( $opts ), true );	// since wp 2.0
		}

	}

}
?>

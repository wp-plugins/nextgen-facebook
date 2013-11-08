<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbUser' ) ) {

	class NgfbUser {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();

			add_action( 'edit_user_profile_update', array( &$this, 'sanitize_contact_methods' ) );
			add_action( 'personal_options_update', array( &$this, 'sanitize_contact_methods' ) );

			add_filter( 'user_contactmethods', array( &$this, 'add_contact_methods' ), 20, 1 );
		}

		public function add_contact_methods( $fields = array() ) { 

			// loop through each social website option prefix
			if ( ! empty( $this->p->cf['opt']['pre'] ) && is_array( $this->p->cf['opt']['pre'] ) ) {

				foreach ( $this->p->cf['opt']['pre'] as $id => $pre ) {
					$cm_opt = 'plugin_cm_'.$pre.'_';
	
					// not all social websites have a contact fields, so check
					if ( array_key_exists( $cm_opt.'name', $this->p->options ) ) {
	
						$enabled = $this->p->options[$cm_opt.'enabled'];
						$name = $this->p->options[$cm_opt.'name'];
						$label = $this->p->options[$cm_opt.'label'];
	
						if ( ! empty( $enabled ) && ! empty( $name ) && ! empty( $label ) )
							$fields[$name] = $label;
					}
				}
				unset( $id, $pre );
			}

			if ( $this->p->check->is_aop() && 
				! empty( $this->p->cf['wp']['cm'] ) && is_array( $this->p->cf['wp']['cm'] ) ) {

				foreach ( $this->p->cf['wp']['cm'] as $id => $name ) {
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
			unset( $id, $name );

			ksort( $fields, SORT_STRING );
			return $fields;
		}

		public function sanitize_contact_methods( $user_id ) {

			if ( ! current_user_can( 'edit_user', $user_id ) )
				return;

			foreach ( $this->p->cf['opt']['pre'] as $id => $pre ) {
				$cm_opt = 'plugin_cm_'.$pre.'_';

				// not all social websites have a contact fields, so check
				if ( array_key_exists( $cm_opt.'name', $this->p->options ) ) {

					$enabled = $this->p->options[$cm_opt.'enabled'];
					$name = $this->p->options[$cm_opt.'name'];
					$label = $this->p->options[$cm_opt.'label'];

					if ( ! empty( $enabled ) && ! empty( $name ) && ! empty( $label ) ) {

						// sanitize values only for those enabled contact methods
						$val = wp_filter_nohtml_kses( $_POST[$name] );

						if ( ! empty( $val ) ) {
							// use the social prefix id to decide on actions
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

					// if empty or not a url, then fallback to the author index page,
					// if the requested field is the opengraph or link author field
					if ( empty( $url ) || ! preg_match( '/:\/\//', $url ) ) {
						if ( ( $field_id == $this->p->options['og_author_field'] || 
							$field_id == $this->p->options['link_author_field'] ) && 
							$this->p->options['og_author_fallback'] )
								$url = get_author_posts_url( $author_id );
					}
					break;
			}
			return $url;
		}

		public function reset_metabox_prefs( $pagehook, $metabox_ids = array(), $state = '', $force = false ) {
			$user_id = get_current_user_id();	// since wp 3.0

			// if forced, remove all existing metabox preferences for that pagehook
			if ( $force == true )
				foreach ( array( 'meta-box-order', 'metaboxhidden', 'closedpostboxes' ) as $meta_name )
					delete_user_option( $user_id, $meta_name.'_'.$pagehook, true );

			// define a new state to set for the metabox_ids given
			switch ( $state ) {
				case 'order' : 
					$meta_key = 'meta-box-order_'.$pagehook; 
					break ;
				case 'hidden' : 
					$meta_key = 'metaboxhidden_'.$pagehook; 
					break ;
				case 'closed' : 
					$meta_key = 'closedpostboxes_'.$pagehook; 
					break ;
				default :
					$meta_key = '';
					break;
			}

			// if preferences don't already exist for that state, then create them
			if ( ! empty( $meta_key ) ) {
				$opts = get_user_option( $meta_key, $user_id );	// since wp 2.0.0 
				if ( ! is_array( $opts ) )
					$opts = array();
				if ( empty( $opts ) ) {
					foreach ( $metabox_ids as $id ) 
						$opts[] = $pagehook.'_'.$id;
					update_user_option( $user_id, $meta_key, array_unique( $opts ), true );	// since wp 2.0
				}
			}
		}

		// delete metabox preferences for one or all users
		public function delete_metabox_prefs( $user_id = false ) {
			foreach ( array( 'meta-box-order', 'metaboxhidden', 'closedpostboxes' ) as $meta_name ) {
				$menu_ids = array( key( $this->p->cf['lib']['setting'] ) );
				foreach ( $menu_ids as $menu ) {
					$setting_ids = array_keys( $this->p->cf['lib']['setting'] );
					foreach ( $setting_ids as $submenu ) {
						if ( $submenu == 'contact' )
							$parent_slug = 'options-general.php';
						else $parent_slug = $this->p->cf['lca'].'-'.$menu;
						$menu_slug = $this->p->cf['lca'].'-'.$submenu;
						$hookname = get_plugin_page_hookname( $menu_slug, $parent_slug);
						$meta_key = $meta_name.'_'.$hookname;
						if ( $user_id !== false )
							delete_user_option( $user_id, $meta_key, true );
						else
							foreach ( get_users( array( 'meta_key' => $meta_key ) ) as $user )
								delete_user_option( $user->ID, $meta_key, true );
					}
				}
			}
		}

		public function get_options( $user_id = false ) {
			$user_id = $user_id === false ? 
				get_current_user_id() : $user_id;
			$opts = get_user_option( constant( $this->p->cf['uca'].'_OPTIONS_NAME' ), $user_id );
			if ( ! is_array( $opts ) )
				$opts = array();
			return $opts;
		}

		public function save_options( $opts = array(), $user_id = false ) {
			$user_id = $user_id === false ? 
				get_current_user_id() : $user_id;
			update_user_option( $user_id, constant( $this->p->cf['uca'].'_OPTIONS_NAME' ), 
				array_unique( $opts ), true );
		}
	}
}

?>

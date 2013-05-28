<?php
/*
Copyright 2013 - Jean-Sebastien Morisset - http://surniaulula.com/

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

if ( ! class_exists( 'ngfbCache' ) ) {

	class ngfbCache {

		public $base_dir = '';
		public $base_url = '/cache/';
		public $pem_file = '';
		public $verify_cert = false;
		public $user_agent = '';
		public $file_expire = 0;
		public $object_expire = 300;

		private $ngfb;		// ngfbPlugin

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->lognew();
			$this->base_dir = dirname ( __FILE__ ) . '/cache/';
			$this->user_agent = $_SERVER['HTTP_USER_AGENT'];
		}

		public function get( $url, $want_this = 'url', $cache_name = 'file', $expire_secs = false ) {
			if ( ! function_exists('curl_init') ) return $url;

			// if we're not using https on the current page, then no need to make our requests using https
			$get_url = empty( $_SERVER['HTTPS'] ) ? preg_replace( '/^https:/', 'http:', $url ) : $url;
			$get_url = preg_replace( '/#.*$/', '', $get_url );

			$url_path = parse_url( $get_url, PHP_URL_PATH );
			$url_ext = pathinfo( $url_path, PATHINFO_EXTENSION );
			$url_frag = parse_url( $url, PHP_URL_FRAGMENT );
			if ( ! empty( $url_frag ) ) $url_frag = '#' . $url_frag;

			$cache_salt = __METHOD__ . '(get_url:' . $get_url . ')';
			$cache_id = md5( $cache_salt );
			$cache_file = $this->base_dir . $cache_id . '.' . $url_ext;
			$cache_url = $this->base_url . $cache_id . '.' . $url_ext . $url_frag;
			$cache_data = '';

			if ( $want_this == 'raw' ) {
				$cache_data = $this->get_cache_data( $cache_salt, $cache_name, $url_ext, $expire_secs );
				if ( ! empty( $cache_data ) ) {
					$this->ngfb->debug->log( 'cache_data is present - returning ' . strlen( $cache_data ) . ' chars' );
					return $cache_data;
				}
			} elseif ( $want_this == 'url' ) {
				$file_expire = $expire_secs == false ? $this->file_expire : $expire_secs;
				if ( file_exists( $cache_file ) && filemtime( $cache_file ) > time() - $file_expire ) {
					$this->ngfb->debug->log( 'cache_file is current - returning cache url "' . $cache_url . '"' );
					return $cache_url;
				} else $this->ngfb->debug->log( 'cache_file is too old or doesn\'t exist - fetching a new copy' );
			}

			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, $get_url );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, TRUE );
			curl_setopt( $ch, CURLOPT_USERAGENT, $this->user_agent );

			if ( empty( $this->verify_cert) ) {
				curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
			} else {
				curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, 0 );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, TRUE );
				curl_setopt( $ch, CURLOPT_CAINFO, $this->pem_file );
			}
			$this->ngfb->debug->log( 'curl: fetching cache_data from ' . $get_url );
			$cache_data = curl_exec( $ch );
			curl_close( $ch );

			if ( empty( $cache_data ) ) 
				$this->ngfb->debug->log( 'curl: cache_data returned from "' . $get_url . '" is empty' );
			elseif ( $this->save_cache_data( $cache_salt, $cache_data, $cache_name, $url_ext, $expire_secs ) == true ) {
				$this->ngfb->debug->log( 'cache_data sucessfully saved' );
				if ( $want_this == 'url' ) return $cache_url;
			}

			if ( $want_this == 'raw' ) return $cache_data;
			else return $url;
		}

		private function get_cache_data( $cache_salt, $cache_name = 'file', $url_ext = '', $expire_secs = false ) {
			$cache_data = '';
			switch ( $cache_name ) {
				case 'wp_cache' :
				case 'transient' :
					$cache_type = 'object cache';
					$cache_id = $this->ngfb->acronym . '_' . md5( $cache_salt );	// add a prefix to the object cache id
					$this->ngfb->debug->log( $cache_type . ': cache_data ' . $cache_name . ' id salt "' . $cache_salt . '"' );
					if ( $cache_name == 'wp_cache' ) 
						$cache_data = wp_cache_get( $cache_id, __METHOD__ );
					elseif ( $cache_name == 'transient' ) 
						$cache_data = get_transient( $cache_id );
					if ( $cache_data !== false ) {
						$this->ngfb->debug->log( $cache_type . ': cache_data retrieved from ' . $cache_name . ' for id "' . $cache_id . '"' );
					}
					break;
				case 'file' :
					$cache_type = 'file cache';
					$cache_id = md5( $cache_salt );
					$cache_file = $this->base_dir . $cache_id . '.' . $url_ext;
					$this->ngfb->debug->log( $cache_type . ': filename id salt "' . $cache_salt . '"' );
					$file_expire = $expire_secs == false ? $this->file_expire : $expire_secs;
					if ( file_exists( $cache_file ) ) {
						if ( ! is_readable( $cache_file ) )
							$this->ngfb->notices->err( $cache_file . ' is not readable.' );
						elseif ( filemtime( $cache_file ) > time() - $file_expire ) {
							if ( ! $fh = @fopen( $cache_file, 'rb' ) )
								$this->ngfb->notices->err( 'Failed to open ' . $cache_file . ' for reading.' );
							else {
								$cache_data = fread( $fh, filesize( $cache_file ) );
								fclose( $fh );
								if ( ! empty( $cache_data ) )
									$this->ngfb->debug->log( $cache_type . ': cache_data retrieved from "' . $cache_file . '"' );
							}
						}
					}
					break;
				default :
					$this->ngfb->debug->log( 'unknown cache name "' . $cache_name . '"' );
					break;
			}
			return $cache_data;	// return data or empty string
		}

		private function save_cache_data( $cache_salt, $cache_data = '', $cache_name = 'file', $url_ext = '', $expire_secs = false ) {
			if ( empty( $cache_data ) ) return false;
			$ret_status = false;
			switch ( $cache_name ) {
				case 'wp_cache' :
				case 'transient' :
					$cache_type = 'object cache';
					$cache_id = $this->ngfb->acronym . '_' . md5( $cache_salt );	// add a prefix to the object cache id
					$this->ngfb->debug->log( $cache_type . ': cache_data ' . $cache_name . ' id salt "' . $cache_salt . '"' );
					$object_expire = $expire_secs == false ? $this->object_expire : $expire_secs;
					if ( $cache_name == 'wp_cache' ) 
						wp_cache_set( $cache_id, $cache_data, __METHOD__, $object_expire );
					elseif ( $cache_name == 'transient' ) 
						set_transient( $cache_id, $cache_data, $object_expire );
					$this->ngfb->debug->log( $cache_type . ': cache_data saved to ' . $cache_name . ' for id "' . $cache_id . '" (' . $object_expire . ' seconds)' );
					$ret_status = true;	// success
					break;
				case 'file' :
					$cache_type = 'file cache';
					$cache_id = md5( $cache_salt );
					$cache_file = $this->base_dir . $cache_id . '.' . $url_ext;
					$this->ngfb->debug->log( $cache_type . ': filename id salt "' . $cache_salt . '"' );
					if ( ! is_dir( $this->base_dir ) ) 
						mkdir( $this->base_dir );
					if ( ! is_writable( $this->base_dir ) )
						$this->ngfb->notices->err( $this->base_dir . ' is not writable.' );
					else {
						if ( ! $fh = @fopen( $cache_file, 'wb' ) )
							$this->ngfb->notices->err( 'Failed to open ' . $cache_file . ' for writing.' );
						else {
							if ( fwrite( $fh, $cache_data ) ) {
								$this->ngfb->debug->log( $cache_type . ': cache_data saved to "' . $cache_file . '"' );
								$ret_status = true;	// success
							}
							fclose( $fh );
						}
					}
					break;
				default :
					$this->ngfb->debug->log( 'unknown cache name "' . $cache_name . '"' );
					break;
			}
			return $ret_status;	// return true or false
		}
	}
}
?>

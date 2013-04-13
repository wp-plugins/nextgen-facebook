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

The following ngfbCache() class was written for the NextGEN Facebook OG plugin
for WordPress, available at http://wordpress.org/extend/plugins/nextgen-facebook/.

Example usage:

require_once ( dirname ( __FILE__ ) . '/cache.php' );

$cache = new ngfbCache();
$cache->base_dir = '/var/www/htdocs/cache/';
$cache->base_url = '/cache/';
$cache->pem_file = dirname ( __FILE__ ) . 'curl/cacert.pem';
$cache->verify_cert = true;
$cache->file_expire = 3 * 60 * 60;	// cache the file for 3 hours

$url = $cache->get( $url );		// return a modified url (default)
$raw = $cache->get( $url, 'raw' );	// return the file's content instead

Some source files, like the Google+ plusone.js javascript file, may change
depending on the user agent. In this case, you may want to define the
$cache->user_agent variable as well. The default user agent is the one provided
by the browser (consider that crawlers may refresh the cache files, so
hard-coding a user agent may be desirable). The $cache->base_url can be
relative or include the protocol (http / https), and could be an alias to the
real folder location (which may be outside of the document root). The
$cache->base_dir and $cache->base_url variables should end with a slash.
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbCache' ) ) {

	class ngfbCache {

		var $base_dir = '';
		var $base_url = '/cache/';
		var $pem_file = '';
		var $verify_cert = false;
		var $file_expire = 0;
		var $object_expire = 300;
		var $user_agent = '';

		function __construct() {
			$this->base_dir = dirname ( __FILE__ ) . '/cache/';
			$this->user_agent = $_SERVER['HTTP_USER_AGENT'];
		}

		// $ret = url; return the cached url if successful, else return the original url
		// $ret = raw; return the cached cache_data if successful, else return an empty string
		// $cache = true; by default, cache the data retrieved

		function get( $url, $ret = 'url', $cache = 'file' ) {

			if ( ! function_exists('curl_init') ) return $url;

			global $ngfb;

			// if we're not using https on the current page, then no need to make our requests using https
			$get_url = empty( $_SERVER['HTTPS'] ) ? preg_replace( '/^https:/', 'http:', $url ) : $url;
			$get_url = preg_replace( '/#.*$/', '', $get_url );

			$url_path = parse_url( $get_url, PHP_URL_PATH );
			$url_ext = pathinfo( $url_path, PATHINFO_EXTENSION );
			$url_frag = parse_url( $url, PHP_URL_FRAGMENT );
			if ( ! empty( $url_frag ) ) $url_frag = '#' . $url_frag;

			$cache_id = md5( $get_url );
			$cache_group = __METHOD__;
			$cache_file = $this->base_dir . $cache_id . '.' . $url_ext;
			$cache_url = $this->base_url . $cache_id . '.' . $url_ext . $url_frag;
			$cache_data = '';

			switch ( $cache ) {
				case 'wp_cache' :
				case 'transient' :
					$cache_type = 'object cache';
					break;
				case 'file' :
					$cache_type = 'file cache';
					break;
				default :
					$ngfb->d_msg( 'unknown cache name "' . $cache . '"' );
					break;
			}

			if ( $cache_type == 'object cache' ) {
				if ( $ret == 'raw' ) {
					if ( $cache == 'wp_cache' ) 
						$cache_data = wp_cache_get( $cache_id, $cache_group );
					elseif ( $cache == 'transient' ) 
						$cache_data = get_transient( $cache_id );

					if ( $cache_data !== false ) {
						$ngfb->d_msg( $cache_type . ' : cache_data retrieved from ' . $cache . ' for id "' . $cache_id . '"' );
						return $cache_data;
					}
				} else {
					$ngfb->d_msg( $cache_type . ' : nothing to do -- returning original url ' . $url );
					return $url;
				}
			} elseif ( $cache == 'file' && file_exists( $cache_file ) && filemtime( $cache_file ) > time() - $this->file_expire ) {
				if ( $ret == 'raw' ) {
					$fh = fopen( $cache_file, 'rb' );
					$cache_data = fread( $fh, filesize( $cache_file ) );
					fclose( $fh );

					if ( ! empty( $cache_data ) ) {
						$ngfb->d_msg( $cache_type . ' : cache_data retrieved from ' . $cache_file );
						return $cache_data;
					}
				} else {
					$ngfb->d_msg( $cache_type . ' : returning url to file ' . $cache_url );
					return $cache_url;
				}
			}

			$cache_data = '';
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
			$ngfb->d_msg( 'curl : fetching cache_data from ' . $get_url );
			$cache_data = curl_exec( $ch );
			curl_close( $ch );

			if ( empty( $cache_data ) ) {
				$ngfb->d_msg( 'curl : cache_data returned from ' . $get_url . ' is empty' );
			} else {
				if ( $cache_type == 'object cache' ) {
					if ( $ret == 'raw' ) {
						if ( $cache == 'wp_cache' ) wp_cache_set( $cache_id, $cache_data, $cache_group, $this->object_expire );
						elseif ( $cache == 'transient' ) set_transient( $cache_id, $cache_data, $this->object_expire );
						$ngfb->d_msg( $cache_type . ' : cache_data saved to ' . $cache . ' for '. $this->object_expire . ' seconds' );
					}
				} elseif ( $cache == 'file' ) {
					if ( ! is_dir( $this->base_dir ) ) 
						mkdir( $this->base_dir );
					$fh = fopen( $cache_file, 'wb' );
					if ( ! empty( $fh ) ) {
						if ( fwrite( $fh, $cache_data ) ) {
							$ngfb->d_msg( $cache_type . ' : cache_data saved to ' . $cache_file );
							$url = $cache_url;
						}
						fclose( $fh );
					}
				}
			}

			if ( $ret == 'raw' ) return $cache_data;
			else return $url;
		}
	}
}
?>

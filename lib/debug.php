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

if ( ! class_exists( 'ngfbDebug' ) ) {

	class ngfbDebug {

		var $on = 0;
		var $log = array();

		function __construct() {
		}

		function push( $msg = '' ) {
			if ( $this->on || ( defined( 'NGFB_DEBUG' ) && NGFB_DEBUG ) ) {
				$stack = debug_backtrace();
				if ( ! empty( $stack[1]['function'] ) )
					$from = $stack[1]['function'];
				if ( ! empty( $from ) ) $msg = sprintf( '%24s() : %s', $from, $msg );
				$this->log[] = $msg;
			}
			return;
		}

		function show( $data = null, $title = null, $from = null ) {
			if ( empty( $from ) ) {
				$stack = debug_backtrace();
				if ( ! empty( $stack[1]['function'] ) )
					$from = $stack[1]['function'];
			}
			echo $this->get( $data, $title, $from );
		}

		function get( $data = null, $title = null, $from = null ) {
			$html = null;
			if ( $this->on || ( defined( 'NGFB_DEBUG' ) && NGFB_DEBUG ) ) {
				$html .= "<!-- " . NGFB_ACRONYM . " debug";
				if ( empty( $from ) ) {
					$stack = debug_backtrace();
					if ( ! empty( $stack[1]['function'] ) )
						$from = $stack[1]['function'];
				}
				if ( empty( $data ) ) {
					$this->push( 'truncating debug log' );
					$data = $this->log;
					$this->log = array();
				}
				if ( ! empty( $from ) ) 
					$html .= ' from ' . $from . '()';
				if ( ! empty( $title ) ) 
					$html .= ' ' . $title;
				if ( ! empty( $data ) ) {
					$html .= ' : ';
					if ( is_array( $data ) ) {
						$html .= "\n";
						$is_assoc = $this->is_assoc( $data );
						if ( $is_assoc ) ksort( $data );
						foreach ( $data as $key => $val ) 
							$html .= $is_assoc ? "\t$key = $val\n" : "\t$val\n";
						unset ( $key, $val );
					} else {
						if ( preg_match( '/^Array/', $data ) ) $html .= "\n";	// check for print_r() output
						$html .= $data;
					}
				}
				$html .= ' -->' . "\n";
			}
			return $html;
		}

		function is_assoc( $arr ) {
			if ( ! is_array( $arr ) ) return 0;
			return is_numeric( implode( array_keys( $arr ) ) ) ? 0 : 1;
		}


	}
}
?>

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

		public $on = false;

		private $ngfb;		// ngfbPlugin
		private $msgs = array();

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->lognew();
		}

		public function lognew() {
			$this->log( 'object created', 2 );
		}

		public function log( $msg = '', $back = 1 ) {
			if ( $this->on || ( defined( 'NGFB_DEBUG' ) && NGFB_DEBUG ) ) {
				$from = '';
				$stack = debug_backtrace();
				if ( ! empty( $stack[$back]['class'] ) ) 
					$from .= sprintf( '%-25s:: ', $stack[$back]['class'] );

				if ( ! empty( $stack[$back]['function'] ) ) 
					$from .= sprintf( '%-24s : ', $stack[$back]['function'] );

				if ( ! empty( $from ) ) 
					$msg = $from . $msg;

				$this->msgs[] = $msg;
			}
		}

		public function show( $data = null, $title = null, $from = null ) {
			if ( empty( $from ) ) {
				$stack = debug_backtrace();
				if ( ! empty( $stack[1]['class'] ) ) 
					$from .= $stack[1]['class'] . '::';
				if ( ! empty( $stack[1]['function'] ) )
					$from .= $stack[1]['function'];
			}
			echo $this->get( $data, $title, $from );
		}

		public function get( $data = null, $title = null, $from = null ) {
			$html = null;
			if ( $this->on || ( defined( 'NGFB_DEBUG' ) && NGFB_DEBUG ) ) {
				$html .= "<!-- " . $this->ngfb->fullname . " debug";
				if ( empty( $from ) ) {
					$stack = debug_backtrace();
					if ( ! empty( $stack[1]['class'] ) ) 
						$from .= $stack[1]['class'] . '::';
					if ( ! empty( $stack[1]['function'] ) )
						$from .= $stack[1]['function'];
				}
				if ( empty( $data ) ) {
					$this->log( 'truncating debug log' );
					$data = $this->msgs;
					$this->msgs = array();
				}
				if ( ! empty( $from ) ) $html .= ' from ' . $from . '()';
				if ( ! empty( $title ) ) $html .= ' ' . $title;
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

		private function is_assoc( $arr ) {
			if ( ! is_array( $arr ) ) return false;
			return is_numeric( implode( array_keys( $arr ) ) ) ? false : true;
		}


	}
}

?>

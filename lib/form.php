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

if ( ! class_exists( 'ngfbForm' ) ) {

	class ngfbForm {
	
		public $options_name;

		private $ngfb;
		private $options = array();
		private $defaults = array();

		public function __construct( &$ngfb_plugin, $opts_name, &$opts, &$def_opts ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
			$this->options_name =& $opts_name;
			$this->options =& $opts;
			$this->defaults =& $def_opts;
		}

		public function get_hidden( $name, $value = '' ) {
			if ( empty( $name ) ) return;	// just in case
			// hide the current options value, unless one is given as an argument to the method
			$value = empty( $value ) && $this->in_options( $name ) ? $this->options[$name] : $value;
			return '<input type="hidden" name="' . $this->options_name . '[' . $name . ']" value="' . $value . '" />' . "\n";
		}

		public function get_input( $name, $class = '', $id = '' ) {
			if ( empty( $name ) ) return;	// just in case
			return '<input type="text" name="' . $this->options_name . '[' . $name . ']"' .
				( empty( $class ) ? '' : ' class="'.$class.'"' ) .
				( empty( $id ) ? '' : ' id="'.$id.'"' ) .
				' value="' . ( $this->in_options( $name ) ? $this->options[$name] : '' ) . '" />' . "\n";
		}

		public function get_checkbox( $name, $check = array( '1', '0' ) ) {
			if ( empty( $name ) ) return;	// just in case
			return $this->get_hidden( 'is_checkbox_' . $name, 1 ) .
				'<input type="checkbox" name="' . $this->options_name . '[' . $name . ']" value="' . $check[0] . '"' .
				( $this->in_options( $name ) ? checked( $this->options[$name], $check[0], false ) : '' ) . 
				' title="Default is ' .
				( $this->in_options( $name ) && $this->defaults[$name] == $check[0] ? 'Checked' : 'Unchecked' ) . '" />' . "\n";
		}

		public function get_select( $name, $values = array(), $class = '', $id = '', $is_assoc = false ) {
			if ( empty( $name ) || ! is_array( $values ) ) return;
			if ( $is_assoc == false ) $is_assoc = $this->ngfb->util->is_assoc( $values );

			$html = '<select name="' . $this->options_name . '[' . $name . ']"' .
				( empty( $class ) ? '' : ' class="'.$class.'"' ) .
				( empty( $id ) ? '' : ' id="'.$id.'"' ) . '>' . "\n";

			foreach ( $values as $val => $desc ) {
				// if the array is NOT associative (so regular numered array), 
				// then the description is used as the saved value as well
				if ( $is_assoc == false ) $val = $desc;

				if ( $val == -1 ) 
					$desc = '(value from settings)';
				else {
					switch ( $name ) {
						case 'og_img_max' :
							if ( $desc === 0 ) 
								$desc .= ' (no images)';
							break;
						case 'og_vid_max' :
							if ( $desc === 0 ) 
								$desc .= ' (no videos)';
							break;
						default:
							if ( $desc === '' || $desc === 'none' ) 
								$desc = '[none]';
							break;
					}
					if ( $this->in_options( $name ) && $val == $this->defaults[$name] ) 
						$desc .= ' (default)';
				}
				$html .= '<option value="' . $val . '"';
				if ( $this->in_options( $name ) )
					$html .= selected( $this->options[$name], $val, false );
				$html .= '>' . $desc . '</option>' . "\n";
			}
			$html .= '</select>' . "\n";
			return $html;
		}

		public function get_select_img_size( $name ) {
			if ( empty( $name ) ) return;	// just in case
			global $_wp_additional_image_sizes;
			$size_names = get_intermediate_image_sizes();
			natsort( $size_names );
			$html = '<select name="' . $this->options_name . '[' . $name . ']">' . "\n";
			foreach ( $size_names as $size_name ) {
				if ( is_integer( $size_name ) ) continue;
				$size = $this->ngfb->media->get_size_info( $size_name );
				$html .= '<option value="' . $size_name . '" ';
				if ( $this->in_options( $name ) )
					$html .= selected( $this->options[$name], $size_name, false );
				$html .= '>' . $size_name . ' [ ' . $size['width'] . 'x' . $size['height'] . ( $size['crop'] ? " cropped" : "" ) . ' ]';
				if ( $size_name == $this->defaults[$name] ) 
					$html .= ' (default)';
				$html .= '</option>' . "\n";
			}
			unset ( $size_name );
			$html .= '</select>' . "\n";
			return $html;
		}

		public function get_textarea( $name, $class = '', $id = '', $len ) {
			if ( empty( $name ) ) return;	// just in case
			$html = '<textarea name="' . $this->options_name . '[' . $name . ']"' .
				( empty( $class ) ? '' : ' class="'.$class.'"' ) .
				( empty( $id ) ? '' : ' id="'.$id.'"' ) . 
				( empty( $len ) ? '' : ' maxLength="'.$len.'"' ) . 
				'>' .  ( $this->in_options( $name ) ? $this->options[$name] : '' ) .
				'</textarea>' . "\n";
			return $html;
		}

		private function in_options( $name ) {
			return is_array( $this->options ) && array_key_exists( $name, $this->options ) ? true : false;
		}
	}
}

?>

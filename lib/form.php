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
	
		private $ngfb;
		private $options_name;
		private $options = array();
		private $default_options = array();

		public function __construct( &$ngfb_plugin, $opts_name, &$opts, &$def_opts ) {
			$this->ngfb =& $ngfb_plugin;
			$this->options_name =& $opts_name;
			$this->options =& $opts;
			$this->default_options =& $def_opts;
		}

		public function hidden( $name, $value = '' ) {
			if ( empty( $name ) ) return;	// just in case
			// hide the current options value, unless one is given as an argument to the method
			$value = empty( $value ) && array_key_exists( $name, $this->options ) ? $this->options[$name] : $value;
			echo '<input type="hidden" name="', $this->options_name, '[', $name, ']" value="', $value, '" />';
		}

		public function input( $name, $class = '', $id = '' ) {
			if ( empty( $name ) ) return;	// just in case
			echo '<input type="text" name="', $this->options_name, '[', $name, ']"',
				( empty( $class ) ? '' : ' class="'.$class.'"' ),
				( empty( $id ) ? '' : ' id="'.$id.'"' ),
				' value="', ( array_key_exists( $name, $this->options ) ? $this->options[$name] : '' ), '" />';
		}

		public function checkbox( $name, $echo = true, $check = array( '1', '0' ) ) {
			if ( empty( $name ) ) return;	// just in case
			$input = '<input type="checkbox" name="' . $this->options_name . '[' . $name . ']" value="' . $check[0] . '"' .
				( array_key_exists( $name, $this->options ) ? checked( $this->options[$name], $check[0], false ) : '' ) . 
				' title="Default is ' .
				( array_key_exists( $name, $this->options ) && 
					$this->default_options[$name] == $check[0] ? 'Checked' : 'Unchecked' ) . '" />';
			if ( $echo ) echo $input;
			else return $input;
		}

		public function select( $name, $values = array(), $class = '', $id = '', $is_assoc = false ) {
			if ( empty( $name ) ) return;	// just in case
			if ( $is_assoc == false )
				$is_assoc = $this->ngfb->is_assoc( $values );

			echo '<select name="', $this->options_name, '[', $name, ']"',
				( empty( $class ) ? '' : ' class="'.$class.'"' ),
				( empty( $id ) ? '' : ' id="'.$id.'"' ), '>', "\n";

			foreach ( (array) $values as $val => $desc ) {
				if ( $is_assoc == false ) 
					$val = $desc;

				echo '<option value="', $val, '"';
				if ( array_key_exists( $name, $this->options ) )
					selected( $this->options[$name], $val );
				echo '>', $desc;
				if ( $desc === '' ) echo 'None';
				if ( array_key_exists( $name, $this->options ) && 
					$val == $this->default_options[$name] ) 
						echo ' (default)';
				echo '</option>', "\n";
			}
			echo '</select>';
		}

		public function select_img_size( $name ) {
			if ( empty( $name ) ) return;	// just in case
			global $_wp_additional_image_sizes;
			$size_names = get_intermediate_image_sizes();
			natsort( $size_names );
			echo '<select name="', $this->options_name, '[', $name, ']">', "\n";
			foreach ( $size_names as $size_name ) {
				if ( is_integer( $size_name ) ) continue;
				$size = $this->ngfb->get_size_values( $size_name );
				echo '<option value="', $size_name, '" ';
				if ( array_key_exists( $name, $this->options ) )
					selected( $this->options[$name], $size_name );
				echo '>', $size_name, ' [ ', $size['width'], 'x', $size['height'], $size['crop'] ? " cropped" : "", ' ]';
				if ( $size_name == $this->default_options[$name] ) echo ' (default)';
				echo '</option>', "\n";
			}
			unset ( $size_name );
			echo '</select>', "\n";
		}

		public function textarea( $name, $class = '', $id = '' ) {
			if ( empty( $name ) ) return;	// just in case
			echo '<textarea name="', $this->options_name, '[', $name, ']"',
				( empty( $class ) ? '' : ' class="'.$class.'"' ),
				( empty( $id ) ? '' : ' id="'.$id.'"' ), '>', 
				( array_key_exists( $name, $this->options ) ? $this->options[$name] : '' ),
				'</textarea>';
		}

	}
}

?>

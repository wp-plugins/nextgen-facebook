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

if ( ! function_exists( 'ngfb_get_social_buttons' ) ) {
	function ngfb_get_social_buttons( $ids = array(), $atts = array() ) {
		global $ngfb;
		$cache_salt = __METHOD__ . '(url:' . $ngfb->get_sharing_url( 'notrack' ) . '_ids:' . ( implode( '_', $ids ) ) . '_atts:' . ( implode( '_', $atts ) ) . ')';
		$cache_id = 'ngfb_' . md5( $cache_salt );
		$cache_type = 'object cache';
		$ngfb->debug->push( $cache_type . ': social buttons transient id salt "' . $cache_salt . '"' );
		$button_html = get_transient( $cache_id );

		if ( $button_html !== false ) {
			$ngfb->debug->push( $cache_type . ': button_html retrieved from transient for id "' . $cache_id . '"' );
		} else {
			$button_html = "\n<!-- " . NGFB_FULLNAME . " social buttons BEGIN -->\n" .
				$ngfb->buttons->get_js( 'pre-social-buttons', $ids ) .
				$ngfb->buttons->get_html( $ids, $atts ) .
				$ngfb->buttons->get_js( 'post-social-buttons', $ids ) .
				"<!-- " . NGFB_FULLNAME . " social buttons END -->\n";

			set_transient( $cache_id, $button_html, $ngfb->cache->object_expire );
			$ngfb->debug->push( $cache_type . ': button_html saved to transient for id "' . $cache_id . '" (' . $ngfb->cache->object_expire . ' seconds)');
		}
		return $ngfb->debug->get() . $button_html;
	}
}

?>

<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! function_exists( 'ngfb_get_social_buttons' ) ) {

	function ngfb_get_social_buttons( $ids = array(), $atts = array() ) {
		global $ngfb;

		if ( defined( 'NGFB_TRANSIENT_CACHE_DISABLE' ) && NGFB_TRANSIENT_CACHE_DISABLE )
			$this->p->debug->log( 'transient cache is disabled' );
		else {
			$cache_salt = __METHOD__.'(lang:'.get_locale().'_sharing_url:'.$ngfb->util->get_sharing_url( 'notrack' ).'_ids:'.( implode( '_', $ids ) ).'_atts:'.( implode( '_', $atts ) ).')';
			$cache_id = $ngfb->acronym.'_'.md5( $cache_salt );
			$cache_type = 'object cache';
			$ngfb->debug->log( $cache_type.': social buttons transient id salt "'.$cache_salt.'"' );
			$html = get_transient( $cache_id );
			if ( $html !== false ) {
				$ngfb->debug->log( $cache_type.': html retrieved from transient for id "'.$cache_id.'"' );
				return $ngfb->debug->get_html().$html;
			}
		}

		$html = "\n<!-- ".$ngfb->fullname." social buttons BEGIN -->\n" .
			$ngfb->social->get_js( 'pre-social-buttons', $ids ) .
			$ngfb->social->get_html( $ids, $atts ) .
			$ngfb->social->get_js( 'post-social-buttons', $ids ) .
			"<!-- ".$ngfb->fullname." social buttons END -->\n";

		if ( ! defined( 'NGFB_TRANSIENT_CACHE_DISABLE' ) || ! NGFB_TRANSIENT_CACHE_DISABLE ) {
			set_transient( $cache_id, $html, $ngfb->cache->object_expire );
			$ngfb->debug->log( $cache_type.': html saved to transient for id "'.$cache_id.'" ('.$ngfb->cache->object_expire.' seconds)');
		}
		return $ngfb->debug->get_html().$html;
	}
}

?>

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

if ( ! class_exists( 'ngfbSocialButtonsWidget' ) ) {

	class ngfbSocialButtonsWidget extends WP_Widget {
	
		function __construct() {
			$widget_ops = array( 'classname' => 'ngfb-widget-buttons',
				'description' => 'The ' . NGFB_FULLNAME . ' social sharing buttons widget.' );
			$this->WP_Widget( 'ngfb-widget-buttons', NGFB_ACRONYM . ' Social Sharing Buttons', $widget_ops );
		}
	
		function widget( $args, $instance ) {
			global $ngfb;
	
			// if using the Exclude Pages plugin, skip social buttons on those pages
			if ( is_page() && $ngfb->is_excluded() ) return;
	
			extract( $args );

			//if ( is_search() ) $sharing_url = $ngfb->get_sharing_url( 'notrack' );
			//else $sharing_url = $ngfb->get_sharing_url();

			$sharing_url = $ngfb->get_sharing_url( 'notrack' );
			$cache_salt = __METHOD__ . '(widget:' . $this->id . '_sharing_url:' . $sharing_url . ')';
			$cache_id = 'ngfb_' . md5( $cache_salt );
			$cache_type = 'object cache';
			$widget_html = get_transient( $cache_id );
			$ngfb->debug->push( $cache_type . ' : widget_html transient id salt "' . $cache_salt . '"' );

			if ( $widget_html !== false ) {
				$ngfb->debug->push( $cache_type . ' : widget_html retrieved from transient for id "' . $cache_id . '"' );
			} else {
				$widget_html = '';
				$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
				$sorted_ids = array();
				foreach ( $ngfb->social_options_prefix as $id => $prefix )
					if ( (int) $instance[$id] )
						$sorted_ids[$ngfb->options[$prefix.'_order'] . '-' . $id] = $id;
				ksort( $sorted_ids );
	
				$widget_html .= "\n<!-- " . NGFB_LONGNAME . " widget BEGIN -->\n";
				$widget_html .= $before_widget . "\n";
				if ( $title ) $widget_html .= $before_title . $title . $after_title . "\n";
				$widget_html .= $ngfb->buttons->get_html( $sorted_ids, array( 'is_widget' => 1, 'css_id' => $args['widget_id'] ) );
				$widget_html .= $after_widget . "\n";
				$widget_html .= "<!-- " . NGFB_LONGNAME . " widget END -->\n";
	
				set_transient( $cache_id, $widget_html, $ngfb->cache->object_expire );
				$ngfb->debug->push( $cache_type . ' : widget_html saved to transient for id "' . $cache_id . '" (' . $ngfb->cache->object_expire . ' seconds)');
			}
			$ngfb->debug->show();
			echo $widget_html;
		}
	
		function update( $new_instance, $old_instance ) {
			global $ngfb;
			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			foreach ( $ngfb->social_nice_names as $id => $name ) {
				$instance[$id] = empty( $new_instance[$id] ) ? 0 : 1;
			}
			unset( $name, $id );
			return $instance;
		}
	
		function form( $instance ) {
			global $ngfb;
			$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : 'Share It';
			echo "\n", '<p><label for="', $this->get_field_id( 'title' ), '">Title (Leave Blank for No Title):</label>',
				'<input class="widefat" id="', $this->get_field_id( 'title' ), 
					'" name="', $this->get_field_name( 'title' ), 
					'" type="text" value="', $title, '" /></p>', "\n";
	
			foreach ( $ngfb->social_nice_names as $id => $name ) {
				echo '<p><label for="', $this->get_field_id( $id ), '">', 
					'<input id="', $this->get_field_id( $id ), 
					'" name="', $this->get_field_name( $id ), 
					'" value="1" type="checkbox" ';
				if ( ! empty( $instance[$id] ) )
					echo checked( 1 , $instance[$id] );
				echo ' /> ', $name;
				switch ( $id ) {
					case 'pinterest' :
						echo ' (not added on indexes)';
						break;
					case 'tumblr' :
						echo ' (shares link on indexes)';
						break;
				}
				echo '</label></p>', "\n";
			}
			unset( $name, $id );
		}
	}
	
	add_action( 'widgets_init', 
		create_function( '', 'return register_widget( "ngfbSocialButtonsWidget" );' ) );
}	
?>

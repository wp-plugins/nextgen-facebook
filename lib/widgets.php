<?php
/*
Copyright 2012 - Jean-Sebastien Morisset - http://surniaulula.com/

This script is free software; you can redistribute it and/or modify it under
the terms of the GNU General Public License as published by the Free Software
Foundation; either version 3 of the License, or (at your option) any later
version.

This script is distributed in the hope that it will be useful, but WITHOUT ANY
WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details at
http://www.gnu.org/licenses/.
*/

if ( ! class_exists( 'ngfbSocialButtonsWidget' ) ) {

	class ngfbSocialButtonsWidget extends WP_Widget {
	
		function ngfbSocialButtonsWidget() {
			$widget_ops = array( 'classname' => 'ngfb-widget-buttons',
				'description' => "The NextGEN Facebook OG social buttons widget is only visible on single posts, pages and attachments." );
			$this->WP_Widget( 'ngfb-widget-buttons', NGFB_FULLNAME, $widget_ops );
		}
	
		function widget( $args, $instance ) {
			global $ngfb;
	
			// only show widget on single posts, pages, and attachments
			if ( ! is_singular() ) return;
	
			// if using the Exclude Pages plugin, skip social buttons on those pages
			if ( is_page() && $ngfb->is_excluded() ) return;
	
			extract( $args );
			$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
			$sorted_ids = array();
			foreach ( $ngfb->social_options_prefix as $id => $prefix )
				if ( (int) $instance[$id] )
					$sorted_ids[$ngfb->options[$prefix.'_order'] . '-' . $id] = $id;
			ksort( $sorted_ids );
			echo $before_widget;
			if ( $title ) echo $before_title . $title . $after_title;
			echo $ngfb->get_buttons_html( $sorted_ids, array( 'css_id' => $args['widget_id'] ) );
			echo $after_widget;
		}
	
		function update( $new_instance, $old_instance ) {
			global $ngfb;
			$instance = $old_instance;
			$instance['title'] = strip_tags( $new_instance['title'] );
			foreach ( $ngfb->social_nice_names as $id => $name ) 
				$instance[$id] = (int) $new_instance[$id] ? 1 : 0;
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
	
			foreach ( $ngfb->social_nice_names as $id => $name )
				echo '<p><label for="', $this->get_field_id( $id ), '">', 
					'<input id="', $this->get_field_id( $id ), 
					'" name="', $this->get_field_name( $id ), 
					'" value="1" type="checkbox" ', checked( 1 , $instance[$id] ), 
					' /> ', $name, '</label></p>', "\n";
			unset( $name, $id );
		}
	}
	
	add_action( 'widgets_init', 
		create_function( '', 'return register_widget( "ngfbSocialButtonsWidget" );' ) );
}	
?>

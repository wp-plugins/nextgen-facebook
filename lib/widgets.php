<?php

class ngfbSocialButtonsWidget extends WP_Widget {

	var $social_full_names = array(
		'facebook' => 'Facebook', 
		'gplus' => 'Google+',
		'twitter' => 'Twitter',
		'linkedin' => 'Linkedin',
		'pinterest' => 'Pinterest',
		'stumbleupon' => 'StumbleUpon',
		'tumblr' => 'Tumblr',
	);

	var $social_options_prefix = array(
		'facebook' => 'fb', 
		'gplus' => 'gp',
		'twitter' => 'twitter',
		'linkedin' => 'linkedin',
		'pinterest' => 'pin',
		'stumbleupon' => 'stumble',
		'tumblr' => 'tumblr',
	);

	function ngfbSocialButtonsWidget() {
		$widget_ops = array( 'classname' => 'ngfb-widget-buttons',
			'description' => "The NextGEN Facebook OG social buttons widget is only visible on single posts, pages and attachments." );
		$this->WP_Widget( 'ngfb-widget-buttons', 'NGFB Social Buttons', $widget_ops );
	}

	function widget( $args, $instance ) {

		// only show widget on single posts, pages, and attachments
		if ( ! is_singular() ) return;

		// if using the Exclude Pages from Navigation plugin, skip social buttons on those pages
		if ( is_page() && ngfb_is_excluded() ) return;

		extract( $args );

		$options = ngfb_get_options();
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$sorted_ids = array();
		foreach ( $this->social_options_prefix as $id => $prefix )
			if ( (int) $instance[$id] )
				$sorted_ids[$options[$prefix.'_order'] . '-' . $id] = $id;
		ksort( $sorted_ids );

		echo $before_widget;
		if ( $title ) echo $before_title . $title . $after_title;
		echo ngfb_get_social_buttons( $sorted_ids );
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		foreach ( $this->social_full_names as $id => $name ) 
			$instance[$id] = (int) $new_instance[$id] ? 1 : 0;
		unset( $name, $id );
		return $instance;
	}

	function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : 'Share It';

		echo "\n", '<p><label for="', $this->get_field_id( 'title' ), '">Title (Leave Blank for No Title):</label>',
			'<input class="widefat" id="', $this->get_field_id( 'title' ), 
				'" name="', $this->get_field_name( 'title' ), 
				'" type="text" value="', $title, '" /></p>', "\n";

		foreach ( $this->social_full_names as $id => $name )
			echo '<p><label for="', $this->get_field_id( $id ), '">', 
				'<input id="', $this->get_field_id( $id ), 
				'" name="', $this->get_field_name( $id ), 
				'" value="1" type="checkbox" ', checked( 1 , $instance[$id] ), 
				' /> ', $name, '</label></p>', "\n";
		unset( $name, $id );
	}
}

add_action( 'widgets_init', create_function( '', 'return register_widget( "ngfbSocialButtonsWidget" );' ) );

?>

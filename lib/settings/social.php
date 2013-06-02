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

if ( ! class_exists( 'ngfbSettingsSocialSharing' ) && class_exists( 'ngfbAdmin' ) ) {

	class ngfbSettingsSocialSharing extends ngfbAdmin {

		protected $ngfb;
		protected $menu_id;
		protected $menu_name;
		protected $pagehook;
		protected $website = array();

		public function __construct( &$ngfb_plugin, $id, $name ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->lognew();
			$this->menu_id = $id;
			$this->menu_name = $name;
			$this->do_extend();
		}

		private function do_extend() {
			foreach ( $this->ngfb->website_libs as $id => $name ) {
				$classname = 'ngfbSettings' . preg_replace( '/ /', '', $name );
				$this->website[$id] = new $classname( $this->ngfb );
			}
			unset ( $id, $name );
		}

		protected function add_meta_boxes() {
			// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
			add_meta_box( $this->pagehook, 'Social Sharing Settings', array( &$this, 'show_metabox_social' ), $this->pagehook, 'normal' );

			$col = 0;
			$row = 0;
			foreach ( $this->ngfb->website_libs as $id => $name ) {
				$col = $col == 1 ? 2 : 1;
				$row = $col == 1 ? $row + 1 : $row;
				$pos_id = 'website-row-' . $row . '-col-' . $col;
				$name = $name == 'GooglePlus' ? 'Google+' : $name;
				add_meta_box( $this->pagehook . '_' . $id, $name, array( &$this->website[$id], 'show_metabox_website' ), $this->pagehook, $pos_id );
				add_filter( 'postbox_classes_' . $this->pagehook . '_' . $this->pagehook . '_' . $id, array( &$this, 'add_class_postbox_website' ) );
			}
			$this->ngfb->user->collapse_metaboxes( $this->pagehook, array_keys( $this->ngfb->website_libs ) );
		}

		public function add_class_postbox_website( $classes ) {
			array_push( $classes, 'postbox_website' );
			return $classes;
		}

		public function show_metabox_website() {
			echo '<table class="ngfb-settings">', "\n";
			foreach ( $this->get_rows() as $row ) echo '<tr>', $row, '</tr>';
			echo '</table>', "\n";
		}

		public function show_metabox_social() {
			?>
			<table class="ngfb-settings">
			<tr>
				<td><p><?php echo $this->ngfb->fullname; ?> uses the "ngfb-buttons" class to wrap all social buttons, and each button has it's own individual class name as well. 
				See the <b><a href="http://wordpress.org/extend/plugins/nextgen-facebook/other_notes/" target="_blank">Other Notes webpage for stylesheet examples</a></b> -- 
				including how to hide the social buttons for specific Posts, Pages, categories, tags, etc. 
				<b><?php echo $this->ngfb->fullname; ?> does not come with it's own CSS stylesheet</b> -- you must add CSS styling information to your theme's existing stylesheet, 
				or use a plugin like <a href="http://wordpress.org/extend/plugins/lazyest-stylesheet/">Lazyest Stylesheet</a> (for example) to create an additional stylesheet.</p>
				
				<p>Each of the following social buttons can also be enabled via the "<?php echo ngfbWidgetSocialSharing::$fullname; ?>" 
				widget as well (<a href="widgets.php">see the widgets admin webpage</a>).</p></td>
			</tr>
			</table>
			<table class="ngfb-settings">
			<tr>
				<th>Include on Index Webpages</th>
				<td><?php echo $this->ngfb->admin->form->get_checkbox( 'buttons_on_index' ); ?></td>
				<td><p>Include social sharing buttons (that are enabled) on each entry of index webpages (index, archives, author, etc.).</p></td>
			</tr>
			<tr>
				<th>Location in Excerpt Text</th>
				<td><?php echo $this->ngfb->admin->form->get_select( 'buttons_location_the_excerpt', array( 'top' => 'Top', 'bottom' => 'Bottom' ) ); ?></td>
				<td><p>The social sharing button(s) must also be enabled below.</p></td>
			</tr>
			<tr>
				<th>Location in Content Text</th>
				<td><?php echo $this->ngfb->admin->form->get_select( 'buttons_location_the_content', array( 'top' => 'Top', 'bottom' => 'Bottom' ) ); ?></td>
				<td><p>The social sharing button(s) must also be enabled below.</p></td>
			</tr>
			</table>
			<?php
		}

	}
}

?>

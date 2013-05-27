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
				<td><p>Add the social sharing buttons (that are enabled) to each entry on index webpages (index, archives, author, etc.).</p></td>
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
			<table class="ngfb-settings">
			<?php
				$col = 0;
				$box = -1;	// a "box" is a collection of rows from one website class
				$section = -1;	// a "section" is a row of several boxes
				$max_col = 2;
				$rows = array();
				foreach ( $this->ngfb->website_libs as $id => $name ) {
					$box++;				// increment the website box number (first box is 0)
					$col = $box % $max_col;		// determine column number based on the box number
					if ( $col == 0 ) $section++;	// increment section if we're on column 0
					foreach ( $this->website[$id]->get_rows() as $num => $row ) {
						// avoids undefined offset error
						if ( empty( $rows[$section][$num] ) )
							$rows[$section][$num] = '';
						$rows[$section][$num] .= $row;
					}
				}
				unset ( $id, $name );
				foreach ( $rows as $section )
					foreach ( $section as $row )
						echo "<tr>", $row, "</tr>\n";
			?>
			</table>
			<?php
		}

	}
}

?>

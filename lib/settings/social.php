<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
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
			$this->ngfb->debug->mark();
			$this->menu_id = $id;
			$this->menu_name = $name;
			$this->setup_vars();
		}

		private function setup_vars() {
			foreach ( $this->ngfb->website_libs as $id => $name ) {
				$classname = 'ngfbSettings' . preg_replace( '/ /', '', $name );
				$this->website[$id] = new $classname( $this->ngfb );
			}
			unset ( $id, $name );

			// use the custom css file, or a default one if it doesn't exist
			$css_file = file_exists( $this->ngfb->style->buttons_css_file ) ?
				$this->ngfb->style->buttons_css_file :  NGFB_PLUGINDIR . 'css/social-buttons.css';
			if ( ! $fh = @fopen( $css_file, 'rb' ) )
				$this->ngfb->notices->err( 'Failed to open ' . $css_file . ' for reading.' );
			else {
				$this->ngfb->options['buttons_css_data'] = fread( $fh, filesize( $css_file ) );
				$this->ngfb->debug->log( 'read css from file ' . $css_file );
				fclose( $fh );
			}
		}

		protected function add_meta_boxes() {
			// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
			add_meta_box( $this->pagehook . '_social', 'Social Buttons', array( &$this, 'show_metabox_social' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_style', 'StyleSheet', array( &$this, 'show_metabox_style' ), $this->pagehook, 'bottom' );

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
			$reset_ids = array_diff( array_keys( $this->ngfb->website_libs ), array( 'facebook', 'gplus' ) );
			$reset_ids[] = 'style';
			$this->ngfb->user->reset_metaboxes( $this->pagehook, $reset_ids );
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
				<td colspan="3"><p>The following social buttons can be added to the content, excerpt, 
				and / or enabled within the "<?php echo ngfbWidgetSocialSharing::$fullname; ?>" widget as well 
				(<a href="<?php echo get_admin_url( null, 'widgets.php' ); ?>">see the widgets admin webpage</a>).</p></td>
			</tr>
			<tr>
				<th>Include on Index Webpages</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'buttons_on_index' ); ?></td>
				<td><p>Add the following (enabled) social sharing buttons on each entry of an index webpage (homepage, category, 
				archive, etc.). By Default, social sharing buttons are not included on index webpages (default is unchecked).</p></td>
			</tr>
			<tr>
				<th>Location in Excerpt Text</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_select( 'buttons_location_the_excerpt', array( 'top' => 'Top', 'bottom' => 'Bottom' ) ); ?></td>
				<td><p>The social sharing button(s) must also be enabled below.</p></td>
			</tr>
			<tr>
				<th>Location in Content Text</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_select( 'buttons_location_the_content', array( 'top' => 'Top', 'bottom' => 'Bottom' ) ); ?></td>
				<td><p>The social sharing button(s) must also be enabled below.</p></td>
			</tr>
			</table>
			<?php
		}

		public function show_metabox_style() {
			?>
			<table class="ngfb-settings">
			<tr>
				<td colspan="3"><p><?php echo $this->ngfb->fullname; ?> uses the '<em>ngfb-buttons</em>' class to wrap all social buttons, 
				and each button has it's own individual class name as well. Refer to the <a href="http://wordpress.org/extend/plugins/nextgen-facebook/other_notes/" 
				target="_blank">Other Notes</a> webpage for additional stylesheet information, including how to hide the social buttons 
				for specific Posts, Pages, categories, tags, etc.</p></td>
			</tr>
			<tr>
				<th class="short">Use Social StyleSheet</th>
				<td><?php echo $this->ngfb->admin->form->get_checkbox( 'buttons_link_css' ); ?></td>
				<td>
					<p>Add the following stylesheet to all webpages (default is unchecked).</p>
				</td>
			</tr>
			<tr>
				<th class="short">StyleSheet Editor</th>
				<td colspan="2">
					<?php echo $this->ngfb->admin->form->get_textarea( 'buttons_css_data', 'large' ); ?>
					<p>The stylesheet URL is <?php echo $this->ngfb->style->buttons_css_url; ?>.</p>
				</td>
			</tr>
			</table>
			<?php
		}

	}
}

?>

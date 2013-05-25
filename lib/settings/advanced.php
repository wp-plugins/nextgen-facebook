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

if ( ! class_exists( 'ngfbSettingsAdvanced' ) && class_exists( 'ngfbAdmin' ) ) {

	class ngfbSettingsAdvanced extends ngfbAdmin {

		protected $ngfb;
		protected $menu_id;
		protected $menu_name;
		protected $pagehook;

		// executed by ngfbSettingsAdvancedPro() as well
		public function __construct( &$ngfb_plugin, $id, $name ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->lognew();
			$this->menu_id = $id;
			$this->menu_name = $name;
		}

		protected function add_meta_boxes() {
			// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
			add_meta_box( $this->pagehook, 'Advanced Settings', array( &$this, 'show_metabox_advanced' ), $this->pagehook, 'normal' );
		}

		public function show_metabox_advanced() {
			?>
			<table class="ngfb-settings">
			<tr>
				<th>Reset on Activate</th>
				<td><?php echo $this->ngfb->admin->form->get_checkbox( 'ngfb_reset' ); ?></td>
				<td><p>Check this option if you would like to reset the <?php echo $this->ngfb->fullname; ?> settings to their default values 
				<u>when you deactivate, and then reactivate the plugin</u>.</p></td>
			</tr>
			<tr>
				<th>Add Hidden Debug Info</th>
				<td><?php echo $this->ngfb->admin->form->get_checkbox( 'ngfb_debug' ); ?></td>
				<td><p>Include hidden debug information with the Open Graph meta tags.</p></td>
			</tr>
			<tr>
				<th>Enable Shortcode(s)</th>
				<td><?php echo $this->ngfb->admin->form->get_checkbox( 'ngfb_enable_shortcode' ); ?></td>
				<td><p>Enable the <?php echo $this->ngfb->fullname; ?> content shortcode(s) (default is unchecked).</p></td>
			</tr>
			<tr>
				<th>Ignore Small Images</th>
				<td><?php echo $this->ngfb->admin->form->get_checkbox( 'ngfb_skip_small_img' ); ?></td>
				<td><p><?php echo $this->ngfb->fullname; ?> will attempt to include images from <code>&lt;img/&gt;</code> HTML tags it finds in the content 
				(provided the "Maximim Number of Images" chosen has not been reached). The <code>&lt;img/&gt;</code> HTML tags must have a width and height attribute, 
				and their size must be equal to or larger than the Image Size Name you've selected. You can uncheck this option to include smaller images from the content, 
				or refer to the <a href="http://wordpress.org/extend/plugins/nextgen-facebook/faq/"><?php echo $this->ngfb->fullname; ?> FAQ</a> webpage for additional solutions.</p></td>
			</tr>
			<tr>
				<th>Apply Title Filters</th>
				<td><?php echo $this->ngfb->admin->form->get_checkbox( 'ngfb_filter_title' ); ?></td>
				<td><p>Apply the standard WordPress filters to render the title (default is checked).</p></td>
			</tr>
			<tr>
				<th>Apply Content Filters</th>
				<td><?php echo $this->ngfb->admin->form->get_checkbox( 'ngfb_filter_content' ); ?></td>
				<td><p>Apply the standard WordPress filters to render the content (default is checked).</p></td>
			</tr>
			<tr>
				<th>Apply Excerpt Filters</th>
				<td><?php echo $this->ngfb->admin->form->get_checkbox( 'ngfb_filter_excerpt' ); ?></td>
				<td><p>Apply the standard WordPress filters to render the excerpt (default is unchecked).
				Check this option if you use shortcodes in your excerpt, for example.</p></td>
			</tr>
			<tr>
				<th>Object Cache Expiry</th>
				<td><?php echo $this->ngfb->admin->form->get_input( 'ngfb_object_cache_exp', 'short' ); ?> Seconds</td>
				<td><p><?php echo $this->ngfb->fullname; ?> saves the rendered (filtered) content text to a non-presistant cache (wp_cache), 
				and the completed Open Graph meta tags and social buttons to a persistant (transient) cache. Changes to the website content and 
				webpages will not be reflected in the Open Graph and NGFB social sharing buttons until the object cache has expired. 
				Decrease this value if your content is often revised after publishing, or increase it to improve performance. 
				The default is 60 seconds, and the minimum value is 1 second (such a low value is not recommended).</p></td>
			</tr>
			<tr>
				<th>Goo.gl Simple API Access Key</th>
				<td></td>
				<td><?php echo $this->ngfb->admin->form->get_input( 'ngfb_googl_api_key', 'wide' ); ?>
				<p>The "Google URL Shortener API Key" for this website. If you don't already have one, visit Google's 
				<a href="https://developers.google.com/url-shortener/v1/getting_started#APIKey" target="_blank">acquiring and using an API Key</a> documentation, 
				and follow the directions to acquire your <em>Simple API Access Key</em>.</p></td>
			</tr>
			<?php foreach ( $this->get_more_rows() as $row ) echo '<tr>' . $row . '</tr>'; ?>
			</table>
			<?php
		}

		protected function get_more_rows() {
			return array(
				'<th>File Cache Expiry</th><td></td><td>' . $this->ngfb->admin->form->get_hidden( 'ngfb_file_cache_hrs' ) . $this->ngfb->pro_msg . '</td>',
				'<th>Verify SSL Certificates</th><td></td><td>' . $this->ngfb->admin->form->get_hidden( 'ngfb_verify_certs' ) . $this->ngfb->pro_msg . '</td>',
			);
		}
	}
}

?>

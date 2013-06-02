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
			add_meta_box( $this->pagehook . '_plugin', 'Plugin Settings', array( &$this, 'show_metabox_plugin' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_cache', 'Cache Settings', array( &$this, 'show_metabox_cache' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_rewrite', 'Rewrite Settings', array( &$this, 'show_metabox_rewrite' ), $this->pagehook, 'normal' );
		}

		public function show_metabox_plugin() {
			?>
			<table class="ngfb-settings">
			<tr>
				<th>Reset on Activate</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'ngfb_reset' ); ?></td>
				<td><p>Check this option if you would like to reset the <?php echo $this->ngfb->fullname; ?> 
					settings to their default values when you <em>deactivate</em>, and then 
					<em>re-activate</em> the plugin (default is unchecked).</p></td>
			</tr>
			<tr>
				<th>Preserve on Uninstall</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'ngfb_preserve' ); ?></td>
				<td><p>Check this option if you would like to preserve all <?php echo $this->ngfb->fullname; ?> 
					settings when you <em>uninstall</em> the plugin (default is unchecked).</p></td>
			</tr>
			<tr>
				<th>Add Hidden Debug Info</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'ngfb_debug' ); ?></td>
				<td><p>Include hidden debug information with the Open Graph meta tags (default is unchecked).</p></td>
			</tr>
			<tr>
				<th>Enable Shortcode(s)</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'ngfb_enable_shortcode' ); ?></td>
				<td><p>Enable the <?php echo $this->ngfb->fullname; ?> content shortcode(s) (default is unchecked).</p></td>
			</tr>
			<tr>
				<th>Ignore Small Images</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'ngfb_skip_small_img' ); ?></td>
				<td><p><?php echo $this->ngfb->fullname; ?> will attempt to include images from <code>&lt;img/&gt;</code> HTML tags it finds in the content 
				(provided the "Maximim Number of Images" chosen has not been reached). The <code>&lt;img/&gt;</code> HTML tags must have a width and height attribute, 
				and their size must be equal to or larger than the Image Size Name you've selected. You can uncheck this option to include smaller images from the content, 
				or refer to the <a href="http://wordpress.org/extend/plugins/nextgen-facebook/faq/"><?php echo $this->ngfb->fullname; ?> FAQ</a> webpage for additional solutions.</p></td>
			</tr>
			<tr>
				<th>Apply Title Filters</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'ngfb_filter_title' ); ?></td>
				<td><p>Apply the standard WordPress filters to render the title (default is checked).</p></td>
			</tr>
			<tr>
				<th>Apply Content Filters</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'ngfb_filter_content' ); ?></td>
				<td><p>Apply the standard WordPress filters to render the content (default is checked).</p></td>
			</tr>
			<tr>
				<th>Apply Excerpt Filters</th>
				<td class="second"><?php echo $this->ngfb->admin->form->get_checkbox( 'ngfb_filter_excerpt' ); ?></td>
				<td><p>Apply the standard WordPress filters to render the excerpt (default is unchecked).
				Check this option if you use shortcodes in your excerpt, for example.</p></td>
			</tr>
			<?php foreach ( $this->get_more_plugin() as $row ) echo '<tr>' . $row . '</tr>'; ?>
			</table>
			<?php
		}

		protected function get_more_plugin() {
			return array(
				'<td colspan="3" align="center">' . $this->ngfb->msgs['pro_feature'] . '</td>',
				'<th>Purchase Transaction ID</th><td colspan="2" class="blank">' .  $this->ngfb->admin->form->get_hidden( 'ngfb_pro_tid' ) . '
				<p>In order for the ' . $this->ngfb->fullname . ' plugin to authenticate itself for future updates, enter the transaction ID 
				you receive by email after your purchase.</p>
				</td>',
			);
		}

		public function show_metabox_rewrite() {
			?>
			<table class="ngfb-settings">
			<tr>
				<th>Goo.gl Simple API Access Key</th>
				<td colspan="2"><?php echo $this->ngfb->admin->form->get_input( 'ngfb_googl_api_key', 'wide' ); ?>
				<p>The "Google URL Shortener API Key" for this website. If you don't already have one, visit Google's 
				<a href="https://developers.google.com/url-shortener/v1/getting_started#APIKey" target="_blank">acquiring and using an API Key</a> documentation, 
				and follow the directions to acquire your <em>Simple API Access Key</em>.</p></td>
			</tr>
			<?php foreach ( $this->get_more_rewrite() as $row ) echo '<tr>' . $row . '</tr>'; ?>
			</table>
			<?php
		}

		protected function get_more_rewrite() {
			return array(
				'<td colspan="2" align="center">' . $this->ngfb->msgs['pro_feature'] . '</td>',

				'<th>Static Content URL(s)</th><td class="blank">' .  $this->ngfb->admin->form->get_hidden( 'ngfb_cdn_urls' ) . '
				<p>Rewrite image URLs in the Open Graph meta tags, image URLs shared by social buttons (Pinterest and Tumblr), 
				and cached social media files (see the "File Cache Expiry" option above).</p>
				<p>As an example, <u>http://mydomain.com/wp-content/gallery/test/image.jpg</u> could be rewritten as 
				<u>http://static.mydomain.com/wp-content/gallery/test/image.jpg</u>. The Static Content URL setting for this example would be 
				<em>http://static.mydomain.com/</em>. You can enter multiple comma-delimited Static Content URLs, use numbered wildcards like 
				<em>http://cdn%3%.static.mydomain.com/</em> for example (which expands to cdn1, cdn2, and cdn3), or 
				<em>http://cdn%4-6%.static.mydomain.com/</em> (which expands to cdn4, cdn5, and cdn6). 
				If wildcards or multiple Static Content URLs are entered, one URL in the range is chosen at random for each rewrite.</p>
				</td>',

				'<th>Include Folders</th><td class="blank">' .  $this->ngfb->admin->form->get_hidden( 'ngfb_cdn_folders' ) . '
				<p>A comma delimited list of patterns to match. These patterns must be present in the URL for the rewrite to take place 
				(the default value is "<em>wp-content, wp-includes</em>").</p>
				</td>',

				'<th>Exclude Patterns</th><td class="blank">' .  $this->ngfb->admin->form->get_hidden( 'ngfb_cdn_excl' ) . '
				<p>A comma delimited list of patterns to match. If these patterns are found in the URL, the rewrite will be skipped 
				(the default value is blank). If you are caching social website images and JavaScript (see File Cache Expiry option above), 
				the URLs to this cached content will be rewritten as well. To exclude the NGFB cache folder from being rewritten, 
				use "<em>/nextgen-facebook/cache/</em>" as a value here.</p>
				</td>',

				'<th>Not when Using HTTPS</th><td class="blank">' .  $this->ngfb->admin->form->get_hidden( 'ngfb_cdn_not_https' ) . '
				<p>Do not rewrite URLs when using HTTPS.</p></td>',

				'<th>www is Optional</th><td class="blank">' .  $this->ngfb->admin->form->get_hidden( 'ngfb_cdn_www_opt' ) . '
				<p>Any www hostname prefix in the WordPress site URL is optional (default is checked).</p></td>',
			);
		}

		public function show_metabox_cache() {
			?>
			<table class="ngfb-settings">
			<tr>
				<th>Object Cache Expiry</th>
				<td nowrap><?php echo $this->ngfb->admin->form->get_input( 'ngfb_object_cache_exp', 'short' ); ?> Seconds</td>
				<td><p><?php echo $this->ngfb->fullname; ?> saves the rendered (filtered) content text to a non-presistant cache (wp_cache), 
				and the completed Open Graph meta tags and social buttons to a persistant (transient) cache. Changes to the website content and 
				webpages will not be reflected in the Open Graph and NGFB social sharing buttons until the object cache has expired. 
				Decrease this value if your content is often revised after publishing, or increase it to improve performance. 
				The default is 60 seconds, and the minimum value is 1 second (such a low value is not recommended).</p></td>
			</tr>
			<?php foreach ( $this->get_more_cache() as $row ) echo '<tr>' . $row . '</tr>'; ?>
			</table>
			<?php
		}

		protected function get_more_cache() {
			return array(
				'<td colspan="3" align="center">' . $this->ngfb->msgs['pro_feature'] . '</td>',

				'<th>File Cache Expiry</th><td colspan="2" class="blank">' .  $this->ngfb->admin->form->get_hidden( 'ngfb_file_cache_hrs' ) . '
				<p>NGFB can save social website button images and JavaScript to a cache folder, and provide URLs to these files instead of the originals.</p>
				</td>',

				'<th>Verify SSL Certificates</th><td colspan="2" class="blank">' .  $this->ngfb->admin->form->get_hidden( 'ngfb_verify_certs' ) . '
				<p>Verify the peer SSL certificate when fetching content to be cached by HTTPS.</p>
				</td>',
			);
		}

	}
}

?>

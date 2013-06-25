<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
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
			$this->ngfb->debug->mark();
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
				The <code>&lt;img/&gt;</code> HTML tags must have a width and height attribute, and their size must be equal to or larger than the 
				Image Dimensions you've chosen (on the General settings page). You can uncheck this option to include smaller images from the content, 
				or refer to the <a href="http://wordpress.org/extend/plugins/nextgen-facebook/faq/">FAQ</a> for additional solutions.</p></td>
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
				'<th>Purchase Transaction ID</th><td class="second">' . $this->ngfb->admin->form->get_input( 'ngfb_pro_tid' ) . '</td><td class="blank">
				<p>After purchasing of the Pro version, an email will be sent to you with installation instructions and a unique Transaction ID. 
				Enter your unique Transaction ID here, and after saving the changes, an update for "' . $this->ngfb->fullname . '" will appear on the 
				<a href="' . get_admin_url( null, 'update-core.php' ) . '">WordPress Updates</a> page. 
				Update the "' . $this->ngfb->fullname . '" plugin to download and activate the new Pro version.</p></td>',
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
				<p>NGFB can save social sharing images and JavaScript to a cache folder, providing URLs to these files instead of the originals. 
				If your hosting infrastructure performs reasonably well, this option can improve page load times significantly.</p>
				</td>',

				'<th>Verify SSL Certificates</th><td colspan="2" class="blank">' .  $this->ngfb->admin->form->get_hidden( 'ngfb_verify_certs' ) . '
				<p>An option to enable verification of peer SSL certificates when fetching content to be cached using HTTPS.</p>
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
				</td>',

				'<th>Include Folders</th><td class="blank">' .  $this->ngfb->admin->form->get_hidden( 'ngfb_cdn_folders' ) . '
				<p>A comma delimited list of patterns to match. These patterns must be present in the URL for the rewrite to take place 
				(the default value is "<em>wp-content, wp-includes</em>").</p>
				</td>',

				'<th>Exclude Patterns</th><td class="blank">' .  $this->ngfb->admin->form->get_hidden( 'ngfb_cdn_excl' ) . '
				<p>A comma delimited list of patterns to match. If these patterns are found in the URL, the rewrite will be skipped 
				(the default value is blank).</p>
				</td>',

				'<th>Not when Using HTTPS</th><td class="blank">' .  $this->ngfb->admin->form->get_hidden( 'ngfb_cdn_not_https' ) . '
				<p>Skip rewriting URLs when using HTTPS (useful if your CDN provider does not offer HTTPS, for example).</p></td>',

				'<th>www is Optional</th><td class="blank">' .  $this->ngfb->admin->form->get_hidden( 'ngfb_cdn_www_opt' ) . '
				<p>The www hostname prefix (if any) in the WordPress site URL is optional (default is checked).</p></td>',
			);
		}

	}
}

?>

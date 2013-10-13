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

		protected $p;
		protected $menu_id;
		protected $menu_name;
		protected $pagehook;

		// executed by ngfbSettingsAdvancedPro() as well
		public function __construct( &$plugin, $id, $name ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
			$this->menu_id = $id;
			$this->menu_name = $name;
		}

		protected function add_meta_boxes() {
			// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
			add_meta_box( $this->pagehook . '_plugin', 'Plugin Settings', array( &$this, 'show_metabox_plugin' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_contact', 'Profile Contact Methods', array( &$this, 'show_metabox_contact' ), $this->pagehook, 'normal' );
			add_meta_box( $this->pagehook . '_taglist', 'Meta Tag List', array( &$this, 'show_metabox_taglist' ), $this->pagehook, 'normal' );
		}

		public function show_metabox_plugin() {
			$show_tabs = array( 
				'activation' => 'Activate and Update',
				'content' => 'Content and Filters',
				'cache' => 'File and Object Cache',
				'shorten' => 'URL Shortening',
				'rewrite' => 'URL Rewrite',
			);
			$tab_rows = array();
			foreach ( $show_tabs as $key => $title )
				$tab_rows[$key] = $this->get_rows( $key );
			$this->p->util->do_tabs( 'plugin', $show_tabs, $tab_rows );
		}

		public function show_metabox_contact() {
			echo '<table class="ngfb-settings" style="padding-bottom:0"><tr><td>
			<p>The following options allow you to customize the contact field names and labels shown on the <a href="'.get_admin_url( null, 'profile.php' ).'">user profile page</a>.
			'.$this->p->fullname.' uses the Facebook, Google+ and Twitter contact field values for Open Graph and Twitter Card meta tags (along with the Twitter social sharing button).
			<strong>You should not modify the <em>Contact Field Name</em> unless you have a very good reason to do so.</strong>
			The <em>Profile Contact Label</em> is for display purposes and may be changed as you wish.
			Although contact methods may be shown on user profile pages, your theme is responsible for displaying their values in the appropriate locations
			(see <a href="http://codex.wordpress.org/Function_Reference/get_the_author_meta" target="_blank">get_the_author_meta()</a> for examples).</p>
			</td></tr></table>';
			$show_tabs = array( 
				'custom' => 'Custom Contacts',
				'builtin' => 'Built-In Contacts',
			);
			$tab_rows = array();
			foreach ( $show_tabs as $key => $title )
				$tab_rows[$key] = $this->get_rows( $key );
			$this->p->util->do_tabs( 'cm', $show_tabs, $tab_rows );
		}

		protected function get_pre_activation() {
			return array(
				$this->p->util->th( 'Authentication ID', 'highlight', null, '
				After purchasing of the Pro version, an email will be sent to you with an Authentication ID and installation instructions.
				Enter your unique Authentication ID here, and after saving the changes, an update for ' . $this->p->fullname . ' 
				will appear on the <a href="' . get_admin_url( null, 'update-core.php' ) . '">WordPress Updates</a> page. 
				Update the \'' . $this->p->fullname . '\' plugin to download and activate the new Pro version.' ) .
				'<td class="blank">' . $this->p->admin->form->get_input( 'ngfb_pro_tid' ) . '</td>',
			);
		}

		protected function get_more_content() {
			$add_to_checkboxes = '';
			foreach ( get_post_types( array( 'show_ui' => true, 'public' => true ), 'objects' ) as $post_type )
				$add_to_checkboxes .= '<p>' . $this->p->admin->form->get_hidden( 'ngfb_add_to_'.$post_type->name ) . 
					$this->p->admin->form->get_fake_checkbox( $this->p->options['ngfb_add_to_'.$post_type->name] ) . ' ' . 
					$post_type->label . '</p>';

			return array(
				'<td colspan="2" align="center">' . $this->p->msg->get( 'pro_feature' ) . '</td>',

				$this->p->util->th( 'Show Custom Settings on', null, null, 
				'The Custom Settings metabox, which allows you to enter custom Open Graph values (among other options), 
				is available on the Posts, Pages, Media, and most custom post type admin pages by default. 
				If your theme (or another plugin) supports additional custom post types, and you would like to 
				<em>exclude</em> the Custom Settings metabox from these admin pages, uncheck the appropriate options here.' ) . 
				'<td class="blank">' . $add_to_checkboxes . '</td>',
			);
		}

		protected function get_more_cache() {
			return array(
				'<td colspan="2" align="center">' . $this->p->msg->get( 'pro_feature' ) . '</td>',

				$this->p->util->th( 'File Cache Expiry', 'highlight', null, 
				$this->p->fullname . ' can save social sharing images and JavaScript to a cache folder, 
				providing URLs to these cached files instead of the originals. 
				A value of \'0\' hours (the default) disables this feature. 
				If your hosting infrastructure performs reasonably well, this option can improve page load times significantly.
				All social sharing images and javascripts will be cached, except for the Facebook JavaScript SDK, which does not work correctly when cached. 
				The cached files are served from the ' . NGFB_CACHEURL . ' folder.' ) .
				'<td class="blank">' . $this->p->admin->form->get_hidden( 'ngfb_file_cache_hrs' ) . 
					$this->p->options['ngfb_file_cache_hrs'] . ' Hours</td>',

				$this->p->util->th( 'Verify SSL Certificates', null, null, 
				'Enable verification of peer SSL certificates when fetching content to be cached using HTTPS. 
				The PHP \'curl\' function will use the ' .  NGFB_CURL_CAINFO . ' certificate file by default. 
				You may want define the NGFB_CURL_CAINFO constant in your wp-config.php file to use an 
				alternate certificate file (see the constants.txt file in the plugin folder for additional information).' ) . 
				'<td class="blank">' . $this->p->admin->form->get_hidden( 'ngfb_verify_certs' ) . 
					$this->p->admin->form->get_fake_checkbox( $this->p->options['ngfb_verify_certs'] ) . '</td>',
			);
		}

		protected function get_more_rewrite() {
			return array(
				'<td colspan="2" align="center">' . $this->p->msg->get( 'pro_feature' ) . '</td>',

				$this->p->util->th( 'Static Content URL(s)', 'highlight', null, 
				'Rewrite image URLs in the Open Graph meta tags, encoded image URLs shared by social buttons (Pinterest and Tumblr), 
				and cached social media files. Leave this option blank to disable the rewriting feature (default is disabled).
				Wildcarding and multiple CDN hostnames are supported -- see the 
				<a href="http://wordpress.org/plugins/nextgen-facebook/other_notes/" target="_blank">Other Notes</a> for 
				more information and examples.' ) .
				'<td class="blank">' .  $this->p->admin->form->get_hidden( 'ngfb_cdn_urls' ) . 
					$this->p->options['ngfb_cdn_urls'] . '</td>',

				$this->p->util->th( 'Include Folders', null, null, '
				A comma delimited list of patterns to match. These patterns must be present in the URL for the rewrite to take place 
				(the default value is "<em>wp-content, wp-includes</em>").') .
				'<td class="blank">' .  $this->p->admin->form->get_hidden( 'ngfb_cdn_folders' ) . 
					$this->p->options['ngfb_cdn_folders'] . '</td>',

				$this->p->util->th( 'Exclude Patterns', null, null,
				'A comma delimited list of patterns to match. If these patterns are found in the URL, the rewrite will be skipped (the default value is blank).
				If you are caching social website images and JavaScript (see <em>File Cache Expiry</em> option above), 
				the URLs to this cached content will be rewritten as well. To exclude the ' . $this->p->fullname . ' cache folder 
				from being rewritten, use \'<em>/nextgen-facebook/cache/</em>\' as a value here.' ) .
				'<td class="blank">' .  $this->p->admin->form->get_hidden( 'ngfb_cdn_excl' ) . 
					$this->p->options['ngfb_cdn_excl'] . '</td>',

				$this->p->util->th( 'Not when Using HTTPS', null, null, 
				'Skip rewriting URLs when using HTTPS (useful if your CDN provider does not offer HTTPS, for example).' ) .
				'<td class="blank">' .  $this->p->admin->form->get_hidden( 'ngfb_cdn_not_https' ) . 
					$this->p->admin->form->get_fake_checkbox( $this->p->options['ngfb_cdn_not_https'] ) . '</td>',

				$this->p->util->th( 'www is Optional', null, null, 
				'The www hostname prefix (if any) in the WordPress site URL is optional (default is checked).' ) .
				'<td class="blank">' .  $this->p->admin->form->get_hidden( 'ngfb_cdn_www_opt' ) . 
					$this->p->admin->form->get_fake_checkbox( $this->p->options['ngfb_cdn_www_opt'] ) . '</td>',
			);
		}

		protected function get_rows( $id ) {
			$ret = array();
			switch ( $id ) {

				case 'custom' :
					if ( $this->p->is_avail['aop'] == false )
						$ret[] = '<td colspan="4" align="center">'.$this->p->msg->get( 'pro_feature' ).'</td>';

					$ret[] = '<td></td>' .
					$this->p->util->th( 'Show', 'left checkbox' ) .
					$this->p->util->th( 'Contact Field Name', 'left medium', null,
					'You should not modify the contact field names unless you have a specific reason to do so.
					As an example, to match the contact field name of a theme or other plugin, you might change \'gplus\' to \'googleplus\'.
					If you change the Facebook or Google+ field names, please make sure to update the Open Graph 
					<em>Author Profile URL</em> and Google <em>Author Link URL</em> options on the ' .
					$this->p->util->get_admin_url( 'general', 'General Settings' ) . ' page.' ) .
					$this->p->util->th( 'Profile Contact Label', 'left wide' );

					$social_prefix = $this->p->social_prefix;
					ksort( $social_prefix );
					foreach ( $social_prefix as $id => $opt_prefix ) {
						$cm_opt = 'ngfb_cm_'.$opt_prefix.'_';
						$th_val = empty( $this->p->website_libs[$id] ) ? ucfirst( $id ) : $this->p->website_libs[$id];
						$th_val = $th_val == 'GooglePlus' ? 'Google+' : $th_val;
						// not all social websites have a contact method field
						if ( array_key_exists( $cm_opt.'enabled', $this->p->options ) ) {
							if ( $this->p->is_avail['aop'] == true ) {
								$ret[] = $this->p->util->th( $th_val ) .
								'<td class="checkbox">' . $this->p->admin->form->get_checkbox( $cm_opt.'enabled' ) . '</td>' .
								'<td>' . $this->p->admin->form->get_input( $cm_opt.'name' ) . '</td>' .
								'<td>' . $this->p->admin->form->get_input( $cm_opt.'label' ) . '</td>';
							} else {
								$ret[] = $this->p->util->th( $th_val ) .
								'<td class="blank checkbox">' . $this->p->admin->form->get_hidden( $cm_opt.'enabled' ) . 
									$this->p->admin->form->get_fake_checkbox( $this->p->options[$cm_opt.'enabled'] ) . '</td>' .
								'<td class="blank">' . $this->p->admin->form->get_hidden( $cm_opt.'name' ) .
									$this->p->options[$cm_opt.'name'] . '</td>' .
								'<td class="blank">' . $this->p->admin->form->get_hidden( $cm_opt.'label' ) .
									$this->p->options[$cm_opt.'label'] . '</td>';
							}
						}
					
					}
					break;

				case 'builtin' :
					if ( $this->p->is_avail['aop'] == false )
						$ret[] = '<td colspan="4" align="center">'.$this->p->msg->get( 'pro_feature' ).'</td>';

					$ret[] = '<td></td>' .
					$this->p->util->th( 'Show', 'left checkbox' ) .
					$this->p->util->th( 'Contact Field Name', 'left medium', null, 
					'The built-in WordPress contact field names cannot be changed.' ) .
					$this->p->util->th( 'Profile Contact Label', 'left wide' );

					$wp_contacts = $this->p->wp_contacts;
					ksort( $wp_contacts );
					foreach ( $wp_contacts as $id => $th_val ) {
						$cm_opt = 'wp_cm_'.$id.'_';
						if ( array_key_exists( $cm_opt.'enabled', $this->p->options ) ) {
							if ( $this->p->is_avail['aop'] == true ) {
								$ret[] = $this->p->util->th( $th_val ) .
								'<td class="checkbox">' . $this->p->admin->form->get_checkbox( $cm_opt.'enabled' ) . '</td>' .
								'<td>' . $this->p->admin->form->get_fake_input( $id ) . '</td>' .
								'<td>' . $this->p->admin->form->get_input( $cm_opt.'label' ) . '</td>';
							} else {
								$ret[] = $this->p->util->th( $th_val ) .
								'<td class="blank checkbox">' . $this->p->admin->form->get_hidden( $cm_opt.'enabled' ) . 
									$this->p->admin->form->get_fake_checkbox( $this->p->options[$cm_opt.'enabled'] ) . '</td>' .
								'<td>' . $this->p->admin->form->get_fake_input( $id ) . '</td>' .
								'<td class="blank">' . $this->p->admin->form->get_hidden( $cm_opt.'label' ) .
									$this->p->options[$cm_opt.'label'] . '</td>';
							}
						}
					}
					break;

				case 'activation':

					$ret = array_merge( $ret, $this->get_pre_activation() );

					$ret[] = $this->p->util->th( 'Preserve Settings on Uninstall', 'highlight', null, 
					'Check this option if you would like to preserve all ' . $this->p->fullname . 
					' settings when you <em>uninstall</em> the plugin (default is unchecked).' ) . 
					'<td>' . $this->p->admin->form->get_checkbox( 'ngfb_preserve' ) . '</td>';

					$ret[] = $this->p->util->th( 'Reset Settings on Activate', null, null, 
					'Check this option if you would like to reset the ' . $this->p->fullname . 
					' settings to their default values when you <em>deactivate</em>, and then 
					<em>re-activate</em> the plugin (default is unchecked).' ) .  
					'<td>' . $this->p->admin->form->get_checkbox( 'ngfb_reset' ) . '</td>';

					$ret[] = $this->p->util->th( 'Add Hidden Debug Info', null, null, 
					'Include hidden debug information with the Open Graph meta tags (default is unchecked).' ) . 
					'<td>' . $this->p->admin->form->get_checkbox( 'ngfb_debug' ) . '</td>';

					break;

				case 'content':

					$ret[] = $this->p->util->th( 'Enable Shortcode(s)', 'highlight', null, 
					'Enable the ' . $this->p->fullname . ' content shortcode(s) (default is unchecked).' ) .
					'<td>' . $this->p->admin->form->get_checkbox( 'ngfb_enable_shortcode' ) . '</td>';

					$ret[] =  $this->p->util->th( 'Ignore Small Images', 'highlight', null, 
					$this->p->fullname . ' will attempt to include images from img html tags it finds in the content.
					The img html tags must have a width and height attribute, and their size must be equal to or larger than the 
					<em>Image Dimensions</em> you\'ve chosen (on the General Settings page). 
					You can uncheck this option to include smaller images from the content, 
					or refer to the <a href="http://wordpress.org/extend/plugins/nextgen-facebook/faq/">FAQ</a> 
					for additional solutions.' ) . 
					'<td>' . $this->p->admin->form->get_checkbox( 'ngfb_skip_small_img' ) . '</td>';

					$ret[] = $this->p->util->th( 'Apply Content Filters', null, null, 
					'Apply the standard WordPress filters to render the content (default is checked).
					This renders all shortcodes, and allows ' . $this->p->fullname . ' to detect images and 
					embedded videos that may be provided by these.' ) . 
					'<td>' . $this->p->admin->form->get_checkbox( 'ngfb_filter_content' ) . '</td>';

					$ret[] = $this->p->util->th( 'Apply Excerpt Filters', null, null, 
					'Apply the standard WordPress filters to render the excerpt (default is unchecked).
					Check this option if you use shortcodes in your excerpt, for example.' ) . 
					'<td>' . $this->p->admin->form->get_checkbox( 'ngfb_filter_excerpt' ) . '</td>';

					$ret = array_merge( $ret, $this->get_more_content() );

					break;

				case 'cache':

					$ret[] = $this->p->util->th( 'Object Cache Expiry', null, null, 
					$this->p->fullname . ' saves the rendered (filtered) content to a non-presistant cache (wp_cache), 
					and the completed Open Graph meta tags and social buttons to a persistant (transient) cache. 
					The default is ' . $this->p->opt->defaults['ngfb_object_cache_exp'] . ' seconds, and the minimum value is 
					1 second (such a low value is not recommended).' ) .
					'<td nowrap>' . $this->p->admin->form->get_input( 'ngfb_object_cache_exp', 'short' ) . ' Seconds</td>';

					$ret = array_merge( $ret, $this->get_more_cache() );

					break;

				case 'shorten':

					$ret[] = $this->p->util->th( 'Minimum URL Length to Shorten', null, null, 
					'URLs shorter than this length will not be shortened (default is '.
					$this->p->opt->defaults['ngfb_min_shorten'].').' ).
					'<td>'.$this->p->admin->form->get_input( 'ngfb_min_shorten', 'short' ).' Characters</td>';

					$ret[] = $this->p->util->th( 'Goo.gl Simple API Access Key', null, null, 
					'The "Google URL Shortener API Key" for this website. If you don\'t already have one, visit Google\'s 
					<a href="https://developers.google.com/url-shortener/v1/getting_started#APIKey" target="_blank">acquiring 
					and using an API Key</a> documentation, and follow the directions to acquire your <em>Simple API Access Key</em>.' ) .
					'<td>'.$this->p->admin->form->get_input( 'ngfb_googl_api_key', 'wide' ).'</td>';

					$ret[] = $this->p->util->th( 'Bit.ly Username', null, null, 
					'The Bit.ly username for the following API key. If you don\'t already have one, see 
					<a href="https://bitly.com/a/your_api_key" target="_blank">Your Bit.ly API Key</a>.' ) .
					'<td>'.$this->p->admin->form->get_input( 'ngfb_bitly_login' ).'</td>';

					$ret[] = $this->p->util->th( 'Bit.ly API Key', null, null, 
					'The Bit.ly API key for this website. If you don\'t already have one, see 
					<a href="https://bitly.com/a/your_api_key" target="_blank">Your Bit.ly API Key</a>.' ) .
					'<td>'.$this->p->admin->form->get_input( 'ngfb_bitly_api_key', 'wide' ).'</td>';

					break;

				case 'rewrite':

					$ret = array_merge( $ret, $this->get_more_rewrite() );

					break;
			}
			return $ret;
		}

		public function show_metabox_taglist() {
			?>
			<table class="ngfb-settings" style="padding-bottom:0;">
			<tr>
				<td>
				<p><?php echo $this->p->fullname; ?> will add the following Facebook and Open Graph meta tags to your webpages. 
				If your theme or another plugin already generates one or more of these meta tags, you may uncheck them here to prevent 
				<?php echo $this->p->fullname; ?> from adding duplicate meta tags (for example, the "description" meta tag is popular
				with SEO plugins, so it is unchecked by default).</p>
				</td>
			</tr>
			</table>

			<table class="ngfb-settings" style="padding-bottom:0;">
			<?php
			$og_cols = 5;
			$cells = array();
			$rows = array();
			foreach ( $this->p->opt->get_defaults() as $opt => $val ) {
				if ( preg_match( '/^inc_(.*)$/', $opt, $match ) )
					$cells[] = '<td class="taglist">' . $this->p->admin->form->get_checkbox( $opt ) . '</td>' .
						'<th class="taglist' . ( $opt == 'inc_description' ? ' highlight' : '' ) .
							'">' . $match[1] . '</th>' . "\n";
			}
			unset( $opt, $val );
			$per_col = ceil( count( $cells ) / $og_cols );
			foreach ( $cells as $num => $cell ) {
				if ( empty( $rows[ $num % $per_col ] ) )
					$rows[ $num % $per_col ] = '';	// initialize the array
				$rows[ $num % $per_col ] .= $cell;	// create the html for each row
			}
			unset( $num, $cell );
			foreach ( $rows as $num => $row ) 
				echo '<tr>', $row, '</tr>', "\n";
			unset( $num, $row );

			echo '<table class="ngfb-settings"><tr>';
			echo $this->p->util->th( 'Include Empty og:* Meta Tags', null, null, '
				Include meta property tags of type og:* without any content (default is unchecked).' );
			echo '<td>', $this->p->admin->form->get_checkbox( 'og_empty_tags' ), '</td>';
			echo '</tr></table>';

		}

	}
}

?>

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

if ( ! class_exists( 'ngfbAdmin' ) ) {

	class ngfbAdmin {
	
		public $plugin_name = '';
		public $lang = array();
		public $settings = array();	// allow ngfbPro() to extend

		protected $js_locations = array(
			'header' => 'Header',
			'footer' => 'Footer',
		);

		protected $captions = array(
			'none' => '',
			'title' => 'Title Only',
			'excerpt' => 'Excerpt Only',
			'both' => 'Title and Excerpt',
		);

		protected $ngfb;	// ngfbPlugin
		protected $form;	// ngfbForm
		protected $menu_id;
		protected $menu_name;
		protected $pagehook;
		protected $readme;

		private $min_wp_version = '3.0';

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->lognew();
			$this->form = new ngfbForm( $this->ngfb, NGFB_OPTIONS_NAME, $this->ngfb->options, $this->ngfb->opt->get_defaults() );
			$this->do_extend();

			add_action( 'admin_init', array( &$this, 'check_wp_version' ) );
			add_action( 'admin_init', array( &$this, 'register_settings' ) );
			add_action( 'admin_init', array( &$this, 'set_readme' ) );
			add_action( 'admin_menu', array( &$this, 'add_admin_menus' ) );
			add_action( 'wp_loaded', array( &$this, 'check_options' ) );

			add_filter( 'plugin_action_links', array( &$this, 'add_plugin_links' ), 10, 2 );
		}

		private function do_extend() {
			foreach ( $this->ngfb->setting_libs as $id => $name ) {
				$classname = 'ngfbSettings' . preg_replace( '/ /', '', $name );
				$this->settings[$id] = new $classname( $this->ngfb, $id, $name );
			}
			unset ( $id, $name );
		}

		public function set_readme() {
			$this->readme = $this->ngfb->util->parse_readme( $this->ngfb->urls['readme'] );
		}

		public function check_wp_version() {
			global $wp_version;
			if ( version_compare( $wp_version, $this->min_wp_version, "<" ) ) {
				if( is_plugin_active( $this->plugin_name ) ) {
					deactivate_plugins( $this->plugin_name );
					wp_die( '"' . $this->ngfb->fullname . '" requires WordPress ' . $this->min_wp_version .  ' or higher, and has therefore been deactivated. 
						Please upgrade WordPress and try again. Thank you.<br /><br />Back to <a href="' . admin_url() . '">WordPress admin</a>.' );
				}
			}
		}

		public function add_admin_menus() {

			reset( $this->ngfb->setting_libs );
			$this->menu_id = key( $this->ngfb->setting_libs );
			$this->menu_name = $this->ngfb->setting_libs[$this->menu_id];
			$this->settings[$this->menu_id]->add_menu( $this->menu_id );

			foreach ( $this->ngfb->setting_libs as $id => $name )
				$this->settings[$id]->add_submenu( $this->menu_id );
			unset ( $id, $name );
		}

		protected function add_menu( $parent_id ) {
			// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
			$this->pagehook = add_menu_page( 
				$this->ngfb->fullname . ' Settings : ' . $this->menu_name, 
				$this->ngfb->menuname, 
				'manage_options', 
				$this->ngfb->acronym . '-' . $parent_id, 
				array( &$this, 'show_page' ), null, NGFB_MENU_PRIORITY);
			add_action( 'load-' . $this->pagehook, array( &$this, 'load_page' ) );
		}

		protected function add_submenu( $parent_id ) {
			// add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
			$this->pagehook = add_submenu_page( 
				$this->ngfb->acronym . '-' . $parent_id, 
				$this->ngfb->fullname . ' Settings : ' . $this->menu_name, 
				$this->menu_name, 
				'manage_options', 
				$this->ngfb->acronym . '-' . $this->menu_id, 
				array( &$this, 'show_page' ) 
			);
			add_action( 'load-' . $this->pagehook, array( &$this, 'load_page' ) );
		}

		protected function add_meta_boxes() {
		}

		public function check_options() {
			$size_info = $this->ngfb->media->get_size_info( $this->ngfb->options['og_img_size'] );

			if ( $size_info['width'] < NGFB_MIN_IMG_WIDTH || $size_info['height'] < NGFB_MIN_IMG_HEIGHT ) {

				$size_desc = $size_info['width'] . 'x' . $size_info['height'] . ', ' . ( $size_info['crop'] == 1 ? '' : 'not ' ) . 'cropped';

				$this->ngfb->notices->inf( 'The "' . $this->ngfb->options['og_img_size'] . '" image size (' . $size_desc . '), used for images 
					in the Open Graph meta tags, is smaller than the minimum of ' . NGFB_MIN_IMG_WIDTH . 'x' . NGFB_MIN_IMG_HEIGHT . '. 
					<a href="' . $this->ngfb->util->get_options_url() . '">Please select a larger Image Size Name from the settings page</a>.' );
			}

			if ( $this->ngfb->is_avail['ngfbpro'] == true && empty( $this->ngfb->options['ngfb_pro_tid'] ) ) {
				$url = $this->ngfb->util->get_options_url( 'advanced' );
				$this->ngfb->notices->inf( '<b>Transaction ID option value not found. In order for the plugin to authenticate itself for future updates, 
					please enter the transaction ID you received by email on the <a href="' . $url . '">Advanced Settings</a> page.</b>' );
			}
		}

		// display a settings link on the main plugins page
		public function add_plugin_links( $links, $file ) {
			// only add links when filter is called for this plugin
			if ( $file == $this->plugin_name ) {
				array_push( $links, '<a href="' . $this->ngfb->util->get_options_url( 'about' ) . '">' . __( 'About' ) . '</a>' );
				array_push( $links, '<a href="' . $this->ngfb->urls['support'] . '">' . __( 'Support' ) . '</a>' );
				if ( $this->ngfb->is_avail['ngfbpro'] == false ) 
					array_push( $links, '<a href="' . $this->ngfb->urls['plugin'] . '">' . __( 'Purchase Pro' ) . '</a>' );
				else
					array_push( $links, 'Pro Installed' );
			}
			return $links;
		}

		public function register_settings() {
			register_setting( $this->ngfb->acronym . '_settings', NGFB_OPTIONS_NAME, array( &$this, 'sanitize_options' ) );
		} 

		// this method receives only a partial options array, so re-create a full one
		public function sanitize_options( $opts ) {
			if ( is_array( $opts ) ) {
				// un-checked checkboxes are not given, so re-create them here based on hidden values
				$checkbox = $this->ngfb->util->preg_grep_keys( '/^is_checkbox_/', $opts, false, true );
				foreach ( $checkbox as $key => $val ) {
					if ( ! array_key_exists( $key, $opts ) )
						$opts[$key] = 0;	// add missing checkbox as empty
					unset ( $opts['is_checkbox_'.$key] );
				}
				$opts = array_merge( $this->ngfb->options, $opts );
				$opts = $this->ngfb->opt->sanitize( $opts, $this->ngfb->opt->get_defaults() );
			}
			return $opts;
		}

		public function load_page() {
			wp_enqueue_script( 'common' );
			wp_enqueue_script( 'wp-lists' );
			wp_enqueue_script( 'postbox' );

			foreach ( $this->ngfb->setting_libs as $id => $name )
				$this->ngfb->admin->settings[$id]->add_meta_boxes();

			add_meta_box( $this->pagehook . '_news', 'News Feed', array( &$this, 'show_metabox_news' ), $this->pagehook, 'side' );
			add_meta_box( $this->pagehook . '_version', 'Version Info', array( &$this, 'show_metabox_version' ), $this->pagehook, 'side' );

			if ( $this->ngfb->is_avail['ngfbpro'] == true )
				add_meta_box( $this->pagehook . '_thankyou', 'Pro Installed', array( &$this, 'show_metabox_thankyou' ), $this->pagehook, 'side' );

			add_meta_box( $this->pagehook . '_help', 'Help and Support', array( &$this, 'show_metabox_help' ), $this->pagehook, 'side' );
		}

		public function show_page() {
			$this->ngfb->debug->show( null, 'Debug Log' );
			$this->ngfb->style->admin_page();
			$this->ngfb->style->settings();

			// add meta box here (after wp_enqueue_script()) to prevent removal
			if ( $this->ngfb->is_avail['ngfbpro'] !== true ) {
				add_meta_box( $this->pagehook . '_purchase', 'Pro Version', array( &$this, 'show_metabox_purchase' ), $this->pagehook, 'side' );
				add_filter( 'postbox_classes_' . $this->pagehook . '_' . $this->pagehook . '_purchase', array( &$this, 'add_class_postbox_purchase_side' ) );
			}

			?>
			<div class="wrap" id="<?php echo $this->pagehook; ?>">
				<?php screen_icon('options-general'); ?>
				<h2><?php echo $this->ngfb->fullname; ?></h2>
				<div id="poststuff" class="metabox-holder <?php echo 'has-right-sidebar'; ?>">
					<div id="side-info-column" class="inner-sidebar">
						<?php do_meta_boxes( $this->pagehook, 'side', null ); ?>
					</div><!-- .inner-sidebar -->
					<div id="post-body" class="has-sidebar">
						<div id="post-body-content" class="has-sidebar-content">
							<?php $this->show_form(); ?>
						</div><!-- .has-sidebar-content -->
					</div><!-- .has-sidebar -->
				</div><!-- .metabox-holder -->
			</div><!-- .wrap -->
			<script type="text/javascript">
				//<![CDATA[
					jQuery(document).ready( 
						function($) {
							// close postboxes that should be closed
							$('.if-js-closed').removeClass('if-js-closed').addClass('closed');
							// postboxes setup
							postboxes.add_postbox_toggles('<?php echo $this->pagehook; ?>');
						}
					);
				//]]>
			</script>
			<?php
		}

		public function add_class_postbox_purchase_side( $classes ) {
			array_push( $classes, 'postbox_purchase_side' );
			return $classes;
		}

		protected function show_form() {
			echo '<form name="ngfb" method="post" action="options.php" id="settings">', "\n";
			settings_fields( $this->ngfb->acronym . '_settings' ); 
			wp_nonce_field( plugin_basename( __FILE__ ), NGFB_NONCE );
			wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
			wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

			// always include the version number of the options
			echo $this->ngfb->admin->form->get_hidden( 'ngfb_version', $this->ngfb->opt->version );

			do_meta_boxes( $this->pagehook, 'normal', null ); 

			foreach ( range( 1, ceil( count( $this->ngfb->website_libs ) / 2 ) ) as $row ) {
				echo '<div class="website-row">', "\n";
				foreach ( range( 1, 2 ) as $col ) {
					$pos_id = 'website-row-' . $row . '-col-' . $col;
					echo '<div class="website-col-', $col, '" id="', $pos_id, '" >';
					do_meta_boxes( $this->pagehook, $pos_id, null ); 
					echo '</div>', "\n";
				}
				echo '</div>';
			}
			echo '<div style="clear:both;"></div>';

			$this->show_submit_button();

			echo '</form>', "\n";
		}

		protected function show_submit_button( $text = 'Save All Changes' ) {
			echo '<div class="save_button"><input type="submit" class="button-primary" value="', $text, '" /></div>', "\n";
		}

		public function show_metabox_news() {
			$this->show_feed( $this->ngfb->urls['news_feed'], 3, 'news_feed' );
		}

		protected function show_feed( $url, $max_num = 5, $class = 'rss_feed' ) {
			include_once( ABSPATH . WPINC . '/feed.php' );
			$have_items = 0;
			$rss_items = array();
			$rss_feed = fetch_feed( $url );
			if ( ! is_wp_error( $rss_feed ) ) {
				$have_items = $rss_feed->get_item_quantity( $max_num ); 
				$rss_items = $rss_feed->get_items( 0, $have_items );
			}
			echo '<div class="', $class, '"><ul>', "\n";
			if ( $have_items == 0 ) {
				echo '<li>No items found.</li>', "\n";
			} else {
				foreach ( $rss_items as $item ) {
					$desc = $item->get_description();
					$desc = preg_replace( '/^\.rss-manager [^<]*/m', '', $desc );	// remove the inline styling
					$desc = preg_replace( '/ cellspacing=["\'][^"\]*["\']/im', '', $desc );	// remove the inline styling
					echo '<li><div class="title"><a href="', esc_url( $item->get_permalink() ), '" title="', 
						printf( 'Posted %s', $item->get_date('j F Y | g:i a') ), '">',
						esc_html( $item->get_title() ), '</a></div><div class="description">', $desc, '</div></li>', "\n";
				}
			}
			echo '</ul></div>', "\n";
		}

		public function show_metabox_version() {
			$latest_version = '';
			$latest_notice = '';
			if ( ! empty( $this->ngfb->admin->readme['stable_tag'] ) ) {
				$upgrade_notice = $this->ngfb->admin->readme['upgrade_notice'];
				if ( is_array( $upgrade_notice ) ) {
					reset( $upgrade_notice );
					$latest_version = key( $upgrade_notice );
					$latest_notice = $upgrade_notice[$latest_version];
				}
			}
			?>
			<table class="ngfb-settings">
			<tr><th class="side">Installed:</th><td><?php echo $this->ngfb->version; echo $this->ngfb->is_avail['ngfbpro'] ? ' (Pro)' : ''; ?></tr>
			<tr><th class="side">Stable:</th><td><?php echo $this->ngfb->admin->readme['stable_tag']; ?></tr>
			<tr><th class="side">Latest:</th><td><?php echo $latest_version; ?></tr>
			<tr><td colspan="2"><p><?php echo $latest_notice; ?></p></tr>
			</table>
			<?php
		}

		public function show_metabox_purchase() {
			echo '<form name="ngfb" method="get" action="' . $this->ngfb->urls['plugin'] . '" target="_blank">', "\n";
			echo '<p>', $this->ngfb->msgs['purchase'], '</p>', "\n";
			echo '<p>', $this->ngfb->msgs['review'], '</p>', "\n";
			echo '<p class="sig">Thank you.</p>', "\n";
			echo '<p>'; $this->show_submit_button( 'Download the Pro Version' ); echo '</p>';
			echo '</form>', "\n";
		}

		public function show_metabox_thankyou() {
			echo '<p>Thank you for your purchase! I hope the ' . $this->ngfb->fullname . ' plugin will meet and exceed your expectations.</p>', "\n";
		}

		public function show_metabox_help() {
			?>
			<p>Individual option boxes (like this one) can be opened / closed by clicking on their title bar, moved and re-ordered by dragging them, and removed / added from the <em>Screen Options</em> tab (top-right).</p>
			<p>Need help? Visit the WordPress <a href="http://wordpress.org/support/plugin/nextgen-facebook" target="_blank">NGFB Open Graph Support Forum</a>.</p>
			<?php
		}

	}
}

?>

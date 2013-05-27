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
				$this->settings[$id] = new $classname( &$this->ngfb, $id, $name );
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
				array( &$this, 'show_page' ) 
			);
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

			if ( $this->ngfb->is_avail['ngfbpro'] == false ) {
				$this->ngfb->notices->inf( '<b>' . $this->ngfb->msgs['purchase'] . '</b>' );
			} elseif ( empty( $this->ngfb->options['ngfb_pro_tid'] ) ) {
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

		// this method receives only a partial options array
		public function sanitize_options( $opts ) {
			if ( is_array( $opts ) ) {
				// if the input arrays have the same string keys, then the later value for that key will overwrite the previous one
				$opts = array_merge( $this->ngfb->options, $opts );
				$opts = $this->ngfb->opt->sanitize( &$opts, $this->ngfb->opt->get_defaults() );
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
			add_meta_box( $this->pagehook . '_consult', 'Consulting Services', array( &$this, 'show_metabox_consult' ), $this->pagehook, 'side' );

			if ( $this->ngfb->is_avail['ngfbpro'] == true )
				add_meta_box( $this->pagehook . '_thankyou', 'Pro Installed', array( &$this, 'show_metabox_thankyou' ), $this->pagehook, 'side' );
		}

		public function show_page() {
			$this->ngfb->debug->show( null, 'Debug Log' );
			$this->admin_page_style();
			$this->settings_style();

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
							<?php $this->show_form( 'normal' ); ?>
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

		protected function show_form( $context = 'normal' ) {
			echo '<form name="ngfb" method="post" action="options.php" id="settings">', "\n";
			settings_fields( $this->ngfb->acronym . '_settings' ); 
			wp_nonce_field( plugin_basename( __FILE__ ), NGFB_NONCE );
			wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
			wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

			// always include the version number of the options
			echo $this->ngfb->admin->form->get_hidden( 'ngfb_version', $this->ngfb->opt->version );

			do_meta_boxes( $this->pagehook, 'normal', null ); 

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
			echo '<p>Thank you for your support and appreciation.</p>', "\n";
		}

		public function show_metabox_consult() {
			?>
			<p>Need some UNIX or WordPress related help? Have a look at my freelance consulting 
			<a href="http://surniaulula.com/contact-me/services/" target="blank">services</a> 
			and <a href="http://surniaulula.com/contact-me/rates/">rates</a>.</p>
			<?php
		}

		public function admin_page_style() {
			?>
			<style type="text/css">
				.sig {
					font-family:cursive;
					font-size:1.2em;
				}
				.wrap { 
					font-size:1em; 
					line-height:1.3em; 
				}
				.wrap h2 { 
					margin:0 0 10px 0; 
				}
				.wrap p { 
					text-align:justify; 
					line-height:1.2em; 
					margin:10px 0 10px 0;
				}
				.wrap p.inline { 
					display:inline-block;
					margin:0 0 10px 10px;
				}
				.btn_wizard_column { 
					white-space:nowrap;
				}
				.btn_wizard_example { 
					display:inline-block; 
					width:155px; 
				}
				.postbox {
					-webkit-border-radius:5px;
					border-radius:5px;
					border:1px solid transparent;
					margin:0 0 10px 0;
				}
				.inside {
					padding:2px 14px 2px 12px	!important;
				}
				.save_button { 
					text-align:center;
					margin:15px 0 0 0;
				}
				.postbox_purchase_side {
					color:#333;
					background:#eeeeff;
					background-image: -webkit-gradient(linear, left bottom, left top, color-stop(7%, #eeeeff), color-stop(77%, #ddddff));
					background-image: -webkit-linear-gradient(bottom, #eeeeff 7%, #ddddff 77%);
					background-image:    -moz-linear-gradient(bottom, #eeeeff 7%, #ddddff 77%);
					background-image:      -o-linear-gradient(bottom, #eeeeff 7%, #ddddff 77%);
					background-image: linear-gradient(to top, #eeeeff 7%, #ddddff 77%);
					border:1px solid #b4b4b4;
				}
				#toplevel_page_ngfb-about table {
					table-layout:fixed;
				}
				.rss-manager table {
				}
				.rss-manager p {
					text-align:justify;
					margin:7px 0 14px 0;
				}
				.rss-manager p.tags,
				.rss-manager p.categories {
					font-size:0.8em;
					font-style:italic;
					margin:5px 0 5px 0;
				}
				.rss-manager p.tags,
				.rss-manager p.categories {
					display:none;
				}
				.rss-manager img {
					border:1px solid #ddd;
					background-color:#ffffff;
					padding:4px;
				}
				.support_feed .description {
					font-size:0.9em;
				}
				.support_feed .description p {
					margin:5px 0 5px 20px;
				}
			</style>
			<?php
		}

		public function settings_style() {
			?>
			<style type="text/css">
				table.ngfb-settings .pro_feature {
					font-style:italic;
				}
				table.ngfb-settings { 
					width:100%;
				}
				table.ngfb-settings h3 { 
					padding:0		!important;
					margin:20px 0 10px 0	!important;
					background:none;
				}
				table.ngfb-settings pre {
					white-space:pre;
					overflow:auto;
				}
				table.ngfb-settings ul { 
					margin-left:20px;
					list-style-type:circle;
				}
				table.ngfb-settings tr { vertical-align:top; }
				table.ngfb-settings th { 
					text-align:right;
					white-space:nowrap; 
					padding:0 10px 0 4px; 
					width:220px;
				}
				table.ngfb-settings th.short { width:120px; }
				table.ngfb-settings th.side { width:50px; }
				table.ngfb-settings th.social { 
					font-weight:bold; 
					text-align:left; 
					padding:2px 10px 2px 10px; 
					background-color:#eee; 
					border:1px solid #ccc;
					width:50%;
				}
				table.ngfb-settings td { 
					padding:0 4px 0 4px;
				}
				table.ngfb-settings td p { 
					margin:0 0 10px 0;
				}
				table.ngfb-settings td select,
				table.ngfb-settings td input { margin:0 0 5px 0; }
				table.ngfb-settings td input[type=text] { width:250px; }
				table.ngfb-settings td input[type=text].short { width:50px; }
				table.ngfb-settings td input[type=text].wide { width:100%; }
				table.ngfb-settings td input[type=radio] { vertical-align:top; margin:4px 4px 4px 0; }
				table.ngfb-settings td textarea { padding:2px; }
				table.ngfb-settings td textarea.wide { width:100%; height:5em; }
				table.ngfb-settings td select { width:250px; }
				table.ngfb-settings td select.short { width:100px; }
				table.ngfb-settings td select.medium { width:160px; }
			</style>
			<?php
		}

	}
}

?>

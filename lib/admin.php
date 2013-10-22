<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbAdmin' ) ) {

	class ngfbAdmin {
	
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

		protected $p;
		protected $form;
		protected $menu_id;
		protected $menu_name;
		protected $pagehook;
		protected $readme;

		public $lang = array();
		public $settings = array();

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
			$this->setup_vars();
			$def_opts = $this->p->opt->get_defaults();
			$this->form = new ngfbForm( $this->p, NGFB_OPTIONS_NAME, $this->p->options, $def_opts );

			add_action( 'admin_init', array( &$this, 'register_settings' ) );
			// use priority -1 to make sure Settings sub-menus are top-most
			add_action( 'admin_menu', array( &$this, 'add_admin_menus' ), -1 );
			add_action( 'network_admin_menu', array( &$this, 'add_network_admin_menus' ), -1 );
			add_filter( 'plugin_action_links', array( &$this, 'add_plugin_links' ), 10, 2 );
		}

		private function setup_vars() {
			foreach ( array_merge( $this->p->setting_libs, $this->p->network_setting_libs ) as $id => $name ) {
				$classname = 'ngfbSettings'.preg_replace( '/ /', '', $name );
				if ( class_exists( $classname ) )
					$this->settings[$id] = new $classname( $this->p, $id, $name );
			}
			unset ( $id, $name );
		}

		public function set_readme( $expire_secs = 0 ) {
			if ( empty( $this->readme ) )
				$this->readme = $this->p->util->parse_readme( $expire_secs );
		}

		public function add_admin_menus( $libs = array() ) {
			if ( empty( $libs ) ) 
				$libs = $this->p->setting_libs;
			$this->menu_id = key( $libs );
			$this->menu_name = $libs[$this->menu_id];
			$this->settings[$this->menu_id]->add_menu_page( $this->menu_id );
			foreach ( $libs as $id => $name )
				$this->settings[$id]->add_submenu_page( $this->menu_id );
			unset ( $id, $name );
		}

		public function add_network_admin_menus() {
			$this->add_admin_menus( $libs = $this->p->network_setting_libs );
		}

		protected function add_menu_page( $parent_id ) {
			// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
			$this->pagehook = add_menu_page( 
				$this->p->fullname.' : '.$this->menu_name, 
				$this->p->menuname, 
				'manage_options', 
				$this->p->acronym.'-'.$parent_id, 
				array( &$this, 'show_page' ), null, NGFB_MENU_PRIORITY);
			add_action( 'load-'.$this->pagehook, array( &$this, 'load_page' ) );
		}

		protected function add_submenu_page( $parent_id ) {
			if ( $this->menu_id == 'contact' )
				$parent_slug = 'options-general.php';
			else
				$parent_slug = $this->p->acronym.'-'.$parent_id;

			// add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
			$this->pagehook = add_submenu_page( 
				$parent_slug, 
				$this->p->fullname.' : '.$this->menu_name, 
				$this->menu_name, 
				'manage_options', 
				$this->p->acronym.'-'.$this->menu_id, 
				array( &$this, 'show_page' ) 
			);
			add_action( 'load-'.$this->pagehook, array( &$this, 'load_page' ) );
		}

		protected function add_meta_boxes() {
		}

		// display a settings link on the main plugins page
		public function add_plugin_links( $links, $file ) {
			// only add links when filter is called for this plugin
			if ( $file == NGFB_PLUGINBASE ) {
				foreach ( $links as $num => $val )
					if ( preg_match( '/>Edit</', $val ) )
						unset ( $links[$num] );
				array_push( $links, '<a href="'.$this->p->util->get_admin_url( 'about' ).'">'.__( 'About' ).'</a>' );
				array_push( $links, '<a href="'.$this->p->urls['forum'].'">'.__( 'Support' ).'</a>' );
				if ( ! $this->p->check->pro_active() ) 
					array_push( $links, '<a href="'.$this->p->urls['plugin'].'">'.__( 'Purchase Pro' ).'</a>' );
			}
			return $links;
		}

		public function register_settings() {
			register_setting( $this->p->acronym.'_settings', NGFB_OPTIONS_NAME, array( &$this, 'sanitize_options' ) );
		} 

		// this method receives only a partial options array, so re-create a full one
		// wordpress handles the actual saving of the options
		public function sanitize_options( $opts ) {

			if ( ! is_array( $opts ) ) {
				add_settings_error( NGFB_OPTIONS_NAME, 'notarray', '<b>'.$this->p->acronym_uc.' Error</b> : 
					Submitted settings are not an array.', 'error' );
				return $opts;
			}

			// get default values, including css from default stylesheets
			$def_opts = $this->p->opt->get_defaults();

			// unchecked checkboxes are not provided, so re-create them here based on hidden values
			$checkbox = $this->p->util->preg_grep_keys( '/^is_checkbox_/', $opts, false, true );
			foreach ( $checkbox as $key => $val ) {
				if ( ! array_key_exists( $key, $opts ) )
					$opts[$key] = 0;	// add missing checkbox as empty
				unset ( $opts['is_checkbox_'.$key] );
			}

			$opts = array_merge( $this->p->options, $opts );
			$opts = $this->p->opt->sanitize( $opts, $def_opts );

			// remove any options that shouldn't exist
			foreach ( $opts as $key => $val )
				if ( ! empty( $key ) && ! array_key_exists( $key, $def_opts ) )
					unset( $opts[$key] );
			unset ( $key, $val );

			// update the social stylesheet
			if ( empty( $opts['buttons_link_css'] ) ) 
				$this->p->style->unlink_social();
			else $this->p->style->update_social( $opts );

			// the pro version authentication id can be applied to all sites within a multisite
			if ( is_multisite() ) {
				if ( ! empty( $opts['plugin_pro_tid_site'] ) ) {
					if ( ! empty( $opts['plugin_pro_tid'] ) ) {
						update_site_option( NGFB_OPTIONS_NAME.'_site', 
							array( 'plugin_pro_tid' => $opts['plugin_pro_tid'], ) );
						$opts['plugin_pro_tid'] = '';	// always trunc to inherit multisite value
					} else delete_site_option( NGFB_OPTIONS_NAME.'_site' );
				}
			}

			add_settings_error( NGFB_OPTIONS_NAME, 'updated', '<b>'.$this->p->acronym_uc.' Info</b> : '.
				__( 'Plugin settings have been updated.', NGFB_TEXTDOM ).' '.
				sprintf( __( 'Wait %d seconds for cache objects to expire (default) or use the \'Clear All Cache\' button.' ), 
					$this->p->options['plugin_object_cache_exp'] ), 'updated' );

			return $opts;
		}

		public function load_page() {
			wp_enqueue_script( 'postbox' );
			$upload_dir = wp_upload_dir();	// returns assoc array with path info
			$old_css_file = trailingslashit( $upload_dir['basedir'] ).'ngfb-social-buttons.css';
			$user_opts = $this->p->user->get_options();

			if ( ! empty( $_GET['settings-updated'] ) ) {

				if ( empty( $this->p->options['plugin_pro_tid'] ) ) {
					$this->p->update_error = '';
					delete_option( $this->p->acronym.'_update_error' );
				} elseif ( ! $this->p->check->pro_active() && 
					! empty( $this->p->options['plugin_pro_tid'] ) )
						$this->p->update->check_for_updates();

			} elseif ( ! empty( $_GET['action'] ) ) {

				switch ( $_GET['action'] ) {
					case 'remove_old_css' : 
						if ( file_exists( $old_css_file ) )
							if ( @unlink( $old_css_file ) )
								add_settings_error( NGFB_OPTIONS_NAME, 'cssnotrm', 
									'<b>'.$this->p->acronym_uc.' Info</b> : The old <u>'.$old_css_file.'</u> 
										stylesheet has been removed.', 'updated' );
							else
								add_settings_error( NGFB_OPTIONS_NAME, 'cssnotrm', 
									'<b>'.$this->p->acronym_uc.' Error</b> : Error removing the old <u>'.$old_css_file.'</u> 
										stylesheet. Does the web server have sufficient privileges?', 'error' );

						break;
					case 'check_for_updates' : 
						if ( ! empty( $this->p->options['plugin_pro_tid'] ) ) {
							$this->p->admin->set_readme( 0 );
							$this->p->update->check_for_updates();
							$this->p->notices->inf( 'Plugin update information has been checked and updated.' );
						}
						break;
					case 'clear_all_cache' : 
						$deleted_cache = $this->p->util->delete_expired_file_cache( true );
						$deleted_transient = $this->p->util->delete_expired_transients( true );
						wp_cache_flush();
						if ( function_exists('w3tc_pgcache_flush') ) 
							w3tc_pgcache_flush();
						elseif ( function_exists('wp_cache_clear_cache') ) 
							wp_cache_clear_cache();
						$this->p->notices->inf( 'Cached files, WP object cache, transient cache, and any 
							additional caches, like APC, Memcache, Xcache, W3TC, Super Cache, etc. have all been cleared.' );
						break;
				}
			}

			if ( file_exists( $old_css_file ) ) {
				$this->p->notices->inf( 
					sprintf( __( 'The <u>%s</u> stylesheet is no longer used.', 
						NGFB_TEXTDOM ), $old_css_file ).' '.
					sprintf( __( 'Styling for social buttons is now managed on the <a href="%s">Social Style settings page</a>.', 
						NGFB_TEXTDOM ), $this->p->util->get_admin_url( 'style' ) ).' '.
					sprintf( __( 'When you are ready, you can <a href="%s">click here to remove the old stylesheet</a>.', 
						NGFB_TEXTDOM ), $this->p->util->get_admin_url( '?action=remove_old_css' ) ) 
				);
			}

			$this->p->admin->set_readme( $this->p->update_hours * 60 * 60 );	// the version info metabox on all settings pages needs this

			foreach ( $this->p->setting_libs as $id => $name )
				$this->p->admin->settings[$id]->add_meta_boxes();

			add_meta_box( $this->pagehook.'_info', 'Plugin Information', array( &$this, 'show_metabox_info' ), $this->pagehook, 'side' );
			add_meta_box( $this->pagehook.'_news', 'News Feed', array( &$this, 'show_metabox_news' ), $this->pagehook, 'side' );
			add_meta_box( $this->pagehook.'_help', 'Help and Support', array( &$this, 'show_metabox_help' ), $this->pagehook, 'side' );

			if ( $this->p->check->pro_active() )
				add_meta_box( $this->pagehook.'_thankyou', 'Pro Version', array( &$this, 'show_metabox_thankyou' ), $this->pagehook, 'side' );

		}

		public function show_page() {
			// the settings page displays its own error messages
			if ( $this->menu_id !== 'contact' )
				settings_errors( NGFB_OPTIONS_NAME );	// display "error" and "updated" messages
			$this->p->debug->show_html( null, 'Debug Log' );
			// add meta box here to prevent removal
			if ( ! $this->p->check->pro_active() ) {
				add_meta_box( $this->pagehook.'_purchase', 'Pro Version', array( &$this, 'show_metabox_purchase' ), $this->pagehook, 'side' );
				add_filter( 'postbox_classes_'.$this->pagehook.'_'.$this->pagehook.'_purchase', array( &$this, 'add_class_postbox_highlight_side' ) );
			}
			?>
			<div class="wrap" id="<?php echo $this->pagehook; ?>">
				<?php screen_icon('options-general'); ?>
				<h2><?php echo $this->p->fullname.' : '.$this->menu_name; ?></h2>
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

		public function add_class_postbox_highlight_side( $classes ) {
			array_push( $classes, 'postbox_highlight_side' );
			return $classes;
		}

		protected function show_form() {
			echo '<form name="ngfb" method="post" action="options.php" id="settings">', "\n";
			settings_fields( $this->p->acronym.'_settings' ); 
			wp_nonce_field( plugin_basename( __FILE__ ), NGFB_NONCE );
			wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
			wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

			// always include the version number of the options
			echo $this->p->admin->form->get_hidden( 'options_version', $this->p->opt->options_version );
			echo $this->p->admin->form->get_hidden( 'plugin_version', $this->p->version );
			do_meta_boxes( $this->pagehook, 'normal', null ); 
			foreach ( range( 1, ceil( count( $this->p->admin->settings['social']->website ) / 2 ) ) as $row ) {
				echo '<div class="website-row">', "\n";
				foreach ( range( 1, 2 ) as $col ) {
					$pos_id = 'website-row-'.$row.'-col-'.$col;
					echo '<div class="website-col-', $col, '" id="', $pos_id, '" >';
					do_meta_boxes( $this->pagehook, $pos_id, null ); 
					echo '</div>', "\n";
				}
				echo '</div>', "\n";
			}
			echo '<div style="clear:both;"></div>';
			do_meta_boxes( $this->pagehook, 'bottom', null ); 
			echo $this->get_submit_button();
			echo '</form>', "\n";
		}

		protected function get_submit_button( $submit_text = '', $class = 'save-all-button' ) {
			if ( empty( $submit_text ) ) 
				$submit_text = __( 'Save All Changes', NGFB_TEXTDOM );
			return '<div class="'.$class.'"><input type="submit" class="button-primary" value="'.$submit_text.'" /></div>'."\n";
		}

		protected function show_feed( $url, $max_num = 5, $class = 'rss_feed' ) {
			include_once( ABSPATH.WPINC.'/feed.php' );
			$have_items = 0;
			$rss_items = array();
			add_filter( 'wp_feed_cache_transient_lifetime', array( &$this, 'feed_cache_expire' ) );
			$rss_feed = fetch_feed( $url );		// since wp 2.8
			remove_filter( 'wp_feed_cache_transient_lifetime' , array( &$this, 'feed_cache_expire' ) );
			echo '<div class="', $class, '"><ul>', "\n";
			if ( is_wp_error( $rss_feed ) ) {
				$error_string = $rss_feed->get_error_message();
				echo '<li>', __( 'WordPress reported an error:', NGFB_TEXTDOM ), 
					' ', $error_string, '</li>', "\n";
			} else {
				$have_items = $rss_feed->get_item_quantity( $max_num ); 
				$rss_items = $rss_feed->get_items( 0, $have_items );
			}
			if ( $have_items == 0 ) {
				echo '<li>', __( 'No items found.', NGFB_TEXTDOM ), '</li>', "\n";
			} else {
				foreach ( $rss_items as $item ) {
					$desc = $item->get_description();
					$desc = preg_replace( '/^\.rss-manager [^<]*/m', '', $desc );		// remove the inline styling
					$desc = preg_replace( '/ cellspacing=["\'][0-9]*["\']/im', '', $desc );	// remove table cellspacing
					echo '<li><div class="title"><a href="', esc_url( $item->get_permalink() ), '" title="', 
						printf( 'Posted %s', $item->get_date('j F Y | g:i a') ), '">',
						esc_html( $item->get_title() ), '</a></div><div class="description">', 
						$desc, '</div></li>', "\n";
				}
			}
			echo '</ul></div>', "\n";
		}

		public function feed_cache_expire( $seconds ) {
			return $this->p->update_hours * 60 * 60;
		}

		public function show_metabox_news() {
			$this->show_feed( $this->p->urls['feed'], 3, $this->p->acronym.'_feed' );
		}

		public function show_metabox_info() {
			$stable_tag = 'N/A';
			$latest_version = 'N/A';
			$latest_notice = '';
			if ( ! empty( $this->p->admin->readme['stable_tag'] ) ) {
				$stable_tag = $this->p->admin->readme['stable_tag'];
				$upgrade_notice = $this->p->admin->readme['upgrade_notice'];
				if ( is_array( $upgrade_notice ) ) {
					reset( $upgrade_notice );
					$latest_version = key( $upgrade_notice );
					$latest_notice = $upgrade_notice[$latest_version];
				}
			}
			?>
			<table class="ngfb-settings">
			<tr><th class="side">Installed:</th>
			<td><?php 
				echo $this->p->version;
				if ( $this->p->check->pro_active() )
					echo ' (Pro)';
				elseif ( $this->p->is_avail['aop'] )
					echo ' (Pro)';
				else
					echo ' (Free)'; 
			?></td></tr>
			<tr><th class="side">Stable:</th><td><?php echo $stable_tag; ?></td></tr>
			<tr><th class="side">Latest:</th><td><?php echo $latest_version; ?></td></tr>
			<tr>
				<td colspan="2" id="latest_notice">
					<p><?php echo $latest_notice; ?></p>
					<p><?php echo $this->p->util->get_admin_url( 'about', 
						__( 'See the Changelog for additional details...', NGFB_TEXTDOM ) ); ?></p>
				</td>
			</tr>
			<?php
			echo '<tr><td colspan="2">';
			echo '<p class="centered">';
			if ( ! empty( $this->p->options['plugin_pro_tid'] ) )
				echo $this->p->admin->form->get_button( __( 'Check for Updates', NGFB_TEXTDOM ), 
					'button-primary', null, $this->p->util->get_admin_url().'&amp;action=check_for_updates' );
			echo $this->p->admin->form->get_button( __( 'Clear All Cache', NGFB_TEXTDOM ), 
				'button-primary', null, $this->p->util->get_admin_url().'&amp;action=clear_all_cache' );
			echo '</p></td></tr></table>';
		}

		public function show_metabox_purchase() {
			echo '<table class="ngfb-settings"><tr><td>';
			echo $this->p->msg->get( 'purchase_box' ), "\n";
			echo '<p>Thank you,</p>', "\n";
			echo '<p class="sig">js.</p>', "\n";
			echo '<p class="centered">';
			echo $this->p->admin->form->get_button( __( 'Purchase the Pro Version', NGFB_TEXTDOM ), 
				'button-primary', null, $this->p->urls['plugin'] );
			echo '</p></td></tr></table>';
		}

		public function show_metabox_thankyou() {
			echo '<table class="ngfb-settings"><tr><td>';
			echo $this->p->msg->get( 'thankyou' ), "\n";
			echo '<p class="sig">js.</p>', "\n";
			echo '</td></tr></table>';
		}

		public function show_metabox_help() {
			echo '<table class="ngfb-settings"><tr><td>';
			echo $this->p->msg->get( 'help_boxes' ), "\n";
			if ( $this->p->is_avail['aop'] == true )
				echo $this->p->msg->get( 'help_pro' ), "\n";
			else
				echo $this->p->msg->get( 'help_free' ), "\n";
			echo '<p class="centered" style="margin-top:15px;">';
			$img_size = 32;
			foreach ( $this->p->follow as $img => $url )
				echo '<a href="'.$url.'" target="_blank"><img 
					src="'.NGFB_URLPATH.'images/'.$img.'" width="'.$img_size.'" height="'.$img_size.'"></a> ';
			echo '</p>';
			echo '</td></tr></table>';
		}

	}

}

?>

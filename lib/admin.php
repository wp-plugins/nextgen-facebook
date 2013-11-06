<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbAdmin' ) ) {

	class NgfbAdmin {
	
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
			$this->p->check->conflicts();
			$this->setup_vars();

			add_action( 'admin_init', array( &$this, 'register_settings' ) );
			add_action( 'admin_menu', array( &$this, 'add_admin_menus' ), -1 );
			add_filter( 'plugin_action_links', array( &$this, 'add_plugin_action_links' ), 10, 2 );

			if ( is_multisite() ) {
				add_action( 'network_admin_menu', array( &$this, 'add_network_admin_menus' ), -1 );
				add_action( 'network_admin_edit_'.NGFB_SITE_OPTIONS_NAME, array( &$this, 'save_site_options' ) );
				add_filter( 'network_admin_plugin_action_links', array( &$this, 'add_plugin_action_links' ), 10, 2 );
			}
		}

		private function setup_vars() {
			$def_opts = $this->p->opt->get_defaults();
			$this->form = new SucomForm( $this->p, NGFB_OPTIONS_NAME, $this->p->options, $def_opts );

			$libs = $this->p->cf['lib']['setting'];
			if ( is_multisite() )
				$libs = array_merge( $libs, $this->p->cf['lib']['network_setting'] );
			foreach ( $libs as $id => $name ) {
				$classname = __CLASS__.ucfirst( $id );
				if ( class_exists( $classname ) )
					$this->settings[$id] = new $classname( $this->p, $id, $name );
			}
			unset ( $id, $name );
		}

		public function register_settings() {
			register_setting( $this->p->cf['lca'].'_settings', NGFB_OPTIONS_NAME, array( &$this, 'sanitize_options' ) );
		} 

		public function set_readme( $expire_secs = 0 ) {
			if ( empty( $this->readme ) )
				$this->readme = $this->p->util->parse_readme( $expire_secs );
		}

		public function add_admin_menus( $libs = array() ) {
			if ( empty( $libs ) ) 
				$libs = $this->p->cf['lib']['setting'];
			$this->menu_id = key( $libs );
			$this->menu_name = $libs[$this->menu_id];
			$this->settings[$this->menu_id]->add_menu_page( $this->menu_id );
			foreach ( $libs as $id => $name )
				$this->settings[$id]->add_submenu_page( $this->menu_id );
			unset ( $id, $name );
		}

		public function add_network_admin_menus() {
			$this->add_admin_menus( $this->p->cf['lib']['network_setting'] );
		}

		protected function add_menu_page( $parent_id ) {
			// add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position );
			$this->pagehook = add_menu_page( 
				$this->p->cf['full'].' : '.$this->menu_name, 
				$this->p->cf['menu'], 
				'manage_options', 
				$this->p->cf['lca'].'-'.$parent_id, 
				array( &$this, 'show_page' ), 
				null, 
				NGFB_MENU_PRIORITY
			);
			add_action( 'load-'.$this->pagehook, array( &$this, 'load_page' ) );
		}

		protected function add_submenu_page( $parent_id ) {
			if ( $this->menu_id == 'contact' )
				$parent_slug = 'options-general.php';
			else
				$parent_slug = $this->p->cf['lca'].'-'.$parent_id;

			// add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
			$this->pagehook = add_submenu_page( 
				$parent_slug, 
				$this->p->cf['full'].' : '.$this->menu_name, 
				$this->menu_name, 
				'manage_options', 
				$this->p->cf['lca'].'-'.$this->menu_id, 
				array( &$this, 'show_page' ) 
			);
			add_action( 'load-'.$this->pagehook, array( &$this, 'load_page' ) );
		}

		protected function add_meta_boxes() {
		}

		// display a settings link on the main plugins page
		public function add_plugin_action_links( $links, $file ) {

			// only add links when filter is called for this plugin
			if ( $file == NGFB_PLUGINBASE ) {

				// remove the Edit link
				foreach ( $links as $num => $val ) {
					if ( preg_match( '/>Edit</', $val ) )
						unset ( $links[$num] );
				}
				if ( $this->p->is_avail['aop'] ) {
					array_push( $links, '<a href="'.$this->p->cf['url']['pro_faq'].'">'.__( 'FAQ', NGFB_TEXTDOM ).'</a>' );
					array_push( $links, '<a href="'.$this->p->cf['url']['pro_notes'].'">'.__( 'Notes', NGFB_TEXTDOM ).'</a>' );
					array_push( $links, '<a href="'.$this->p->cf['url']['pro_request'].'">'.__( 'Support Request', NGFB_TEXTDOM ).'</a>' );
					if ( ! $this->p->check->pro_active() ) 
						array_push( $links, '<a href="'.$this->p->cf['url']['purchase'].'">'.__( 'Purchase License', NGFB_TEXTDOM ).'</a>' );
				} else {
					array_push( $links, '<a href="'.$this->p->cf['url']['faq'].'">'.__( 'FAQ', NGFB_TEXTDOM ).'</a>' );
					array_push( $links, '<a href="'.$this->p->cf['url']['notes'].'">'.__( 'Notes', NGFB_TEXTDOM ).'</a>' );
					array_push( $links, '<a href="'.$this->p->cf['url']['support'].'">'.__( 'Forum', NGFB_TEXTDOM ).'</a>' );
					array_push( $links, '<a href="'.$this->p->cf['url']['purchase'].'">'.__( 'Purchase Pro', NGFB_TEXTDOM ).'</a>' );
				}

			}
			return $links;
		}

		// this method receives only a partial options array, so re-create a full one
		// wordpress handles the actual saving of the options
		public function sanitize_options( $opts ) {
			if ( ! is_array( $opts ) ) {
				add_settings_error( NGFB_OPTIONS_NAME, 'notarray', '<b>'.$this->p->cf['uca'].' Error</b> : 
					Submitted settings are not an array.', 'error' );
				return $opts;
			}
			// get default values, including css from default stylesheets
			$def_opts = $this->p->opt->get_defaults();
			$opts = $this->p->util->restore_checkboxes( $opts );
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

			add_settings_error( NGFB_OPTIONS_NAME, 'updated', '<b>'.$this->p->cf['uca'].' Info</b> : '.
				__( 'Plugin settings have been updated.', NGFB_TEXTDOM ).' '.
				sprintf( __( 'Wait %d seconds for cache objects to expire (default) or use the \'Clear All Cache\' button.' ), 
					$this->p->options['plugin_object_cache_exp'] ), 'updated' );

			return $opts;
		}

		public function save_site_options() {

			$page = empty( $_POST['page'] ) ? 
				key( $this->p->cf['lib']['network_setting'] ) : $_POST['page'];

			if ( ! current_user_can( 'manage_network_options' ) ) {
				$this->p->notice->err( __( 'Insufficient privileges to modify network options.', NGFB_TEXTDOM ), true );
				wp_redirect( $this->p->util->get_admin_url( $page ) );
				exit;
			} elseif ( ! isset( $_POST[ NGFB_NONCE ] ) || 
				! wp_verify_nonce( $_POST[ NGFB_NONCE ], plugin_basename( __FILE__ ) ) ) {
				$this->p->notice->err( __( 'Nonce token validation has failed.', NGFB_TEXTDOM ), true );
				wp_redirect( $this->p->util->get_admin_url( $page ) );
				exit;
			}

			$opts = empty( $_POST[NGFB_SITE_OPTIONS_NAME] ) ? 
				array() : $this->restore_checkboxes( $_POST[NGFB_SITE_OPTIONS_NAME] );

			if ( empty( $this->p->site_options['plugin_pro_tid'] ) ) {
				$this->p->update_error = '';
				delete_option( $this->p->cf['lca'].'_update_error' );
			}

			update_site_option( NGFB_SITE_OPTIONS_NAME, $opts );

			// store message in user options table
			$this->p->notice->inf( __( 'Plugin settings have been updated.', NGFB_TEXTDOM ), true );

			wp_redirect( $this->p->util->get_admin_url( $page ).'&settings-updated=true' );
			exit;
		}

		public function load_page() {
			wp_enqueue_script( 'postbox' );
			$upload_dir = wp_upload_dir();	// returns assoc array with path info
			$old_css_file = trailingslashit( $upload_dir['basedir'] ).'ngfb-social-buttons.css';
			$user_opts = $this->p->user->get_options();

			if ( ! empty( $_GET['settings-updated'] ) ) {

				// if the settings are being updated, and there is no Authentication ID,
				// then clear any update error messages
				if ( empty( $this->p->options['plugin_pro_tid'] ) ) {
					$this->p->update_error = '';
					delete_option( $this->p->cf['lca'].'_update_error' );

				// if the pro version plugin is installed, not active, and we have an
				// Authentication ID, then check for updates
				} elseif ( $this->p->is_avail['aop'] && 
					! $this->p->check->pro_active() && 
					! empty( $this->p->options['plugin_pro_tid'] ) )
						$this->p->update->check_for_updates();

			} elseif ( ! empty( $_GET['action'] ) ) {
				if ( empty( $_GET[ NGFB_NONCE ] ) ||
					! wp_verify_nonce( $_GET[ NGFB_NONCE ], plugin_basename( __FILE__ ) ) )
						$this->p->notice->err( __( 'Nonce token validation has failed.', NGFB_TEXTDOM ) );
				else {
					switch ( $_GET['action'] ) {
						case 'remove_old_css' : 
							if ( file_exists( $old_css_file ) )
								if ( @unlink( $old_css_file ) )
									add_settings_error( NGFB_OPTIONS_NAME, 'cssnotrm', 
										'<b>'.$this->p->cf['uca'].' Info</b> : The old <u>'.$old_css_file.'</u> 
											stylesheet has been removed.', 'updated' );
								else
									add_settings_error( NGFB_OPTIONS_NAME, 'cssnotrm', '<b>'.$this->p->cf['uca'].' Error</b> : '.
										sprintf( __( 'Error removing the old <u>%s</u> stylesheet.', NGFB_TEXTDOM ), $old_css_file ).
										__( 'Does the web server have sufficient privileges?', NGFB_TEXTDOM ), 'error' );
	
							break;
						case 'check_for_updates' : 
							if ( ! empty( $this->p->options['plugin_pro_tid'] ) ) {
								$this->p->admin->set_readme( 0 );
								$this->p->update->check_for_updates();
								$this->p->notice->inf( __( 'Plugin update information has been checked and updated.', NGFB_TEXTDOM ) );
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
							$this->p->notice->inf( __( 'Cached files, WP object cache, transient cache, and any additional caches, like APC, Memcache, Xcache, W3TC, Super Cache, etc. have all been cleared.', NGFB_TEXTDOM ) );
							break;
						case 'clear_metabox_prefs' : 
							$this->p->util->delete_metabox_prefs( get_current_user_id() );
							break;
					}
				}
			}

			if ( file_exists( $old_css_file ) ) {
				$this->p->notice->inf( 
					sprintf( __( 'The <u>%s</u> stylesheet is no longer used.', 
						NGFB_TEXTDOM ), $old_css_file ).' '.
					sprintf( __( 'Styling for social buttons is now managed on the <a href="%s">Social Style settings page</a>.', 
						NGFB_TEXTDOM ), $this->p->util->get_admin_url( 'style' ) ).' '.
					sprintf( __( 'When you are ready, you can <a href="%s">click here to remove the old stylesheet</a>.', 
						NGFB_TEXTDOM ), wp_nonce_url( $this->p->util->get_admin_url( '?action=remove_old_css' ),
							plugin_basename( __FILE__ ), NGFB_NONCE ) ) 
				);
			}

			$this->p->admin->settings[$this->menu_id]->add_meta_boxes();

			add_meta_box( $this->pagehook.'_news', __( 'News Feed', NGFB_TEXTDOM ), array( &$this, 'show_metabox_news' ), $this->pagehook, 'side' );
			add_meta_box( $this->pagehook.'_info', __( 'Plugin Information', NGFB_TEXTDOM ), array( &$this, 'show_metabox_info' ), $this->pagehook, 'side' );
			add_meta_box( $this->pagehook.'_help', __( 'Help and Support', NGFB_TEXTDOM ), array( &$this, 'show_metabox_help' ), $this->pagehook, 'side' );

			if ( $this->p->check->pro_active() )
				add_meta_box( $this->pagehook.'_thankyou', __( 'Pro Version', NGFB_TEXTDOM ), array( &$this, 'show_metabox_thankyou' ), $this->pagehook, 'side' );

			$this->p->admin->set_readme( $this->p->cf['update_hours'] * 3600 );	// the version info metabox on all settings pages needs this
		}

		public function show_page() {
			if ( $this->menu_id !== 'contact' )		// the "settings" page displays its own error messages
				settings_errors( NGFB_OPTIONS_NAME );	// display "error" and "updated" messages

			$this->p->debug->show_html( null, 'Debug Log' );

			// add meta box here to prevent removal
			if ( ! $this->p->check->pro_active() ) {
				add_meta_box( $this->pagehook.'_purchase', __( 'Pro Version', NGFB_TEXTDOM ), array( &$this, 'show_metabox_purchase' ), $this->pagehook, 'side' );
				add_filter( 'postbox_classes_'.$this->pagehook.'_'.$this->pagehook.'_purchase', array( &$this, 'add_class_postbox_highlight_side' ) );
			}

			?>
			<div class="wrap" id="<?php echo $this->pagehook; ?>">
				<?php screen_icon('options-general'); ?>
				<h2><?php echo $this->p->cf['full'].' : '.$this->menu_name; ?></h2>
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
			if ( ! empty( $this->p->cf['lib']['setting'][$this->menu_id] ) ) {
				echo '<form name="ngfb" id="settings" method="post" action="options.php">';
				echo $this->p->admin->form->get_hidden( 'options_version', $this->p->opt->options_version );
				echo $this->p->admin->form->get_hidden( 'plugin_version', $this->p->cf['version'] );
				settings_fields( $this->p->cf['lca'].'_settings' ); 

			} elseif ( ! empty( $this->p->cf['lib']['network_setting'][$this->menu_id] ) ) {
				echo '<form name="ngfb" id="settings" method="post" action="edit.php?action='.NGFB_SITE_OPTIONS_NAME.'">';
				echo '<input type="hidden" name="page" value="'.$this->menu_id.'">';
				echo $this->form->get_hidden( 'options_version', $this->p->opt->options_version );
				echo $this->form->get_hidden( 'plugin_version', $this->p->cf['version'] );
			}
			wp_nonce_field( plugin_basename( __FILE__ ), NGFB_NONCE );
			wp_nonce_field( 'closedpostboxes', 'closedpostboxesnonce', false );
			wp_nonce_field( 'meta-box-order', 'meta-box-order-nonce', false );

			do_meta_boxes( $this->pagehook, 'normal', null ); 

			// if we're displaying the "social" page, then do the social website metaboxes
			if ( $this->menu_id == 'social' ) {
				foreach ( range( 1, ceil( count( $this->p->admin->settings[$this->menu_id]->website ) / 2 ) ) as $row ) {
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
			}

			do_meta_boxes( $this->pagehook, 'bottom', null ); 

			echo $this->get_submit_button();
			echo '</form>', "\n";
		}

		protected function show_feed( $url, $max_num = 5, $class = 'rss_feed' ) {
			include_once( ABSPATH.WPINC.'/feed.php' );
			$have_items = 0;
			$rss_items = array();
			add_filter( 'wp_feed_cache_transient_lifetime', array( &$this, 'feed_cache_expire' ) );
			$rss_feed = fetch_feed( $url );		// since wp 2.8
			remove_filter( 'wp_feed_cache_transient_lifetime' , array( &$this, 'feed_cache_expire' ) );
			echo '<div class="', $class, '"><ul>';
			if ( is_wp_error( $rss_feed ) ) {
				$error_string = $rss_feed->get_error_message();
				echo '<li>', __( 'WordPress reported an error:', NGFB_TEXTDOM ), ' ', $error_string, '</li>';
			} else {
				$have_items = $rss_feed->get_item_quantity( $max_num ); 
				$rss_items = $rss_feed->get_items( 0, $have_items );
			}
			if ( $have_items == 0 ) {
				echo '<li>', __( 'No items found.', NGFB_TEXTDOM ), '</li>';
			} else {
				foreach ( $rss_items as $item ) {
					$desc = $item->get_description();
					$desc = preg_replace( '/^\.rss-manager [^<]*/m', '', $desc );		// remove the inline styling
					$desc = preg_replace( '/ cellspacing=["\'][0-9]*["\']/im', '', $desc );	// remove table cellspacing
					echo '<li><div class="title"><a href="', esc_url( $item->get_permalink() ), '" title="', 
						printf( 'Posted %s', $item->get_date('j F Y | g:i a') ), '">',
						esc_html( $item->get_title() ), '</a></div><div class="description">', 
						$desc, '</div></li>';
				}
			}
			echo '</ul></div>';
		}

		public function feed_cache_expire( $seconds ) {
			return $this->p->cf['update_hours'] * 3600;
		}

		public function show_metabox_news() {
			$this->show_feed( $this->p->cf['url']['feed'], 3, $this->p->cf['lca'].'_feed' );
		}

		public function show_metabox_info() {
			$stable_tag = __( 'N/A', NGFB_TEXTDOM );
			$latest_version = __( 'N/A', NGFB_TEXTDOM );
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
			echo '<table class="sucom-settings">';
			echo '<tr><th class="side">'.__( 'Installed', NGFB_TEXTDOM ).':</th>';
			echo '<td>'.$this->p->cf['version'].' (';

			if ( $this->p->check->pro_active() ) echo __( 'Pro', NGFB_TEXTDOM );
			elseif ( $this->p->is_avail['aop'] ) echo __( 'Unlicensed Pro', NGFB_TEXTDOM );
			else echo __( 'GPL', NGFB_TEXTDOM );

			echo ')</td></tr>';
			echo '<tr><th class="side">'.__( 'Stable', NGFB_TEXTDOM ).':</th><td>'.$stable_tag.'</td></tr>';
			echo '<tr><th class="side">'.__( 'Latest', NGFB_TEXTDOM ).':</th><td>'.$latest_version.'</td></tr>';
			echo '<tr><td colspan="2" id="latest_notice"><p>'.$latest_notice.'</p>';
			echo '<p><a href="'.$this->p->cf['url']['changelog'].'" target="_blank">'.__( 'See the Changelog for additional details...', NGFB_TEXTDOM ).'</a></p>';
			echo '</td></tr>';

			$action_buttons = '';
			if ( ! empty( $this->p->options['plugin_pro_tid'] ) )
				$action_buttons .= $this->p->admin->form->get_button( __( 'Check for Updates', NGFB_TEXTDOM ), 
					'button-primary', null, wp_nonce_url( $this->p->util->get_admin_url( '?action=check_for_updates' ), 
						plugin_basename( __FILE__ ), NGFB_NONCE ) );

			// don't show the 'Clear All Cache' on network admin pages
			if ( empty( $this->p->cf['lib']['network_setting'][$this->menu_id] ) )
				$action_buttons .= $this->p->admin->form->get_button( __( 'Clear All Cache', NGFB_TEXTDOM ), 
					'button-primary', null, wp_nonce_url( $this->p->util->get_admin_url( '?action=clear_all_cache' ),
						plugin_basename( __FILE__ ), NGFB_NONCE ) );

			if ( ! empty( $action_buttons ) )
				echo '<tr><td colspan="2"><p class="centered">'.$action_buttons.'</p></td></tr>';
			echo '</table>';
		}

		public function show_metabox_purchase() {
			echo '<table class="sucom-settings"><tr><td>';
			echo $this->p->msg->get( 'purchase_box' );
			echo '<p>Thank you,</p>';
			echo '<p class="sig">js.</p>';
			echo '<p class="centered">';
			echo $this->p->admin->form->get_button( 
				( $this->p->is_avail['aop'] ? 
					__( 'Purchase a Pro License', NGFB_TEXTDOM ) :
					__( 'Purchase the Pro Version', NGFB_TEXTDOM ) ), 
				'button-primary', null, $this->p->cf['url']['purchase'] );
			echo '</p></td></tr></table>';
		}

		public function show_metabox_thankyou() {
			echo '<table class="sucom-settings"><tr><td>';
			echo $this->p->msg->get( 'thankyou' );
			echo '<p class="sig">js.</p>';
			echo '</td></tr></table>';
		}

		public function show_metabox_help() {
			echo '<table class="sucom-settings"><tr><td>';
			echo $this->p->msg->get( 'help_boxes' );
			if ( $this->p->is_avail['aop'] == true )
				echo $this->p->msg->get( 'help_pro' );
			else echo $this->p->msg->get( 'help_free' );
			echo '<p class="centered" style="margin-top:15px;">';
			$img_size = $this->p->cf['img']['follow']['size'];
			foreach ( $this->p->cf['img']['follow']['src'] as $img => $url )
				echo '<a href="'.$url.'" target="_blank"><img src="'.NGFB_URLPATH.'images/'.$img.'" 
					width="'.$img_size.'" height="'.$img_size.'"></a> ';
			echo '</p></td></tr></table>';
		}

		protected function get_submit_button( $submit_text = '', $class = 'save-all-button' ) {
			if ( empty( $submit_text ) ) 
				$submit_text = __( 'Save All Changes', NGFB_TEXTDOM );
			return '<div class="'.$class.'"><input type="submit" class="button-primary" value="'.$submit_text.'" /></div>'."\n";
		}

	}
}

?>

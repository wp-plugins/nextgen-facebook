<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbAdminNetwork' ) && class_exists( 'NgfbAdmin' ) ) {

	class NgfbAdminNetwork extends NgfbAdmin {

		protected $p;
		protected $form;
		protected $menu_id;
		protected $menu_name;
		protected $pagehook;

		// executed by NgfbAdminAdvancedPro() as well
		public function __construct( &$plugin, $id, $name ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
			$this->menu_id = $id;
			$this->menu_name = $name;
			$this->setup_vars();
		}

		private function setup_vars() {
			$def_site_opts = $this->p->opt->get_site_defaults();
			$this->form = new SucomForm( $this->p, NGFB_SITE_OPTIONS_NAME, $this->p->site_options, $def_site_opts );
		}

		protected function add_meta_boxes() {
			// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
			add_meta_box( $this->pagehook.'_network', 'Network-Wide Settings', array( &$this, 'show_metabox_network' ), $this->pagehook, 'normal' );
		}

		public function show_metabox_network() {
			echo '<table class="sucom-settings">';
			foreach ( $this->get_rows( 'network' ) as $row )
				echo '<tr>'.$row.'</tr>';
			echo '</table>';
		}

		protected function get_rows( $id ) {
			$ret = array();
			$use = array( 'default' => 'As Default Value', 'empty' => 'If Value is Empty', 'force' => 'Force This Value' );
			$use_msg = esc_attr( 'Individual sites / blogs may use this value as a default when the plugin is first activated, 
			if the current site / blog option value is blank, or force every site / blog to use this value (disabling editing of this field).' );

			switch ( $id ) {
				case 'network' :
					if ( $this->p->is_avail['aop'] )
						$pro_msg = 'After purchasing a Pro version license, an email will be sent to you with a unique Authentication ID 
						and installation instructions. Enter the Authentication ID here to activate the Pro version features.';
					else
						$pro_msg = 'After purchasing the Pro version, an email will be sent to you with a unique Authentication ID 
						and installation instructions. Enter this Authentication ID here, and after saving the changes, an update 
						for '.$this->p->cf['full'].' will appear on the <a href="'.get_admin_url( null, 'update-core.php' ).'">WordPress 
						Updates</a> page. Update the \''.$this->p->cf['full'].'\' plugin to download and activate the Pro version.';
		
					$ret[] = $this->p->util->th( 'Pro Version Authentication ID', 'highlight', null, $pro_msg ).
					'<td>'.$this->form->get_input( 'plugin_pro_tid' ).'</td>'.
					'<td>All Sites Use <img src="'.NGFB_URLPATH.'images/question-mark.png" class="sucom_tooltip'.'" alt="'.
					$use_msg.'" /> '.$this->form->get_select( 'plugin_pro_tid_use', $use ).'</td>';

					break;

			}
			return $ret;
		}
	}
}

?>

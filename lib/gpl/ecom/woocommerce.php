<?php
/*
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.txt
Copyright 2012-2014 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'These aren\'t the droids you\'re looking for...' );

if ( ! class_exists( 'NgfbGplEcomWoocommerce' ) ) {

	class NgfbGplEcomWoocommerce {

		private $p;
		private $sharing;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();

			if ( isset( $this->p->is_avail['ssb'] ) &&
				$this->p->is_avail['ssb'] === true ) {
				$classname = __CLASS__.'Sharing';
				$this->sharing = new $classname( $this->p );
			}
		}
	}
}

if ( ! class_exists( 'NgfbGplEcomWoocommerceSharing' ) && class_exists( 'NgfbSharing' ) ) {

	class NgfbGplEcomWoocommerceSharing {

		private $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
			$this->p->util->add_plugin_filters( $this, array( 
				'get_defaults' => 1,
			) );

			if ( is_admin() ) {
				$this->p->util->add_plugin_filters( $this, array( 
					'style_tabs' => 1,
					'style_woo_short_rows' => 2,
					'sharing_show_on' => 2,
				) );
			}
		}

		/* Purpose: Create default options for the sanitation process, so it doesn't strip-out non-existing options */
		public function filter_get_defaults( $opts_def ) {
			$opts_def['buttons_css_woo_short'] = '/* Save an empty style text box to reload the default example styles.
 * These styles are provided as examples only - modifications may be 
 * necessary to customize the layout for your website. Social sharing
 * buttons can be aligned vertically, horizontally, floated, etc.
 */

.ngfb-woo_short-buttons { 
	display:block;
	margin:10px auto 10px auto;
	text-align:center;
}';
			// the default 'Show Button in' for 'Woo Short' is unchecked
			foreach ( $this->p->cf['opt']['pre'] as $name => $prefix )
				$opts_def[$prefix.'_on_woo_short'] = 0;
			return $opts_def;
		}

		/* Purpose: Include the 'Woo Short' checkbox in the 'Show Button in' options */
		public function filter_sharing_show_on( $show_on = array(), $prefix ) {
			$show_on['woo_short'] = 'Woo Short';
			$this->p->options[$prefix.'_on_woo_short:is'] = 'disabled';
			return $show_on;
		}

		/* Purpose: Add a 'bbPress Single' tab to the Style settings */
		public function filter_style_tabs( $tabs ) {
			$tabs['woo_short'] = 'Woo Short';
			return $tabs;
		}

		/* Purpose: Add css input textarea for the 'bbPress Single' style tab */
		public function filter_style_woo_short_rows( $rows, $form ) {
			$rows[] = '<td class="textinfo">
			<p>Social sharing buttons added to the <strong>WooCommerce Short Description</strong> 
			are assigned the \'ngfb-woo_short-buttons\' class, which itself contains the 
			\'ngfb-buttons\' class -- a common class for all the sharing buttons 
			(see the All Buttons tab).</p> 
			<p>Example:</p><pre>
.ngfb-woo_short-buttons 
    .ngfb-buttons
        .facebook-button { }</pre></td><td>'.
			'<td class="blank tall code">'.$form->get_hidden( 'buttons_css_woo_short' ).
				$this->p->options['buttons_css_woo_short'].'</td>';
			return $rows;
		}
	}
}

?>

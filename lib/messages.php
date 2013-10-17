<?php
/*
License: GPLv3
License URI: http://surniaulula.com/wp-content/plugins/nextgen-facebook/license/gpl.txt
Copyright 2012-2013 - Jean-Sebastien Morisset - http://surniaulula.com/
*/

if ( ! defined( 'ABSPATH' ) ) 
	die( 'Sorry, you cannot call this webpage directly.' );

if ( ! class_exists( 'ngfbMessages' ) ) {

	class ngfbMessages {

		protected $p;

		public function __construct( &$plugin ) {
			$this->p =& $plugin;
			$this->p->debug->mark();
		}

		public function get( $name = '' ) {
			$msg = '';
			switch ( $name ) {
				case 'pro_feature' :
					$msg = '<p class="pro_feature"><a href="'.$this->p->urls['plugin'].'" target="_blank">Upgrade 
					to the Pro version to enable the following features</a></p>';
					break;
				case 'pro_details' :
					$msg = '<p style="font-weight:bold;font-size:1.1em;">Would you like to... 
					Add support for <a href="https://dev.twitter.com/docs/cards" target="_blank">Twitter Cards</a>,
					including <em>Gallery, Photo, Large Image, Player and Product</em> Cards?<br/>';
					$msg .= 'Customize Open Graph and Twitter Card meta tags for each <em>individual</em> Post and Page?<br/>';
					$msg .= 'Change the Facebook, Google+ and Twitter social button language as the webpage switches language?<br/>';
					$msg .= 'Add tighter integration with 3rd party plugins like WordPress SEO, All-In-One SEO and WooCommerce?<br/>';
					$msg .= 'Improve page load times with file caching for <em>external</em> social images and JavaScript?<br/>';
					$msg .= '<p style="font-size:1.2em;">Help support '.$this->p->fullname.' by <a href="'.$this->p->urls['plugin'].'" 
					target="_blank">purchasing the Pro version today</a>.</p>';
					$msg .= '<p>Upgrading to the Pro version is easy and simple! Enter the unique <em>Authentication ID</em> 
					(that you\'ll receive by email) on the Advanced settings page, and update the plugin from within WordPress.</p>';
					break;
				case 'purchase_box' :
					$msg = '<p>'.$this->p->fullname.' has taken many, many months of long days to develop and fine-tune.
					If you compare this plugin with others, I think you\'ll agree that the result was worth the effort.
					Please show your appreciation by <a href="'.$this->p->urls['plugin'].'" target="_blank">purchasing 
					the Pro version</a>.</p>';
					break;
				case 'thankyou' :
					$msg = '<p>Thank you for your purchase! I hope the '.$this->p->fullname.' plugin will exceed all of your expectations.</p>';
					break;
				case 'help_boxes' :
					$msg = '<p>Individual option boxes (like this one) can be opened / closed by clicking on their title bar, 
					moved and re-ordered by dragging them, and removed / added from the <em>Screen Options</em> tab (top-right).
					Values in multiple tabs can be edited before clicking the \'Save All Changes\' button.</p>';
					break;
				case 'help_free' :
					$msg = '<p><strong>Need help with the <em>Free</em> version? 
					See the <a href="'.$this->p->urls['faq'].'" target="_blank">FAQ</a>, 
					the <a href="'.$this->p->urls['notes'].'" target="_blank">Other Notes</a>, or visit the 
					<a href="'.$this->p->urls['forum'].'" target="_blank">Support Forum</a> on WordPress.org</strong>.</p>';
					break;
				case 'help_pro' :
					$msg = '<p><strong>Need help with the Pro version? 
					See the <a href="'.$this->p->urls['pro_faq'].'" target="_blank">FAQ</a>, 
					the <a href="'.$this->p->urls['pro_notes'].'" target="_blank">Other Notes</a>, or 
					<a href="'.$this->p->urls['pro_request'].'" target="_blank">Submit a Request</a>.</p>';
					break;
			}
			return $msg;
		}

	}

}
?>

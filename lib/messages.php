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

		private $ngfb;		// ngfbPlugin

		public function __construct( &$ngfb_plugin ) {
			$this->ngfb =& $ngfb_plugin;
			$this->ngfb->debug->mark();
		}

		public function get( $name = '' ) {
			$msg = '';
			switch ( $name ) {
				case 'pro_feature' :
					$msg = '<p class="pro_feature"><a href="'.$this->ngfb->urls['plugin'].'" target="_blank">Upgrade 
						to the Pro version to enable the following features</a>.</p>';
					break;
				case 'pro_details' :
					$msg = '<p style="font-weight:bold;font-size:1.2em;">Would you like to... 
						Customize the <em>Open Graph</em> meta tags for each <em>individual</em> Post and Page?<br/>
						Add support for <em><a href="https://dev.twitter.com/docs/cards" target="_blank">Twitter Cards</a></em>, 
						including the Gallery, Photo, Player and Large Image Cards?<br/>
						Improve page load times with file caching for social button images and JavaScript?<br/>
						<p style="font-size:1.2em;">Help support '.$this->ngfb->fullname.' by <a href="'.$this->ngfb->urls['plugin'].'" 
						target="_blank">purchasing the Pro version today</a>.</p>
						<p>Upgrading is easy -- simply enter your <em>Purchase Transaction ID</em> (that you\'ll receive by email) 
						on the Advanced settings page, then install the Pro update from within WordPress.</p>';
					break;
				case 'purchase_box' :
					$msg = '<p>'.$this->ngfb->fullname.' has taken many, many months of long days to develop and fine-tune.
						If you compare this plugin with others, I think you\'ll agree that the result was worth the effort.
						Please help continue that work by <a href="'.$this->ngfb->urls['plugin'].'" target="_blank">purchasing 
						the Pro version</a>.</p>';
					break;
				case 'review_plugin' :
					$msg = '<p>You can also help other WordPress users find out about this plugin by 
						<a href="'.$this->ngfb->urls['review'].'" target="_blank">reviewing and rating the plugin</a> 
						on WordPress.org. A short \'<em>Thank you.</em>\' is all it takes, and your feedback is always greatly appreciated.</p>';
					break;
				case 'thankyou' :
					$msg = '<p>Thank you for your purchase! I hope the '.$this->ngfb->fullname.' plugin will exceed all of your expectations.</p>';
					break;
				case 'help_boxes' :
					$msg = '<p>Individual option boxes (like this one) can be opened / closed by clicking on their title bar, 
						moved and re-ordered by dragging them, and removed / added from the <em>Screen Options</em> tab (top-right).</p>';
					break;
				case 'help_forum' :
					$msg = '<p>Need help? Visit the <a href="'.$this->ngfb->urls['support_forum'].'" target="_blank">Support Forum</a> on WordPress.org.</p>';
					break;
				case 'help_email' :
					$msg = '<p>Need help with the Pro version? Visit my website at <a href="'.$this->ngfb->urls['website'].'" target="_blank">surniaulula.com</a>,
						or contact me by email at <a href="mailto:'.$this->ngfb->urls['email'].'" target="_blank">'.$this->ngfb->urls['email'].'</a>.</p>';
					break;
			}
			return $msg;
		}

	}

}
?>

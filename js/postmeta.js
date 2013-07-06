function ngfbTextLen( id ) {
	var text = jQuery.trim( ngfbClean( jQuery('#'+id).val() ) );
	var len = text.length;
	var max = jQuery('#'+id).attr('maxLength');
	var html = '<div class="max_chars">'+ngfbLenSpan(len, max)+' of '+max+' characters maximum</span>';
	jQuery('#'+id+'-length').html(html);
}

function ngfbLenSpan( len, max ) {
	var diff = max - len;
	var classname = '';
	if (diff > 10) classname = 'good';
	else if (diff > 0) classname = 'warn';
	else classname = 'bad';
	return '<span class="'+classname+'">'+len+'</span>';
}

function ngfbClean( str ) {
	if ( str == '' || str == undefined )
		return ''; 
	try {
		str = str.replace(/<\/?[^>]+>/gi, '');
		str = str.replace(/\[(.+?)\](.+?\[\/\\1\])?/, '');
	} catch(e) {} 
	return str;
}

jQuery(document).ready(function(){

	var active_tab = window.location.hash;
	if ( active_tab == '' || active_tab.search('ngfb') == -1 )
		active_tab = 'ngfb_default';
	else active_tab = active_tab.replace('#',''); 

	jQuery('.'+active_tab).addClass('active'); 

	jQuery('a.ngfb-tablink').click( function($) {
		jQuery('.ngfb-metabox-tabs li').removeClass('active');
		jQuery('.ngfb-tab').removeClass('active'); 

		var id = jQuery(this).attr('href').replace('#','');
		jQuery('.'+id).addClass('active');
		jQuery(this).parent().addClass('active'); 
		
		jQuery( "html, body" ).animate({
			scrollTop: jQuery( '#ngfb_meta' ).offset().top
		}, 500);
	});

	jQuery('.ngfb-metabox-tabs').show();

	jQuery('#ngfb_og_title').focus( function() { ngfbTextLen('ngfb_og_title'); });
	jQuery('#ngfb_og_title').keyup( function() { ngfbTextLen('ngfb_og_title'); });

	jQuery('#ngfb_og_desc').focus( function() { ngfbTextLen('ngfb_og_desc'); });
	jQuery('#ngfb_og_desc').keyup( function() { ngfbTextLen('ngfb_og_desc'); });

	jQuery('#ngfb_link_desc').focus( function() { ngfbTextLen('ngfb_link_desc'); });
	jQuery('#ngfb_link_desc').keyup( function() { ngfbTextLen('ngfb_link_desc'); });

	jQuery('#ngfb_tc_desc').focus( function() { ngfbTextLen('ngfb_tc_desc'); });
	jQuery('#ngfb_tc_desc').keyup( function() { ngfbTextLen('ngfb_tc_desc'); });

	jQuery('#ngfb_pin_desc').focus( function() { ngfbTextLen('ngfb_pin_desc'); });
	jQuery('#ngfb_pin_desc').keyup( function() { ngfbTextLen('ngfb_pin_desc'); });
	
	jQuery('#ngfb_tumblr_img_desc').focus( function() { ngfbTextLen('ngfb_tumblr_img_desc'); });
	jQuery('#ngfb_tumblr_img_desc').keyup( function() { ngfbTextLen('ngfb_tumblr_img_desc'); });
	
	jQuery('#ngfb_tumblr_vid_desc').focus( function() { ngfbTextLen('ngfb_tumblr_vid_desc'); });
	jQuery('#ngfb_tumblr_vid_desc').keyup( function() { ngfbTextLen('ngfb_tumblr_vid_desc'); });
	
	jQuery('#ngfb_twitter_desc').focus( function() { ngfbTextLen('ngfb_twitter_desc'); });
	jQuery('#ngfb_twitter_desc').keyup( function() { ngfbTextLen('ngfb_twitter_desc'); });
	
});

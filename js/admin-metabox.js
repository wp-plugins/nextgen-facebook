function ngfbTextLen( id ) {
	var text = jQuery.trim( ngfb_clean( jQuery('#'+id).val() ) );
	var len_max = jQuery('#'+id).attr('maxLength');
	var len = text.length;
	var html = '';

	if (len <= len_max) html = '<span class="good">'+len+'</span>';
	else html = '<span class="bad">'+len+'</span>';

	jQuery('#'+id+'-length').html(html);

	if ( id == 'ngfb_og_desc' ) {
		var gs_max = 156;
		var tc_max = 200;
		var fb_max = 300;
		var html = '';
	
		html += 'Google Search ';
		if (len <= gs_max) html += '<span class="good">ok</span>';
		else html += '<span class="bad">exceeded</span>';
	
		html += ', Twitter Cards ';
		if (len <= tc_max) html += '<span class="good">ok</span>';
		else html += '<span class="bad">exceeded</span>';
	
		html += ', Facebook ';
		if (len <= fb_max) html += '<span class="good">ok</span>';
		else html += '<span class="bad">exceeded</span>';
	
		jQuery('#'+id+'-info').html(' ('+html+')');
	}
}

function ngfb_clean( str ) {
	if ( str == '' || str == undefined )
		return ''; 
	try {
		str = str.replace(/<\/?[^>]+>/gi, '');
		str = str.replace(/\[(.+?)\](.+?\[\/\\1\])?/, '');
	} catch(e) {} 
	return str;
}

jQuery(document).ready(function(){
	jQuery('#ngfb_og_desc').keyup( function() {
		ngfbTextLen('ngfb_og_desc');
	});
	jQuery('#ngfb_og_desc').live('change', function() {
		ngfbTextLen('ngfb_og_desc');
	});
	ngfbTextLen('ngfb_og_desc');

	jQuery('#ngfb_tweet').keyup( function() {
		ngfbTextLen('ngfb_tweet');
	});
	jQuery('#ngfb_tweet').live('change', function() {
		ngfbTextLen('ngfb_tweet');
	});
	ngfbTextLen('ngfb_tweet'); 
	
	jQuery(".ngfb_tooltip").qtip({
		position:{
			corner:{
				target:'topMiddle',
				tooltip:'bottomLeft',
			},
		},
		style:{
			name:'blue',
			tip:'bottomLeft',
			width:{
				min:'500',
				max:'500',
			},
			classes:{
				content:'ngfb-qtip-content',
			},
		},
	});
});

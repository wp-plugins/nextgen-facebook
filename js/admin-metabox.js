
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
	jQuery('#ngfb_og_title').keyup( function() { ngfbTextLen('ngfb_og_title'); });
	jQuery('#ngfb_og_title').live('change', function() { ngfbTextLen('ngfb_og_title'); });

	jQuery('#ngfb_og_desc').keyup( function() { ngfbTextLen('ngfb_og_desc'); });
	jQuery('#ngfb_og_desc').live('change', function() { ngfbTextLen('ngfb_og_desc'); });

	jQuery('#ngfb_link_desc').keyup( function() { ngfbTextLen('ngfb_link_desc'); });
	jQuery('#ngfb_link_desc').live('change', function() { ngfbTextLen('ngfb_link_desc'); });

	jQuery('#ngfb_tc_desc').keyup( function() { ngfbTextLen('ngfb_tc_desc'); });
	jQuery('#ngfb_tc_desc').live('change', function() { ngfbTextLen('ngfb_tc_desc'); });

	jQuery('#ngfb_tweet').keyup( function() { ngfbTextLen('ngfb_tweet'); });
	jQuery('#ngfb_tweet').live('change', function() { ngfbTextLen('ngfb_tweet'); });
	
	jQuery(".ngfb_tooltip").qtip({
		position:{
			corner:{
				target:'topMiddle',
				tooltip:'bottomLeft',
			},
			adjust:{
			},
		},
		show:{
			when:{
			},
		},
		hide:{
			fixed:true,
		},
		style:{
			name:'blue',
			tip:'bottomLeft',
			width:{
				min:'500',
				max:'500',
			},
			classes:{
				tooltip:'ngfb-qtip',
			},
		},
	});
});


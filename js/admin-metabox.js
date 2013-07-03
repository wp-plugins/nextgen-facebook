
function ngfbTextLen( id ) {
	var text = jQuery.trim( ngfbClean( jQuery('#'+id).val() ) );
	var len = text.length;
	var max = jQuery('#'+id).attr('maxLength');

	jQuery('#'+id+'-length').html(ngfbLenSpan(len, max, 'len'));

	if ( id == 'ngfb_og_desc' ) {
		jQuery('#'+id+'-diff-gs').html(ngfbLenSpan(len, 156, 'diff'));
		jQuery('#'+id+'-diff-tc').html(ngfbLenSpan(len, 200, 'diff'));
		jQuery('#'+id+'-diff-fb').html(ngfbLenSpan(len, 300, 'diff'));
	}
}

function ngfbLenSpan( len, max, want ) {
	var diff = max - len;
	var classname = '';
	var show = len;

	if ( want == 'diff' ) show = diff;
	if (diff > 10) classname = 'good';
	else if (diff > 0) classname = 'warn';
	else classname = 'bad';
	return '<span class="'+classname+'">'+show+'</span>';
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


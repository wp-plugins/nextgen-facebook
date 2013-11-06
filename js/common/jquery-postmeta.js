function sucomTextLen( id ) {
	var text = jQuery.trim( sucomClean( jQuery('#'+id).val() ) );
	var len = text.length;
	var max = jQuery('#'+id).attr('maxLength');
	var html = '<div class="max_chars">'+sucomLenSpan(len, max)+' of '+max+' characters maximum</span>';
	jQuery('#'+id+'-length').html(html);
}

function sucomLenSpan( len, max ) {
	var diff = max - len;
	var classname = '';
	if (diff > 10) classname = 'good';
	else if (diff > 0) classname = 'warn';
	else classname = 'bad';
	return '<span class="'+classname+'">'+len+'</span>';
}

function sucomClean( str ) {
	if ( str == '' || str == undefined )
		return ''; 
	try {
		str = str.replace(/<\/?[^>]+>/gi, '');
		str = str.replace(/\[(.+?)\](.+?\[\/\\1\])?/, '');
	} catch(e) {} 
	return str;
}

function sucomTabs( prefix, default_tab, scroll_to ) {
	var default_tab = typeof default_tab !== "undefined" ? 
		'sucom-tab'+prefix+'_'+default_tab : 'sucom-tab_default';

	var active_tab = window.location.hash;
	if ( active_tab == '' )
		active_tab = default_tab;
	else if ( prefix !== '' && ( active_tab.search('sucom-tab_') == -1 || active_tab.search(prefix) == -1 ) )
		active_tab = default_tab;
	else if ( prefix == '' && active_tab.search('sucom-tab_') == -1 )
		active_tab = default_tab;
	else active_tab = active_tab.replace('#',''); 

	jQuery('.'+active_tab).addClass('active'); 

	jQuery('a.sucom-tablink'+prefix).click( function($) {
		jQuery('.sucom-metabox-tabs'+prefix+' li').removeClass('active');
		jQuery('.sucom-tab'+prefix).removeClass('active'); 

		var href_id = jQuery(this).attr('href').replace('#','');
		jQuery('.'+href_id).addClass('active');
		jQuery(this).parent().addClass('active'); 
	
		if ( scroll_to )
			jQuery( "html, body" ).animate({
				scrollTop: jQuery(scroll_to).offset().top
			}, 500);
	});
	jQuery('.sucom-metabox-tabs').show();
};

function ngfbDescLen( desc ) {
	var desc = jQuery.trim( ngfb_clean( jQuery("#ngfb_og_desc").val() ) );
	var max = jQuery("#ngfb_og_desc").attr("maxLength");
	var len = desc.length;

	if (len <= max)
		len = '<span class="good">' + len + '</span>';
	else
		len = '<span class="bad">' + len + '</span>';

	jQuery('#ngfb_og_desc-length').html(len);
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
		ngfbDescLen();
	});
	jQuery('#ngfb_og_desc').live('change', function() {
		ngfbDescLen();
	});
	ngfbDescLen();
});

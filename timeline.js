jQuery(document).ready(function() {
	jQuery.post(wpt_js.base_url+'/wp-admin/admin-ajax.php', {'action':'get_slides'}, function(response) {
		json_resp = jQuery.parseJSON(response);
		createStoryJS({
				width:		'100%',
				height:		'600',
				source:		json_resp,
				embed_id:	'wpt_timeline',
				debug:		true
			});
	});
});


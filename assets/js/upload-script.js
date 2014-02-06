// JavaScript Document
// show upload form and buttons
function uploaderWeb(container){	
		formfield = jQuery('#'+container).attr('name');
		tb_show('', 'media-upload.php?type=image&amp;TB_iframe=true');
		//return false;
	
	window.send_to_editor = function(html){
		imgurl = jQuery('img', html).attr('src');
		jQuery('#'+container).val(imgurl);
		tb_remove();
	}
}

// show company details .
/*function showModel(idmodel){
	tb_show('Model Details', '/brooks/wp-content/plugins/sammybrooks/sammy_details_model.php?id='+idmodel);
}*/



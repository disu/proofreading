// https://gist.github.com/RichiUfo/db81eee918c8112f20c2d2b3cb1781f0

function tmce_getContent(editor_id, textarea_id) {
	if ( typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;
	if ( typeof textarea_id == 'undefined' ) textarea_id = editor_id;

	if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
		return tinyMCE.get(editor_id).getContent();
	}else{
		return jQuery('#'+textarea_id).val();
	}
}

function tmce_setContent(content, editor_id, textarea_id) {
	if ( typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;
	if ( typeof textarea_id == 'undefined' ) textarea_id = editor_id;

	if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
		return tinyMCE.get(editor_id).setContent(content);
	}else{
		return jQuery('#'+textarea_id).val(content);
	}
}

function tmce_getSelection(editor_id, textarea_id, start, length) {
	if ( typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;
	if ( typeof textarea_id == 'undefined' ) textarea_id = editor_id;

	if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
		return tinyMCE.get(editor_id).selection.getContent();
	}else{
		return jQuery('#'+textarea_id).val(content);
	}
}

function tmce_focus(editor_id, textarea_id) {
	if ( typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;
	if ( typeof textarea_id == 'undefined' ) textarea_id = editor_id;

	if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
		return tinyMCE.get(editor_id).focus();
	}else{
		return jQuery('#'+textarea_id).focus();
	}
}
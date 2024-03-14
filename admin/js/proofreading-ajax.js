window.webdev_ajax = ( function( window, document, jQuery ){
	var app = {};

	app.cache = function(){
		app.$analyze_button = jQuery( '#proofreading_analyze' );
		app.$spinner = jQuery( '#proofreading_box .spinner' );
		app.$analyze_result = jQuery( '#proofreading_analyze_results' );
	};

	app.init = function(){
		app.cache();
		app.$analyze_button.on( 'click', app.analyze_button_handler );
	};

	app.post_ajax_analyze = function( post_text, language ){
		
		app.$spinner.css('visibility', 'visible');
		
		var post_data = {
			action	   : 'analyze',
			nonce      : proofreading.nonce,
			post_text  : post_text,
			language   : language
		};

		jQuery.post(
			ajaxurl,
			post_data,
			function( response_data ){
				if( response_data.success ){
					proofreading.nonce = response_data.data.nonce;
					
					var response = JSON.parse(response_data.data.result);
					if (response == null || response.error != null || response.matches == null)
					{
						var error = '';
						if (response == null)
							error = proofreading.trans_result_error;
						else{
							switch(response.error){
								case 'TOO_MANY_ERRORS' : error = proofreading.trans_result_error_too_many_errors; break;
								case 'INVALID_REQUEST' : error = proofreading.trans_result_error_invalid_request; break;
								default: error = proofreading.trans_result_error; break;
							}
						}
						app.$analyze_result.html('<p class="proofreading_error">' + error + '</p>');
					}
					else{
						if (response.matches.length == 0)
							app.$analyze_result.html('<p id="proofreading_success">' + proofreading.trans_result_ok + '</p>');
						else{
							var result_text = '<p class="proofreading_error" id="proofreading_errors_number">' + proofreading.trans_result_errors_to_correct + ': <span>' + response.matches.length + '</span></p>'+
								'<p id="proofreading_hidden_errors_number">' + proofreading.trans_result_errors_hidden + ': <span></span></p>'+
								'<ul class="proofreading_corrections">';
							var post_text = tmce_getContent('content', {format : 'text'});
							
							response.matches.forEach(function(match) {
								result_text = result_text + '<li>'+
										'<span class="proofreading_correction_button"></span> '+
										'<pre class="proofreading_correction_text">' + match.context.text.substring(match.context.offset, match.context.offset + match.context.length) + '</pre>'+
										'<div class="proofreading_correction_message" data-errorcategory="' + match.rule.category.id + '">' + match.message + '</div>'+
										'<div class="proofreading_correction_sentence" style="display:none">' + match.sentence + '</div>'+
									'</li>';
								return;
								
							});
							app.$analyze_result.html(result_text + '</ul>');
							
							if ( jQuery('#wpb_visual_composer').length == 0 || jQuery('#wpb_visual_composer').is(":hidden") ) {
								var btnShow = jQuery('<button/>', {
									title: proofreading.trans_btn_show_error,
									class: 'dashicons dashicons-editor-alignleft',
									click: function (e) {
										e.preventDefault();
										var sentence = jQuery(this).closest('li').find('.proofreading_correction_sentence').html();
										tmce_setSelection('content', sentence);
									}
								});
								jQuery('.proofreading_correction_button').append(btnShow);
							}
							
							var btnHide = jQuery('<button/>', {
								title: proofreading.trans_btn_hide_error,
								class: 'dashicons dashicons-hidden',
								click: function (e) {
									e.preventDefault();
									jQuery(this).closest('li').fadeOut('medium', function(){
										updateCounters();
									});
								}
							});
							jQuery('.proofreading_correction_button').append(btnHide);
							
							var btnHideCategory = jQuery('<button/>', {
								title: proofreading.trans_btn_hide_error_category,
								class: 'dashicons dashicons-feedback',
								style: 'margin-left:10px',
								click: function (e) {
									var category = jQuery(this).closest('li').find('.proofreading_correction_message').data('errorcategory');
									console.log('cat: '+ category);
									jQuery(this).closest('ul').find('li').each(function(){
										var exist = jQuery(this).find(".proofreading_correction_message[data-errorcategory='"+ category +"']").length;
										if (exist) jQuery(this).fadeOut('medium', function(){
											updateCounters();
										});
									});
									e.preventDefault();
								}
							});
							jQuery('.proofreading_correction_button').append(btnHideCategory);
							
							
							var btnShowHidden = jQuery('<button/>', {
								title: proofreading.trans_btn_show_error_hidden,
								class: 'dashicons dashicons-visibility',
								style: 'margin-left:10px',
								click: function (e) {
									e.preventDefault();
									jQuery('.proofreading_corrections li').each(function(){
										jQuery(this).fadeIn('medium');
									});
									updateCounters();
								}
							});
							jQuery('#proofreading_hidden_errors_number').append(btnShowHidden);
						}
						
						var node = tinymce.activeEditor.selection.getNode();
					}
					
				} else {
					app.$analyze_result.html('<p class="proofreading_error">Generic error</p>');
				}
				
				app.$spinner.css('visibility', 'hidden');
				
				function updateCounters() {
					var number = jQuery('ul.proofreading_corrections li').filter(":visible").length;
					jQuery('#proofreading_errors_number span').html(number);
					
					var number_hidden = jQuery('ul.proofreading_corrections li').filter(":hidden").length;
					if (number_hidden > 0){
						jQuery('#proofreading_hidden_errors_number span').html(number_hidden);
						jQuery('#proofreading_hidden_errors_number').show();
					}
					else
						jQuery('#proofreading_hidden_errors_number').hide();
				}
			},
			'json' )
	};

	app.analyze_button_handler = function( evt ){
		evt.preventDefault();
		app.$analyze_result.html('');
		
		var is_tinymce_active = (typeof tinyMCE != "undefined") && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden();
		if (is_tinymce_active !== true){
			app.$analyze_result.html('<p id="proofreading_warning">' + proofreading.trans_warn_visual_mode + '</p>');
			return;
		}
		//alert('is_tinymce_active:' + is_tinymce_active);
		
		var post_text = tmce_getContent('content', {format : 'text'});
		var language = jQuery('#language').val();
		app.post_ajax_analyze( post_text, language );
	};

	jQuery(document).ready( app.init );

	return app;

})( window, document, jQuery );



// https://gist.github.com/RichiUfo/db81eee918c8112f20c2d2b3cb1781f0
function tmce_getContent(editor_id, format) {
	if ( typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;

	if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
		return tinyMCE.get(editor_id).getContent(format);
	}
}

function tmce_setSelection(editor_id, phrase) {
	if ( typeof editor_id == 'undefined' ) editor_id = wpActiveEditor;
	
	if ( jQuery('#wp-'+editor_id+'-wrap').hasClass('tmce-active') && tinyMCE.get(editor_id) ) {
		
		var ed = tinyMCE.get(editor_id);
		phrase = phrase.replaceAll('\n','');
		var marker = jQuery(ed.getBody()).find('p:contains('+ phrase +')');
		var selection = ed.selection.select(marker.get(0))
		if (selection != undefined) selection.focus();
	}
}

String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.replace(new RegExp(search, 'g'), replacement);
};
(function( jQuery ) {
	'use strict';

	jQuery( document ).ready(function() {
	
		// Settings page
		if (jQuery('.proofreading-settings').length){
			
			jQuery('#select_all_rules').change(function() {
				if(this.checked)
					jQuery('.rule').each(function(){ jQuery(this).prop('checked', 'checked') });
				else
					jQuery('.rule').each(function(){ jQuery(this).prop('checked', '') });
			});
			
			loadLanguageRules(jQuery('#proofreading-language-rules-settings').val());
			
			jQuery('#proofreading-language-rules-settings').change(function() {
				loadLanguageRules(jQuery(this).val());
			});

			function loadLanguageRules(lang_code) {
				var post_data = {
					action	   : 'select_lang_rules',
					nonce      : proofreading.nonce,
					lang_code  : lang_code
				};
				
				jQuery.post(
					ajaxurl,
					post_data,
					function( response ){
						
						if( response.success ){
							var html = '';
							if (response.data.result.length == 0 || response.data.error != null)
							{
								jQuery('.select_all_rules_row').hide();
								html += '<tr><td>No rules</td></tr>';
							}
							else{
								jQuery('.select_all_rules_row').show();
								for(var i = 0; i < response.data.result.length; i++){
									var rule = response.data.result[i];
									html += '<tr><td><label for="'+ rule.key + '"><input type="checkbox" class="rule" id="'+ rule.key + '" name="rules[]" value="'+ rule.key + '"';
									if (rule.included) html += ' checked = "checked"';
									html += '> '+ rule.name + '</label></td></tr>';
								}
							}
							jQuery('.rules_container').html(html);
							
						} else {
							app.$analyze_result.html('<p class="error">Generic error</p>');
						}
					},
					'json');
			};
		}
	});

})( jQuery );
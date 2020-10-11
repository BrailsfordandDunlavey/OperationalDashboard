jQuery(document).ready(function($){
	var $formActions = jQuery(document.getElementById('frm_notification_settings'));
	$formActions.on('click', '.frmapi_test_connection', frmapi_test_connection);
	$formActions.on('click', '.frmapi_insert_default_json', frmapi_insert_json);
	$formActions.on('click', '.frmapi_add_data_row', frmapi_add_data_row );
	$formActions.on('change', '.frmapi_data_format', frmapi_toggle_options );
});

function frmapi_test_connection(){
	var settings = jQuery(this).closest('.frm_single_api_settings');
	var key = settings.data('actionkey');
	var baseName = 'frm_api_action['+key+'][post_content]';

	var url = jQuery('input[name="'+baseName+'[url]"]').val();
	var key = jQuery('input[name="'+baseName+'[api_key]"]').val();
	var testResponse = settings.find('.frmapi_test_resp'),
		button = this;

	if (url == '') {
		settings.find('.frmapi_test_connection').html('Please enter a URL');
		return;
	} else if ( url.indexOf('[') != '-1' ) {
		testResponse.html('Sorry, Dynamic URLs cannot be tested');
		return;
	}

	button.classList.add( 'frm_loading_button' );
	testResponse.html('');

	jQuery.ajax({
		type:'POST',url:ajaxurl,
		data:{action:'frmapi_test_connection',url:url,key:key},
		success:function(html){
			testResponse.html(html);
			button.classList.remove( 'frm_loading_button' );
		}
	});
}

function frmapi_insert_json(){
	var form_id = jQuery('input[name="id"]').val();
	var settings = jQuery(this).closest('.frm_single_api_settings');
	var key = settings.data('actionkey');
	var baseName = 'frm_api_action['+key+'][post_content]';

	if (form_id == '') {
		jQuery('textarea[name="'+baseName+'[data_format]"]').val('');
		return;
	}

	jQuery.ajax({
		type:'POST',url:ajaxurl,
		data:'action=frmapi_insert_json&form_id='+form_id,
		success:function(html){
			jQuery('textarea[name="'+baseName+'[data_format]"]').val(html);
		}
	});
}

function frmapi_add_data_row(){
	var table = jQuery(this).closest('.frmapi_data_rows'),
		actionId = jQuery(this).closest('.frm_form_action_settings').data('actionkey'),
		rowNum = table.find('.frm_postmeta_row:last').attr('id').replace('frm_api_data_', '').replace( '_' + actionId, '' ),
		nextRowNum = parseInt( rowNum ) + 1;

	var newRow = frmapiRowMarkup( {
		id: actionId,
		row: nextRowNum
	} );

	table.append( newRow );
	var addedRow = '#frm_api_data_' + nextRowNum + '_' + actionId;
	jQuery( document ).trigger( 'frmElementAdded', [ addedRow ] );
}

function frmapiRowMarkup( action ) {
	return `
	<div id="frm_api_data_${action.row}_${action.id}" class="frm_postmeta_row frm_grid_container">
		<div class="frm4 frm_form_field">
			<label class="screen-reader-text" for="frm_api_data_key_${action.row}_${action.id}">
				Name
			</label>
			<input type="text" value="" name="frm_api_action[${action.id}][post_content][data_fields][${action.row}][key]" id="frm_api_data_key_${action.row}_${action.id}" class="frm_not_email_message" />
		</div>
		<div class="frm7 frm_form_field">
			<label class="screen-reader-text" for="frm_api_data_value_${action.row}_${action.id}">
				Value
			</label>
			<input type="text" name="frm_api_action[${action.id}][post_content][data_fields][${action.row}][value]" value="" id="frm_api_data_value_${action.row}_${action.id}" class="frm_not_email_message" />
		</div>
		<div class="frm1 frm_form_field frm-inline-select">
			<a href="#" class="frm_remove_tag frm_icon_font" data-removeid="frm_api_data_${action.row}_${action.id}"></a>
			<a href="#" class="frm_add_tag frm_icon_font frmapi_add_data_row"></a>
		</div>
	</div>`;
}

function frmapi_toggle_options(){
	var val = this.value;
	var settings = jQuery(this).closest('.frm_single_api_settings');
	if ( val == 'raw' ) {
		settings.find('.frm_data_raw').show();
		settings.find('.frm_data_json').hide();
	} else {
		settings.find('.frm_data_raw').hide();
		settings.find('.frm_data_json').show();
	}
}

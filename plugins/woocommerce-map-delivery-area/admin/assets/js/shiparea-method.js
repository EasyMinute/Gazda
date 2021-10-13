/******************************************
	Add a row in the admin table.
	This clone the last row and append it
*******************************************/
jQuery('#shiparea_admin_table').on( 'click', 'a.add', function(e){
	e.preventDefault();

	//vars
	var $tr = jQuery('table#shiparea_admin_table tbody').children('tr').last(),
		$add = $tr.clone(),
		old_id = $add.attr('id'),
		new_id = 'trarea_' + ( parseInt( old_id.replace('trarea_', ''), 10 ) + 1);

	$add.find('.select2-container').remove();

	$add.find('.areamaps').selectWoo({
				options : shiparea_var.areamaps,
				//plugins: ['remove_button'],
			});

	// update names
	$add.find('[name]').each(function(){
		jQuery(this).attr('name', jQuery(this).attr('name').replace( old_id, new_id ));
	});

	// update data-i
	$add.attr( 'id', new_id );

	// add tr
	$tr.after( $add );

	return false;
});

/*************************************
	Avoid the first row be removed
	when click the button Remove All
**************************************/
jQuery('#shiparea_admin_table').on( 'click', 'a.remove_all', function(){

	jQuery('table#shiparea_admin_table.wc_input_table tbody tr#trarea_0').removeClass( 'current' );

	jQuery('table#shiparea_admin_table.wc_input_table tbody tr').filter( ':not(tr#trarea_0)' ).each(function() {
		jQuery( this ).addClass( 'current' );
	});

	jQuery( 'a.remove_rows' ).trigger( 'click' );

	return false;
});

/*************************************
	Avoid the first row be removed
**************************************/
jQuery( '#shiparea_admin_table' ).on( 'focus click', 'input', function( e ) {
	e.preventDefault();

	var $this_row = jQuery( this ).closest( 'tr' ),
		$this_id = $this_row.attr('id');

	if( $this_id == 'trarea_0' ) {
		jQuery('a.remove_rows').hide();
	}
	else {
		jQuery('a.remove_rows').show();
	}

});

/************************************************
	Add/Remove column MinPrice
	when check/uncheck set minumun price field
*************************************************/
jQuery( '#woocommerce_shiparea_is_minprice' ).on( 'click', function( e ) {
	
	if( jQuery(this).is(':checked') )
		jQuery('.cell_minprice').show();
	else
		jQuery('.cell_minprice').attr('style','display : none !important');

});

/***************************************************
	Default Values
	Checked
****************************************************/
jQuery( '#woocommerce_shiparea_default_default_yes' ).on( 'click', function( e ) {
	if( jQuery(this).is(':checked') ) {
		jQuery('.shiparea_default_table_yes').slideDown();
		jQuery('.shiparea_default_table_no').slideUp();
	}
});

jQuery( '#woocommerce_shiparea_default_default_no' ).on( 'click', function( e ) {
	if( jQuery(this).is(':checked') ) {
		jQuery('.shiparea_default_table_yes').slideUp();
		jQuery('.shiparea_default_table_no').slideDown();
	}
});
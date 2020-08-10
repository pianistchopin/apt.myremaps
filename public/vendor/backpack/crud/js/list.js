/*
*
* Backpack Crud / List
*
*/

jQuery(function($){

    'use strict';
	
	setTimeout(function(){
		jQuery("#crudTable tbody tr td:first-child").each(function() {	
			if(jQuery(this).find('strong').attr('class') == 'envelope') {
				jQuery(this).parent().css('font-weight','bold');
			} 
		});
	}, 1500);
});

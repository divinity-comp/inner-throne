jQuery(document).ready(function($) {

	var taxDefaultValue=$('select#tax').val();
	$('#'+taxDefaultValue).show();
	
	$('select#tax').change(function() {
		var value=$(this).val();
		$('.new-cat-dd').each(function() {
			$(this).hide();
		});
		$('#'+value).show();
	});
	
	
	
});
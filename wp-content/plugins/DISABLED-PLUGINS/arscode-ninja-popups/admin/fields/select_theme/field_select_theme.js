jQuery(document).ready(function(){
	if(jQuery('.nhp-opts-select-theme').lenght>0)
	{
		jQuery('#postdivrich').hide();
	}
	jQuery('.nhp-opts-select-theme').change(function(){
		var aID='#select-theme-' + jQuery(this).attr('id');
		var eColors = jQuery('.nhp-opts-select-theme-color');
		var eTypes = jQuery('.nhp-opts-select-theme-type');
		jQuery(aID).html('');
		jQuery(aID).addClass('snp-loading');
		jQuery.post(
		   ajaxurl, 
		   {
			  'action': 'snp_popup_fields',
			  'popup': jQuery(this).val(),
			  'snp_post_ID' : jQuery('#post_ID').val()
		   }, 
		   function(response){
			  jQuery(aID).removeClass('snp-loading');
			  jQuery(aID).html(response);
		   }
		);
		// Colors
		eColors.find('option').remove();
		jQuery("<option/>").val('').text('--').appendTo(eColors);
		jQuery.ajax({
			url: ajaxurl,
			data:{
				'action': 'snp_popup_colors',
				'popup': jQuery(this).val(),
				'snp_post_ID' : jQuery('#post_ID').val()
			},
			dataType: 'JSON',
			type: 'POST',
			success:function(response){
				eColors.find('option').remove();
				jQuery.each(response, function(i, option)
				{
					if(i==jQuery('#nhp-opts-select-theme-color-org-val').val())
					{
						jQuery("<option/>").val(i).attr('selected','selected').text(option.NAME).appendTo(eColors);
					}
					else
					{
						jQuery("<option/>").val(i).text(option.NAME).appendTo(eColors);
					}
				});
				eColors.change();
			},
			error: function(errorThrown){
			   alert('Error...');
			}
		});
		// Types
		eTypes.find('option').remove();
		jQuery("<option/>").val('').text('--').appendTo(eTypes);
		jQuery.ajax({
			url: ajaxurl,
			data:{
				'action': 'snp_popup_types',
				'popup': jQuery(this).val(),
				'snp_post_ID' : jQuery('#post_ID').val()
			},
			dataType: 'JSON',
			type: 'POST',
			success:function(response){
				eTypes.find('option').remove();
				jQuery.each(response, function(i, option)
				{
					if(i==jQuery('#nhp-opts-select-theme-type-org-val').val())
					{
						jQuery("<option/>").val(i).attr('selected','selected').text(option.NAME).appendTo(eTypes);
					}
					else
					{
						jQuery("<option/>").val(i).text(option.NAME).appendTo(eTypes);
					}
				});
				eTypes.change();
			},
			error: function(errorThrown){
			   alert('Error...');
			}
		});
		
	}).change();
	jQuery('.nhp-opts-select-theme-color').change(function(){
		var eTheme = jQuery(this).prev('.nhp-opts-select-theme');
		jQuery('#nhp-opts-select-theme-preview-img').hide();
		jQuery('.nhp-opts-select-theme-preview').addClass('snp-loading');
		//jQuery('#nhp-opts-select-theme-preview-img').attr('src',''+jQuery('#SNP_URL').val()+'themes/'+eTheme.val()+'/preview/'+jQuery(this).val()+'.png');
		jQuery('#nhp-opts-select-theme-preview-img').attr('src',''+eTheme.find('option:selected').data('preview')+'/'+eTheme.val()+'/preview/'+jQuery(this).val()+'.png');
		jQuery('#nhp-opts-select-theme-preview-img').load(function(){
			jQuery('.nhp-opts-select-theme-preview').removeClass('snp-loading');
			jQuery('#nhp-opts-select-theme-preview-img').show();
		});		
	});
	jQuery('.nhp-opts-select-theme-type').change(function(){
		if(jQuery(this).val()=='optin' || jQuery(this).val()=='iframe' || jQuery(this).val()=='html' || jQuery(this).val()=='likebox')
		{
			jQuery('#snp-cf-fb').hide();
			jQuery('#snp-cf-tw').hide();
			jQuery('#snp-cf-gp').hide();
			jQuery('#snp-cf-li').hide();
			jQuery('#snp-cf-pi').hide();
		}
		if(jQuery(this).val()=='optin')
		{
			jQuery('#snp-cf-optin').show();
		}
		else
		{
			jQuery('#snp-cf-optin').hide();	
		}
		if(jQuery(this).val()=='social')
		{
			jQuery('#snp-cf-fb').show();
			jQuery('#snp-cf-tw').show();
			jQuery('#snp-cf-gp').show();
			jQuery('#snp-cf-li').show();
			jQuery('#snp-cf-pi').show();
		}
		if(jQuery(this).val()=='html')
		{
			jQuery('#postdivrich').show();
		}
		else
		{
			jQuery('#postdivrich').hide();
		}
	});
});
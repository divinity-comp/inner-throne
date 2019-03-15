jQuery(document).ready ( function () {
  if(jQuery('.captionpix-metabox').length > 0) {
		jQuery('.captionpix-metabox li.tab a').each(function(i) {
			var thisMetabox =  jQuery(this).closest('.captionpix-metabox');
         var tabs = thisMetabox.find('.footer-putter-metabox-tabs'); 	
         var content = thisMetabox.find('.metabox-content'); 			
			var thisTab = jQuery(this).parent().attr('class').replace(/tab /, '');
			var selectedTab = thisMetabox.find('#tabselect');
			if ( thisTab == selectedTab.val() ) {
				jQuery(this).addClass('active');
            content.children('div.'+thisTab).addClass('active');        	
			} else {
	       	content.children('div.' + thisTab).hide();
			}
         jQuery(this).click(function(){
         	content.children('div').hide();
				content.children('div.active').removeClass('active');
				tabs.find('li a.active').removeClass('active');
 	       	selectedTab.val(thisTab);
				tabs.find('li.'+thisTab+' a').addClass('active');
				content.children('div.'+thisTab).addClass('active').show();
				return false;
			});
	   	tabs.show();
		});
  }
});
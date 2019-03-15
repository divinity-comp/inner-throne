$j = jQuery.noConflict();
$j(document).ready(function() {
	$j('#ryit-info').appendTo($j('.main-flex'));
	$j('.post-content p a').each(function() { //Add additional styling tags for links
		$j(this).wrapInner('<span></span>');
		$j(this).attr('data-hover',$j(this).text());
	});
	if($j(document).scrollTop() > 0) {
		$j('.ryit-arrow-down').remove();
	}
});

$j('#toggle-source-1').click(function() {
	$j('#toggle-target-1').slideToggle();
	$j('#toggle-source-1').remove();
});

$j(document).bind( 'scroll', function(){
    $j('.ryit-arrow-down').remove();
});
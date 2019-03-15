jqcc = jQuery;

jqcc('.ccplugin_outerframe').before('<div id="overlaymain" style="position:relative"></div>');
var overlay = jqcc('<div></div>')
.attr('id','overlay')
.appendTo('#overlaymain');
jqcc('<div class="col-sm-12 col-lg-12">cURL extension is disabled on your server. Please contact your webhost to enable it.<br> cURL is required for CometChat installation.</div>')
	.css({'z-index':' 9999',
	'color':'#000000',
	'font-size':'15px',
	'font-weight':'bold',
	'display':'block',
	'text-align':'center',
	'margin':'auto',
	'position':'absolute',
	'width':'100%',
	'top':'8%'
}).appendTo('#overlaymain');
jqcc("#overlaymain").css({
	'width': '100%',
	'padding': '0',
	'display': 'table',
	'height': '97vh',
	'position': 'absolute',
	'top': '0',
	'left': '0',
	'margin': '0',
	'background-color': 'rgb(255, 255, 255)',
    'opacity': '0.85',
    'z-index': '99',
    'right': '0px'
});
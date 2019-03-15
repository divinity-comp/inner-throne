<?php
if (!intval($POPUP_META['snp_width']))
{
	$POPUP_META['snp_width']='415';
}
if (!intval($POPUP_META['snp_height']))
{
	$POPUP_META['snp_height']='345';
}
$POPUP_META['snp_bg_gradient']=unserialize($POPUP_META['snp_bg_gradient']);
?>
<div class="snp-fb snp-theme-likebox">
	<div class="snp-content">
		<div class="snp-content-inner">
			<div id="fb-root"></div>
			<script>(function(d, s, id) {
			  var js, fjs = d.getElementsByTagName(s)[0];
			  if (d.getElementById(id)) return;
			  js = d.createElement(s); js.id = id;
			  js.src = "//connect.facebook.net/en_GB/all.js#xfbml=1";
			  fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));</script>
			<?php
				if(!empty($POPUP_META['snp_facebook_url']))
				{
					echo '<div class="fb-like-box" data-href="'.($POPUP_META['snp_facebook_url']).'" data-colorscheme="'.($POPUP_META['snp_lb_colorscheme']==2 ? 'dark' : 'light').'" data-width="'.($POPUP_META['snp_width']-10).'" data-height="'.($POPUP_META['snp_height']-10).'" data-show-faces="'.($POPUP_META['snp_lb_show_faces'] ? 'true' : 'false').'" data-stream="'.($POPUP_META['snp_lb_show_stream'] ? 'true' : 'false').'" data-header="'.($POPUP_META['snp_lb_show_header'] ? 'true' : 'false').'" data-border-color="'.($POPUP_META['snp_bg_gradient']['from'] ? $POPUP_META['snp_bg_gradient']['from'] : '#ffffff').'" ></div>';
				}
			?>
        </div>
	</div>
	<?php
	if($POPUP_META['snp_width']>260 && snp_get_option('PROMO_ON') && snp_get_option('PROMO_REF') && SNP_PROMO_LINK!='')
	{
		$PROMO_LINK=SNP_PROMO_LINK.snp_get_option('PROMO_REF');
		echo '<div class="snp-powered">';
		echo '<a href="'.$PROMO_LINK.'" target="_blank">Powered by <strong>Ninja Popups</strong></a>';
		echo '</div>';
	}
	?>
</div>
<?php
echo '<style>';
if (intval($POPUP_META['snp_width']))
{
	echo '.snp-pop-'.$ID.' .snp-theme-likebox { max-width: 100%; width: '.$POPUP_META['snp_width'].'px;}';
	echo '.snp-pop-'.$ID.' .snp-content-inner, .snp-pop-'.$ID.' .fb-like-box, .snp-pop-'.$ID.' span { width: 100% !important; }';
	echo '@media (max-width: '.$POPUP_META['snp_width'].'px) { .snp-pop-'.$ID.' .snp-theme-likebox { min-width: 300px; width: 100%; } }';
}
if (intval($POPUP_META['snp_height']))
{
	echo '.snp-pop-'.$ID.' .snp-theme-likebox { min-height: '.$POPUP_META['snp_height'].'px;}';
}
if ($POPUP_META['snp_bg_gradient'])
{
	if(!$POPUP_META['snp_bg_gradient']['to'])
	{
		$POPUP_META['snp_bg_gradient']['to']=$POPUP_META['snp_bg_gradient']['from'];
	}
	?>
		.snp-pop-<?php echo $ID; ?> .snp-theme-likebox{
		  background: <?php echo $POPUP_META['snp_bg_gradient']['to'];?>;
		  background-image: -moz-radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo $POPUP_META['snp_bg_gradient']['to'];?> 500%);
		  background-image: -webkit-radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo $POPUP_META['snp_bg_gradient']['to'];?> 500%);
		  background-image: -o-radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo $POPUP_META['snp_bg_gradient']['to'];?> 500%);
		  background-image: -ms-radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo $POPUP_META['snp_bg_gradient']['to'];?> 500%);
		  background-image: radial-gradient(50% 50%, circle contain, <?php echo $POPUP_META['snp_bg_gradient']['from'];?>, <?php echo $POPUP_META['snp_bg_gradient']['to'];?> 500%);
		}
		.snp-pop-<?php echo $ID; ?> .snp-theme-likebox .snp-powered {
		  <?php if ($POPUP_META['snp_bg_gradient']['from']) {echo 'background:'.$POPUP_META['snp_bg_gradient']['from'].';';}?>
		}
	<?php
}
echo '</style>';
?>
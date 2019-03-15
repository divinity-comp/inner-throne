<?php
/**
 *  This template is used to display the Checkout page when items are in the cart
 */

global $post; ?>
<table id="edd_checkout_cart" <?php if ( ! edd_is_ajax_disabled() ) { echo 'class="ajaxed"'; } ?>>
	<thead>
		<tr class="edd_cart_header_row">
			<?php do_action( 'edd_checkout_table_header_first' ); ?>
			<th class="edd_cart_item_name"><?php _e( 'Item Name', 'easy-digital-downloads' ); ?></th>
			<th class="edd_cart_item_price"><?php _e( 'Item Price', 'easy-digital-downloads' ); ?></th>
			<th class="edd_cart_actions"><?php _e( 'Actions', 'easy-digital-downloads' ); ?></th>
			<?php do_action( 'edd_checkout_table_header_last' ); ?>
		</tr>
	</thead>
	<tbody>
		<?php $cart_items = edd_get_cart_contents(); ?>
		<?php do_action( 'edd_cart_items_before' ); ?>
		<?php if ( $cart_items ) : ?>
			<?php foreach ( $cart_items as $key => $item ) : ?>
				<?php
					edd_debug_log();

					$discounts = EDD()->cart->get_discounts();
					$discount = $discounts[0];
					$discount = edd_get_discount_by_code($discount);

					/* Reclaim your Inner Throne modification */
					$item_id = $item['id'];
					$price_id = $item['options']['price_id'];

					//var_dump($item['options']);		
					//var_dump($price_id);
					//var_dump($item_id);

					if(!empty($item['options']['recurring'])) { //item has recurring payments
						$recurring_payments = true;
						$item_times = $item['options']['recurring']['times'];
						$signup_fee = $item['options']['recurring']['signup_fee'];
					}

					$cart_item = new EDD_download($item_id);

					//var_dump($cart_item);
					//var_dump($prices);

					if($cart_item->has_variable_prices()) {
						$prices = $cart_item->get_prices();
						$price = $prices[$price_id]['amount'];	
					}
					else {
						$price = $cart_item->get_price();
					}
					

					//echo "price " . $price;
					//echo "times " . $item_times;

					$price_total += $price * $item_times + $signup_fee;

					if(!empty($discount)) {
						if($discount->type == 'percent') {
							$price_total *= ((100-$discount->amount)/100);
						}
						else {
							$price_total -= $discount->amount;
						}
					}
				?>
				<tr class="edd_cart_item" id="edd_cart_item_<?php echo esc_attr( $key ) . '_' . esc_attr( $item['id'] ); ?>" data-download-id="<?php echo esc_attr( $item['id'] ); ?>">
					<?php do_action( 'edd_checkout_table_body_first', $item ); ?>
					<td class="edd_cart_item_name">
						<?php
							if ( current_theme_supports( 'post-thumbnails' ) && has_post_thumbnail( $item['id'] ) ) {
								echo '<div class="edd_cart_item_image">';
									echo get_the_post_thumbnail( $item['id'], apply_filters( 'edd_checkout_image_size', array( 25,25 ) ) );
								echo '</div>';
							}
							$item_title = edd_get_cart_item_name( $item );
							echo '<span class="edd_checkout_cart_item_title">' . esc_html( $item_title ) . '</span>';

							/**
							 * Runs after the item in cart's title is echoed
							 * @since 2.6
							 *
							 * @param array $item Cart Item
							 * @param int $key Cart key
							 */
							do_action( 'edd_checkout_cart_item_title_after', $item, $key );
						?>
					</td>
					<td class="edd_cart_item_price">
						<?php
						echo edd_cart_item_price( $item['id'], $item['options'] );
						do_action( 'edd_checkout_cart_item_price_after', $item );
						?>
					</td>
					<td class="edd_cart_actions">
						<?php if( edd_item_quantities_enabled() && ! edd_download_quantities_disabled( $item['id'] ) ) : ?>
							<input type="number" min="1" step="1" name="edd-cart-download-<?php echo $key; ?>-quantity" data-key="<?php echo $key; ?>" class="edd-input edd-item-quantity" value="<?php echo edd_get_cart_item_quantity( $item['id'], $item['options'] ); ?>"/>
							<input type="hidden" name="edd-cart-downloads[]" value="<?php echo $item['id']; ?>"/>
							<input type="hidden" name="edd-cart-download-<?php echo $key; ?>-options" value="<?php echo esc_attr( json_encode( $item['options'] ) ); ?>"/>
						<?php endif; ?>
						<?php do_action( 'edd_cart_actions', $item, $key ); ?>
						<a class="edd_cart_remove_item_btn" href="<?php echo esc_url( wp_nonce_url( edd_remove_item_url( $key ), 'edd-remove-from-cart-' . $key, 'edd_remove_from_cart_nonce' ) ); ?>"><?php _e( 'Remove', 'easy-digital-downloads' ); ?></a>
					</td>
					<?php do_action( 'edd_checkout_table_body_last', $item ); ?>
				</tr>
			<?php endforeach; ?>
		<?php endif; ?>
		<?php do_action( 'edd_cart_items_middle' ); ?>
		<!-- Show any cart fees, both positive and negative fees -->
		<?php if( edd_cart_has_fees() ) : ?>
			<?php foreach( edd_get_cart_fees() as $fee_id => $fee ) : ?>
				<tr class="edd_cart_fee" id="edd_cart_fee_<?php echo $fee_id; ?>">

					<?php do_action( 'edd_cart_fee_rows_before', $fee_id, $fee ); ?>

					<td class="edd_cart_fee_label"><?php echo esc_html( $fee['label'] ); ?></td>
					<td class="edd_cart_fee_amount"><?php echo esc_html( edd_currency_filter( edd_format_amount( $fee['amount'] ) ) ); ?></td>
					<td>
						<?php if( ! empty( $fee['type'] ) && 'item' == $fee['type'] ) : ?>
							<a href="<?php echo esc_url( edd_remove_cart_fee_url( $fee_id ) ); ?>"><?php _e( 'Remove', 'easy-digital-downloads' ); ?></a>
						<?php endif; ?>

					</td>

					<?php do_action( 'edd_cart_fee_rows_after', $fee_id, $fee ); ?>

				</tr>
			<?php endforeach; ?>
		<?php endif; ?>

		<?php do_action( 'edd_cart_items_after' ); ?>
	</tbody>
	<tfoot>

		<?php if( has_action( 'edd_cart_footer_buttons' ) ) : ?>
			<tr class="edd_cart_footer_row<?php if ( edd_is_cart_saving_disabled() ) { echo ' edd-no-js'; } ?>">
				<th colspan="<?php echo edd_checkout_cart_columns(); ?>">
					<?php do_action( 'edd_cart_footer_buttons' ); ?>
				</th>
			</tr>
		<?php endif; ?>

		<?php if( edd_use_taxes() && ! edd_prices_include_tax() ) : ?>
			<tr class="edd_cart_footer_row edd_cart_subtotal_row"<?php if ( ! edd_is_cart_taxed() ) echo ' style="display:none;"'; ?>>
				<?php do_action( 'edd_checkout_table_subtotal_first' ); ?>
				<th colspan="<?php echo edd_checkout_cart_columns(); ?>" class="edd_cart_subtotal">
					<?php _e( 'Subtotal', 'easy-digital-downloads' ); ?>:&nbsp;<span class="edd_cart_subtotal_amount"><?php echo edd_cart_subtotal(); ?></span>
				</th>
				<?php do_action( 'edd_checkout_table_subtotal_last' ); ?>
			</tr>
		<?php endif; ?>

		<tr class="edd_cart_footer_row edd_cart_discount_row" <?php if( ! edd_cart_has_discounts() )  echo ' style="display:none;"'; ?>>
			<?php do_action( 'edd_checkout_table_discount_first' ); ?>
			<th colspan="<?php echo edd_checkout_cart_columns(); ?>" class="edd_cart_discount">
				<?php edd_cart_discounts_html(); ?>
			</th>
			<?php do_action( 'edd_checkout_table_discount_last' ); ?>
		</tr>

		<?php if( edd_use_taxes() ) : ?>
			<tr class="edd_cart_footer_row edd_cart_tax_row"<?php if( ! edd_is_cart_taxed() ) echo ' style="display:none;"'; ?>>
				<?php do_action( 'edd_checkout_table_tax_first' ); ?>
				<th colspan="<?php echo edd_checkout_cart_columns(); ?>" class="edd_cart_tax">
					<?php _e( 'Tax', 'easy-digital-downloads' ); ?>:&nbsp;<span class="edd_cart_tax_amount" data-tax="<?php echo edd_get_cart_tax( false ); ?>"><?php echo esc_html( edd_cart_tax() ); ?></span>
				</th>
				<?php do_action( 'edd_checkout_table_tax_last' ); ?>
			</tr>

		<?php endif; ?>

		<!-- RYIT modifications -->
		<tr class="edd_cart_footer_row">
			<?php do_action( 'edd_checkout_table_footer_first' ); ?>
			<?php if($recurring_payments) : ?>
				<th colspan="<?php echo edd_checkout_cart_columns(); ?>" class="edd_cart_total"><p style="margin: 3px 0 0 0; font-size: 16px; font-weight: bold;"><?php _e( 'First payment', 'easy-digital-downloads' ); ?>: <span class="edd_cart_amount" data-subtotal="<?php echo edd_get_cart_subtotal(); ?>" data-total="<?php echo edd_get_cart_total(); ?>"> <?php edd_cart_total(); ?></span></p><p style="font-size: 14px; margin-bottom: 10px;">Total plan payments: $<span id="checkout_price_total"><?php echo money_format("%i", $price_total); ?></span></p></th>
			<?php else : ?>
				<th colspan="<?php echo edd_checkout_cart_columns(); ?>" class="edd_cart_total"><?php _e( 'Total', 'easy-digital-downloads' ); ?>: <span class="edd_cart_amount" data-subtotal="<?php echo edd_get_cart_subtotal(); ?>" data-total="<?php echo edd_get_cart_total(); ?>"><?php edd_cart_total(); ?></span></th>
			<?php endif; ?>
			<?php do_action( 'edd_checkout_table_footer_last' ); ?>
		</tr>
	</tfoot>
</table>
<script type="text/javascript">
    //Update payment plan total after Discount Code is applied
	$j('body').on('edd_discount_applied', function(e, discount_response) {
	    var str = discount_response.amount; //Get discount
	    var checkout_total = $j('#checkout_price_total').text(); //Check current total
	    
	    var pct_test = str.indexOf("%"); //check if discount is percent
	    if(pct_test == -1)  {
    	    var str = str.replace('&#36;','');
    	    var discount_amount = str.replace('.00','');
    	    window.discount = discount_amount;
    	    var checkout_discounted_total = parseInt(checkout_total) - parseInt(discount_amount);
    	    console.log("test " + discount_amount);
	    }
	    else {
	        var discount_pct = parseInt(str.substr(0, str.indexOf('%')));
	        window.discount = discount_pct;
	        var checkout_discounted_total = Math.floor(checkout_total * ((100-window.discount)/100));
	    }
	    
	    
        $j('#checkout_price_total').text(checkout_discounted_total.toString());
	});
	
    //Update payment plan total after Discount Code is removed
	$j('body').on('edd_discount_removed', function() {
	    var checkout_total = $j('#checkout_price_total').text();
	    checkout_total = parseInt(checkout_total) + parseInt(window.discount);
	    $j('#checkout_price_total').text(checkout_total.toString());
	});
</script>
<?php if($signup_fee) : ?>
	<p style="font-size: 15px; color: #bbb; margin-bottom: 2.5em; font-style: italic;">* For longer payment plans, we ask for 30% of the fees up front.</p>
<?php endif; ?>
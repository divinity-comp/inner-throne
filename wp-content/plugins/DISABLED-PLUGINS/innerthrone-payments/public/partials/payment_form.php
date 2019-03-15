<script>var plans = <?php echo json_encode($this->plans); ?>;</script>
<script>var innerthrone_ajaxurl = '<?php echo admin_url( 'admin-ajax.php' ); ?>';</script>
<script src="https://checkout.stripe.com/checkout.js"></script>
<div class="innerthrone-payment_form">
	<form id='innerthrone-payment_form' method='POST' class="clearfix">
		<fieldset>
			<label for='name'>Name:</label>
			<input type='text' name='name' id='innerthrone_name' />
		</fieldset>
		<fieldset>
			<label for='email'>Email:</label>
			<input type='email' id='innerthrone_email' name='email' />
		</fieldset>
		<div class="plans">
			<label for='plan'>Plan:</label>
			<ul>
			<?php foreach( $this->plans as $id => $plan ): ?>
				<li>
					
					<input type='radio' name='plan' value='<?php echo $id ?>'>
					<?php echo $plan['description']; ?>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
		<div>
			<input type='hidden' name='token' id='token' value='' />
			<button id="innerthrone_payment_submit">Sign up for your initiation</button>
		</div>
	</form>
</div>

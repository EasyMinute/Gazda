<div class="shiparea_verify_address_return">
	<div class="shiparea_verify_address_shiprice_msg"><?php echo $shiprice_msg; ?></div>
	<div class="shiparea_verify_address_shiprice_num"><?php echo $shiprice_num; ?></div>
	
	<?php if( $is_minprice ) : ?>
		<div class="shiparea_verify_address_minprice">
			<span class="shiparea_verify_address_minprice_msg"><?php echo $minprice_msg; ?></span>
			<span class="shiparea_verify_address_minprice_num"><?php echo $minprice_num; ?></span>
		</div>
	<?php endif; ?>

</div>
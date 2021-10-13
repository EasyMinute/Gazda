<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://proacto.software/
 * @since      1.0.0
 *
 * @package    Prt_Woo_Portmone
 * @subpackage Prt_Woo_Portmone/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->


<?php 

// Make sure WooCommerce is active
if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) { ?>

	<div class="prt-container">
		<h1 class="prt-title">
			Будь ласка, спершу активуйте плагін WooCommerce
		</h1>
	</div>

<?php } else { ?>
	<?php
		add_action( 'plugins_loaded', 'print_gate', 11 );
		function print_gate() {

		}

	var_dump( WC()->payment_gateways);
	?>
	<div class="prt-container">
		<h1 class="prt-title">
			Сторінка опцій для методу оплати у Portmone
		</h1>
		<form action="" method="post">
			<input type="hidden" name="action" value="pm-single">

			<label for="main-key" class="pm-label">
				Введіть публічний ключ основного рахунку
			</label>
			<br>
			<input type="text" name="main-key" id="main-key">

			<div class="submit">
				<button class="button button-primary" type="submit">Зберегти</button>
			</div>
		</form>
	</div>

<?php } ?>




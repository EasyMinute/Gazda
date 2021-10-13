<?php
/**
 * Show messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/notice.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! $messages ) {
	return;
}

?>
<?php foreach ( $messages as $message ) : ?>
	<div class="woocommerce-notice">
		<?php echo ideapark_svg( 'wc-notice', 'woocommerce-notice-info-svg' ); ?>
		<?php echo wc_kses_notice( $message ); ?>
		<button class="h-cb h-cb--svg woocommerce-notice-close js-notice-close"><?php echo ideapark_svg( 'close-round', 'woocommerce-notice-close-svg' ); ?></button>
	</div>
<?php endforeach; ?>

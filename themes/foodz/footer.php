<?php $has_sidebar = ! ideapark_mod( 'footer_minimal' ) && is_active_sidebar( 'footer-sidebar' ) ?>
<footer class="l-section c-footer<?php ideapark_class( $has_sidebar, 'c-footer--widgets', 'c-footer--minimal' ); ideapark_class( ideapark_mod( 'mobile_layout' ) == 'layout-type-1', 'c-footer--bottom-sticky' ); ideapark_class( ideapark_mod( 'mobile_layout' ) == 'layout-type-2' && ideapark_mod( 'product_mobile_single_ajax_add_to_cart' ), 'js-footer--add-to-cart') ?>">
	<div class="l-section__container">
		<div class="c-footer__row-1<?php ideapark_class( $has_sidebar, 'c-footer__row-1--widgets', 'c-footer__row-1--minimal' ); ?>">
			<div class="c-footer__main<?php ideapark_class( $has_sidebar, 'c-footer__main--widgets', 'c-footer__main--minimal' ); ?>">
				<?php get_template_part( 'templates/footer-logo' ); ?>
				<?php get_template_part( 'templates/footer-phone' ); ?>
				<?php get_template_part( 'templates/footer-contacts' ); ?>
			</div>
			<?php if ( $has_sidebar ) { ?>
			<div class="c-footer__widgets">
				<?php dynamic_sidebar( 'footer-sidebar' ); ?>
			</div>
			<?php } ?>
		</div>
		<div class="c-footer__row-2<?php ideapark_class( $has_sidebar, 'c-footer__row-2--widgets', 'c-footer__row-2--minimal' ); ?>">
			<?php if ( ideapark_mod( 'footer_copyright' ) ) { ?>
				<?php get_template_part( 'templates/footer-copyright' ); ?>
				<?php ideapark_get_template_part( 'templates/soc', [ 'class' => 'c-footer__soc' . ( ideapark_mod( 'footer_copyright' ) && $has_sidebar? ' c-footer__soc--second' : '' ) ] ); ?>
			<?php } ?>
		</div>
	</div>
</footer>
<?php if (ideapark_mod( 'shop_modal' ) || ideapark_mod( 'shop_product_modal' ) && ideapark_woocommerce_on() && is_product()) { ?>
	<?php get_template_part( 'templates/pswp' ); ?>
<?php } ?>
<?php if ( ideapark_mod( 'to_top_button' ) ) { ?>
	<button class="c-to-top-button js-to-top-button <?php ideapark_class(ideapark_mod( 'mobile_layout' ) == 'layout-type-2' , 'c-to-top-button--without-menu' ); ?>" type="button">
		<?php echo ideapark_svg( 'to-top', 'c-to-top-button__svg' ); ?>
	</button>
<?php } ?>
<?php wp_footer(); ?>
</body>
</html>

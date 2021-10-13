<?php $cnt = 1; ?>
<?php ob_start(); ?>
<?php if ( is_active_sidebar( 'filter-sidebar' ) ) { ?>
	<div class="c-ordering__filter-show-button">
		<button class="h-cb h-cb--svg" type="button"
				id="ideapark-shop-sidebar-button"><?php echo ideapark_svg( 'filter', 'c-ordering__filter-ico' ) ?><?php esc_html_e( 'Filter', 'foodz' ); ?></button>
	</div>
	<?php $cnt ++; ?>
<?php } ?>

<?php if ( is_product_taxonomy() && ( $filters = ideapark_horizontal_filter() ) ) { ?>
	<?php if ( ! ideapark_mod( 'shop_sidebar' ) ) { ?>
		<div class="c-ordering__spacer"></div>
	<?php } ?>
	<div
		class="c-ordering__filter-wrap<?php ideapark_class( ideapark_mod( 'shop_sidebar' ), 'c-ordering__filter-wrap--with-sidebar' ) ?>">
		<ul class="c-ordering__filter-list<?php ideapark_class( ideapark_mod( 'shop_sidebar' ), 'c-ordering__filter-list--with-sidebar' ) ?>">
			<?php foreach ( $filters as $filter ) {
				$content = ideapark_wrap( $filter['title'], '<span class="c-ordering__filter-button ' . ( $filter['chosen'] ? 'c-ordering__filter-button--chosen' : '' ) . '">', '</span>' );
				if ( ! $filter['chosen'] ) {
					$content = ideapark_wrap( $content, '<a rel="nofollow" href="' . $filter['link'] . '">', '</a>' );
				}
				echo ideapark_wrap( $content, '<li class="c-ordering__filter-item ' . ( $filter['chosen'] ? 'c-ordering__filter-list--chosen' : '' ) . '">', '</li>' );
			} ?>
		</ul>
	</div>
	<?php $cnt ++; ?>
<?php } ?>

<div
	class="c-ordering__select<?php ideapark_class( ! empty( $filters ), 'c-ordering__select--filter', 'c-ordering__select--no-filter' ); ?>">
	<?php woocommerce_catalog_ordering(); ?>
</div>
<?php $content = trim( ob_get_clean() ); ?>

<div class="c-ordering<?php if ( ! ideapark_mod( 'shop_sidebar' ) ) { ?> c-ordering--preload<?php } ?><?php if ($cnt == 1) { ?> c-ordering--single<?php } ?>">
	<?php echo ideapark_wrap( $content ); ?>
</div>
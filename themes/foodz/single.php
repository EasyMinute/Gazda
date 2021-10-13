<?php get_header(); ?>
<?php global $post;
$with_sidebar   = ideapark_mod( 'sidebar_post' ) && ! ( ideapark_woocommerce_on() && ( is_cart() || is_checkout() || is_account_page() || ideapark_is_wishlist_page() ) );
$is_hide_header = ideapark_woocommerce_on() && ( ! is_user_logged_in() && is_account_page() || is_order_received_page() );
?>
<?php if ( ! $is_hide_header ) { ?>
	<header class="l-section c-page-header c-page-header--common">
		<h1 class="c-page-header__title <?php ideapark_class( ideapark_woocommerce_on() && ( is_cart() || is_checkout() || is_account_page() || ideapark_is_wishlist_page() ), '', 'c-page-header__title--post' ); ?>"><?php the_title(); ?></h1>
	</header>
<?php } ?>

<?php if ( have_posts() ): ?>
	<?php while ( have_posts() ) : the_post(); ?>

		<?php if ( ! is_page() ) { ?>
			<div class="l-section l-section--container c-post__meta">
				<?php if ( ! ideapark_mod( 'post_hide_author' ) ) { ?>
					<?php esc_html_e( 'by', 'foodz' ); ?>
					<span class="c-post__author">
						<?php the_author_posts_link(); ?>
					</span>
				<?php } ?>
				<?php if ( ! ideapark_mod( 'post_hide_date' ) ) { ?>
					<?php esc_html_e( 'on', 'foodz' ); ?>
					<span class="c-post__date">
						<?php the_time( get_option( 'date_format' ) ); ?>
					</span>
				<?php } ?>
				<?php if ( ! ideapark_mod( 'post_hide_category' ) ) { ?>
					<?php esc_html_e( 'in', 'foodz' ); ?>
					<ul class="c-post__categories">
						<li class="c-post__categories-item"><?php ideapark_category( ',</li> <li class="c-post__categories-item">', null, 'c-post__categories-item-link' ); ?></li>
					</ul>
				<?php } ?>
			</div>
		<?php } ?>

		<div
			class="l-section l-section--container l-section--top-margin<?php if ( $with_sidebar ) { ?> l-section--with-sidebar<?php } ?>">
			<div class="l-section__content<?php if ( $with_sidebar ) { ?> l-section__content--with-sidebar<?php } ?>">
				<?php if ( $with_sidebar && ideapark_mod( 'sticky_sidebar' ) ) { ?>
				<div class="js-sticky-sidebar-nearby">
					<?php } ?>
					<?php
					$name = '';
					if ( ideapark_woocommerce_on() && ( is_cart() || is_checkout() || is_account_page() ) || ideapark_is_wishlist_page() ) {
						$name = 'woocommerce';
					}
					if ( ideapark_is_wishlist_page() ) {
						get_template_part( 'woocommerce/wishlist' );
					} else {
						get_template_part( 'content', $name );
					}
					?>
					<?php if ( $with_sidebar && ideapark_mod( 'sticky_sidebar' ) ) { ?>
				</div>
			<?php } ?>
			</div>
			<?php if ( $with_sidebar ) { ?>
				<div class="l-section__sidebar l-section__sidebar--right">
					<?php get_sidebar(); ?>
				</div>
			<?php } ?>
		</div>

	<?php endwhile; ?>
<?php endif; ?>

<?php get_footer(); ?>












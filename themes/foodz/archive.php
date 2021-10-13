<?php
get_header();
$with_sidebar = ! ! ideapark_mod( 'sidebar_blog' );
if ( is_category() ) {
	$page_title = single_cat_title( '', false );
} elseif ( is_tag() ) {
	$page_title = single_tag_title( '', false );
} elseif ( is_author() ) {
	the_post();
	$page_title = get_the_author();
	rewind_posts();
} elseif ( is_day() ) {
	$page_title = get_the_date();
} elseif ( is_month() ) {
	$page_title = get_the_date( 'F Y' );
} elseif ( is_year() ) {
	$page_title = get_the_date( 'Y' );
} else {
	$page_title = esc_html__( 'Archives', 'foodz' );
} ?>

<header class="l-section c-page-header c-page-header--common">
	<h1 class="c-page-header__title"><?php echo esc_html( $page_title ); ?></h1>
</header>

<div
	class="c-blog l-section l-section--container l-section--top-margin<?php if ( $with_sidebar ) { ?> l-section--with-sidebar<?php } ?>">
	<div class="l-section__content<?php if ( $with_sidebar ) { ?> l-section__content--with-sidebar<?php } ?>">
		<?php if ( $with_sidebar && ideapark_mod( 'sticky_sidebar' ) ) { ?>
		<div class="js-sticky-sidebar-nearby">
			<?php } ?>
			<?php if ( have_posts() ): ?>
				<div class="c-blog__list js-masonry">
					<?php while ( have_posts() ) : the_post(); ?>
						<div
							class="c-blog__item <?php if ( $with_sidebar ) { ?>c-blog__item--with-sidebar<?php } ?> js-post-item">
							<?php get_template_part( 'content', 'list' ); ?>
						</div>
					<?php endwhile; ?>
					<div
						class="c-blog__sizer <?php if ( $with_sidebar ) { ?>c-blog__sizer--with-sidebar<?php } ?> js-post-sizer"></div>
				</div>
				<?php ideapark_corenavi();
			else : ?>
				<p class="c-blog__nothing"><?php esc_html_e( 'Sorry, no posts were found.', 'foodz' ); ?></p>
			<?php endif; ?>
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

<?php get_footer(); ?>
   
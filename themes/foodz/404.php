<?php get_header(); ?>

<section class="l-section l-section--container">
	<div class="c-404">
		<div class="c-404__image-wrap">
			<?php if ( ideapark_mod( '404_image' ) ) { ?>
				<img src="<?php echo stripslashes( ideapark_mod( '404_image' ) ); ?>" alt="<?php esc_html_e( 'Oops! That page can’t be found.', 'foodz' ); ?>" class="c-404__image" />
			<?php } else { ?>
				<?php echo ideapark_svg( '404', 'c-404__svg' ); ?>
			<?php } ?>
		</div>
		<h1 class="c-404__header"><?php esc_html_e( 'Oops! That page can’t be found.', 'foodz' ); ?></h1>
		<div class="c-404__text"><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'foodz' ); ?></div>
		<div class="c-404__search-wrap">
			<?php get_search_form(); ?>
		</div>
	</div>
</section>

<?php get_footer(); ?>

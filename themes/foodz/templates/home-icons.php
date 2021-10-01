<?php
$section_style = [];

if ( ideapark_mod( 'home_icons_background_color' ) ) {
	$section_style[] = 'background-color:' . esc_attr( ideapark_mod_hex_color_norm( 'home_icons_background_color' ) );
}
if ( ! ideapark_mod( 'lazyload' ) && ideapark_mod( 'home_icons_background_image' ) ) {
	$section_style[] = 'background-image:url(\'' . esc_url( ideapark_mod( 'home_icons_background_image' ) ) . '\')';
}

$block_count = 0;
for ( $i = 1; $i <= 4; $i ++ ) {
	$block_count += ideapark_mod( 'home_icons_header_' . $i ) || ideapark_mod( 'home_icons_content_' . $i ) ? 1 : 0;
}
?>
<section id="home-icons" class="l-section c-icons<?php ideapark_class( ideapark_mod( 'lazyload' ), 'lazyload' ); ?><?php ideapark_class( ideapark_mod( 'home_icons_boxed' ), 'l-section--container c-icons--container', 'c-icons--fullwidth' ); ?><?php ideapark_class( ideapark_mod( 'home_icons_margins' ), 'c-icons--top-margin' ); ?>"<?php ideapark_style($section_style); ?><?php if (ideapark_mod( 'lazyload' ) && ideapark_mod( 'home_icons_background_image' )) { ?>data-bg="<?php echo esc_url( ideapark_mod( 'home_icons_background_image' ) ); ?>"<?php } ?>>
	<?php if ( ! ideapark_mod( 'home_icons_boxed' ) ) { ?>
	<div class="l-section__container">
		<?php } ?>
		<div class="c-icons__wrap">
			<?php for ( $i = 1; $i <= 4; $i ++ ) { ?>
				<?php if ( ideapark_mod( 'home_icons_content_' . $i ) || ideapark_mod( 'home_icons_header_' . $i ) ) { ?>
					<div class="c-icons__block <?php ideapark_class( ideapark_mod( 'home_icons_boxed' ) && $block_count == 4, 'c-icons__block--container-4' ); ?>">
						<div class="c-icons__icon-block">
						<?php if ( ideapark_mod( 'home_icons_icon_' . $i ) ) { ?>
							<img class="c-icons__icon <?php ideapark_class(ideapark_mod( 'lazyload' ) , 'lazyload' ); ?>" <?php if ( ideapark_mod( 'lazyload' ) ) { ?>src="<?php echo ideapark_empty_gif(); ?>" data-<?php } ?>src="<?php echo esc_url( ideapark_mod( 'home_icons_icon_' . $i ) ); ?>" alt="<?php echo esc_html( ideapark_mod( 'home_icons_header_' . $i ) ); ?>">
						<?php } ?>
						</div>
						<?php if ( ideapark_mod( 'home_icons_header_' . $i ) ) { ?>
							<div class="c-icons__header"><?php echo esc_html( ideapark_mod( 'home_icons_header_' . $i ) ); ?></div>
						<?php } ?>
						<?php if ( ideapark_mod( 'home_icons_content_' . $i ) ) { ?>
							<div class="entry-content c-icons__content"><?php echo ideapark_mod( 'home_icons_content_' . $i ); ?></div>
						<?php } ?>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
		<?php if ( ! ideapark_mod( 'home_icons_boxed' ) ) { ?>
	</div>
<?php } ?>
</section>
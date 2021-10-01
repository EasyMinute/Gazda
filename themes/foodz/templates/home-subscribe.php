<?php if ( ideapark_mod( 'home_subscribe_content' ) ) { ?>
	<section id="home-subscribe" <?php echo ideapark_wrap( trim( 'l-section c-subscribe ' . ( ideapark_mod( "home_subscribe_container" ) ? 'l-section--container' : '' ) . ' ' . ( ideapark_mod( "home_subscribe_margins" ) ? 'c-subscribe--top-margin' : '' ) . ' ' . ( ideapark_mod( "home_subscribe_container" ) ? 'c-subscribe--container' : 'c-subscribe--fullwidth' ) ), 'class="', '"' ) ?> <?php echo ideapark_bg( ideapark_mod( 'home_subscribe_background_color' ) ); ?>>
		<?php if ( ! ideapark_mod( 'home_subscribe_container' ) ) { ?>
		<div class="l-section__container"><?php } ?>
			<div class="c-subscribe__wrap <?php ideapark_class( ideapark_mod( "home_subscribe_container" ), 'c-subscribe__wrap--container', '' ); ?>">
				<?php if ( ideapark_mod( 'home_subscribe_header' ) ) { ?>
					<div class="c-subscribe__header">
						<?php echo esc_html( ideapark_mod( 'home_subscribe_header' ) ) ?>
					</div>
				<?php } ?>
				<?php if ( ideapark_mod( 'home_subscribe_subheader' ) ) { ?>
					<div class="c-subscribe__subheader">
						<?php echo esc_html( ideapark_mod( 'home_subscribe_subheader' ) ) ?>
					</div>
				<?php } ?>
				<div class="c-subscribe__code">
					<?php echo ideapark_shortcode( ideapark_mod( 'home_subscribe_content' ) ); ?>
				</div>
			</div>
			<?php if ( ! ideapark_mod( 'home_subscribe_container' ) ) { ?></div><?php } ?>
	</section>
<?php } ?>
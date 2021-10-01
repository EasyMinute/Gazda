<?php if ( ideapark_mod( 'footer_copyright' ) ) { ?>
	<div class="c-footer__copyright">
		<?php echo wp_kses_post( ideapark_mod( 'footer_copyright' ) ); ?>
	</div>
<?php } ?>
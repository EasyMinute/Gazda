<?php if ( ideapark_mod( 'footer_contacts' ) ) { ?>
	<div class="c-footer__contacts">
		<?php echo make_clickable( str_replace( ']]>', ']]&gt;', ideapark_mod( 'footer_contacts' ) ) ); ?>
	</div>
<?php } ?>
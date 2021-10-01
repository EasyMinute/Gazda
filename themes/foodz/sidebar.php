<aside id="sidebar" class="c-post-sidebar <?php ideapark_class( ideapark_mod( 'sticky_sidebar' ), 'js-sticky-sidebar' ); ?>">
    <?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar( ideapark_mod( 'main_sidebar' ) ? ideapark_mod( 'main_sidebar' ) : 1 ) ) ?>
</aside>
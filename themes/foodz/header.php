<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
	<meta name="format-detection" content="telephone=no"/>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php get_template_part( 'templates/header-search' ); ?>

<?php if ( ideapark_mod( 'store_notice' ) == 'top' && function_exists( 'woocommerce_demo_store' ) ) {
	woocommerce_demo_store();
	ideapark_ra( 'wp_footer', 'woocommerce_demo_store' );
} ?>

<header class="l-section" id="main-header">

	<div class="c-header c-header--mobile js-header-mobile">
		<div
			class="c-header__row-1 <?php if ( ideapark_mod( 'mobile_layout' ) == 'layout-type-1' ) { ?>c-header__row-1--3<?php } ?> <?php if ( ideapark_mod( 'sticky_menu_mobile' ) ) { ?>c-header__row-1--sticky js-mobile-sticky-menu <?php } ?>js-header-row">
			<?php get_template_part( 'templates/header-mobile-menu-button' ); ?>
			<?php get_template_part( 'templates/header-logo' ); ?>
			<div
				class="c-header__buttons-block<?php if ( ideapark_mod( 'mobile_layout' ) == 'layout-type-1' ) { ?> c-header__buttons-block--1<?php } else { ?> c-header__buttons-block--2<?php } ?>">
				<?php get_template_part( 'templates/header-search-button' ); ?>
				<?php if ( ideapark_mod( 'mobile_layout' ) == 'layout-type-2' ) { ?>
					<?php get_template_part( 'templates/header-auth' ); ?>
					<?php get_template_part( 'templates/header-wishlist' ); ?>
					<?php get_template_part( 'templates/header-cart' ); ?>
				<?php } ?>
			</div>
		</div>
		<?php if ( ideapark_mod( 'mobile_layout' ) == 'layout-type-1' ) { ?>
			<div class="c-header__row-2">
				<?php
				$content = '';
				$count   = 0;
				?>
				<?php ob_start(); ?>
				<?php get_template_part( 'templates/header-home' ); ?>
				<?php $content .= ( $s = trim( ob_get_clean() ) ); ?>
				<?php $count += ! ! $s; ?>
				<?php ob_start(); ?>
				<?php get_template_part( 'templates/header-wishlist' ); ?>
				<?php $content .= ( $s = trim( ob_get_clean() ) ); ?>
				<?php $count += ! ! $s; ?>
				<?php ob_start(); ?>
				<?php get_template_part( 'templates/header-auth' ); ?>
				<?php $content .= ( $s = trim( ob_get_clean() ) ); ?>
				<?php $count += ! ! $s; ?>
				<?php ob_start(); ?>
				<?php get_template_part( 'templates/header-cart' ); ?>
				<?php $content .= ( $s = trim( ob_get_clean() ) ); ?>
				<?php $count += ! ! $s; ?>
				<?php echo ideapark_wrap( $content, '<div class="c-header__bottom-buttons c-header__bottom-buttons--' . esc_attr( $count ) . '">', '</div>' ) ?>
			</div>
		<?php } ?>

		<div class="c-header__menu js-mobile-menu">
			<div class="c-header__menu-shadow"></div>
			<div class="c-header__menu-wrap">
				<div class="c-header__menu-buttons">
					<button type="button" class="h-cb h-cb--svg c-header__menu-back"
							id="ideapark-menu-back"><?php echo ideapark_svg( 'angle-right', 'c-header__menu-back-svg' ); ?><?php esc_html_e( 'Back', 'foodz' ) ?></button>
					<button type="button" class="h-cb h-cb--svg c-header__menu-close"
							id="ideapark-menu-close"><?php echo ideapark_svg( 'close', 'c-header__menu-close-svg' ); ?></button>
				</div>
				<div class="c-header__menu-content">
					<?php ideapark_get_template_part( 'templates/header-mega-menu', [ 'device' => 'mobile' ] ); ?>
					<?php ideapark_get_template_part( 'templates/header-mobile-top-menu', [ 'device' => 'mobile' ] ); ?>
					<?php get_template_part( 'templates/header-phone' ); ?>
					<?php get_template_part( 'templates/header-text' ); ?>
					<div class="c-header__menu-bottom">
						<?php ideapark_get_template_part( 'templates/soc' ); ?>
						<?php get_template_part( 'templates/header-menu-auth' ); ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div
		class="c-header c-header--desktop js-header-desktop c-header--<?php echo esc_attr( ideapark_mod( 'header_type' ) ); ?>">
		<div class="c-header__bg js-header-bg"
			 data-height="<?php echo esc_attr( ideapark_mod( 'header_image_height' ) ); ?>"></div>
		<?php ob_start(); ?>
		<?php if ( ideapark_mod( 'header_type' ) == 'header-type-4' ) { ?>
			<?php get_template_part( 'templates/header-search-button' ); ?>
		<?php } ?>
		<?php get_template_part( 'templates/header-auth' ); ?>
		<?php if ( ideapark_mod( 'header_type' ) == 'header-type-4' ) { ?>
			<?php get_template_part( 'templates/header-wishlist' ); ?>
			<?php get_template_part( 'templates/header-cart' ); ?>
		<?php } ?>
		<?php $buttons_block = trim( ob_get_clean() ); ?>

		<?php ob_start(); ?>
		<?php if ( ideapark_mod( 'header_type' ) == 'header-type-4' ) { ?>
			<?php get_template_part( 'templates/header-logo' ); ?>
		<?php } ?>
		<?php if ( ideapark_mod( 'header_type' ) == 'header-type-3' || ideapark_mod( 'header_type' ) == 'header-type-4' ) { ?>
			<?php ideapark_get_template_part( 'templates/soc', [ 'class' => 'c-soc--header-row-1' ] ); ?>
		<?php } ?>
		<?php ideapark_get_template_part( 'templates/header-top-menu', [ 'device' => 'desktop' ] ); ?>
		<?php get_template_part( 'templates/header-phone' ); ?>
		<?php get_template_part( 'templates/header-text' ); ?>
		<?php $class = ( ( ideapark_mod( 'header_type' ) == 'header-type-1' || ideapark_mod( 'header_type' ) == 'header-type-2' ) && ! ( ideapark_mod( 'header_menu_text' ) && ( ideapark_mod( 'header_phone' ) || ideapark_mod( 'header_callback' ) ) ) ) ? 'c-header__container--3' : '' ?>
		<?php echo ideapark_wrap( $buttons_block, '<div class="c-header__buttons-block">', '</div>' ) ?>
		<?php echo ideapark_wrap( trim( ob_get_clean() ), '<div class="c-header__row-1 js-header-row"><div class="l-section__container c-header__container ' . $class . '">', '</div></div>' ); ?>

		<div class="c-header__row-2 js-header-row">
			<div
				class="l-section__container c-header__container <?php if ( ideapark_mod( 'header_type' ) == 'header-type-1' || ideapark_mod( 'header_type' ) == 'header-type-2' ) { ?>c-header__container--3<?php } ?> <?php if ( ideapark_mod( 'header_type' ) == 'header-type-4' ) { ?>c-header__container--center<?php } ?>">
				<?php if ( ideapark_mod( 'header_type' ) == 'header-type-1' || ideapark_mod( 'header_type' ) == 'header-type-2' ) { ?>
					<?php ideapark_get_template_part( 'templates/soc', [ 'class' => 'c-soc--header-row-2' ] ); ?>
				<?php } ?>
				<?php if ( ideapark_mod( 'header_type' ) != 'header-type-4' ) { ?>
					<?php get_template_part( 'templates/header-logo' ); ?>
				<?php } ?>
				<?php if ( ideapark_mod( 'header_type' ) == 'header-type-3' || ideapark_mod( 'header_type' ) == 'header-type-4' ) { ?>
					<?php get_template_part( 'templates/header-mega-menu' ); ?>
				<?php } ?>
				<?php if ( ideapark_mod( 'header_type' ) != 'header-type-4' ) { ?>
					<?php ob_start(); ?>
					<?php get_template_part( 'templates/header-search-button' ); ?>
					<?php get_template_part( 'templates/header-wishlist' ); ?>
					<?php get_template_part( 'templates/header-cart' ); ?>
					<?php $content = trim( ob_get_clean() ); ?>
					<?php echo ideapark_wrap( $content, '<div class="c-header__buttons-block">', '</div>' ) ?>
				<?php } ?>
			</div>
		</div>
		<?php if ( ideapark_mod( 'header_type' ) == 'header-type-1' || ideapark_mod( 'header_type' ) == 'header-type-2' ) { ?>
			<div class="c-header__row-3 js-header-row">
				<div class="l-section__container c-header__container c-header__container--center">
					<?php get_template_part( 'templates/header-mega-menu' ); ?>
				</div>
			</div>
		<?php } ?>
		<?php if ( ideapark_mod( 'sticky_menu_desktop' ) ) { ?>
			<div class="c-header__row-sticky c-header__row-sticky--disabled  js-fixed js-desktop-sticky-menu">
				<div class="l-section__container c-header__container">

				</div>
			</div>
		<?php } ?>
	</div>

	<?php if ( ideapark_mod( 'header_callback' ) ) { ?>
		<div class="c-header__callback-popup c-header__callback-popup--disabled">
			<div class="c-header__callback-wrap">
				<div
					class="c-header__callback-header"><?php echo esc_html( ideapark_mod( 'header_callback_title' ) ); ?></div>
				<?php echo ideapark_shortcode( ideapark_mod( 'header_callback_shortcode' ) ); ?>
				<button type="button" class="h-cb h-cb--svg c-header__callback-close js-callback-close"
						id="ideapark-callback-close"><?php echo ideapark_svg( 'close' ); ?></button>
			</div>
		</div>
	<?php } ?>
</header>
<?php if ( ideapark_woocommerce_on() && function_exists( 'wc_print_notices' ) && isset( WC()->session ) ) { ?>
<div class="woocommerce-notices-wrapper"><?php wc_print_notices(); ?></div>
<?php } ?>


<div class="proacto-testing-block">
	<?php 
	$portmone_args = array(
        'payee_id'           => '1185',
        'shop_order_number'  => 'yura_ebat_nykolyshn_12',
        'bill_amount'        => '12',
        'bill_currency'      => 'UAH',
        'success_url'        => get_site_url() . '/test-page/&status=success',
        'failure_url'        => get_site_url() . '/test-page/&status=failure',
        'cms_module_name'    => json_encode(['name' => 'WordPress', 'v' => '1']),
        'encoding'           => 'UTF-8'
    );
    $out = '';
        foreach ($portmone_args as $key => $value) {
            $portmone_args_array[] = "<input type='hidden' name='$key' value='$value'/>";
        }
        $out .= '<form action="' . 'https://www.portmone.com.ua/gateway/' . '" method="post" id="portmone_payment_form">
            ' . implode('', $portmone_args_array) . '
        <input type="submit" id="submit_portmone_payment_form" value="' . 'PAY TEST' . '" /></form>';

    // echo $out;

    function curlRequest($url, $data) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if (200 !== intval($httpCode)) {
            return false;
        }
        return $response;
    }


    $data = array(
        "method" => "result",
        "payee_id" => '1185',
        "login" => 'WDISHOP',
        "password" => 'wdi451',
        "shop_order_number" => 'yura_ebat_nykolyshn_12',
    );


    

    // $result_portmone = curlRequest('https://www.portmone.com.ua/gateway/', $data);
    // if ($result_portmone === false) {
    // 	echo 'Yura to pizda';
    // }
    // $parseXml = simplexml_load_string($result_portmone, 'SimpleXMLElement', LIBXML_NOCDATA);
    // echo '<pre>';
    // var_dump($parseXml->orders->order->status=="PAYED");
    // echo '</pre>';




    ?>

</div>	
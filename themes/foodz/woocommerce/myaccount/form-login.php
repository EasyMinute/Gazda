<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<header class="c-page-header c-page-header--no-padding">
	<h1 class="c-page-header__title c-page-header__title--tabs">
		<a href="#" class="c-page-header__tab-login c-page-header__tab-login--active js-tab-header" data-tab-class="js-login-form"><?php esc_html_e( 'Login', 'woocommerce' ); ?></a>
		<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) { ?>
			<span class="c-page-header__title-or"><?php esc_html_e( 'or', 'foodz' ); ?></span>
			<a href="#" class="c-page-header__tab-register c-page-header__tab-register--not-active js-tab-header" data-tab-class="js-register-form"><?php esc_html_e( 'Create Account', 'foodz' ); ?></a>
		<?php } ?>
	</h1>
</header>

<?php do_action( 'woocommerce_before_customer_login_form' ); ?>

<div class="c-login" id="customer_login">

	<div class="c-login__form js-login-form c-login__form--active">
		<form class="c-form" method="post">

			<?php do_action( 'woocommerce_login_form_start' ); ?>

			<p class="c-form__row">
				<label class="c-form__label" for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>
					<span class="required">*</span></label>
				<input type="text" class="c-form__input c-form__input--full c-form__input--fill woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" />
			</p>
			<p class="c-form__row">
				<label class="c-form__label" for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>
					<span class="required">*</span></label>
				<input class="c-form__input c-form__input--full c-form__input--fill woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" />
			</p>

			<?php do_action( 'woocommerce_login_form' ); ?>

			<p class="c-form__row c-form__row--inline">
				<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
				<label class="c-form__label">
					<input class="c-form__checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /><i></i> <?php esc_html_e( 'Remember me', 'woocommerce' ); ?>
				</label>
				<span class="c-login__lost-password">
					<a class="c-login__lost-password-link" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
				</span>
			</p>

			<p class="c-form__row">
				<button type="submit" class="c-form__button woocommerce-Button button" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
			</p>



			<?php do_action( 'woocommerce_login_form_end' ); ?>

		</form>
	</div>

	<?php if ( get_option( 'woocommerce_enable_myaccount_registration' ) === 'yes' ) { ?>

		<div class="c-login__form js-register-form">
			<form method="post" class="c-form" <?php do_action( 'woocommerce_register_form_tag' ); ?>>

				<?php do_action( 'woocommerce_register_form_start' ); ?>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

					<p class="c-form__row">
						<label class="c-form__label" for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>
							<span class="required">*</span></label>
						<input type="text" class="c-form__input c-form__input--full c-form__input--fill woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" />
					</p>

				<?php endif; ?>

				<p class="c-form__row">
					<label class="c-form__label" for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>
						<span class="required">*</span></label>
					<input type="email" class="c-form__input c-form__input--full c-form__input--fill woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" />
				</p>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

					<p class="c-form__row">
						<label class="c-form__label" for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>
							<span class="required">*</span></label>
						<input type="password" class="c-form__input c-form__input--full c-form__input--fill woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" />
					</p>

				<?php else : ?>

					<p class="c-form__row"><?php esc_html_e( 'A password will be sent to your email address.', 'woocommerce' ); ?></p>

				<?php endif; ?>

				<?php do_action( 'woocommerce_register_form' ); ?>

				<p class="c-form__row">
					<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
					<button type="submit" class="c-form__button woocommerce-Button button" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
				</p>

				<?php do_action( 'woocommerce_register_form_end' ); ?>

			</form>
		</div>

	<?php } ?>

	<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
</div>

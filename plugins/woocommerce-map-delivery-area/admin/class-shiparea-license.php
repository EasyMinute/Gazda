<?php

/**
 * License key fun.
 *
 * @package    ShipArea
 * @author     ShipArea
 * @since 2.0.0
 * @license    GPL-2.0+
 * @copyright  Copyright (c) 2016, Timersys LLC
 */
class ShipArea_License {

	/**
	 * Holds any license error messages.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	public $errors = [];

	/**
	 * Holds any license success messages.
	 *
	 * @since 2.0.0
	 * @var array
	 */
	public $success = [];

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		// Admin notices.
		if ( ! isset( $_GET['section'] ) || 'shiparea' !== $_GET['section'] ) {
			add_action( 'admin_notices', [ $this, 'notices' ] );
		}

		// Periodic background license check.
		if ( $this->get() ) {
			add_action( 'init', [ $this, 'maybe_validate_key' ] );
		}
	}

	/**
	 * Load the license key.
	 *
	 * @since 2.0.0
	 */
	public function get() {

		// Check for license key.
		$key = shiparea_settings( 'key', false, 'shiparea_license' );

		// Allow wp-config constant to pass key.
		if ( ! $key && defined( 'SHIPAREA_LICENSE_KEY' ) ) {
			$key = SHIPAREA_LICENSE_KEY;
		}

		return $key;
	}

	/**
	 * Load the license key level.
	 *
	 * @since 2.0.0
	 */
	public function type() {

		$type = shiparea_settings( 'type', false, 'shiparea_license' );

		return $type;
	}

	/**
	 * Verifies a license key entered by the user.
	 *
	 * @param string $key
	 * @param bool $ajax
	 * @param bool $forced Force to set contextual messages (false by default).
	 *
	 * @return bool
	 * @throws Exception
	 * @since 2.0.0
	 */
	public function verify_key( $key = '', $ajax = false, $forced = false) {

		if ( empty( $key ) ) {
			return false;
		}

		// Perform a request to verify the key.
		$verify = $this->perform_remote_request( 'activate_license', [ 'license' => $key ] );

		// If it returns false, send back a generic error message and return.
		if ( ! $verify ) {
			$msg = esc_html__( 'There was an error connecting to the remote key API. Please try again later.', 'shiparea' );
			if ( $ajax ) {
				wp_send_json_error( $msg );
			} else {
				$this->errors[] = $msg;
				return false;
			}
		}

		// If an error is returned, set the error and return.
		if ( empty( $verify->license ) || $verify->license == 'invalid' ) {
			$msg = esc_html__( "The provided license it's not valid", 'shiparea' );

			if( ! empty( $verify->error ) ) {
				switch( $verify->error ) {
					case 'expired' :
						$msg = sprintf(
							esc_html__( 'Your license key expired on %s.', 'shiparea' ),
							date_i18n( get_option( 'date_format' ), strtotime( $verify->expires, current_time( 'timestamp' ) ) )
						);
						break;
					case 'revoked' :
					case 'disabled' :
						$msg = esc_html__( 'Your license key has been disabled.', 'shiparea' );
						break;
					case 'missing' :
						$msg = esc_html__( 'Invalid license.', 'shiparea' );
						break;
					case 'invalid' :
					case 'site_inactive' :
						$msg = esc_html__( 'Your license is not active for this URL.', 'shiparea' );
						break;
					case 'item_name_mismatch' :
						$msg = sprintf( esc_html__( 'This appears to be an invalid license key for %s.', 'shiparea' ), 'Woocommerce Shipping Rates by City' );
						break;
					case 'no_activations_left':
						$msg = esc_html__( 'Your license key has reached its activation limit.', 'shiparea' );
						break;
					default :
						$msg = esc_html__( 'An error occurred, please try again.', 'shiparea' );
						break;
				}
				if( $forced ) {
					$msg = 'Woocommerce Shipping Map Delivery Area: ' . $msg;
				}
			}
			if ( $ajax ) {
				wp_send_json_error( $msg );
			} else {
				$this->errors[] = $msg;
				return false;
			}
		}

		$option = (array) get_option( 'shiparea_license', [] );

		// If the license is disabled, set the transient and disabled flag and return.
		if ( $verify->license == 'disabled' ) {
			$option['is_expired']  = false;
			$option['is_disabled'] = true;
			$option['is_invalid']  = false;
			update_option( 'shiparea_license', $option );
			if ( $ajax ) {
				wp_send_json_error( esc_html__( 'Your license key for Woocommerce Shipping Map Delivery Area has been disabled. Please use a different key to continue receiving automatic updates.', 'shiparea' ) );
			}

			return;
		}

		$success = esc_html__( 'Congratulations! This site is now receiving automatic updates.', 'shiparea' );

		// Otherwise, our request has been done successfully. Update the option and set the success message.

		$option['key']         = $key;
		$option['is_expired']  = false;
		$option['is_disabled'] = false;
		$option['is_invalid']  = false;
		$this->success[]       = $success;
		update_option( 'shiparea_license', $option );
		delete_transient( '_shiparea_addons' );

		delete_site_transient( 'update_plugins' );
		wp_cache_delete( 'plugins', 'plugins' );

		if ( $ajax ) {
			wp_send_json_success($success);
		}
	}

	/**
	 * Maybe validates a license key entered by the user.
	 *
	 * @return void Return early if the transient has not expired yet.
	 * @throws Exception
	 * @since 2.0.0
	 */
	public function maybe_validate_key() {

		$key = $this->get();

		if ( ! $key ) {
			return;
		}

		// Perform a request to validate the key  - Only run every 12 hours.
		$timestamp = get_option( 'shiparea_license_updates' );

		if ( ! $timestamp ) {
			$timestamp = strtotime( '+24 hours' );
			update_option( 'shiparea_license_updates', $timestamp );
			$this->verify_key( $key, false, true );
		} else {
			$current_timestamp = time();
			if ( $current_timestamp < $timestamp ) {
				return;
			} else {
				update_option( 'shiparea_license_updates', strtotime( '+24 hours' ) );
				$this->verify_key( $key, false, true );
			}
		}
	}

	/**
	 * Deactivates a license key entered by the user.
	 *
	 * @param bool $ajax
	 *
	 * @since 2.0.0
	 *
	 */
	public function deactivate_key( $ajax = false ) {

		$key = $this->get();

		if ( ! $key ) {
			return;
		}

		// Perform a request to deactivate the key.
		$deactivate = $this->perform_remote_request( 'deactivate_license', [ 'license' => $key ] );

		// If it returns false, send back a generic error message and return.
		if ( ! $deactivate ) {
			$msg = esc_html__( 'There was an error connecting to the remote key API. Please try again later.', 'shiparea' );
			if ( $ajax ) {
				wp_send_json_error( $msg );
			} else {
				$this->errors[] = $msg;

				return;
			}
		}

		// If an error is returned, set the error and return.
		if ( ! empty( $deactivate->error ) ) {
			if ( $ajax ) {
				wp_send_json_error( $deactivate->error );
			} else {
				$this->errors[] = $deactivate->error;

				return;
			}
		}

		// Otherwise, our request has been done successfully. Reset the option and set the success message.
		$success         = esc_html__( 'You have deactivated the key from this site successfully.', 'shiparea' );
		$this->success[] = $success;
		update_option( 'shiparea_license', '' );
		delete_transient( '_shiparea_addons' );

		if ( $ajax ) {
			wp_send_json_success( $success );
		}
	}

	/**
	 * Returns possible license key error flag.
	 *
	 * @return bool True if there are license key errors, false otherwise.
	 * @since 2.0.0
	 */
	public function get_errors() {

		$option = get_option( 'shiparea_license' );

		return ! empty( $option['is_expired'] ) || ! empty( $option['is_disabled'] ) || ! empty( $option['is_invalid'] );
	}

	/**
	 * Outputs any notices generated by the class.
	 *
	 * @param bool $below_h2
	 *
	 * @since 2.0.0
	 *
	 */
	public function notices( $below_h2 = false ) {

		// Grab the option and output any nag dealing with license keys.
		$key      = $this->get();
		$option   = get_option( 'shiparea_license' );
		$below_h2 = $below_h2 ? 'below-h2' : '';

		// If there is no license key, output nag about ensuring key is set for automatic updates.
		if ( ! $key ) :
			?>
			<div class="notice notice-info <?php echo $below_h2; ?> shiparea-license-notice">
				<p>
					<?php
					printf(
						wp_kses(
						/* translators: %s - plugin settings page URL. */
							__( 'Please <a href="%s">enter and activate</a> your license key for Woocommerce Map Delivery Area to enable automatic updates.', 'shiparea' ),
							[
								'a' => [
									'href' => [],
								],
							]
						),
						esc_url( add_query_arg( [ 'page' => 'wc-settings', 'tab' => 'wc_timersys', 'section' => 'shiparea' ], admin_url( 'admin.php' ) ) )
					);
					?>
				</p>
			</div>
		<?php
		endif;

		// If a key has expired, output nag about renewing the key.
		if ( isset( $option['is_expired'] ) && $option['is_expired'] ) :
			?>
			<div class="error notice <?php echo $below_h2; ?> shiparea-license-notice">
				<p>
					<?php
					printf(
						wp_kses(
							__( 'Your license key for Woocommerce Shipping Map Delivery Area has expired. <a href="%s" target="_blank" rel="noopener noreferrer">Please click here to renew your license key and continue receiving automatic updates.</a>', 'shiparea' ),
							[
								'a' => [
									'href'   => [],
									'target' => [],
									'rel'    => [],
								],
							]
						),
						'https://timersys.com/login/'
					);
					?>
				</p>
			</div>
		<?php
		endif;

		// If a key has been disabled, output nag about using another key.
		if ( isset( $option['is_disabled'] ) && $option['is_disabled'] ) :
			?>
			<div class="error notice <?php echo $below_h2; ?> shiparea-license-notice">
				<p><?php esc_html_e( 'Your license key for Woocommerce Shipping Map Delivery Area has been disabled. Please use a different key to continue receiving automatic updates.', 'shiparea' ); ?></p>
			</div>
		<?php
		endif;

		// If a key is invalid, output nag about using another key.
		if ( isset( $option['is_invalid'] ) && $option['is_invalid'] ) :
			?>
			<div class="error notice <?php echo $below_h2; ?> shiparea-license-notice">
				<p><?php esc_html_e( 'Your license key for Woocommerce Shipping Map Delivery Area is invalid. The key no longer exists or the user associated with the key has been deleted. Please use a different key to continue receiving automatic updates.', 'shiparea' ); ?></p>
			</div>
		<?php
		endif;

		// If there are any license errors, output them now.
		if ( ! empty( $this->errors ) ) :
			?>
			<div class="error notice <?php echo $below_h2; ?> shiparea-license-notice">
				<p><?php echo implode( '<br>', $this->errors ); ?></p>
			</div>
		<?php
		endif;

		// If there are any success messages, output them now.
		if ( ! empty( $this->success ) ) :
			?>
			<div class="updated notice <?php echo $below_h2; ?> shiparea-license-notice">
				<p><?php echo implode( '<br>', $this->success ); ?></p>
			</div>
		<?php
		endif;

	}

	/**
	 * Retrieves addons from the stored transient or remote server.
	 *
	 * @param bool $force
	 *
	 * @return array|bool|mixed 2.0.0
	 */
	public function addons( $force = false ) {

		$key = $this->get();

		if ( ! $key ) {
			return false;
		}

		$addons = get_transient( '_shiparea_addons' );

		if ( $force || false === $addons ) {
			$addons = $this->get_addons();
		}

		return $addons;
	}

	/**
	 * Pings the remote server for addons data.
	 *
	 * @return bool|array False if no key or failure, array of addon data otherwise.
	 * @since 2.0.0
	 *
	 */
	public function get_addons() {

		$key    = $this->get();
		$addons = $this->perform_remote_request( 'get-addons-data', [ 'tgm-updater-key' => $key ] );

		// If there was an API error, set transient for only 10 minutes.
		if ( ! $addons ) {
			set_transient( '_shiparea_addons', false, 10 * MINUTE_IN_SECONDS );

			return false;
		}

		// If there was an error retrieving the addons, set the error.
		if ( isset( $addons->error ) ) {
			set_transient( '_shiparea_addons', false, 10 * MINUTE_IN_SECONDS );

			return false;
		}

		// Otherwise, our request worked. Save the data and return it.
		set_transient( '_shiparea_addons', $addons, DAY_IN_SECONDS );

		return $addons;
	}

	/**
	 * Queries the remote URL via wp_remote_post and returns a json decoded response.
	 *
	 * @param string $action The name of the $_POST action var.
	 * @param array $body The content to retrieve from the remote URL.
	 *
	 * @return string|bool Json decoded response on success, false on failure.
	 * @since 2.0.0
	 *
	 */
	public function perform_remote_request( $action, $body = [] ) {

		// Data to send to the API
		$api_params = [
			'edd_action' => $action,
			'item_id'  => SHIPAREA_EDD_ID
		];

		$api_params = array_merge( $api_params, $body);

		// Call the API
		$response = wp_remote_get( add_query_arg( $api_params, SHIPAREA_UPDATER_API ), [ 'sslverify' => false ] );

		// Perform the query and retrieve the response.
		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		// Bail out early if there are any errors.
		if ( is_wp_error( $response ) || 200 != $response_code ) {
			return false;
		}

		// Return the json decoded content.
		return json_decode( $response_body );
	}

	/**
	 * Checks to see if the site is using an active license.
	 *
	 * @return bool
	 * @since 2.0.0
	 *
	 */
	public function is_active() {

		$license = get_option( 'shiparea_license', false );

		if (
			empty( $license ) ||
			! empty( $license['is_expired'] ) ||
			! empty( $license['is_disabled'] ) ||
			! empty( $license['is_invalid'] )
		) {
			return false;
		}

		return true;
	}
}

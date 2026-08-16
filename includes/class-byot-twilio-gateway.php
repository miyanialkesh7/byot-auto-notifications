<?php
/**
 * Twilio SMS gateway.
 *
 * @package BYOT_Auto_Notifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sends SMS notifications through the Twilio Messages API.
 */
class BYOT_Twilio_Gateway extends BYOT_Gateway {

	/**
	 * Whether the Twilio SID, token, and from-number are all set.
	 *
	 * @return bool
	 */
	public function is_configured() {
		return ! empty( $this->settings['twilio_sid'] )
			&& ! empty( $this->settings['twilio_token'] )
			&& ! empty( $this->settings['twilio_from'] );
	}

	/**
	 * Sends an SMS via the Twilio Messages API.
	 *
	 * @param string $to      E.164 phone number to send to.
	 * @param string $message Message body to send.
	 * @return bool True on success.
	 */
	public function send( $to, $message ) {
		if ( ! $this->is_configured() ) {
			$this->log( 'Twilio gateway is not configured.', 'error' );
			return false;
		}

		$sid = $this->settings['twilio_sid'];
		$url = 'https://api.twilio.com/2010-04-01/Accounts/' . rawurlencode( $sid ) . '/Messages.json';

		$response = wp_remote_post(
			$url,
			array(
				'headers' => array(
					// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Required by the Twilio API's HTTP Basic Auth scheme, not used to obfuscate code.
					'Authorization' => 'Basic ' . base64_encode( $sid . ':' . $this->settings['twilio_token'] ),
				),
				'body'    => array(
					'To'   => $to,
					'From' => $this->settings['twilio_from'],
					'Body' => $message,
				),
				'timeout' => 45,
			)
		);

		return $this->handle_response( $response, $to, 'Twilio' );
	}

	/**
	 * Logs the result of a gateway HTTP request.
	 *
	 * @param array|WP_Error $response HTTP API response.
	 * @param string         $to       Recipient phone number, for logging.
	 * @param string         $label    Gateway name, for logging.
	 * @return bool True when the response was a 2xx success.
	 */
	private function handle_response( $response, $to, $label ) {
		if ( is_wp_error( $response ) ) {
			$this->log( "{$label} request failed: " . $response->get_error_message(), 'error' );
			return false;
		}

		$code = wp_remote_retrieve_response_code( $response );

		if ( $code >= 200 && $code < 300 ) {
			$this->log( "{$label} message sent to {$to}.", 'info' );
			return true;
		}

		$this->log( "{$label} HTTP error {$code}: " . wp_remote_retrieve_body( $response ), 'error' );
		return false;
	}
}

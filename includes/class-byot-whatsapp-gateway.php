<?php
/**
 * WhatsApp Cloud API gateway.
 *
 * @package BYOT_Auto_Notifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sends WhatsApp notifications through the Meta WhatsApp Cloud API.
 */
class BYOT_WhatsApp_Gateway extends BYOT_Gateway {

	const API_VERSION = 'v18.0';

	/**
	 * Whether the WhatsApp access token and phone number ID are both set.
	 *
	 * @return bool
	 */
	public function is_configured() {
		return ! empty( $this->settings['whatsapp_token'] ) && ! empty( $this->settings['whatsapp_phone_id'] );
	}

	/**
	 * Sends a WhatsApp text message via the Meta Cloud API.
	 *
	 * @param string $to      E.164 phone number to send to.
	 * @param string $message Message body to send.
	 * @return bool True on success.
	 */
	public function send( $to, $message ) {
		if ( ! $this->is_configured() ) {
			$this->log( 'WhatsApp gateway is not configured.', 'error' );
			return false;
		}

		$phone_id = $this->settings['whatsapp_phone_id'];
		$url      = sprintf( 'https://graph.facebook.com/%s/%s/messages', self::API_VERSION, rawurlencode( $phone_id ) );

		$payload = array(
			'messaging_product' => 'whatsapp',
			'recipient_type'    => 'individual',
			'to'                => ltrim( $to, '+' ),
			'type'              => 'text',
			'text'              => array( 'body' => $message ),
		);

		$response = wp_remote_post(
			$url,
			array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $this->settings['whatsapp_token'],
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode( $payload ),
				'timeout' => 45,
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->log( 'WhatsApp request failed: ' . $response->get_error_message(), 'error' );
			return false;
		}

		$code = wp_remote_retrieve_response_code( $response );

		if ( $code >= 200 && $code < 300 ) {
			$this->log( "WhatsApp message sent to {$to}.", 'info' );
			return true;
		}

		$this->log( "WhatsApp HTTP error {$code}: " . wp_remote_retrieve_body( $response ), 'error' );
		return false;
	}
}

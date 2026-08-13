<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Sends SMS notifications through the Twilio Messages API.
 */
class BYOT_Twilio_Gateway extends BYOT_Gateway {

	public function is_configured() {
		return ! empty( $this->settings['twilio_sid'] )
			&& ! empty( $this->settings['twilio_token'] )
			&& ! empty( $this->settings['twilio_from'] );
	}

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

<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Base class for outbound messaging gateways (SMS, WhatsApp, ...).
 */
abstract class BYOT_Gateway {

	/** @var array */
	protected $settings;

	public function __construct() {
		$this->settings = get_option( 'byot_an_settings', array() );
	}

	/**
	 * Sends a message to the given E.164 phone number.
	 *
	 * @return bool True on success.
	 */
	abstract public function send( $to, $message );

	/**
	 * Whether the gateway has the credentials required to send.
	 */
	abstract public function is_configured();

	protected function log( $message, $level = 'info' ) {
		if ( function_exists( 'wc_get_logger' ) ) {
			wc_get_logger()->log( $level, $message, array( 'source' => 'byot-auto-notifications' ) );
		}
	}

	/**
	 * Replaces message template placeholders with order data.
	 *
	 * @param string   $message Template containing {placeholder} tokens.
	 * @param WC_Order $order   Order to source replacement values from.
	 */
	public function replace_placeholders( $message, $order ) {
		$replacements = array(
			'{customer_name}' => trim( $order->get_billing_first_name() . ' ' . $order->get_billing_last_name() ),
			'{order_id}'      => $order->get_order_number(),
			'{order_total}'   => wc_format_decimal( $order->get_total(), 2 ),
			'{status}'        => wc_get_order_status_name( $order->get_status() ),
			'{site_name}'     => wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
		);

		return str_replace( array_keys( $replacements ), array_values( $replacements ), $message );
	}
}

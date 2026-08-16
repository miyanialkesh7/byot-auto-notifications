<?php
/**
 * WooCommerce order status change listener.
 *
 * @package BYOT_Auto_Notifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Listens for WooCommerce order status changes and dispatches
 * the configured gateway when a matching notification is enabled.
 */
class BYOT_Order_Handler {

	/**
	 * Hooks into WooCommerce order status changes.
	 */
	public function __construct() {
		add_action( 'woocommerce_order_status_changed', array( $this, 'handle_status_change' ), 10, 4 );
	}

	/**
	 * Sends a notification when an order enters a status the merchant
	 * has enabled, provided the gateway is configured and the customer
	 * has a phone number that normalizes to E.164.
	 *
	 * @param int      $order_id   Order ID.
	 * @param string   $old_status Previous order status.
	 * @param string   $new_status New order status.
	 * @param WC_Order $order      Order object.
	 */
	public function handle_status_change( $order_id, $old_status, $new_status, $order ) {
		$settings = get_option( 'byot_an_settings', array() );

		if ( empty( $settings['enabled'] ) || 'yes' !== $settings['enabled'] ) {
			return;
		}

		$gateway_type = ! empty( $settings['gateway'] ) ? $settings['gateway'] : '';
		if ( '' === $gateway_type ) {
			return;
		}

		if ( empty( $settings[ 'notify_' . $new_status ] ) || 'yes' !== $settings[ 'notify_' . $new_status ] ) {
			return;
		}

		$template = ! empty( $settings[ 'message_' . $new_status ] ) ? $settings[ 'message_' . $new_status ] : '';
		if ( '' === trim( $template ) ) {
			return;
		}

		$phone = $order->get_billing_phone();
		if ( empty( $phone ) ) {
			$this->log( "Order {$order_id}: no billing phone on file, skipping notification.", 'warning' );
			return;
		}

		$normalized = BYOT_Validator::normalize_phone( $phone, $order->get_billing_country() );
		if ( ! $normalized ) {
			$this->log( "Order {$order_id}: could not normalize phone number '{$phone}'.", 'warning' );
			return;
		}

		$gateway = $this->get_gateway( $gateway_type );
		if ( ! $gateway ) {
			return;
		}

		if ( ! $gateway->is_configured() ) {
			$this->log( "Order {$order_id}: gateway '{$gateway_type}' is not configured, skipping notification.", 'warning' );
			return;
		}

		/**
		 * Fires right before a notification is sent for an order status change.
		 *
		 * @param WC_Order $order
		 * @param string   $new_status
		 */
		do_action( 'byot_an_before_send_notification', $order, $new_status );

		$gateway->send( $normalized, $gateway->replace_placeholders( $template, $order ) );
	}

	/**
	 * Instantiates the gateway matching the configured type.
	 *
	 * @param string $type Gateway type ('twilio' or 'whatsapp').
	 * @return BYOT_Gateway|null Gateway instance, or null when unknown.
	 */
	private function get_gateway( $type ) {
		switch ( $type ) {
			case 'twilio':
				return new BYOT_Twilio_Gateway();
			case 'whatsapp':
				return new BYOT_WhatsApp_Gateway();
			default:
				return null;
		}
	}

	/**
	 * Writes a message to the WooCommerce logger, when available.
	 *
	 * @param string $message Message to log.
	 * @param string $level   Log level (info, warning, error, ...).
	 */
	private function log( $message, $level = 'info' ) {
		if ( function_exists( 'wc_get_logger' ) ) {
			wc_get_logger()->log( $level, $message, array( 'source' => 'byot-auto-notifications' ) );
		}
	}
}

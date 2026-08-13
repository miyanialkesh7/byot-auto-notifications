<?php
/**
 * Plugin Name: BYOT Auto Notifications
 * Description: Notificari automate WhatsApp si SMS pentru WooCommerce, in functie de statusul comenzii. Suport international pentru numere de telefon.
 * Version: 1.0.0
 * Author: Byot
 * License: GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: byot-auto-notifications
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'BYOT_AN_VERSION', '1.0.0' );
define( 'BYOT_AN_FILE', __FILE__ );
define( 'BYOT_AN_PATH', plugin_dir_path( __FILE__ ) );
define( 'BYOT_AN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Bootstraps the plugin once WooCommerce is confirmed active.
 * Wired on plugins_loaded so translations and dependency checks
 * run before any WooCommerce hooks are registered.
 */
final class BYOT_Auto_Notifications {

	/** @var self|null */
	private static $instance = null;

	private $admin;
	private $order_handler;

	public static function instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_action( 'plugins_loaded', array( $this, 'init' ), 20 );
	}

	public function load_textdomain() {
		load_plugin_textdomain( 'byot-auto-notifications', false, dirname( plugin_basename( BYOT_AN_FILE ) ) . '/languages' );
	}

	public function init() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			return;
		}

		$this->includes();

		$this->admin         = new BYOT_Admin();
		$this->order_handler = new BYOT_Order_Handler();
	}

	private function includes() {
		require_once BYOT_AN_PATH . 'includes/class-byot-validator.php';
		require_once BYOT_AN_PATH . 'includes/class-byot-gateway.php';
		require_once BYOT_AN_PATH . 'includes/class-byot-twilio-gateway.php';
		require_once BYOT_AN_PATH . 'includes/class-byot-whatsapp-gateway.php';
		require_once BYOT_AN_PATH . 'includes/class-byot-order-handler.php';
		require_once BYOT_AN_PATH . 'includes/class-byot-admin.php';
	}

	public function woocommerce_missing_notice() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-error"><p>' .
			sprintf(
				/* translators: %s: plugin name */
				esc_html__( '%s requires WooCommerce to be installed and active.', 'byot-auto-notifications' ),
				'<strong>BYOT Auto Notifications</strong>'
			) .
			'</p></div>';
	}
}

BYOT_Auto_Notifications::instance();

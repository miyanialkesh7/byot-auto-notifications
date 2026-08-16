<?php
/**
 * Admin settings page controller.
 *
 * @package BYOT_Auto_Notifications
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and renders the plugin's settings page under WooCommerce.
 */
class BYOT_Admin {

	const OPTION_NAME = 'byot_an_settings';
	const PAGE_SLUG   = 'byot-auto-notifications';

	/**
	 * Cached settings loaded from the options table.
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * Registers the admin hooks for the settings page.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Adds the settings page as a WooCommerce submenu item.
	 */
	public function add_menu_page() {
		add_submenu_page(
			'woocommerce',
			__( 'BYOT Notifications', 'byot-auto-notifications' ),
			__( 'BYOT Notifications', 'byot-auto-notifications' ),
			'manage_woocommerce',
			self::PAGE_SLUG,
			array( $this, 'render_page' )
		);
	}

	/**
	 * Enqueues the settings page's CSS/JS, only on the plugin's own screen.
	 *
	 * @param string $hook Current admin page hook suffix.
	 */
	public function enqueue_assets( $hook ) {
		if ( 'woocommerce_page_' . self::PAGE_SLUG !== $hook ) {
			return;
		}
		wp_enqueue_style( 'byot-an-admin', BYOT_AN_URL . 'assets/css/admin.css', array(), BYOT_AN_VERSION );
		wp_enqueue_script( 'byot-an-admin', BYOT_AN_URL . 'assets/js/admin.js', array( 'jquery' ), BYOT_AN_VERSION, true );
	}

	/**
	 * Registers the plugin's settings with the Settings API.
	 */
	public function register_settings() {
		register_setting(
			'byot_an_settings_group',
			self::OPTION_NAME,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize_settings' ),
			)
		);
	}

	/**
	 * Sanitizes and whitelists settings submitted from the settings form.
	 *
	 * @param array $input Raw submitted settings.
	 * @return array Sanitized settings.
	 */
	public function sanitize_settings( $input ) {
		$output = array();

		$output['enabled'] = ! empty( $input['enabled'] ) ? 'yes' : 'no';
		$output['gateway'] = ! empty( $input['gateway'] ) && in_array( $input['gateway'], array( 'twilio', 'whatsapp' ), true )
			? $input['gateway']
			: '';

		$output['twilio_sid']   = isset( $input['twilio_sid'] ) ? sanitize_text_field( $input['twilio_sid'] ) : '';
		$output['twilio_token'] = isset( $input['twilio_token'] ) ? sanitize_text_field( $input['twilio_token'] ) : '';
		$output['twilio_from']  = isset( $input['twilio_from'] ) ? sanitize_text_field( $input['twilio_from'] ) : '';

		$output['whatsapp_token']    = isset( $input['whatsapp_token'] ) ? sanitize_text_field( $input['whatsapp_token'] ) : '';
		$output['whatsapp_phone_id'] = isset( $input['whatsapp_phone_id'] ) ? sanitize_text_field( $input['whatsapp_phone_id'] ) : '';

		foreach ( $this->get_order_statuses() as $status => $label ) {
			$output[ 'notify_' . $status ]  = ! empty( $input[ 'notify_' . $status ] ) ? 'yes' : 'no';
			$output[ 'message_' . $status ] = isset( $input[ 'message_' . $status ] ) ? sanitize_textarea_field( $input[ 'message_' . $status ] ) : '';
		}

		return $output;
	}

	/**
	 * Renders the plugin's settings page markup.
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$this->settings = get_option( self::OPTION_NAME, array() );
		?>
		<div class="wrap byot-admin-wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'byot_an_settings_group' ); ?>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Notifications', 'byot-auto-notifications' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[enabled]" value="yes" <?php checked( 'yes', $this->get_setting( 'enabled' ) ); ?>>
								<?php esc_html_e( 'Send automated WhatsApp / SMS notifications on order status changes', 'byot-auto-notifications' ); ?>
							</label>
						</td>
					</tr>

					<tr>
						<th scope="row"><?php esc_html_e( 'Gateway', 'byot-auto-notifications' ); ?></th>
						<td>
							<select id="byot-gateway-select" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[gateway]">
								<option value=""><?php esc_html_e( '— Select —', 'byot-auto-notifications' ); ?></option>
								<option value="twilio" <?php selected( 'twilio', $this->get_setting( 'gateway' ) ); ?>><?php esc_html_e( 'Twilio (SMS)', 'byot-auto-notifications' ); ?></option>
								<option value="whatsapp" <?php selected( 'whatsapp', $this->get_setting( 'gateway' ) ); ?>><?php esc_html_e( 'WhatsApp Cloud API', 'byot-auto-notifications' ); ?></option>
							</select>
						</td>
					</tr>

					<tr class="byot-section-title byot-gateway-twilio">
						<td colspan="2"><h2><?php esc_html_e( 'Twilio Settings', 'byot-auto-notifications' ); ?></h2></td>
					</tr>
					<tr class="byot-gateway-twilio">
						<th scope="row"><?php esc_html_e( 'Account SID', 'byot-auto-notifications' ); ?></th>
						<td><input type="text" class="regular-text" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[twilio_sid]" value="<?php echo esc_attr( $this->get_setting( 'twilio_sid' ) ); ?>" autocomplete="off"></td>
					</tr>
					<tr class="byot-gateway-twilio">
						<th scope="row"><?php esc_html_e( 'Auth Token', 'byot-auto-notifications' ); ?></th>
						<td><input type="password" class="regular-text" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[twilio_token]" value="<?php echo esc_attr( $this->get_setting( 'twilio_token' ) ); ?>" autocomplete="new-password"></td>
					</tr>
					<tr class="byot-gateway-twilio">
						<th scope="row"><?php esc_html_e( 'From Number', 'byot-auto-notifications' ); ?></th>
						<td><input type="text" class="regular-text" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[twilio_from]" value="<?php echo esc_attr( $this->get_setting( 'twilio_from' ) ); ?>" placeholder="+1234567890"></td>
					</tr>

					<tr class="byot-section-title byot-gateway-whatsapp">
						<td colspan="2"><h2><?php esc_html_e( 'WhatsApp Cloud API Settings', 'byot-auto-notifications' ); ?></h2></td>
					</tr>
					<tr class="byot-gateway-whatsapp">
						<th scope="row"><?php esc_html_e( 'Access Token', 'byot-auto-notifications' ); ?></th>
						<td><input type="password" class="regular-text" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[whatsapp_token]" value="<?php echo esc_attr( $this->get_setting( 'whatsapp_token' ) ); ?>" autocomplete="new-password"></td>
					</tr>
					<tr class="byot-gateway-whatsapp">
						<th scope="row"><?php esc_html_e( 'Phone Number ID', 'byot-auto-notifications' ); ?></th>
						<td><input type="text" class="regular-text" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[whatsapp_phone_id]" value="<?php echo esc_attr( $this->get_setting( 'whatsapp_phone_id' ) ); ?>"></td>
					</tr>

					<tr class="byot-section-title">
						<td colspan="2"><h2><?php esc_html_e( 'Message Templates', 'byot-auto-notifications' ); ?></h2></td>
					</tr>
					<tr>
						<td colspan="2">
							<p class="description">
								<?php esc_html_e( 'Available placeholders:', 'byot-auto-notifications' ); ?>
								<code>{customer_name}</code>, <code>{order_id}</code>, <code>{order_total}</code>, <code>{status}</code>, <code>{site_name}</code>
							</p>
						</td>
					</tr>
					<?php foreach ( $this->get_order_statuses() as $status => $label ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $label ); ?></th>
							<td>
								<label>
									<input type="checkbox" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[notify_<?php echo esc_attr( $status ); ?>]" value="yes" <?php checked( 'yes', $this->get_setting( 'notify_' . $status ) ); ?>>
									<?php esc_html_e( 'Enable for this status', 'byot-auto-notifications' ); ?>
								</label>
								<br><br>
								<textarea class="large-text" rows="3" name="<?php echo esc_attr( self::OPTION_NAME ); ?>[message_<?php echo esc_attr( $status ); ?>]"><?php echo esc_textarea( $this->get_setting( 'message_' . $status ) ); ?></textarea>
							</td>
						</tr>
					<?php endforeach; ?>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Returns the WooCommerce order statuses available for notifications.
	 *
	 * @return array<string,string> Status key (without wc- prefix) => label.
	 */
	private function get_order_statuses() {
		$statuses = array();
		foreach ( wc_get_order_statuses() as $key => $label ) {
			$statuses[ str_replace( 'wc-', '', $key ) ] = $label;
		}
		return $statuses;
	}

	/**
	 * Reads a single value from the cached settings.
	 *
	 * @param string $key Setting key.
	 * @return string Setting value, or an empty string when not set.
	 */
	private function get_setting( $key ) {
		return isset( $this->settings[ $key ] ) ? $this->settings[ $key ] : '';
	}
}

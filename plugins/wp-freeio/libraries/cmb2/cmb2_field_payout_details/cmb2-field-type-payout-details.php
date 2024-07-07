<?php
/**
 * CMB2 Addons
 *
 * @package    wp-freeio
 * @author     Habq 
 * @license    GNU General Public License, version 3
 */

if ( ! defined( 'ABSPATH' ) ) {
  	exit;
}

class WP_Freeio_CMB2_Field_Payout_Details {

	public static function init() {
		add_filter( 'cmb2_render_wpfr_payout_details', array( __CLASS__, 'render_field' ), 10, 5 );
		add_filter( 'cmb2_sanitize_wpfr_payout_details', array( __CLASS__, 'sanitize_field' ), 10, 4 );	
	}

	/**
	 * Render field
	 */
	public static function render_field( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		$value = !empty($field_escaped_value) ? $field_escaped_value : $field->default();
		$value = get_post_meta($field_object_id, WP_FREEIO_WITHDRAW_PREFIX.'payouts_details', true);
		if ( $value ) {
			?>
			<ul class="payout-details-wrapper">
				<?php
				$bank_transfer_fields = wp_freeio_get_option('bank_transfer_fields', array('bank_account_name', 'bank_account_number', 'bank_name', 'bank_routing_number', 'bank_iban', 'bank_bic_swift'));
				foreach ($value as $key => $value) {
					
					switch ($key) {
						case 'payout_method':
							$all_payout_methods = WP_Freeio_Mixes::get_default_withdraw_payout_methods();
							$title = esc_html__('Payout Method', 'wp-freeio');
							$val = isset($all_payout_methods[$value]) ? $all_payout_methods[$value] : $value;
							break;
						case 'paypal_email':
							$title = esc_html__('Paypal Email', 'wp-freeio');
							$val = $value;
							break;
						case 'payoneer_email':
							$title = esc_html__('Payoneer Email', 'wp-freeio');
							$val = $value;
							break;
						default:
							$title = isset($bank_transfer_fields[$key]) ? $bank_transfer_fields[$key] : $key;
							$val = $value;
							break;
					}
					$title = apply_filters('wp-freeio-render-payout-details-title', $title, $key, $value);
					$val = apply_filters('wp-freeio-render-payout-details-val', $val, $key, $value);
					?>
					<li class="item">
						<span class="text"><?php echo esc_html($title); ?>:</span>
						<strong class="value"><?php echo esc_html($val); ?></strong>
					</li>
					<?php
				}
				?>
			</ul>
			<?php
		}
	}

	public static function sanitize_field( $override_value, $value, $object_id, $field_args ) {
		return $value;
	}

}

WP_Freeio_CMB2_Field_Payout_Details::init();
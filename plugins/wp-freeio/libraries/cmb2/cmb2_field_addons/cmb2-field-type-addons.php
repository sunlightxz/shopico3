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

class WP_Freeio_CMB2_Field_Addons {

	public static function init() {
		add_filter( 'cmb2_render_wpjb_addons', array( __CLASS__, 'render_addons' ), 10, 5 );
		add_filter( 'cmb2_sanitize_wpjb_addons', array( __CLASS__, 'sanitize_addons' ), 10, 4 );	
	}

	/**
	 * Render field
	 */
	public static function render_addons( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		self::setup_admin_scripts();
		$value = !empty($field_escaped_value) ? $field_escaped_value : $field->default();

		$user_id = get_current_user_id();
		if ( is_admin() ) {
			if ( $field_object_id ) {
				$user_id = get_post_field('post_author', $field_object_id);
			}
		}
		$posts = get_posts(array(
			'numberposts' => -1,
			'post_type' => 'service_addon',
			'author' => $user_id,
			'orderby' => 'date',
			'order' => 'DESC',
		));

		if ( $posts ) {
			?>
			<ul class="addons-wrapper">
				<?php
				$i = 1;
				foreach ($posts as $post) {
					$checked = '';
					if ( !empty($value) ) {
						if ( is_array($value) ) {
							if ( in_array($post->ID, $value) ) {
								$checked = 'checked="checked"';
							}
						} else {
							if ( $post->ID == $value ) {
								$checked = 'checked="checked"';
							}
						}
					}
					?>
					<li class="addon-select-item">
						<input id="<?php echo esc_attr($field->args( 'id' ).'-'.$i);?>" type="checkbox" name="<?php echo esc_attr($field->args( '_name' )); ?>[]" value="<?php echo esc_attr($post->ID); ?>" <?php echo trim($checked); ?>>
						<label class="<?php echo esc_attr($value == $post->ID ? 'selected' : ''); ?>" for="<?php echo esc_attr($field->args( 'id' ).'-'.$i);?>">
							<div class="content">
								<h5 class="title"><?php echo $post->post_title; ?></h5>
								<div class="inner">
									<?php echo $post->post_content; ?>
								</div>
							</div>

							<div class="price">
								<?php
									$price = get_post_meta($post->ID, WP_FREEIO_SERVICE_ADDON_PREFIX . 'price', true);
									echo WP_Freeio_Price::format_price($price);
								?>
							</div>
						</label>
					</li>
					<?php
					$i++;
				}
				?>
			</ul>
			<?php
		}
	}

	public static function sanitize_addons( $override_value, $value, $object_id, $field_args ) {
		return $value;
	}

	public static function setup_admin_scripts() {
		if ( is_admin() ) {
			wp_enqueue_style( 'image-select-style', plugins_url( 'css/style.css', __FILE__ ), array(), '1.0' );
		}
	}
}

WP_Freeio_CMB2_Field_Addons::init();
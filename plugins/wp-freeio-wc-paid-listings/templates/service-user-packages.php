<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( $user_packages ) : ?>
	<div class="widget widget-your-packages">
		<h2 class="widget-title"><?php esc_html_e( 'Your Packages', 'wp-freeio-wc-paid-listings' ); ?></h2>
		<ul class="user-service-packaged">
			<?php
				$prefix = WP_FREEIO_WC_PAID_LISTINGS_PREFIX;
				$checked = 1; 
			foreach ( $user_packages as $key => $package ) :
				$package_count = get_post_meta($package->ID, $prefix.'package_count', true);
				$service_limit = get_post_meta($package->ID, $prefix.'service_limit', true);
				$service_duration = get_post_meta($package->ID, $prefix.'service_duration', true);
				$feature_services = get_post_meta($package->ID, $prefix.'feature_services', true);
			?>

				<li>
					<input type="radio" <?php checked( $checked, 1 ); ?> name="wpfiwpl_service_user_package" value="<?php echo esc_attr($package->ID); ?>" id="user-package-<?php echo esc_attr($package->ID); ?>" />
					<label for="user-package-<?php echo esc_attr($package->ID); ?>"><?php echo trim($package->post_title); ?></label><br/>

					<?php
						if ( $service_limit ) {
							printf( _n( '%s service posted out of %d', '%s services posted out of %d', $package_count, 'wp-freeio-wc-paid-listings' ), $package_count, $service_limit );
						} else {
							printf( _n( '%s service posted', '%s services posted', $package_count, 'wp-freeio-wc-paid-listings' ), $package_count );
						}

						if ( $service_duration ) {
							printf(  ', ' . _n( 'listed for %s day', 'listed for %s days', $service_duration, 'wp-freeio-wc-paid-listings' ), $service_duration );
						}
						echo sprintf(__( ', featured service: %s', 'wp-freeio-wc-paid-listings' ), $feature_services ? __( 'Yes', 'wp-freeio-wc-paid-listings' ) : __( 'No', 'wp-freeio-wc-paid-listings' )  );
						$checked = 0;
					?>
				</li>
			<?php endforeach; ?>
		</ul>
		<button class="btn btn-theme" type="submit">
			<?php esc_html_e('Continue using quota', 'wp-freeio-wc-paid-listings') ?>
		</button>
	</div>
<?php endif; ?>
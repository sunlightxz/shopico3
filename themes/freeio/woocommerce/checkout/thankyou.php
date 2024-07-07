<?php
/**
 * Thankyou page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/thankyou.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 8.1.0
 */

defined( 'ABSPATH' ) || exit; ?>
<div class="max-930">
	<div class="clearfix">
		<?php if ( $order ) : ?>

			<?php if ( $order->has_status( 'failed' ) ) : ?>

				<p class="woocommerce-thankyou-order-failed"><?php esc_html_e( 'Unfortunately your order cannot be processed as the originating bank/merchant has declined your transaction. Please attempt your purchase again.', 'freeio' ); ?></p>

				<p class="woocommerce-thankyou-order-failed-actions">
					<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="button pay"><?php esc_html_e( 'Pay', 'freeio' ) ?></a>
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="button pay"><?php esc_html_e( 'My Account', 'freeio' ); ?></a>
					<?php endif; ?>
				</p>

			<?php else : ?>
				<div class="top-header-order text-center">
					<div class="wrapper-icon-completed"><i class="fas fa-check"></i></div>
					<h2 class="order-completed"><?php esc_html_e( 'Your order is completed!', 'freeio' ) ?></h2>
				
					<div class="woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'freeio' ), $order ); ?></div>

				</div>

				<div class="box-white-inner box-order">
					<ul class="woocommerce-thankyou-order-details order_details clearfix">
						<li class="order">
							<?php esc_html_e( 'Order Number', 'freeio' ); ?>
							<strong><?php echo trim($order->get_order_number()); ?></strong>
						</li>
						<li class="date">
							<?php esc_html_e( 'Date', 'freeio' ); ?>
							<strong><?php echo wc_format_datetime( $order->get_date_created() ); ?></strong>
						</li>
						<li class="total">
							<?php esc_html_e( 'Total', 'freeio' ); ?>
							<strong><?php echo trim($order->get_formatted_order_total()); ?></strong>
						</li>
						<?php if ( $order->get_payment_method_title() ) : ?>
						<li class="method">
							<?php esc_html_e( 'Payment Method', 'freeio' ); ?>
							<strong><?php echo trim($order->get_payment_method_title()); ?></strong>
						</li>
						<?php endif; ?>
					</ul>
				</div>
				<div class="clear"></div>

			<?php endif; ?>
		<?php else : ?>
			<div class="box-white-inner box-order max-770 space-30">
				<p class="woocommerce-thankyou-order-received"><?php echo apply_filters( 'woocommerce_thankyou_order_received_text', esc_html__( 'Thank you. Your order has been received.', 'freeio' ), null ); ?></p>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( $order ) : ?>
		<div class="mt-5">
			<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>	
		</div>
	<?php endif; ?>
</div>
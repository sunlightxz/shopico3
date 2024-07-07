<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

	
<div class="box-dashboard-wrapper">
	

	<h3 class="widget-title"><?php echo esc_html__('Payouts History','freeio') ?></h3>
	<div class="inner-list">		
		
		<?php
		$user_id = WP_Freeio_User::get_user_id();
	    if ( get_query_var( 'paged' ) ) {
		    $paged = get_query_var( 'paged' );
		} elseif ( get_query_var( 'page' ) ) {
		    $paged = get_query_var( 'page' );
		} else {
		    $paged = 1;
		}

		$query_vars = array(
		    'post_type' => 'withdraw',
		    'posts_per_page'    => get_option('posts_per_page'),
		    'paged'    			=> $paged,
		    'post_status' => array('publish', 'pending'),
		    'fields' => 'ids',
		    'author' => $user_id,
		);
		$withdraws = WP_Freeio_Query::get_posts($query_vars);

		if ( !empty($withdraws) && !empty($withdraws->posts) ) {
			?>
			<div class="table-responsive">
				<table class="job-table">
					<thead>
						<tr>
							<th class="amount"><?php esc_html_e('Amount', 'freeio'); ?></th>
							<th class="payment-gateway"><?php esc_html_e('Payout Method', 'freeio'); ?></th>
							<th class="date"><?php esc_html_e('Date', 'freeio'); ?></th>
							<th class="status"><?php esc_html_e('Status', 'freeio'); ?></th>
						</tr>
					</thead>
					<?php
					$all_payout_methods = WP_Freeio_Mixes::get_default_withdraw_payout_methods();
					foreach ($withdraws->posts as $post_id) {
						
						$amount = get_post_meta($post_id, WP_FREEIO_WITHDRAW_PREFIX . 'amount', true);
						$payout_method = get_post_meta($post_id, WP_FREEIO_WITHDRAW_PREFIX . 'payout_method', true);

						?>

						<tr <?php post_class('payout-wrapper'); ?>>
							<td>
								<div class="amount price-wrapper">
						        	<?php
						        	echo WP_Freeio_Price::format_price($amount, true);
						        	?>
						        </div>
							</td>
							<td>
								<div class="payout-method">
						        	<?php
						        	if ( !empty($all_payout_methods[$payout_method]) ) {
						        		echo trim($all_payout_methods[$payout_method]);
						        	} else {
						        		echo trim($payout_method);
						        	}
						        	?>
						        </div>
							</td>
							<td>
								<div class="date">
						            <?php echo get_the_date( get_option('date_format', 'd M, Y'), $post_id ); ?>
						        </div>
							</td>
							<td>
								<div class="status">
						            <?php
					        		$post_status = get_post_status($post_id);
					        		if ( $post_status == 'pending' ) {
					        			$classes = 'bg-pending';
					        		} elseif( $post_status == 'cancelled' ) {
					        			$classes = 'bg-cancelled';
					        		} else {
					        			$classes = 'bg-success';
					        		}

									$post_status_object = get_post_status_object( $post_status );
									?>
									<span class="badge <?php echo esc_attr($classes);?>">
										<?php
										if ( $post_status == 'publish' || $post_status == 'completed' ) {
											esc_html_e('Completed', 'freeio');
										} elseif ( !empty($post_status_object->label) ) {
											echo esc_html($post_status_object->label);
										} else {
											echo esc_html($post_status_object->post_status);
										}
										?>
									</span>
						        </div>
							</td>
						</tr>
						
						<?php
					}

					?>
				</table>
			</div>
			<?php WP_Freeio_Mixes::custom_pagination( array(
				'wp_query' => $withdraws,
				'max_num_pages' => $withdraws->max_num_pages,
				'prev_text'     => '<i class="ti-angle-left"></i>',
				'next_text'     => '<i class="ti-angle-right"></i>',
			));
		?>

		<?php } else { ?>
			<div class="not-found"><?php esc_html_e('No withdraw found.', 'freeio'); ?></div>
		<?php } ?>
	</div>

	<h3 class="widget-title"><?php echo esc_html__('Payouts Settings','freeio') ?></h3>
	<div class="inner-list">
        <div class="d-flex">
	        <ul class="nav nav-tabs nav-payouts">
				<?php
				$payout_method = get_user_meta($user_id, 'payout_method', true);

				$all_payout_methods = WP_Freeio_Mixes::get_default_withdraw_payout_methods();
				$withdraw_payout_methods = wp_freeio_get_option('withdraw_payout_methods', array('paypal', 'bacs', 'payoneer'));
				if ( !in_array($payout_method, $withdraw_payout_methods) ) {
					$payout_method = array_shift(array_values($withdraw_payout_methods));
				}
				$i=0; foreach ($withdraw_payout_methods as $val) {
					if ( !empty($all_payout_methods[$val]) ) {
						$active_class = '';
						if ( ($i == 0 && empty($payout_method)) || ($val == $payout_method ) ) {
							$active_class = 'active';
						}
					?>
						<li>
							<a data-bs-toggle="tab" href="#payout_method_<?php echo esc_attr($val); ?>" class="<?php echo esc_attr($active_class); ?>"><?php echo esc_html($all_payout_methods[$val]); ?></a>
						</li>
					<?php
					$i++;
					}
				}
				?>
			</ul>
		</div>
        <div class="tab-content">

            <?php if ( in_array('paypal', $withdraw_payout_methods) ) {
            	$paypal_email = get_user_meta($user_id, 'paypal_email', true);
            	$active_class = '';
            	if ( empty($payout_method) || ('paypal' == $payout_method ) ) {
					$active_class = 'show active';
				}
        	?>
        		<div id="payout_method_paypal" class="tab-pane fade <?php echo esc_attr($active_class); ?>" role="tabpanel">
	        		<form id="payouts-withdraw-settings-form-paypal" class="payouts-withdraw-settings-form form-theme" method="post" action="?">
		                
	                    <div class="form-group">
	                    	<label for="input-paypal_email"><?php esc_html_e('Paypal Email', 'freeio'); ?></label>
	                        <input id="input-paypal_email" type="email" class="form-control" name="paypal_email" placeholder="<?php esc_attr_e( 'Paypal Email', 'freeio' ); ?>" value="<?php echo esc_attr($paypal_email); ?>"  required="required">
	                    </div><!-- /.form-group -->
		                
		                <input type="hidden" name="payout_method" value="paypal">

		                <div class="clearfix">
			            	<button type="submit" class="btn btn-theme"><?php esc_html_e('Save Details', 'freeio'); ?><i class="flaticon-right-up next"></i></button>
			            </div>
		            </form>
		        </div>
            <?php } ?>

            <?php if ( in_array('payoneer', $withdraw_payout_methods) ) {
            	$payoneer_email = get_user_meta($user_id, 'payoneer_email', true);
            	$active_class = '';
            	if ( 'payoneer' == $payout_method ) {
					$active_class = 'show active';
				}
        	?>
        		<div id="payout_method_payoneer" class="tab-pane fade in <?php echo esc_attr($active_class); ?>" role="tabpanel">
	                <form id="payouts-withdraw-settings-form-payoneer" class="payouts-withdraw-settings-form form-theme" method="post" action="?">
		                
	                    <div class="form-group">
	                    	<label for="input-payoneer_email"><?php esc_html_e('Payoneer Email', 'freeio'); ?></label>
	                        <input type="email" class="form-control" name="payoneer_email" placeholder="<?php esc_attr_e( 'Payoneer Email', 'freeio' ); ?>" value="<?php echo esc_attr($payoneer_email); ?>" required="required">
	                    </div><!-- /.form-group -->
		                
		                <input type="hidden" name="payout_method" value="payoneer">

		                <div class="clearfix">
			            	<button type="submit" class="btn btn-theme"><?php esc_html_e('Save Details', 'freeio'); ?><i class="flaticon-right-up next"></i></button>
			            </div>
		            </form>
		        </div>
            <?php } ?>

            <?php
            if ( in_array('bacs', $withdraw_payout_methods) ) {
	            $all_bank_transfer_fields = WP_Freeio_Mixes::get_default_bank_transfer_fields();
				$bank_transfer_fields = wp_freeio_get_option('bank_transfer_fields', array('bank_account_name', 'bank_account_number', 'bank_name', 'bank_routing_number', 'bank_iban', 'bank_bic_swift'));

				if ( $bank_transfer_fields ) {
					$active_class = '';
	            	if ( 'bacs' == $payout_method ) {
						$active_class = 'show active';
					}
	            ?>
	            	<div id="payout_method_bacs" class="tab-pane fade in <?php echo esc_attr($active_class); ?>" role="tabpanel">
		            	<form id="payouts-withdraw-settings-form-bacs" class="payouts-withdraw-settings-form form-theme" method="post" action="?">
			                
		                	<div class="row">
		                		<?php $i=0; foreach ($bank_transfer_fields as $val) {
		                			if ( !empty($all_bank_transfer_fields[$val]) ) {
		                				$value = get_user_meta($user_id, $val, true);
		            				?>
		                			<div class="col-6">
		                				<div class="form-group">
		                					<label for="input-<?php echo esc_attr($val); ?>"><?php echo trim($all_bank_transfer_fields[$val]); ?></label>
					                        <input type="text" class="form-control style2" name="<?php echo esc_attr($val); ?>" value="<?php echo esc_attr($value); ?>" required="required">
					                    </div><!-- /.form-group -->
		                			</div>
		                			<?php } ?>
		                		<?php } ?>
		                	</div>
			                <input type="hidden" name="payout_method" value="bacs">

			                <div class="clearfix">
				            	<button type="submit" class="btn btn-theme"><?php esc_html_e('Save Details', 'freeio'); ?><i class="flaticon-right-up next"></i></button>
				            </div>
			            </form>
			        </div>
	            <?php } ?>
            <?php } ?>
        </div>
	</div>
</div>